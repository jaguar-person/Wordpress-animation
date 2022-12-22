<?php
class sibg_frontend {

    public function GravityInit(){
        add_filter( 'gform_form_tag', array($this,'AddStyleClass'), 10, 2 );
        add_filter( 'gform_field_content',array( $this, 'FormCustomization'),11, 5);
        add_action( 'wp_enqueue_scripts', array($this,'load_dashicons_front_end') );
        add_filter( 'gform_next_button', array($this,'add_next_onclick'), 10, 2 );
        add_filter( 'gform_next_button', array($this,'add_next_value'), 10, 2 );
        add_filter( 'gform_previous_button', array($this,'add_previous_onclick'), 10, 2 );
        add_filter( 'gform_previous_button', array($this,'add_previous_value'), 10, 2 );
        add_filter( 'gform_previous_button', array($this,'add_previous_class'), 11, 2 );
        add_filter( 'gform_ajax_spinner_url', array($this,'change_loading') );
        add_filter( 'gform_confirmation_anchor', '__return_false' );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script("theme",SIBG_js."theme.js","",SIBG_VERSION,true);
        wp_enqueue_style("tooltip_style",SIBG_CSS."tooltip.css","",SIBG_VERSION);
        wp_enqueue_style("animation_style",SIBG_CSS."bg-animations.css","",SIBG_VERSION);
        wp_enqueue_script("bg-validation-fields-js",SIBG_js."bg-validation-fields.js","",SIBG_VERSION,true);
    }

    public $page_next_default_number_value = 0;
    public $next_form_id   = 0;
    public $next_anim_type = "None";
    public function add_next_onclick( $button, $form ) {

        $form_id = $form['id'];

        if($form_id == $this->next_form_id){
        }else{

            $this->next_form_id = $form_id;
            $this->page_next_default_number_value = 0;
            $this->next_anim_type = json_decode(gform_get_meta($form_id, "bg_custom_settings"),true)["form_animation"];

        }

        $dom        = new DOMDocument();
        $dom->loadHTML( $button );
        $input      = $dom->getElementsByTagName( 'input' )->item(0);
        $onclick    = $input->getAttribute( 'onclick' );
        $onkeypress = $input->getAttribute( 'onkeypress' );
        $current_page_number_value = $this->page_next_default_number_value + 1;

        add_filter( 'gform_cdata_open', array($this,'not_comment'));
        if ($this->next_anim_type != "None" && $this->next_anim_type != ""){
            add_filter( 'gform_cdata_open', array($this,'comment_ajax') );
            if (is_rtl()){
                $name = strtolower($this->next_anim_type);
                $temp = SIBG_js."animations/".$name."_rtl_animation.js";
                wp_enqueue_script($name."_rtl_animation",$temp,array("jquery"),SIBG_VERSION,true);
                $onclick    = "bg_form_animation('next','".$this->next_anim_type."_rtl',$form_id,$current_page_number_value);";
                $onkeypress = "if( event.keyCode == 13 ){".$onclick."}";
            }else{
                $name = strtolower($this->next_anim_type);
                $temp = SIBG_js."animations/".$name."_animation.js";
                wp_enqueue_script($name."_animation",$temp,"jquery",SIBG_VERSION,true);
                $onclick    = "bg_form_animation('next','$this->next_anim_type',$form_id,$current_page_number_value);";
                $onkeypress = "if( event.keyCode == 13 ){".$onclick."}";
            }
        }

        $this->page_next_default_number_value = $this->page_next_default_number_value + 1;
        $input->setAttribute( 'onclick', $onclick );
        $input->setAttribute( 'onkeypress', $onkeypress );
        return $dom->saveHtml( $input );

    }

    public function add_next_value( $previous_button, $form ) {

        $dom   = new DOMDocument();
        $dom->loadHTML( $previous_button );
        $input = $dom->getElementsByTagName( 'input' )->item(0);
        $val   = __( 'Next', 'gravityforms' );
        $input->setAttribute( 'value', $val );
        return $dom->saveHtml( $input);

    }

    public $page_pre_default_number_value = 1;
    public $prev_form_id=0;
    public $prev_anim_type = "None";
    public function add_previous_onclick( $previous_button, $form ) {
        $form_id = $form['id'];

        if($form_id == $this->prev_form_id){
        }else{

            $this->prev_form_id                  = $form_id;
            $this->page_pre_default_number_value = 1;
            $this->prev_anim_type                = json_decode(gform_get_meta($form_id, "bg_custom_settings"),true)["form_animation"];

        }

        $dom = new DOMDocument();
        $dom->loadHTML( $previous_button );
        $input                     = $dom->getElementsByTagName( 'input' )->item(0);
        $onclick                   = $input->getAttribute( 'onclick' );
        $onkeypress                = $input->getAttribute( 'onkeypress' );
        $current_page_number_value = $this->page_pre_default_number_value + 1;

        if( $this->prev_anim_type != "None" && $this->prev_anim_type != ""){
            add_filter( 'gform_cdata_open', array($this,'comment_ajax') );
            if (is_rtl()){
                $onclick   = "bg_form_animation('prev','".$this->prev_anim_type."_rtl',$form_id,$current_page_number_value);";
                $onkeypress = "if( event.keyCode == 13 ){".$onclick."}";
            }else{
                $onclick   = "bg_form_animation('prev','$this->prev_anim_type',$form_id,$current_page_number_value);";
                $onkeypress = "if( event.keyCode == 13 ){".$onclick."}";
            }
        }

        $this->page_pre_default_number_value += 1;
        $input->setAttribute( 'onclick', $onclick );
        $input->setAttribute( 'onkeypress', $onkeypress );
        return $dom->saveHtml( $input);

    }

    public function add_previous_value( $previous_button, $form ) {
        $dom   = new DOMDocument();
        $dom->loadHTML( $previous_button );
        $input = $dom->getElementsByTagName( 'input' )->item(0);
        $val   = __( 'Previous', 'gravityforms' );
        $input->setAttribute( 'value', $val );
        return $dom->saveHtml( $input);
    }

    public function add_previous_class( $previous_button, $form ) {
        $customize           = self::GetCustomizeSetting($form["id"]);
        if ($customize ["additionalSetting"]["prev_UX"]=="true"){
            $start           = strpos( $previous_button,"class");
            $previous_button = substr_replace( $previous_button, "class=\"BG_prev_ux ",$start, "7" );
        }
        return $previous_button;
    }

    public function not_comment(){
        return "";
    }
    public function comment_ajax(){
        return "//";
    }

    public function change_loading( $src ) {
        $S_Url = get_option("siteurl");
        return $S_Url;

    }

    public $is_add_style = false;
    public function AddStyleClass($form_tag , $form) {
        if (!$this->is_add_style){
            require_once("form_themes.php");
        }
        $customize       = self::GetCustomizeSetting($form["id"]);
        $customStyle    = $customize["form_theme"] != "Default" && $customize["form_theme"] != "" ? $customize["form_theme"] : "bg_default_theme";
        $customFont     = $customize["font_name"]  != "Default" ? $customize["font_name"]  : "";
        $themeType       = $customize["theme_type"]!="" ? "BG_".$customize["theme_type"]:"BG_Light";
        if ($customFont){
            wp_enqueue_style("BG_font".$customFont,"https://fonts.googleapis.com/css?family={$customFont}&display=swap","",1.0,false);
            $customFont = "BG_{$customFont}_font";
        }

        $costumeFontSize = $customize["font_size"]!= ""?"BG_".$customize["font_size"]."_size":"BG_medium_size";
        $customFont      = str_replace("+","_",$customFont);
        $customClass     = $customStyle." ".$customFont." ".$costumeFontSize." ".$themeType;

        //add form main color to data tag
        $mainColor = $customize["main_color"];
        $start = strpos($form_tag,"id");
        $form_tag = substr_replace( $form_tag, "data-color='".$mainColor."' id", $start, "2" );

        if ($customClass == ""){
            return $form_tag;
        }

        //add custom class to <form>
        $form_tag = substr_replace( $form_tag, " class='".$customClass."' ", "5", "0" );
        return $form_tag;
    }

    public function load_dashicons_front_end() {
        wp_enqueue_style('dashicons');
    }

    public $nextStart = 0;

    public function FormCustomization($content, $field, $value, $lead_id, $form_id){

        $customize   = self::GetCustomizeSetting($form_id);

        //Change fileupload field design
        if ($field->type=="fileupload" && $customize["form_theme"] != "Default"){
            $content = self::ChangeUploadField($content,$customize);
        }

        //Add form fields tooltip
        $content = self::FormTooltip($field,$content,$customize);

        return $content;
    }

    public function GetFieldType(){
        $types = array(
            'text',
            'textarea',
            'select',
            'multiselect',
            'number',
            'name',
            'date',
            'time',
            'phone',
            'address',
            'website',
            'email',
            'fileupload',
            'captcha',
            'list',
            'consent',
            'post_title',
            'post_content',
            'post_excerpt',
            'post_tags',
            'post_category',
            'post_image',
            'post_custom_field',
            'product',
            'quantity',
            'option',
            'shipping',
            'total',
            'creditcard',
            'password',
            'singleproduct',
            'calculation',
            'price',
            'hiddenproduct',
            'singleshipping',
            'donation',
        );

        return $types;

    }

    public function ChangeUploadField($content,$customize){
        $start   = strpos( $content,"ginput_container_fileupload");
        if($customize["form_theme"] == 'BG_Android'){
            $content = substr_replace( $content, " ginput_container_fileupload'><i class='BG_fileupload_icon'></i><label class='BG_fileupload'><span class='BG_fileupload_text'>Upload File</span>",$start, "29" );
        }
        else{
            $content = substr_replace( $content, " ginput_container_fileupload'><label class='BG_fileupload'><i class='BG_fileupload_icon'></i><span class='BG_fileupload_text'>Upload File</span>",$start, "29" );
        }

        $start   = strlen($content) - 6;
        $start   = strpos( $content,"</div>",$start);
        $content = substr_replace( $content, "</label></div>",$start, "6" );
        return $content;
    }

    public function GetCustomizeSetting($form_id){
        $customize = json_decode(gform_get_meta($form_id,"bg_custom_settings"),true);
        return $customize;
    }

    public function FormTooltip($field,$content,$customize){
        $type        = self::GetFieldType();
        if(in_array($field->type,$type)){
            $content = self::FieldTooltip($content,$field,$customize);
        }
        else{
            $content = self::RadioCheckTooltip($content,$field,$customize);
        }
        return $content;
    }

    public function FieldTooltip($content,$field,$customize){
        $tooltipTheme = $customize["tooltip_class"]?$customize["tooltip_class"]:"None";
        if ($tooltipTheme != "None"){
            if($field->is_tooltip && $customize["form_theme"]!="BG_Material"){
                $viewTooltip = "BG_".$customize["tooltip_view_type"];
                $start   = strpos( $content,"<label>");
                $content = substr_replace( $content, "<div class='main_label {$viewTooltip}'><label ",$start, "7" );
                $start   = strpos( $content,"</label>");
                $content = substr_replace( $content, "</label></div>",$start, "8" );
                $start   = strpos( $content,"</label>");
                $tooltip = self::RenderTooltip($customize,$field->is_tooltip);
                $content = substr_replace( $content, "</label>".$tooltip, $start, "8" );
            }
        }

        if ($field->type =="consent" && $customize["form_theme"]!= "Default"){
            $start       = strpos($content,"ginput_container_consent");
            $classLength = strlen("ginput_container_consent");
            $content     = substr_replace( $content, " BG_default ",$start + $classLength , "0" );
            $start       = strpos($content,"</label>",$start);
            $content     = substr_replace( $content, "<span class='BG_check'></span>",$start , "0" );
        }

        return $content;
    }

    public function RadioCheckTooltip($content,$field,$customize){
        $this->nextStart = strpos( $content,"</label>");
        $tooltipTheme = $customize["tooltip_class"]?$customize["tooltip_class"]:"None";

        if ($field->is_tooltip && $tooltipTheme != "None"){

            $viewTooltip     = "BG_".$customize["tooltip_view_type"];


            $this->nextStart = strpos( $content,"<label>");
            $content         = substr_replace( $content, "<div class='main_label {$viewTooltip}'><label ",$this->nextStart, "7" );
            $this->nextStart = strpos( $content,"</label>");
            $content         = substr_replace( $content, "</label></div>",$this->nextStart, "8" );
            $tooltip         = self::RenderTooltip($customize,$field->is_tooltip);
            $content         = substr_replace( $content, "</label>".$tooltip, $this->nextStart , "8" );
            $toolLength      = strlen("</label>".$tooltip);
            $this->nextStart = strpos( $content,"</label>",$this->nextStart + $toolLength + 1);

        }else{
            $this->nextStart = $this->nextStart + 8;
            $limitLength = strlen($content);
            if ($this->nextStart > $limitLength){

            }else{
                $this->nextStart = strpos( $content,"</label>",$this->nextStart);
            }

        }

        if ($field->type == "checkbox" || $field->type == "radio") {

            $viewMode             = $field->view_mode ? $field->view_mode : "default";
            $viewMode             = "BG_".$viewMode;
            $viewTooltip          = "BG_".$customize["tooltip_view_type"];

            // in default theme delete radio/checkbox view mode
            if ($customize["form_theme"]==="Default" || $customize["form_theme"]===""){
                $viewMode = "";
            }

            $classPos             = strpos( $content,"gfield_".$field->type);
            $classLength          = strlen("gfield_".$field->type);
            $content              = substr_replace( $content, " ".$viewMode." ".$viewTooltip, $classPos+$classLength, "0" );
            $this->nextStart     += strlen($viewMode) + strlen($viewTooltip) + 2;
            foreach ($field->choices as $key=>$value){
                $content          = substr_replace( $content, "<span class='BG_check'></span></label>", $this->nextStart , "8" );
                $this->nextStart += strlen("<span class='BG_check'></span>");
                $content          = self::GetChoicesTooltip($content,$customize,$value,$tooltipTheme);
            }
        }
        return $content;
    }

    public function GetChoicesTooltip($content,$customize,$value,$tooltipTheme){

        if ($value["is_tooltip"] != "" && $tooltipTheme != "None"){
            $tooltip         = self::RenderTooltip($customize,$value["is_tooltip"]);
            $toolLength      = strlen("</label>".$tooltip);
            $content         = substr_replace( $content, "</label>".$tooltip, $this->nextStart, "8" );
            $this->nextStart = strpos( $content,"</label>",$this->nextStart + $toolLength);
        }else{
            $this->nextStart  = strpos( $content,"</label>",$this->nextStart + 8);
        }
        return $content;
    }

    public function RenderTooltip($customize,$content){
        $tooltipThemeClass   = $customize["tooltip_class"];
        $tooltipThemeType    = $customize["theme_type"] == "Dark" || $customize["theme_type"] == "" ? "Light":"Dark";
        $formTooltipPosition = $customize["tooltip_position"];
        $tooltipClasses    = $tooltipThemeClass . " " . $tooltipThemeType;
        $tooltip = "<span class='gf_tooltip_body {$tooltipClasses}'data-position={$formTooltipPosition}><i class='dashicons dashicons-editor-help'></i><span>{$content}</span></span>";
        return $tooltip;
    }

}
$gravityTooltip = new sibg_frontend();
$gravityTooltip->GravityInit();
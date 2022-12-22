<?php
class sibg_backend{
    public function GravityInit(){
        // add a custom menu item to the Form Settings page menu
        add_filter( 'gform_form_settings_menu', array($this,'my_custom_form_settings_menu_item') );
        // colorpicker
        add_action( 'admin_enqueue_scripts', array($this,'mw_enqueue_color_picker') );
        // handle displaying content for our custom menu when selected
        add_action( 'gform_form_settings_page_Beauty Gravity', array($this,'my_custom_form_settings_page') );
        add_action( 'admin_enqueue_scripts',                array( $this, 'TooltipScript'              ),  10      );
        add_action( 'gform_field_standard_settings', array($this,'MyStandardSettings'), 10, 2 );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_style("backend_style",SIBG_CSS."bg-backend.css","",1.0);
    }

    public function my_custom_form_settings_menu_item( $menu_items ) {
        $menu_items[] = array(
            'name'  => 'Beauty Gravity',
            'label' => esc_html__('Beauty Gravity',SIBG_DOMAIN)
        );
        return $menu_items;
    }

    public function mw_enqueue_color_picker( $hook_suffix ) {
        // first check that $hook_suffix is appropriate for your admin page
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'my-script-handle', plugins_url('my-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
    }

    public function my_custom_form_settings_page() {
        GFFormSettings::page_header();


        $tabs = array(
            'theme' => 'theme',
            'settings' => 'settings'
        );

        if (isset($_GET['tab'])){
            $currentTab = sanitize_file_name($_GET['tab']);
        }else{
            $currentTab = 'Theme';
        }

        $form_id = null;
        if (isset($_GET["id"])){
            if (is_numeric($_GET["id"])){
                $form_id = $_GET["id"];
            }
        }
        if ($form_id == null){
            die();
        }
        echo '<h2 class="nav-tab-wrapper">';
        foreach( $tabs as $tab => $name ){
            $class = ( $tab == $currentTab ) ? ' nav-tab-active' : "";
            echo "<a class='nav-tab$class' href='?page=gf_edit_forms&view=settings&id=$form_id&subview=Beauty+Gravity&tab=$tab'>$name</a>";

        }
        echo '</h2>';

        $settingFunction ="BG_".$currentTab."_tab";
        $this->$settingFunction();
        wp_enqueue_script("settings-preview",SIBG_js."settings-preview.js",array('jquery'),SIBG_VERSION,true);

        GFFormSettings::page_footer();
    }

    public function BG_theme_tab(){

        wp_enqueue_style("tooltip_style",SIBG_CSS."tooltip.css","",1.0);

        $formSettingID = null;
        if (isset($_GET["id"])){
            if (is_numeric($_GET["id"])){
                $formSettingID = $_GET["id"];
            }
        }
        if ($formSettingID == null){
            die();
        }
        $is_form_exist = GFAPI::get_form($formSettingID);
        if(!$is_form_exist){
            die();
        }
        $settingMeta = json_decode(gform_get_meta($formSettingID, "bg_custom_settings"), true);
        $formTheme = $settingMeta["form_theme"] ? $settingMeta["form_theme"] : "None";
        $siteThemeType = $settingMeta["theme_type"] ? $settingMeta["theme_type"] : "Light";
        $formMainColor = $settingMeta["main_color"] ? $settingMeta["main_color"] : "#EEE";
        $formFont = $settingMeta["font_name"] ? $settingMeta["font_name"] : "Default";
        $fontSize = $settingMeta["font_size"] ? $settingMeta["font_size"] : "medium";
        $multiPageFormAnimation = $settingMeta["form_animation"] ? $settingMeta["form_animation"] : "None";
        $tooltipThemeClass = $settingMeta["tooltip_class"] ? $settingMeta["tooltip_class"] : "None";
        if (is_rtl()){
            $formTooltipPosition = $settingMeta["tooltip_position"] ? $settingMeta["tooltip_position"] : "L";
        }else{
            $formTooltipPosition = $settingMeta["tooltip_position"] ? $settingMeta["tooltip_position"] : "R";
        }
        $tooltipIconStyle = $settingMeta["tooltip_view_type"] ? $settingMeta["tooltip_view_type"] : "Icon";
        $tooltipThemeType = $siteThemeType == "Dark" ? "Light" : "Dark";
        $tooltipClasses = $tooltipThemeClass . " " . $tooltipThemeType;

        $themes = [
            "Default" => "Default",
            "Microsoft" => "BG_Microsoft"
        ];
        $animations = [
            "None" => "None",
            "Slider2" => "Slide2"
        ];
        $tooltipThemes = [
            "None" => "None",
            "Style 1" => "BG_tooltip_1"
        ];
        $fonts = [
            "Default" => ["Name" => "Default",
                "Type" => "Default"],
            "Abel" => ["Name" => "Abel",
                "Type" => "sans-serif"],
            "Anton" => ["Name" => "Anton",
                "Type" => "sans-serif"],
            "Bebas Neue" => ["Name" => "Bebas+Neue",
                "Type" => "cursive"],
            "Courgette" => ["Name" => "Courgette",
                "Type" => "cursive"],
            "Dancing Script" => ["Name" => "Dancing+Script",
                "Type" => "cursive"],
            "Dosis" => ["Name" => "Dosis",
                "Type" => "sans-serif"],
            "Inconsolata" => ["Name" => "Inconsolata",
                "Type" => "monospace"],
            "Indie Flower" => ["Name" => "Indie+Flower",
                "Type" => "cursive"],
            "Lato" => ["Name" => "Lato",
                "Type" => "sans-serif"],
            "Lobster" => ["Name" => "Lobster",
                "Type" => "cursive"],
            "Lora" => ["Name" => "Lora",
                "Type" => "serif"],
            "Merriweather" => ["Name" => "Merriweather",
                "Type" => "serif"],
            "Montserrat" => ["Name" => "Montserrat",
                "Type" => "sans-serif"],
            "Noto Sans" => ["Name" => "Noto+Sans",
                "Type" => "sans-serif"],
            "Nunito Sans" => ["Name" => "Nunito+Sans",
                "Type" => "sans-serif"],
            "Open Sans" => ["Name" => "Open+Sans",
                "Type" => "sans-serif"],
            "Oswald" => ["Name" => "Oswald",
                "Type" => "sans-serif"],
            "Playfair Display" => ["Name" => "Playfair+Display",
                "Type" => "serif"],
            "Poppins" => ["Name" => "Poppins",
                "Type" => "sans-serif"],
            "Quicksand" => ["Name" => "Quicksand",
                "Type" => "sans-serif"],
            "Rajdhani" => ["Name" => "Rajdhani",
                "Type" => "sans-serif"],
            "Roboto" => ["Name" => "Roboto",
                "Type" => "sans-serif"],
            "Roboto Condensed" => ["Name" => "Roboto+Condensed",
                "Type" => "sans-serif"],
            "Roboto Mono" => ["Name" => "Roboto+Mono",
                "Type" => "monospace"],
            "Roboto Slab" => ["Name" => "Roboto+Slab",
                "Type" => "serif"],
            "Shadows Into Light" => ["Name" => "Shadows+Into+Light",
                "Type" => "cursive"],
            "Source Sans Pro" => ["Name" => "Source+Sans+Pro",
                "Type" => "sans-serif"],
            "Ubuntu" => ["Name" => "Ubuntu",
                "Type" => "sans-serif"],
            "Varela Round" => ["Name" => "Varela+Round",
                "Type" => "sans-serif"],
            "Work Sans" => ["Name" => "Work+Sans",
                "Type" => "sans-serif"]
        ];

        $themes = apply_filters("bg_themes", $themes);
        $animations = apply_filters("bg_animations", $animations);
        $tooltipThemes = apply_filters("bg_animations", $tooltipThemes);
        ?>
        <form action="" method="POST">
            <table class="ga_form_settings" cellspacing="0" cellpadding="0">
                <tbody>

                <tr>
                    <th>
                        <?php esc_html_e("Choose Theme : ", SIBG_DOMAIN); ?>
                    </th>
                    <td>
                        <select name="bg-theme-select" id="" class="ga-theme-select">
                            <?php
                            foreach ($themes as $name => $class) {
                                if ($formTheme == $class) {
                                    echo "<option value={$class} class='ga-theme-optoins' selected>" . __($name, SIBG_DOMAIN) . "</option>";
                                } else {
                                    echo "<option value={$class} class='ga-theme-optoins'>" . __($name, SIBG_DOMAIN) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>
                        <?php esc_html_e("Site Current Theme : ", SIBG_DOMAIN); ?>
                    </th>
                    <td>
                        <select name="bg-site-theme-select" id="" class="ga-site-theme-select">
                            <?php
                            if ($siteThemeType == "Dark") {
                                echo "<option value='Dark' class='site-theme-options' selected>" . __('Dark', SIBG_DOMAIN) . "</option>";
                            } else {
                                echo "<option value='Dark' class='site-theme-options'>" . __('Dark', SIBG_DOMAIN) . "</option>";
                            }
                            if ($siteThemeType == "Light") {
                                echo "<option value='Light' class='site-theme-options' selected>" . __('Light', SIBG_DOMAIN) . "</option>";
                            } else {
                                echo "<option value='Light' class='site-theme-options'>" . __('Light', SIBG_DOMAIN) . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>
                        <?php esc_html_e("Form Color : ", SIBG_DOMAIN); ?>
                    </th>
                    <td>
                        <script>
                            jQuery(document).ready(function ($) {
                                $('.my-color-field').wpColorPicker();

                                $('.my-color-field').iris({
                                    hide: false,
                                    palettes: ['#ff4f81', '#0389ff', '#1cc7d0', '#ffc20e', '#8e43e7', '#123962','#2baf2b','#706357']
                                });

                            });

                        </script>
                        <?php
                        echo "<input type='text' value={$formMainColor} class='my-color-field'/>";
                        echo "<input id='bg-color-picker' name='bg-form-color' type='hidden' value={$formMainColor}>";
                        ?>

                    </td>
                </tr>
                <tr>
                    <th>
                        <?php esc_html_e("Font : ", SIBG_DOMAIN); ?>
                    </th>
                    <td>
                        <select name="bg-font-select" class="ga-font-select">
                            <?php
                            foreach ($fonts as $name => $value) {
                                if ($formFont == $value["Name"]) {
                                    echo "<option value='" . $value["Name"] . "/" . $value["Type"] . "' class='font-options' selected>" . __($name, SIBG_DOMAIN) . "</option>";
                                } else {
                                    echo "<option value='" . $value["Name"] . "/" . $value["Type"] . "' class='font-options'>" . __($name, SIBG_DOMAIN) . "</option>";
                                }
                            }
                            ?>

                        </select>
                    </td>
                </tr>
                <tr>
                    <th>
                        <?php esc_html_e("Font Size : ", SIBG_DOMAIN); ?>
                    </th>
                    <td>
                        <select name="bg-font-size" id="" class="ga-font-select">
                            <?php

                            if ($fontSize == "small") {
                                echo "<option value='small' class='font-options' selected>" . __("Small", SIBG_DOMAIN) . "</option>";
                            } else {
                                echo "<option value='small' class='font-options'>" . __("Small", SIBG_DOMAIN) . "</option>";
                            }
                            if ($fontSize == "medium") {
                                echo "<option value='medium' class='font-options' selected>" . __("Medium", SIBG_DOMAIN) . "</option>";
                            } else {
                                echo "<option value='medium' class='font-options'>" . __("Medium", SIBG_DOMAIN) . "</option>";
                            }
                            if ($fontSize == "large") {
                                echo "<option value='large' class='font-options' selected>" . __("Large", SIBG_DOMAIN) . "</option>";
                            } else {
                                echo "<option value='large' class='font-options'>" . __("Large", SIBG_DOMAIN) . "</option>";
                            }
                            if ($fontSize == "xlarge") {
                                echo "<option value='xlarge' class='font-options' selected>" . __("XLarge", SIBG_DOMAIN) . "</option>";
                            } else {
                                echo "<option value='xlarge' class='font-options'>" . __("XLarge", SIBG_DOMAIN) . "</option>";
                            }
                            ?>

                        </select>
                    </td>
                </tr>
                <tr>
                    <th>
                        <?php esc_html_e("Multipage Animation : ", SIBG_DOMAIN); ?>
                    </th>
                    <td>
                        <select name="bg-animation-select" id="" class="ga-animation-select">
                            <?php
                            foreach ($animations as $name => $value) {
                                if ($multiPageFormAnimation == $value) {
                                    echo "<option value={$value} class='ga-theme-optoins' selected>" . __($name, SIBG_DOMAIN) . "</option>";
                                } else {
                                    echo "<option value={$value} class='ga-theme-optoins'>" . __($name, SIBG_DOMAIN) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>
                        <?php esc_html_e("Tooltip Style : ", SIBG_DOMAIN); ?>
                    </th>
                    <td>
                        <select name="bg-tooltip-select" id="" class="bg-tooltip-select">
                            <?php
                            foreach ($tooltipThemes as $name => $class) {
                                if ($tooltipThemeClass == $class) {
                                    echo "<option value={$class} class='ga-theme-optoins' selected>" . __($name, SIBG_DOMAIN) . "</option>";
                                } else {
                                    echo "<option value={$class} class='ga-theme-optoins'>" . __($name, SIBG_DOMAIN) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>
                        <?php esc_html_e("Tooltip position : ", SIBG_DOMAIN); ?>
                    </th>
                    <td>
                        <div class="tooltip-pos BG_Hover">

                            <div class="tooltip-pos-top">

									<span class="tooltip_pos_body" data-position='TL' data-text="<?php echo __("Top Left",SIBG_DOMAIN) ?>">
                                        <label value="top-left">
                                            <?php
                                            if ($formTooltipPosition == "TL") {
                                                echo "<input type='radio' value='TL' name='bg-tooltip-position' checked>";
                                            } else {
                                                echo "<input type='radio' value='TL' name='bg-tooltip-position'>";
                                            }
                                            ?>
                                            <span class="poisition-title"></span>
										</label>
                                        <?php
                                        if ($tooltipThemeClass!="None") {
                                            echo "<span class='gf_tooltip_body {$tooltipClasses}' data-position='TL'><i class='dashicons dashicons-editor-help'></i><span>Top Left</span></span>";
                                        }
                                        ?>
									</span>


                                <span class="tooltip_pos_body" data-position='T' data-text="<?php echo __("Top",SIBG_DOMAIN) ?>">
                                        <label value="top">
											<?php
                                            if ($formTooltipPosition == "T") {
                                                echo "<input type='radio' value='T' name='bg-tooltip-position' checked>";
                                            } else {
                                                echo "<input type='radio' value='T' name='bg-tooltip-position'>";
                                            }
                                            ?>
                                            <span class="poisition-title"></span>
										</label>
										<?php
                                        if ($tooltipThemeClass!="None") {
                                            echo "<span class='gf_tooltip_body {$tooltipClasses}'data-position='T'><i class='dashicons dashicons-editor-help'></i><span>Top</span></span>";
                                        }
                                        ?>
									</span>


                                <span class="tooltip_pos_body" data-position='TR' data-text="<?php echo __("Top Right",SIBG_DOMAIN) ?>">
                                         <label value="top-right">
										 <?php
                                         if ($formTooltipPosition == "TR") {
                                             echo "<input type='radio' value='TR' name='bg-tooltip-position' checked>";
                                         } else {
                                             echo "<input type='radio' value='TR' name='bg-tooltip-position'>";
                                         }
                                         ?>
                                         <span class="poisition-title"></span>
									 </label>
										<?php
                                        if ($tooltipThemeClass!="None") {
                                            echo "<span class='gf_tooltip_body {$tooltipClasses}'data-position='TR'><i class='dashicons dashicons-editor-help'></i><span>Top Right</span></span>";
                                        }
                                        ?>
									 </span>


                            </div>

                            <div class="tooltip-pos-middle">

									  <span class="tooltip_pos_body" data-position='L' data-text="<?php echo __("Left",SIBG_DOMAIN) ?>">
                                          <label value="left">

											<?php
                                            if ($formTooltipPosition == "L") {
                                                echo "<input type='radio' value='L' name='bg-tooltip-position' checked>";
                                            } else {
                                                echo "<input type='radio' value='L' name='bg-tooltip-position'>";
                                            }
                                            ?>
                                              <span class="poisition-title"></span>
										</label>

									   <?php
                                       if ($tooltipThemeClass!="None"){
                                           echo "<span class='gf_tooltip_body {$tooltipClasses}'data-position='L'><i class='dashicons dashicons-editor-help'></i><span>Left</span></span>";
                                       }
                                       ?>

									 </span>

                                <span class="tooltip_pos_body" data-position='R' data-text="<?php echo __("Right",SIBG_DOMAIN) ?>">
                                        <label value="right">

											<?php
                                            if ($formTooltipPosition == "R") {
                                                echo "<input type='radio' value='R' name='bg-tooltip-position' checked>";
                                            } else {
                                                echo "<input type='radio' value='R' name='bg-tooltip-position'>";
                                            }
                                            ?>
                                            <span class="poisition-title"></span>
										</label>
									  <?php
                                      if ($tooltipThemeClass!="None") {
                                          echo "<span class='gf_tooltip_body {$tooltipClasses}'data-position='R'><i class='dashicons dashicons-editor-help'></i><span>Right</span></span>";
                                      }
                                      ?>
									</span>


                            </div>

                            <div class="tooltip-pos-bottom">

									<span class="tooltip_pos_body" data-position='BL' data-text="<?php echo __("Bottom Left",SIBG_DOMAIN) ?>">
                                        <label value="bottom-left">

											<?php
                                            if ($formTooltipPosition == "BL") {
                                                echo "<input type='radio' value='BL' name='bg-tooltip-position' checked>";
                                            } else {
                                                echo "<input type='radio' value='BL' name='bg-tooltip-position'>";
                                            }
                                            ?>
                                            <span class="poisition-title"></span>
										</label>
										<?php
                                        if ($tooltipThemeClass!="None") {
                                            echo "<span class='gf_tooltip_body {$tooltipClasses}'data-position='BL'><i class='dashicons dashicons-editor-help'></i><span>Bottom Left</span></span>";
                                        }
                                        ?>
									</span>

                                <span class="tooltip_pos_body" data-position='B' data-text="<?php echo __("Bottom",SIBG_DOMAIN) ?>">
                                            <label value="bottom">

											<?php
                                            if ($formTooltipPosition == "B") {
                                                echo "<input type='radio' value='B' name='bg-tooltip-position' checked>";
                                            } else {
                                                echo "<input type='radio' value='B' name='bg-tooltip-position'>";
                                            }
                                            ?>
                                                <span class="poisition-title"></span>
										</label>
										<?php
                                        if ($tooltipThemeClass!="None") {
                                            echo "<span class='gf_tooltip_body {$tooltipClasses}'data-position='B'><i class='dashicons dashicons-editor-help'></i><span>Bottom</span></span>";
                                        }
                                        ?>

										</span>

                                <span class="tooltip_pos_body" data-position='BR' data-text="<?php echo __("Bottom Right",SIBG_DOMAIN) ?>">
                                            <label value="bottom-right">

											<?php
                                            if ($formTooltipPosition == "BR") {
                                                echo "<input type='radio' value='BR' name='bg-tooltip-position' checked>";
                                            } else {
                                                echo "<input type='radio' value='BR' name='bg-tooltip-position'>";
                                            }
                                            ?>
                                                <span class="poisition-title"></span>
										</label>
										<?php
                                        if ($tooltipThemeClass!="None") {
                                            echo "<span class='gf_tooltip_body {$tooltipClasses}'data-position='BR'><i class='dashicons dashicons-editor-help'></i><span>Bottom Right</span></span>";
                                        }
                                        ?>


										</span>

                            </div>

                        </div>
                    </td>
                </tr>

                <tr>
                    <th>
                        <?php esc_html_e("Tooltip Icon Style : ", SIBG_DOMAIN); ?>
                    </th>
                    <td>
                        <select name="bg-tooltip-icon-select" id="" class="ga-tooltip-icon-select">
                            <?php
                            if ($tooltipIconStyle == "Icon") {
                                echo "<option value='Icon' class='ga-theme-optoins' selected>" . __('Icon', SIBG_DOMAIN) . "</option>";
                            } else {
                                echo "<option value='Icon' class='ga-theme-optoins'>" . __('Icon', SIBG_DOMAIN) . "</option>";
                            }
                            if ($tooltipIconStyle == "Hover") {
                                echo "<option value='Hover' class='ga-theme-optoins' selected>" . __('Hover', SIBG_DOMAIN) . "</option>";
                            } else {
                                echo "<option value='Hover' class='ga-theme-optoins'>" . __('Hover', SIBG_DOMAIN) . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                </tbody>
            </table>
            <input class="button-primary" type="submit" name="save_theme_settings"
                   value="<?php esc_html_e("Save ", SIBG_DOMAIN); ?>">
        </form>
        <?php
        if (isset($_POST["save_theme_settings"])) {
            $formTheme                           = sanitize_file_name($_POST["bg-theme-select"]);
            $siteThemeType                       = sanitize_file_name($_POST["bg-site-theme-select"]);
            $formMainColor                       = sanitize_hex_color($_POST["bg-form-color"]);
            $formFont                            = sanitize_file_name(explode("/", $_POST["bg-font-select"])[0]);
            $fontType                            = sanitize_file_name(explode("/", $_POST["bg-font-select"])[1]);
            $multiPageFormAnimation              = sanitize_file_name($_POST["bg-animation-select"]);
            $tooltipThemeClass                   = sanitize_file_name($_POST["bg-tooltip-select"]);
            $formTooltipPosition                 = sanitize_file_name($_POST["bg-tooltip-position"]);
            $tooltipIconStyle                    = sanitize_file_name($_POST["bg-tooltip-icon-select"]);
            $fontSize                            = sanitize_file_name($_POST["bg-font-size"]);
            $formCustomMeta                      = json_decode(gform_get_meta($formSettingID, "bg_custom_settings"), true);
            $formCustomMeta["form_theme"]        = $formTheme;
            $formCustomMeta["theme_type"]        = $siteThemeType;
            $formCustomMeta["main_color"]        = $formMainColor;
            $formCustomMeta["font_name"]         = $formFont;
            $formCustomMeta["font_type"]         = $fontType;
            $formCustomMeta["font_size"]         = $fontSize;
            $formCustomMeta["tooltip_class"]     = $tooltipThemeClass;
            $formCustomMeta["form_animation"]    = $multiPageFormAnimation;
            $formCustomMeta["tooltip_position"]  = $formTooltipPosition;
            $formCustomMeta["tooltip_view_type"] = $tooltipIconStyle;
            $formCustomMeta = json_encode($formCustomMeta);
            gform_update_meta($formSettingID, "bg_custom_settings", $formCustomMeta, $formSettingID);
            ?>
            <script>location.reload();</script>
            <?php
        }
    }

    public function BG_settings_tab(){
        $form_id = null;
        if (isset($_GET["id"])){
            if (is_numeric($_GET["id"])){
                $form_id = $_GET["id"];
            }
        }
        if ($form_id==null){
            die();
        }
        $is_form_exist = GFAPI::get_form($form_id);
        if(!$is_form_exist){
            die();
        }
        $jsonSettings      = json_decode(gform_get_meta($form_id , "bg_custom_settings"),true);
        $additionalSetting = $jsonSettings["additionalSetting"];
        $prevUX            = $additionalSetting["prev_UX"];
        ?>
        <form action="" method="POST">
            <table class="ga_form_settings" cellspacing="0" cellpadding="0">
                <tbody>

                <tr>
                    <th>
                        <?php esc_html_e("Previous button UX perfectly ",SIBG_DOMAIN); ?>
                    </th>
                    <td>
                        <?php
                        if ($prevUX == "true"){
                            echo "<input type='checkbox' class='ga-prebutton-ux' name='bg-prev-ux' checked>";
                        }else{
                            echo "<input type='checkbox' class='ga-prebutton-ux' name='bg-prev-ux'>";
                        }
                        ?>
                    </td>
                </tr>
                </tbody></table>
            <input class="button-primary" type="submit" name="save_settings_settings" value="<?php esc_html_e("Save ",SIBG_DOMAIN); ?>">
        </form>
        <?php
        if (isset($_POST["save_settings_settings"])) {
            $prevUX            = $_POST["bg-prev-ux"]       ? "true" : "false";
            $jsonSettings      = json_decode(gform_get_meta($form_id , "bg_custom_settings"),true);
            $additionalSetting["prev_UX"] = $prevUX;
            $jsonSettings["additionalSetting"] = $additionalSetting;
            $jsonSettings                      = json_encode($jsonSettings);
            gform_update_meta( $form_id ,"bg_custom_settings" ,$jsonSettings ,$form_id );
            ?>
            <script>location.reload();</script>
            <?php
        }
    }

    public function TooltipScript(){
        wp_enqueue_script( 'custom-admin', SIBG_js."custom-fields.js",  array( 'jquery' ),  SIBG_VERSION, true );
    }

    public function MyStandardSettings( $position, $form_id ) {

        //create settings on position 25 (right after Field Label)
        if ( $position == 10 ) {
            echo '<li class="gravity_tooltip">';

            // Set the label for the field.
            echo '<label class="section_label" for="custom_tooltip">';
            echo __( 'Tooltip', SIBG_DOMAIN );
            echo '</label>';

            // Set the input field.
            echo '<input type="text" class="fieldwidth-3" id="is_tooltip" size="35" onkeyup="SetFieldProperty(\'is_tooltip\', this.value);"/>';

            // Close the <li> tag.
            echo '</li>';


        }
        if ( $position == 25 ) {
            echo '<li class="Beauty_choose" style="display: none">';

            // Set the label for the field.
            echo '<label class="section_label" for="custom_tooltip">';
            echo __( 'View Mode', 'gravity-tooltips' );
            echo '</label>';

            // Set the input field.
            echo '<select id="view_mode" onchange=\'SetFieldProperty("view_mode", this.value);\'>';
            echo '<option value="default">'.__( "Default", "gravity-tooltips").'</option>';
            echo '<option value="toggle">'.__( "Toggle", "gravity-tooltips").'</option>';
            echo '<option value="Button">'.__( "Button", "gravity-tooltips").'</option></select>';
            // Close the <li> tag.
            echo '</li>';
        }

    }

}
if (is_admin()){
    $GravityTooltipInput = new sibg_backend();
    $GravityTooltipInput->GravityInit();
}

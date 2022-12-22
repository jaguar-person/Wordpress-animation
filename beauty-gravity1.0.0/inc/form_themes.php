<?php

$css    = "";
$formThemes = ["false"];

function sibg_GetThemesSettings(){

    global $wpdb,$table_prefix;

    $compiledCSS = "";
    $css         = "";
    $select      = $wpdb->get_results (
        "SELECT meta_value,entry_id 
                  FROM {$table_prefix}gf_entry_meta 
                  where meta_key='bg_custom_settings'"
        ,ARRAY_A );

    if (!empty($select)){
        if (is_rtl()){
            $css      = file_get_contents(SIBG_CSS.'themes/BG_Themes.rtl.min.css');
        }else{
            $css      = file_get_contents(SIBG_CSS.'themes/BG_Themes.min.css');
        }
        $compiledCSS  = sibg_GetGeneralCSS($css);
    }
    foreach ($select as $key=>$value){
        $form_value = json_decode($value["meta_value"],true);
        $form_id    = $value["entry_id"];
        $main_color = $form_value["main_color"];
        $form_theme = $form_value["form_theme"];
        $themeType  = $form_value["theme_type"];

        $fontName = str_replace("+","_",$form_value["font_name"]);
        $fontType = $form_value["font_type"];
        if ($fontName != "Default" && $fontName != "") {
            $compiledCSS .= ".BG_" . "{$fontName}" . "_font{font-family:'" . "{$fontName}" . "'," . $fontType . " !important}";
        }

        if ($form_theme != "Default"){
            $compiledCSS .= sibg_GetThemeCSS($form_theme,$form_id,$main_color,$themeType,$css);
        }
    }
    return $compiledCSS;

}

function sibg_GetGeneralCSS($css){
    $start       = strpos($css,"/*region form-general*/");
    $finish      = strpos($css,"/*endregion*/") + strlen("/*endregion*/");
    $length      = $finish - $start;
    $compiledCSS = substr($css,$start,$length);
    return $compiledCSS;
}

function sibg_GetThemeCSS($form_theme,$form_id,$main_color,$themeType,$css){
    global $formThemes;
    $start = strpos($css,"/*region ".$form_theme);
    
    if ($formThemes[$form_theme] !== true){
        $finishGeneral = strpos($css,"/*endregion*/",$start) + strlen("/*endregion*/");
        $lengthGeneral = $finishGeneral - $start;
        $compiledCSS   = substr($css,$start,$lengthGeneral);
        $startColor    = strpos($css,"/*region color*/",$start);
        $finishColor   = strpos($css,"/*endregion*/",$startColor) ;
        $finish        = strpos($css,"/*endregion*/",$finishColor) + strlen("/*endregion*/");
        $lengthColor   = $finish - $startColor;
        $colorCSS      = substr($css,$startColor,$lengthColor);
        $compiledCSS  .= str_replace("[form_id]",$form_id,$colorCSS);
        $compiledCSS   = sibg_GetThemeColor($form_theme,$compiledCSS,$main_color,$themeType);
        $formThemes[$form_theme] = true;
    }else{
        $startColor    = strpos($css,"/*region color*/",$start);
        $finishColor   = strpos($css,"/*endregion*/",$startColor) + strlen("/*endregion*/");
        $finish        = strpos($css,"/*endregion*/",$finishColor) + strlen("/*endregion*/");
        $lengthColor   = $finish - $startColor;
        $colorCSS      = substr($css,$startColor,$lengthColor);
        $compiledCSS   = str_replace("[form_id]",$form_id,$colorCSS);
        $compiledCSS   = sibg_GetThemeColor($form_theme,$compiledCSS,$main_color,$themeType);
		
    }
    return $compiledCSS;
}

function sibg_GetThemeColor($form_theme,$compiledCSS,$main_color,$themeType){
    $compiledCSS = str_replace("var(--option-background-color-dark)", "#363636",$compiledCSS);
    $compiledCSS = str_replace("var(--input-background-color)", "#ffffff36",$compiledCSS);
    $compiledCSS = str_replace("var(--error-color)", "red",$compiledCSS);

    
    if ($form_theme == "BG_Microsoft") {
        $compiledCSS = str_replace("var(--microsoft-color-primary-dark)", sibg_adjustBrightness($main_color, "-0.2"),$compiledCSS);
        $compiledCSS = str_replace("var(--microsoft-color-primary)", $main_color,$compiledCSS);
        $compiledCSS = str_replace("var(--microsoft-border-color)", "rgb(138, 136, 134)",$compiledCSS);
    }

    return $compiledCSS;
}

/**
 * Increases or decreases the brightness of a color by a percentage of the current brightness.
 *
 * @param   string  $hexCode        Supported formats: `#FFF`, `#FFFFFF`, `FFF`, `FFFFFF`
 * @param   float   $adjustPercent  A number between -1 and 1. E.g. 0.3 = 30% lighter; -0.4 = 40% darker.
 *
 * @return  string
 */
function sibg_adjustBrightness($hexCode, $adjustPercent) {
    $hexCode = ltrim($hexCode, '#');

    if (strlen($hexCode) == 3) {
        $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
    }

    $hexCode = array_map('hexdec', str_split($hexCode, 2));

    foreach ($hexCode as & $color) {
        $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
        $adjustAmount    = ceil($adjustableLimit * $adjustPercent);

        $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
    }

    return '#' . implode($hexCode);
}

function sibg_color_triadic($color){
    $color = str_replace('#', '', $color);
    $hexCode = str_split($color, 2);
    return '#'.$hexCode[2].$hexCode[0].$hexCode[1];
}

$compiledCSS = sibg_GetThemesSettings();
$upload_dir   = wp_upload_dir();
$file = $upload_dir["basedir"].'/beauty_gravity';
wp_mkdir_p( $file );
$file .= '/generated-style.css';
file_put_contents($file, $compiledCSS);
$file = $upload_dir["baseurl"].'/beauty_gravity/generated-style.css';
wp_enqueue_style("theme_style",$file,"",SIBG_VERSION);
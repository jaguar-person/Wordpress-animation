jQuery(document).ready(function () {
    jQuery("body").on("change",".bg-tooltip-select",function(){
        var tooltipType = jQuery(this).val()
        if (tooltipType == "None") {
            jQuery(".gf_tooltip_body").each(function () {
                jQuery(this).remove()
            })
        }else {
            jQuery(".gf_tooltip_body").each(function () {
                jQuery(this).remove()
            })
            jQuery(".tooltip_pos_body").each(function () {
                var pos   = jQuery(this).attr("data-position")
                var theme = jQuery(".ga-site-theme-select").val()
                var text  = jQuery(this).attr("data-text")
                theme = theme == "Dark" ? "Light":"Dark"
                jQuery(this).append('<span class="gf_tooltip_body '+tooltipType+' '+theme+'" data-position="'+pos+'"><i class="dashicons dashicons-editor-help"></i><span>'+text+'</span></span>')
            })
        }
    })

    jQuery("body").on("change",".ga-site-theme-select",function(){

        var theme = jQuery(this).val()
        jQuery(".gf_tooltip_body").each(function () {
            jQuery(this).removeClass("Dark")
            jQuery(this).removeClass("Light")
            if (theme == "Dark"){
                jQuery(this).addClass("Light")
            } else {
                jQuery(this).addClass("Dark")
            }
        })
    })

    jQuery('.ga_form_settings').on('mouseup','.iris-palette',function(){
        setTimeout(function () {
            var mainColor = jQuery('.my-color-field').val()
            jQuery("#bg-color-picker").val(mainColor)
        },10)
    })
    jQuery('body').on('mouseup','.iris-square-handle',function(){
        var mainColor = jQuery('.my-color-field').val()
        jQuery("#bg-color-picker").val(mainColor)
    })
    jQuery('body').on('mouseup','.ui-slider-handle',function(){
        var mainColor = jQuery('.my-color-field').val()
        jQuery("#bg-color-picker").val(mainColor)
    })
    jQuery('body').on('mouseup','.wp-picker-clear',function(){
        jQuery('.my-color-field').val("#fff")
        jQuery("#bg-color-picker").val("#fff")
    })



    jQuery('body').on("click",function (e) {
        var sor_container = jQuery(".iris-picker");
        if (!sor_container.is(e.target) && sor_container.has(e.target).length === 0) {
            var color = jQuery(".my-color-field").val()
            if(color == "" || color=="#"){
                jQuery("#bg-color-picker").val("#fff")
            }else{
                jQuery("#bg-color-picker").val(color)
            }
        }

    })

})
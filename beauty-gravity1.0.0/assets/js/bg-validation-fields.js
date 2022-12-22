function bg_form_animation(button,animation,form_id,current_page) {
    var next = current_page + 1
    var prev = current_page - 1
    var val = null
    if (button == "next"){
        val = current_page + 1
    } else{
        val = current_page - 1
    }

    jQuery("#gform_target_page_number_"+form_id).val(val);
    jQuery("#gform_"+form_id).trigger("submit",[true]);
    jQuery('#gform_page_' + form_id + '_' + current_page).find(".gform_page_footer").append('<i class="icon-spin5 bg-spin"></i>')

    setTimeout(function () {
        jQuery('#gform_page_' + form_id + '_' + current_page).find(".gform_page_footer").find("input").prop("disabled",true)
        jQuery('#gform_page_' + form_id + '_' + next).find(".gform_page_footer").find("input").prop("disabled",true)
        jQuery('#gform_page_' + form_id + '_' + prev).find(".gform_page_footer").find("input").prop("disabled",true)
        jQuery('#gform_page_' + form_id + '_' + current_page).find(".gform_page_footer").find("input").addClass("bg_disabled")
        jQuery('#gform_page_' + form_id + '_' + next).find(".gform_page_footer").find("input").addClass("bg_disabled")
        jQuery('#gform_page_' + form_id + '_' + prev).find(".gform_page_footer").find("input").addClass("bg_disabled")
    },50)

    jQuery('#gform_ajax_frame_'+form_id).unbind("load").on('load', function() {
        var form_content = jQuery('#gform_ajax_frame_'+form_id).contents().find('#gform_wrapper_'+form_id);
        jQuery(form_content).find('#gform_page_' + form_id + '_' + current_page).find(".gform_page_footer").find("input").addClass("bg_disabled")
        jQuery(form_content).find('#gform_page_' + form_id + '_' + prev).find(".gform_page_footer").find("input").addClass("bg_disabled")
        jQuery(form_content).find('#gform_page_' + form_id + '_' + next).find(".gform_page_footer").find("input").addClass("bg_disabled")
        var contents = jQuery(this).contents().find('*').html();
        var is_postback = contents.indexOf('GF_AJAX_POSTBACK') >= 0;
        if (!is_postback) {
            return;
        }
        var form_content = jQuery(this).contents().find('#gform_wrapper_'+form_id);
        var is_confirmation = jQuery(this).contents().find('#gform_confirmation_wrapper_'+form_id).length > 0;
        var is_redirect = contents.indexOf('gformRedirect(){') >= 0;
        var is_form = form_content.length > 0 && !is_redirect && !is_confirmation;
        if (is_form) {
            if (form_content.hasClass('gform_validation_error')) {
                jQuery('#gform_wrapper_' + form_id).addClass('gform_validation_error');
                jQuery('#gform_wrapper_' + form_id).html(form_content.html())
                setTimeout(function () {
                    jQuery('#gform_page_' + form_id + '_' + current_page).find(".gform_page_footer").find("input").removeClass("bg_disabled")
                },8)

                jQuery(document).trigger('bg_page_loaded')
            } else {
                jQuery('#gf_progressbar_wrapper_'+form_id).find(".gf_progressbar").find("span").css("visibility","hidden")
                jQuery('#gform_page_' + form_id + '_' + current_page).find(".gform_page_footer").find("i").remove()
                jQuery('#gf_progressbar_wrapper_'+form_id).find("h3").css("visibility","hidden")
                var animation_function = animation + "_"+button+"_button(" + form_id + "," + current_page + ")"
                eval(animation_function)
            }

            setTimeout(function () {
                /* delay the scroll by 50 milliseconds to fix a bug in chrome */
            }, 50);
            if (window['gformInitDatepicker']) {
                gformInitDatepicker();
            }
            if (window['gformInitPriceFields']) {
                gformInitPriceFields();
            }
            jQuery(document).trigger('gform_page_loaded', [1, current_page]);
            window['gf_submitting_' + form_id] = false;
        }
         else if (!is_redirect) {
            var confirmation_content = jQuery(this).contents().find('.GF_AJAX_POSTBACK').html();
            if (!confirmation_content) {
                confirmation_content = contents;
            }
            var currentHeight = jQuery('#gform_wrapper_' + form_id).height()
            jQuery('#gform_wrapper_' + form_id).html(confirmation_content)
            var subHeight = jQuery('#gform_wrapper_' + form_id).height()
            jQuery('#gform_wrapper_' + form_id).css({transition:"height 0.5s ease-in-out",height:currentHeight,opacity:"0"})
            setTimeout(function () {
                jQuery('#gform_wrapper_' + form_id).css({opacity:"1","height":subHeight})
            },10)



            setTimeout(function() {
                jQuery(document).trigger('gform_confirmation_loaded', [1]);
                window['gf_submitting_'+form_id] = false;
            }, 50);
        } else {
            if (window['gformRedirect']) {
                gformRedirect();
            }
        }
        jQuery(document).trigger('gform_post_render', [1, current_page]);
    });
}
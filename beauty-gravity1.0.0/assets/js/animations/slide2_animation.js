function Slide2_next_button(form_id,current){

    var next = current + 1;

    var pageCNT = jQuery("#gform_"+form_id).find(".gform_body").children().length
    var percent = Math.floor((next/pageCNT)*100)
    if (percent<98){
        percent += "%"
    }else {
        percent = "100%"
    }

    var currentHeight = jQuery("#gform_page_" + form_id + "_" + current).find(".gform_page_fields").height()
    jQuery("#gform_page_" + form_id + "_" + next).css({opacity:"0",display:"block"})
    var nextHeight = jQuery("#gform_page_" + form_id + "_" + next).find(".gform_page_fields").height()
    jQuery("#gform_page_" + form_id + "_" + next).removeAttr("style")
    jQuery("#gform_page_" + form_id + "_" + next).css("display","none")

    jQuery("#gform_page_" + form_id + "_" + current).find(".gform_page_fields").css("height",currentHeight)
    if (currentHeight >= nextHeight) {
        setTimeout(function () {
            jQuery("#gform_page_" + form_id + "_" + current).find(".gform_page_fields").css({ height: nextHeight,transition:"height 0.3s ease-in-out" })
        },  400)
    } else {
        setTimeout(function () {
            jQuery("#gform_page_" + form_id + "_" + current).find(".gform_page_fields").css({ "height": nextHeight,transition:"height 0.3s ease-in-out"})
        },1)
    }

    jQuery(".gf_progressbar_percentage").animate({"width":percent},700);
    jQuery("#gform_page_"+form_id+"_"+current).find(".gform_page_fields").css({position: "relative",width:"100%"})
    jQuery("#gform_page_"+form_id+"_"+next).css({display:"block",position: "absolute",width:"100%",left:"0",top:"0"})
    jQuery("#gform_page_"+form_id+"_"+next).find(".gform_page_fields").css("opacity","0")
    jQuery("#gform_page_"+form_id+"_"+next).find(".gform_page_footer").css("opacity","0")
    setTimeout(function () {
        jQuery("#gform_page_"+form_id+"_"+current).find(".gform_page_fields").addClass("s2body-next-go")
    },1)
    setTimeout(function () {
        jQuery("#gform_page_"+form_id+"_"+next).find(".gform_page_fields").addClass("s2body-next-come")
    },300)
    setTimeout(function () {
        var form_content = jQuery('#gform_ajax_frame_'+form_id).contents().find('#gform_wrapper_'+form_id);
        jQuery('#gform_wrapper_' + form_id).html(form_content.html())
        setTimeout(function () {
            jQuery('#gform_page_' + form_id + '_' + next).find(".gform_page_footer").find("input").removeClass("bg_disabled")
        },8)

        jQuery(document).trigger('bg_page_loaded')
    },710)
}
function Slide2_prev_button(form_id,current){

    var prev = current - 1;

    var pageCNT = jQuery("#gform_"+form_id).find(".gform_body").children().length
    var percent = Math.floor((prev/pageCNT)*100)+"%"


    var currentHeight = jQuery("#gform_page_" + form_id + "_" + current).find(".gform_page_fields").height()
    jQuery("#gform_page_" + form_id + "_" + prev).css({opacity:"0",display:"block"})
    var prevHeight = jQuery("#gform_page_" + form_id + "_" + prev).find(".gform_page_fields").height()
    jQuery("#gform_page_" + form_id + "_" + prev).removeAttr("style")
    jQuery("#gform_page_" + form_id + "_" + prev).css("display","none")

    jQuery("#gform_page_" + form_id + "_" + current).find(".gform_page_fields").css("height",currentHeight)
    if (currentHeight >= prevHeight) {
        setTimeout(function () {
            jQuery("#gform_page_" + form_id + "_" + current).find(".gform_page_fields").css({transition:"height 0.3s ease-in-out",height: prevHeight})
        },  400)
    } else {
        setTimeout(function () {
            jQuery("#gform_page_" + form_id + "_" + current).find(".gform_page_fields").css({transition:"height 0.3s ease-in-out",height: prevHeight})
        },1)
    }
    jQuery(".gf_progressbar_percentage").animate({"width":percent},700);

    jQuery("#gform_page_"+form_id+"_"+current).find(".gform_page_fields").css({position: "relative",width:"100%"})
    jQuery("#gform_page_"+form_id+"_"+prev).css({display:"block",position: "absolute",width:"100%",left:"0",top:"0"})
    jQuery("#gform_page_"+form_id+"_"+prev).find(".gform_page_fields").css("opacity","0")
    jQuery("#gform_page_"+form_id+"_"+prev).find(".gform_page_footer").css("opacity","0")
    setTimeout(function () {
        jQuery("#gform_page_"+form_id+"_"+current).find(".gform_page_fields").addClass("s2body-prev-go")
    },1)
    setTimeout(function () {
        jQuery("#gform_page_"+form_id+"_"+prev).find(".gform_page_fields").addClass("s2body-prev-come")
    },300)
    setTimeout(function () {
        var form_content = jQuery('#gform_ajax_frame_'+form_id).contents().find('#gform_wrapper_'+form_id);
        jQuery('#gform_wrapper_' + form_id).html(form_content.html())
        setTimeout(function () {
            jQuery('#gform_page_' + form_id + '_' + prev).find(".gform_page_footer").find("input").removeClass("bg_disabled")
        },8)

        jQuery(document).trigger('bg_page_loaded')
    },710)

}

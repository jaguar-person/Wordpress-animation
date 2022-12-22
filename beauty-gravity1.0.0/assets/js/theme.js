jQuery(document).ready(function(){
    selectSizeFix();
    buttonModeFix();
    fileInputFix();
    tooltipResp();
    errors();

    jQuery(document).on('bg_page_loaded', function () {
        selectSizeFix();
        buttonModeFix();
        fileInputFix();
        tooltipResp();
        errors();
    })

    function selectSizeFix(){
        jQuery('select[multiple=multiple]').each(function(){
            var count = jQuery(this).children().length
            jQuery(this).attr('size',count)
            var multiple_height = jQuery(this).height()
            jQuery(this).css({"height":""+multiple_height+8+" !important"})
        })
    }
   
    function buttonModeFix(){

        jQuery('.BG_Button').each(function(){
            if(!jQuery(this).hasClass('BG_Hover')){
                jQuery(this).find('label').each(function(){
                    if(jQuery(this).siblings('.gf_tooltip_body')[0]){
                        if(jQuery('html').attr('dir')=="rtl"){
                            jQuery(this).css({"padding-left":"35px"})
                            jQuery(this).siblings('.gf_tooltip_body').css({left:"5px"})
                        }
                        else{
                            jQuery(this).css({"padding-right":"35px"})
                            jQuery(this).siblings('.gf_tooltip_body').css({right:"10px"})
                        }
                    }
                })
            }
        })
        
        jQuery('.BG_Button .gf_tooltip_body').on('mouseover',function(){
            var hover_color = jQuery(this).parents('form').find('input[type=submit]:not(.gform_previous_button)').css('background-color');
            jQuery(this).siblings('label').css({"background":hover_color,"color": "white","border-color":hover_color})
            jQuery(this).find('i').css('color','white')
        })
        
        jQuery('.BG_Button .gf_tooltip_body').on('mouseleave',function(){
            jQuery(this).siblings('label').removeAttr('style')
            if(!jQuery(this).parents('.BG_Hover')[0]){
                if(jQuery('html').attr('dir')=="rtl"){
                    jQuery(this).siblings('label').css({"padding-left":"35px"})
                }
                else{
                    jQuery(this).siblings('label').css({"padding-right":"35px"})
                }
            }
            jQuery(this).find('i').removeAttr('style');
        })
            
        jQuery('.BG_Button .gf_tooltip_body i').on('click',function(){
            jQuery(this).parent().siblings('label').trigger('click');
        })
    }

    function fileInputFix(){

        jQuery('.ginput_preview').each(function(){
                var elm = jQuery(this).parent();
                showName(elm);
        })
 
        function showName(elm){
            try{
                var text = elm[0].files[0].name;
            }
            catch{
                var text = elm.find('strong').text();
            }
            var validation_message = elm.siblings('.validation_message').text().split('-');

            if(validation_message.length>1){
                
                elm.siblings('.BG_fileupload_text').text(validation_message[validation_message.length-1]);
                elm.siblings('.BG_fileupload_text').css('color','red')
            }
            else{
                elm.siblings('.BG_fileupload_text').css('color','inherit')
                
                var ext = text.split('.')
                var validExt = elm.siblings('.screen-reader-text').text().replace('Accepted file types:','').slice(0,-1)
                var validExts = []
                validExt = validExt.split(',')
                
                jQuery.each(validExt,function(i , item){
                    validExts.push(item.trim())
                })

                if(jQuery.inArray(ext[ext.length-1].toLowerCase(),validExts)<0 && text!=""){
                    elm.siblings('.BG_fileupload_text').text('File extension must be '+validExt+'.');
                    elm.siblings('.BG_fileupload_text').css('color','red')
                }
                
                else if(text!=""){
                    elm.siblings('.BG_fileupload_text').text(text);
                    elm.parents('.ginput_container_fileupload').find('i').eq(0).addClass('BG_fileupload_icon_selected');
                    elm.parents('.ginput_container_fileupload').find('i').eq(0).removeClass('BG_fileupload_icon');
                    if(jQuery(window).width()>705){
                        elm.parents('.BG_fileupload').width('calc(50% - 35px)');
                    }else{
                        elm.parents('.BG_fileupload').width('calc(100% - 35px)');
                    }
                   
                    if(elm.parents('.ginput_container_fileupload').find('.BG_filecancel_icon').length==0){
                        if(elm.parents('form').hasClass('BG_Android')){
                            elm.parents('.BG_fileupload').append('<i class="BG_filecancel_icon"></i>');
                        }
                        else{
                            elm.parents('.BG_fileupload').after('<i class="BG_filecancel_icon"></i>');
                        }
                    }
                }
                else{
                    elm.siblings('.BG_fileupload_text').text("Upload File");
                    elm.parents('.ginput_container_fileupload').find('i').eq(0).removeClass('BG_fileupload_icon_selected');
                    elm.parents('.ginput_container_fileupload').find('i').eq(0).addClass('BG_fileupload_icon');
                    elm.parents('.ginput_container_fileupload').find('.BG_filecancel_icon').remove();
                }
            }
        }

        jQuery('body').on('change','input[type="file"]',function(){
            var elm = jQuery(this);
            showName(elm);
        })

        jQuery('body').on('click','.BG_filecancel_icon',function(e){
            e.preventDefault();
            var target = jQuery(this).siblings('.BG_fileupload').find('.gform_delete');
            var elm = jQuery(this).parents('.ginput_container_fileupload').find('input[type="file"]');
            if(target.length>0){
                target.trigger('click');
            }
            else{
                elm.val("")
            }
            showName(elm);
            elm.parents('.BG_fileupload').width('calc(50% - 8px)');
        })
    }


    // tooltip responsive

    function tooltipResp(){
        
    var def_pos = "";
    jQuery('.gf_tooltip_body').on('mouseover',function(){
        def_pos = jQuery(this).attr('data-position');
        var rect = jQuery(this).children('span')[0].getBoundingClientRect();
        var container = jQuery(window).width();
        var elm = jQuery(this);
        var pos = ["TR","T","TL","R","L","BR","B","BL"];

       
        if(rect.x+rect.width>container || rect.x<0){
            var is_check = true;
            checkPos();

            if(is_check){
                elm.children('span').css('max-width','150px')
                checkPos();
            }

            function checkPos(){
                jQuery.each(pos,function(i,item){
                    if(is_check){
                        elm.attr('data-position',item);
                        var rect2 = elm.children('span')[0].getBoundingClientRect();
                        if (!(rect2.x+rect.width>container) && !(rect2.x<0)){
                            is_check = false;
                        }
                    }
                })
            }
        }
    })

    jQuery('.gf_tooltip_body').on('mouseleave',function(){

        setToDef(jQuery(this));
        
    })

    function setToDef(elm){
        setTimeout(function(){
            jQuery(this).attr('data-position','def_pos');
        },400)
    }
    }

    // show error 
     function errors(){
         jQuery('form:not(.bg_default_theme) .gfield_description.validation_message').each(function(i,item){

            
            var error_text = jQuery(this).text();
            console.log(error_text)
            jQuery(this).parents('li').append('<div class="bg_error_message">'+error_text+'</div>')

         })
     }
    
})
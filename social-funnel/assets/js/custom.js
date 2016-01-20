jQuery(document).ready(function($){
    
    /* Remove any alert box if already exists*/
    removeAlert();
    /* Checkbox with green tich */
    $('.checkradios').checkradios();
    
    $("a[data-toggle='tab']").click(function(){
        //return false; 
    });

	//sc edits
	
	if($(".sc_tut_button").length > 0) {
		if($(".video_tut").length > 0) {
			$(".sc_tut_button").css("display", "inline-block");
		}
	}
	if($(".sc_tut_button_steps").length > 0) {
		$(".sc_tut_button_steps").css("display", "inline-block");
	}
	$(".sc_tut_button_steps").click(function(){
		$tab = $(".tab-pane:visible").attr("id");
console.log($tab);
		if($tab == "gernal_sett") {
			$show = 1;
		} else if($tab == "layout_sett") {
			$show = 2;
		} else if($tab == "content_sett") {
			$show = 3;
		} else if($tab == "gift_sett") {
			$show = 4;
		}
	console.log(".step"+$show);
		$(".step"+$show).css(
			"left", ($(window).width() / 2) - ($(".step"+$show).width() / 2)
		);
		$("#mask").show();
		$(".step"+$show).animate({
			opacity: 1,
			top: "150px"
		}, 500);
		return false;
	});


	$(".sc_tut_button").click(function(){
		$(".video_tut").css(
			"left", ($(window).width() / 2) - ($(".video_tut").width() / 2)
		);
		$("#mask").show();
		$(".video_tut").animate({
			opacity: 1,
			top: "150px"
		}, 500);
		return false;
	});
	
	$("#mask, .video_tut_close").click(function(){
		$(".video_tut").animate({
			opacity: 0,
			top: "-500px"
		}, 500);
		
		$(this).parent().find("iframe").attr('src', $(this).parent().find("iframe").attr('src'));

		
		$("#mask").hide();
	});
	
	//sc end


    $(".previous").click(function(){
    	$(".finish").hide();
    	var index = $("ul[role='tablist'] > li.active").index();

    	if(index ==1){
            
    		$(".previous").hide(); 
    	}
    	if(index ==3){
    		$(".next").show();
    	}
    	$("ul[role='tablist'] > li.active").removeClass('active');
    	$("ul[role='tablist'] li:nth-child("+parseInt(index)+")").addClass('active');
    	$(".tab-pane.active").removeClass('active');
    	$(".tab-pane:nth-child("+parseInt(index)+")").addClass('active');
    	return false; 
    });
    
    $(".next").click(function(){
    	$(".finish").hide();
    	var index = $("ul[role='tablist'] > li.active").index() + 2;
    	if(index==2){
    		$(".previous").show(); 
    	}
    	if(index ==4){
    		$(".next").hide();
    		$(".finish").show();
    	}
    	$("ul[role='tablist'] > li.active").removeClass('active');
    	$("ul[role='tablist'] li:nth-child("+parseInt(index)+")").addClass('active');
    	$(".tab-pane.active").removeClass('active');
    	$(".tab-pane:nth-child("+parseInt(index)+")").addClass('active');
    	return false; 
    });

    $(".autoresponder").change(function(){
        $(".ar").hide();
        var val = $(this).val();
        $("."+val).show();
    });
    
    $(".popup_style_wrapper .popu_styl_sect").hover(function(){
        $(this).find(".dash_popup_hover").show();
    },function(){
        $("span.dash_popup_hover").not(this).hide();
    });
    
    $(".popu_styl_sect").click(function(){
        $(".popu_styl_sect").removeClass('active');
        $(this).addClass('active');
        $("#popstyle").val( $(this).data('popstyle') );
        $(".popstyleoption").hide(); 
        //$("#main_heading").show();
        $("#popstyleoption"+$(this).data('popstyle')).show();
    });
    
    $(".gift_feature > .add_btn").click(function(){
        if($("#featurediv > .well").length == 0){
            $(".remove_btn").show();
        }
        var html = $("#featureContent").html();
        var appendedhtml = $("#featurediv").append(html);
        var jay =  $('#featurediv .well').last();
        jay.find(".tbupload").removeData('to').attr("data-to","giftfeatureimage-"+ $("#featurediv > .well").length);
        jay.find(".giftfeatureimage").removeClass('giftfeatureimage').addClass("giftfeatureimage-"+$("#featurediv > .well").length); 
        return false;  
    });
    
    $(".gift_feature > .remove_btn").click(function(){
        $("#featurediv > .well").last().remove();
         if($("#featurediv > .well").length == 0){
            $(".remove_btn").hide();
        }
        return false;  
    });
    
    $(".dashb_in_wrap").on('click','.tbupload', function(){
        $(".tbuploadcurrent").val($(this).data('to')); 
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });

    $('.giftupload').click(function(){    
        $(".tbuploadcurrent").val($(this).data('to'));     
        tb_show('', 'media-upload.php?TB_iframe=true');
        return false; 
    });
    
    window.send_to_editor = function(html) {
        var cls = $(".tbuploadcurrent").val();
        if(cls == 'featureimagebrowse'){
           if(html.indexOf("caption") > -1)
           {
             var html = $.parseHTML( html );
             var href = $('img',html).attr('src');
           }else
           {
             var href = $(html).attr('href');
           }
            $(".gifturl").val(href);  
        }else{
            var html = $.parseHTML( html );
            var image_url = $('img',html).attr('src'); 
            $("."+cls).find("img").attr('src',image_url);
            $("."+cls).find("input").val(image_url);
        }
        tb_remove();
    }

    $("#campaign_table").on("click",".deleteme", function(){
        $('#deleteid').val($(this).data('delid')); 
    });

    $("#deleteconfirm").click(function(){
        $.ajax({   
            url:ajaxurl, 
            data:{action:'delete_campaign',delid:$("#deleteid").val()},
            method:'post',
            dataType:'html',
            success:function(response){
                if($.trim(response) == 'success'){
                    var html = '<div class="alert campaign_successfully_deleted"><a data-dismiss="alert" class="close" href="#"><span class="fa fa-close"></span></a>Campaign successfully deleted!</div>';
                    $("#messages").append(html);
                    $('html, body').animate({
                        scrollTop: $('.dash_b_content_wrap').offset().top
                    }, 1000);
                    $("#campain_delete").modal('hide');
                    $(".deleteme[data-delid='"+$("#deleteid").val()+"']").parents('tr').css('background-color','#a14646').fadeOut(1000); 
                    removeAlert(); 
                }
            },
            error:function(xhr, text, message){
                console.log(text); 
            }
        });
        return false; 
    });

    $("#deletecancel").click(function(){
        $("#campain_delete").modal('hide');
        return false; 
    });

    $("#campaign_table").on("click",".linkme", function(){
        $(".slugname").html($(this).data('slug'));
        $(".uniqueid").html($(this).data('unique'));
    });


    $("#campaign_table").on("click",".copyme", function(){
        var id = $(this).data('copyid');
        $.ajax({
            url:ajaxurl, 
            data:{action:'copy_campaign',id:id},
            method:'post',
            dataType:'json',
            success:function(response){
                var html = '<tr><td class="campain_title"><h4><a href="/wp-admin/admin.php?page=social-funnel&sf=add-compaign&edit-id='+response.id+'">'+response.cpname+'</a></h4><a href="/wp-admin/admin.php?page=social-funnel&sf=add-compaign&edit-id='+response.id+'">Edit</a> | <a href="#" data-toggle="modal" data-target="#campain_links" data-id="'+response.id+'"  class="linkme" data-slug="'+response.slug+'" data-unique="'+response.unique+'">Link</a> | <a href="#">Export</a> | <a  data-toggle="modal" data-target="#campain_delete" data-delid="'+response.id+'" class="deleteme">Delete</a> | <a href="#" data-copyid="'+response.id+'" class="copyme">Copy</a> | <a href="#" data-toggle="modal" data-target="#campain_share" data-slug="'+response.slug+'" class="share">Share</a></td><td align="center">0</td><td align="center">0</td><td align="center">0</td><td align="center">0</td></tr>';
                var msg = '<div class="alert campaign_successfully_copied"><a data-dismiss="alert" class="close" href="#"><span class="fa fa-close"></span></a>Campaign successfully copied!</div>';
                $("#campaign_table").append(html).fadeIn();
                 $("#messages").append(msg);
                 $('html, body').animate({
                    scrollTop: $('.dash_b_content_wrap').offset().top
                }, 1000);
                removeAlert();
            },  
            error:function(xhr, text, message){
                console.log(text); 
            }
        });
        return false; 
    });

    $("#campaign_table").on('click','.export',function(){
        var slug = $(this).data('slug');
        $.ajax({
            url:ajaxurl,
            method:'post',
            dataType:'html',
            data:{action:'export_campaign',id:$(this).data('id')},
            success:function(response){
                var iframe = $('<iframe>',{
                    width:1,
                    height:1,
                    frameborder:0,
                    css:{
                        display:'none'
                    }
                }).appendTo('body');

                var formHTML = '<form action="" method="post">'+
                    '<input type="hidden" name="filename" />'+
                    '<input type="hidden" name="content" />'+
                    '</form>';
                setTimeout(function(){
                    var body = (iframe.prop('contentDocument') !== undefined) ?
                        iframe.prop('contentDocument').body :
                        iframe.prop('document').body; 
                    body = $(body);
                    body.html(formHTML);
                    var form = body.find('form');
                    form.attr('action',exporturl);
                    form.find('input[name=filename]').val(slug+".txt");
                    form.find('input[name=content]').val(response);
                    form.submit();
                },50);
            }
        });
        return false; 
    });

    $("#campaign_table").on('click','.share',function(){
        $(".twitter_icon_1").attr('href',"http://twitter.com/home?status="+siteurl+'/'+$(this).data('slug'));  
        $(".facebook_icon_1").attr('href',"http://www.facebook.com/sharer.php?u="+siteurl+'/'+$(this).data('slug')); 
        $(".google_plus_icon_1").attr('href',"https://plus.google.com/share?url="+siteurl+'/'+$(this).data('slug')); 
        $(".linkedin_icon_1").attr('href',"https://www.linkedin.com/cws/share?url="+siteurl+'/'+$(this).data('slug')); 
        $(".pinterest_icon_1").attr('href',"https://www.pinterest.com/pin/create/button/?url="+siteurl+'/'+$(this).data('slug')); 
        $(".stumbleupon_icon_1").attr('href',"http://www.stumbleupon.com/submit?url="+siteurl+'/'+$(this).data('slug')); 

    }); 

    $(".vidoe_disable_enble .checkradios-checkbox").click(function(){
        $("#videodiv").toggle();
    });

    function removeAlert(){
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 2000);
    }
});


/*jQuery(document).ready(function($) {
	 $(".animsition").animsition({
	  
		inClass               :   'fade-in-down',
		outClass              :   'fade-out-down',
		inDuration            :    1500,
		outDuration           :    800,
		linkElement           :   '.animsition-link',
		// e.g. linkElement   :   'a:not([target="_blank"]):not([href^=#])'
		loading               :    true,
		loadingParentElement  :   'body', //animsition wrapper element
		loadingClass          :   'animsition-loading',
		unSupportCss          : [ 'animation-duration',
								  '-webkit-animation-duration',
								  '-o-animation-duration'
								],
	
		
		overlay               :   false,
		
		overlayClass          :   'animsition-overlay-slide',
		overlayParentElement  :   'body'
	  });

   $("html").niceScroll({});
  
});*/



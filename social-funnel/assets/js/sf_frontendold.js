jQuery(document).ready(function($){
	setTimeout(function() {
		$("#popup_style"+style).modal('show');
	}, time);
	$('#test').click(function(){
		$("#popup_style"+style).modal('show');

	});
	$(".card").flip({
        trigger: "manual"
    });
    $("#flip-btn").click(function(){
      	$(".card").flip(true);
    });
    
    $("#savebtn").click(function(){
    	var action = $(this).parents('form').attr('action');
        var optin = $("#is_optin").data('optin');
        var optinid = $("#is_optin").data('unique');
        var userid = $("#is_optin").data('userid');
        var slug = $("#is_optin").data('slug');
        /*if(optin == 't'){
            optinid = $("#is_optin").data('unique');
        }*/
    	$.ajax({
    		url:ajaxurl, 
    		method:'post',
    		dataType:'html',
    		data:{
	                action:'sf_subscribe',
	                name:$("#sub-form-name").val(), 
	                email:$("#sub-form-email").val(),
	                id:$("#unique_id").val(),
	                optin:optin,
	                optinid:optinid,
                    userid:userid,
                    slug:slug, 
	            },
    		success:function(response){
    			if($.trim(response) == 'email_exists'){
    				alert('Email already exists');
    				$("#sub-form-email").css("border-color","red");
    				$("#sub-form-email").focus();
                    return false;
    			}else if($.trim(response) == 'fail'){
    				alert('There is some error. Please try after some time.');
                    return false;
    			}else{
                    var aweber_type = $('#aweber_type').val();
                    var target_url = $("#target_url").val();
                    var main_url = target_url+"?i="+response;
                    if(aweber_type == "aweber")
                    {   
                        $("#weber_id").val(main_url);
                        $("#weber_metaid").val(main_url);
                        window.setTimeout(function() {
                            $("#subscriptionForm").submit();
                        }, 200);
                    }else if(aweber_type == "getresponse")
                    {
                        //$("#subscriptionForm").submit();
                        window.setTimeout(function() {
                             window.location.href = "?i="+response;
                        }, 1500);
                    }
                }
    		},
    	});
        return false; 
    });

    



    // $('.buttonContainer .image').on("click", function(e){
    //     var f = $('.af-form-wrapper').serialize();
    //     var act = $('.af-form-wrapper').attr("action");
    //     alert("jay");
    //     $.ajax({
    //             url: act,
    //             type:"post",
    //             data : f,
    //             //headers: { 'Access-Control-Allow-Origin': '*' },
    //             crossDomain: true,
    //             success: function(data)
    //             {
    //                 alert("yes");
    //             },
    //             error: function(response)
    //             {
    //                 alert("no");
    //                 submit_byweber();
    //             }
    //     });

    //       return false;
    //       e.preventDefault(); 
    // });

});

// function submit_byweber(){
//         var action = $(this).parents('form').attr('action');
//         var optin = $("#is_optin").data('optin');
//         var optinid = $("#is_optin").data('unique');
//         var userid = $("#is_optin").data('userid');
//         var slug = $("#is_optin").data('slug');
//         /*if(optin == 't'){
//             optinid = $("#is_optin").data('unique');
//         }*/
//         $.ajax({
//             url:ajaxurl, 
//             method:'post',
//             dataType:'html',
//             data:{
//                     action:'sf_subscribe',
//                     name:'', 
//                     email:'',
//                     id:$("#unique_id").val(),
//                     optin:optin,
//                     optinid:optinid,
//                     userid:userid,
//                     slug:slug, 
//                 },
//             success:function(response){
//                 if($.trim(response) == 'email_exists'){
//                     alert('Email already exists');
//                     $("#sub-form-email").css("border-color","red");
//                     $("#sub-form-email").focus();
//                     return false;
//                 }else if($.trim(response) == 'fail'){
//                     alert('There is some error. Please try after some time.');
//                     return false;
//                 }else{
//                     window.location.href = "?i="+response;
//                 }
//             },
//         });
//         return false; 
//     };

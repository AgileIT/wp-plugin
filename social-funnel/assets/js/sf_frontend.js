jQuery(document).ready(function($){
    setTimeout(function() {
        $("#popup_style"+style).modal({backdrop: "static"});
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
        var emailReg = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
     	if($("[name=\'name\']").length == 1) {
     		if($("[name=\'name\']").val() == "") {
     			$("[name=\'name\']").css("border","2px solid red");
     			shake();
     			return false;
     		} else {
     			$("[name=\'name\']").css("border","none");
     		}
     	}
		var valid = validateEmail($("[type=\'email\']").val());
		
		if(!valid) {
			alert("bad email");
			$("[name=\'email\']").css("border","2px solid red");
			shake();
	      	return false;
	    }
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
                    alert("asdghfjjasd11");
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
                    var show_name = $("#show_name").val();
                    if(aweber_type == "aweber")
                    {   
                        $("#weber_id").val(main_url);
                        $("#weber_metaid").val(main_url);
                        window.setTimeout(function() {
                            $("#subscriptionForm").submit();
                        }, 100);
                    }else if(aweber_type == "getresponse")
                    {
                        $("#subscriptionForm").submit();
                        window.setTimeout(function() {
                             window.location.href = "?i="+response;
                        }, 3000);
                    }else if(aweber_type == "betaGetresponse"){
                        $("#subscriptionForm").submit();
                        //alert($("#subscriptionForm").submit()+"aakashsir");
                        window.setTimeout(function() {
                          //  alert("aakash");
                             window.location.href = "?i="+response;
                        }, 3000);
                    }else if(aweber_type == "mailchimp")
                    {
                        $('#mce-EMAIL').val($('#sub-form-email').val());
                        $('#mc-embedded-subscribe-form').attr('target', 'mccode');
                        $('#mc-embedded-subscribe-form').submit();
                        window.setTimeout(function() {
                             window.location.href = "?i="+response;
                        }, 2000); 
                    }else if(aweber_type == "other")
                    {
                        var myemail = $('#sub-form-email').val();
                        //alert($('#myf').val());
                        var typeemail = $('#form_holder').find('form').find('input[type="email"]');
                        var nameemail = $('#form_holder').find('form').find('input[name="email"]');
                        var nameEmail = $('#form_holder').find('form').find('input[name="Email"]');
                        var daemail = $('#form_holder').find('form').find('input[name="da_email"]');
                        if (typeemail.length > 0) {
                            typeemail.val(myemail);
                        }
                        if (nameemail.length > 0) {
                            nameemail.val(myemail);
                        }
                        if (nameEmail.length > 0) {
                            nameEmail.val(myemail);
                        }
                        if (daemail.length > 0) {
                            daemail.val(myemail);
                        }
                        if (show_name != 0 ) { 
                            $('#form_holder').find('form').find('input[name=name]').val($('#myname').val());
                            $('#form_holder').find('form').find('input[name=fname]').val($('#myname').val());
                            $('#form_holder').find('form').find('input[name=FullName]').val($('#myname').val());
                            $('#form_holder').find('form').find('input[name=da_fname]').val($('#myname').val());
                            $('#form_holder').find('form').find('input[name=da_lname]').val($('#myname').val());
                        }

                        var target_type = $('#form_holder').find('form').find('input[name="redirect"]');
                        var target_type1 = $('#form_holder').find('form').find('input[name="meta_redirect_onlist"]');
                        
                        if (target_type.length > 0) {
                            target_type.val(main_url);
                            window.setTimeout(function() {
                                $('#form_holder form').submit();
                            }, 100);
                        }else if(target_type1.length > 0) {
                            target_type1.val(main_url);
                            window.setTimeout(function() {
                                $('#form_holder form').submit();
                            }, 100);
                        }else
                        {
                            $('#form_holder form').attr('target', 'otcode');
                            $('#form_holder form').submit();    
                            window.setTimeout(function() {
                                 window.location.href = "?i="+response;
                            }, 2000);
                        }
                        
                    }else{
                        window.location.href = "?i="+response;
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
function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function shake() {
	$(".back").css("transition","none");
	$(".back").animate({
	  left: "+=20px"
	}, 150, function() {
	  // Animation complete.
	});
	$(".back").animate({
	  left: "-=40px"
	}, 150, function() {
	  // Animation complete.
	});
	$(".back").animate({
	  left: "+=40px"
	}, 150, function() {
	  // Animation complete.
	});
	$(".back").animate({
	  left: "-=40px"
	}, 150, function() {
	  // Animation complete.
	});
	$(".back").animate({
	  left: "+=20px"
	}, 150, function() {
	  // Animation complete.
	});
	
}
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

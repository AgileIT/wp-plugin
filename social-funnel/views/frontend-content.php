<?php 
if(isset($_GET['i'])){
	
	global $wpdb;
	$uri = end(explode("/",$_SERVER[REQUEST_URI]));
	$uri = explode("?",$uri);
	$social = $wpdb->get_row("select * from `".$wpdb->prefix."campaigns` where `campaign_slug` = '".$uri[0]."'");
	$unlock = $social->unlock_after;
	$social_user = $wpdb->prefix."optins";
	$social_clicks = $wpdb->prefix."clicks";
	$host = $wpdb->get_results(" SELECT id FROM $social_user WHERE unique_id = '".$_GET['i']."' ");
	$host_add = $host[0]->id;
	$total = $wpdb->get_results(" SELECT count(reference) as clicks FROM $social_user WHERE reference = '$host_add' ");
	$remaining = $unlock - $total[0]->clicks; 
	$subunique = $wpdb->get_var("select `optin_unique` from $social_user where `unique_id` = '".$_GET['i']."'"); 
	$subunique = get_site_url().'/'.$social->campaign_slug.'?t='.$subunique;
	$content = '
	<!DOCTYPE html>
<html lang="en">
 	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	    
	    <title>'.stripcslashes($social->page_title).'</title>
	    <!-- Bootstrap -->
	    <link href="'.SF_URL.'assets/css/bootstrap.css" rel="stylesheet"/>
	    <link href="'.SF_URL.'assets/css/custom.css" rel="stylesheet"/>
	    <link href="'.SF_URL.'assets/css/responsive.css" rel="stylesheet"/>
	    <link href="'.SF_URL.'assets/css/font-awesome.min.css" rel="stylesheet"/>
	    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	    <!-- WARNING: Respond.js doesn\'t work if you view the page via file: -->
	    <!--[if lt IE 9]>
	      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->
  	</head>
 	<body>
	  	<!--main container start-->
	  	<div class="main_container">
	       	<div class="container">
	       		<div class="row">
	            	<header class="text-center top_heading">
	                    <h1>'.stripcslashes($social->content_headline).'</h1>
	                </header>';
	                $content .= ($social->enable_video ==1) ? '
	                <div class="banner_wrapper">
	                	'.str_replace("&quot;",'"',stripcslashes($social->video_embed_code)).'
	                </div>':'';
	                $content .= '
	                <h3 class="in_title">'.stripcslashes($social->content_subheadline).'</h3>
	                <div class="clearfix"></div>';
	                $ftitle = json_decode($social->feature_title);
	                if(!empty($ftitle)){
	                	//$ftitle = json_decode($social->feature_title);
	                	$ftext = json_decode($social->feature_text);
	                	$fimage = json_decode($social->feature_image);
	                	$fialign = json_decode($social->feature_image_align); 
	                	$ftotal = count($ftitle);
	                	for($i=0; $i < count($fimage) ; $i++){
	                		$align = ($fialign[$i] == "right") ? 'img_float_right' : 'img_float_left';
	                		$content .= '<div class="cont_section1">
			                	<div class="cont_sect">
			                    <img class="img-responsive '.$align.'" src="'.stripcslashes($fimage[$i]).'"/>
			                	<h2>'.stripcslashes($ftitle[$i]).'</h2>
			                    <p>'.stripcslashes($ftext[$i]).'</p>
			                    </div>
			                    <div class="clearfix"></div>
			                </div>';
	                	} 
	                }
	                //echo $remaining; die();
	                if($remaining <= 0){
	                	$content .= '<a href="'.$social->gift_url.'" class="download_yellow_btn">'.stripcslashes($social->button_text).'</a>';
	                }else{
	                	$content .= '<a href="#unlockdiv" class="download_y_btn"><span><img src="http://developerup.com/wp-content/uploads/2015/05/lock_icon11.png" /></span>'.stripcslashes($social->button_text).'</a>';
	                }
	                if($remaining <= 0){
	                	$content .= " ";
	                }else{
	                $content .= '
	                <div class="fivemore_link">
	                	<i>To unlock the download link you need to refer <a href="javascript:void(0)">'.$remaining.' more people</a></i>
	                </div>
	                <div class="clearfix"><br/></div>
	                <div id="unlockdiv">
	                	<button class="unlock_data_btn"><span class="download_icon1"><img src="'.SF_URL.'assets/images/download.png"/></span>how to unlock the download button</button>
		                <div class="share_link_5step">
		                	<ul>
		                    <li><strong>Step 1</strong> - in order to unlock the download you’ll need to refer '. $remaining .' people. Simply share this URL with those who would enjoy the page you came from! Send them an email, a private Facebook message, make a Facebook post on your timeline, post it on your twitter, or just text this URL:</li>
		                    <li class="text-center"><a href="'.$subunique.'" target="_blank">'.$subunique.'</a></li>
		                    <li class="text-center"><h3>you need '.$remaining.' more people to unlock the download</h3></li>
		                    <li><strong>Step 2</strong> - after you send '.$remaining.' referrals through the URL above, come back to this page (you can bookmark or save the URL) and access the download! Please note, it will not work if you will submit '. $remaining .' emails yourself!</li>
		                    </ul>
		                </div>
		            </div>
	                <footer class="footer_sect">
	                	<h3 class="share_unlock">Share and unlock the download:</h3>
	                    <div class="social_share_box">
	                    	<ul>
	                        <li><a href="http://www.facebook.com/sharer.php?u='.$subunique.'" class="facebook_icon" target="_blank"><span class="fa fa-facebook"></span></a></li>
	                        <li><a href="http://twitter.com/home?status='.$subunique.'" target="_blank" class="twitter_icon"><span class="fa fa-twitter"></span></a></li>
	                        <li><a href="https://plus.google.com/share?url='.$subunique.'" target="_blank" class="google_plus_icon"><span class="fa fa-google-plus"></span></a></li>
	                        <li><a href="https://www.linkedin.com/cws/share?url='.$subunique.'" class="linkedin_icon" target="_blank"><span class="fa fa-linkedin"></span></a></li>
	                        <li><a href="https://www.pinterest.com/pin/create/button/?url='.$subunique.'&description=Next stop Pinterest&media=http://developerup.com/wp-content/uploads/2015/05/staff4-220x180.jpg&title=Hello" 
	                        data-pin-do="buttonPin" data-pin-config="above" 
	                        	
	                            class="pinterest_icon" target="_blank"><span class="fa fa-pinterest"></span></a></li>
	                        <li><a href="http://www.stumbleupon.com/submit?url='.$subunique.'" class="stumbleupon_icon"><span class="fa fa-stumbleupon"></span></a></li>
	                        </ul>
	                    </div>' ;
	                }
	                if($social->support_email){
	                	$content .= '<a class="support_btn" href="mailto:'.stripcslashes($social->support_email).'"><span><img src="'.SF_URL.'assets/images/icon2.png"/></span>Need help? - '.stripcslashes($social->support_email).'</a>';
	                }
	                $content .= ' </footer>
	            </div>
	       	</div>
	  	</div>
	  	<!--main container end-->
		<!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content popup_wrapper">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">where to send it?</h4>
					</div>
					<div class="modal-body">
						<h5>In order to access the video we need your name and email:</h5>
						<form>
							<div class="form-group">
							<input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter your name here">
							</div>
							<div class="form-group">
							<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Enter your email here">
							</div>
							<button type="submit" class="videos_btn">click here to access video</button>
							<p><i>(it’s free!)</i></p>
						</form>
					</div>
				</div>
			</div>
		</div>	
	    <!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
	    <script src="'.SF_URL.'assets/js/jquery.min.js" type="text/javascript"></script>
	    <script src="'.SF_URL.'assets/js/modernizr.custom.28101.js" type="text/javascript"></script>
	    <script src="'.SF_URL.'assets/js/bootstrap.min.js" type="text/javascript"></script>
	    <script src="'.SF_URL.'assets/js/jquery.nicescroll.min.js" type="text/javascript"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
        <script>
    $(document).ready(function(){
	   $( ".download_y_btn" ).click(function() {
	     $( "#unlockdiv" ).addClass( "highlight_wrap_box", 1000, callback );
	   });
	   function callback() {
    	 setTimeout(function() {
   		 $( "#unlockdiv" ).removeClass( "highlight_wrap_box" );
    	 }, 1000 );
   		}
	});		
       </script>
	   
	   
	   
		
  	</body>
</html>';
echo $content; 
}
?>
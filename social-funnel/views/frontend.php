<?php 

error_reporting(0);
function sf_disable_redirect($location){
    global $wpdb;
    require_once('metadata.class.php');
    $disable_redirect = get_query_var('name');
    $social_user = $wpdb->prefix."optins";
    $social_clicks = $wpdb->prefix."clicks";

    if(!empty($disable_redirect)) {

        $args=array(
          'name' => $disable_redirect,
          'post_type' => 'socialfunnel'
        );
        $my_posts = get_posts($args);

        $my_ip = $_SERVER["REMOTE_ADDR"];
        $url = site_url()."/".$disable_redirect;
        $user_id = "";
        $unique_id = "";
        $check_host = $wpdb->get_results(" SELECT * FROM  $social_user WHERE host = '$my_ip' AND url = '$url' ");
        if(empty($check_host))
        {
            if(!isset($_GET['i']) AND !isset($_GET['t']))
            {
                $query = $wpdb->query(" INSERT INTO $social_user (host,url) VALUES ('$my_ip', '$url') ");
                $user_id = $wpdb->insert_id;
                $get_clicks = $wpdb->get_results(" SELECT clicks FROM $social_clicks WHERE slug = '$disable_redirect' ");
                $new_clicks = (int)$get_clicks[0]->clicks + 1;
                $wpdb->update($social_clicks, array('clicks' => $new_clicks), array('slug' => $disable_redirect));
            }
           else if(isset($_GET['t']))
            {
                $tag = $_GET['t'];
                $search_tag = $wpdb->get_results(" SELECT id FROM $social_user WHERE optin_unique = '$tag' ");
                $ref_id = $search_tag[0]->id;
                
                $query = $wpdb->query(" INSERT INTO $social_user (host,url,reference) VALUES ('$my_ip', '$url','$ref_id') ");
                $user_id = $wpdb->insert_id;
                $get_clicks = $wpdb->get_results(" SELECT socail_clicks FROM $social_clicks WHERE slug = '$disable_redirect' ");
                $new_clicks = (int)$get_clicks[0]->socail_clicks + 1;
                $wpdb->update($social_clicks, array('socail_clicks' => $new_clicks), array('slug' => $disable_redirect));
            }
        }else{
            $unique_id = $check_host[0]->unique_id;

        }
        if(isset($_GET['i'])){
            require('frontend-content.php');
            die; 
        }

       
        function get_title($url){
            $url = stripcslashes($url);
              $str = file_get_contents($url);
              if(strlen($str)>0){
                $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
                preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
                return $title[1];
              }
            }
        if( $my_posts ) { ?>
        <?php
            header('Content-Type: text/html; charset=utf-8');
            status_header(200);
            $social = $wpdb->get_row("select * from `".$wpdb->prefix."campaigns` where `campaign_slug` = '".$disable_redirect."'");
//echo "<pre>"; print_r($social); die;
            $popup_details = json_decode($social->popup_details);
            $active_ar = $social->autoresponder;
            
            if($active_ar == "betaGetresponse")
            {
                $form_code = $social->autoresponder_code;
                $form_code = esc_attr($form_code);
                $grregex = "~webforms_id=(\d+)~";
                $uregex = "~u=([a-zA-Z\d]+)~";
                $matches = array();
                preg_match($grregex, $form_code, $matches);
                $grcode = $matches[1]; 
                $matches_u= array();
                preg_match($uregex, $form_code, $matches_u);
                $akash = $matches_u[1]; 
            }else if($active_ar == "getresponse"){
                $form_code = $social->autoresponder_code;
                $form_code = esc_attr($form_code);
                $grregex = "~wid=(\d+)~";
                $uregex = "~u=([a-zA-Z\d]+)~";
                $matches = array();
                preg_match($grregex, $form_code, $matches);
                $grcode = $matches[1]; 
                $matches_u= array();
                preg_match($uregex, $form_code, $matches_u);
                $akash = $matches_u[1]; 
            }else if($active_ar == "aweber")
            {
                $aweber_list = stripcslashes($social->autoresponder_code);
            }else if($active_ar == "mailchimp")
            {
                $mc_list = stripcslashes($social->autoresponder_code);
                $mc_list = html_entity_decode($mc_list);
            }else if($active_ar == "other")
            {
                $other_list = stripcslashes($social->autoresponder_code);
                $other_list = str_replace("<script", "<!-- ", str_replace("</script>", "</script -->", html_entity_decode($other_list)));
            }
            $http = "http://";
            $sourceUrl = $social->source_url;
            if(strpos($sourceUrl,"http") > -1 ){
                
            }else{
                $sourceUrl = $http.$sourceUrl;
            }
            $metaData = MetaData::fetch($sourceUrl);
            
           
            $title = get_title($sourceUrl);
            
            $content = '
            <!doctype html>
            <html>
            <head>
            <title>'.$title.'</title>
            <script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
                <link href="'.SF_URL.'/assets/css/bootstrap.min.css" rel="stylesheet"/>
                <link href="'.SF_URL.'/assets/css/sf_frontend_custom.css" rel="stylesheet"/>
                <link href="'.SF_URL.'/assets/css/sf_frontend_responsive.css" rel="stylesheet"/>
                <link href="'.SF_URL.'/assets/css/font-awesome.min.css" rel="stylesheet"/>';
                  if($metaData)
                  {
                    $tags = $metaData->tags();
                    unset($tags["msapplication-task"]);
                    foreach($tags as $key => $val)
                                    {
                                        if((strpos($key, "og") > -1) OR (strpos($key, "twitter") > -1))
                                        {
                                            $content .= '<meta property="'.$key .'" content="'.$val.'" />';    
                                        }else{
                                            $content .= '<meta name="'.$key .'" content="'.$val.'" />';    
                                        }
                                        
                                        $content .= "\n";
                                    }
                   }
           
                $content .= '<meta property="og:image:width" content="1200" />
                            <meta property="og:image:height" content="630" />';


               
            $content .= stripcslashes($social->fb_retarget_pixel).'</head>
            <body style="overflow:hidden;">
                <iframe src="'.stripcslashes($sourceUrl).'" width="100%" allowfullscreen name="disable-x-frame-options" frameBorder="0" style="min-height:700px;"></iframe> ';
            $content .= '<input type="hidden" id="aweber_type" value="'.$active_ar.'">
                         <input type="hidden" id="target_url" value="'.$url.'">
                         <input type="hidden" id="show_name" value="'.$social->include_field.'">';
            if($social->popup_style==1){
                $content .= '<div class="modal fade" id="popup_style1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content" style="box-shadow:none; background:none; border:none;">
                            <div class="modal-body popup_wrapper_1 card" style="width:600px; height:auto;">
                                <a href="#" class="close_btn" data-dismiss="modal" aria-label="Close"></a> 
                                <!-- step 1 start -->
                                <div class="front" style="background:#d8e6f0; border:7px solid #fff; padding:15px; height:auto;">
                                    <div class="row">
                                        <div class="popup_style_1_step1_wrap">
                                            <div class="col-md-5 col-sm-5">
                                                <div class="popup_st1_img"><img class="img-responsive" src="'.stripcslashes($popup_details->p1s1image).'"/></div>
                                            </div>
                                            <div class="col-md-7 col-sm-7">
                                                <h2>'.stripcslashes($popup_details->s1headline1).'</h2>
                                                    <ul>';
                                                    if($popup_details->p1s1bullet1 !=""){
                                                        $content .= '<li>'.stripcslashes(htmlspecialchars_decode($popup_details->p1s1bullet1)).'</li>'; }
                                                    if($popup_details->p1s1bullet2 !=""){
                                                        $content .='<li>'.stripcslashes(htmlspecialchars_decode($popup_details->p1s1bullet2)).'</li>'; }
                                                    if($popup_details->p1s1bullet3 !=""){
                                                       $content.= '<li>'.stripcslashes(htmlspecialchars_decode($popup_details->p1s1bullet3)).'</li>';
                                                    }
                                                    $content .= '</ul>
                                                <button class="btn popup_yello_btn" id="flip-btn">'.stripcslashes($popup_details->p1s1buttontext).'</button>';
                                                $content .= ($popup_details->p1s1sign ==1) ? '<p><i>(it’s free!)</i></p>' : '';
                                            $content .= '</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- step 1 end -->
                                <!-- step 2 start -->
                                <div class="back" style="background:#d8e6f0; padding:15px; border:7px solid #fff">
                                    <div class="row">
                                        <div class="popup_style_1_step2_wrap">
                                            <div class="col-md-12 col-sm-12 text-center">
                                                <h2>'.stripcslashes($popup_details->p1s2headline).'</h2>
                                                <p>'.stripcslashes($popup_details->p1s2subheadline).'</p>';
                                                if($active_ar == "betaGetresponse")
                                                {
                                                    $content .= '<iframe name="grcode" id="grcode" style="display:none"></iframe>
                                                                <form id="subscriptionForm" target="grcode" method="post" action="https://app.getresponse.com/add_contact_webform_v2.html?u='.$akash.'&webforms_id="'.$grcode.'">
                                                                     <input type="hidden" name="redirect" value="http://developerup.com/123" />';
                                                }else if($active_ar == "getresponse"){
                                                   $content .= '<iframe name="grcode" id="grcode" style="display:none"></iframe>
                                                                <form id="subscriptionForm" target="grcode" method="post" action="https://app.getresponse.com/add_contact_webform.html?u='.$akash.'">
                                                                     <input type="hidden" name="webform_id" value="'.$grcode.'" />
                                                                     <input type="hidden" name="redirect" value="http://developerup.com/123" />';
                                                }elseif($active_ar == 'aweber')
                                                {
                                                   $content .= '<form method="post" id="subscriptionForm" target="_top" action="http://www.aweber.com/scripts/addlead.pl" onsubmit="PreventExitSplash = true;">
                                                            <input type="hidden" name="meta_web_form_id" value="" />
                                                            <input type="hidden" name="meta_split_id" value="" />
                                                            <input type="hidden" name="listname" value="'.$aweber_list.'" />
                                                            <input type="hidden" id="weber_id" name="redirect" value="" />
                                                            <input type="hidden" id="weber_metaid" name="meta_redirect_onlist" value="" />
                                                            <input type="hidden" name="meta_message" value="1" />
                                                            <input type="hidden" name="meta_required" value="email" />
                                                            <input type="hidden" name="meta_tooltip" value="" />';
                                                }elseif($active_ar == 'mailchimp')
                                                {
                                                   $content .= '<div style="display:none" id="form_holder">
                                                                <iframe name="grcode" id="mccode" style="display:none"></iframe>';
                                                   $content .= $mc_list;
                                                   $content .= '</div>
                                                    <form id="subscriptionForm" action="#">';  
                                                }elseif($active_ar == 'other')
                                                {
                                                    $content .= '<div style="display:none" id="form_holder">
                                                                <iframe name="otcode" id="mccode" style="display:none"></iframe>';
                                                    $content .= $other_list;
                                                    $content .= '</div>
                                                        <form id="subscriptionForm" action="#">';
                                                }
                                                
                                                if($social->include_field !=0 ){
                                                    $content .= '<div class="form-group">
                                                    <input type="text" class="form-control" placeholder="Enter your name here" name="name" id="sub-form-name"/>
                                                    </div>';
                                                }
                                                if($active_ar == 'betaGetresponse'){
                                                $content .= '<div class="form-group">
                                                        <input type="email" name="webform[email]" class="form-control" placeholder="Enter your email here" id="sub-form-email"/>
                                                    </div>
                                                    <input type="hidden" value="'.$social->unique_id.'" id="unique_id" />
                                                    <button class="btn popup_yello_btn" type="submit" name="save" id="savebtn">'.stripcslashes($popup_details->p1s1buttontext).'</button>
                                                </form>';
                                            }else{
                                                $content .= '<div class="form-group">
                                                        <input type="email" name="email" class="form-control" placeholder="Enter your email here" id="sub-form-email" />
                                                    </div>
                                                    <input type="hidden" value="'.$social->unique_id.'" id="unique_id" />
                                                    <button class="btn popup_yello_btn" type="submit" name="save" id="savebtn">'.stripcslashes($popup_details->p1s1buttontext).'</button>
                                                </form>';
                                            }
                                                $content .= ($popup_details->p1s1sign ==1) ? '<p><i>(it’s free!)</i></p>' : '';
                                            $content .= '</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- step 2 end -->
                            </div>
                        </div>
                    </div>
                </div>';
            }
            if($social->popup_style==2){
                //if($social->include_field==0){
                $content .= '<div class="modal fade" id="popup_style2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content" style="box-shadow:none; background:none; border:none;">
                            <div class="modal-body popup_wrapper_2 card" style="width:600px; height:auto;">
                                <a href="#" class="close_btn" data-dismiss="modal" aria-label="Close"></a> 
                                <!-- step 1 start -->
                                <div class="front">
                                    <div class="row" style="border:7px solid #fff; background-color:#f1f1f1">
                                        <div class="popup_st2_border1"></div>
                                        <div class="popup_style_2_step1_wrap">
                                            <div class="col-md-12 col-sm-12" style="padding:20px;">
                                                <h4>'.stripcslashes($popup_details->s1headline2).'</h4>
                                                <h2>'.stripcslashes($popup_details->p2s1subheadline).'</h2>
                                                <button class="btn popup_yello_btn2" id="flip-btn">'.stripcslashes($popup_details->p2s1buttontext).'</button>';
                                                $content .= ($popup_details->p2s1sign ==1)?'<p class="its_free_text"><i>(it’s free!)</i></p>':'';
                                            $content .= '
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="popup_st2_border1"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- step 1 end -->

                                <!-- step 2 start -->

                                <div class="back">
                                    <div class="row" style="border:7px solid #fff; background-color:#f1f1f1">
                                        <div class="popup_st2_border1"></div>
                                        <div class="popup_style_2_step1_wrap">
                                            <div class="col-md-12 col-sm-12" style="padding:20px;">
                                                <h3>'.stripcslashes($popup_details->p2s2headline).'</h3>
                                                <p class="text-center">'.stripcslashes($popup_details->p2s2subheadline).'</p>';
                                                if($active_ar == "getresponse")
                                                {
                                                    $content .= '<iframe name="grcode" id="grcode" style="display:none"></iframe><form id="subscriptionForm" target="grcode" method="post" action="https://app.getresponse.com/add_contact_webform.html?u='.$akash.'">
                                                                     <input type="hidden" name="webform_id" value="'.$grcode.'" />
                                                                     <input type="hidden" name="redirect" value="http://developerup.com/123" />';
                                                }else if($active_ar == "betaGetresponse"){
                                                    $content .= '<iframe name="grcode" id="grcode" style="display:none"></iframe><form id="subscriptionForm" target="grcode" method="post" action="https://app.getresponse.com/add_contact_webform_v2.html?u='.$akash.'&webforms_id='.$grcode.'">
                                                                     <input type="hidden" name="redirect" value="http://developerup.com/123" />';
                                                }elseif($active_ar == 'aweber')
                                                {
                                                   $content .= '<form method="post" id="subscriptionForm" target="_top" action="http://www.aweber.com/scripts/addlead.pl" onsubmit="PreventExitSplash = true;">
                                                            <input type="hidden" name="meta_web_form_id" value="" />
                                                            <input type="hidden" name="meta_split_id" value="" />
                                                            <input type="hidden" name="listname" value="'.$aweber_list.'" />
                                                            <input type="hidden" id="weber_id" name="redirect" value="" />
                                                            <input type="hidden" id="weber_metaid" name="meta_redirect_onlist" value="" />
                                                            <input type="hidden" name="meta_message" value="1" />
                                                            <input type="hidden" name="meta_required" value="email" />
                                                            <input type="hidden" name="meta_tooltip" value="" />';
                                                }elseif($active_ar == 'mailchimp')
                                                {
                                                   $content .= '<div style="display:none" id="form_holder">
                                                                <iframe name="grcode" id="mccode" style="display:none"></iframe>';
                                                   $content .= $mc_list;
                                                   $content .= '</div>
                                                    <form id="subscriptionForm" action="#">';  
                                                }elseif($active_ar == 'other')
                                                {
                                                    $content .= '<div style="display:none" id="form_holder">
                                                                <iframe name="otcode" id="mccode" style="display:none"></iframe>';
                                                    $content .= $other_list;
                                                    $content .= '</div>
                                                        <form id="subscriptionForm" action="#">';
                                                }
                                                if($social->include_field !=0){

                                                $content .= 
                                                '<div class="form-group">
                                                    <input type="text" class="form-control" name="name" placeholder="Enter your name here" id="sub-form-name" />
                                                </div>';
                                                
                                              }
                                              if($active_ar == "betaGetresponse"){
                                              $content .= '<input type="hidden" name="webform_id" value="'.$grcode.'" />';
                                              $content .=
                                                '<div class="form-group">
                                                    <input type="email" class="form-control" name="webform[email]" placeholder="Enter your email here" id="sub-form-email">
                                                  </div>
                                                  <center>
                                                    <button class="btn popup_yello_btn" type="submit" name="save" id="savebtn">'.stripcslashes($popup_details->p2s2buttontext).'</button>
                                                 </center>
                                                </form>';
                                            }else{
                                                $content .=
                                                '<div class="form-group">
                                                    <input type="email" class="form-control" name="email" placeholder="Enter your email here" id="sub-form-email">
                                                  </div>
                                                  <center>
                                                    <button class="btn popup_yello_btn" type="submit" name="save" id="savebtn">'.stripcslashes($popup_details->p2s2buttontext).'</button>
                                                 </center>
                                                </form>';
                                            }
                                            $content .= ($popup_details->p2s1sign == 1) ? '<p class="its_free_text"><i>(it’s free!)</i></p>' : '';
                                            $content .= '</div>
                                            <div class="clearfix"></div>
                                            <div class="popup_st2_border1"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- step 2 end -->
                            </div>
                        </div>
                    </div>
                </div>'; 
            }
            if($social->popup_style ==3){
                $content .= '<div class="modal fade" id="popup_style3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body popup_wrapper_3 card" style="width:600px; height:auto; padding:15px;">
                                <a href="#" class="close_btn" data-dismiss="modal" aria-label="Close"></a> 
                                <!-- step 1 start -->
                                <div class="front popup_in_cont_wrap" style="background:white;">
                                    <div class="row">
                                        <div class="popup_style_3_step1_wrap">
                                            <div class="col-md-12 col-sm-12">
                                                <h4>'.stripcslashes($popup_details->s1headline3).'</h4>
                                                <h2>'.stripcslashes($popup_details->p3s1subheadline).'</h2>
                                                <div class="form-inline">

                                                    <div class="form-group">

                                                        <button class="btn popup_green_btn" id="flip-btn">'.stripcslashes($popup_details->p3s1yesbutton).'</button>';

                                                        $content .= ($popup_details->p3s1sign ==1)?'<p class="its_free_text"><i>(it’s free!)</i></p>':'';

                                                    $content.='
                                                    </div>
                                                        <div class="form-group pull-right">
                                                            <button class="btn popup_gray_btn">'.stripcslashes($popup_details->p3s1nobutton).'</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- step 1 end -->
                                <!-- step 2 start -->
                                <div class="back popup_in_cont_wrap" style="background:white;">
                                    <div class="row">
                                        <div class="popup_style_3_step2_wrap">
                                            <div class="col-md-12 col-sm-12">
                                                <h3>'.stripcslashes($popup_details->p3s2headline).'</h3>
                                                <p class="text-center">'.stripcslashes($popup_details->p3s2subheadline).'</p>';
                                                if($active_ar == "getresponse")
                                                {
                                                    $content .= '<iframe name="grcode" id="grcode" style="display:none"></iframe><form id="subscriptionForm" target="grcode" method="post" action="https://app.getresponse.com/add_contact_webform.html?u='.$akash.'">
                                                                     <input type="hidden" name="webform_id" value="'.$grcode.'" />
                                                                     <input type="hidden" name="redirect" value="http://developerup.com/123" />';
                                                }
                                                else if($active_ar == "betaGetresponse"){
                                                    $content .= '<iframe name="grcode" id="grcode" style="display:none"></iframe><form id="subscriptionForm" target="grcode" method="post" action="https://app.getresponse.com/add_contact_webform_v2.html?u='.$akash.'&webforms_id='.$grcode.'">
                                                                     <input type="hidden" name="redirect" value="http://developerup.com/123" />';
                                                }
                                                elseif($active_ar == 'aweber')
                                                {
                                                   $content .= '<form method="post" id="subscriptionForm" target="_top" action="http://www.aweber.com/scripts/addlead.pl" onsubmit="PreventExitSplash = true;">
                                                            <input type="hidden" name="meta_web_form_id" value="" />
                                                            <input type="hidden" name="meta_split_id" value="" />
                                                            <input type="hidden" name="listname" value="'.$aweber_list.'" />
                                                            <input type="hidden" id="weber_id" name="redirect" value="" />
                                                            <input type="hidden" id="weber_metaid" name="meta_redirect_onlist" value="" />
                                                            <input type="hidden" name="meta_message" value="1" />
                                                            <input type="hidden" name="meta_required" value="email" />
                                                            <input type="hidden" name="meta_tooltip" value="" />';
                                                }elseif($active_ar == 'mailchimp')
                                                {
                                                   $content .= '<div style="display:none" id="form_holder">
                                                                <iframe name="grcode" id="mccode" style="display:none"></iframe>';
                                                   $content .= $mc_list;
                                                   $content .= '</div>
                                                    <form id="subscriptionForm" action="#">';  
                                                }elseif($active_ar == 'other')
                                                {
                                                    $content .= '<div style="display:none" id="form_holder">
                                                                <iframe name="otcode" id="mccode" style="display:none"></iframe>';
                                                    $content .= $other_list;
                                                    $content .= '</div>
                                                        <form id="subscriptionForm" action="#">';
                                                }
                                                if($social->include_field !=0 ){
                                                    $content .=
                                                    '<div class="form-group">
                                                        <input type="text" class="form-control" name="name" placeholder="Enter your name here" id="sub-form-name"/>
                                                    </div>';
                                                }
                                                if($active_ar == "betaGetresponse"){
                                                 $content .=
                                                  '<div class="form-group">
                                                    <input type="email" class="form-control" name="webform[email]" placeholder="Enter your email here"  id="sub-form-email">
                                                  </div>
                                                 <center><button class="btn popup_yello_btn" type="submit" name="save" id="savebtn">'.stripcslashes($popup_details->p3s2buttontext).'</button></center>
                                                </form>';
                                            }else{
                                                  $content .=
                                                  '<div class="form-group">
                                                    <input type="email" class="form-control" name="email" placeholder="Enter your email here"  id="sub-form-email">
                                                  </div>
                                                 <center><button class="btn popup_yello_btn" type="submit" name="save" id="savebtn">'.stripcslashes($popup_details->p3s2buttontext).'</button></center>
                                                </form>';
                                            }
                                                $content .= ($popup_details->p3s1sign == 1) ? '<p class="its_free_text"><i>(it’s free!)</i></p>':'';
                                                $content .= '</div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- step 2 end -->
                            </div>
                        </div>
                    </div>
                </div>';     
        }
          if($social->popup_style ==4 ){
            
                $content .= '<div class="modal fade" id="popup_style4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content" style="background:none;">
                          <div class="modal-body popup_wrapper_4 card" style="height:auto; width:600px;">
                                <!-- step 1 start -->

                                <div class="front" style="background:#e05b49;">
                                    <div class="modal-header">
                                        <a href="#" class="close_btn" data-dismiss="modal" aria-label="Close"></a> 
                                        <h4 class="modal-title">'.stripcslashes($popup_details->s1headline4).'</h4>
                                    </div>
                                    <div class="modal-body">
                                        <p>'.stripcslashes($popup_details->p4s1subheadline).'</p>
                                        <div class="popup_video_sect">
                                            <img class="img-responsive" src="'.stripcslashes($popup_details->p4s1image).'" />
                                        </div>
                                        <footer>
                                            <button class="btn yallo_popup_sty4_btn" id="flip-btn">'.stripcslashes($popup_details->p4s1buttontext).'</button>';
                                            $content .= ($popup_details->p4s1sign == 1) ? '<p class="its_free_text"><i>(it’s free!)</i></p>' : '';
                                        $content .= '</footer>
                                    </div>
                                </div>
                                <!-- step 1 end -->
                                <!-- step 2 start -->
                                <div class="back" style="background:#e05b49;">
                                    <div class="modal-header">
                                        <a href="#" class="close_btn" data-dismiss="modal" aria-label="Close"></a> 
                                        <h4 class="modal-title">'.stripcslashes($popup_details->p4s2headline).'</h4>
                                    </div>
                                    <div class="modal-body">
                                        <p>'.stripcslashes($popup_details->p4s2subheadline).'</p>
                                        <div class="popup4_form">';
                                           if($active_ar == "getresponse")
                                                {
                                                    $content .= '<iframe name="grcode" id="grcode" style="display:none"></iframe><form id="subscriptionForm" target="grcode" method="post" action="https://app.getresponse.com/add_contact_webform.html?u='.$akash.'">
                                                                     <input type="hidden" name="webform_id" value="'.$grcode.'" />
                                                                     <input type="hidden" name="redirect" value="http://developerup.com/123" />';
                                                }else if($active_ar == "betaGetresponse"){
                                                    $content .= '<iframe name="grcode" id="grcode" style="display:none"></iframe><form id="subscriptionForm" target="grcode" method="post" action="https://app.getresponse.com/add_contact_webform_v2.html?u='.$akash.'&webforms_id='.$grcode.'">
                                                                     <input type="hidden" name="redirect" value="http://developerup.com/123" />';
                                                }
                                                elseif($active_ar == 'aweber')
                                                {
                                                   $content .= '<form method="post" id="subscriptionForm" target="_top" action="http://www.aweber.com/scripts/addlead.pl" onsubmit="PreventExitSplash = true;">
                                                            <input type="hidden" name="meta_web_form_id" value="" />
                                                            <input type="hidden" name="meta_split_id" value="" />
                                                            <input type="hidden" name="listname" value="'.$aweber_list.'" />
                                                            <input type="hidden" id="weber_id" name="redirect" value="" />
                                                            <input type="hidden" id="weber_metaid" name="meta_redirect_onlist" value="" />
                                                            <input type="hidden" name="meta_message" value="1" />
                                                            <input type="hidden" name="meta_required" value="email" />
                                                            <input type="hidden" name="meta_tooltip" value="" />';
                                                }elseif($active_ar == 'mailchimp')
                                                {
                                                   $content .= '<div style="display:none" id="form_holder">
                                                                <iframe name="grcode" id="mccode" style="display:none"></iframe>';
                                                   $content .= $mc_list;
                                                   $content .= '</div>
                                                    <form id="subscriptionForm" action="#">';  
                                                }elseif($active_ar == 'other')
                                                {
                                                    $content .= '<div style="display:none" id="form_holder">
                                                                <iframe name="otcode" id="mccode" style="display:none"></iframe>';
                                                    $content .= $other_list;
                                                    $content .= '</div>
                                                        <form id="subscriptionForm" action="#">';
                                                }
                                            if($social->include_field != 0){
                                                $content .= '<input type="text" name="name" class="form-control" placeholder="Enter your name here">';
                                            }
                                            if($active_ar == "betaGetresponse"){
                                            $content .=
                                                '<input type="email" name="webform[email]" class="form-control" placeholder="Enter your email here"  id="sub-form-email">       
                                            </form>   
                                        </div>                                        
                                        <footer>';
                                    }else{
                                        $content .=
                                                '<input type="email" name="email" class="form-control" placeholder="Enter your email here"  id="sub-form-email">       
                                            </form>   
                                        </div>                                        
                                        <footer>';
                                    }
                                            $p4s1btntxt = stripcslashes($popup_details->p4s1buttontext);
                                            $content .= '<button class="btn popup_yello_btn" type="submit" name="save" id="savebtn">'.strtoupper($p4s1btntxt).'</button>';
                                            $content .= ($popup_details->p4s1sign) ? '<p class="its_free_text"><i>(it’s free!)</i></p>':'';
                                        $content .= '</footer>
                                    </div>
                                </div> 
                                <!-- step 2 end -->
                          </div>
                        </div>
                      </div>
                    </div>';
            }
            $content .= '
    
                <script src="'.SF_URL.'assets/js/jquery.min.js" type="text/javascript"></script>

                <script src=" '.SF_URL . 'assets/js/jquery-migrate-1.0.0.js'.'" type="text/javascript"></script>
<script>var time = '. ($social->delay * 1000) .', style='.$social->popup_style.', ajaxurl = "'.admin_url("admin-ajax.php").'";</script>
                <script src="'.SF_URL.'assets/js/bootstrap.min.js" type="text/javascript"></script>
                <script src=" '.SF_URL . 'assets/js/flip.js'.'" type="text/javascript"></script>
                <script src=" '.SF_URL . 'assets/js/sf_frontend.js'.'" type="text/javascript"></script>


<script type="text/javascript">
                jQuery(document).ready(function($){
                    var popup = '.$social->popup_style.';
                    $("iframe").height($(document).height());
                    var status = '.$social->closable.';
                    if(status==0){
                       //$("#popup_style"+popup).modal({backdrop: "static"});
                       $(".close_btn").css("display","none");

                    }
                });
                </script> 

                ';  
                if($social->closable==1){
                
                $content .='<script>

                
                jQuery(document).ready(function($){
                    
                    $(".popup_style_3_step1_wrap .popup_gray_btn").click(function(){

                        $("body").removeClass("modal-open");
                        $(this).parents("#popup_style3").removeClass("in");
                        $(".modal-backdrop").removeClass("in")
                    });

                });

                </script>

                
                <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
                <script>
                    $(document).ready(function(){
                       $( "#flip-btn" ).click(function() {
                         $( ".close_btn" ).addClass( "hide_close_btn", 1, callback );
                       });
                       function callback() {
                         setTimeout(function() {
                         $( ".close_btn" ).removeClass( "hide_close_btn" );
                         }, 400 );
                        }
                    });     
                </script>
<script type="text/javascript">
                      $(document).ready(function() {
                     // If the browser type if Mozilla Firefox
                     if ($.browser.mozilla && $.browser.version >= "1.8" )
                     {$(".close_btn").addClass("close_btn_mozilla");}
                     // If the browser type is Opera
                     if( $.browser.opera)
                     {$(".close_btn").addClass("close_btn_opera");}
                     // If the web browser type is Safari
                     if( $.browser.safari )
                     {$(".close_btn").addClass("close_btn_safari");}
                     // If the web browser type is Chrome
                     if( $.browser.chrome)
                     {$(".close_btn").addClass("close_btn_chrome");}
                     // If the web browser type is Internet Explorer
                     if ($.browser.msie && $.browser.version <= 6 )
                     {$(".close_btn").addClass("close_btn_ielat");}
                     //If the web browser type is Internet Explorer 6 and above
                     if ($.browser.msie && $.browser.version > 6)
                     {$(".close_btn").addClass("close_btn_ie_old");}
                     
                 });
                 
                 $(window).resize(function(){
                    $("iframe").css("height", $(this).height());
                 });
                  </script>
                
                ';      
                 
            }                     
            if(isset($_GET['t'])){
                $content .= '<span id="is_optin" data-optin="t" data-userid="'.$user_id.'" data-slug="'.$disable_redirect.'" data-unique='.$unique_id.'></span>';
            }else if(isset($_GET['i'])){
                $content .= '<span id="is_optin" data-optin="i" data-userid="" data-slug="'.$disable_redirect.'" data-unique=""></span>';
            }else{
                $content .= '<span id="is_optin" data-optin="n" data-userid="'.$user_id.'" data-slug="'.$disable_redirect.'" data-unique='.$unique_id.' ></span>';
            }
            $content .= '</body></html>';
            echo $content; 
            exit;
        }
    }
    return $location; 
} ?>
<?php 
global $wpdb;
$wpdb->show_errors();
if(isset($_POST['save-campaign'])):
    $include_field = (isset($_POST['include_field'])) ? '1':'0';
    $enable_video = (isset($_POST['enable_video'])) ? '1':'0';
    $popupdetails = '';
    $totalfeature =  count(array_filter($_POST['feature_title']));
    $featuretitle = json_encode(array_filter($_POST['feature_title']));
    $featuretext = json_encode(array_filter($_POST['feature_text']));
   // echo $featuretitle; echo $featuretext; die();
    $featureimage = json_encode(array_filter($_POST['giftfeatureimage']));
    $featureimagealign = json_encode(array_filter($_POST['feature_image_align']));
    
    foreach($_POST as $key => $val) {
	    $_POST[$key] = str_replace("\"", "&quot;", $val );
    }
    if(isset($_POST['popstyle'])){
        $data = array(); 
        switch ($_POST['popstyle']) {
            case '1':
                $data['s1headline1'] = $_POST['s1headline1'];
                $data['p1s1bullet1'] = htmlspecialchars($_POST['p1s1bullet1']);
                $data['p1s1bullet2'] = htmlspecialchars($_POST['p1s1bullet2']);
                $data['p1s1bullet3'] = htmlspecialchars($_POST['p1s1bullet3']);
                $data['p1s1image'] = $_POST['p1s1image'];
                $data['p1s1buttontext'] = $_POST['p1s1buttontext'];
                $data['p1s1sign'] = (isset($_POST['p1s1sign'])) ? '1':'0';
                $data['p1s2headline'] = $_POST['p1s2headline'];
                $data['p1s2subheadline'] = $_POST['p1s2subheadline'];
            break;
            
            case '2':
                $data['s1headline2'] = $_POST['s1headline2'];
                $data['p2s1subheadline'] = $_POST['p2s1subheadline'];
                $data['p2s1buttontext'] = $_POST['p2s1buttontext'];
                $data['p2s1sign'] = (isset($_POST['p2s1sign'])) ? '1' : '0';
                $data['p2s2headline'] = $_POST['p2s2headline'];
                $data['p2s2subheadline'] = $_POST['p2s2subheadline'];
                $data['p2s2buttontext'] = $_POST['p2s2buttontext'];
            break; 
            case '3':
                $data['s1headline3'] = $_POST['s1headline3'];
                $data['p3s1subheadline'] = $_POST['p3s1subheadline'];
                $data['p3s1yesbutton'] = $_POST['p3s1yesbutton'];
                $data['p3s1nobutton'] = $_POST['p3s1nobutton'];
                $data['p3s1sign'] = (isset($_POST['p3s1sign'])) ? '1' : '0';
                $data['p3s2headline'] = $_POST['p3s2headline'];
                $data['p3s2subheadline'] = $_POST['p3s2subheadline'];
                $data['p3s2buttontext'] = $_POST['p3s2buttontext'];
            break; 
            case '4':
                $data['s1headline4'] = $_POST['s1headline4'];
                $data['p4s1subheadline'] = $_POST['p4s1subheadline'];
                $data['p4s1image'] = $_POST['p4s1image'];
                $data['p4s1buttontext'] = $_POST['p4s1buttontext'];
                $data['p4s1sign'] = (isset($_POST['p4s1sign'])) ? '1': '0';
                $data['p4s2headline'] = $_POST['p4s2headline'];
                $data['p4s2subheadline'] = $_POST['p4s2subheadline'];
            break; 
        }
        $popupdetails = json_encode($data); 
    }
    if(isset($_GET['edit-id']) && !empty($_GET['edit-id'])){
        $postid = $wpdb->get_var("select `post_id` from `".$wpdb->prefix."campaigns` where `id` = ".$_GET['edit-id']);
        $post_info = array(
            'post_status' => 'publish', 
            'post_type' => 'socialfunnel',
            'post_name' => $_POST['campaign_slug'],
            'post_title' => $_POST['campaign_name'],
            'ID' => $postid,
        );
        wp_update_post($post_info);
        $slug = get_post($postid);
        $slug = $slug->post_name;
        $camp_table = $wpdb->prefix."campaigns";
		
		if(strpos($_POST['autoresponder_code'], "<style") !== FALSE) {
			$_POST['autoresponder_code'] = preg_replace("/<style\\b[^>]*>(.*?)<\\/style>/s", "", $_POST['autoresponder_code']);
		}
		
		if(strpos($_POST['autoresponder_code'], "<link") !== FALSE) {
			$_POST['autoresponder_code'] = preg_replace("/<link\\b[^>]*(.*?)\\/>/s", "", $_POST['autoresponder_code']);
			
		}

        $update = array(
                    "campaign_name" => $_POST['campaign_name'],
                    "campaign_slug" => $slug,
                    "autoresponder" => $_POST['autoresponder'],
                    "autoresponder_code" => $_POST['autoresponder_code'],
                    "include_field" => $include_field,
                    "fb_retarget_pixel" => $_POST['fb_retarget_pixel'],
                    "source_url" => $_POST['source_url'],
                    "popup_style" => $_POST['popstyle'],
                    "delay" => $_POST['delay'],
                    "closable" => $_POST['closable'],
                    "popup_details" => $popupdetails,
                    "content_headline" => $_POST['content_headline'],
                    "enable_video" => $enable_video,
                    "video_embed_code" => $_POST['video_embed_code'],
                    "content_subheadline" => $_POST['content_subheadline'],
                    "support_email" => $_POST['support_email'],
                    "gift_url" => $_POST['gift_url'],
                    "unlock_after" => $_POST['unlock_after'],
                    "button_text" => $_POST['button_text'],
                    "page_title" => $_POST['page_title'],
                    "feature_title" => $featuretitle,
                    "feature_text" => $featuretext,
                    "feature_image" => $featureimage,
                    "feature_image_align" => $featureimagealign

            );
        $where = array(
                    "id" => $_GET['edit-id']
            );
        $query_update = $wpdb->update($camp_table,$update,$where);
        
        if($query_update){ 
            wp_redirect("?page=social-funnel&sfaction=update");
        }else{ 
            wp_redirect("?page=social-funnel&sfaction=updatee");   
        }

    }else{
        $unique = randomPassword();
        $post_info = array(
            'post_status' => 'publish', 
            'post_type' => 'socialfunnel',
            'post_name' => $_POST['campaign_slug'],
            'post_title' => $_POST['campaign_name']
        );
        $new_postid = wp_insert_post($post_info);
        $slug = get_post($new_postid); 
        $slug = $slug->post_name;
        $camp_table = $wpdb->prefix."campaigns";
        
        if(strpos($_POST['autoresponder_code'], "<style") !== FALSE) {
			$_POST['autoresponder_code'] = preg_replace("/<style\\b[^>]*>(.*?)<\\/style>/s", "", $_POST['autoresponder_code']);
		}
		
		if(strpos($_POST['autoresponder_code'], "<link") !== FALSE) {
			$_POST['autoresponder_code'] = preg_replace("/<link\\b[^>]*(.*?)\\/>/s", "", $_POST['autoresponder_code']);
			
		}
        
        $insert = array(
                    "unique_id" => $unique,
                    "post_id" => $new_postid,
                    "campaign_name" => $_POST['campaign_name'],
                    "campaign_slug" => $slug,
                    "autoresponder" => $_POST['autoresponder'],
                    "autoresponder_code" => $_POST['autoresponder_code'],
                    "include_field" =>  $include_field,
                    "fb_retarget_pixel" => $_POST['fb_retarget_pixel'],
                    "source_url" => $_POST['source_url'],
                    "popup_style" => $_POST['popstyle'],
                    "delay" => $_POST['delay'],
                    "closable" =>  $_POST['closable'],
                    "popup_details" => $popupdetails,
                    "content_headline" => $_POST['content_headline'],
                    "enable_video" => $enable_video,
                    "video_embed_code" => $_POST['video_embed_code'],
                    "content_subheadline" => $_POST['content_subheadline'],
                    "support_email" => $_POST['support_email'],
                    "gift_url" => $_POST['gift_url'],
                    "unlock_after" => $_POST['unlock_after'],
                    "button_text" => $_POST['button_text'],
                    "page_title" => $_POST['page_title'],
                    "feature_title" => $featuretitle,
                    "feature_text" => $featuretext,
                    "feature_image" => $featureimage,
                    "feature_image_align" => $featureimagealign
            );

    $query_insert = $wpdb->insert($camp_table,$insert);
    if($query_insert){
            $camp_id = $wpdb->insert_id;
            $social_clicks = $wpdb->prefix."clicks";
            $wpdb->query(" INSERT INTO $social_clicks (camp_id,slug) VALUES ($camp_id, '$slug') ");
            wp_redirect("?page=social-funnel&sfaction=save");   
        }else{ 
           wp_redirect("?page=social-funnel&sfaction=updatee");   
        }    
    }
   
endif; 

if(isset($_GET['edit-id'])){
    $campaign = $wpdb->get_row("select * from `".$wpdb->prefix."campaigns` where `id`= ".$_GET['edit-id']);
}
?>



 <!-- popup modal 1   start -->
  
    <div class="modal fade modal_setting" id="popup_style1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <button aria-label="Close" data-dismiss="modal" class="close" type="button"></button>
            <div class="modal_img">
                <img class="img-responsive" src="<?php echo SF_URL; ?>assets/images/popup_st1.png"/> 
            </div>
        </div>
    </div>
  
  <!-- popup modal 1  end -->
  
  
  <!-- popup modal 2   start -->
  
    <div class="modal fade modal_setting" id="popup_style2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <button aria-label="Close" data-dismiss="modal" class="close" type="button"><</button>
            <div class="modal_img">
                <img class="img-responsive" src="<?php echo SF_URL; ?>assets/images/popup_st2.png"/> 
            </div>
        </div>
    </div>
  
  <!-- popup modal 2  end -->
  
  
  <!-- popup modal 3   start -->
  
    <div class="modal fade modal_setting" id="popup_style3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <button aria-label="Close" data-dismiss="modal" class="close" type="button"></button>
            <div class="modal_img">
                <img class="img-responsive" src="<?php echo SF_URL; ?>assets/images/popup_st3.png"/> 
            </div>
        </div>
    </div>
  
  <!-- popup modal 3  end -->
  
  
<!-- popup modal 4   start -->

<div class="modal fade modal_setting" id="popup_style4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <button aria-label="Close" data-dismiss="modal" class="close" type="button"></button>
        <div class="modal_img">
            <img class="img-responsive" src="<?php echo SF_URL; ?>assets/images/popup_st4.png"/> 
        </div>
    </div>
</div>

<!-- popup modal 4 end -->

<div class="dashboard_main_wrapper">
    <div class="dashb_in_wrap">
        <form method="post" id="campaignform">
            <header class="dashboard_header">
            	<div class="row">
                	<div class="col-md-4 col-sm-4">
                        <h2 class="dash_title">
                            <?php echo (isset($_GET['edit-id'])) ? stripcslashes($campaign->campaign_name) : 'New Campaign'; ?> 

                        </h2>
                    </div>
                    <div class="col-md-8 col-sm-8 text-right">
                    	<a href="#" class="dash_h_green_btn sc_hidden sc_tut_button_steps">Tutorial</a>
                        <a class="previous dash_h_green_btn" style="display:none">Previous Step</a>
                        <a class="next dash_h_green_btn">Next Step</a>
                        <input type="submit" class="dash_h_green_btn finish" value="Finish" name="save-campaign" style="display:none" onclick=" return validate_camp()"/>
                    </div>
                </div>
            </header>
            
            <? 
	        global $videos;
	        $v_id = "step1";
	        if(!empty($videos[$v_id])) {
				?>
				<div class="video_tut <?= $v_id ?>">
					<button title="Close (Esc)" type="button" class="video_tut_close">×</button>
					<iframe src="<?= $videos[$v_id] ?>" width="642" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				</div>
				<div id="mask"></div>
				<?
			}
			?>
			<? 
	        global $videos;
	        $v_id = "step2";
	        if(!empty($videos[$v_id])) {
				?>
				<div  class="video_tut <?= $v_id ?>">
					<button title="Close (Esc)" type="button" class="video_tut_close">×</button>
					<iframe src="<?= $videos[$v_id] ?>" width="642" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				</div>
				<div id="mask"></div>
				<?
			}
			?>
			<? 
	        global $videos;
	        $v_id = "step3";
	        if(!empty($videos[$v_id])) {
				?>
				<div  class="video_tut <?= $v_id ?>">
					<button title="Close (Esc)" type="button" class="video_tut_close">×</button>
					<iframe src="<?= $videos[$v_id] ?>" width="642" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				</div>
				<div id="mask"></div>
				<?
			}
			?>
			<? 
	        global $videos;
	        $v_id = "step4";
	        if(!empty($videos[$v_id])) {
				?>
				<div  class="video_tut <?= $v_id ?>">
					<button title="Close (Esc)" type="button" class="video_tut_close">×</button>
					<iframe src="<?= $videos[$v_id] ?>" width="642" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				</div>
				<div id="mask"></div>
				<?
			}
			?>
            
             
            <div class="dashboard_tab_section" role="tabpanel">
            	
               <!-- Nav tabs start -->
               <ul role="tablist">
               		<li role="presentation" class="active">
                        <a id="general" href="#gernal_sett" data-id="#gernal_sett" aria-controls="gernal_sett" role="tab" data-toggle="tab">
                        	<span class="g_set_icon"></span><b>General Settings</b>
                        </a>
                    </li>
                    <li role="presentation" >
                        <a id="layout" data-id="#layout_sett" href="#layout_sett"  aria-controls="layout_sett" role="tab" data-toggle="tab">
                        	<span class="lay_set_icon"></span><b>Layout Settings</b>
                        </a>
                    </li>
                    <li role="presentation">
                        <a id="content" href="#content_sett" data-id="#content_sett" aria-controls="content_sett" role="tab" data-toggle="tab">
                        	<span class="cont_set_icon"></span><b>Content Settings</b>
                        </a>
                    </li>
                    <li role="presentation">
                        <a id="gift" href="#gift_sett" data-id="#gift_sett" aria-controls="gift_sett" role="tab" data-toggle="tab">
                        	<span class="gift_set_icon"></span><b>Gift Settings</b>
                        </a>
                    </li>
               </ul>
               
            </div>
            <!-- Nav tabs end -->
            
            <!-- Tab panes start -->        
            <div class="tab-content">
                <!-- General settings -->
             	<div class="tab-pane dash_b_content_wrap active" role="tabpanel" id="gernal_sett">
                    <header class="dash_cont_header">
                        <h3>General Settings</h3>
                        <p>Here you need to specify name of the campaign and other general things...</p>
                    </header>
                    
                    <div class="dash_inner_form_wrap">
                        
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Name of the Campaign</label>
                                <p class="help-block">Give your campaign a name</p>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="campaign_name" value="<?php echo (isset($_GET['edit-id'])) ? stripcslashes($campaign->campaign_name): ''; ?>" />
                            </div>
                        </div>
                          
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Slug of the Campaign</label>
                                <p class="help-block">You can specify a custom URL slug for this campaign, or you can keep it as it is</p>
                            </div>
                            <div class="col-md-8">
                            <?php if(isset($_GET['edit-id'])){ ?>
                                <input type="text" class="form-control" name="campaign_slug" value="<?php echo (isset($_GET['edit-id'])) ? stripcslashes($campaign->campaign_slug): ''; ?>" readonly>
                            <?php }else{ ?>
                                <input type="text" class="form-control" id="campaign_slug" name="campaign_slug" value="">
                            <?php } ?>
                            </div>
                        </div>
                          
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Autoresponder Integration</label>
                                <p class="help-block">Select the autoresponder provider you use. If your provider is not listed simply select “Other” from the dropdown list</p>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control autoresponder" name="autoresponder">
                                    <option value="aweber" <?php echo (isset($_GET['edit-id']) && $campaign->autoresponder == 'aweber') ? 'selected': ''; ?>>AWeber</option>
                                    <option value="getresponse" <?php echo (isset($_GET['edit-id']) && $campaign->autoresponder == 'getresponse') ? 'selected' : ''; ?> >GetResponse</option>
                                    <option value="betaGetresponse" <?php echo (isset($_GET['edit-id']) && $campaign->autoresponder == 'betaGetresponse') ? 'selected' : ''; ?> >GetResponse (Beta Version)</option>
                                    <!-- <option value="mailchimp" <?php echo (isset($_GET['edit-id']) && $campaign->autoresponder == 'mailchimp') ? 'selected' : ''; ?> >MailChimp</option> -->
                                    <option value="other" <?php echo (isset($_GET['edit-id']) && $campaign->autoresponder == 'other') ? 'selected': ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1" class="aweber ar" style="display:<?php if(isset($_GET['edit-id']) && $campaign->autoresponder != 'aweber') echo 'none'; ?>">AWeber List Name</label>
                                <label for="exampleInputEmail1" class="getresponse ar" style="display:<?php echo (isset($_GET['edit-id']) && $campaign->autoresponder == 'getresponse') ? 'block': 'none'; ?>; ">GetResponse Web Form Code</label>
                                <label for="exampleInputEmail1" class="betaGetresponse ar" style="display:<?php echo (isset($_GET['edit-id']) && $campaign->autoresponder == 'betaGetresponse') ? 'block': 'none'; ?>; ">GetResponse (Beta Version) Web Form Code</label>

                                <!-- <label for="exampleInputEmail1" class="mailchimp ar" style="display:<?php echo (isset($_GET['edit-id']) && $campaign->autoresponder == 'mailchimp') ? 'block': 'none'; ?>; ">MailChimp Web Form Code</label> -->
                                <label for="exampleInputEmail1" class="other ar" style="display:<?php echo (isset($_GET['edit-id']) && $campaign->autoresponder == 'other') ? 'block': 'none'?>;">Autoresponder Web Form Code</label>
                                <p class="help-block">
                                    <span class="aweber ar" style="display:<?php if(isset($_GET['edit-id']) && $campaign->autoresponder != 'aweber') echo 'none'; ?>">Copy and paste the unique AWeber list name. It looks similar to awlist3716987</span>
                                    <span class="getresponse ar" style="display:<?php echo (isset($_GET['edit-id']) && $campaign->autoresponder == 'getresponse') ? 'block': 'none'; ?>; ">Copy and paste the GetResponse javascript web form code. It should look similar to this: &lt;script type=&quot;text/javascript&quot; src=&quot;http://app.getresponse.com/view_webform.js?wid=XXXXXX&quot;&gt;&lt;/script&gt;</span>
                                    <span class="betaGetresponse ar" style="display:<?php echo (isset($_GET['edit-id']) && $campaign->autoresponder == 'betaGetresponse') ? 'block': 'none'; ?>; ">Copy and paste the GetResponse javascript web form code. It should look similar to this: &lt;script type=&quot;text/javascript&quot; src=&quot;http://app.getresponse.com/view_webform_v2.js?wid=XXXXXX&quot;&gt;&lt;/script&gt;</span>
                                    <!-- <span class="mailchimp ar" style="display:<?php echo (isset($_GET['edit-id']) && $campaign->autoresponder == 'mailchimp') ? 'block': 'none'; ?>; ">Copy and paste the MailChimp HTML web form code</span> -->
                                    <span class="other ar" style="display:<?php echo (isset($_GET['edit-id']) && $campaign->autoresponder == 'other') ? 'block': 'none'?>;">Copy and paste the autoresponder web form code</span>
                                </p>
                            </div>
                            <?php $autores = stripcslashes($campaign->autoresponder_code); ?>
                            <div class="col-md-8">
                                <textarea class="form-control" rows="3" name="autoresponder_code"><?php echo (isset($_GET['edit-id'])) ? trim($autores): ''; ?></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Include the Name Field</label>
                                <p class="help-block">You can specify whether you want to collect the subscriber names or not. You need to have an email field created in your webform for this to work!</p>
                            </div>
                            <div class="col-md-8">
                                <input type="checkbox" class="checkradios custom" name="include_field" <?php echo (isset($_GET['edit-id']) && $campaign->include_field == 1) ? 'checked': ''; ?>/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>Facebook Retargeting Pixel</label>
                                <p class="help-block">You can use Facebook (or other platform) retargeting. Insert code here, and it will be placed in  You can find retargeting training in members area</p>
                            </div>

                            <div class="col-md-8">
                                <textarea placeholder="" class="form-control" rows="3" name="fb_retarget_pixel"><?php echo (isset($_GET['edit-id'])) ? stripcslashes($campaign->fb_retarget_pixel): ''; ?></textarea>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- End of General settings -->
                <!-- Layout settings -->
                <div class="tab-pane dash_b_content_wrap " role="tabpanel" id="layout_sett">
                    <header class="dash_cont_header">
                        <h3>Layout Settings</h3>
                        <p>Here you need to specify the source of the campaign and layout of popup box...</p>
                    </header>
                    
                    <div class="dash_inner_form_wrap">
                        
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Source URL</label>
                                <p class="help-block">What page you want to use as source page to display popup</p>
                            </div>
                            <div class="col-md-8">
                                <input type="text" id="source_url" class="form-control" name="source_url" value="<?php echo (isset($_GET['edit-id'])) ? stripcslashes($campaign->source_url): ''; ?>" />
                            </div>
                        </div>
                          
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Popup Style</label>
                                <p class="help-block">Select the style of the popup box that will appear</p>
                            </div>
                            <div class="col-md-8">

                                <div class="popup_style_wrapper">

                                    <div class="popu_styl_sect popup_style1 <?php echo (isset($_GET['edit-id']) && $campaign->popup_style ==1) ? 'active': ''; ?>" data-popstyle="1">
                                        <section>
                                            <img src="<?php echo SF_URL; ?>assets/images/popup_style1.jpg" class="popup" />
                                            <span class="dash_popup_hover"  data-toggle="modal" data-target="#popup_style1"><i class="fa fa-search-plus"></i></span>
                                        </section>
                                    </div>

                                    <div class="popu_styl_sect popup_style1 <?php echo (isset($_GET['edit-id']) && $campaign->popup_style ==2) ? 'active': ''; ?>" data-popstyle="2">
                                        <section>
                                            <img src="<?php echo SF_URL; ?>assets/images/popup_style2.jpg" class="popup" />
                                            <span class="dash_popup_hover" data-toggle="modal" data-target="#popup_style2"><i class="fa fa-search-plus"></i></span>
                                        </section>
                                    </div>

                                    <div class="popu_styl_sect popup_style1 <?php echo (isset($_GET['edit-id']) && $campaign->popup_style ==3) ? 'active': ''; ?>" data-popstyle="3">
                                        <section>
                                            <img src="<?php echo SF_URL; ?>assets/images/popup_style3.jpg" class="popup" />
                                            <span  data-toggle="modal" data-target="#popup_style3" class="dash_popup_hover"><i class="fa fa-search-plus"></i></span>
                                        </section>
                                    </div>

                                    <div class="popu_styl_sect popup_style1 <?php echo (isset($_GET['edit-id']) && $campaign->popup_style ==4) ? 'active': ''; ?>" data-popstyle="4">
                                        <section>
                                            <img src="<?php echo SF_URL; ?>assets/images/popup_style4.jpg" class="popup" />
                                            <span  data-toggle="modal" data-target="#popup_style4" class="dash_popup_hover"><i class="fa fa-search-plus"></i></span>
                                        </section>
                                    </div>
                                    <input type="hidden" value="<?php echo (isset($_GET['edit-id'])) ? $campaign->popup_style : '1'; ?>" id="popstyle" name="popstyle" />
                                </div>
                            </div>
                        </div>
                          
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Delay</label>
                                <p class="help-block">Specify the delay in seconds after how many seconds the popup should be displayed</p>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="delay" value="<?php echo (isset($_GET['edit-id'])) ? $campaign->delay : '1'; ?>" >
                            </div>
                        </div>
                          
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Closable</label>
                                <p class="help-block">Specify whether you want to allow visitor to close the popup box</p>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control" name="closable">
                                    <option value="1" <?php echo (isset($_GET['edit-id']) && $campaign->closable == 1) ? 'selected' : ''; ?> >Yes</option>
                                    <option value="0" <?php echo (isset($_GET['edit-id']) && $campaign->closable == 0) ? 'selected' : ''; ?> >No</option>
                                </select>
                            </div>
                        </div>
                        
                        <?php if(isset($_GET['edit-id'])){
                            $pdetails = json_decode($campaign->popup_details);
                        } ?>

                        <!-- <div id="main_heading" class="main_heading" style="display:<?php //echo (isset($_GET['edit-id'])) ? 'block': 'none'; ?>">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 1 Headline</label>
                                    <p class="help-block">Enter the headline that you want to display in the first step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="s1headline" value="<?php echo (isset($_GET['edit-id'])) ?  stripslashes($pdetails->s1headline1) : ''; ?>" />
                                </div>
                            </div>
                        </div> -->
                       

                        <!--  pop up style 1 options -->
                        
                        <div id="popstyleoption1" class="popstyleoption" style="display:<?php echo (isset($_GET['edit-id']) && $campaign->popup_style ==1) ? 'block': 'none'; ?>">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Step 1 Headline</label>
                                <p class="help-block">Enter the headline that you want to display in the first step of the optin process</p>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="s1headline1" value="<?php echo (isset($_GET['edit-id'])) ?  stripslashes($pdetails->s1headline1) : ''; ?>" />
                            </div>
                        </div>

                        <input type="hidden" class="tbuploadcurrent" />
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Bullet Point 1</label>
                                    <p class="help-block">Enter the bullet point that will be displayed in the first step of optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p1s1bullet1" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p1s1bullet1)) ? stripslashes($pdetails->p1s1bullet1) : '' ;?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Bullet Point 2</label>
                                    <p class="help-block">Enter the bullet point that will be displayed in the first step of optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p1s1bullet2" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p1s1bullet2)) ? stripslashes($pdetails->p1s1bullet2) : '' ;?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Bullet Point 3</label>
                                    <p class="help-block">Enter the bullet point that will be displayed in the first step of optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p1s1bullet3" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p1s1bullet3)) ? stripslashes($pdetails->p1s1bullet3) : '';?>">
                                </div>
                            </div>

                            <?php add_thickbox(); ?>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 1 Image</label>
                                    <p class="help-block">Recommended image size 200 X 230 pixels</p>
                                </div>
                                <div class="col-md-8">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div>
                                            <span class="btn input_file_select btn-file">
                                                <span class="fileinput-new">Select image</span>
                                                <span class="fileinput-exists">Change</span>
                                                <input type="file" class="tbupload" data-to="p1s1image" />
                                               <!--  <input alt="media-upload.php?TB_iframe=true&amp;height=400&amp;width=700" title="Add a Image" class="thickbox button-primary" type="button" value="Upload Image" />   -->
                                            </span>
                                            <a href="#" class="btn input_file_select fileinput-exists" data-dismiss="fileinput">Remove</a>
                                        </div>
                                        <br/>
                                        <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                        <div class="fileinput-new p1s1image">
                                            <img class="featured_img1" <?php echo (isset($_GET['edit-id']) && isset($pdetails->p1s1image)) ? 'src="'.stripcslashes($pdetails->p1s1image).'"' : ''; ?>>
                                            <input type="hidden" name="p1s1image" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p1s1image)) ? $pdetails->p1s1image : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                              
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Button Text</label>
                                    <p class="help-block">Enter the text that will be displayed on the call to action button</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p1s1buttontext" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p1s1buttontext)) ? stripslashes($pdetails->p1s1buttontext) : '';  ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Include “It’s Free” Sign</label>
                                    <p class="help-block">Specify whether you want to show “(it’s free)” sign under the call to action button</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="checkbox" class="checkradios custom" name="p1s1sign" 
                                        <?php if(isset($_GET['edit-id']) && isset($pdetails->p1s1sign)){
                                            echo ($pdetails->p1s1sign == 1)?'checked':'';
                                        } ?> 
                                    />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 2 Headline</label>
                                    <p class="help-block">Enter the headline that you want to display in the second step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p1s2headline" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p1s2headline)) ? stripslashes($pdetails->p1s2headline) : ''; ?>" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 2 Subheadline</label>
                                    <p class="help-block">Enter the headline that you want to display in the second step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p1s2subheadline" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p1s2subheadline)) ? stripslashes($pdetails->p1s2subheadline) : ''; ?>" />
                                </div>
                            </div>
                        </div>
                        
                        <!--  pop up style 2 options -->
                        
                        <div id="popstyleoption2" class="popstyleoption" style="display:<?php echo (isset($_GET['edit-id']) && $campaign->popup_style ==2) ? 'block': 'none'; ?>">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 1 Headline</label>
                                    <p class="help-block">Enter the headline that you want to display in the first step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="s1headline2" value="<?php echo (isset($_GET['edit-id'])) ?  stripslashes($pdetails->s1headline2) : ''; ?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 1 Subheadline</label>
                                    <p class="help-block">Enter the subheadline that you want to display in the first step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p2s1subheadline" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p2s1subheadline)) ?  stripslashes($pdetails->p2s1subheadline) : ''; ?>" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Button Text</label>
                                    <p class="help-block">Enter the text that will be displayed on the call to action button</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p2s1buttontext" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p2s1buttontext)) ?  stripslashes($pdetails->p2s1buttontext) : ''; ?>" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Include “It’s Free” Sign</label>
                                    <p class="help-block">Specify whether you want to show “(it’s free)” sign under the call to action button</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="checkbox" class="checkradios custom" name="p2s1sign" 
                                    <?php if(isset($_GET['edit-id']) && isset($pdetails->p2s1sign)){
                                        echo ($pdetails->p2s1sign == 1)?'checked':'';
                                    } ?>
                                     />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 2 Headline</label>
                                    <p class="help-block">Enter the headline that you want to display in the second step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p2s2headline" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p2s2headline)) ?  stripslashes($pdetails->p2s2headline) : ''; ?>" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 2 Subheadline</label>
                                    <p class="help-block">Enter the headline that you want to display in the second step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p2s2subheadline" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p2s2subheadline)) ?  stripslashes($pdetails->p2s2subheadline) : ''; ?>"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 2 Button Text</label>
                                    <p class="help-block">Enter the text that will be displayed on the second call to action button</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p2s2buttontext" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p2s2buttontext)) ?  stripslashes($pdetails->p2s2buttontext) : ''; ?>" />
                                </div>
                            </div>
                        </div>
                        
                        <!--  pop up style 3 options -->

                        <div id="popstyleoption3" class="popstyleoption" style="display:<?php echo (isset($_GET['edit-id']) && $campaign->popup_style ==3) ? 'block': 'none'; ?>">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 1 Headline</label>
                                    <p class="help-block">Enter the headline that you want to display in the first step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="s1headline3" value="<?php echo (isset($_GET['edit-id'])) ?  stripslashes($pdetails->s1headline3) : ''; ?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 1 Subheadline</label>
                                    <p class="help-block">Enter the subheadline that you want to display in the first step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p3s1subheadline" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p3s1subheadline)) ?  stripslashes($pdetails->p3s1subheadline) : ''; ?>" />
                                </div>
                            </div>
                              
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Yes Button Text</label>
                                    <p class="help-block">Specify the text for the YES button</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p3s1yesbutton" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p3s1yesbutton)) ?  stripslashes($pdetails->p3s1yesbutton) : ''; ?>" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">No Button Text</label>
                                    <p class="help-block">Specify the text for the NO button</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p3s1nobutton" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p3s1nobutton)) ?  stripslashes($pdetails->p3s1nobutton) : ''; ?>"  />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Include “It’s Free” Sign</label>
                                    <p class="help-block">Specify whether you want to show “(it’s free)” sign under the call to action button</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="checkbox" class="checkradios custom" name="p3s1sign" 
                                     <?php if(isset($_GET['edit-id']) && isset($pdetails->p3s1sign)){
                                        echo ($pdetails->p3s1sign == 1)?'checked':'';
                                    } ?>
                                    />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 2 Headline</label>
                                    <p class="help-block">Enter the headline that you want to display in the second step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p3s2headline" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p3s2headline)) ?  stripslashes($pdetails->p3s2headline) : ''; ?>"  />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 2 Subheadline</label>
                                    <p class="help-block">Enter the headline that you want to display in the second step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p3s2subheadline" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p3s2subheadline)) ?  stripslashes($pdetails->p3s2subheadline) : ''; ?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 2 Button Text</label>
                                    <p class="help-block">Specify the text for the call to action button displayed on the second step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p3s2buttontext" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p3s2buttontext)) ?  stripslashes($pdetails->p3s2buttontext) : ''; ?>" />
                                </div>
                            </div>
                        </div>
                        
                        <!--  pop up style 4 options -->
                        
                        <div id="popstyleoption4" class="popstyleoption" style="display:<?php echo (isset($_GET['edit-id']) && $campaign->popup_style ==4) ? 'block': 'none'; ?>">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 1 Headline</label>
                                    <p class="help-block">Enter the headline that you want to display in the first step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="s1headline4" value="<?php echo (isset($_GET['edit-id'])) ?  stripslashes($pdetails->s1headline4) : ''; ?>" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 1 Subheadline</label>
                                    <p class="help-block">Enter the subheadline that you want to display in the first step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p4s1subheadline" value="<?php echo (isset($_GET['edit-id']) && $pdetails->p4s1subheadline) ?  stripslashes($pdetails->p4s1subheadline) : ''; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 1 Image</label>
                                    <p class="help-block">Recommended image size 300 x 170 pixels</p>
                                </div>
                                <div class="col-md-8">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div>
                                            <span class="btn input_file_select btn-file">
                                                <span class="fileinput-new">Select image</span>
                                                <span class="fileinput-exists">Change</span>
                                                <input type="file" class="tbupload" data-to="p4s1image" />
                                            </span>
                                            <a href="#" class="btn input_file_select fileinput-exists" data-dismiss="fileinput">Remove</a>
                                        </div>
                                        <br/>
                                        <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                        <div class="fileinput-new p4s1image">
                                            <img <?php echo (isset($_GET['edit-id']) && isset($pdetails->p4s1image)) ? 'src="'.$pdetails->p4s1image.'"' : ''; ?> />
                                            <input type="hidden" name="p4s1image" value="<?php echo (isset($_GET['edit-id']) && isset($pdetails->p4s1image)) ? $pdetails->p4s1image : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                              
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Button Text</label>
                                    <p class="help-block">Enter the text that will be displayed on the call to action button</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p4s1buttontext"  value="<?php echo (isset($_GET['edit-id']) && $pdetails->p4s1buttontext) ?  stripslashes($pdetails->p4s1buttontext) : ''; ?>" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Include “It’s Free” Sign</label>
                                    <p class="help-block">Specify whether you want to show “(it’s free)” sign under the call to action button</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="checkbox" class="checkradios custom" name="p4s1sign"
                                    <?php if(isset($_GET['edit-id']) && isset($pdetails->p4s1sign)){
                                        echo ($pdetails->p4s1sign == 1)?'checked':'';
                                    } ?>
                                    />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 2 Headline</label>
                                    <p class="help-block">Enter the headline that you want to display in the second step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p4s2headline" value="<?php echo (isset($_GET['edit-id']) && $pdetails->p4s2headline) ?  stripslashes($pdetails->p4s2headline) : ''; ?>" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Step 2 Subheadline</label>
                                    <p class="help-block">Enter the headline that you want to display in the second step of the optin process</p>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="p4s2subheadline" value="<?php echo (isset($_GET['edit-id']) && $pdetails->p4s2subheadline) ?  stripslashes($pdetails->p4s2subheadline) : ''; ?>" />
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
                <!-- End of Layout settings -->
                <!-- Content settings -->
                <div class="tab-pane dash_b_content_wrap" role="tabpanel" id="content_sett">
                    <header class="dash_cont_header">
                        <h3>Content Settings</h3>
                        <p>Here you specify the settings for the page where visitors are going to be taken after they optin</p>
                    </header>
                    
                    <div class="dash_inner_form_wrap">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Headline</label>
                                <p class="help-block">Specify the headline for your content page</p>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="content_headline" value="<?php echo (isset($_GET['edit-id'])) ? stripcslashes($campaign->content_headline) : ''; ?>"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Enable Video</label>
                                <p class="help-block">You can enable or disable the video on your content page. We highly recommend you have a video, because that will increase the engagement!</p>
                            </div>
                            <div class="col-md-8">
                                <div class="vidoe_disable_enble">
                                    <input type="checkbox" class="checkradios custom" name="enable_video" <?php echo (isset($_GET['edit-id']) && $campaign->enable_video == 1) ? 'checked' : ''; ?> />
                                </div>
                            </div>
                        </div>
                        <?php if(isset($_GET['edit-id'])){
                            if($campaign->enable_video ==0){
                                $display = 'none';  
                            }else{
                                $display = 'block';
                            }
                        }else{
                            $display = 'none';
                        }  ?>
                        <div class="form-group row" id="videodiv" style="display:<?php echo $display; ?>;">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Video Embed Code</label>
                                <p class="help-block">Insert the video embed code. Recommended video size is 640 x 360 pixels</p>
                            </div>
                            <div class="col-md-8">
                                <textarea class="form-control" rows="3" name="video_embed_code"><?php echo (isset($_GET['edit-id'])) ? stripslashes($campaign->video_embed_code) : ''; ?></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Subheadline</label>
                                <p class="help-block">Specify the subheadline for your content page</p>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="content_subheadline" value="<?php echo (isset($_GET['edit-id'])) ? stripcslashes($campaign->content_subheadline) : ''; ?>"  />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>Button Text</label>
                                <p class="help-block">Enter the text for the download button</p>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control button_text" name="button_text" value="<?php echo (isset($_GET['edit-id'])) ? stripcslashes($campaign->button_text) : ''; ?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Support Email</label>
                                <p class="help-block">You can include a support email for your subscribers. Leave blank if you don’t have to have any support email</p>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="support_email"  value="<?php echo (isset($_GET['edit-id'])) ? stripcslashes($campaign->support_email) : ''; ?>" >
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Content settings -->

                <!-- Gift settings -->
                <div class="tab-pane dash_b_content_wrap" role="tabpanel" id="gift_sett">
                    <header class="dash_cont_header">
                        <h3>Gift Settings</h3>
                        <p>Here you can upload and describe the gift you are giving to your subscribers</p>
                    </header>
                    
                    <div class="dash_inner_form_wrap">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Gift Download</label>
                                <p class="help-block">Upload your gift or specify the URL to the download source</p>
                            </div>
                            <div class="col-md-8">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <!-- <div class="form-control" data-trigger="fileinput"> -->
                                        <!-- <i class="glyphicon glyphicon-file fileinput-exists"></i> 
                                        <span class="fileinput-filename"></span> -->
                                        <input type="text" name="gift_url" class="gifturl form-control"  value="<?php echo (isset($_GET['edit-id'])) ? stripcslashes($campaign->gift_url) : '';  ?>"/>
                                    <!-- </div> -->
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">BROWSE</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="file" class="giftupload" data-to="featureimagebrowse" />
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                </div>
                            </div>
                        </div>
                          
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Unlock After</label>
                                <p class="help-block">Specify how many unique subscribers a visitor must refer to unlock the gift</p>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control unlock_after" name="unlock_after" value="<?php echo (isset($_GET['edit-id'])) ? $campaign->unlock_after : ''; ?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>Page Title</label>
                                <p class="help-block">Specify the content page title</p>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="page_title" value="<?php echo (isset($_GET['edit-id'])) ? stripcslashes($campaign->page_title) : ''; ?>" />
                                
                            </div>
                        </div>


                        <div id="featurediv">
                            
                            <?php if(isset($_GET['edit-id'])){
                                $features = json_decode($campaign->feature_title); 
                                $ftext = json_decode($campaign->feature_text);
                                $fimage = json_decode($campaign->feature_image);
                                $fimagealign = json_decode($campaign->feature_image_align);
                                $totalft = count($features); 
                                if($features):
                                foreach ($features as $key => $feature) { ?>
                                    <div class="well">
                                
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="exampleInputEmail1">Feature Title</label>
                                                <p class="help-block">Specify the title of the feature</p>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="feature_title[]" value="<?php echo stripcslashes($feature); ?>" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="exampleInputEmail1">Feature Text</label>
                                                <p class="help-block">Describe the feature of your gift</p>
                                            </div>
                                            <div class="col-md-8">
                                                <textarea class="form-control" rows="3" name="feature_text[]"><?php echo stripcslashes($ftext[$key]); ?></textarea>
                                            </div>
                                        </div>
                                          
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="exampleInputEmail1">Feature Image</label>
                                                <p class="help-block">Recommended image size 280 x 210 pixels. Do not upload images greater than 1200 x 1000 pixels.</p>
                                            </div>
                                            <div class="col-md-8">

                                                <div class="fileinput fileinput-new" data-provides="fileinput">

                                                <div>
                                                    <span class="btn input_file_select btn-file">
                                                        <span class="fileinput-new">Select image</span>
                                                        <span class="fileinput-exists">Change</span>
                                                        <input type="file" class="tbupload" data-to="giftfeatureimage-<?php echo $key+1;?>" />
                                                    </span>
                                                    <a href="#" class="btn input_file_select fileinput-exists" data-dismiss="fileinput">Remove</a>
                                                </div>
                                                <br/>
                                                <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                                    <div class="fileinput-new giftfeatureimage-<?php echo $key+1;?>">
                                                        <img class="featured_img1"  <?php echo ($fimage[$key]) ? 'src="'.$fimage[$key].'"' : ''; ?> />
                                                        <input type="hidden" name="giftfeatureimage[]" value="<?php echo $fimage[$key];  ?>">
                                                    </div>
                                                </div>     
                                            </div>
                                        </div>
                                          
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="exampleInputEmail1">Image alignment</label>
                                                <p class="help-block">Specify whether you want to align the image to the left or right</p>
                                            </div>
                                            <div class="col-md-8">
                                                <select class="form-control" name="feature_image_align[]">
                                                    <option value="left" <?php echo ($fimagealign[$key] == 'left') ?'selected':''; ?> >Left</option>
                                                    <option value="right" <?php echo ($fimagealign[$key] == 'right') ?'selected':''; ?>>Right</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                <?php }
                                endif;
                            } ?>

                        </div>

                        <div id="featureContent" style="display:none;">
                            <div class="well">
                                
                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label for="exampleInputEmail1">Feature Title</label>
                                        <p class="help-block">Specify the title of the feature</p>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="feature_title[]">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label for="exampleInputEmail1">Feature Text</label>
                                        <p class="help-block">Describe the feature of your gift</p>
                                    </div>
                                    <div class="col-md-8">
                                        <textarea class="form-control" rows="3" name="feature_text[]"></textarea>
                                    </div>
                                </div>
                                  
                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label for="exampleInputEmail1">Feature Image</label>
                                        <p class="help-block">Recommended image size 280 x 210 pixels. Do not upload images greater than 1200 x 1000 pixels.</p>
                                    </div>
                                    <div class="col-md-8">

                                        <div class="fileinput fileinput-new" data-provides="fileinput">

                                        <div>
                                            <span class="btn input_file_select btn-file">
                                                <span class="fileinput-new">Select image</span>
                                                <span class="fileinput-exists">Change</span>
                                                <input type="file" class="tbupload" data-to="giftfeatureimage" />
                                            </span>
                                            <a href="#" class="btn input_file_select fileinput-exists" data-dismiss="fileinput">Remove</a>
                                        </div>
                                        <br/>
                                        <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                            <div class="fileinput-new giftfeatureimage">
                                                <img class="featured_img1" />
                                                <input type="hidden" name="giftfeatureimage[]">
                                            </div>
                                        </div>     
                                    </div>
                                </div>
                                  
                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label for="exampleInputEmail1">Image alignment</label>
                                        <p class="help-block">Specify whether you want to align the image to the left or right</p>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" name="feature_image_align[]">
                                            <option value="left">Left</option>
                                            <option value="right">Right</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                          
                          
                        <div class="text-center">
                            <div class="form-inline gift_feature">
                                <button class="btn remove_btn"
                                    <?php if(isset($_GET['edit-id']) && (count($features) != 0)){
                                        
                                    }else{
                                        echo 'style="display:none;"';
                                    } ?>
                                >REMOVE</button>
                                <button class="btn add_btn">ADD</button>
                            </div>
                        </div>
                          
                    </div>
                    
                </div>
                <!-- End of Gift settings -->
            </div>
            <!-- Tab panes end -->
            
        </form>
    </div>
</div>

<!-- alert modal -->
<div class="modal fade bs-example-modal-sm alert_popup" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="gridSystemModalLabel">Alert</h4>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12 alert_data" style="color:red"></div>
            </div>
          </div>
        </div>      
    </div>
  </div>
</div>
<!-- alert modal -->

<script type="text/javascript">
    $(document).ready(function(){
        
        $("#general").click(function(){
            $('.previous').hide();
            $('.next').show();
            $('.finish').hide();
        });
    });

    $(document).ready(function(){
        
        $("#layout").click(function(){
            $('.previous').show();
            $('.next').show();
            $('.finish').hide();
        });
    });

    $(document).ready(function(){
        
        $("#content").click(function(){
            $('.previous').show();
            $('.next').show();
            $('.finish').hide();
        });
    });

    $(document).ready(function(){
        
        $("#gift").click(function(){
           
            $('.previous').show();
            $('.next').hide();
            $('.finish').show();
        });
    });
</script>
<script>

//Get
</script>

<script>

    function validate_camp()
    {
        var show_error = "";
        //var myRegExp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
        var bla = $('#source_url').val();
        var s_l = bla.length;
        var dotpos = bla.lastIndexOf(".");
        var slug = $('#campaign_slug').val();
        var unlock_after = $('.unlock_after').val();
        var urlToValidate = bla;
       
        if(slug == "")
        {
            show_error += "Please Enter Slug Name<br/>";
        }

        if(s_l == "" || s_l < dotpos + 2 || dotpos < 2 )
        {
             show_error += "Please Enter a Valid URL<br/>";
        }
        
        // if (!myRegExp.test(bla)){
        //     show_error += "Please Enter a Valid URL<br/>";
        // }

        if(unlock_after == "")
        {
            show_error += "Please Enter Unlock value<br/>";   
        }

        if(show_error != "")
        {
            $(".alert_data").html(show_error);
            $('.alert_popup').modal('show');
            return false;
        }
        return true;
    }

</script>
<style type="text/css">
    #adminmenuback{ z-index:2 !important;}
    .toplevel_page_social-funnel {
  background: none repeat scroll 0 0 #f1f1f1;
}
</style>
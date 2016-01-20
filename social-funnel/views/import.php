<?php 
if(isset($_POST['import'])){
	global $wpdb;
	
	if(empty($_FILES['importfile']['tmp_name'])) {
		wp_redirect("admin.php?page=social-funnel&sfaction=nofile&sf=import");
		exit;
	} 	
	$homepage = file_get_contents($_FILES['importfile']['tmp_name']);
	$json = $homepage;
	$jay = json_decode($json,true);
	$json = json_decode($jay["content"], true);
	if(isset($_POST['campaign_name']) && trim($_POST['campaign_name'] != '')){
		$json['campaign_name'] = $_POST['campaign_name'];	
	}else{
		$json['campaign_name'] = $json['campaign_name']." 2";
	}
	
	if(empty($json['campaign_name'])) {
		wp_redirect("admin.php?page=social-funnel&sfaction=noname&sf=import");
		exit;
	}
	
	$json['campaign_name'] = str_replace("\"", "&quot;",$json['campaign_name']);
	
	$checkexisting = $wpdb->query("select * from `".$wpdb->prefix."campaigns` where `campaign_name` = '".$json['campaign_name']."'");
	if($checkexisting){
		$json['campaign_name'] = $json['campaign_name']." 2";
	}
	foreach($_POST as $key => $val) {
	    $_POST[$key] = str_replace("\"", "&quot;", $val );
    }
	/** Upload All Images **/

	$upload_dir = wp_upload_dir();
	$public_upload_dir = $upload_dir['url']."/";
	$upload_dir = $upload_dir['path']."/";

	/* Image */
	if(isset($jay["images"]))
	{
		$jay_image = $jay["images"]["data"];
		$old_img_path = $jay["images"]["old_img"];
		
		if(!empty($jay_image)) {
			if(!empty($jay_image["data"])) {
				$new_name = md5(microtime()).".".$jay_image["ext"];
				$fh = fopen($upload_dir.$new_name,"w");
				fwrite($fh, base64_decode($jay_image['data']));
				fclose($fh);
				$new_path = $public_upload_dir.$new_name;
				$json["popup_details"] = str_replace($old_img_path, $new_path, $json["popup_details"]);
			}
		}
	}
	

	/* Gift Image */

	if(isset($jay["gift_images"]))
	{
		$gift_image = $jay["gift_images"]["data"];
		$gift_img_path = $jay["gift_images"]["old_img"];

		if(!empty($gift_image)) {
			if(!empty($gift_image["data"])) {
				$new_name = md5(microtime())."_".strlen($gift_image["data"]).".".$gift_image["ext"];
				$fh = fopen($upload_dir.$new_name,"w");
				fwrite($fh, base64_decode($gift_image['data']));
				fclose($fh);
				$new_path = $public_upload_dir.$new_name;
				$json["gift_url"] = $new_path;
			}
		}
	}

	/* Feature Images */

	if(isset($jay["feature_images"]))
	{	
		foreach($jay["feature_images"]["data"] as $fet)
		{
			if(!empty($fet)) {
				if(!empty($fet["data"])) {
					$new_name = md5(microtime())."_".strlen($fet["data"]).".".$fet["ext"];
					$fh = fopen($upload_dir.$new_name,"w");
					fwrite($fh, base64_decode($fet['data']));
					fclose($fh);
					$fet_array[] = $public_upload_dir.$new_name;
				}
			}
		}
		
		$fet_img = '["' . implode( '","' , $fet_array ) . '"]' ;
		$json["feature_image"] = $fet_img;
	}

	/** Upload Images **/
	
	unset($json['id']);
	$post_info = array(
        'post_status' => 'publish', 
        'post_type' => 'socialfunnel',
        'post_title' => $json['campaign_name']
    );
    $new_postid = wp_insert_post($post_info);
    $post_slug = get_post($new_postid);
    $slug = $post_slug->post_name;
    $camp_name = $post_slug->post_title;
    $json['campaign_slug'] = $slug;  
    $camp_table = $wpdb->prefix."campaigns";
    if($new_postid){
    	$json['post_id'] = $new_postid;
    	$unique_id = randomPassword();
    	$insert = array(
                    "unique_id" => $unique_id,
                    "post_id" => $new_postid,
                    "campaign_name" => $camp_name,
                    "campaign_slug" => $slug,
                    "autoresponder" => $json['autoresponder'],
                    "autoresponder_code" => $json['autoresponder_code'],
                    "include_field" =>  $json["include_field"],
                    "fb_retarget_pixel" => $json['fb_retarget_pixel'],
                    "source_url" => $json['source_url'],
                    "popup_style" => $json['popup_style'],
                    "delay" => $json['delay'],
                    "closable" =>  $json['closable'],
                    "popup_details" => $json["popup_details"],
                    "content_headline" => $json['content_headline'],
                    "enable_video" => $json["enable_video"],
                    "video_embed_code" => $json['video_embed_code'],
                    "content_subheadline" => $json['content_subheadline'],
                    "support_email" => $json['support_email'],
                    "gift_url" => $json['gift_url'],
                    "unlock_after" => $json['unlock_after'],
                    "button_text" => $json['button_text'],
                    "page_title" => $json['page_title'],
                    "feature_title" => $json["feature_title"],
                    "feature_text" => $json["feature_text"],
                    "feature_image" => $json["feature_image"],
                    "feature_image_align" => $json["feature_image_align"]
            );

		$query_insert = $wpdb->insert($camp_table,$insert);

		if($query_insert){
			$lid = $wpdb->insert_id;
			$social_clicks = $wpdb->prefix."clicks";
            $wpdb->query(" INSERT INTO $social_clicks (camp_id,slug) VALUES ($lid, '$slug') ");
			wp_redirect("?page=social-funnel&sfaction=import");
		}else{
			wp_redirect("?page=social-funnel&sfaction=importfail");
		}
    }
} 
?>
<div class="dashboard_main_wrapper">
	<div class="dashb_in_wrap">
        <form method="post" enctype="multipart/form-data">
	        
	        <header class="dashboard_header">
	        	<div class="row">
	            	<div class="col-md-4 col-sm-4"><h2 class="dash_title">Import Campaign</h2></div>
	                <div class="col-md-8 col-sm-8 text-right">
	                	<a href="#" class="dash_h_green_btn sc_hidden sc_tut_button">Tutorial</a>
	                    <a class="dash_h_green_btn" href="?page=social-funnel">Back</a>
	                    <button class="dash_h_green_btn finish" type="submit" name="import">Import</button>
	                </div>
	            </div>
	        </header>
			<? 
	        global $videos;
	        $v_id = "import";
	        if(!empty($videos[$v_id])) {
				?>
				<div class="video_tut">
					<button title="Close (Esc)" type="button" class="video_tut_close">×</button>
					<iframe src="<?= $videos[$v_id] ?>" width="642" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				</div>
				<div id="mask"></div>
				<?
			}
			?>
	        <div class="dash_b_content_wrap">
	        	<header class="dash_cont_header">
	            	<h3>Import</h3>
	                <p>Here you can import previously exported Social Funnels campaigns...</p>
	            </header>
	            <div class="dash_inner_form_wrap">
	               <?php if(isset($_GET['sfaction'])) {
		            	if($_GET['sfaction'] == "nofile") {
		            	?>
	            	
						<div id="messages">
							<br>
							<div class="alert campaign_successfully_failed">
	                            <a data-dismiss="alert" class="close" href="#"><span class="fa fa-close"></span></a>
	                            No file selected!
	                        </div>
						</div>
						<?php
						} elseif($_GET['sfaction'] == "noname") {
		            	?>
	            	
						<div id="messages">
							<br>
							<div class="alert campaign_successfully_failed">
	                            <a data-dismiss="alert" class="close" href="#"><span class="fa fa-close"></span></a>
	                            Please provide a campaign name!
	                        </div>
						</div>
						<?php
						}
					} ?>
 
	                <div class="form-group row">
	                    <div class="col-md-4">
	                    	<label for="exampleInputEmail1">Campaign Name</label>
	                        <p class="help-block">Give your new imported campaign a name</p>
	                    </div>
	                    <div class="col-md-8"><input type="text" class="form-control" name="campaign_name"></div>
	                </div>
                  	<div class="form-group row">
	                    <div class="col-md-4">
	                    	<label for="exampleInputEmail1">Import File</label>
	                        <p class="help-block">Select the file to import campaign from</p>
	                    </div>
	                    <div class="col-md-8">
	                        <div class="fileinput fileinput-new " data-provides="fileinput">
	                          	 <span class="btn input_file_select btn-file">
	                          		<span class="fileinput-new">Select file</span>
	                          		<span class="fileinput-exists">Change</span>
	                          		<input type="file" name="importfile">
	                          	 </span>
	                          	<span class="fileinput-filename"></span>
	                          	<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
	                        </div>
	                    </div>
                  	</div>
	            </div>
	        </div>

	    </form>
    </div>
</div>
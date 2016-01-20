<?php
global $wpdb;
if(isset($_POST['new'])) {
	if(!empty($_POST['name'])) {
		$include_field = '0';
	    $enable_video = '0';
	    $popupdetails = '';
	    
	    
        $unique = randomPassword();
        $post_info = array(
            'post_status' => 'publish', 
            'post_type' => 'socialfunnel',
            'post_name' => $_POST['name'],
            'post_title' => $_POST['name']
        );
        $new_postid = wp_insert_post($post_info);
        $slug = get_post($new_postid); 
        $slug = $slug->post_name;
        $camp_table = $wpdb->prefix."campaigns";
        $insert = array(
                    "unique_id" => $unique,
                    "post_id" => $new_postid,
                    "campaign_name" => $_POST['name'],
                    "campaign_slug" => $slug,
                    "autoresponder" => $_POST['autoresponder'],
                    "autoresponder_code" => $_POST['autoresponder_code'],
                    "include_field" =>  $include_field,
                    "fb_retarget_pixel" => $_POST['fb_retarget_pixel'],
                    "source_url" => urldecode($_GET['url']),
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
            ?>
            <script>
           	 open('<?php echo site_url() ?>/wp-admin/admin.php?page=social-funnel&sf=add-compaign&edit-id=<?echo $camp_id ?>','social-funnel');
           	</script>
            <p style="Font-family:Arial; font-size:14px; color:#9e9e9e;">If you see this more than 5 seconds, <a href="<?php echo site_url() ?>/wp-admin/admin.php?page=social-funnel&sf=add-compaign&edit-id=<?echo $camp_id ?>" style="Font-family:Arial; font-size:14px; color:#9e9e9e; font-weight:bold; text-decoration:underline;">click here</a></p>
            <?   
            exit;
        }   
	}
}

if(isset($_POST['clone'])) {
	
	$tmp = explode("-",$_POST['id']);
	
	$post_id = $tmp[0];
	$campaign_id = $tmp[1];


 
	$post = get_post( $post_id );

	$current_user = wp_get_current_user();
	$new_post_author = $current_user->ID;
 
	if (isset( $post ) && $post != null) {
 
		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $_POST['name'],
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'publish',
			'post_title'     => $_POST['name'],
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order
		);
 
		$new_post_id = wp_insert_post( $args );
 
		$taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
		foreach ($taxonomies as $taxonomy) {
			$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
			wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
		}
 
		$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
		if (count($post_meta_infos)!=0) {
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			foreach ($post_meta_infos as $meta_info) {
				$meta_key = $meta_info->meta_key;
				$meta_value = addslashes($meta_info->meta_value);
				$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}
			$sql_query.= implode(" UNION ALL ", $sql_query_sel);
			$wpdb->query($sql_query);
		}
		$camp_tab = $wpdb->prefix.'campaigns';
	    $campaigns = $wpdb->get_results(" SELECT * FROM $camp_tab WHERE $camp_tab.id = {$campaign_id}"); 
	    $campaign = $campaigns[0];
	    
	    $unique = randomPassword();	
	    
	    $slug = get_post($new_post_id); 
        $slug = $slug->post_name;
	    
		$camp_table = $wpdb->prefix."campaigns";
        $insert = array(
                    "unique_id" => $unique,
                    "post_id" => $new_post_id,
                    "campaign_name" => $_POST['name'],
                    "campaign_slug" => $slug,
                    "autoresponder" => $campaign->autoresponder,
                    "autoresponder_code" => $campaign->autoresponder_code,
                    "include_field" =>  $campaign->include_field,
                    "fb_retarget_pixel" => $campaign->fb_retarget_pixel,
                    "source_url" => urldecode($_GET['url']),
                    "popup_style" => $campaign->popup_style,
                    "delay" => $campaign->delay,
                    "closable" =>  $campaign->closable,
                    "popup_details" => $campaign->popup_details,
                    "content_headline" => $campaign->content_headline,
                    "enable_video" => $campaign->enable_video,
                    "video_embed_code" => $campaign->video_embed_code,
                    "content_subheadline" => $campaign->content_subheadline,
                    "support_email" => $campaign->support_email,
                    "gift_url" => $campaign->gift_url,
                    "unlock_after" => $campaign->unlock_after,
                    "button_text" => $campaign->button_text,
                    "page_title" => $campaign->page_title,
                    "feature_title" => $campaign->feature_title,
                    "feature_text" => $campaign->feature_text,
                    "feature_image" => $campaign->feature_image,
                    "feature_image_align" => $campaign->feature_imagealign
            );
	
	    $query_insert = $wpdb->insert($camp_table,$insert);
	    
	    $camp_id = $wpdb->insert_id;
        $social_clicks = $wpdb->prefix."clicks";
        $wpdb->query(" INSERT INTO $social_clicks (camp_id,slug) VALUES ($camp_id, '$slug') ");
		?>
        <script>
       	 open('<?php echo site_url() ?>/wp-admin/admin.php?page=social-funnel&sf=add-compaign&edit-id=<?echo $camp_id ?>','social-funnel');
       	</script>
        <p style="Font-family:Arial; font-size:14px; color:#9e9e9e;">If you see this more than 5 seconds, <a href="<?php echo site_url() ?>/wp-admin/admin.php?page=social-funnel&sf=add-compaign&edit-id=<?echo $camp_id ?>" style="Font-family:Arial; font-size:14px; color:#9e9e9e; font-weight:bold; text-decoration:underline;">click here</a></p>
        <?   
        exit;
	} else {
		wp_die('Post creation failed, could not find original post: ' . $post_id);
	}
 
}
?><html>
	<head>
		<title>Social Funnel</title>
	</head>
	<body>
		<div id="container">
			<h1>Create campaign</h1>
			<div id="main_view">
				<div class="new_c">Create a new campaign</div>
				<span></span>
				<div class="cp">Create a lookalike campaign</div>
			</div>
			<div id="c_new">
				<form action="" method="post">	
					<h2>Campaign Name</h2>

					<p>Give your campaign a name</p>
					<input type="text" name="name"/>
					<center>
						<input type="submit" name="new" value="CREATE"/>
					</center>
				</form>
			</div>
			
			<div id="clone">
				<form action="" method="post">	
					<h2>Campaign Name</h2>

					<p>Give your campaign a name</p>
					<input type="text" name="name"/>
					
					<hr/>
					<h2>Choose a Lookalike Campaign</h2>

					<p>Choose a campaign that you want to copy. All the settings will be kept, except for the source URL</p>
					<select name="id">
						<?php
						$camp_tab = $wpdb->prefix.'campaigns';
	                    $user_click = $wpdb->prefix.'clicks';
	                    $campaigns = $wpdb->get_results(" SELECT $camp_tab.*, $user_click.clicks, $user_click.optins, $user_click.socail_clicks, $user_click.socail_optins FROM $camp_tab LEFT JOIN $user_click ON $camp_tab.id = $user_click.camp_id "); 
		                if($campaigns){
		                	foreach ($campaigns as $campaign) {
			                	?>
								<option value="<?php echo $campaign->post_id ?>-<?php echo $campaign->id ?>"><?php echo stripcslashes($campaign->campaign_name);  ?></option>
								<?
							}
						}
						?>
					</select>
					
					<center>
						<input type="submit" name="clone" value="CREATE"/>
					</center>
				</form>
			</div>
		</div>
		<style>
			body {
				padding: 0;
				margin: 0;
				font-family: 'Myriad Pro', Arial;
			}
			#container {
				width: 100%;
			}
			h1 {
				background: #023666;
				color:#fff;
				font-size: 18px;
				font-family: 'Myriad Pro', Arial;
				padding: 26px 21px;
				margin: 0;
				font-weight: normal;
			}
			#main_view div {
				margin:38px 34px 0px;
				width:150px;
				padding-top:116px;
				text-align: center;
				cursor: pointer;
				font-size: 14px;
				color:#7b7b7b;
				float:left;
			}
			#main_view span {
				background: #f6f6f6;
				width:2px;
				margin-top:30px;
				height:154px;
				float:left;
				margin-left:10px;
				display: block;
			}
			.new_c {
				background:url('<?php echo SF_URL ?>assets/images/sf_box.jpg') no-repeat top center;
			}
			.new_c:hover {
				background:url('<?php echo SF_URL ?>assets/images/sf_box_hover.jpg') no-repeat top center;
			}
			.cp {
				background:url('<?php echo SF_URL ?>assets/images/sf_looking_glass.jpg') no-repeat top center;
				width:180px !important;
			}
			.cp:hover {
				background:url('<?php echo SF_URL ?>assets/images/sf_looking_glass_hover.jpg') no-repeat top center;
			}
			#c_new, #clone {
				padding:0 20px;
				display: none;
			}
			h2 {
				font-size: 14px;
				color:#1d1d1d;
				margin: 0;
				padding: 0;
				margin-top: 22px;
				margin-bottom: 20px;
				
			}
			p {
				font-size: 14px;
				color:#8e8e8e;
				margin:0;
				padding: 0;
				margin-bottom: 23px;
			}
			input[type='text'] {
				border:1px solid #dbdbdb;
				border-radius: 5px;
				height:39px;
				padding:0 3%;
				width: 94%;
				margin-bottom: 25px;
			}
			input[type="submit"] {
				margin:auto;
				font-size: 14px;
				color:#fff;
				background: #c5c5c5;
				padding:11px 23px;
				border-radius: 5px;
				border:1px solid #c5c5c5;
				margin-bottom: 15px;
			}
			input[type="submit"]:hover {
				background: #5cb553;
				border:1px solid #5cb553;
			}
			select {
				border:1px solid #dbdbdb;
				border-radius: 5px;
				height:39px;
				padding:0 3%;
				width: 94%;
				margin-bottom: 25px;
			}
			hr {
				color: #f6f6f6;
				background-color: #f6f6f6;
			}
		</style>
		<script src="<?php echo SF_URL ?>assets/js/jquery.min.js"></script>
		<script>
			$(".new_c").click(function(){
				$("#main_view").hide();
				$("#c_new").show();
			});
			$(".cp").click(function(){
				$("#main_view").hide();
				$("#clone").show();
			});
		</script>
	</body>
</html>
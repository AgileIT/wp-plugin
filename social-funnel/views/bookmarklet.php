<div class="dashboard_main_wrapper">
	<div class="dashb_in_wrap">
        <form method="post" enctype="multipart/form-data">
	        
	        <header class="dashboard_header">
	        	<div class="row">
	            	<div class="col-md-4 col-sm-4"><h2 class="dash_title">Social Funnels</h2></div>
	                <div class="col-md-8 col-sm-8 text-right">
	                	<a href="#" class="dash_h_green_btn sc_hidden sc_tut_button">Tutorial</a>
	                    <a class="dash_h_green_btn" href="?page=social-funnel">Back</a>
	                </div>
	            </div>
	        </header>
	        <? 
	        global $videos;
	        $v_id = "bookmarklet";
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
	            	<h3>Bookmarklet</h3>
	                <p>Here you will be able to grab a bookmarklet that will allow you to create campaigns in 1 click...</p>
	            </header>
	            <div class="dash_inner_form_wrap">
	                <div class="form-group row">
	                    <div class="col-md-4">
	                    	<label for="exampleInputEmail1">Bookmarklet</label>
	                        <p class="help-block">Drag the button to the bookmarks bar of your browser</p>
	                    </div>
	                    <div class="col-md-8"><a href="javascript:void(open('<?php echo site_url() ?>/wp-admin/admin.php?page=social-funnel&sf=bookmarklet_action&url='+encodeURIComponent(location.href),'social-funnel','height=500,width=478'))" onclick="return false;" style="border:1px solid #dbdbdb; padding:16px 42px; color:#1d1d1d; cursor:move;display:inline-block; margin-top:2px; padding-top:18px;"><span>SOCIAL FUNNELS</span></a></div>
	                </div>
	                
	                <div class="form-group row">
	                    <div class="col-md-4">
	                    	<label for="exampleInputEmail1">Bookmark code</label>
	                        <p class="help-block">If you can’t drag the bookmarklet to your bookmarks, copy the following code and create a new bookmark. Paste the code into the new bookmark’s URL field.</p>
	                    </div>
	                    <div class="col-md-8"><textarea class="form-control">javascript:void(open('<?php echo site_url() ?>/wp-admin/admin.php?page=social-funnel&sf=bookmarklet_action&url='+encodeURIComponent(location.href),'social-funnel','height=500,width=478'))</textarea></div>
	                </div>
	                
                  	
	            </div>
	        </div>

	    </form>
    </div>
</div>
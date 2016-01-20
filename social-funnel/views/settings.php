<?php 
if(isset($_POST['activate'])){
	if(isset($_POST['key'])) {
		if(sf_validate_license($_POST['key'])) {
			//key
			update_option("sf_license", $_POST['key']);
			wp_redirect("?page=social-funnel&sfaction=ssaved");
			exit;
		
} 
}
}
?>
<div class="dashboard_main_wrapper">
	<div class="dashb_in_wrap">
        <form method="post" enctype="multipart/form-data">
	        
	        <header class="dashboard_header">
	        	<div class="row">
	            	<div class="col-md-4 col-sm-4"><h2 class="dash_title">Activate Social Funnels</h2></div>
	                <div class="col-md-8 col-sm-8 text-right">
	                    <button class="dash_h_green_btn finish" type="submit" name="activate">Activate</button>
	                </div>
	            </div>
	        </header>

	        <div class="dash_b_content_wrap">
	        	<header class="dash_cont_header">
	            	<h3>Activation</h3>
	                <p>In order to start using Social Funnels you need to activate it with the license key...</p>
	            </header>
	            <div class="dash_inner_form_wrap">
	            	<?php if(isset($_GET['sfaction'])) {
		            	if($_GET['sfaction'] == "sfal") {
		            	?>
	            	
						<
	                        </div>
						</div>
						<?php
						}
					} ?>
	                <div class="form-group row">
	                    <div class="col-md-4">
	                    	<label for="exampleInputEmail1">Your Name</label>
	                        <p class="help-block">Enter your full name (name and last name)</p>
	                    </div>
	                    <div class="col-md-8"><input type="text" class="form-control" name="name"></div>
	                </div>
	                
	                <div class="form-group row">
	                    <div class="col-md-4">
	                    	<label for="exampleInputEmail1">Your Email</label>
	                        <p class="help-block">Enter your email address you’ve used during the checkout</p>
	                    </div>
	                    <div class="col-md-8"><input type="text" class="form-control" name="email"></div>
	                </div>
	                
	                <div class="form-group row">
	                    <div class="col-md-4">
	                    	<label for="exampleInputEmail1">License Key</label>
	                        <p class="help-block">Enter your license key. You can find your license key at http://www.social-funnels.com/members</p>
	                    </div>
	                    <div class="col-md-8"><input type="text" class="form-control" name="key"></div>
	                </div>
                  	
	            </div>
	        </div>

	    </form>
    </div>
</div>
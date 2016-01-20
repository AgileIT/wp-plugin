<style type="text/css">
    #adminmenuback{ z-index:2 !important;}
    .toplevel_page_social-funnel {
  background: none repeat scroll 0 0 #f1f1f1;
}
</style>
<div class="dashboard_main_wrapper">
    <div class="dashb_in_wrap">
        <header class="dashboard_header">
        	<div class="row">
            	<div class="col-md-3 col-sm-3"><h2 class="dash_title">Social Funnels</h2></div>
                <div class="col-md-9 col-sm-9 text-right">
                	<a href="<?php echo add_query_arg(array('sf' => 'bookmarklet'), get_permalink()); ?>" class="dash_h_green_btn">Bookmarklet</a>
                	<a href="#" class="dash_h_green_btn sc_hidden sc_tut_button">Tutorial</a>
                    <a href="<?php echo add_query_arg(array('sf' => 'import'), get_permalink()); ?>" class="dash_h_green_btn">Import</a>
                    <a href="<?php echo add_query_arg(array('sf' => 'add-compaign'), get_permalink()); ?>" class="dash_h_green_btn">New Campaign</a>
                </div>
            </div>
        </header>
        <? 
        global $videos;
        $v_id = "dash";
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
            	<h3>Dashboard</h3>
                <p>Here you can see all your campaigns, and you can create a new campaign...</p>
            </header>
            
            <div class="dash_camp_list_wrap">
                <div id="messages">
                    <?php if(isset($_GET['sfaction'])){ 
                        if($_GET['sfaction'] == 'save'){ ?>
                            <br />
                            <div class="alert campaign_successfully_created">
                                <a data-dismiss="alert" class="close" href="#"><span class="fa fa-close"></span></a>
                                Campaign successfully created!
                            </div>
                    <?php }elseif($_GET['sfaction'] == 'error'){ ?>
                        <br />
                        <div class="alert campaign_successfully_failed">
                            <a data-dismiss="alert" class="close" href="#"><span class="fa fa-close"></span></a>
                            Campaign creation failed!
                        </div>
                    <?php }elseif($_GET['sfaction'] == 'update'){ ?>
                        <br />
                        <div class="alert campaign_successfully_created">
                            <a data-dismiss="alert" class="close" href="#"><span class="fa fa-close"></span></a>
                            Campaign successfully updated!
                        </div>
                    <?php }elseif($_GET['sfaction'] == 'import'){ ?>
                        <br />
                        <div class="alert campaign_successfully_imported">
                            <a data-dismiss="alert" class="close" href="#"><span class="fa fa-close"></span></a>
                            Campaign successfully imported!
                        </div>
                    <?php }elseif($_GET['sfaction'] == 'importfail'){ ?>
                        <br />
                        <div class="alert campaign_successfully_failed">
                            <a data-dismiss="alert" class="close" href="#"><span class="fa fa-close"></span></a>
                            Campaign import failed!
                        </div>
                        <?php } elseif($_GET['sfaction'] == 'ssaved'){ ?>
                        <br />
                        <div class="alert campaign_successfully_imported">
                            <a data-dismiss="alert" class="close" href="#"><span class="fa fa-close"></span></a>
                            Settings successfully saved!
                        </div>
                    <?php }
                    } ?>
                </div>
                <?php global $wpdb; 
                    $camp_tab = $wpdb->prefix.'campaigns';
                    $user_click = $wpdb->prefix.'clicks';
                    $campaigns = $wpdb->get_results(" SELECT $camp_tab.*, $user_click.clicks, $user_click.optins, $user_click.socail_clicks, $user_click.socail_optins FROM $camp_tab LEFT JOIN $user_click ON $camp_tab.id = $user_click.camp_id "); 
                if($campaigns){ ?>
                    <div class="campain_list_wrap">
                    	<table class="table table-responsive" id="campaign_table">
                            <thead>
                                <tr>
                                    <th align="left" style="text-align:left;">Name</th>
                                    <th align="center">Clicks</th>
                                    <th align="center">Optins</th>
                                    <th align="center">Social Clicks</th>
                                    <th align="center">Social Optins</th>
                                </tr>
                            </thead>
                          	<tbody>
                                <?php
                                foreach ($campaigns as $campaign) { ?>
                                    <tr>
                                    	<td class="campain_title">
                                        	<h4>
                                                <a href="<?php echo add_query_arg(array('sf' => 'add-compaign','edit-id'=>$campaign->id), get_permalink()); ?>"><?php echo stripcslashes($campaign->campaign_name);  ?></a>
                                            </h4>
                                            <a href="<?php echo add_query_arg(array('sf' => 'add-compaign','edit-id'=>$campaign->id), get_permalink()); ?>">Edit</a> |
                                            <a href="#" data-slug="<?php echo $campaign->campaign_slug;  ?>" class="linkme" data-unique="<?php echo $campaign->unique_id; ?>" data-toggle="modal" data-target="#campain_links" >Link</a> |
                                            <a href="#" data-id="<?php echo $campaign->id;?>" class="export" data-slug="<?php echo $campaign->campaign_slug; ?>">Export</a> |
                                            <a href="#" data-toggle="modal" data-target="#campain_delete" data-delid="<?php echo $campaign->id; ?>" class="deleteme">Delete</a> |
                                            <a href="#" class="copyme" data-copyid="<?php echo $campaign->id; ?>">Copy</a> |
                                            <a href="#" data-toggle="modal" data-target="#campain_share" data-slug="<?php echo $campaign->campaign_slug; ?>" class="share">Share</a>
                                        </td>
                                        <td align="center"><?php echo $campaign->clicks; ?></td>
                                        <td align="center"><?php echo $campaign->optins; ?></td>
                                        <td align="center"><?php echo $campaign->socail_clicks; ?></td>
                                        <td align="center"><?php echo $campaign->socail_optins; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php }else{ ?>
                    <p class="text-center">You haven't created any campaigns yet... Create one by clicking the "New Campaign" button at the top<!-- You haven't created any campaigns yet... <a href="<?php echo add_query_arg(array('sf' => 'add-compaign'), get_permalink()); ?>">Create one</a> --></p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var exporturl = "<?php echo SF_URL; ?>export.php";
    var siteurl = "<?php echo get_site_url();  ?>";
</script>

<!--  Link Modal  -->
<div class="modal fade" id="campain_links" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog campin_light_box">
        <div class="modal-content dash_campain_lightbox_wrap">
            <div class="modal-header">
                <div class="campign_ligh_icon campain_link_icon"><span class="fa fa-link"></span></div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="fa fa-close"></span></button>
                <h4 class="modal-title" id="myModalLabel">Campaign Link</h4>
            </div>
            <div class="modal-body">
                <div class="dash_campain_link_form">
                    <form>
                        <div class="form-group row">
                            <div class="col-md-3"><label>Capture Page:</label></div>
                            <div class="col-md-9"><div onclick="select_all(this)" class="form-control"><?php echo get_site_url(); ?>/<span class="slugname"></span></div></div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3"><label>Content Page:</label></div>
                            <div class="col-md-9"><span class="form-control" onclick="select_all(this)"><?php echo get_site_url(); ?>/<span class="slugname"></span>?i=<span class="uniqueid"></span></span></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  Delete modal  -->
<div class="modal fade" id="campain_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog campin_light_box">
        <div class="modal-content dash_campain_lightbox_wrap">
            <div class="modal-header">
                <div class="campign_ligh_icon campain_delete_icon"><span class="fa fa-exclamation-triangle"></span></div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="fa fa-close"></span></button>
                <h4 class="modal-title" id="myModalLabel">Delete Campaign</h4>
            </div>
            <div class="modal-body">

                <div class="dash_campain_delete">
                    <p>Are you sure you want to delete the campaign?</p>
                    <form>
                        <input type="hidden" id="deleteid" />
                        <button class="btn dash_del_btn" id="deleteconfirm">Yes</button>
                        <button class="btn dash_del_btn" id="deletecancel">No</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  Share modal -->
<div class="modal fade" id="campain_share" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog campin_light_box">
        <div class="modal-content dash_campain_lightbox_wrap">
            <div class="modal-header">
                <div class="campign_ligh_icon campain_share_icon"><span class="fa fa fa-sign-out"></span></div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="fa fa-close"></span></button>
                <h4 class="modal-title" id="myModalLabel">Share Campaign</h4>
            </div>
            <div class="modal-body">

                <div class="dash_campain_share">
                    <ul>
                        <li><a class="facebook_icon_1" href="" target="_blank"><span class="fa fa-facebook"></span></a></li>
                        <li><a class="twitter_icon_1" href="" target="_blank"><span class="fa fa-twitter"></span></a></li>
                        <li><a class="google_plus_icon_1" href="" target="_blank"><span class="fa fa-google-plus"></span></a></li>
                        <li><a class="linkedin_icon_1" href="" target="_blank"><span class="fa fa-linkedin"></span></a></li>
                        <li><a class="pinterest_icon_1" href="" target="_blank"><span class="fa fa-pinterest"></span></a></li>
                        <li><a class="stumbleupon_icon_1" href="" target="_blank"><span class="fa fa-stumbleupon"></span></a></li>
                    </ul>
                </div>

            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    function select_all(el) {
        if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
            var range = document.createRange();
            range.selectNodeContents(el);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        } else if (typeof document.selection != "undefined" && typeof document.body.createTextRange != "undefined") {
            var textRange = document.body.createTextRange();
            textRange.moveToElementText(el);
            textRange.select();
        }
    }
</script>

<!-- <script type="text/javascript" src="//connect.facebook.net/en_US/sdk.js"></script> -->
<div id="fb-root"></div>
<script>
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3&appId=145634995501895";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
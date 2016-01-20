 <?php
    if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
    

    function sf_delete_plugin_table()
    {
    	global $wpdb;

    	$table_name = array(
    			   $wpdb->prefix .'campaigns',
                   $wpdb->prefix .'clicks',
                   $wpdb->prefix .'optins',
    		);

    	foreach($table_name as $tab)
    	{
    		$wpdb->query( "DROP TABLE IF EXISTS $tab ");	
    	}
	    
	    delete_option("my_plugin_db_version");
    }

    sf_delete_plugin_table();


?>
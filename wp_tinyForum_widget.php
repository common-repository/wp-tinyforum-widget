<?php
/*
Plugin Name: WP tinyForum Widget
Plugin URI: http://www.7mediaws.org/extend/plugins/wp-tinyForum-widget/
Description: A sidebar widget for your WordPress site that will show a certain number of topics from your tinyForum installation.
Author: Joshua Parker
Version: 1.1
Author URI: http://www.joshparker.us/
*/

add_action( 'admin_init', 'register_wp_tinyForum_widget_settings' );
add_action( 'admin_menu', 'wp_tinyForum_menu' );

function register_wp_tinyForum_widget_settings() {
	//register our settings
	register_setting( 'wp-tinyForum-widget-settings-group', 'wp_tinyForum_host' );
	register_setting( 'wp-tinyForum-widget-settings-group', 'wp_tinyForum_user' );
	register_setting( 'wp-tinyForum-widget-settings-group', 'wp_tinyForum_pass' );
	register_setting( 'wp-tinyForum-widget-settings-group', 'wp_tinyForum_database' );
	register_setting( 'wp-tinyForum-widget-settings-group', 'wp_tinyForum_topics_table' );
	register_setting( 'wp-tinyForum-widget-settings-group', 'wp_tinyForum_no_of_topics' );
	register_setting( 'wp-tinyForum-widget-settings-group', 'wp_tinyForum_url' );
}

function wp_tinyForum_menu() {

	//create options menu
	add_options_page('WP tinyForum Widget', 'WP tinyForum Widget', 'manage_options', 'wp-tinyForum-widget-settings', 'wp_tinyForum_widget_settings_page');

}

function set_wp_tinyForum_options() {
	add_option('wp_tinyForum_host','','tinyForum Database Host');
	add_option('wp_tinyForum_user','','tinyForum Database User');
	add_option('wp_tinyForum_pass','','tinyForum Database Password');
	add_option('wp_tinyForum_database','','tinyForum Database Name');
	add_option('wp_tinyForum_topics_table','','Topics Table');
	add_option('wp_tinyForum_no_of_topics','','Number of topics');
	add_option('wp_tinyForum_url','','URL to Forum');
}

function wp_tinyForum_widget_settings_page() {
?>
<div class="wrap">
<h2><?php _e('WP tinyForum Recent Forum Topics','wp-tinyforum-widget'); ?></h2>

<form method="post" action="options.php">
    <?php settings_fields( 'wp-tinyForum-widget-settings-group' ); ?>
    <table class="form-table">
    	<tr valign="top">
        <th scope="row"><?php _e('tinyForum Database Host (usually localhost)','wp-tinyforum-widget'); ?></th>
        <td><input type="text" name="wp_tinyForum_host" size="50" value="<?php echo get_option('wp_tinyForum_host'); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e('tinyForum Database Username','wp-tinyforum-widget'); ?></th>
        <td><input type="text" name="wp_tinyForum_user" size="50" value="<?php echo get_option('wp_tinyForum_user'); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e('tinyForum Database Password','wp-tinyforum-widget'); ?></th>
        <td><input type="password" name="wp_tinyForum_pass" size="50" value="<?php echo get_option('wp_tinyForum_pass'); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e('tinyForum Database Name','wp-tinyforum-widget'); ?></th>
        <td><input type="text" name="wp_tinyForum_database" size="50" value="<?php echo get_option('wp_tinyForum_database'); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e('tinyForum Topics Table','wp-tinyforum-widget'); ?></th>
        <td><input type="text" name="wp_tinyForum_topics_table" size="50" value="<?php echo get_option('wp_tinyForum_topics_table'); ?>" /></td>
        </tr>
             
        <tr valign="top">
        <th scope="row"><?php _e('Number of topics to show','wp-tinyforum-widget'); ?></th>
        <td><input type="text" name="wp_tinyForum_no_of_topics" size="50" value="<?php echo get_option('wp_tinyForum_no_of_topics'); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e('URL to Forum (including trailing slash)','wp-tinyforum-widget'); ?></th>
        <td><input type="text" name="wp_tinyForum_url" size="50" value="<?php echo get_option('wp_tinyForum_url'); ?>" /></td>
        </tr>

    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes','wp-tinyforum-widget') ?>" />
    </p>

</form>
</div>
<?php
}

function widget_wp_tinyForum_init() {
	if ( !function_exists('wp_register_sidebar_widget') )
		return;

function widget_wp_tinyForum() {
	$link = mysqli_connect(get_option('wp_tinyForum_host'), get_option('wp_tinyForum_user'), get_option('wp_tinyForum_pass'), get_option('wp_tinyForum_database'));
	$topics = get_option('wp_tinyForum_topics_table');
	
	$q = mysqli_query( $link, "SELECT * FROM $topics ORDER BY topic_date DESC LIMIT " . get_option('wp_tinyForum_no_of_topics') );

?>
<style type="text/css">
		#latestTopics
		{
			margin-left: 0;
			padding-left: 0;
			list-style: none;
		}
		
		#latestTopics li		
		{
			padding-left: 1.8em;
			background-image: url(<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-tinyforum-widget/images/topic.png);
			background-repeat: no-repeat;
			background-position: 0 .3em;
		}
</style>
<div id="widget_wptinyForum" class="widget widget_wp_tinyForum"><h3 class="widget-title"><span><?php _e('Recent Forum Topics','wp-tinyforum-widget'); ?></span></h3>
	<ul id="latestTopics">
		<?php if(mysqli_num_rows($q) > 0) {
				while($row = mysqli_fetch_assoc($q)) {
					echo '<li><a href="'.get_option('wp_tinyforum_url').'index/topic/'.$row['topic_id'].'" title="'.$row['topic_subject'].'">'.$row['topic_subject'].'</a></li>';
				}
			} else {
				echo '<li>There are currently no topics</li>';
			}
		?>
	</ul>
</div>

<?php
	}	
	wp_register_sidebar_widget('widget_wp_tinyForum_init_2012', 'WP tinyForum Topics', 'widget_wp_tinyForum', array('description' => 'Adds a sidebar widget showing a set number of topics from your tinyForum installation.'));
}
add_action('plugins_loaded', 'widget_wp_tinyForum_init');

// i18n
$plugin_dir = basename(dirname(__FILE__)). '/languages';
load_plugin_textdomain( 'wp-tinyforum-widget', WP_PLUGIN_DIR.'/'.$plugin_dir, $plugin_dir );
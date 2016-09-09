<?php
// $Id$

/**
 * @file template.php
 */
$tmpl_path = drupal_get_path('theme', 'stunningpress');
//drupal_add_js($tmpl_path.'/js/shCore.js','theme');
//drupal_add_css($tmpl_path.'/css/shCore.css','theme');


/**
 * Initialize theme settings
 */
if (is_null(theme_get_setting('user_notverified_display')) || theme_get_setting('rebuild_registry')) {
	
	// Auto-rebuild the theme registry during theme development.
	if(theme_get_setting('rebuild_registry')) {
		drupal_set_message(t('The theme registry has been rebuilt. <a href="!link">Turn off</a> this feature on production websites.', array('!link' => url('admin/build/themes/settings/' . $GLOBALS['theme']))), 'warning');
	}
	
	global $theme_key;
  
	/**
   * The default values for the theme variables.
   * Matches with $defaults in the theme-setting.php file.
   */
	$defaults = array(
    'google_analytics_id'				=> '',
		'rss_link'									=> '',
		'rss_text'									=> 'Subscribe to our RSS feed!',
		'twitter_username'					=> '',
		'twitter_text'							=> 'Follow me on Twitter!',
		'video_code'								=> '',
		'header_ads_code_468x60'		=> '',
		'sidebar_top_ads'						=> '',
		'sidebar_bt_ads'						=> '',
	);
	
	$defaults = array_merge($defaults, theme_get_settings());
	// Get default theme settings.
	$settings = theme_get_settings($theme_key);
	
	// Save default theme settings
	variable_set(
		str_replace('/', '_', 'theme_'. $theme_key .'_settings'),
		array_merge($defaults, $settings)
	);
	// Force refresh of Drupal internals
  	theme_get_setting('', TRUE);
}

/**
 * Override or insert PHPTemplate variables into the templates.
 */
function phptemplate_preprocess_page(&$vars) {
	$vars['theme_path'] = path_to_theme().'/';
	$vars['theme_basepath'] = base_path().path_to_theme().'/';
	$vars['tabs2'] = menu_secondary_local_tasks();
	
	$vars['rss_link'] = theme_get_setting('rss_link');
	$vars['rss_text'] = theme_get_setting('rss_text');
	$vars['twitter_username'] = theme_get_setting('twitter_username');
	$vars['twitter_text'] = theme_get_setting('twitter_text');
	$vars['video_code'] = theme_get_setting('video_code');
	
	$vars['header_ads_code_468x60'] = theme_get_setting('header_ads_code_468x60');
	$vars['sidebar_top_ads'] = theme_get_setting('sidebar_top_ads');
	$vars['sidebar_bt_ads'] = theme_get_setting('sidebar_bt_ads');
	
	if (theme_get_setting('google_analytics_id')) {
		$vars['closure'] .= "\n".'<script type="text/javascript">
			var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
			document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
			</script>
			<script type="text/javascript">
			try {
			var pageTracker = _gat._getTracker("'.theme_get_setting('google_analytics_id').'");
			pageTracker._trackPageview();
			} catch(err) {}</script>';
		}
 
	}


/**
 * Override or insert PHPTemplate variables into the templates.
 */
function phptemplate_preprocess_node(&$vars) {
	$vars['theme_path'] = path_to_theme().'/';
	$vars['theme_basepath'] = base_path().path_to_theme().'/';
	$vars['node_content_block'] = theme('blocks', 'node_content_block');
}


/**
 * Generates IE Fix
 */
function phptemplate_get_ie_fix() {
	$iefix = '<!--[if IE]><link rel="stylesheet" href="'. base_path() . path_to_theme() .'/css/ie.css" type="text/css" media="screen, projection"><![endif]-->';
	$iefix .= '<!--[if lte IE 6]>';
	$iefix .= '<script type="text/javascript" src="'. base_path() . path_to_theme() .'/js/DD_belatedPNG.js"></script>';
	$iefix .= '<script type="text/javascript" src="'. base_path() . path_to_theme() .'/js/styleie6.js"></script>';
	$iefix .= '<![endif]-->';
	return $iefix;
}
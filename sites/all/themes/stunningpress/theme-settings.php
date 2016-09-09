<?php
// $Id$

/**
* Implementation of THEMEHOOK_settings() function.
*
* @param $saved_settings
*   array An array of saved settings for this theme.
* @return
*   array A form array.
*/
function phptemplate_settings($saved_settings) {
	/**
   * The default values for the theme variables.
   * Matches with $defaults in the template.php file.
   */
		$defaults = array(
    'google_analytics_id'			=> '',
		'rss_link'									=> '',
		'rss_text'									=> 'Subscribe to our RSS feed!',
		'twitter_username'					=> '',
		'twitter_text'							=> 'Follow me on Twitter!',
		'video_code'								=> '',
		'header_ads_code_468x60'		=> '',
		'sidebar_top_ads'						=> '',
		'sidebar_bt_ads'						=> '',
	);
	
	// Merge the saved variables and their default values
	$settings = array_merge($defaults, $saved_settings);
	// Create theme settings form widgets using Forms API
	// General Settings
	$form['ab_general_settings'] = array(
		'#type' => 'fieldset',
		'#title' => t('General settings'),
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,
		'#attributes' => array('class' => 'ab_general_settings'),
	);
		// Google analytics ID
		$form['ab_general_settings']['google_analytics_id'] = array(
		  '#type'          => 'textfield',
		  '#title'         => t('Google analytics ID'),
		  '#default_value' => $settings['google_analytics_id'],
		  '#description'   => t('Insert Google analytics ID'),
		);
		// RSS link
		$form['ab_general_settings']['rss_link'] = array(
		  '#type'          => 'textfield',
		  '#title'         => t('RSS link'),
		  '#default_value' => $settings['rss_link'],
		  '#description'   => t('Insert RSS link'),
		);
		// RSS text
		$form['ab_general_settings']['rss_text'] = array(
		  '#type'          => 'textfield',
		  '#title'         => t('RSS Text'),
		  '#default_value' => $settings['rss_text'],
		  '#description'   => t('Insert RSS text'),
		);
		// Twitter username
		$form['ab_general_settings']['twitter_username'] = array(
		  '#type'          => 'textfield',
		  '#title'         => t('Twitter username'),
		  '#default_value' => $settings['twitter_username'],
		  '#description'   => t('Insert Twitter Username'),
		);
		// Twitter text
		$form['ab_general_settings']['twitter_text'] = array(
		  '#type'          => 'textfield',
		  '#title'         => t('Twitter text'),
		  '#default_value' => $settings['twitter_text'],
		  '#description'   => t('Insert Twitter text'),
		);
	// Sidebar Feature Video Settings
	$form['ab_video_settings'] = array(
		'#type' => 'fieldset',
		'#title' => t('Sidebar Feature Video settings'),
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,
		'#attributes' => array('class' => 'ab_video_settings'),
	);
		// Header ads code
		$form['ab_video_settings']['video_code'] = array(
		  '#type'          => 'textarea',
		  '#title'         => t('Video embed code'),
		  '#default_value' => $settings['video_code'],
		  '#description'   => t('Insert video embed code (width: 290px, height ~ 220px)'),
		);
		
	// Advertise Settings
	$form['ab_ads_settings'] = array(
		'#type' => 'fieldset',
		'#title' => t('Advertise settings'),
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,
		'#attributes' => array('class' => 'ab_ads_settings'),
	);
		// Header ads code
		$form['ab_ads_settings']['header_ads_code_468x60'] = array(
		  '#type'          => 'textarea',
		  '#title'         => t('Header ads code'),
		  '#default_value' => $settings['header_ads_code_468x60'],
		  '#description'   => t('Insert ads code in header (468x60)'),
		);
		// Sidebar top ads code
		$form['ab_ads_settings']['sidebar_top_ads'] = array(
		  '#type'          => 'textarea',
		  '#title'         => t('Sidebar top ads code'),
		  '#default_value' => $settings['sidebar_top_ads'],
		  '#description'   => t('Insert ads code'),
		);
		// Sidebar bottom ads code
		$form['ab_ads_settings']['sidebar_bt_ads'] = array(
		  '#type'          => 'textarea',
		  '#title'         => t('Sidebar bottom ads code'),
		  '#default_value' => $settings['sidebar_bt_ads'],
		  '#description'   => t('Insert ads code'),
		);

	
	// Return theme settings form
	return $form;
}
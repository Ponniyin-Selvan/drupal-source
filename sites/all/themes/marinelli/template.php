<?php
//template for Marinelli Theme
//author: singalkuppe - www.signalkuppe.com




function marinelli_width($left, $right) {
  $width = "short";
  if (!$left ) {
    $width = "long";
  }  
  
   if (!$right) {
    $width = "long";
  }
  return $width;
}



/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return a string containing the breadcrumb output.
 */
function phptemplate_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb)) {
      $breadcrumb[] = drupal_get_title();
        array_shift($breadcrumb);
       return '<div class="path"><p><span>'.t('You are here').'</span>'. implode(' / ', $breadcrumb) .'</p></div>';
  }
  }

function phptemplate_preprocess_page(&$vars) {
   $current_language = $vars['language']->language ;
   foreach ( array('menu_primary_links_source', 'primary_links', 'secondary_links') as $menu_name ) {
       if (! empty($vars[$menu_name])) {
          //print_r($vars[$menu_name]);
          foreach ( $vars[$menu_name] as $menu_key => $menu_item ) {
    	      if ( $menu_item['langcode'] != $current_language && ! empty($menu_item['langcode']) ) {
    	          unset($vars[$menu_name][$menu_key]) ;
    	      }
          }
        }
   }
}

//overrides taxonomy term page function
function marinelli_taxonomy_term_page($tids, $result) {
  drupal_add_css(drupal_get_path('module', 'taxonomy') .'/taxonomy.css');

  $output = '';

  // Only display the description if we have a single term, to avoid clutter and confusion.
  if (count($tids) == 1) {
    $term = taxonomy_get_term($tids[0]);
    $description = $term->description;

    // Check that a description is set.
    if (!empty($description)) {
      $output .= '<div class="terminfo"><p>';
      $output .= filter_xss_admin($description);
      $output .= '</p></div>';
    }
  }

  $output .= taxonomy_render_nodes($result);

  return $output;
}





function marinelli_admin_page($blocks) {
  $stripe = 0;
  $container = array();

  foreach ($blocks as $block) {
    if ($block_output = theme('admin_block', $block)) {
      if (empty($block['position'])) {
        // perform automatic striping.
        $block['position'] = ++$stripe % 2 ? 'left' : 'right';
      }
      if (!isset($container[$block['position']])) {
        $container[$block['position']] = '';
      }
      $container[$block['position']] .= $block_output;
    }
  }

  $output = '<div class="admin clear-block">';
  $output .= '<div class="compact-link"><p>'; // use <p> for hide/show anchor
  if (system_admin_compact_mode()) {
    $output .= l(t('Show descriptions'), 'admin/compact/off', array('title' => t('Expand layout to include descriptions.')));
  }
  else {
    $output .= l(t('Hide descriptions'), 'admin/compact/on', array('title' => t('Compress layout by hiding descriptions.')));
  }
  $output .= '</p></div>';

  foreach ($container as $id => $data) {
    $output .= '<div class="'. $id .' clear-block">';
    $output .= $data;
    $output .= '</div>';
  }
  $output .= '</div>';
  return $output;
}

function marinelli_admin_block_content($content) {
  if (!$content) {
    return '';
  }

  if (system_admin_compact_mode()) {
    $output = '<dl class="menu">';
    foreach ($content as $item) {
      $output .= '<dt>'. l($item['title'], $item['href'], $item['localized_options']) .'</dt>'; // use definition list per compact mode
    }
    $output .= '</dl>';
  }
  else {
    $output = '<dl class="admin-list">';
    foreach ($content as $item) {
      $output .= '<dt>'. l($item['title'], $item['href'], $item['localized_options']) .'</dt>';
      $output .= '<dd>'. $item['description'] .'</dd>';
    }
    $output .= '</dl>';
  }
  return $output;
}

function marinelli_system_admin_by_module($menu_items) { // admin by module page
  $stripe = 0;
  $output = '';
  $container = array('left' => '', 'right' => '');
  $flip = array('left' => 'right', 'right' => 'left');
  $position = 'left';

  // Iterate over all modules
  foreach ($menu_items as $module => $block) {
    list($description, $items) = $block;

    // Output links
    if (count($items)) {
      $block = array();
      $block['title'] = $module;
      $block['content'] = theme('item_list', $items);
      $block['description'] = t($description);

      if ($block_output = theme('admin_block', $block)) {
        if (!isset($block['position'])) {
          // Perform automatic striping.
          $block['position'] = $position;
          $position = $flip[$position];
        }
        $container[$block['position']] .= $block_output;
      }
    }
  }

  $output = '<div class="bymodule">';
  foreach ($container as $id => $data) {
    $output .= '<div class="'. $id .' clear-block">';
    $output .= $data;
    $output .= '</div>';
  }
  $output .= '</div>';

  return $output;
}

function phptemplate_get_primary_links() {
  return menu_tree(variable_get('menu_primary_links_source', 'primary-links'));
}

function phptemplate_tagadelic_weighted($terms) {
   if (module_exists("i18ntaxonomy")) {
       $terms = i18ntaxonomy_localize_terms($terms);
   }
  foreach ($terms as $term) {
    $name = $term->name;
    $output .= l(
      $name,
      taxonomy_term_path($term),
      array(
        'attributes' => array(
          'class' => "tagadelic level$term->weight",
          'rel' => 'tag'
         )
      )
     ) ." \n";
    }
  return $output;
}
function phptemplate_pager_link($text, $page_new, $element, $parameters = array(), $attributes = array()) {
  $args = arg();
  $page_no = array_pop($args);
  $page = array_pop($args);
  // Check if we are not on the node/X or node/X/page/X path or node/X/view/page/X paths.
  // Still using arg(x) for some checks to avoid popped $args for certain conditions.
  if ((arg(0) != 'node' || !is_numeric(arg(1))) && (arg(2) != 'view' || (!empty($page) && $page != 'page')) && $element != 1) {
    // Don't mingle when the required module is absent.
    if (!module_exists('paging_sweet_urls')) {
      return theme_pager_link($text, $page_new, $element, $parameters, $attributes);
    }
  }
 
  $page = isset($_GET['page']) ? $_GET['page'] : '';
  if ($new_page = implode(',', pager_load_array($page_new[$element], $element, explode(',', $page)))) {
    //$parameters['page'] = $new_page;
  }
 
  $query = array();
  if (count($parameters)) {
    $query[] = drupal_query_string_encode($parameters, array());
  }
  $querystring = pager_get_querystring();
  if ($querystring != '') {
    $query[] = $querystring;
  }
 
  // Set each pager link title
  if (!isset($attributes['title'])) {
    static $titles = NULL;
    if (!isset($titles)) {
      $titles = array(
        t('« first') => t('Go to first page'),
        t('‹ previous') => t('Go to previous page'),
        t('next ›') => t('Go to next page'),
        t('last »') => t('Go to last page'),
      );
    }
    if (isset($titles[$text])) {
      $attributes['title'] = $titles[$text];
    }
    else if (is_numeric($text)) {
      $attributes['title'] = t('Go to page @number', array('@number' => $text));
    }
  }
 
  //$nid = arg(1);
  $nid = array_pop($args);
  //$page_no = $page_new[1] + 1;
  $page_no = $new_page;
  echo "<!-- $nid $new_page $page_no -->";
 
  return l($text, "node/page/$page_no", array('attributes' => $attributes, 'query' => count($query) ? implode('&', $query) : NULL));
}

// retrieve custom theme settings


$preload = theme_get_setting('cssPreload'); // print the js file if we choose css image preload

if($preload == '1'){
	
	drupal_add_js(drupal_get_path('theme','marinelli').'/js/preloadCssImages.jQuery_v5.js'); // load the javascript
	drupal_add_js('$(document).ready(function(){
		
	$.preloadCssImages();
		
	});
	
	','inline');

}

	
$valore = theme_get_setting('menutype'); // if we choose dropdown

if($valore == '1'){ 
 
    drupal_add_js(drupal_get_path('theme','marinelli').'/js/jquery.hoverIntent.minified.js'); // load the javascript
	drupal_add_js(drupal_get_path('theme','marinelli').'/js/marinellidropdown.js'); // load the javascript
	drupal_add_css(drupal_get_path('theme','marinelli').'/dropdown.css'); // load the css
	
}
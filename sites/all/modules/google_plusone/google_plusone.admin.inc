<?php

/**
 * @file
 * Administration page for the Google +1 Button.
 */

/**
 * Menu callback: displays the Google Plus One module settings page.
 *
 * @ingroup forms
 */
function google_plusone_admin_settings() {

  $available_sizes = array(
    'small' => t('Small (15px)'),
    'medium' => t('Medium (20px)'),
    'standard' => t('Standard (24px)'),
    'tall' => t('Tall (60px)'),
  );

  $form['google_plusone_node_types'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Display the Google +1 button on these content types:'),
    '#options' => node_get_types('names'),
    '#default_value' => variable_get('google_plusone_node_types', array('story')),
  );
  $form['google_plusone_count'] = array(
    '#type' => 'radios',
    '#title' => t('Include count?'),
    '#options' => array(t('No'), t('Yes')),
    '#default_value' => variable_get('google_plusone_count', 1),
    '#description' => t('Indicates whether or not to render an aggregate count.'),
  );
  $form['google_plusone_showonteasers'] = array(
    '#type' => 'radios',
    '#title' => t('Display button on teasers?'),
    '#options' => array(t('No'), t('Yes')),
    '#default_value' => variable_get('google_plusone_showonteasers', 0),
    '#description' => t('If <em>Yes</em> is selected, the button will appear when the node is displayed in teaser mode. Otherwise it will only appear when the full node is being viewed.'),
  );
  $form['google_plusone_size'] = array(
    '#type' => 'radios',
    '#title' => t('Size'),
    '#options' => $available_sizes,
    '#default_value' => variable_get('google_plusone_size', 'standard'),
    '#description' => t('Size of the button.'),
  );
  $form['google_plusone_weight'] = array(
    '#type' => 'weight',
    '#title' => t('Weight'),
    '#delta' => 50,
    '#default_value' => variable_get('google_plusone_weight', '-5'),
    '#description' => t('Heavier items will sink. Default weight -5 will show the button at the top of the node content.'),
  );
  return system_settings_form($form);
}
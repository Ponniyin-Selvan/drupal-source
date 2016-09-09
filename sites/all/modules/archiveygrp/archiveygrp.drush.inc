<?php
define('LOG4PHP_CONFIGURATION', 'log4php.properties'); 
require('log4php/LoggerManager.php');
require('archive.php');
// $Id$
/**
 * @file
 * Module for archiving yahoo group messages to drupal forum
 * Archives messages from yahoo group messages and post them as drupal forum 
 * topic and replies as comments
 * @see http://thiru.in
 */
 
/**
 * Implementation of hook_drush_command().
 *
 * Hooks into Drush Modules
 *
 * @See drush_parse_command() for a list of recognized keys.
 *
 * @return
 *   An associative array describing your command(s).
 */
function archiveygrp_drush_command() {
  $items = array();

  // the key in the $items array is the name of the command.
  $items['archiveygrp'] = array(
    // the name of the function implementing your command.
    'callback' => 'archiveygrp_callback',
    // a short description of your command
    'description' => "Drush Archive Yahoo Group Command",
  );
  return $items;
}


/**
 * Implementation of hook_drush_help().
 *
 * This function is called whenever a drush user calls
 * 'drush help <name-of-your-command>'
 *
 * @param
 *   A string with the help section (prepend with 'drush:')
 *
 * @return
 *   A string with the help text for your command.
 */
function archiveygrp_drush_help($section) {
  switch ($section) {
    case 'drush:archiveygrp':
      return dt("Archive Yahoo Group Messages to Drupal Forum.");
  }
}

/**
 * Import Yahoo Group Messages to Drupal Forums
 *
 *
 * To print something to the terminal window, use drush_print().
 *
 */
function archiveygrp_callback() {
  $commands = func_get_args();
  
  $archive = new Archive;
  $archive->initialize('ponniyinselvan');
  $archive->archiveNewMessages();
  $archive->shutdown();
  LoggerManager::shutdown();
}

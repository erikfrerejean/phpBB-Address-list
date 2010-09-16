<?php
/**
 *
 * @package phpBB Address List
 * @copyright (c) 2010 waterscouting.com http://www.waterscouting.com
 * @author Erik Frèrejean ( N/A ) http://www.erikfrerejean.nl
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * @ignore
 */
define('IN_PHPBB', true);
if (!defined('PHPBB_ROOT_PATH')) define('PHPBB_ROOT_PATH', './../');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strchr(__FILE__, '.'), 1));
if (!defined('ADL_ROOT_PATH')) define('ADL_ROOT_PATH', './');	// /me === lazy
require ADL_ROOT_PATH . 'common.' . PHP_EXT;

// Collect some vars
$page = request_var('page', '');

// Display teh left page
$page_tpl = '';
switch ($page)
{
	// Create the main page
	default :
		$page_title = 'ADDRESS_LIST_MAIN';
}

// Display
addresslist::display($page_title, $page_tpl);
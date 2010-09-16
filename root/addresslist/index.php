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
if (!defined('ADDRESS_ROOT_PATH')) define('ADDRESS_ROOT_PATH', './');	// /me === lazy
require ADDRESS_ROOT_PATH . 'common.' . PHP_EXT;
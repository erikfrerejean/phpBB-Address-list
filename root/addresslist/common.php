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
if (!defined('IN_PHPBB'))
{
	exit;
}

// This is nasty but otherwise phpBB will break :/
$phpbb_root_path = PHPBB_ROOT_PATH;
$phpEx = PHP_EXT;

// Include all non-common constants
require ADDRESS_ROOT_PATH . 'includes/constants.' . PHP_EXT;

// Include the main core classes
require ADDRESS_ROOT_PATH . 'includes/core/phpbb.' . PHP_EXT;

// Include common phpBB files and functions.
if (!file_exists(PHPBB_ROOT_PATH . 'common.' . PHP_EXT))
{
	die('<p>No phpBB installation found. Verify that you\'ve installed the Address List correctly.</p>');
}
require PHPBB_ROOT_PATH . 'common.' . PHP_EXT;

// Initialise phpBB
adl_phpbb::initialise();
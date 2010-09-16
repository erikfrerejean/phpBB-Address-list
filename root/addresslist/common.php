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
require ADL_ROOT_PATH . 'includes/constants.' . PHP_EXT;

// Include the main core classes
require ADL_ROOT_PATH . 'includes/core/addresslist.' . PHP_EXT;
require ADL_ROOT_PATH . 'includes/core/phpbb.' . PHP_EXT;

// Include common phpBB files and functions.
if (!file_exists(PHPBB_ROOT_PATH . 'common.' . PHP_EXT))
{
	die('<p>No phpBB installation found. Verify that you\'ve installed the Address List correctly.</p>');
}
require PHPBB_ROOT_PATH . 'common.' . PHP_EXT;

// Initialise phpBB
adl_phpbb::initialise();

// Check whether this thing has to be installed or the db has to be updated
if (!defined('IN_ADL_INSTALL') && (empty(adl_phpbb::$config['adl_version']) || version_compare(adl_phpbb::$config['adl_version'], ADL_VERSION, '<')))
{
	// No founder throw an error
	if (adl_phpbb::$user->data['user_type'] != USER_FOUNDER)
	{
		adl_phpbb::$user->set_custom_lang_path(ADL_ROOT_PATH . 'language/');
		adl_phpbb::$user->add_lang('common');

		trigger_error(adl_phpbb::$user->lang('ADL_DISABLED'), E_USER_ERROR);
	}

	// Redirect to the installer
	redirect(append_sid(ADL_ROOT_PATH . 'install.' . PHP_EXT));
}

// Initialise the core
addresslist::initialise();
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
define('IN_ADL_INSTALL', true);
define('UMIL_AUTO', true);
if (!defined('PHPBB_ROOT_PATH')) define('PHPBB_ROOT_PATH', './../');
if (!defined('PHP_EXT')) define('PHP_EXT', substr(strchr(__FILE__, '.'), 1));
if (!defined('ADL_ROOT_PATH')) define('ADL_ROOT_PATH', './');	// /me === lazy
require ADL_ROOT_PATH . 'common.' . PHP_EXT;

// Include ADL install language file
adl_phpbb::$user->set_custom_lang_path(ADL_ROOT_PATH . 'language/');
adl_phpbb::$user->add_lang('install');
adl_phpbb::$user->set_custom_lang_path(PHPBB_ROOT_PATH . 'language/');

// Check UMIL
if (!file_exists(PHPBB_ROOT_PATH . 'umil/umil_auto.' . PHP_EXT))
{
	trigger_error(adl_phpbb::$user->lang('DOWNLOAD_UMIL'), E_USER_ERROR);
}

// Load version data
require ADL_ROOT_PATH . 'includes/version_data.' . PHP_EXT;

// Spit out UMIL
require PHPBB_ROOT_PATH . 'umil/umil_auto.' . PHP_EXT;
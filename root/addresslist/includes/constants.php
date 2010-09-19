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

// The version
define ('ADL_VERSION', '0.0.2');

// Tables
define ('ADL_ADDRESLIST_TABLE'		, $table_prefix . 'adl_addresslist');
define ('ADL_ADDRESLIST_DATA_TABLE'	, $table_prefix . 'adl_addresslist_data');
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

$mod_name = 'ADDRESS_LIST';
$version_config_name = 'adl_version';

// The array of versions and actions within each.
// You do not need to order it a specific way (it will be sorted automatically),
// however, you must enter every version, even if no actions are done for it.
//
// You must use correct version numbering.  Unless you know exactly what you can
// use, only use X.X.X (replacing X with an integer).
// The version numbering must otherwise be compatible with the version_compare
// function - http://php.net/manual/en/function.version-compare.php
$versions = array(
	'0.0.1'	=> array(
		// No schema
	),
	'0.0.2' => array(
		'table_add' => array(
			// Create the main Addresses table
			array(
				ADL_ADDRESLIST_TABLE, array(
					'COLUMNS'	=> array(
						'adress_id' => array('UINT', NULL, 'auto_increment'),
						'user_id'   => array('UINT', 0),
					),
					'PRIMARY_KEY'	=> 'adress_id',
					'KEYS'			=> array(
						'address_user_id'	=> array('INDEX', 'user_id'),
					),
				),
			),
			
			// Create the Address list data table
			array(
				ADL_ADDRESLIST_DATA_TABLE, array(
					'COLUMNS'	=> array(
						'address_id'    => array('UINT', 0),
						'addresstype'	=> array('USINT', 0),
						'addressvalue'	=> array('VCHAR', 0),
					),
					'KEYS'			=> array(
						'address_id'	=> array('INDEX', 'address_id'),
						'addresstype'	=> array('INDEX', 'addresstype'),
					),
				),
			),
		),
	),
);
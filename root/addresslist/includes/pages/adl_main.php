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

if (!class_exists('adl_page'))
{
	require ADL_ROOT_PATH . 'includes/adl_page.' . PHP_EXT;
}

/**
 * Generate main page
 *
 * @author erikfrerejean
 */
class adl_main extends adl_page
{
    public function __construct()
	{
		// Set the stuff
		$this->page_template	= 'index_body.html';
		$this->page_title		= 'ADDRESS_LIST_MAIN';
		$this->run_cron			= true;
	}
}
?>

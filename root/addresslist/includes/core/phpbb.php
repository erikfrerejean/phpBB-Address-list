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

/**
 * phpBB class that will be used in place of globalising these variables,
 * and contains a set of method that are used in place of phpBB's build
 * in functions to make sure we can inject required stuff.
 */
abstract class adl_phpbb
{
	/**
	 * @var auth phpBB Auth class
	 */
	static public $auth = null;

	/**
	 * @var cache phpBB Cache class
	 */
	static public $cache = null;

	/**
	 * @var config phpBB Config class
	 */
	static public $config = null;

	/**
	 * @var db phpBB DBAL class
	 */
	static public $db = null;

	/**
	 * @var template phpBB Template class
	 */
	static public $template = null;

	/**
	 * @var user phpBB User class
	 */
	static public $user = null;

	/**
	 * Load the data
	 */
	static public function initialise()
	{
		global $auth, $config, $cache, $db, $template, $user;

		self::$auth		= &$auth;
		self::$cache	= &$cache;
		self::$config	= &$config;
		self::$db		= &$db;
		self::$template	= &$template;
		self::$user		= &$user;

		// Start phpBB session management
		self::$user->session_begin();
		self::$auth->acl(self::$user->data);
		self::$user->setup();
	}
}
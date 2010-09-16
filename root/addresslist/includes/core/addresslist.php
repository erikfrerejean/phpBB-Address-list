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
 * phpBB Address List core class
 */
abstract class addresslist
{
	/**
	 * Initialise phpBB Address List
	 *
	 * @return void
	 */
	static public function initialise()
	{
		// Nothing to initialise yet
	}

	/**
	 * Wrapper for user::add_lang(), this method is used to load phpBB Address List
	 * language files.
	 *
	 * @param	String|array	$langfiles	Name (or an array containing multiple names)
	 *										of the file(s) that will be added
	 * @return	void
	 */
	public static function add_lang($lang_set, $use_db = false, $use_help = false)
	{
		static $_added = array();

		if (!is_array($lang_set))
		{
			$lang_set = array($lang_set);
		}

		// Run through the language set and collect the files that will be included
		$_files = array();
		foreach ($lang_set as $lang_file)
		{
			// Only once
			if (in_array($lang_file, $_added))
			{
				continue;
			}

			// Include
			$_files[] = $lang_file;

			// Prevent inclution next time
			$_added[] = $lang_file;
		}

		// Nothing
		if (empty($_files))
		{
			return;
		}

		// Store the current language path and switch to the ADL one
		$_path = adl_phpbb::$user->lang_path;
		adl_phpbb::$user->set_custom_lang_path(ADL_ROOT_PATH . 'language/');

		// Include the files
		adl_phpbb::$user->add_lang($_files, $use_db, $use_help);

		// Switch the path back
		adl_phpbb::$user->set_custom_lang_path($_path);
	}
}
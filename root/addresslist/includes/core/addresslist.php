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
	 * @var string The absolute path to phpBB
	 */
	static public $absolute = '';

	/**
	 * Initialise phpBB Address List
	 *
	 * @return void
	 */
	static public function initialise()
	{
		// Set the template path
		adl_phpbb::$template->set_custom_template(ADL_ROOT_PATH . 'styles/', adl_phpbb::$user->theme['theme_name'], ADL_ROOT_PATH . 'styles/prosilver/template/');
		adl_phpbb::$user->theme['template_storedb'] = false;

		// Set the absolute path, this is a bit nasty but it allows users to
		// rename the addresslist directory.
		$_dir_parts = explode('/', __DIR__);
		$_dir = $_dir_parts[sizeof($_dir_parts) - 3]; // It's *always* three down
		self::$absolute = generate_board_url(true) . adl_phpbb::$config['script_path'] . '/' . $_dir . '/';
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

	/**
	 * A small wrapper that calls both self::page_header() and self::page_footer()
	 * just cause I don't want to bother to wrote the two calls :P
	 *
	 * @param	String	$page_title		The title of the current page
	 * @param	String	$template_file	The template file that is used for this view
	 * @param	Boolean	$run_cron		Boolean used to define whether cron's should be ran
	 */
	static public function display($page_title = '', $template_file = '', $run_cron = true)
	{
		self::page_header($page_title);
		self::page_footer($template_file, $run_cron);
	}

	/**
	 * Generate the Address List footer
	 *
	 * @param	String	$template_file	The template file that is used for this view
	 * @param	Boolean	$run_cron		Boolean used to define whether cron's should be ran
	 * @return	void
	 */
	static public function page_footer($template_file = '', $run_cron = true)
	{
		// Fall back ^^
		if (empty($template_file))
		{
			$template_file = 'index_body.html';
		}

		// Set the template file
		adl_phpbb::$template->set_filenames(array(
			'body'	=> $template_file,
		));

		// Output
		adl_phpbb::page_footer($run_cron);
	}

	/**
	 * Generate the Address List header
	 *
	 * @param	String	$page_title	The title of the current page
	 * @return	void
	 */
	static public function page_header($page_title = '')
	{
		if (defined('HEADER_INC'))
		{
			return;
		}

		define('HEADER_INC', true);

		// Call phpBB's header first it does some stuff we need ;)
		adl_phpbb::page_header($page_title);

		// Assign more template vars
		adl_phpbb::$template->assign_vars(array(
			'T_ADL_THEME_PATH'			=> self::$absolute . 'styles/' . adl_phpbb::$user->theme['theme_path'] . '/theme',
			'T_ADL_TEMPLATE_PATH'		=> self::$absolute . 'styles/' . adl_phpbb::$user->theme['template_path'] . '/template',
			'T_ADL_SUPER_TEMPLATE_PATH'	=> (isset(adl_phpbb::$user->theme['template_inherit_path']) && adl_phpbb::$user->theme['template_inherit_path']) ? self::$absolute . 'styles/' . adl_phpbb::$user->theme['template_inherit_path'] . '/template' : self::$absolute . 'styles/' . adl_phpbb::$user->theme['template_path'] . '/template',
			'T_ADL_IMAGESET_PATH'		=> self::$absolute . 'styles/' . adl_phpbb::$user->theme['imageset_path'] . '/imageset',
			'T_ADL_IMAGESET_LANG_PATH'	=> self::$absolute . 'styles/' . adl_phpbb::$user->theme['imageset_path'] . '/imageset/' . adl_phpbb::$user->data['user_lang'],
			'T_STYLESHEET_LINK'			=> (!adl_phpbb::$user->theme['theme_storedb']) ? self::$absolute . 'styles/' . adl_phpbb::$user->theme['theme_path'] . '/theme/stylesheet.css' : append_sid(PHPBB_ROOT_PATH . 'style.' . PHP_EXT, 'id=' . adl_phpbb::$user->theme['style_id'] . '&amp;lang=' . adl_phpbb::$user->data['user_lang']),
			'T_ADL_STYLESHEET_NAME'		=> adl_phpbb::$user->theme['theme_name'],
		));
	}
}
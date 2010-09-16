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
	 *
	 * @return void
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

	/**
	 * The phpBB page_footer function, its a plain copy. Doing it this way prevents
	 * interference from MODs that like to edit this function. And we can limit it
	 * to just that what is needed
	 *
	 * @param	Boolean	$run_cron		Boolean used to define whether cron's should be ran
	 * @return	void
	 */
	static public function page_footer($run_cron = true)
	{
		global $starttime;

		// Output page creation time
		if (defined('DEBUG'))
		{
			$mtime = explode(' ', microtime());
			$totaltime = $mtime[0] + $mtime[1] - $starttime;

			if (!empty($_REQUEST['explain']) && self::$auth->acl_get('a_') && defined('DEBUG_EXTRA') && method_exists(self::$db, 'sql_report'))
			{
				self::$db->sql_report('display');
			}

			$debug_output = sprintf('Time : %.3fs | ' . self::$db->sql_num_queries() . ' Queries | GZIP : ' . ((self::$config['gzip_compress'] && @extension_loaded('zlib')) ? 'On' : 'Off') . ((self::$user->load) ? ' | Load : ' . self::$user->load : ''), $totaltime);

			if (self::$auth->acl_get('a_') && defined('DEBUG_EXTRA'))
			{
				if (function_exists('memory_get_usage'))
				{
					if ($memory_usage = memory_get_usage())
					{
						global $base_memory_usage;
						$memory_usage -= $base_memory_usage;
						$memory_usage = get_formatted_filesize($memory_usage);

						$debug_output .= ' | Memory Usage: ' . $memory_usage;
					}
				}

				$debug_output .= ' | <a href="' . build_url() . '&amp;explain=1">Explain</a>';
			}
		}

		self::$template->assign_vars(array(
			'DEBUG_OUTPUT'			=> (defined('DEBUG')) ? $debug_output : '',
			'TRANSLATION_INFO'		=> (!empty(self::$user->lang['TRANSLATION_INFO'])) ? self::$user->lang['TRANSLATION_INFO'] : '',

			'U_ACP' => (self::$auth->acl_get('a_') && !empty(self::$user->data['is_registered'])) ? append_sid(PHPBB_ROOT_PATH . 'adm/index.' . PHP_EXT, false, true, self::$user->session_id) : '')
		);

		// Call cron-type script
		$call_cron = false;
		if (!defined('IN_CRON') && $run_cron && !self::$config['board_disable'])
		{
			$call_cron = true;
			$time_now = (!empty(self::$user->time_now) && is_int(self::$user->time_now)) ? self::$user->time_now : time();

			// Any old lock present?
			if (!empty(self::$config['cron_lock']))
			{
				$cron_time = explode(' ', self::$config['cron_lock']);

				// If 1 hour lock is present we do not call cron.php
				if ($cron_time[0] + 3600 >= $time_now)
				{
					$call_cron = false;
				}
			}
		}

		// Call cron job?
		if ($call_cron)
		{
			// At some point this MOD might need a cron
		}

		self::$template->display('body');

		garbage_collection();
		exit_handler();
	}

	/**
	 * The phpBB page_footer function, its a plain copy. Doing it this way prevents
	 * interference from MODs that like to edit this function. And we can limit it
	 * to just that what is needed
	 *
	 * @todo	See whether there is more that can be removed from here.
	 * @todo	Remove all non-used code (only commented out atm)
	 * @param	String	$page_title	The title of the current page
	 * @return	void
	 */
	static public function page_header($page_title = '')
	{
		// gzip_compression
		if (self::$config['gzip_compress'])
		{
			if (@extension_loaded('zlib') && !headers_sent())
			{
				ob_start('ob_gzhandler');
			}
		}
/*
		// Generate logged in/logged out status
		if (self::$user->data['user_id'] != ANONYMOUS)
		{
			$u_login_logout = append_sid(PHPBB_ROOT_PATH . 'ucp.' . PHP_EXT, 'mode=logout', true, self::$user->session_id);
			$l_login_logout = sprintf(self::$user->lang['LOGOUT_USER'], self::$user->data['username']);
		}
		else
		{
			$u_login_logout = append_sid(PHPBB_ROOT_PATH . 'ucp.' . PHP_EXT, 'mode=login');
			$l_login_logout = self::$user->lang['LOGIN'];
		}
*/
		// Last visit date/time
		$s_last_visit = (self::$user->data['user_id'] != ANONYMOUS) ? self::$user->format_date(self::$user->data['session_last_visit']) : '';
/*
		// Get users online list ... if required
		$l_online_users = $online_userlist = $l_online_record = $l_online_time = '';

		$l_privmsgs_text = $l_privmsgs_text_unread = '';
		$s_privmsg_new = false;

		// Obtain number of new private messages if user is logged in
		if (!empty(self::$user->data['is_registered']))
		{
			if (self::$user->data['user_new_privmsg'])
			{
				$l_message_new = (self::$user->data['user_new_privmsg'] == 1) ? self::$user->lang['NEW_PM'] : self::$user->lang['NEW_PMS'];
				$l_privmsgs_text = self::$user->lang($l_message_new, self::$user->data['user_new_prvmsg']);

				if (!self::$user->data['user_last_privmsg'] || self::$user->data['user_last_privmsg'] > self::$user->data['session_last_visit'])
				{
					$sql = 'UPDATE ' . USERS_TABLE . '
						SET user_last_privmsg = ' . self::$user->data['session_last_visit'] . '
						WHERE user_id = ' . self::$user->data['user_id'];
					self::$db->sql_query($sql);

					$s_privmsg_new = true;
				}
				else
				{
					$s_privmsg_new = false;
				}
			}
			else
			{
				$l_privmsgs_text = self::$user->lang['NO_NEW_PM'];
				$s_privmsg_new = false;
			}

			$l_privmsgs_text_unread = '';

			if (self::$user->data['user_unread_privmsg'] && self::$user->data['user_unread_privmsg'] != self::$user->data['user_new_privmsg'])
			{
				$l_message_unread = (self::$user->data['user_unread_privmsg'] == 1) ? self::$user->lang['UNREAD_PM'] : self::$user->lang['UNREAD_PMS'];
				$l_privmsgs_text_unread = sprintf($l_message_unread, self::$user->data['user_unread_privmsg']);
			}
		}
*/
		// Determine board url - we may need it later
		$board_url = generate_board_url() . '/';
		$web_path = (defined('PHPBB_USE_BOARD_URL_PATH') && PHPBB_USE_BOARD_URL_PATH) ? $board_url : PHPBB_ROOT_PATH;

		// Which timezone?
		$tz = (self::$user->data['user_id'] != ANONYMOUS) ? strval(doubleval(self::$user->data['user_timezone'])) : strval(doubleval(self::$config['board_timezone']));

		// Send a proper content-language to the output
		$user_lang = self::$user->lang['USER_LANG'];
		if (strpos($user_lang, '-x-') !== false)
		{
			self::$user_lang = substr($user_lang, 0, strpos($user_lang, '-x-'));
		}

		// The following assigns all _common_ variables that may be used at any point in a template.
		self::$template->assign_vars(array(
			'SITENAME'						=> self::$config['sitename'],
			'SITE_DESCRIPTION'				=> self::$config['site_desc'],
			'PAGE_TITLE'					=> $page_title,
//			'SCRIPT_NAME'					=> str_replace('.' . PHP_EXT, '', self::$user->page['page_name']),
			'LAST_VISIT_DATE'				=> sprintf(self::$user->lang['YOU_LAST_VISIT'], $s_last_visit),
			'LAST_VISIT_YOU'				=> $s_last_visit,
			'CURRENT_TIME'					=> sprintf(self::$user->lang['CURRENT_TIME'], self::$user->format_date(time(), false, true)),
//			'TOTAL_USERS_ONLINE'			=> $l_online_users,
//			'LOGGED_IN_USER_LIST'			=> $online_userlist,
//			'RECORD_USERS'					=> $l_online_record,
//			'PRIVATE_MESSAGE_INFO'			=> $l_privmsgs_text,
//			'PRIVATE_MESSAGE_INFO_UNREAD'	=> $l_privmsgs_text_unread,

//			'S_USER_NEW_PRIVMSG'			=> self::$user->data['user_new_privmsg'],
//			'S_USER_UNREAD_PRIVMSG'			=> self::$user->data['user_unread_privmsg'],
//			'S_USER_NEW'					=> self::$user->data['user_new'],

//			'SID'				=> $SID,
//			'_SID'				=> $_SID,
//			'SESSION_ID'		=> self::$user->session_id,
			'ROOT_PATH'			=> PHPBB_ROOT_PATH,
			'BOARD_URL'			=> $board_url,

//			'L_LOGIN_LOGOUT'	=> $l_login_logout,
//			'L_INDEX'			=> self::$user->lang['FORUM_INDEX'],
//			'L_ONLINE_EXPLAIN'	=> $l_online_time,

//			'U_PRIVATEMSGS'			=> append_sid(PHPBB_ROOT_PATH . 'ucp.' . PHP_EXT, 'i=pm&amp;folder=inbox'),
//			'U_RETURN_INBOX'		=> append_sid(PHPBB_ROOT_PATH . 'ucp.' . PHP_EXT, 'i=pm&amp;folder=inbox'),
//			'U_POPUP_PM'			=> append_sid(PHPBB_ROOT_PATH . 'ucp.' . PHP_EXT, 'i=pm&amp;mode=popup'),
//			'UA_POPUP_PM'			=> addslashes(append_sid(PHPBB_ROOT_PATH . 'ucp.' . PHP_EXT, 'i=pm&amp;mode=popup')),
//			'U_MEMBERLIST'			=> append_sid(PHPBB_ROOT_PATH . 'memberlist.' . PHP_EXT),
//			'U_VIEWONLINE'			=> ($auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel')) ? append_sid(PHPBB_ROOT_PATH . 'viewonline.' . PHP_EXT) : '',
//			'U_LOGIN_LOGOUT'		=> $u_login_logout,
			'U_INDEX'				=> append_sid(PHPBB_ROOT_PATH . 'index.' . PHP_EXT),
//			'U_SEARCH'				=> append_sid(PHPBB_ROOT_PATH . 'search.' . PHP_EXT),
//			'U_REGISTER'			=> append_sid(PHPBB_ROOT_PATH . 'ucp.' . PHP_EXT, 'mode=register'),
//			'U_PROFILE'				=> append_sid(PHPBB_ROOT_PATH . 'ucp.' . PHP_EXT),
//			'U_MODCP'				=> append_sid(PHPBB_ROOT_PATH . 'mcp.' . PHP_EXT, false, true, self::$user->session_id),
//			'U_FAQ'					=> append_sid(PHPBB_ROOT_PATH . 'faq.' . PHP_EXT),
//			'U_SEARCH_SELF'			=> append_sid(PHPBB_ROOT_PATH . 'search.' . PHP_EXT, 'search_id=egosearch'),
//			'U_SEARCH_NEW'			=> append_sid(PHPBB_ROOT_PATH . 'search.' . PHP_EXT, 'search_id=newposts'),
//			'U_SEARCH_UNANSWERED'	=> append_sid(PHPBB_ROOT_PATH . 'search.' . PHP_EXT, 'search_id=unanswered'),
//			'U_SEARCH_UNREAD'		=> append_sid(PHPBB_ROOT_PATH . 'search.' . PHP_EXT, 'search_id=unreadposts'),
//			'U_SEARCH_ACTIVE_TOPICS'=> append_sid(PHPBB_ROOT_PATH . 'search.' . PHP_EXT, 'search_id=active_topics'),
			'U_DELETE_COOKIES'		=> append_sid(PHPBB_ROOT_PATH . 'ucp.' . PHP_EXT, 'mode=delete_cookies'),
//			'U_TEAM'				=> (self::$user->data['user_id'] != ANONYMOUS && !$auth->acl_get('u_viewprofile')) ? '' : append_sid(PHPBB_ROOT_PATH . 'memberlist.' . PHP_EXT, 'mode=leaders'),
//			'U_TERMS_USE'			=> append_sid(PHPBB_ROOT_PATH . 'ucp.' . PHP_EXT, 'mode=terms'),
//			'U_PRIVACY'				=> append_sid(PHPBB_ROOT_PATH . 'ucp.' . PHP_EXT, 'mode=privacy'),
//			'U_RESTORE_PERMISSIONS'	=> (self::$user->data['user_perm_from'] && $auth->acl_get('a_switchperm')) ? append_sid(PHPBB_ROOT_PATH . 'ucp.' . PHP_EXT, 'mode=restore_perm') : '',
//			'U_FEED'				=> generate_board_url() . '/feed.' . PHP_EXT,

			'S_USER_LOGGED_IN'		=> (self::$user->data['user_id'] != ANONYMOUS) ? true : false,
			'S_AUTOLOGIN_ENABLED'	=> (self::$config['allow_autologin']) ? true : false,
			'S_BOARD_DISABLED'		=> (self::$config['board_disable']) ? true : false,
			'S_REGISTERED_USER'		=> (!empty(self::$user->data['is_registered'])) ? true : false,
			'S_IS_BOT'				=> (!empty(self::$user->data['is_bot'])) ? true : false,
//			'S_USER_PM_POPUP'		=> self::$user->optionget('popuppm'),
			'S_USER_LANG'			=> $user_lang,
//			'S_USER_BROWSER'		=> (isset(self::$user->data['session_browser'])) ? self::$user->data['session_browser'] : self::$user->lang['UNKNOWN_BROWSER'],
			'S_USERNAME'			=> self::$user->data['username'],
			'S_CONTENT_DIRECTION'	=> self::$user->lang['DIRECTION'],
			'S_CONTENT_FLOW_BEGIN'	=> (self::$user->lang['DIRECTION'] == 'ltr') ? 'left' : 'right',
			'S_CONTENT_FLOW_END'	=> (self::$user->lang['DIRECTION'] == 'ltr') ? 'right' : 'left',
			'S_CONTENT_ENCODING'	=> 'UTF-8',
			'S_TIMEZONE'			=> (self::$user->data['user_dst'] || (self::$user->data['user_id'] == ANONYMOUS && self::$config['board_dst'])) ? sprintf(self::$user->lang['ALL_TIMES'], self::$user->lang['tz'][$tz], self::$user->lang['tz']['dst']) : sprintf(self::$user->lang['ALL_TIMES'], self::$user->lang['tz'][$tz], ''),
//			'S_DISPLAY_ONLINE_LIST'	=> ($l_online_time) ? 1 : 0,
//			'S_DISPLAY_SEARCH'		=> (!self::$config['load_search']) ? 0 : (isset($auth) ? ($auth->acl_get('u_search') && $auth->acl_getf_global('f_search')) : 1),
//			'S_DISPLAY_PM'			=> (self::$config['allow_privmsg'] && !empty(self::$user->data['is_registered']) && (self::$auth->acl_get('u_readpm') || self::$auth->acl_get('u_sendpm'))) ? true : false,
//			'S_DISPLAY_MEMBERLIST'	=> (isset($auth)) ? $auth->acl_get('u_viewprofile') : 0,
//			'S_NEW_PM'				=> ($s_privmsg_new) ? 1 : 0,
			'S_REGISTER_ENABLED'	=> (self::$config['require_activation'] != USER_ACTIVATION_DISABLE) ? true : false,
//			'S_FORUM_ID'			=> $forum_id,
//			'S_TOPIC_ID'			=> $topic_id,

			'S_LOGIN_ACTION'		=> ((!defined('ADMIN_START')) ? append_sid(PHPBB_ROOT_PATH . 'ucp.' . PHP_EXT, 'mode=login') : append_sid('index.' . PHP_EXT, false, true, self::$user->session_id)),
			'S_LOGIN_REDIRECT'		=> build_hidden_fields(array('redirect' => build_url())),

//			'S_ENABLE_FEEDS'			=> (self::$config['feed_enable']) ? true : false,
//			'S_ENABLE_FEEDS_OVERALL'	=> (self::$config['feed_overall']) ? true : false,
//			'S_ENABLE_FEEDS_FORUMS'		=> (self::$config['feed_overall_forums']) ? true : false,
//			'S_ENABLE_FEEDS_TOPICS'		=> (self::$config['feed_topics_new']) ? true : false,
//			'S_ENABLE_FEEDS_TOPICS_ACTIVE'	=> (self::$config['feed_topics_active']) ? true : false,
//			'S_ENABLE_FEEDS_NEWS'		=> ($s_feed_news) ? true : false,

//			'S_LOAD_UNREADS'			=> (self::$config['load_unreads_search']) ? true : false,

			'T_THEME_PATH'			=> "{$web_path}styles/" . self::$user->theme['theme_path'] . '/theme',
			'T_TEMPLATE_PATH'		=> "{$web_path}styles/" . self::$user->theme['template_path'] . '/template',
			'T_SUPER_TEMPLATE_PATH'	=> (isset(self::$user->theme['template_inherit_path']) && self::$user->theme['template_inherit_path']) ? "{$web_path}styles/" . self::$user->theme['template_inherit_path'] . '/template' : "{$web_path}styles/" . self::$user->theme['template_path'] . '/template',
			'T_IMAGESET_PATH'		=> "{$web_path}styles/" . self::$user->theme['imageset_path'] . '/imageset',
			'T_IMAGESET_LANG_PATH'	=> "{$web_path}styles/" . self::$user->theme['imageset_path'] . '/imageset/' . self::$user->data['user_lang'],
			'T_IMAGES_PATH'			=> "{$web_path}images/",
			'T_SMILIES_PATH'		=> $web_path . self::$config['smilies_path'] . '/',
			'T_AVATAR_PATH'			=> $web_path . self::$config['avatar_path'] . '/',
			'T_AVATAR_GALLERY_PATH'	=> $web_path . self::$config['avatar_gallery_path'] . '/',
			'T_ICONS_PATH'			=> $web_path . self::$config['icons_path'] . '/',
			'T_RANKS_PATH'			=> $web_path . self::$config['ranks_path'] . '/',
			'T_UPLOAD_PATH'			=> $web_path . self::$config['upload_path'] . '/',
			'T_STYLESHEET_LINK'		=> (!self::$user->theme['theme_storedb']) ? "{$web_path}styles/" . self::$user->theme['theme_path'] . '/theme/stylesheet.css' : append_sid(PHPBB_ROOT_PATH . 'style.' . PHP_EXT, 'id=' . self::$user->theme['style_id'] . '&amp;lang=' . self::$user->data['user_lang']),
			'T_STYLESHEET_NAME'		=> self::$user->theme['theme_name'],

			'T_THEME_NAME'			=> self::$user->theme['theme_path'],
			'T_TEMPLATE_NAME'		=> self::$user->theme['template_path'],
			'T_SUPER_TEMPLATE_NAME'	=> (isset(self::$user->theme['template_inherit_path']) && self::$user->theme['template_inherit_path']) ? self::$user->theme['template_inherit_path'] : self::$user->theme['template_path'],
			'T_IMAGESET_NAME'		=> self::$user->theme['imageset_path'],
			'T_IMAGESET_LANG_NAME'	=> self::$user->data['user_lang'],
			'T_IMAGES'				=> 'images',
			'T_SMILIES'				=> self::$config['smilies_path'],
			'T_AVATAR'				=> self::$config['avatar_path'],
			'T_AVATAR_GALLERY'		=> self::$config['avatar_gallery_path'],
			'T_ICONS'				=> self::$config['icons_path'],
			'T_RANKS'				=> self::$config['ranks_path'],
			'T_UPLOAD'				=> self::$config['upload_path'],

			'SITE_LOGO_IMG'			=> self::$user->img('site_logo'),

			'A_COOKIE_SETTINGS'		=> addslashes('; path=' . self::$config['cookie_path'] . ((!self::$config['cookie_domain'] || self::$config['cookie_domain'] == 'localhost' || self::$config['cookie_domain'] == '127.0.0.1') ? '' : '; domain=' . self::$config['cookie_domain']) . ((!self::$config['cookie_secure']) ? '' : '; secure')),
		));

		// application/xhtml+xml not used because of IE
		header('Content-type: text/html; charset=UTF-8');

		header('Cache-Control: private, no-cache="set-cookie"');
		header('Expires: 0');
		header('Pragma: no-cache');

		return;
	}
}
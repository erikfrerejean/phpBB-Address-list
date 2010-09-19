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
 * Addresslist main page class,
 * all pages must implement this class
 *
 * @author erikfrerejean
 */
abstract class adl_page
{
	/**
	 * @var String The used page title
	 */
    protected $page_title = '';

	/**
	 * @var String The template file that is used to generate this page
	 */
	protected $page_template = '';

	/**
	 * @var Boolean Run the crons
	 */
	protected $run_cron = true;

	/**
	 * Generate the requested page by calling only the page_header/footer
	 * methods. This assumes that *everything* is setup at this point.
	 * This method will perform no additional checks.
	 */
	public function genereate_page_quick()
	{
		addresslist::page_header($this->page_title);
		addresslist::page_footer($this->page_template, $this->run_cron);
	}

	//-- Getters and Setters
	public function set_page_title($title = '')
	{
		$this->page_title = $title;
	}

	public function set_page_template($template = '')
	{
		$this->page_template = $template;
	}

	public function set_run_cron($run_cron = false)
	{
		$this->run_cron = (bool) $run_cron;
	}
}
?>

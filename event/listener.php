<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\event;

/**
 * Event listener
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use \ernadoo\phpbbdirectory\core\helper;

class listener extends helper implements EventSubscriberInterface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var string $table_prefix */
	protected $table_prefix;

	/** @var string phpEx */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface		$db				Database object
	* @param \phpbb\controller\helper				$helper			Controller helper object
	* @param \phpbb\language\language				$language		Language object
	* @param \phpbb\template\template				$template		Template object
	* @param string									$table_prefix 	prefix table
	* @param string									$php_ext 		phpEx
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\language\language $language, \phpbb\template\template $template, $table_prefix, $php_ext)
	{
		$this->db			= $db;
		$this->helper 		= $helper;
		$this->language		= $language;
		$this->template 	= $template;
		$this->table_prefix = $table_prefix;
		$this->php_ext		= $php_ext;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return	array
	* @static
	* @access	public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.delete_user_after'				=> 'update_links_with_anonymous',
			'core.page_header'        				=> 'add_page_header_variables',
			'core.permissions'						=> 'permissions_add_directory',
			'core.user_setup'						=> 'load_language_on_setup',
			'core.viewonline_overwrite_location'	=> 'add_page_viewonline'
		);
	}

	/**
	* Display links to Directory
	*
	* @return null
	*/
	public function add_page_header_variables()
	{
		$ext_theme_path		= $this->get_ext_name() . '/styles/prosilver/theme/';
		$theme_lang_path	= $this->language->get_used_language();

		// Prevent 'Twig_Error_Loader' if user's lang directory doesn't exist
		if (!file_exists($ext_theme_path . $theme_lang_path . '/directory.css'))
		{
			// Fallback to English language.
			$theme_lang_path = \phpbb\language\language::FALLBACK_LANGUAGE;
		}

		$this->template->assign_vars(array(
			'T_DIR_THEME_LANG_NAME' => $theme_lang_path,
			'U_DIRECTORY'			=> $this->helper->route('ernadoo_phpbbdirectory_base_controller'),
		));
	}

	/**
	* Show users as viewing Directory on Who Is Online page
	*
	* @param	object $event The event object
	* @return	null
	*/
	public function add_page_viewonline($event)
	{
		if (strrpos($event['row']['session_page'], 'app.' . $this->php_ext . '/directory') === 0)
		{
			$event['location']		= $this->language->lang('DIRECTORY');
			$event['location_url']	= $this->helper->route('ernadoo_phpbbdirectory_base_controller');
		}
	}

	/**
	* Load common language files during user setup
	*
	* @param	object $event The event object
	* @return	null
	*/
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'ernadoo/phpbbdirectory',
			'lang_set' => 'directory_common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	* Add administrative permissions to manage Directory
	*
	* @param	object $event The event object
	* @return	null
	*/
	public function permissions_add_directory($event)
	{
		$event->update_subarray('categories', 'dir',	'ACL_CAT_DIRECTORY');

		$event->update_subarray('permissions', 'm_delete_dir',			array('lang' => 'ACL_M_DELETE_DIR', 		'cat' => 'dir'));
		$event->update_subarray('permissions', 'm_delete_comment_dir',	array('lang' => 'ACL_M_DELETE_COMMENT_DIR',	'cat' => 'dir'));
		$event->update_subarray('permissions', 'm_edit_dir',			array('lang' => 'ACL_M_EDIT_DIR',			'cat' => 'dir'));
		$event->update_subarray('permissions', 'm_edit_comment_dir',	array('lang' => 'ACL_M_EDIT_COMMENT_DIR',	'cat' => 'dir'));

		$event->update_subarray('permissions', 'u_comment_dir',			array('lang' => 'ACL_U_COMMENT_DIR',		'cat' => 'dir'));
		$event->update_subarray('permissions', 'u_delete_dir',			array('lang' => 'ACL_U_DELETE_DIR',			'cat' => 'dir'));
		$event->update_subarray('permissions', 'u_delete_comment_dir',	array('lang' => 'ACL_U_DELETE_COMMENT_DIR',	'cat' => 'dir'));
		$event->update_subarray('permissions', 'u_edit_dir',			array('lang' => 'ACL_U_EDIT_DIR',			'cat' => 'dir'));
		$event->update_subarray('permissions', 'u_edit_comment_dir',	array('lang' => 'ACL_U_EDIT_COMMENT_DIR',	'cat' => 'dir'));
		$event->update_subarray('permissions', 'u_search_dir',			array('lang' => 'ACL_U_SEARCH_DIR',			'cat' => 'dir'));
		$event->update_subarray('permissions', 'u_submit_dir',			array('lang' => 'ACL_U_SUBMIT_DIR',			'cat' => 'dir'));
		$event->update_subarray('permissions', 'u_vote_dir',			array('lang' => 'ACL_U_VOTE_DIR',			'cat' => 'dir'));
	}

	/**
	* Update Directory tables if needed, after deleted an user
	*
	* @param	object $event The event object
	* @return	null
	*/
	public function update_links_with_anonymous($event)
	{
		$user_ids = $event['user_ids'];

		if (!is_array($user_ids))
		{
			$user_ids = array($user_ids);
		}

		$sql = 'UPDATE ' . $this->comments_table . '
			SET comment_user_id = ' . ANONYMOUS . '
			WHERE ' . $this->db->sql_in_set('comment_user_id', $user_ids);
		$this->db->sql_query($sql);

		$sql = 'UPDATE ' . $this->links_table . '
			SET link_user_id = ' . ANONYMOUS . '
			WHERE ' . $this->db->sql_in_set('link_user_id', $user_ids);
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . $this->watch_table . '
			WHERE ' . $this->db->sql_in_set('user_id', $user_ids);
		$this->db->sql_query($sql);
	}
}

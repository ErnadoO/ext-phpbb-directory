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

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string $table_prefix */
	protected $table_prefix;

	/** @var string phpEx */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface	$db				Database object
	* @param \phpbb\controller\helper			$helper			Controller helper object
	* @param \phpbb\template\template			$template		Template object
	* @param \phpbb\user						$user			User object
	* @param string								$table_prefix 	prefix table
	* @param string								$php_ext 		phpEx
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, $table_prefix, $php_ext)
	{
		$this->db			= $db;
		$this->helper 		= $helper;
		$this->template 	= $template;
		$this->user 		= $user;
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
			'core.common'            				=> 'set_constants_tables',
			'core.delete_user_after'				=> 'update_links_with_anonymous',
			'core.page_header'        				=> 'add_page_header_link',
			'core.permissions'						=> 'permissions_add_directory',
			'core.user_setup'						=> 'load_language_on_setup',
			'core.viewonline_overwrite_location'	=> 'add_page_viewonline'
		);
	}

	/**
	* Define table constants
	*
	* @return null
	*/
	public function set_constants_tables()
	{
		define('DIR_CAT_TABLE', $this->table_prefix.'directory_cats');
		define('DIR_COMMENT_TABLE', $this->table_prefix.'directory_comments');
		define('DIR_LINK_TABLE', $this->table_prefix.'directory_links');
		define('DIR_VOTE_TABLE', $this->table_prefix.'directory_votes');
		define('DIR_WATCH_TABLE', $this->table_prefix.'directory_watch');
	}

	/**
	* Display links to Directory
	*
	* @return null
	*/
	public function add_page_header_link()
	{
		$this->template->assign_vars(array(
			'U_DIRECTORY'	=> $this->helper->route('ernadoo_phpbbdirectory_base_controller'),
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
			$event['location']		= $this->user->lang['DIRECTORY'];
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
		$categories				= $event['categories'];
		$categories				= array_merge($categories, array('dir' => 'ACL_CAT_DIRECTORY'));
		$event['categories']	= $categories;

		$permissions = $event['permissions'];

		$permissions = array_merge($permissions, array(
			'm_delete_dir'			=> array('lang' => 'ACL_M_DELETE_DIR', 			'cat' => 'dir'),
			'm_delete_comment_dir'	=> array('lang' => 'ACL_M_DELETE_COMMENT_DIR',	'cat' => 'dir'),
			'm_edit_dir'			=> array('lang' => 'ACL_M_EDIT_DIR',			'cat' => 'dir'),
			'm_edit_comment_dir'	=> array('lang' => 'ACL_M_EDIT_COMMENT_DIR',	'cat' => 'dir'),

			'u_comment_dir'			=> array('lang' => 'ACL_U_COMMENT_DIR',			'cat' => 'dir'),
			'u_delete_dir'			=> array('lang' => 'ACL_U_DELETE_DIR',			'cat' => 'dir'),
			'u_delete_comment_dir'	=> array('lang' => 'ACL_U_DELETE_COMMENT_DIR',	'cat' => 'dir'),
			'u_edit_dir'			=> array('lang' => 'ACL_U_EDIT_DIR',			'cat' => 'dir'),
			'u_edit_comment_dir'	=> array('lang' => 'ACL_U_EDIT_COMMENT_DIR',	'cat' => 'dir'),
			'u_search_dir'			=> array('lang' => 'ACL_U_SEARCH_DIR',			'cat' => 'dir'),
			'u_submit_dir'			=> array('lang' => 'ACL_U_SUBMIT_DIR',			'cat' => 'dir'),
			'u_vote_dir'			=> array('lang' => 'ACL_U_VOTE_DIR',			'cat' => 'dir'),
		));

		$event['permissions'] = $permissions;
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

		$sql = 'UPDATE ' . DIR_COMMENT_TABLE . '
			SET comment_user_id = ' . ANONYMOUS . '
			WHERE ' . $this->db->sql_in_set('comment_user_id', $user_ids);
		$this->db->sql_query($sql);

		$sql = 'UPDATE ' . DIR_LINK_TABLE . '
			SET link_user_id = ' . ANONYMOUS . '
			WHERE ' . $this->db->sql_in_set('link_user_id', $user_ids);
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . DIR_WATCH_TABLE . '
			WHERE ' . $this->db->sql_in_set('user_id', $user_ids);
		$this->db->sql_query($sql);
	}
}

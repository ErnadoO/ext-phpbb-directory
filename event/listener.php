<?php
/**
 *
 * @package phpBB Directory
 * @copyright (c) 2014 ErnadoO
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

namespace ernadoo\phpbbdirectory\event;

/**
 * Event listener
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $table_prefix;

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper $helper Controller helper object
	* @param \phpbb\template\template $template Template object
	* @param \phpbb\user $user User object
	* @param string $table_prefix prefix table
	* @return \ernadoo\phpbbdirectory\event
	* @access public
	*/
	public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, $table_prefix )
	{
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->table_prefix = $table_prefix;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
		   'core.common'            	=> 'set_constants_tables',
		   'core.page_header'        	=> 'add_page_header_link',

		   'core.permissions'			=> 'permissions_add_directory',
		);
	}

	public function set_constants_tables($event)
	{
		$this->user->add_lang_ext('ernadoo/phpbbdirectory', 'directory_common');

		define('DIR_CAT_TABLE',			$this->table_prefix.'directory_cats');
		define('DIR_COMMENT_TABLE',		$this->table_prefix.'directory_comments');
		define('DIR_LINK_TABLE',		$this->table_prefix.'directory_links');
		define('DIR_VOTE_TABLE',		$this->table_prefix.'directory_votes');
		define('DIR_WATCH_TABLE',		$this->table_prefix.'directory_watch');
	}

	public function add_page_header_link($event)
	{
		$this->template->assign_vars(array(
		   'U_DIRECTORY'   				=> $this->helper->route('phpbbdirectory_base_controller'),
		));
	}

	public function permissions_add_directory($event)
	{
		$categories = $event['categories'];
		$categories = array_merge($categories, array('dir' => 'ACL_CAT_DIRECTORY'));
		$event['categories'] = $categories;

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
}

<?php
/**
 *
 * @package phpBB Directory
 * @copyright (c) 2014 ErnadoO
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace ernadoo\phpbbdirectory\core;

/**
 * categorie class
 * @package phpBB3
 */
class categorie
{
	private $id	= 0;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\ext\ernadoo\phpbbdirectory\core\helper */
	protected $dir_path_helper;

	/** @var string */
	protected $container;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	public $data = array();


	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\config\config $config
	 * @param \phpbb\template\template $template
	 * @param \phpbb\user $user
	 * @param \phpbb\controller\helper $controller_helper
	 * @param \phpbb\request\request $request
	 * @param \phpbb\auth\auth $auth
	 * @param string         $container   container
	 * @param string         $root_path   phpBB root path
	 * @param string         $php_ext   phpEx
	 */
	function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\auth\auth $auth, $dir_path_helper, $container, $root_path, $php_ext)
	{
		$this->db			= $db;
		$this->config		= $config;
		$this->template		= $template;
		$this->user			= $user;
		$this->helper		= $helper;
		$this->request		= $request;
		$this->auth			= $auth;
		$this->dir_helper	= $dir_path_helper;
		$this->container 	= $container;
		$this->root_path	= $root_path;
		$this->php_ext		= $php_ext;
	}

	/**
	 * Function for get approval setting
	 * used in edit mode for test the setting of new category's link
	 */
	function need_approval()
	{
		return (int)$this->data['cat_validate'];
	}

	/**
	 * Generate a list of directory'scategories
	 *
	 * @param int $select_id is selected cat
	 * @retur html code
	 */
	function make_cat_select($select_id = false, $ignore_id = false)
	{
		// This query is identical to the jumpbox one
		$sql = 'SELECT cat_id, cat_name, parent_id, left_id, right_id
			FROM ' . DIR_CAT_TABLE . '
			ORDER BY left_id ASC';
		$result = $this->db->sql_query($sql, 600);

		$right = 0;
		$padding_store = array('0' => '');
		$padding = '';
		$cat_list = '';

		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($row['left_id'] < $right)
			{
				$padding .= '&nbsp; &nbsp;';
				$padding_store[$row['parent_id']] = $padding;
			}
			else if ($row['left_id'] > $right + 1)
			{
				$padding = (isset($padding_store[$row['parent_id']])) ? $padding_store[$row['parent_id']] : '';
			}

			$right = $row['right_id'];
			$disabled = false;

			if (((is_array($ignore_id) && in_array($row['cat_id'], $ignore_id)) || $row['cat_id'] == $ignore_id))
			{
				$disabled = true;
			}

			$selected = (($row['cat_id'] == $select_id) ? ' selected="selected"' : '');
			$cat_list .= '<option value="' . $row['cat_id'] . '"' . (($disabled) ? ' disabled="disabled" class="disabled-option"' : $selected) . '>' . $padding . $row['cat_name'] . '</option>';
		}
		$this->db->sql_freeresult($result);
		unset($padding_store);

		return $cat_list;
	}

	/**
	 * Display cat or subcat
	 */
	function display($id = 0)
	{
		$cat_rows	= $subcats = array();
		$parent_id	= $visible_cats = 0;

		$sql_array = array(
			'SELECT'	=> 'cat_id, left_id, right_id, parent_id, cat_name, cat_desc, display_subcat_list, cat_desc_uid, cat_desc_bitfield, cat_desc_options, cat_links, cat_icon, cat_count_all',
			'FROM'		=> array(
				DIR_CAT_TABLE => ''
			),
		);

		if (!$this->data)
		{
			$root_data = array('cat_id' => 0);
			$sql_where = '';
		}
		else
		{
			$root_data = $this->data;
			$sql_where = 'left_id > ' . $root_data['left_id'] . ' AND left_id < ' . $root_data['right_id'];
		}

		$sql = $this->db->sql_build_query('SELECT', array(
			'SELECT'	=> $sql_array['SELECT'],
			'FROM'		=> $sql_array['FROM'],

			'WHERE'		=> $sql_where,

			'ORDER_BY'	=> 'left_id',
		));

		$result = $this->db->sql_query($sql);

		$branch_root_id = $root_data['cat_id'];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$dir_cat_id = $row['cat_id'];

			if ($row['parent_id'] == $root_data['cat_id'] || $row['parent_id'] == $branch_root_id)
			{
				// Direct child of current branch
				$parent_id = $dir_cat_id;
				$cat_rows[$dir_cat_id] = $row;
			}
			else
			{
				$subcats[$parent_id][$dir_cat_id]['display'] = ($row['display_subcat_list']) ? true : false;
				$subcats[$parent_id][$dir_cat_id]['name'] = $row['cat_name'];
				$subcats[$parent_id][$dir_cat_id]['links'] = $row['cat_links'];
				$subcats[$parent_id][$dir_cat_id]['parent_id'] = $row['parent_id'];
			}
		}
		$this->db->sql_freeresult($result);

		// Used to tell whatever we have to create a dummy category or not.
		$last_catless = true;

		foreach ($cat_rows as $row)
		{
			$visible_cats++;
			$dir_cat_id = $row['cat_id'];

			$folder_image = $folder_alt = '';
			$subcats_list = array();

			// Generate list of subcats if we need to
			if (isset($subcats[$dir_cat_id]))
			{
				foreach ($subcats[$dir_cat_id] as $subcat_id => $subcat_row)
				{
					$row['cat_links'] = ($row['cat_count_all']) ? ($row['cat_links']+$subcat_row['links']) : $row['cat_links'];

					if ($subcat_row['display'] && $subcat_row['parent_id'] == $dir_cat_id)
					{
						$subcats_list[] = array(
							'link'		=> $this->helper->route('phpbbdirectory_page1_controller', array('cat_id' => (int)$subcat_id)),
							'name'		=> $subcat_row['name'],
							'links'		=> $subcat_row['links']
						);
					}
					else
					{
						unset($subcats[$dir_cat_id][$subcat_id]);
					}
				}
			}

			$this->template->assign_block_vars('cat', array(
				'CAT_NAME'				=> $row['cat_name'],
				'CAT_DESC'				=> generate_text_for_display($row['cat_desc'], $row['cat_desc_uid'], $row['cat_desc_bitfield'], $row['cat_desc_options']),
				'CAT_LINKS'				=> $row['cat_links'],
				'CAT_IMG'				=> $this->dir_helper->get_img_path('icons', $row['cat_icon']),

				'U_CAT'					=> $this->helper->route('phpbbdirectory_page1_controller', array('cat_id' => (int)$row['cat_id'])),
			));

			// Assign subcats loop for style authors
			foreach ($subcats_list as $subcat)
			{
				$this->template->assign_block_vars('cat.subcat', array(
					'U_CAT'		=> $subcat['link'],
					'CAT_NAME'	=> $subcat['name'],
					'CAT_LINKS'	=> $subcat['links']
				));
			}
		}

		$this->template->assign_vars(array(
			'S_AUTH_ADD'		=> $this->auth->acl_get('u_submit_dir'),
			'S_AUTH_SEARCH'		=> $this->auth->acl_get('u_search_dir'),
			'S_HAS_SUBCAT'		=> ($visible_cats) ? true : false,

			'U_MAKE_SEARCH'		=> $this->helper->route('phpbbdirectory_search_controller'),
			'U_NEW_SITE' 		=> (!empty($this->data)) ? $this->helper->route('phpbbdirectory_new_controller', array('cat_id' => $this->data['cat_id'])) : false,
		));

		// Do the categorie Prune thang - cron type job ...
		if (!$this->config['use_system_cron'])
		{
			$cron = $this->container->get('cron.manager');

			$task = $cron->find_task('cron.task.core.prune_categorie');
			$task->set_categorie_data($this->data);

			if ($task->is_ready())
			{
				$url = $task->get_url();
				$template->assign_var('RUN_CRON_TASK', '<img src="' . $url . '" width="1" height="1" alt="cron" />');
			}
		}
	}

	public function get($cat_id = 0)
	{
		if ($cat_id)
		{
			$sql_array = array(
				'SELECT'	=> 'c.*, w.notify_status',
				'FROM'		=> array(
						DIR_CAT_TABLE	=> 'c'),
				'LEFT_JOIN'	=> array(
						array(
							'FROM'	=> array(DIR_WATCH_TABLE	=> 'w'),
							'ON'	=> 'c.cat_id = w.cat_id AND w.user_id = ' . (int)$this->user->data['user_id']
						),
				),
				'WHERE'		=> 'c.cat_id = ' . (int)$cat_id
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			if(!$this->data = $this->db->sql_fetchrow($result))
			{
				send_status_line(410, 'Gone');
				return $this->helper->error($this->user->lang['DIR_ERROR_NO_CATS']);
			}
			$this->db->sql_freeresult($result);
		}
	}

	/**
	 * Generate directory navigation for navbar
	 */
	public function generate_dir_nav(&$dir_cat_data)
	{
		// Get cat parents
		$dir_cat_parents = $this->_get_cat_parents($dir_cat_data);

		// Build navigation links
		if (!empty($dir_cat_parents))
		{
			foreach ($dir_cat_parents as $parent_cat_id => $parent_name)
			{
				$this->template->assign_block_vars('dir_navlinks', array(
					'FORUM_NAME'	=> $parent_name,
					'FORUM_ID'		=> $parent_cat_id,
					'U_VIEW_FORUM'	=> $this->helper->route('phpbbdirectory_page1_controller', array('cat_id' => (int)$parent_cat_id)),
				));
			}
		}

		$this->template->assign_block_vars('dir_navlinks', array(
			'FORUM_NAME'	=> $dir_cat_data['cat_name'],
			'FORUM_ID'		=> $dir_cat_data['cat_id'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbdirectory_page1_controller', array('cat_id' => (int)$dir_cat_data['cat_id'])),
		));

		return;
	}

	/**
	 * Returns cat parents as an array. Get them from cat_data if available, or update the database otherwise
	 *
	 * @param array $dir_cat_data fatas from db
	 */
	private function _get_cat_parents(&$dir_cat_data)
	{
		$dir_cat_parents = array();

		if ($dir_cat_data['parent_id'] > 0)
		{
			if ($dir_cat_data['cat_parents'] == '')
			{
				$sql = 'SELECT cat_id, cat_name
					FROM ' . DIR_CAT_TABLE . '
					WHERE left_id < ' . (int)$dir_cat_data['left_id'] . '
						AND right_id > ' . (int)$dir_cat_data['right_id'] . '
					ORDER BY left_id ASC';
				$result = $this->db->sql_query($sql);

				while ($row = $this->db->sql_fetchrow($result))
				{
					$dir_cat_parents[$row['cat_id']] = $row['cat_name'];
				}
				$this->db->sql_freeresult($result);

				$dir_cat_data['cat_parents'] = serialize($dir_cat_parents);

				$sql = 'UPDATE ' . DIR_CAT_TABLE . "
					SET cat_parents = '" . $this->db->sql_escape($dir_cat_data['cat_parents']) . "'
					WHERE parent_id = " . (int)$dir_cat_data['parent_id'];
				$this->db->sql_query($sql);
			}
			else
			{
				$dir_cat_parents = unserialize($dir_cat_data['cat_parents']);
			}
		}

		return $dir_cat_parents;
	}

	/*
	* Return good key language
	*
	* @param int $validate true if approbation needed before publication
	*/
	public function dir_submit_type($validate)
	{
		if ($validate && !$this->auth->acl_get('a_'))
		{
			return ($this->user->lang['DIR_SUBMIT_TYPE_1']);
		}
		else if (!$validate && !$this->auth->acl_get('a_'))
		{
			return ($this->user->lang['DIR_SUBMIT_TYPE_2']);
		}
		else if ($this->auth->acl_get('a_'))
		{
			return ($this->user->lang['DIR_SUBMIT_TYPE_3']);
		}
		else if ($this->auth->acl_get('m_'))
		{
			return ($this->user->lang['DIR_SUBMIT_TYPE_4']);
		}
		return $this->helper->error($this->user->lang['DIR_ERROR_SUBMIT_TYPE']);
	}

	/**
	 * Topic and forum watching common code
	 */
	public function watch_categorie($mode, &$s_watching, $user_id, $cat_id, $notify_status, $item_title = '')
	{
		$is_watching = 0;

		// Is user watching this thread?
		if ($user_id != ANONYMOUS)
		{
			$can_watch = true;

			if (!is_null($notify_status) && $notify_status !== '')
			{
				if ($mode == 'unwatch')
				{
					$sql = 'DELETE FROM ' . DIR_WATCH_TABLE . "
						WHERE cat_id = $cat_id
							AND user_id = $user_id";
					$this->db->sql_query($sql);

					$redirect_url = $this->helper->route('phpbbdirectory_page1_controller', array('cat_id' => (int)$cat_id));
					$message = $this->user->lang['DIR_NOT_WATCHING_CAT'];

					if (!$this->request->is_ajax())
					{
						$message .= '<br /><br />' . $this->user->lang('DIR_CLICK_RETURN_CAT', '<a href="' . $redirect_url . '">', '</a>');
					}
					meta_refresh(3, $redirect_url);
					return $this->helper->error($message);

				}
				else
				{
					$is_watching = true;

					if ($notify_status != NOTIFY_YES)
					{
						$sql = 'UPDATE ' . DIR_WATCH_TABLE . "
						SET notify_status = " . NOTIFY_YES . "
						WHERE cat_id = $cat_id
							AND user_id = $user_id";
						$this->db->sql_query($sql);
					}
				}
			}
			else
			{
				if ($mode == 'watch')
				{
					$uid = request_var('uid', 0);
					$token = request_var('hash', '');

					$is_watching = true;

					$sql = 'INSERT INTO ' . DIR_WATCH_TABLE . " (user_id, cat_id, notify_status)
					VALUES ($user_id, $cat_id, " . NOTIFY_YES . ')';
					$this->db->sql_query($sql);

					$redirect_url = $this->helper->route('phpbbdirectory_page1_controller', array('cat_id' => (int)$cat_id));
					$message = $this->user->lang['DIR_ARE_WATCHING_CAT'];

					if (!$this->request->is_ajax())
					{
						$message .= '<br /><br />' . $this->user->lang('DIR_CLICK_RETURN_CAT', '<a href="' . $redirect_url . '">', '</a>');
					}
					meta_refresh(3, $redirect_url);
					return $this->helper->error($message);
				}
				else
				{
					$is_watching = 0;
				}
			}
		}
		else
		{
			$can_watch = 0;
			$is_watching = 0;
		}

		if ($can_watch)
		{
			$s_watching['link'] 		= $this->helper->route('phpbbdirectory_suscribe_controller', array('cat_id' => $cat_id, 'mode' => (($is_watching) ? 'unwatch' : 'watch')));
			$s_watching['link_toggle'] 		= $this->helper->route('phpbbdirectory_suscribe_controller', array('cat_id' => $cat_id, 'mode' => ((!$is_watching) ? 'unwatch' : 'watch')));
			$s_watching['title'] 		= $this->user->lang[(($is_watching) ? 'DIRE_STOP' : 'DIRE_START') . '_WATCHING_CAT'];
			$s_watching['title_toggle'] = $this->user->lang[((!$is_watching) ? 'DIRE_STOP' : 'DIRE_START') . '_WATCHING_CAT'];
			$s_watching['is_watching'] 	= $is_watching;
		}

		return;
	}

	static public function getname($cat_id)
	{
		global $db;

		$sql = 'SELECT cat_name FROM ' . DIR_CAT_TABLE . '
			WHERE cat_id = ' . (int)$cat_id;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);

		if(!empty($row))
		{
			return $row['cat_name'];
		}
	}
}

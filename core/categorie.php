<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\core;

use \ernadoo\phpbbdirectory\core\helper;

class categorie extends helper
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\language\language */
	protected $language;

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

	/** @var \phpbb\cron\manager */
	protected $cron;


	/** @var array data */
	public $data = array();

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface 		$db			Database object
	* @param \phpbb\config\config 					$config		Config object
	* @param \phpbb\language\language				$language	Language object
	* @param \phpbb\template\template 				$template	Template object
	* @param \phpbb\user 							$user		User object
	* @param \phpbb\controller\helper 				$helper		Controller helper object
	* @param \phpbb\request\request 				$request	Request object
	* @param \phpbb\auth\auth 						$auth		Auth object
	* @param \phpbb\cron\manager					$cron		Cron object
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\language\language $language, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\auth\auth $auth, \phpbb\cron\manager $cron)
	{
		$this->db			= $db;
		$this->config		= $config;
		$this->language		= $language;
		$this->template		= $template;
		$this->user			= $user;
		$this->helper		= $helper;
		$this->request		= $request;
		$this->auth			= $auth;
		$this->cron 		= $cron;
	}

	/**
	* Function for get approval setting
	* used in edit mode for test the setting of new category's link
	*
	* @return bool
	*/
	public function need_approval()
	{
		return (bool) $this->data['cat_validate'];
	}

	/**
	* Generate Jumpbox
	*
	* @return null
	*/
	public function make_cat_jumpbox()
	{
		$sql = 'SELECT cat_id, cat_name, parent_id, left_id, right_id
			FROM ' . $this->categories_table . '
			ORDER BY left_id ASC';
		$result = $this->db->sql_query($sql, 600);

		$right = $padding = 0;
		$padding_store = array('0' => 0);
		$display_jumpbox = false;
		$iteration = 0;

		while ($row = $this->db->sql_fetchrow($result))
		{
			$display_jumpbox = true;

			if ($row['left_id'] < $right)
			{
				$padding++;
				$padding_store[$row['parent_id']] = $padding;
			}
			else if ($row['left_id'] > $right + 1)
			{
				$padding = (isset($padding_store[$row['parent_id']])) ? $padding_store[$row['parent_id']] : $padding;
			}

			$right = $row['right_id'];

			$this->template->assign_block_vars('jumpbox_forums', array(
				'FORUM_ID'		=> $row['cat_id'],
				'FORUM_NAME'	=> $row['cat_name'],
				'S_FORUM_COUNT'	=> $iteration,
				'LINK'			=> $this->helper->route('ernadoo_phpbbdirectory_dynamic_route_' . $row['cat_id']),
			));

			for ($i = 0; $i < $padding; $i++)
			{
				$this->template->assign_block_vars('jumpbox_forums.level', array());
			}
			$iteration++;
		}
		$this->db->sql_freeresult($result);
		unset($padding_store);

		$this->template->assign_vars(array(
			'S_DISPLAY_JUMPBOX'			=> $display_jumpbox,
		));

		return;
	}

	/**
	* Generate a list of directory's categories
	*
	* @param	int		$select_id		Selected category
	* @param	array	$ignore_id		Array of ignored categories
	* @return	string	$cat_list		html code
	*/
	public function make_cat_select($select_id = 0, $ignore_id = array())
	{
		$ignore_id = is_array($ignore_id) ? $ignore_id : array($ignore_id);

		// This query is identical to the jumpbox one
		$sql = 'SELECT cat_id, cat_name, parent_id, left_id, right_id
			FROM ' . $this->categories_table . '
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

			if (in_array($row['cat_id'], $ignore_id))
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
	*
	* @return	null
	*/
	public function display()
	{
		$cat_rows	= $subcats = array();
		$parent_id	= $visible_cats = 0;

		$sql_array = array(
			'SELECT'	=> 'cat_id, left_id, right_id, parent_id, cat_name, cat_desc, display_subcat_list, cat_desc_uid, cat_desc_bitfield, cat_desc_options, cat_links, cat_icon, cat_count_all',
			'FROM'		=> array(
				$this->categories_table => ''
			),
		);

		if (empty($this->data))
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

		foreach ($cat_rows as $row)
		{
			$visible_cats++;
			$dir_cat_id = $row['cat_id'];

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
							'link'		=> $this->helper->route('ernadoo_phpbbdirectory_dynamic_route_' . $subcat_id),
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
				'CAT_IMG'				=> $this->get_img_path('icons', $row['cat_icon']),

				'U_CAT'					=> $this->helper->route('ernadoo_phpbbdirectory_dynamic_route_' . $row['cat_id']),
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
			'S_ROOT'			=> empty($this->data),

			'U_MAKE_SEARCH'		=> $this->helper->route('ernadoo_phpbbdirectory_search_controller'),
		));

		// Do the categorie Prune thang - cron type job ...
		if (!$this->config['use_system_cron'])
		{
			$task = $this->cron->find_task('ernadoo.phpbbdirectory.cron.task.core.prune_categorie');
			$task->set_categorie_data($this->data);

			if ($task->is_ready())
			{
				$url = $task->get_url();
				$this->template->assign_var('RUN_CRON_TASK', '<img src="' . $url . '" width="1" height="1" alt="" />');
			}
		}
	}

	/**
	* Get informations about a cat or subcat
	*
	* @param	int	$cat_id		The category ID
	* @return	null|false
	*/
	public function get($cat_id = 0)
	{
		if ($cat_id)
		{
			$sql_array = array(
				'SELECT'	=> 'c.*, w.notify_status',
				'FROM'		=> array(
						$this->categories_table	=> 'c'),
				'LEFT_JOIN'	=> array(
						array(
							'FROM'	=> array($this->watch_table	=> 'w'),
							'ON'	=> 'c.cat_id = w.cat_id AND w.user_id = ' . (int) $this->user->data['user_id']
						),
				),
				'WHERE'		=> 'c.cat_id = ' . (int) $cat_id
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			if (!($this->data = $this->db->sql_fetchrow($result)))
			{
				return false;
			}
			$this->db->sql_freeresult($result);
		}
	}

	/**
	* Create category navigation links for given category, create parent
	* list if currently null, assign basic category info to template
	*
	* @param	array	$dir_cat_data
	*/
	public function generate_dir_nav(&$dir_cat_data)
	{
		global $phpbb_container;

		$nestedset_category = $phpbb_container->get('ernadoo.phpbbdirectory.core.nestedset_category');

		// Get cat parents
		$dir_cat_parents = $nestedset_category->get_path_basic_data($dir_cat_data);

		$microdata_attr = 'data-category-id';

		// Build navigation links
		if (!empty($dir_cat_parents))
		{
			foreach ($dir_cat_parents as $parent_cat_id => $parent_data)
			{
				$this->template->assign_block_vars('dir_navlinks', array(
					'FORUM_NAME'	=> $parent_data['cat_name'],
					'FORUM_ID'		=> $parent_cat_id,
					'MICRODATA'		=> $microdata_attr . '="' . $parent_cat_id . '"',
					'U_VIEW_FORUM'	=> $this->helper->route('ernadoo_phpbbdirectory_dynamic_route_' . $parent_cat_id),
				));
			}
		}

		$this->template->assign_block_vars('dir_navlinks', array(
			'FORUM_NAME'	=> $dir_cat_data['cat_name'],
			'FORUM_ID'		=> $dir_cat_data['cat_id'],
			'MICRODATA'		=> $microdata_attr . '="' . $dir_cat_data['cat_id'] . '"',
			'U_VIEW_FORUM'	=> $this->helper->route('ernadoo_phpbbdirectory_dynamic_route_' . $dir_cat_data['cat_id']),
		));

		return;
	}

	/**
	* Return good key language
	*
	* @param	bool	$validate	True if approbation needed before publication
	* @return	string				Information about approval, depends on user auth level
	* @throws	\phpbb\exception\runtime_exception
	*/
	public function dir_submit_type($validate)
	{
		if ($validate && !$this->auth->acl_get('a_'))
		{
			return $this->language->lang('DIR_SUBMIT_TYPE_1');
		}
		else if (!$validate && !$this->auth->acl_get('a_'))
		{
			return $this->language->lang('DIR_SUBMIT_TYPE_2');
		}
		else if ($this->auth->acl_get('a_'))
		{
			return $this->language->lang('DIR_SUBMIT_TYPE_3');
		}
		else if ($this->auth->acl_get('m_'))
		{
			return $this->language->lang('DIR_SUBMIT_TYPE_4');
		}

		throw new \phpbb\exception\runtime_exception('DIR_ERROR_SUBMIT_TYPE');
	}

	/**
	* Category watching common code
	*
	* @param	string		$mode			Watch or unwatch a category
	* @param	array		$s_watching		An empty array, passed by reference
	* @param	int			$user_id		The user ID
	* @param	int			$cat_id			The category ID
	* @param	string		$notify_status	User is watching the category?
	* @return	null|string
	*/
	public function watch_categorie($mode, &$s_watching, $user_id, $cat_id, $notify_status)
	{
		// Is user watching this thread?
		if ($user_id != ANONYMOUS)
		{
			$can_watch = true;

			if (!is_null($notify_status) && $notify_status !== '')
			{
				if ($mode == 'unwatch')
				{
					$sql = 'DELETE FROM ' . $this->watch_table . "
						WHERE cat_id = $cat_id
							AND user_id = $user_id";
					$this->db->sql_query($sql);

					$redirect_url = $this->helper->route('ernadoo_phpbbdirectory_dynamic_route_' . $cat_id);
					$message = $this->language->lang('DIR_NOT_WATCHING_CAT');

					if (!$this->request->is_ajax())
					{
						$message .= '<br /><br />' . $this->language->lang('DIR_CLICK_RETURN_CAT', '<a href="' . $redirect_url . '">', '</a>');
					}

					meta_refresh(3, $redirect_url);
					return $message;
				}
				else
				{
					$is_watching = true;

					if ($notify_status != NOTIFY_YES)
					{
						$sql = 'UPDATE ' . $this->watch_table . '
							SET notify_status = ' . NOTIFY_YES . "
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
					$sql = 'INSERT INTO ' . $this->watch_table . " (user_id, cat_id, notify_status)
						VALUES ($user_id, $cat_id, " . NOTIFY_YES . ')';
					$this->db->sql_query($sql);

					$redirect_url = $this->helper->route('ernadoo_phpbbdirectory_dynamic_route_' . $cat_id);
					$message = $this->language->lang('DIR_ARE_WATCHING_CAT');

					if (!$this->request->is_ajax())
					{
						$message .= '<br /><br />' . $this->language->lang('DIR_CLICK_RETURN_CAT', '<a href="' . $redirect_url . '">', '</a>');
					}

					meta_refresh(3, $redirect_url);
					return $message;
				}
				else
				{
					$is_watching = false;
				}
			}
		}
		else
		{
			$can_watch = false;
			$is_watching = false;
		}

		if ($can_watch)
		{
			$s_watching['link'] 		= $this->helper->route('ernadoo_phpbbdirectory_suscribe_controller', array('cat_id' => $cat_id, 'mode' => (($is_watching) ? 'unwatch' : 'watch')));
			$s_watching['link_toggle'] 	= $this->helper->route('ernadoo_phpbbdirectory_suscribe_controller', array('cat_id' => $cat_id, 'mode' => ((!$is_watching) ? 'unwatch' : 'watch')));
			$s_watching['title'] 		= $this->language->lang((($is_watching) ? 'DIR_STOP' : 'DIR_START') . '_WATCHING_CAT');
			$s_watching['title_toggle'] = $this->language->lang(((!$is_watching) ? 'DIR_STOP' : 'DIR_START') . '_WATCHING_CAT');
			$s_watching['is_watching'] 	= $is_watching;
		}

		return;
	}

	/**
	* Return Category name
	*
	* @param	int		$cat_id		The category ID
	* @return	string				The category name
	*/
	static public function getname($cat_id)
	{
		global $db, $phpbb_container;

		$categories_table = $phpbb_container->getParameter('tables.dir.categories');

		$sql = 'SELECT cat_name
			FROM ' . $categories_table . '
			WHERE cat_id = ' . (int) $cat_id;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);

		if (!empty($row))
		{
			return $row['cat_name'];
		}
	}
}

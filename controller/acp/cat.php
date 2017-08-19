<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\controller\acp;

use \ernadoo\phpbbdirectory\core\helper;

class cat extends helper
{
	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $phpbb_log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \ernadoo\phpbbdirectory\core\categorie */
	protected $categorie;

	/** @var \ernadoo\phpbbdirectory\core\nestedset_category */
	protected $nestedset_category;

	/** @var string Custom form action */
	protected $u_action;

	/** @var string */
	private $action;

	/** @var array */
	private $cat_data = array();

	/** @var int */
	private $cat_id;

	/** @var array */
	private $errors;

	/** @var string */
	private $form_key;

	/** @var int */
	private $parent_id;

	/** @var bool */
	private $update;

	/**
	* Constructor
	*
	* @param \phpbb\cache\service								$cache				Cache object
	* @param \phpbb\db\driver\driver_interface 					$db					Database object
	* @param \phpbb\controller\helper							$helper				Helper object
	* @param \phpbb\language\language							$language			Language object
	* @param \phpbb\log\log										$log				Log object
	* @param \phpbb\request\request								$request			Request object
	* @param \phpbb\template\template							$template			Template object
	* @param \phpbb\user										$user				User object
	* @param \ernadoo\phpbbdirectory\core\categorie				$categorie			PhpBB Directory extension categorie object
	* @param \ernadoo\phpbbdirectory\core\nestedset_category	$nestedset_category	PhpBB Directory extension nestedset object
	*/
	public function __construct(\phpbb\cache\service $cache, \phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\language\language $language, \phpbb\log\log $log, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \ernadoo\phpbbdirectory\core\categorie $categorie, \ernadoo\phpbbdirectory\core\nestedset_category $nestedset_category)
	{
		$this->cache				= $cache;
		$this->db					= $db;
		$this->helper				= $helper;
		$this->language				= $language;
		$this->phpbb_log			= $log;
		$this->request				= $request;
		$this->template				= $template;
		$this->user					= $user;
		$this->categorie			= $categorie;
		$this->nestedset_category	= $nestedset_category;

		$this->form_key = 'acp_dir_cat';
		add_form_key($this->form_key);

		$this->action		= $this->request->variable('action', '');
		$this->cat_id		= $request->variable('c', 0);
		$this->parent_id	= $request->variable('parent_id', 0);
		$this->update		= ($this->request->is_set_post('update')) ? true : false;
	}

	/**
	* Initialize defaults data for add page
	*
	* @return null
	*/
	public function action_add()
	{
		$this->cat_id = $this->parent_id;
		$parents_list = $this->categorie->make_cat_select($this->parent_id);

		// Fill categorie data with default values
		if (!$this->update)
		{
			$this->cat_data = array(
				'parent_id'				=> $this->parent_id,
				'cat_name'				=> $this->request->variable('cat_name', '', true),
				'cat_route'				=> '',
				'cat_desc'				=> '',
				'cat_icon'				=> '',
				'cat_allow_comments'	=> true,
				'cat_allow_votes'		=> true,
				'cat_must_describe'		=> true,
				'cat_count_all'			=> false,
				'cat_validate'			=> false,
				'enable_icons'			=> false,

				'display_subcat_list'	=> true,

				'cat_link_back'			=> false,
				'cat_cron_enable'		=> false,
				'cat_cron_freq'			=> 7,
				'cat_cron_nb_check'		=> 1,
			);
		}

		$this->_display_cat_form($parents_list);
	}

	/**
	* Display deleting page
	*
	* @return null
	*/
	public function action_delete()
	{
		if (!$this->cat_id)
		{
			trigger_error($this->language->lang('DIR_NO_CAT') . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
		}

		$this->cat_data = $this->_get_cat_info($this->cat_id);

		$subcats_id = array();
		$subcats = $this->nestedset_category->get_subtree_data($this->cat_id);

		foreach ($subcats as $row)
		{
			$subcats_id[] = $row['cat_id'];
		}

		$cat_list = $this->categorie->make_cat_select((int) $this->cat_data['parent_id'], $subcats_id);

		$sql = 'SELECT cat_id
			FROM ' . $this->categories_table . '
			WHERE cat_id <> ' . (int) $this->cat_id;
		$result = $this->db->sql_query_limit($sql, 1);

		if ($this->db->sql_fetchrow($result))
		{
			$this->template->assign_vars(array(
				'S_MOVE_DIR_CAT_OPTIONS'	=> $this->categorie->make_cat_select((int) $this->cat_data['parent_id'], $subcats_id))
			);
		}
		$this->db->sql_freeresult($result);

		$parent_id = ($this->parent_id == $this->cat_id) ? 0 : $this->parent_id;

		$this->template->assign_vars(array(
			'S_DELETE_DIR_CAT'		=> true,
			'U_ACTION'				=> $this->u_action . "&amp;parent_id={$parent_id}&amp;action=delete&amp;c=$this->cat_id",
			'U_BACK'				=> $this->u_action . '&amp;parent_id=' . $this->parent_id,

			'DIR_CAT_NAME'			=> $this->cat_data['cat_name'],
			'S_HAS_SUBCATS'			=> ($this->cat_data['right_id'] - $this->cat_data['left_id'] > 1) ? true : false,
			'S_CATS_LIST'			=> $cat_list,
			'S_ERROR'				=> (sizeof($this->errors)) ? true : false,
			'ERROR_MSG'				=> (sizeof($this->errors)) ? implode('<br />', $this->errors) : '')
		);

		return;
	}

	/**
	* Initialize data for edit page
	*
	* @return null
	*/
	public function action_edit()
	{
		$row = $this->_get_cat_info($this->cat_id);

		if (!$this->update)
		{
			$this->cat_data = $row;
		}
		else
		{
			$this->cat_data['left_id'] = $row['left_id'];
			$this->cat_data['right_id'] = $row['right_id'];
		}

		// Make sure no direct child categories are able to be selected as parents.
		$exclude_cats = array();
		foreach ($this->nestedset_category->get_subtree_data($this->cat_id) as $row2)
		{
			$exclude_cats[] = $row2['cat_id'];
		}
		$parents_list = $this->categorie->make_cat_select((int) $this->cat_data['parent_id'], $exclude_cats);

		$this->_display_cat_form($parents_list);
	}

	/**
	* Move order categories
	*
	* @return null
	*/
	public function action_move()
	{
		if (!$this->cat_id)
		{
			trigger_error($this->language->lang('DIR_NO_CAT') . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
		}

		$sql = 'SELECT cat_id, cat_name, parent_id, left_id, right_id
			FROM ' . $this->categories_table . '
			WHERE cat_id = ' . (int) $this->cat_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			trigger_error($this->language->lang('DIR_NO_CAT') . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
		}

		try
		{
			$move_cat_name = $this->nestedset_category->{$this->action}($this->cat_id);
		}
		catch (\Exception $e)
		{
			trigger_error($e->getMessage(), E_USER_WARNING);
		}

		if ($move_cat_name !== false)
		{
			$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_' . strtoupper($this->action), time(), array($row['cat_name'], $move_cat_name));
			$this->cache->destroy('sql', $this->categories_table);
		}

		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array('success' => ($move_cat_name !== false)));
		}
	}

	/**
	* Display progress bar for syncinc categories
	*
	* @return null
	*/
	public function action_progress_bar()
	{
		$start = $this->request->variable('start', 0);
		$total = $this->request->variable('total', 0);

		adm_page_header($this->language->lang('SYNC_IN_PROGRESS'));

		$this->template->set_filenames(array(
			'body'	=> 'progress_bar.html')
		);

		$this->template->assign_vars(array(
			'L_PROGRESS'			=> $this->language->lang('SYNC_IN_PROGRESS'),
			'L_PROGRESS_EXPLAIN'	=> ($start && $total) ? $this->language->lang('SYNC_IN_PROGRESS_EXPLAIN', $start, $total) : $this->language->lang('SYNC_IN_PROGRESS'))
		);

		adm_page_footer();
	}

	/**
	* Get link's ID interval for _sync_dir_links()
	*
	* @return null
	*/
	public function action_sync()
	{
		if (!$this->cat_id)
		{
			trigger_error($this->language->lang('DIR_NO_CAT') . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
		}

		@set_time_limit(0);

		$sql = 'SELECT cat_name, cat_links
			FROM ' . $this->categories_table . '
			WHERE cat_id = ' . (int) $this->cat_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			trigger_error($this->language->lang('DIR_NO_CAT') . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
		}

		$sql = 'SELECT MIN(link_id) as min_link_id, MAX(link_id) as max_link_id
			FROM ' . $this->links_table . '
			WHERE link_cat = ' . (int) $this->cat_id . '
				AND link_active = 1';
		$result = $this->db->sql_query($sql);
		$row2 = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// Typecast to int if there is no data available
		$row2['min_link_id'] = (int) $row2['min_link_id'];
		$row2['max_link_id'] = (int) $row2['max_link_id'];

		$start = $this->request->variable('start', $row2['min_link_id']);

		$batch_size = 200;
		$end = $start + $batch_size;

		// Sync all links in batch mode...
		$this->_sync_dir_links($start, $end);

		if ($end < $row2['max_link_id'])
		{
			// We really need to find a way of showing statistics... no progress here
			$sql = 'SELECT COUNT(link_id) as num_links
				FROM ' . $this->links_table . '
				WHERE link_cat = ' . (int) $this->cat_id . '
						AND link_active = 1
						AND link_id BETWEEN ' . $start . ' AND ' . $end;
			$result = $this->db->sql_query($sql);
			$links_done = $this->request->variable('links_done', 0) + (int) $this->db->sql_fetchfield('num_links');
			$this->db->sql_freeresult($result);

			$start += $batch_size;

			$url = $this->u_action . "&amp;parent_id={$this->parent_id}&amp;c=$this->cat_id&amp;action=sync&amp;start=$start&amp;links_done=$links_done&amp;total={$row['cat_links']}";

			meta_refresh(0, $url);

			$this->template->assign_vars(array(
				'UA_PROGRESS_BAR'		=> $this->u_action . "&amp;action=progress_bar&amp;start=$links_done&amp;total={$row['cat_links']}",
				'S_CONTINUE_SYNC'		=> true,
				'L_PROGRESS_EXPLAIN'	=> $this->language->lang('SYNC_IN_PROGRESS_EXPLAIN', $links_done, $row['cat_links']))
			);

			return;
		}

		$url = $this->u_action . "&amp;parent_id={$this->parent_id}&amp;c=$this->cat_id&amp;action=sync_cat";
		meta_refresh(0, $url);

		$this->template->assign_vars(array(
			'UA_PROGRESS_BAR'		=> $this->u_action . '&amp;action=progress_bar',
			'S_CONTINUE_SYNC'		=> true,
			'L_PROGRESS_EXPLAIN'	=> $this->language->lang('SYNC_IN_PROGRESS_EXPLAIN', 0, $row['cat_links']))
		);

		return;
	}

	/**
	* Sync category data
	*
	* @return null
	*/
	public function action_sync_cat()
	{
		$sql = 'SELECT cat_name
			FROM ' . $this->categories_table . '
			WHERE cat_id = ' . (int) $this->cat_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			trigger_error($this->language->lang('DIR_NO_CAT') . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
		}

		$this->_sync_dir_cat($this->cat_id);

		$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_SYNC', time(), array($row['cat_name']));
		$this->cache->destroy('sql', $this->categories_table);

		$this->template->assign_var('L_DIR_CAT_RESYNCED', $this->language->lang('DIR_CAT_RESYNCED', $row['cat_name']));
	}

	/**
	* Display categories page
	*
	* @return null
	*/
	public function display_cats()
	{
		// Default management page
		if (!$this->parent_id)
		{
			$navigation = $this->language->lang('DIR_INDEX');
		}
		else
		{
			$navigation = '<a href="' . $this->u_action . '">' . $this->language->lang('DIR_INDEX') . '</a>';

			$cats_nav = $this->nestedset_category->get_path_data($this->parent_id);

			foreach ($cats_nav as $row)
			{
				if ($row['cat_id'] == $this->parent_id)
				{
					$navigation .= ' -&gt; ' . $row['cat_name'];
				}
				else
				{
					$navigation .= ' -&gt; <a href="' . $this->u_action . '&amp;parent_id=' . $row['cat_id'] . '">' . $row['cat_name'] . '</a>';
				}
			}
		}

		// Jumpbox
		$cat_box = $this->categorie->make_cat_select($this->parent_id);

		if ($this->action == 'sync' || $this->action == 'sync_cat')
		{
			$this->template->assign_var('S_RESYNCED', true);
		}

		$sql = 'SELECT cat_id, parent_id, right_id, left_id, cat_name, cat_icon, cat_desc_uid, cat_desc_bitfield, cat_desc, cat_desc_options, cat_links
			FROM ' . $this->categories_table . '
			WHERE parent_id = ' . (int) $this->parent_id . '
			ORDER BY left_id';
		$result = $this->db->sql_query($sql);

		if ($row = $this->db->sql_fetchrow($result))
		{
			do
			{
				$folder_image = ($row['left_id'] + 1 != $row['right_id']) ? '<img src="images/icon_subfolder.gif" alt="' . $this->language->lang('DIR_SUBCAT') . '" />' : '<img src="images/icon_folder.gif" alt="' . $this->language->lang('FOLDER') . '" />';

				$url = $this->u_action . "&amp;parent_id=$this->parent_id&amp;c={$row['cat_id']}";

				$this->template->assign_block_vars('cats', array(
					'FOLDER_IMAGE'		=> $folder_image,
					'CAT_IMAGE'			=> ($row['cat_icon']) ? '<img src="' . $this->get_img_path('icons', $row['cat_icon']) . '" alt="" />' : '',
					'CAT_NAME'			=> $row['cat_name'],
					'CAT_DESCRIPTION'	=> generate_text_for_display($row['cat_desc'], $row['cat_desc_uid'], $row['cat_desc_bitfield'], $row['cat_desc_options']),
					'CAT_LINKS'			=> $row['cat_links'],

					'U_CAT'				=> $this->u_action . '&amp;parent_id=' . $row['cat_id'],
					'U_MOVE_UP'			=> $url . '&amp;action=move_up',
					'U_MOVE_DOWN'		=> $url . '&amp;action=move_down',
					'U_EDIT'			=> $url . '&amp;action=edit',
					'U_DELETE'			=> $url . '&amp;action=delete',
					'U_SYNC'			=> $url . '&amp;action=sync')
				);
			}
			while ($row = $this->db->sql_fetchrow($result));
		}
		else if ($this->parent_id)
		{
			$row = $this->_get_cat_info($this->parent_id);

			$url = $this->u_action . '&amp;parent_id=' . $this->parent_id . '&amp;c=' . $row['cat_id'];

			$this->template->assign_vars(array(
				'S_NO_CATS'			=> true,

				'U_EDIT'			=> $url . '&amp;action=edit',
				'U_DELETE'			=> $url . '&amp;action=delete',
				'U_SYNC'			=> $url . '&amp;action=sync')
			);
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars(array(
			'ERROR_MSG'		=> (sizeof($this->errors)) ? implode('<br />', $this->errors) : '',
			'NAVIGATION'	=> $navigation,
			'CAT_BOX'		=> $cat_box,
			'U_SEL_ACTION'	=> $this->u_action,
			'U_ACTION'		=> $this->u_action . '&amp;parent_id=' . $this->parent_id,

			'UA_PROGRESS_BAR'	=> $this->u_action . '&amp;action=progress_bar',
		));
	}

	/**
	* Set page url
	*
	* @param	string $u_action Custom form action
	* @return	null
	* @access	public
	*/
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}

	/**
	* Update cat table
	*
	* @return null
	*/
	public function update()
	{
		if (!check_form_key($this->form_key))
		{
			$this->update = false;
			$this->errors[] = $this->language->lang('FORM_INVALID');
		}

		switch ($this->action)
		{
			case 'delete':
				$action_subcats	= $this->request->variable('action_subcats', '');
				$subcats_to_id	= $this->request->variable('subcats_to_id', 0);
				$action_links	= $this->request->variable('action_links', '');
				$links_to_id	= $this->request->variable('links_to_id', 0);

				try
				{
					$this->errors = $this->_delete_cat($action_links, $action_subcats, $links_to_id, $subcats_to_id);
				}
				catch (\Exception $e)
				{
					trigger_error($e->getMessage(), E_USER_WARNING);
				}

				if (sizeof($this->errors))
				{
					break;
				}

				$this->cache->destroy('sql', $this->categories_table);

				trigger_error($this->language->lang('DIR_CAT_DELETED') . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id));

				break;

			case 'edit':
				$this->cat_data = array(
					'cat_id'		=>	$this->cat_id
				);
				// No break here
			case 'add':

				$this->cat_data += array(
					'parent_id'				=> $this->request->variable('cat_parent_id', (int) $this->parent_id),
					'cat_parents'			=> '',
					'cat_name'				=> $this->request->variable('cat_name', '', true),
					'cat_route'				=> $this->request->variable('cat_route', ''),
					'cat_desc'				=> $this->request->variable('cat_desc', '', true),
					'cat_desc_uid'			=> '',
					'cat_desc_options'		=> 7,
					'cat_desc_bitfield'		=> '',
					'cat_icon'				=> $this->request->variable('cat_icon', ''),
					'display_subcat_list'	=> $this->request->variable('display_on_index', false),
					'cat_allow_comments'	=> $this->request->variable('allow_comments', 1),
					'cat_allow_votes'		=> $this->request->variable('allow_votes', 1),
					'cat_must_describe'		=> $this->request->variable('must_describe', 1),
					'cat_count_all'			=> $this->request->variable('count_all', 0),
					'cat_validate'			=> $this->request->variable('validate', 0),
					'cat_link_back'			=> $this->request->variable('link_back', 0),
					'cat_cron_enable'		=> $this->request->variable('cron_enable', 0),
					'cat_cron_freq'			=> $this->request->variable('cron_every', 7),
					'cat_cron_nb_check'		=> $this->request->variable('nb_check', 1),
				);

				// Get data for cat description if specified
				if ($this->cat_data['cat_desc'])
				{
					generate_text_for_storage($this->cat_data['cat_desc'], $this->cat_data['cat_desc_uid'], $this->cat_data['cat_desc_bitfield'], $this->cat_data['cat_desc_options'], $this->request->variable('desc_parse_bbcode', false), $this->request->variable('desc_parse_urls', false), $this->request->variable('desc_parse_smilies', false));
				}

				try
				{
					$this->errors = $this->_update_cat_data();
				}
				catch (\Exception $e)
				{
					trigger_error($e->getMessage(), E_USER_WARNING);
				}

				if (!sizeof($this->errors))
				{
					$this->cache->destroy('sql', $this->categories_table);

					$message = ($this->action == 'add') ? $this->language->lang('DIR_CAT_CREATED') : $this->language->lang('DIR_CAT_UPDATED');

					trigger_error($message . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id));
				}

			break;
		}

		// Purge the cache to refresh route collections
		$this->cache->purge();
	}

	/**
	* Check route
	*
	* @param string $route Route text
	* @return null
	* @access public
	* @throws \phpbb\pages\exception\unexpected_value
	*/
	private function _check_route($route)
	{
		// Route is a required field
		if (empty($route))
		{
			$this->errors[] = $this->language->lang('DIR_CAT_ROUTE_EMPTY');
			return;
		}

		// Route should not contain any unexpected special characters
		if (!preg_match('/^[^!"#$%&*\'()+,.\/\\\\:;<=>?@\\[\\]^`{|}~ ]*$/', $route))
		{
			$this->errors[] = $this->language->lang('DIR_CAT_ROUTE_ILLEGAL_CHARACTERS');
		}

		$sql = 'SELECT cat_route
			FROM ' . $this->categories_table  . "
			WHERE cat_route = '" . $this->db->sql_escape($route) . "'
				AND cat_id <> " . $this->cat_id;
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row)
		{
			$this->errors[] = $this->language->lang('DIR_CAT_ROUTE_NOT_UNIQUE');
		}
	}

	/**
	* Display form
	*
	* @param	string $parents_list	Drop-down list
	* @return	null
	*/
	private function _display_cat_form($parents_list)
	{
		$dir_cat_desc_data = array(
			'text'			=> $this->cat_data['cat_desc'],
			'allow_bbcode'	=> true,
			'allow_smilies'	=> true,
			'allow_urls'	=> true
		);

		// Parse desciption if specified
		if ($this->cat_data['cat_desc'])
		{
			if (!isset($this->cat_data['cat_desc_uid']))
			{
				// Before we are able to display the preview and plane text, we need to parse our $request->variable()'d value...
				$this->cat_data['cat_desc_uid'] = '';
				$this->cat_data['cat_desc_bitfield'] = '';
				$this->cat_data['cat_desc_options'] = 0;

				generate_text_for_storage($this->cat_data['cat_desc'], $this->cat_data['cat_desc_uid'], $this->cat_data['cat_desc_bitfield'], $this->cat_data['cat_desc_options'], $this->request->variable('desc_allow_bbcode', false), $this->request->variable('desc_allow_urls', false), $this->request->variable('desc_allow_smilies', false));
			}

			// decode...
			$dir_cat_desc_data = generate_text_for_edit($this->cat_data['cat_desc'], $this->cat_data['cat_desc_uid'], $this->cat_data['cat_desc_options']);
		}

		$this->template->assign_vars(array(
			'S_EDIT_CAT'		=> true,
			'S_ERROR'			=> (sizeof($this->errors)) ? true : false,
			'S_CAT_PARENT_ID'	=> $this->cat_data['parent_id'],
			'S_ADD_ACTION'		=> ($this->action == 'add') ? true : false,

			'U_BACK'			=> $this->u_action . '&amp;parent_id=' . $this->parent_id,
			'U_EDIT_ACTION'		=> $this->u_action . "&amp;parent_id={$this->parent_id}&amp;action=$this->action&amp;c=$this->cat_id",

			'L_TITLE'					=> $this->language->lang('DIR_' . strtoupper($this->action) . '_CAT'),
			'ERROR_MSG'					=> (sizeof($this->errors)) ? implode('<br />', $this->errors) : '',
			'ICON_IMAGE'				=> ($this->cat_data['cat_icon']) ? $this->get_img_path('icons', $this->cat_data['cat_icon']) : 'images/spacer.gif',

			'DIR_ICON_PATH'				=> $this->get_img_path('icons'),
			'DIR_CAT_NAME'				=> $this->cat_data['cat_name'],
			'DIR_CAT_ROUTE'				=> $this->cat_data['cat_route'],
			'DIR_CAT_DESC'				=> $dir_cat_desc_data['text'],

			'S_DESC_BBCODE_CHECKED'		=> ($dir_cat_desc_data['allow_bbcode']) ? true : false,
			'S_DESC_SMILIES_CHECKED'	=> ($dir_cat_desc_data['allow_smilies']) ? true : false,
			'S_DESC_URLS_CHECKED'		=> ($dir_cat_desc_data['allow_urls']) ? true : false,
			'S_DISPLAY_SUBCAT_LIST'		=> ($this->cat_data['display_subcat_list']) ? true : false,
			'S_PARENT_OPTIONS'			=> $parents_list,
			'S_ICON_OPTIONS'			=> $this->_get_dir_icon_list($this->get_img_path('icons'), $this->cat_data['cat_icon']),
			'S_ALLOW_COMMENTS'			=> ($this->cat_data['cat_allow_comments']) ? true : false,
			'S_ALLOW_VOTES'				=> ($this->cat_data['cat_allow_votes']) ? true : false,
			'S_MUST_DESCRIBE'			=> ($this->cat_data['cat_must_describe']) ? true : false,
			'S_COUNT_ALL'				=> ($this->cat_data['cat_count_all']) ? true : false,
			'S_VALIDATE'				=> ($this->cat_data['cat_validate']) ? true : false,

			'DIR_CRON_EVERY'			=> $this->cat_data['cat_cron_freq'],
			'DIR_NEXT_CRON_ACTION'		=> !empty($this->cat_data['cat_cron_next']) ? $this->user->format_date($this->cat_data['cat_cron_next']) : '-',
			'DIR_CRON_NB_CHECK'			=> $this->cat_data['cat_cron_nb_check'],

			'S_LINK_BACK'				=> ($this->cat_data['cat_link_back']) ? true : false,
			'S_CRON_ENABLE'				=> ($this->cat_data['cat_cron_enable']) ? true : false,

			'U_DATE'					=> $this->helper->route('ernadoo_phpbbdirectory_ajax_date_controller'),
			'U_SLUG'					=> $this->helper->route('ernadoo_phpbbdirectory_ajax_slug_controller'),
		));

		return;
	}

	/**
	* Get category details
	*
	* @param	int		$cat_id	The category ID
	* @return 	array
	*/
	private function _get_cat_info($cat_id)
	{
		$sql = 'SELECT cat_id, parent_id, right_id, left_id, cat_desc, cat_desc_uid, cat_desc_options, cat_icon, cat_name, cat_route, display_subcat_list, cat_allow_comments, cat_allow_votes, cat_must_describe, cat_count_all, cat_validate, cat_cron_freq, cat_cron_nb_check, cat_link_back, cat_cron_enable, cat_cron_next
			FROM ' . $this->categories_table . '
			WHERE cat_id = ' . (int) $cat_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			trigger_error('DIR_ERROR_NO_CATS', E_USER_ERROR);
		}

		return $row;
	}

	/**
	* Update category data
	*
	* @return array
	*/
	private function _update_cat_data()
	{
		if (!$this->cat_data['cat_name'])
		{
			$this->errors[] = $this->language->lang('DIR_CAT_NAME_EMPTY');
		}

		$this->_check_route($this->cat_data['cat_route']);

		if (utf8_strlen($this->cat_data['cat_desc']) > 4000)
		{
			$this->errors[] = $this->language->lang('DIR_CAT_DESC_TOO_LONG');
		}

		if (($this->cat_data['cat_cron_enable'] && $this->cat_data['cat_cron_freq'] <= 0) || $this->cat_data['cat_cron_nb_check'] < 0)
		{
			$this->errors[] = $this->language->lang('DIR_CAT_DATA_NEGATIVE');
		}

		// Unset data that are not database fields
		$cat_data_sql = $this->cat_data;

		// What are we going to do tonight Brain? The same thing we do everynight,
		// try to take over the world ... or decide whether to continue update
		// and if so, whether it's a new cat/link or an existing one
		if (sizeof($this->errors))
		{
			return $this->errors;
		}

		if (!$cat_data_sql['cat_link_back'])
		{
			$cat_data_sql['cat_cron_enable'] = 0;
			$cat_data_sql['cat_cron_next'] = 0;
		}

		if (!$cat_data_sql['parent_id'])
		{
			$cat_data_sql['display_subcat_list'] = 0;
		}

		// no cat_id means we're creating a new categorie
		if (!isset($cat_data_sql['cat_id']))
		{
			if ($cat_data_sql['cat_cron_enable'])
			{
				$cat_data_sql['cat_cron_next'] = time() + $cat_data_sql['cat_cron_freq']*86400;
			}

			$this->cat_data = $this->nestedset_category->insert($cat_data_sql);

			if ($cat_data_sql['parent_id'])
			{
				$this->nestedset_category->change_parent($this->cat_data['cat_id'], $cat_data_sql['parent_id']);
			}

			$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_ADD', time(), array($this->cat_data['cat_name']));
		}
		else
		{
			$row = $this->_get_cat_info($cat_data_sql['cat_id']);

			if ($row['parent_id'] != $cat_data_sql['parent_id'])
			{
				$this->nestedset_category->change_parent($cat_data_sql['cat_id'], $cat_data_sql['parent_id']);
			}

			if ($cat_data_sql['cat_cron_enable'] && ($row['cat_cron_freq'] != $cat_data_sql['cat_cron_freq'] || !$row['cat_cron_enable']))
			{
				$cat_data_sql['cat_cron_next'] = time() + $cat_data_sql['cat_cron_freq']*86400;
			}

			if ($row['cat_name'] != $cat_data_sql['cat_name'])
			{
				// the cat name has changed, clear the parents list of all categories (for safety)
				$sql = 'UPDATE ' . $this->categories_table . "
					SET cat_parents = ''";
				$this->db->sql_query($sql);
			}

			// Setting the cat id to the categorie id is not really received well by some dbs. ;)
			unset($cat_data_sql['cat_id']);

			$sql = 'UPDATE ' . $this->categories_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $cat_data_sql) . '
				WHERE cat_id = ' . (int) $this->cat_id;
			$this->db->sql_query($sql);

			$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_EDIT', time(), array($this->cat_data['cat_name']));
		}

		return $this->errors;
	}

	/**
	* Remove complete category
	*
	* @param	string	$action_links	Action for categories links
	* @param	string	$action_subcats	Action for sub-categories
	* @param	int		$links_to_id	New category ID for links
	* @param	int		$subcats_to_id	New category ID for sub-categories
	* @return 	array
	*/
	private function _delete_cat($action_links = 'delete', $action_subcats = 'delete', $links_to_id = 0, $subcats_to_id = 0)
	{
		$this->cat_data = $this->_get_cat_info($this->cat_id);

		$log_action_links = $log_action_cats = $links_to_name = $subcats_to_name = '';

		if ($action_links == 'delete')
		{
			$log_action_links = 'LINKS';
			$this->errors = array_merge($this->errors, $this->_delete_cat_content());
		}
		else if ($action_links == 'move')
		{
			if (!$links_to_id)
			{
				$this->errors[] = $this->language->lang('DIR_NO_DESTINATION_CAT');
			}
			else
			{
				$log_action_links = 'MOVE_LINKS';

				$sql = 'SELECT cat_name
					FROM ' . $this->categories_table . '
					WHERE cat_id = ' . (int) $links_to_id;
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				if (!$row)
				{
					throw new \OutOfBoundsException('DIR_NO_CAT');
				}
				else
				{
					$links_to_name = $row['cat_name'];
					$this->_move_cat_content($this->cat_id, $links_to_id);
				}
			}
		}

		if (sizeof($this->errors))
		{
			return $this->errors;
		}

		if ($action_subcats == 'delete')
		{
			$log_action_cats = 'CATS';
		}
		else if ($action_subcats == 'move')
		{
			if (!$subcats_to_id)
			{
				$this->errors[] = $this->language->lang('DIR_NO_DESTINATION_CAT');
			}
			else
			{
				$log_action_cats = 'MOVE_CATS';

				$subcats_to_name = $row['cat_name'];
				$this->nestedset_category->move_children($this->cat_id, $subcats_to_id);
			}
		}

		if (sizeof($this->errors))
		{
			return $this->errors;
		}

		$this->nestedset_category->delete($this->cat_id);

		$log_action = implode('_', array($log_action_links, $log_action_cats));

		switch ($log_action)
		{
			case 'MOVE_LINKS_MOVE_CATS':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_MOVE_LINKS_MOVE_CATS', time(), array($links_to_name, $subcats_to_name, $this->cat_data['cat_name']));
			break;

			case 'MOVE_LINKS_CATS':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_MOVE_LINKS_CATS', time(), array($links_to_name, $this->cat_data['cat_name']));
			break;

			case 'LINKS_MOVE_CATS':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_LINKS_MOVE_CATS', time(), array($subcats_to_name, $this->cat_data['cat_name']));
			break;

			case '_MOVE_CATS':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_MOVE_CATS', time(), array($subcats_to_name, $this->cat_data['cat_name']));
			break;

			case 'MOVE_LINKS_':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_MOVE_LINKS', time(), array($links_to_name, $this->cat_data['cat_name']));
			break;

			case 'LINKS_CATS':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_LINKS_CATS', time(), array($this->cat_data['cat_name']));
			break;

			case '_CATS':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_CATS', time(), array($this->cat_data['cat_name']));
			break;

			case 'LINKS_':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_LINKS', time(), array($this->cat_data['cat_name']));
			break;

			default:
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_CAT', time(), array($this->cat_data['cat_name']));
			break;
		}

		return $this->errors;
	}

	/**
	* Move category content from one to another forum
	*
	* @param	int	$from_id
	* @param	int	$to_id
	* @return	null
	*/
	private function _move_cat_content($from_id, $to_id)
	{
		$sql = 'UPDATE ' . $this->links_table . '
			SET link_cat = ' . (int) $to_id . '
			WHERE link_cat = ' . (int) $from_id;
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . $this->watch_table . '
			WHERE cat_id = ' . (int) $from_id;
		$this->db->sql_query($sql);

		$this->_sync_dir_cat($to_id);
	}

	/**
	* Delete category content
	*
	* @return array
	*/
	private function _delete_cat_content()
	{
		$this->db->sql_transaction('begin');

		// Before we remove anything we make sure we are able to adjust the post counts later. ;)
		$sql = 'SELECT link_id, link_banner
			FROM ' . $this->links_table . '
			WHERE link_cat = ' . (int) $this->cat_id;
		$result = $this->db->sql_query($sql);

		$link_ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$link_ids[] = $row['link_id'];

			if ($row['link_banner'] && !preg_match('/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|www\.).+/si', $row['link_banner']))
			{
				$banner_img = $this->get_banner_path(basename($row['link_banner']));

				if (file_exists($banner_img))
				{
					@unlink($banner_img);
				}
			}
		}
		$this->db->sql_freeresult($result);

		if (sizeof($link_ids))
		{
			// Delete links datas
			$link_datas_ary = array(
				$this->comments_table	=> 'comment_link_id',
				$this->votes_table		=> 'vote_link_id',
			);

			foreach ($link_datas_ary as $table => $field)
			{
				$this->db->sql_query("DELETE FROM $table WHERE " . $this->db->sql_in_set($field, $link_ids));
			}
		}

		// Delete cats datas
		$cat_datas_ary = array(
			$this->links_table	=> 'link_cat',
			$this->watch_table	=> 'cat_id',
		);

		foreach ($cat_datas_ary as $table => $field)
		{
			$this->db->sql_query("DELETE FROM $table WHERE $field = " . (int) $this->cat_id);
		}

		$this->db->sql_transaction('commit');

		return array();
	}

	/**
	* Update links counter
	*
	* @param	int $cat_id	The category ID
	* @return	null
	*/
	private function _sync_dir_cat($cat_id)
	{
		$sql = 'SELECT COUNT(link_id) AS num_links
			FROM ' . $this->links_table . '
			WHERE link_cat = ' . (int) $cat_id . '
				AND link_active = 1';
		$result = $this->db->sql_query($sql);
		$total_links = (int) $this->db->sql_fetchfield('num_links');
		$this->db->sql_freeresult($result);

		$sql = 'UPDATE ' . $this->categories_table . '
			SET cat_links = ' . $total_links . '
			WHERE cat_id = ' . (int) $cat_id;
		$this->db->sql_query($sql);
	}

	/**
	* Update link data (note, vote, comment)
	*
	* @param	int	$start
	* @param	int	$stop
	* @return	null
	*/
	private function _sync_dir_links($start, $stop)
	{
		$sql_ary = array(
			'link_comment'	=> 0,
			'link_note'		=> 0,
			'link_vote'		=> 0,
		);

		$sql = 'UPDATE ' . $this->links_table . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE link_id BETWEEN ' . (int) $start . ' AND ' . (int) $stop;
		$this->db->sql_query($sql);

		$sql = 'SELECT vote_link_id, COUNT(vote_note) AS nb_vote, SUM(vote_note) AS total FROM ' . $this->votes_table . '
			WHERE vote_link_id BETWEEN ' . (int) $start . ' AND ' . (int) $stop . '
			GROUP BY vote_link_id';
		$result = $this->db->sql_query($sql);
		while ($tmp = $this->db->sql_fetchrow($result))
		{
			$sql = 'UPDATE ' . $this->links_table . '
				SET link_note = ' . (int) $tmp['total'] . ', link_vote = ' . (int) $tmp['nb_vote'] . '
				WHERE link_id = ' . (int) $tmp['vote_link_id'];
			$this->db->sql_query($sql);
		}
		$this->db->sql_freeresult($result);

		$sql = 'SELECT 	comment_link_id, COUNT(comment_id) AS nb_comment
			FROM ' . $this->comments_table . '
			WHERE comment_link_id BETWEEN ' . (int) $start . ' AND ' . (int) $stop . '
			GROUP BY comment_link_id';
		$result = $this->db->sql_query($sql);
		while ($tmp = $this->db->sql_fetchrow($result))
		{
			$sql = 'UPDATE ' . $this->links_table . '
				SET link_comment = ' . (int) $tmp['nb_comment'] . '
				WHERE link_id = ' . (int) $tmp['comment_link_id'];
			$this->db->sql_query($sql);
		}
		$this->db->sql_freeresult($result);
	}

	/**
	* Display icons drop-down list
	*
	* @param	string	$icons_path
	* @param	string	$img_selected
	* @return	string
	*/
	private function _get_dir_icon_list($icons_path, $img_selected)
	{
		$imglist = filelist($icons_path, '');
		$filename_list = '<option value="">----------</option>';

		foreach ($imglist as $path => $img_ary)
		{
			sort($img_ary);

			foreach ($img_ary as $img)
			{
				$img = $path . $img;
				$selected = '';

				if (strlen($img) > 255)
				{
					continue;
				}

				if ($img == $img_selected)
				{
					$selected = ' selected="selected"';
				}

				$filename_list .= '<option value="' . htmlspecialchars($img) . '"' . $selected . '>' . $img . '</option>';
			}
		}

		return ($filename_list);
	}
}

<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\acp;

class phpbbdirectory_module
{
	protected $new_config;
	protected $parent_id = 0;

	protected $config;
	protected $db;
	protected $user;
	protected $template;
	protected $phpbb_log;

	protected $helper;
	protected $categorie;
	protected $dir_helper;
	protected $nestedset_category;

	public $u_action;

	/**
	* 
	* @param int	$id
	* @param string	$mode
	*/
	public function main($id, $mode)
	{
		global $db, $user, $template, $cache, $request, $phpEx, $phpbb_root_path;
		global $config, $phpbb_admin_path, $phpbb_container, $phpbb_log;

		$this->config 			= $config;
		$this->db 				= $db;
		$this->user 			= $user;
		$this->template 		= $template;
		$this->phpbb_log		= $phpbb_log;

		$this->helper				= $phpbb_container->get('controller.helper');
		$this->categorie 			= $phpbb_container->get('ernadoo.phpbbdirectory.core.categorie');
		$this->dir_helper 			= $phpbb_container->get('ernadoo.phpbbdirectory.core.helper');
		$this->nestedset_category	= $phpbb_container->get('ernadoo.phpbbdirectory.core.nestedset_category');

		$action		= $request->variable('action', '');
		$submit		= ($request->is_set_post('submit')) ? true : false;
		$update		= ($request->is_set_post('update')) ? true : false;
		$cat_id		= $request->variable('c', 0);
		$link_id	= $request->variable('u', 0);

		$form_key = 'acp_dir_cat';
		add_form_key($form_key);

		$this->parent_id = $request->variable('parent_id', 0);
		$cat_data = $errors = array();
		if ($update && !check_form_key($form_key))
		{
			$update = false;
			$errors[] = $this->user->lang['FORM_INVALID'];
		}

		switch($mode)
		{
			case 'main':
				$this->page_title = 'ACP_DIRECTORY';
				$this->tpl_name = 'acp_dir_main';
				$this->user->add_lang('install');

				if ($action)
				{
					if (!confirm_box(true))
					{
						switch ($action)
						{
							case 'votes':
								$confirm = true;
								$confirm_lang = 'DIR_RESET_VOTES_CONFIRM';
							break;

							case 'comments':
								$confirm = true;
								$confirm_lang = 'DIR_RESET_COMMENTS_CONFIRM';
							break;

							case 'clicks':
								$confirm = true;
								$confirm_lang = 'DIR_RESET_CLICKS_CONFIRM';
							break;

							case 'orphans':
								$confirm = true;
								$confirm_lang = 'DIR_DELETE_ORPHANS';
								break;

							default:
								$confirm = true;
								$confirm_lang = 'CONFIRM_OPERATION';
						}

						if ($confirm)
						{
							confirm_box(false, $this->user->lang[$confirm_lang], build_hidden_fields(array(
								'i'			=> $id,
								'mode'		=> $mode,
								'action'	=> $action,
							)));
						}
					}
					else
					{
						switch ($action)
						{
							case 'votes':
								switch ($this->db->get_sql_layer())
								{
									case 'sqlite':
									case 'firebird':
										$this->db->sql_query('DELETE FROM ' . DIR_VOTE_TABLE);
									break;

									default:
										$this->db->sql_query('TRUNCATE TABLE ' . DIR_VOTE_TABLE);
									break;
								}

								$sql = 'UPDATE ' . DIR_LINK_TABLE . '
									SET link_vote = 0, link_note = 0';
								$this->db->sql_query($sql);

								if ($request->is_ajax())
								{
									trigger_error('DIR_RESET_VOTES_SUCCESS');
								}
							break;

							case 'comments':
								switch ($this->db->get_sql_layer())
								{
									case 'sqlite':
									case 'firebird':
										$this->db->sql_query('DELETE FROM ' . DIR_COMMENT_TABLE);
									break;

									default:
										$this->db->sql_query('TRUNCATE TABLE ' . DIR_COMMENT_TABLE);
									break;
								}

								$sql = 'UPDATE ' . DIR_LINK_TABLE . '
									SET link_comment = 0';
								$this->db->sql_query($sql);

								if ($request->is_ajax())
								{
									trigger_error('DIR_RESET_COMMENTS_SUCCESS');
								}

							break;

							case 'clicks':
								$sql = 'UPDATE ' . DIR_LINK_TABLE . '
									SET link_view = 0';
								$this->db->sql_query($sql);

								if ($request->is_ajax())
								{
									trigger_error('DIR_RESET_CLICKS_SUCCESS');
								}
							break;

							case 'orphans':
								$this->_orphan_files(true);

								if ($request->is_ajax())
								{
									trigger_error('DIR_DELETE_ORPHANS_SUCCESS');
								}
							break;
						}
					}
				}

				// Count number of categories
				$sql = 'SELECT COUNT(cat_id) AS nb_cats
					FROM ' . DIR_CAT_TABLE;
				$result = $this->db->sql_query($sql);
				$total_cats = (int) $this->db->sql_fetchfield('nb_cats');
				$this->db->sql_freeresult($result);

				// Cont number of links
				$sql = 'SELECT link_id, link_active
					FROM ' . DIR_LINK_TABLE;
				$result = $this->db->sql_query($sql);
				$total_links = $waiting_links = 0;
				while ($row = $this->db->sql_fetchrow($result))
				{
					$total_links++;
					if (!$row['link_active'])
					{
						$waiting_links++;
					}
				}
				$this->db->sql_freeresult($result);

				// Comments number calculating
				$sql = 'SELECT COUNT(comment_id) AS nb_comments
					FROM ' . DIR_COMMENT_TABLE;
				$result = $this->db->sql_query($sql);
				$total_comments = (int) $this->db->sql_fetchfield('nb_comments');
				$this->db->sql_freeresult($result);

				// Votes number calculating
				$sql = 'SELECT COUNT(vote_id) AS nb_votes
					FROM ' . DIR_VOTE_TABLE;
				$result = $this->db->sql_query($sql);
				$total_votes = (int) $this->db->sql_fetchfield('nb_votes');
				$this->db->sql_freeresult($result);

				// Click number calculating
				$sql = 'SELECT SUM(link_view) AS nb_clicks
					FROM ' . DIR_LINK_TABLE;
				$result = $this->db->sql_query($sql);
				$total_clicks = (int) $this->db->sql_fetchfield('nb_clicks');
				$this->db->sql_freeresult($result);

				$banners_dir_size = 0;

				$banners_path = $this->dir_helper->get_banner_path();

				if ($banners_dir = @opendir($banners_path))
				{
					while (($file = readdir($banners_dir)) !== false)
					{
						if ($file[0] != '.' && $file[0] != '..' && strpos($file, 'index.') === false && strpos($file, '.db') === false)
						{
							$banners_dir_size += filesize($banners_path . $file);
						}
					}
					closedir($banners_dir);

					$banners_dir_size = get_formatted_filesize($banners_dir_size);
				}
				else
				{
					// Couldn't open banners dir.
					$banners_dir_size = $this->user->lang['NOT_AVAILABLE'];
				}

				$total_orphan = $this->_orphan_files();

				$this->template->assign_vars(array(
					'U_ACTION'			=> $this->u_action,

					'TOTAL_CATS'		=> $total_cats,
					'TOTAL_LINKS'		=> $total_links-$waiting_links,
					'WAITING_LINKS'		=> $waiting_links,
					'TOTAL_COMMENTS'	=> $total_comments,
					'TOTAL_VOTES'		=> $total_votes,
					'TOTAL_CLICKS'		=> $total_clicks,
					'TOTAL_ORPHANS'		=> $total_orphan,
					'BANNERS_DIR_SIZE'	=> $banners_dir_size,
				));
			break;

			case 'settings':
				$display_vars = array(
					'title'	=> 'ACP_DIRECTORY_SETTINGS',
					'vars'	=> array(
						'legend1' => 'DIR_PARAM',

						'dir_banner_width'					=> '',
						'dir_banner_height'					=> '',

						'dir_mail'							=> array('lang' => 'DIR_MAIL_VALIDATION',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
						'dir_activ_checkurl'				=> array('lang' => 'DIR_ACTIVE_CHECK',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
						'dir_activ_flag'					=> array('lang' => 'DIR_ACTIV_FLAG',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
						'dir_activ_rss'						=> array('lang' => 'DIR_ACTIV_RSS',			'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
						'dir_activ_pagerank'				=> array('lang' => 'DIR_ACTIV_PAGERANK',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
						'dir_show'							=> array('lang' => 'DIR_SHOW',				'validate' => 'int:1', 	'type' => 'text:3:3',		'explain' => false),
						'dir_length_describe'				=> array('lang' => 'DIR_MAX_DESC',			'validate' => 'int:1', 	'type' => 'text:3:3',		'explain' => false),
						'dir_new_time'						=> array('lang' => 'DIR_NEW_TIME',			'validate' => 'int', 	'type' => 'text:3:3',		'explain' => true),
						'dir_default_order'					=> array('lang' => 'DIR_DEFAULT_ORDER',		'validate' => 'string', 'type' => 'select',			'explain' => true, 'method' => 'get_order_list', 'params' => array('{CONFIG_VALUE}')),

						'legend2'							=> 'DIR_RECENT_GUEST',
						'dir_recent_block'					=> array('lang' => 'DIR_RECENT_ENABLE',		'validate' => 'bool',		'type' => 'radio:yes_no',	'explain' => true),
						'dir_recent_rows'					=> array('lang' => 'DIR_RECENT_ROWS',		'validate' => 'int:1',		'type' => 'text:3:3',		'explain' => false),
						'dir_recent_columns'				=> array('lang' => 'DIR_RECENT_COLUMNS',	'validate' => 'int:1',		'type' => 'text:3:3',		'explain' => false),
						'dir_recent_exclude'				=> array('lang' => 'DIR_RECENT_EXCLUDE',	'validate' => 'string',		'type' => 'text:6:99',			'explain' => true),

						'legend3'							=> 'DIR_ADD_GUEST',
						'dir_visual_confirm'				=> array('lang' => 'DIR_VISUAL_CONFIRM',	'validate' => 'bool',		'type' => 'radio:yes_no',	'explain' => true),
						'dir_visual_confirm_max_attempts'	=> array('lang' => 'DIR_MAX_ADD_ATTEMPTS',	'validate' => 'int:1:10',	'type' => 'text:3:3',		'explain' => true),

						'legend4'							=> 'DIR_THUMB_PARAM',
						'dir_activ_thumb'					=> array('lang' => 'DIR_ACTIVE_THUMB',			'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
						'dir_activ_thumb_remote'			=> array('lang' => 'DIR_ACTIVE_THUMB_REMOTE',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
						'dir_thumb_service'					=> array('lang' => 'DIR_THUMB_SERVICE',			'validate' => 'string', 'type' => 'select',			'explain' => true, 'method' => 'get_thumb_service_list', 'params' => array('{CONFIG_VALUE}')),
						'dir_thumb_service_reverse'			=> array('lang' => 'DIR_THUMB_SERVICE_REVERSE',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),

						'legend5'							=> 'DIR_COMM_PARAM',
						'dir_allow_bbcode'					=> array('lang' => 'DIR_ALLOW_BBCODE',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
						'dir_allow_links'					=> array('lang' => 'DIR_ALLOW_LINKS',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
						'dir_allow_smilies'					=> array('lang' => 'DIR_ALLOW_SMILIES',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
						'dir_length_comments'				=> array('lang' => 'DIR_LENGTH_COMMENTS',	'validate' => 'int:2',	'type' => 'text:3:3',		'explain' => true),
						'dir_comments_per_page'				=> array('lang' => 'DIR_COMM_PER_PAGE',		'validate' => 'int:1',	'type' => 'text:3:3',		'explain' => false),

						'legend6'							=> 'DIR_BANN_PARAM',
						'dir_activ_banner'					=> array('lang' => 'DIR_ACTIV_BANNER',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
						'dir_banner'						=> array('lang' => 'DIR_MAX_BANN',			'validate' => 'int',	'type' => 'dimension:0',	'explain' => true, 'append' => ' ' . $this->user->lang['PIXEL']),
						'dir_banner_filesize'				=> array('lang' => 'DIR_MAX_SIZE',			'validate' => 'int:0',	'type' => 'number:0',		'explain' => true, 'append' => ' ' . $this->user->lang['BYTES']),
						'dir_storage_banner'				=> array('lang' => 'DIR_STORAGE_BANNER',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
					)
				);

				if (isset($display_vars['lang']))
				{
					$this->user->add_lang($display_vars['lang']);
				}

				$this->new_config = $config;
				$cfg_array = (isset($_REQUEST['config'])) ? $request->variable('config', array('' => ''), true) : $this->new_config;
				$error = array();

				// We validate the complete config if whished
				validate_config_vars($display_vars['vars'], $cfg_array, $error);

				// Do not write values if there is an error
				if (sizeof($error))
				{
					$submit = false;
				}

				// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
				foreach ($display_vars['vars'] as $config_name => $null)
				{

					if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
					{
						continue;
					}

					$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

					if ($submit)
					{
						$config->set($config_name, $config_value);
					}
				}

				if ($submit)
				{
					$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'DIR_CONFIG_' . strtoupper($mode));

					trigger_error($this->user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}

				$this->tpl_name = 'acp_board';
				$this->page_title = $display_vars['title'];

				$this->template->assign_vars(array(
					'L_TITLE'			=> $this->user->lang[$display_vars['title']],
					'L_TITLE_EXPLAIN'	=> $this->user->lang[$display_vars['title'] . '_EXPLAIN'],

					'S_ERROR'			=> (sizeof($error)) ? true : false,
					'ERROR_MSG'			=> implode('<br />', $error),

					'U_ACTION'			=> $this->u_action)
				);

				// Output relevant page
				foreach ($display_vars['vars'] as $config_key => $vars)
				{
					if (!is_array($vars) && strpos($config_key, 'legend') === false)
					{
						continue;
					}

					if (strpos($config_key, 'legend') !== false)
					{
						$this->template->assign_block_vars('options', array(
							'S_LEGEND'	=> true,
							'LEGEND'	=> (isset($this->user->lang[$vars])) ? $this->user->lang[$vars] : $vars)
						);

						continue;
					}

					$type = explode(':', $vars['type']);

					$l_explain = '';
					if ($vars['explain'] && isset($vars['lang_explain']))
					{
						$l_explain = (isset($this->user->lang[$vars['lang_explain']])) ? $this->user->lang[$vars['lang_explain']] : $vars['lang_explain'];
					}
					else if ($vars['explain'])
					{
						$l_explain = (isset($this->user->lang[$vars['lang'] . '_EXPLAIN'])) ? $this->user->lang[$vars['lang'] . '_EXPLAIN'] : '';
					}

					$this->template->assign_block_vars('options', array(
						'KEY'			=> $config_key,
						'TITLE'			=> (isset($this->user->lang[$vars['lang']])) ? $this->user->lang[$vars['lang']] : $vars['lang'],
						'S_EXPLAIN'		=> $vars['explain'],
						'TITLE_EXPLAIN'	=> $l_explain,
						'CONTENT'		=> build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars),
					));

					unset($display_vars['vars'][$config_key]);
				}

			break;

			case 'cat':

				// Major routines
				if ($update)
				{
					switch ($action)
					{
						case 'delete':
							$action_subcats		= $request->variable('action_subcats', '');
							$subcats_to_id		= $request->variable('subcats_to_id', 0);
							$action_links		= $request->variable('action_links', '');
							$links_to_id		= $request->variable('links_to_id', 0);

							try
							{
								$errors = $this->_delete_cat($cat_id, $action_links, $action_subcats, $links_to_id, $subcats_to_id);
							}
							catch (\Exception $e)
							{
								trigger_error($e->getMessage(), E_USER_WARNING);
							}

							if (sizeof($errors))
							{
								break;
							}

							$cache->destroy('sql', DIR_CAT_TABLE);

							trigger_error($this->user->lang['DIR_CAT_DELETED'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id));

						break;

						case 'edit':
							$cat_data = array(
								'cat_id'		=>	$cat_id
							);
						// No break here
						case 'add':

							$cat_data += array(
								'parent_id'				=> $request->variable('cat_parent_id', (int) $this->parent_id),
								'cat_parents'			=> '',
								'cat_name'				=> $request->variable('cat_name', '', true),
								'cat_desc'				=> $request->variable('cat_desc', '', true),
								'cat_desc_uid'			=> '',
								'cat_desc_options'		=> 7,
								'cat_desc_bitfield'		=> '',
								'cat_icon'				=> $request->variable('cat_icon', ''),
								'display_subcat_list'	=> $request->variable('display_on_index', false),
								'cat_allow_comments'	=> $request->variable('allow_comments', 1),
								'cat_allow_votes'		=> $request->variable('allow_votes', 1),
								'cat_must_describe'		=> $request->variable('must_describe', 1),
								'cat_count_all'			=> $request->variable('count_all', 0),
								'cat_validate'			=> $request->variable('validate', 0),
								'cat_link_back'			=> $request->variable('link_back', 0),
								'cat_cron_enable'		=> $request->variable('cron_enable', 0),
								'cat_cron_freq'			=> $request->variable('cron_every', 7),
								'cat_cron_nb_check'		=> $request->variable('nb_check', 1),
							);

							// Get data for cat description if specified
							if ($cat_data['cat_desc'])
							{
								generate_text_for_storage($cat_data['cat_desc'], $cat_data['cat_desc_uid'], $cat_data['cat_desc_bitfield'], $cat_data['cat_desc_options'], $request->variable('desc_parse_bbcode', false), $request->variable('desc_parse_urls', false), $request->variable('desc_parse_smilies', false));
							}

							try
							{
								$errors = $this->_update_cat_data($cat_data);
							}
							catch (\Exception $e)
							{
								trigger_error($e->getMessage(), E_USER_WARNING);
							}

							if (!sizeof($errors))
							{
								$cache->destroy('sql', DIR_CAT_TABLE);

								$message = ($action == 'add') ? $this->user->lang['DIR_CAT_CREATED'] : $this->user->lang['DIR_CAT_UPDATED'];

								trigger_error($message . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id));
							}
						break;
					}
				}
				$this->page_title = 'ACP_DIRECTORY';
				$this->tpl_name = 'acp_dir_cat';

				switch ($action)
				{
					case 'progress_bar':
						$start = $request->variable('start', 0);
						$total = $request->variable('total', 0);

						$this->_display_progress_bar($start, $total);
					break;

					case 'sync':

						if (!$cat_id)
						{
							trigger_error($this->user->lang['DIR_NO_CAT'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
						}

						@set_time_limit(0);

						$sql = 'SELECT cat_name, cat_links
							FROM ' . DIR_CAT_TABLE . '
							WHERE cat_id = ' . (int) $cat_id;
						$result = $this->db->sql_query($sql);
						$row = $this->db->sql_fetchrow($result);
						$this->db->sql_freeresult($result);

						if (!$row)
						{
							trigger_error($this->user->lang['DIR_NO_CAT'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
						}

						if ($row['cat_links'])
						{
							$sql = 'SELECT MIN(link_id) as min_link_id, MAX(link_id) as max_link_id
								FROM ' . DIR_LINK_TABLE . '
								WHERE link_cat = ' . (int) $cat_id . '
									AND link_active = 1';
							$result = $this->db->sql_query($sql);
							$row2 = $this->db->sql_fetchrow($result);
							$this->db->sql_freeresult($result);

							// Typecast to int if there is no data available
							$row2['min_link_id'] = (int) $row2['min_link_id'];
							$row2['max_link_id'] = (int) $row2['max_link_id'];

							$start = $request->variable('start', $row2['min_link_id']);

							$batch_size = 200;
							$end = $start + $batch_size;

							// Sync all topics in batch mode...
							$this->_sync_dir_links($start, $end);

							if ($end < $row2['max_link_id'])
							{
								// We really need to find a way of showing statistics... no progress here
								$sql = 'SELECT COUNT(link_id) as num_links
									FROM ' . DIR_LINK_TABLE . '
									WHERE link_cat = ' . (int) $cat_id . '
										AND link_active = 1
										AND link_id BETWEEN ' . $start . ' AND ' . $end;
								$result = $this->db->sql_query($sql);
								$links_done = $request->variable('links_done', 0) + (int) $this->db->sql_fetchfield('num_links');
								$this->db->sql_freeresult($result);

								$start += $batch_size;

								$url = $this->u_action . "&amp;parent_id={$this->parent_id}&amp;c=$cat_id&amp;action=sync&amp;start=$start&amp;links_done=$links_done&amp;total={$row['cat_links']}";

								meta_refresh(0, $url);

								$this->template->assign_vars(array(
									'U_PROGRESS_BAR'		=> $this->u_action . "&amp;action=progress_bar&amp;start=$links_done&amp;total={$row['cat_links']}",
									'UA_PROGRESS_BAR'		=> addslashes($this->u_action . "&amp;action=progress_bar&amp;start=$links_done&amp;total={$row['cat_links']}"),
									'S_CONTINUE_SYNC'		=> true,
									'L_PROGRESS_EXPLAIN'	=> $this->user->lang('SYNC_IN_PROGRESS_EXPLAIN', $links_done, $row['cat_links']))
								);

								return;
							}
						}

						$url = $this->u_action . "&amp;parent_id={$this->parent_id}&amp;c=$cat_id&amp;action=sync_cat";
						meta_refresh(0, $url);

						$this->template->assign_vars(array(
							'U_PROGRESS_BAR'		=> $this->u_action . '&amp;action=progress_bar',
							'UA_PROGRESS_BAR'		=> addslashes($this->u_action . '&amp;action=progress_bar'),
							'S_CONTINUE_SYNC'		=> true,
							'L_PROGRESS_EXPLAIN'	=> $this->user->lang('SYNC_IN_PROGRESS_EXPLAIN', 0, $row['cat_links']))
						);

						return;
					break;

					case 'sync_cat':

						$sql = 'SELECT cat_name
							FROM ' . DIR_CAT_TABLE . '
							WHERE cat_id = ' . (int) $cat_id;
						$result = $this->db->sql_query($sql);
						$row = $this->db->sql_fetchrow($result);
						$this->db->sql_freeresult($result);

						if (!$row)
						{
							trigger_error($this->user->lang['DIR_NO_CAT'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
						}

						$this->_sync_dir_cat($cat_id);

						$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_SYNC', time(), array($row['cat_name']));
						$cache->destroy('sql', DIR_CAT_TABLE);

						$this->template->assign_var('L_DIR_CAT_RESYNCED', $this->user->lang('DIR_CAT_RESYNCED', $row['cat_name']));

					break;

					case 'move_up':
					case 'move_down':

						if (!$cat_id)
						{
							trigger_error($this->user->lang['DIR_NO_CAT'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
						}

						$sql = 'SELECT cat_id, cat_name, parent_id, left_id, right_id
							FROM ' . DIR_CAT_TABLE . '
							WHERE cat_id = ' . (int) $cat_id;
						$result = $this->db->sql_query($sql);
						$row = $this->db->sql_fetchrow($result);
						$this->db->sql_freeresult($result);

						if (!$row)
						{
							trigger_error($this->user->lang['DIR_NO_CAT'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
						}

						try
						{
							$move_cat_name = $this->nestedset_category->{$action}($cat_id);
						}
						catch (\Exception $e)
						{
							trigger_error($e->getMessage(), E_USER_WARNING);
						}

						if ($move_cat_name !== false)
						{
							$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_' . strtoupper($action), time(), array($row['cat_name'], $move_cat_name));
							$cache->destroy('sql', DIR_CAT_TABLE);
						}

						if ($request->is_ajax())
						{
							$json_response = new \phpbb\json_response;
							$json_response->send(array('success' => ($move_cat_name !== false)));
						}

					break;

					case 'add':
					case 'edit':

						// Show form to create/modify a categorie
						if ($action == 'edit')
						{
							$this->page_title = 'DIR_EDIT_CAT';
							$row = $this->_get_cat_info($cat_id);

							if (!$update)
							{
								$cat_data = $row;
							}
							else
							{
								$cat_data['left_id'] = $row['left_id'];
								$cat_data['right_id'] = $row['right_id'];
							}

							// Make sure no direct child categories are able to be selected as parents.
							$exclude_cats = array();
							foreach ($this->nestedset_category->get_subtree_data($cat_id) as $row2)
							{
								$exclude_cats[] = $row2['cat_id'];
							}
							$parents_list = $this->categorie->make_cat_select((int) $cat_data['parent_id'], $exclude_cats);
						}
						else
						{
							$this->page_title = 'DIR_CREATE_CAT';

							$cat_id = $this->parent_id;
							$parents_list = $this->categorie->make_cat_select($this->parent_id);

							// Fill categorie data with default values
							if (!$update)
							{
								$cat_data = array(
									'parent_id'				=> $this->parent_id,
									'cat_name'				=> $request->variable('cat_name', '', true),
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
						}

						$dir_cat_desc_data = array(
							'text'			=> $cat_data['cat_desc'],
							'allow_bbcode'	=> true,
							'allow_smilies'	=> true,
							'allow_urls'	=> true
						);

						// Parse desciption if specified
						if ($cat_data['cat_desc'])
						{
							if (!isset($cat_data['cat_desc_uid']))
							{
								// Before we are able to display the preview and plane text, we need to parse our $request->variable()'d value...
								$cat_data['cat_desc_uid'] = '';
								$cat_data['cat_desc_bitfield'] = '';
								$cat_data['cat_desc_options'] = 0;

								generate_text_for_storage($cat_data['cat_desc'], $cat_data['cat_desc_uid'], $cat_data['cat_desc_bitfield'], $cat_data['cat_desc_options'], $request->variable('desc_allow_bbcode', false), $request->variable('desc_allow_urls', false), $request->variable('desc_allow_smilies', false));
							}

							// decode...
							$dir_cat_desc_data = generate_text_for_edit($cat_data['cat_desc'], $cat_data['cat_desc_uid'], $cat_data['cat_desc_options']);
						}

						$sql = 'SELECT cat_id
							FROM ' . DIR_CAT_TABLE . '
							WHERE cat_id <> ' . (int) $cat_id;
						$result = $this->db->sql_query_limit($sql, 1);

						if ($this->db->sql_fetchrow($result))
						{
							$this->template->assign_vars(array(
								'S_MOVE_DIR_CAT_OPTIONS'	=> $this->categorie->make_cat_select((int) $cat_data['parent_id'], $cat_id))
							);
						}
						$this->db->sql_freeresult($result);

						$this->template->assign_vars(array(
							'S_EDIT_CAT'		=> true,
							'S_ERROR'			=> (sizeof($errors)) ? true : false,
							'S_CAT_PARENT_ID'	=> $cat_data['parent_id'],
							'S_ADD_ACTION'		=> ($action == 'add') ? true : false,

							'U_BACK'			=> $this->u_action . '&amp;parent_id=' . $this->parent_id,
							'U_EDIT_ACTION'		=> $this->u_action . "&amp;parent_id={$this->parent_id}&amp;action=$action&amp;c=$cat_id",

							'L_TITLE'					=> $this->user->lang[$this->page_title],
							'ERROR_MSG'					=> (sizeof($errors)) ? implode('<br />', $errors) : '',
							'ICON_IMAGE'				=> ($cat_data['cat_icon']) ? $this->dir_helper->get_img_path('icons', $row['cat_icon']) : $phpbb_admin_path . 'images/spacer.gif',

							'DIR_ICON_PATH'				=> $this->dir_helper->get_img_path('icons'),
							'DIR_CAT_NAME'				=> $cat_data['cat_name'],
							'DIR_CAT_DESC'				=> $dir_cat_desc_data['text'],

							'S_DESC_BBCODE_CHECKED'		=> ($dir_cat_desc_data['allow_bbcode']) ? true : false,
							'S_DESC_SMILIES_CHECKED'	=> ($dir_cat_desc_data['allow_smilies']) ? true : false,
							'S_DESC_URLS_CHECKED'		=> ($dir_cat_desc_data['allow_urls']) ? true : false,
							'S_DISPLAY_SUBCAT_LIST'		=> ($cat_data['display_subcat_list']) ? true : false,
							'S_PARENT_OPTIONS'			=> $parents_list,
							'S_ICON_OPTIONS'			=> $this->_get_dir_icon_list($this->dir_helper->get_img_path('icons'), $cat_data['cat_icon']),
							'S_ALLOW_COMMENTS'			=> ($cat_data['cat_allow_comments']) ? true : false,
							'S_ALLOW_VOTES'				=> ($cat_data['cat_allow_votes']) ? true : false,
							'S_MUST_DESCRIBE'			=> ($cat_data['cat_must_describe']) ? true : false,
							'S_COUNT_ALL'				=> ($cat_data['cat_count_all']) ? true : false,
							'S_VALIDATE'				=> ($cat_data['cat_validate']) ? true : false,

							'DIR_CRON_EVERY'			=> $cat_data['cat_cron_freq'],
							'DIR_NEXT_CRON_ACTION'		=> !empty($cat_data['cat_cron_next']) ? $this->user->format_date($cat_data['cat_cron_next']) : '-',
							'DIR_CRON_NB_CHECK'			=> $cat_data['cat_cron_nb_check'],

							'S_LINK_BACK'				=> ($cat_data['cat_link_back']) ? true : false,
							'S_CRON_ENABLE'				=> ($cat_data['cat_cron_enable']) ? true : false,

							'U_DATE'					=> $this->helper->route('ernadoo_phpbbdirectory_ajax_controller')
						));

						return;

					break;

					case 'delete':

						if (!$cat_id)
						{
							trigger_error($this->user->lang['DIR_NO_CAT'] . adm_back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
						}

						$cat_data = $this->_get_cat_info($cat_id);

						$subcats_id = array();
						$subcats = $this->nestedset_category->get_subtree_data($cat_id);

						foreach ($subcats as $row)
						{
							$subcats_id[] = $row['cat_id'];
						}

						$cat_list = $this->categorie->make_cat_select((int) $cat_data['parent_id'], $subcats_id);

						$sql = 'SELECT cat_id
							FROM ' . DIR_CAT_TABLE . '
							WHERE cat_id <> ' . (int) $cat_id;
						$result = $this->db->sql_query_limit($sql, 1);

						if ($this->db->sql_fetchrow($result))
						{
							$this->template->assign_vars(array(
								'S_MOVE_DIR_CAT_OPTIONS'	=> $this->categorie->make_cat_select((int) $cat_data['parent_id'], $subcats_id))
							);
						}
						$this->db->sql_freeresult($result);

						$parent_id = ($this->parent_id == $cat_id) ? 0 : $this->parent_id;

						$this->template->assign_vars(array(
							'S_DELETE_DIR_CAT'		=> true,
							'U_ACTION'				=> $this->u_action . "&amp;parent_id={$parent_id}&amp;action=delete&amp;c=$cat_id",
							'U_BACK'				=> $this->u_action . '&amp;parent_id=' . $this->parent_id,

							'DIR_CAT_NAME'			=> $cat_data['cat_name'],
							'S_HAS_SUBCATS'			=> ($cat_data['right_id'] - $cat_data['left_id'] > 1) ? true : false,
							'S_CATS_LIST'			=> $cat_list,
							'S_ERROR'				=> (sizeof($errors)) ? true : false,
							'ERROR_MSG'				=> (sizeof($errors)) ? implode('<br />', $errors) : '')
						);

						return;

					break;
				}

				// Default management page
				if (!$this->parent_id)
				{
					$navigation = $this->user->lang['DIR_INDEX'];
				}
				else
				{
					$navigation = '<a href="' . $this->u_action . '">' . $this->user->lang['DIR_INDEX'] . '</a>';

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

				if ($action == 'sync' || $action == 'sync_cat')
				{
					$this->template->assign_var('S_RESYNCED', true);
				}

				$sql = 'SELECT cat_id, parent_id, right_id, left_id, cat_name, cat_icon, cat_desc_uid, cat_desc_bitfield, cat_desc, cat_desc_options, cat_links
					FROM ' . DIR_CAT_TABLE . '
					WHERE parent_id = ' . (int) $this->parent_id . '
					ORDER BY left_id';
				$result = $this->db->sql_query($sql);

				if ($row = $this->db->sql_fetchrow($result))
				{
					do
					{
						$folder_image = ($row['left_id'] + 1 != $row['right_id']) ? '<img src="images/icon_subfolder.gif" alt="' . $this->user->lang['DIR_SUBCAT'] . '" />' : '<img src="images/icon_folder.gif" alt="' . $this->user->lang['FOLDER'] . '" />';

						$url = $this->u_action . "&amp;parent_id=$this->parent_id&amp;c={$row['cat_id']}";

						$this->template->assign_block_vars('cats', array(
							'FOLDER_IMAGE'		=> $folder_image,
							'CAT_IMAGE'			=> ($row['cat_icon']) ? '<img src="' . $this->dir_helper->get_img_path('icons', $row['cat_icon']) . '" alt="" />' : '',
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
					'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '',
					'NAVIGATION'	=> $navigation,
					'CAT_BOX'		=> $cat_box,
					'U_SEL_ACTION'	=> $this->u_action,
					'U_ACTION'		=> $this->u_action . '&amp;parent_id=' . $this->parent_id,

					'U_PROGRESS_BAR'	=> $this->u_action . '&amp;action=progress_bar',
					'UA_PROGRESS_BAR'	=> addslashes($this->u_action . '&amp;action=progress_bar'),
				));

			break;

			case 'val':
				$this->page_title = 'ACP_DIRECTORY';
				$this->tpl_name = 'acp_dir_val';

				$mark	= ($request->is_set_post('link_id')) ? $request->variable('link_id', array(0)) : array();
				$start	= $request->variable('start', 0);

				// Sort keys
				$sort_days	= $request->variable('st', 0);
				$sort_key	= $request->variable('sk', 't');
				$sort_dir	= $request->variable('sd', 'd');

				$form_key = 'acp_dir_val';
				add_form_key($form_key);
				$affected_link = array();

				$pagination = $phpbb_container->get('pagination');

				// Number of entries to display
				$per_page = $request->variable('links_per_page', (int) $config['dir_show']);

				// Categorie ordering options
				$limit_days		= array(0 => $this->user->lang['SEE_ALL'], 1 => $this->user->lang['1_DAY'], 7 => $this->user->lang['7_DAYS'], 14 => $this->user->lang['2_WEEKS'], 30 => $this->user->lang['1_MONTH'], 90 => $this->user->lang['3_MONTHS'], 180 => $this->user->lang['6_MONTHS'], 365 => $this->user->lang['1_YEAR']);
				$sort_by_text	= array('a' => $this->user->lang['AUTHOR'], 't' => $this->user->lang['POST_TIME']);
				$sort_by_sql	= array('a' => 'u.username_clean', 't' => array('l.link_time', 'l.link_id'));

				$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
				gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

				if ($submit && sizeof($mark))
				{
					if ($action !== 'disapproved' && !check_form_key($form_key))
					{
						trigger_error($this->user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
					}

					if (!class_exists('messenger'))
					{
						include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
					}
					$messenger = new \messenger(false);

					$phpbb_notifications = $phpbb_container->get('notification_manager');

					$sql_array = array(
						'SELECT'	=> 'a.link_id, a.link_name, a.link_url, a.link_description, a.link_banner, a.link_user_id, a.link_guest_email, u.username, u.user_email, u.user_lang, u.user_notify_type, c.cat_id, c.cat_name',
						'FROM'		=> array(
							DIR_LINK_TABLE	=> 'a'),
						'LEFT_JOIN'	=> array(
								array(
									'FROM'	=> array(USERS_TABLE => 'u'),
									'ON'	=> 'u.user_id = a.link_user_id'
								),
								array(
									'FROM'	=> array(DIR_CAT_TABLE => 'c'),
									'ON'	=> 'a.link_cat = c.cat_id'
								)
							),
						'WHERE'		=> $this->db->sql_in_set('a.link_id', $mark));

					$sql = $this->db->sql_build_query('SELECT', $sql_array);
					$result = $this->db->sql_query($sql);

					$link_data = array();
					while ($row = $this->db->sql_fetchrow($result))
					{
						$row['link_cat'] = $request->variable('c'.$row['link_id'], (int) $row['cat_id']);
						$link_data[$row['link_id']] = $row;
						$affected_link[] = $row['link_name'];

						$cat_data[$row['link_cat']] = isset($cat_data[$row['link_cat']]) ? $cat_data[$row['link_cat']] + 1 : 1;

						if ($action == 'approved')
						{
							$notification_data = array(
									'user_from'			=> (int) $row['link_user_id'],
									'link_id'			=> (int) $row['link_id'],
									'link_name'			=> $row['link_name'],
									'link_url'			=> $row['link_url'],
									'link_description'	=> preg_replace('/(\[.*?\])(.*?)(\[\/.*?\])/si', '\\1', $row['link_description']),
									'cat_name'			=> strip_tags(\ernadoo\phpbbdirectory\core\categorie::getname((int) $row['link_cat'])),
									'cat_id'			=> (int) $row['link_cat'],
							);

							$phpbb_notifications->add_notifications('ernadoo.phpbbdirectory.notification.type.directory_website', $notification_data);

							$sql_ary = array(
								'link_active'	=> 1,
								'link_time'		=> time(),
								'link_cat'		=> (int) $row['link_cat'],
							);

							$sql = 'UPDATE ' . DIR_LINK_TABLE . '
								SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
								WHERE link_id = ' . (int) $row['link_id'];
							$this->db->sql_query($sql);
						}
						else if ($row['link_banner'] && !preg_match('/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|www\.).+/si', $row['link_banner']))
						{
							$banner_img = $this->dir_helper->get_banner_path(basename($row['link_banner']));

							if (file_exists($banner_img))
							{
								@unlink($banner_img);
							}
						}
					}
					$this->db->sql_freeresult($result);

					switch ($action)
					{
						case 'approved':

							foreach ($cat_data as $cat_id => $count)
							{
								$sql = 'UPDATE ' . DIR_CAT_TABLE . '
									SET cat_links = cat_links + '.$count.'
									WHERE cat_id = ' . (int) $cat_id;
								$this->db->sql_query($sql);
							}

							$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_LINK_ACTIVE', time(), array(implode(', ', $affected_link)));

						break;

						case 'disapproved':

							if (confirm_box(true))
							{
								foreach ($mark as $link_id)
								{
									$sql = 'DELETE FROM ' . DIR_LINK_TABLE . ' WHERE link_id = ' . (int) $link_id;
									$this->db->sql_query($sql);
								}

								$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_LINK_DELETE', time(), array(implode(', ', $affected_link)));
							}
							else
							{
								$s_hidden_fields = array(
									'mode'			=> $mode,
									'action'		=> $action,
									'link_id'		=> $mark,
									'submit'		=> 1,
									'start'			=> $start,
								);
								confirm_box(false, $this->user->lang['CONFIRM_OPERATION'], build_hidden_fields($s_hidden_fields));
							}

						break;
					}

					foreach ($link_data as $id => $row)
					{
						$phpbb_notifications->delete_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_in_queue', (int) $row['link_id']);

						// New notification system can't send mail to an anonymous user with an email adress storage in another table than phpbb_users
						if ($row['link_user_id'] == ANONYMOUS)
						{
							$username = $email = $row['link_guest_email'];

							$messenger->template('@ernadoo_phpbbdirectory/directory_website_'.$action, $row['user_lang']);
							$messenger->to($email, $username);

							$messenger->assign_vars(array(
								'USERNAME'	=> htmlspecialchars_decode($username),
								'LINK_NAME'	=> $row['link_name'],
							));

							$messenger->send(NOTIFY_EMAIL);
						}
						else
						{
							$notification_data = array(
								'user_from'			=> (int) $row['link_user_id'],
								'link_id'			=> (int) $row['link_id'],
								'link_name'			=> strip_tags($row['link_name']),
								'cat_name'			=> strip_tags(\ernadoo\phpbbdirectory\core\categorie::getname((int) $row['link_cat'])),
								'cat_id'			=> (int) $row['link_cat'],
							);

							$phpbb_notifications->add_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_'.$action, $notification_data);
						}
					}
				}

				// Define where and sort sql for use in displaying logs
				$sql_where = ($sort_days) ? (time() - ($sort_days * 86400)) : 0;
				$direction = (($sort_dir == 'd') ? 'DESC' : 'ASC');

				if (is_array($sort_by_sql[$sort_key]))
				{
					$sql_sort_order = implode(' ' . $direction . ', ', $sort_by_sql[$sort_key]) . ' ' . $direction;
				}
				else
				{
					$sql_sort_order = $sort_by_sql[$sort_key] . ' ' . $direction;
				}

				$sql = 'SELECT COUNT(1) AS total_links
					FROM ' . DIR_LINK_TABLE . '
					WHERE link_active = 0' .
						(($sql_where) ? " AND link_time >= $sql_where" : '');
				$result = $this->db->sql_query($sql);
				$total_links = (int) $this->db->sql_fetchfield('total_links');
				$this->db->sql_freeresult($result);

				// Make sure $start is set to the last page if it exceeds the amount
				$start = $pagination->validate_start($start, $per_page, $total_links);

				$sql_array = array(
					'SELECT'	=> 'l.link_id, l.link_name, l.link_url, l.link_description, l.link_cat, l.link_user_id, l.link_guest_email, l.link_uid, l.link_bitfield, l.link_flags, l.link_banner, l.link_time, c.cat_name, u.user_id, u.username, u.user_colour',
					'FROM'		=> array(
						DIR_LINK_TABLE	=> 'l'),
					'LEFT_JOIN'	=> array(
							array(
								'FROM'	=> array(DIR_CAT_TABLE => 'c'),
								'ON'	=> 'c.cat_id = l.link_cat'
							),
							array(
								'FROM'	=> array(USERS_TABLE => 'u'),
								'ON'	=> 'u.user_id = l.link_user_id'
							)
						),
					'WHERE'		=> 'l.link_active = 0' . (($sql_where) ? " AND l.link_time >= $sql_where" : ''),
					'ORDER_BY'	=> $sql_sort_order);

				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query_limit($sql, $per_page, $start);

				while ($row = $this->db->sql_fetchrow($result))
				{
					$s_banner = '';
					if (!empty($row['link_banner']))
					{
						if (!preg_match('/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|www\.).+/si', $row['link_banner']))
						{
							$img_src = $this->helper->route('ernadoo_phpbbdirectory_banner_controller', array('banner_img' => $row['link_banner']));
							$physical_path = $this->dir_helper->get_banner_path($row['link_banner']);
						}
						else
						{
							$img_src = $physical_path = $row['link_banner'];
						}

						list($width, $height) = @getimagesize($physical_path);

						if (($width > $config['dir_banner_width'] || $height > $config['dir_banner_height']) && $config['dir_banner_width'] > 0 && $config['dir_banner_height'] > 0)
						{
							$coef_w = $width / $config['dir_banner_width'];
							$coef_h = $height / $config['dir_banner_height'];
							$coef_max = max($coef_w, $coef_h);
							$width /= $coef_max;
							$height /= $coef_max;
						}

						$s_banner = '<img src="' . $img_src . '" width="' . $width . '" height="' . $height . '" border="0" alt="" />';
					}

					$username = ($row['link_user_id'] == ANONYMOUS) ? $row['link_guest_email'] : $row['username'];

					$link_row = array(
						'LINK_URL'			=> $row['link_url'],
						'LINK_NAME'			=> $row['link_name'],
						'LINK_DESC'			=> generate_text_for_display($row['link_description'], $row['link_uid'], $row['link_bitfield'], $row['link_flags']),
						'L_DIR_USER_PROP'	=> $this->user->lang('DIR_USER_PROP', get_username_string('full', $row['link_user_id'], $username, $row['user_colour'], false, append_sid("{$phpbb_admin_path}index.$phpEx", 'i=users&amp;mode=overview')), '<select name=c'.$row['link_id'].'>'.$this->categorie->make_cat_select((int) $row['link_cat']).'</select>', $this->user->format_date($row['link_time'])),
						'BANNER'			=> $s_banner,
						'LINK_ID'			=> $row['link_id'],

					);
					$this->template->assign_block_vars('linkrow', $link_row);
				}
				$this->db->sql_freeresult($result);

				$option_ary = array('approved' => 'DIR_LINK_ACTIVATE', 'disapproved' => 'DIR_LINK_DELETE');

				$base_url = $this->u_action . "&amp;$u_sort_param&amp;links_per_page=$per_page";
				$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_links, $per_page, $start);

				$this->template->assign_vars(array(
					'S_LINKS_OPTIONS'	=> build_select($option_ary),

					'S_LIMIT_DAYS'		=> $s_limit_days,
					'S_SORT_KEY'		=> $s_sort_key,
					'S_SORT_DIR'		=> $s_sort_dir,
					'LINKS_PER_PAGE'	=> $per_page,

					'U_ACTION'			=> $this->u_action . "&amp;$u_sort_param&amp;links_per_page=$per_page&amp;start=$start",
				));

			break;
		}
	}

	/**
	* Display thumb services available
	* 
	* @param 	string	$url_selected
	* @return 	string
	*/
	public function get_thumb_service_list($url_selected)
	{
		$thumbshot = array(
			'apercite.fr'		=> 'http://www.apercite.fr/apercite/120x90/oui/oui/',
			'easy-thumb.net'	=> 'http://www.easy-thumb.net/min.html?url=',
		);

		$tpl = '';
		foreach ($thumbshot as $service => $url)
		{
			$selected = ($url == $url_selected) ? 'selected="selected"' : '';

			$tpl .= '<option value="' . $url . '" ' . $selected . '>' . $service . '</option>';
		}
		$tpl .= '</select>';

		return ($tpl);
	}

	/**
	* Display order drop-down list
	* 
	* @param	string	$order_selected
	* @return	string
	*/
	public function get_order_list($order_selected)
	{
		$order_array = array(
			'a a',
			'a d',
			't a',
			't d',
			'r a',
			'r d',
			's a',
			's d',
			'v a',
			'v d'
		);
		$tpl = '';
		foreach ($order_array as $i)
		{
			$selected = ($i == $order_selected) ? 'selected="selected"' : '';
			$order_substr = trim(str_replace(' ', '_', $i));
			$tpl .= '<option value="' . $i . '" ' . $selected . '>' . $this->user->lang['DIR_ORDER_' . strtoupper($order_substr)] . '</option>';
		}
		$tpl .= '</select>';

		return ($tpl);
	}

	/**
	* Get category details
	* 
	* @param	int		$cat_id	The category ID
	* @return 	array
	*/
	private function _get_cat_info($cat_id)
	{
		$sql = 'SELECT cat_id, parent_id, right_id, left_id, cat_desc, cat_desc_uid, cat_desc_options, cat_icon, cat_name, display_subcat_list, cat_allow_comments, cat_allow_votes, cat_must_describe, cat_count_all, cat_validate, cat_cron_freq, cat_cron_nb_check, cat_link_back, cat_cron_enable, cat_cron_next
			FROM ' . DIR_CAT_TABLE . '
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
	* @param	array	$cat_data
	* @return	array
	*/
	private function _update_cat_data(&$cat_data)
	{
		$errors = array();

		if (!$cat_data['cat_name'])
		{
			$errors[] = $this->user->lang['DIR_CAT_NAME_EMPTY'];
		}

		if (utf8_strlen($cat_data['cat_desc']) > 4000)
		{
			$errors[] = $this->user->lang['DIR_CAT_DESC_TOO_LONG'];
		}

		if (($cat_data['cat_cron_enable'] && $cat_data['cat_cron_freq'] <= 0) || $cat_data['cat_cron_nb_check'] < 0)
		{
			$errors[] = $this->user->lang['DIR_CAT_DATA_NEGATIVE'];
		}

		// Unset data that are not database fields
		$cat_data_sql = $cat_data;

		// What are we going to do tonight Brain? The same thing we do everynight,
		// try to take over the world ... or decide whether to continue update
		// and if so, whether it's a new cat/link or an existing one
		if (sizeof($errors))
		{
			return $errors;
		}

		if (!$cat_data_sql['cat_link_back'])
		{
			$cat_data_sql['cat_cron_enable'] = 0;
		}

		if (!$cat_data_sql['cat_cron_enable'])
		{
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

			$cat_data = $this->nestedset_category->insert($cat_data_sql);

			if ($cat_data_sql['parent_id'])
			{
				$this->nestedset_category->change_parent($cat_data['cat_id'], $cat_data_sql['parent_id']);
			}

			$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_ADD', time(), array($cat_data['cat_name']));
		}
		else
		{
			$row = $this->_get_cat_info($cat_data_sql['cat_id']);

			if ($row['parent_id'] != $cat_data_sql['parent_id'])
			{
				$this->nestedset_category->change_parent($cat_data_sql['cat_id'], $cat_data_sql['parent_id']);
			}

			if ($cat_data_sql['cat_cron_enable'])
			{
				if ($row['cat_cron_freq'] != $cat_data_sql['cat_cron_freq'] || !$row['cat_cron_enable'])
				{
					$cat_data_sql['cat_cron_next'] = time() + $cat_data_sql['cat_cron_freq']*86400;
				}
			}

			if ($row['cat_name'] != $cat_data_sql['cat_name'])
			{
				// the cat name has changed, clear the parents list of all categories (for safety)
				$sql = 'UPDATE ' . DIR_CAT_TABLE . "
					SET cat_parents = ''";
				$this->db->sql_query($sql);
			}

			// Setting the cat id to the categorie id is not really received well by some dbs. ;)
			$cat_id = $cat_data_sql['cat_id'];
			unset($cat_data_sql['cat_id']);

			$sql = 'UPDATE ' . DIR_CAT_TABLE . '
				SET ' . $this->db->sql_build_array('UPDATE', $cat_data_sql) . '
				WHERE cat_id = ' . (int) $cat_id;
			$this->db->sql_query($sql);

			$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_EDIT', time(), array($cat_data['cat_name']));
		}

		return $errors;
	}

	/**
	* Display progress bar for syncinc categories
	* 
	* @param	int	$start
	* @param	int	$total
	* @return	null
	*/
	private function _display_progress_bar($start, $total)
	{
		adm_page_header($this->user->lang['SYNC_IN_PROGRESS']);

		$this->template->set_filenames(array(
			'body'	=> 'progress_bar.html')
		);

		$this->template->assign_vars(array(
			'L_PROGRESS'			=> $this->user->lang['SYNC_IN_PROGRESS'],
			'L_PROGRESS_EXPLAIN'	=> ($start && $total) ? $this->user->lang('SYNC_IN_PROGRESS_EXPLAIN', $start, $total) : $this->user->lang['SYNC_IN_PROGRESS'])
		);

		adm_page_footer();
	}

	/**
	* Remove complete category
	* 
	* @param	int		$cat_id			The category ID
	* @param	string	$action_links	Action for categories links
	* @param	string	$action_subcats	Action for sub-categories
	* @param	int		$links_to_id	New category ID for links
	* @param	int		$subcats_to_id	New category ID for sub-categories
	* @return array
	*/
	private function _delete_cat($cat_id, $action_links = 'delete', $action_subcats = 'delete', $links_to_id = 0, $subcats_to_id = 0)
	{
		$cat_data = $this->_get_cat_info($cat_id);

		$errors = array();
		$log_action_links = $log_action_cats = $links_to_name = $subcats_to_name = '';

		if ($action_links == 'delete')
		{
			$log_action_links = 'LINKS';
			$errors = array_merge($errors, $this->_delete_cat_content($cat_id));
		}
		else if ($action_links == 'move')
		{
			if (!$links_to_id)
			{
				$errors[] = $this->user->lang['DIR_NO_DESTINATION_CAT'];
			}
			else
			{
				$log_action_links = 'MOVE_LINKS';

				$sql = 'SELECT cat_name
					FROM ' . DIR_CAT_TABLE . '
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
					$this->_move_cat_content($cat_id, $links_to_id);
				}
			}
		}

		if (sizeof($errors))
		{
			return $errors;
		}

		if ($action_subcats == 'delete')
		{
			$log_action_cats = 'CATS';
		}
		else if ($action_subcats == 'move')
		{
			if (!$subcats_to_id)
			{
				$errors[] = $this->user->lang['DIR_NO_DESTINATION_CAT'];
			}
			else
			{
				$log_action_cats = 'MOVE_CATS';

				$subcats_to_name = $row['cat_name'];
				$this->nestedset_category->move_children($cat_id, $subcats_to_id);
			}
		}

		if (sizeof($errors))
		{
			return $errors;
		}

		$this->nestedset_category->delete($cat_id);

		$log_action = implode('_', array($log_action_links, $log_action_cats));

		switch ($log_action)
		{
			case 'MOVE_LINKS_MOVE_CATS':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_MOVE_LINKS_MOVE_CATS', time(), array($links_to_name, $subcats_to_name, $cat_data['cat_name']));
			break;

			case 'MOVE_LINKS_CATS':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_MOVE_LINKS_CATS', time(), array($links_to_name, $cat_data['cat_name']));
			break;

			case 'LINKS_MOVE_CATS':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_LINKS_MOVE_CATS', time(), array($subcats_to_name, $cat_data['cat_name']));
			break;

			case '_MOVE_CATS':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_MOVE_CATS', time(), array($subcats_to_name, $cat_data['cat_name']));
			break;

			case 'MOVE_LINKS_':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_MOVE_LINKS', time(), array($links_to_name, $cat_data['cat_name']));
			break;

			case 'LINKS_CATS':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_LINKS_CATS', time(), array($cat_data['cat_name']));
			break;

			case '_CATS':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_CATS', time(), array($cat_data['cat_name']));
			break;

			case 'LINKS_':
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_LINKS', time(), array($cat_data['cat_name']));
			break;

			default:
				$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_CAT_DEL_CAT', time(), array($cat_data['cat_name']));
			break;
		}

		return $errors;
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
		$sql = 'UPDATE ' . DIR_LINK_TABLE . '
			SET link_cat = ' . (int) $to_id . '
			WHERE link_cat = ' . (int) $from_id;
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . DIR_WATCH_TABLE . '
			WHERE cat_id = ' . (int) $from_id;
		$this->db->sql_query($sql);

		$this->_sync_dir_cat($to_id);
	}

	/**
	* Delete category content
	* 
	* @param	int		$cat_id	The category ID
	* @return	array
	*/
	private function _delete_cat_content($cat_id)
	{
		$this->db->sql_transaction('begin');

		// Before we remove anything we make sure we are able to adjust the post counts later. ;)
		$sql = 'SELECT link_id, link_banner
			FROM ' . DIR_LINK_TABLE . '
			WHERE link_cat = ' . (int) $cat_id;
		$result = $this->db->sql_query($sql);

		$link_ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$link_ids[] = $row['link_id'];

			if ($row['link_banner'] && !preg_match('/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|www\.).+/si', $row['link_banner']))
			{
				$banner_img = $this->dir_helper->get_banner_path(basename($row['link_banner']));

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
				DIR_COMMENT_TABLE	=> 'comment_link_id',
				DIR_VOTE_TABLE		=> 'vote_link_id',
			);

			foreach ($link_datas_ary as $table => $field)
			{
				$this->db->sql_query("DELETE FROM $table WHERE " . $this->db->sql_in_set($field, $link_ids));
			}
		}

		// Delete cats datas
		$cat_datas_ary = array(
			DIR_LINK_TABLE	=> 'link_cat',
			DIR_WATCH_TABLE	=> 'cat_id',
		);

		foreach ($cat_datas_ary as $table => $field)
		{
			$this->db->sql_query("DELETE FROM $table WHERE $field = " . (int) $cat_id);
		}

		$this->db->sql_transaction('commit');

		return array();
	}

	/**
	* Get orphan banners
	* 
	* @param	bool		$delete	True if we want to delete banners, else false
	* @return	null|int	Number of orphan files, else false
	*/
	private function _orphan_files($delete = false)
	{
		$banner_path = $this->dir_helper->get_banner_path();
		$imglist = filelist($banner_path);
		$physical_files = $logical_files = $orphan_files = array();

		if (!empty($imglist['']))
		{
			$imglist = array_values($imglist);
			$imglist = $imglist[0];

			foreach($imglist as $img)
			{
				$physical_files[] = $img;
			}
			$sql = 'SELECT link_banner FROM ' . DIR_LINK_TABLE . '
				WHERE link_banner <> \'\'';
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				if (!preg_match('/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|www\.).+/si', $row['link_banner']))
				{
					$logical_files[] = basename($row['link_banner']);
				}
			}
			$this->db->sql_freeresult($result);

			$orphan_files = array_diff($physical_files, $logical_files);
		}

		if (!$delete)
		{
			return sizeof($orphan_files);
		}

		$dh = @opendir($banner_path);
		while (($file = readdir($dh)) !== false)
		{
			if (in_array($file, $orphan_files))
			{
				@unlink($this->dir_helper->get_banner_path($file));
			}
		}
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
			FROM ' . DIR_LINK_TABLE . '
			WHERE link_cat = ' . (int) $cat_id . '
				AND link_active = 1';
		$result = $this->db->sql_query($sql);
		$total_links = (int) $this->db->sql_fetchfield('num_links');
		$this->db->sql_freeresult($result);

		$sql = 'UPDATE ' . DIR_CAT_TABLE . '
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

		$sql = 'UPDATE ' . DIR_LINK_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE link_id BETWEEN ' . (int) $start . ' AND ' . (int) $stop;
		$this->db->sql_query($sql);

		$sql = 'SELECT vote_link_id, COUNT(vote_note) AS nb_vote, SUM(vote_note) AS total FROM ' . DIR_VOTE_TABLE . '
			WHERE vote_link_id BETWEEN ' . (int) $start . ' AND ' . (int) $stop . '
			GROUP BY vote_link_id';
		$result = $this->db->sql_query($sql);
		while ($tmp = $this->db->sql_fetchrow($result))
		{
			$sql = 'UPDATE ' . DIR_LINK_TABLE . '
				SET link_note = ' . (int) $tmp['total'] . ', link_vote = ' . (int) $tmp['nb_vote'] . '
				WHERE link_id = ' . (int) $tmp['vote_link_id'];
			$this->db->sql_query($sql);
		}
		$this->db->sql_freeresult($result);

		$sql = 'SELECT 	comment_link_id, COUNT(comment_id) AS nb_comment
			FROM ' . DIR_COMMENT_TABLE . '
			WHERE comment_link_id BETWEEN ' . (int) $start . ' AND ' . (int) $stop . '
			GROUP BY comment_link_id';
		$result = $this->db->sql_query($sql);
		while ($tmp = $this->db->sql_fetchrow($result))
		{
			$sql = 'UPDATE ' . DIR_LINK_TABLE . '
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

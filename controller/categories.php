<?php
/**
 *
 * @package phpBB Directory
 * @copyright (c) 2014 ErnadoO
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

namespace ernadoo\phpbbdirectory\controller;

class categories
{
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

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var \phpbb\ext\ernadoo\phpbbdirectory\core\categorie */
	protected $categorie;

	/** @var \phpbb\ext\ernadoo\phpbbdirectory\core\link */
	protected $link;


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
	 * @param \phpbb\pagination $pagination
	 * @param string         $root_path   phpBB root path
	 * @param string         $php_ext   phpEx
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\auth\auth $auth, \phpbb\pagination $pagination, $root_path, $php_ext, $categorie, $link)
	{
		$this->db			= $db;
		$this->config		= $config;
		$this->template		= $template;
		$this->user			= $user;
		$this->helper		= $helper;
		$this->request		= $request;
		$this->auth			= $auth;
		$this->pagination	= $pagination;
		$this->root_path	= $root_path;
		$this->php_ext		= $php_ext;
		$this->categorie	= $categorie;
		$this->link			= $link;

		$this->user->add_lang_ext('ernadoo/phpbbdirectory', array('directory'));

		$this->template->assign_vars(array(
			'S_PHPBB_DIRECTORY'				=> true,
			'DIRECTORY_TRANSLATION_INFO'	=> (!empty($user->lang['DIRECTORY_TRANSLATION_INFO'])) ? $user->lang['DIRECTORY_TRANSLATION_INFO'] : '',
		));

		$this->title = $this->user->lang['DIRECTORY'];
	}

	/**
	 * categories::base()
	 *
	 * @return
	 */
	public function base()
	{
		$this->categorie->display();
		$this->link->recents();

		return $this->helper->render('body.html', $this->title);
	}

	/**
	 * categories::view()
	 *
	 * @param mixed $cat_id
	 * @param mixed $page
	 * @param mixed $sort_days
	 * @param mixed $sort_key
	 * @param mixed $sort_dir
	 * @param string $mode
	 * @return
	 */
	public function view($cat_id, $page, $sort_days, $sort_key, $sort_dir, $mode = '')
	{
		if (!$cat_id)
		{
			send_status_line(404, 'Not Found');
			redirect('directory');
		}
		else
		{
			if(false === $this->categorie->get($cat_id))
			{
				return $this->helper->error($this->user->lang['DIR_ERROR_NO_CATS'], 410);
			}
			$this->user->add_lang_ext('ernadoo/phpbbdirectory', array('help' => 'directory_flags'));
		}

		$start = ($page - 1) * $this->config['dir_show'];

		$default_sort_days	= 0;
		$default_sort_key	= (string)substr($this->config['dir_default_order'], 0, 1);
		$default_sort_dir	= (string)substr($this->config['dir_default_order'], 2);

		$sort_days	= $this->request->variable('st', $default_sort_days);
		$sort_key 	= (!$sort_key) ? $this->request->variable('sk', $default_sort_key) : $sort_key;
		$sort_dir	= (!$sort_dir) ? $this->request->variable('sd', $default_sort_dir) : $sort_dir;
		$link_list = array();

		// Categorie ordering options
		$limit_days		= array(0 => $this->user->lang['SEE_ALL'], 1 => $this->user->lang['1_DAY'], 7 => $this->user->lang['7_DAYS'], 14 => $this->user->lang['2_WEEKS'], 30 => $this->user->lang['1_MONTH'], 90 => $this->user->lang['3_MONTHS'], 180 => $this->user->lang['6_MONTHS'], 365 => $this->user->lang['1_YEAR']);
		$sort_by_text	= array('a' => $this->user->lang['AUTHOR'], 't' => $this->user->lang['POST_TIME'], 'r' => $this->user->lang['DIR_COMMENTS_ORDER'], 's' =>  $this->user->lang['DIR_NAME_ORDER'], 'v' => $this->user->lang['DIR_NB_CLICS_ORDER']);
		$sort_by_sql	= array('a' => 'u.username_clean', 't' => array('l.link_time', 'l.link_id'), 'r' => 'l.link_comment', 's' => 'l.link_name', 'v' => 'l.link_view');

		if ($this->config['dir_activ_pagerank'])
		{
			$sort_by_text['p'] = $this->user->lang['DIR_PR_ORDER'];
			$sort_by_sql['p'] = 'l.link_pagerank';
		}

		$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param, $default_sort_days, $default_sort_key, $default_sort_dir);

		$u_sort_param = ($sort_days === 0 && $sort_key == (string)substr($this->config['dir_default_order'], 0, 1) && $sort_dir == (string)substr($this->config['dir_default_order'], 2)) ? array() : array('sort_days' => $sort_days, 'sort_key' => $sort_key, 'sort_dir' => $sort_dir);

		// Are we watching this categorie?
		$s_watching_categorie = array(
			'link'			=> '',
			'link_toggle'	=> '',
			'title'			=> '',
			'title_toggle'	=> '',
			'is_watching'	=> false,
		);

		if ($this->config['email_enable'] && $this->user->data['is_registered'])
		{
			$notify_status = (isset($this->categorie->data['notify_status'])) ? $this->categorie->data['notify_status'] : null;

			if(($message = $this->categorie->watch_categorie($mode, $s_watching_categorie, $this->user->data['user_id'], $cat_id, $notify_status, $this->categorie->data['cat_name'])))
			{
				return $this->helper->error($message, 200);
			}
		}

		// A deadline has been selected
		if ($sort_days)
		{
			$min_post_time = time() - ($sort_days * 86400);

			$sql = 'SELECT COUNT(link_id) AS nb_links
			FROM ' . DIR_LINK_TABLE . '
			WHERE link_cat = ' . (int)$cat_id . '
				AND link_time >= ' . $min_post_time;
			$result = $this->db->sql_query($sql);
			$nb_links = (int)$this->db->sql_fetchfield('nb_links');
			$this->db->sql_freeresult($result);

			if (isset($_POST['sort']))
			{
				$start = 0;
			}
			$sql_limit_time = " AND l.link_time >= $min_post_time";
		}
		else
		{
			$sql_limit_time = '';
			$nb_links		= (int)$this->categorie->data['cat_links'];
		}

		// Make sure $start is set to the last page if it exceeds the amount
		$start = $this->pagination->validate_start($start, $this->config['dir_show'], $nb_links);

		// Build navigation links
		$this->categorie->generate_dir_nav($this->categorie->data);

		// Jumpbox
		$this->categorie->make_cat_jumpbox();

		$base_url = array(
			'routes'	=> 'phpbbdirectory_page_controller',
			'params'	=> array_merge(array('cat_id' => $cat_id), $u_sort_param),
		);

		$this->pagination->generate_template_pagination($base_url, 'pagination', 'page', $nb_links, $this->config['dir_show'], $start);

		$this->template->assign_vars(array(
			'S_SELECT_SORT_DIR'		=> $s_sort_dir,
			'S_SELECT_SORT_KEY'		=> $s_sort_key,
			'S_SELECT_SORT_DAYS'	=> $s_limit_days,
			'S_CATLIST'				=> $this->categorie->make_cat_select($cat_id),
			//'S_JUMPBOX_ACTION'		=> $this->helper->url('directory/categorie/'),
			'S_PAGE_ACTION'			=> $this->helper->route('phpbbdirectory_page_controller', array('cat_id' => $cat_id, 'page' => $page)),
			'S_CAT_ID'				=> $cat_id,

			'TOTAL_LINKS'			=> $this->user->lang('DIR_NB_LINKS', (int)$nb_links),

			'U_WATCH_CAT'			=> $s_watching_categorie['link'],
			'U_WATCH_CAT_TOGGLE'	=> $s_watching_categorie['link_toggle'],
			'S_WATCH_CAT_TITLE'		=> $s_watching_categorie['title'],
			'S_WATCH_CAT_TOGGLE'	=> $s_watching_categorie['title_toggle'],
			'S_WATCHING_CAT'		=> $s_watching_categorie['is_watching'],
		));

		// If the user is trying to reach late pages, start searching from the end
		$store_reverse = false;
		$sql_limit = $this->config['dir_show'];
		if ($start > $nb_links / 2)
		{
			$store_reverse = true;

			// Select the sort order
			$direction = (($sort_dir == 'd') ? 'ASC' : 'DESC');

			$sql_limit = $this->pagination->reverse_limit($start, $sql_limit, $nb_links);
			$sql_start = $this->pagination->reverse_start($start, $sql_limit, $nb_links);
		}
		else
		{
			// Select the sort order
			$direction = (($sort_dir == 'd') ? 'DESC' : 'ASC');
			$sql_start = $start;
		}

		if (is_array($sort_by_sql[$sort_key]))
		{
			$sql_sort_order = implode(' ' . $direction . ', ', $sort_by_sql[$sort_key]) . ' ' . $direction;
		}
		else
		{
			$sql_sort_order = $sort_by_sql[$sort_key] . ' ' . $direction;
		}

		// Grab just the sorted link ids
		$sql_array = array(
			'SELECT'	=> 'l.link_id',
			'FROM'		=> array(
					DIR_LINK_TABLE	=> 'l'),
			'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(USERS_TABLE	=> 'u'),
						'ON'	=> 'l.link_user_id = u.user_id'
					),
			),
			'WHERE'		=> "l.link_cat = $cat_id
				AND l.link_active = 1
					$sql_limit_time",
			'ORDER_BY'	=> $sql_sort_order
		);

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $sql_limit, $sql_start);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$link_list[] = (int)$row['link_id'];
		}
		$this->db->sql_freeresult($result);

		if (sizeof($link_list))
		{
			/*
			   ** We get links, informations about poster, votes and number of comments
			*/
			$sql_array = array(
				'SELECT'	=> 'l.link_id, l.link_cat, l.link_url, l.link_user_id, l.link_comment, l. link_description, l.link_banner, l.link_rss, l. link_uid, l.link_bitfield, l.link_flags, l.link_vote, l.link_note, l.link_view, l.link_time, l.link_name, l.link_flag, l.link_pagerank, l.link_thumb, u.user_id, u.username, u.user_colour, v.vote_user_id',
				'FROM'		=> array(
						DIR_LINK_TABLE	=> 'l'),
				'LEFT_JOIN'	=> array(
						array(
							'FROM'	=> array(USERS_TABLE	=> 'u'),
							'ON'	=> 'l.link_user_id = u.user_id'
						),
						array(
							'FROM'	=> array(DIR_VOTE_TABLE => 'v'),
							'ON'	=> 'l.link_id = v.vote_link_id AND v.vote_user_id = ' . $this->user->data['user_id']
						)
				),
				'WHERE'		=> $this->db->sql_in_set('l.link_id', $link_list)
			);

			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);

			while ($site = $this->db->sql_fetchrow($result))
			{
				$rowset[$site['link_id']] = $site;
			}
			$this->db->sql_freeresult($result);

			$link_list = ($store_reverse) ? array_reverse($link_list) : $link_list;

			$votes_status 		= ($this->categorie->data['cat_allow_votes']) ? true : false;
			$comments_status 	= ($this->categorie->data['cat_allow_comments']) ? true : false;

			foreach ($link_list as $link_id)
			{
				$site = &$rowset[$link_id];

				$s_flag		= $this->link->display_flag($site);
				$s_note		= $this->link->display_note($site['link_note'], $site['link_vote'], $votes_status);
				$s_thumb	= $this->link->display_thumb($site);
				$s_vote		= $this->link->display_vote($site, $votes_status);
				$s_comment	= $this->link->display_comment($site['link_id'], $site['link_comment'], $comments_status);
				$s_banner	= $this->link->display_bann($site);
				$s_pr		= $this->link->display_pagerank($site);
				$s_rss		= $site['link_rss'];

				$edit_allowed 	= ($this->user->data['is_registered'] && ($this->auth->acl_get('m_edit_dir') || ($this->user->data['user_id'] == $site['link_user_id'] && $this->auth->acl_get('u_edit_dir'))));
				$delete_allowed = ($this->user->data['is_registered'] && ($this->auth->acl_get('m_delete_dir') || ($this->user->data['user_id'] == $site['link_user_id'] && $this->auth->acl_get('u_delete_dir'))));

				$this->template->assign_block_vars('site', array(
					'LINK_ID'		=> $site['link_id'],
					'USER'			=> get_username_string('full', $site['link_user_id'], $site['username'], $site['user_colour']),
					'DESCRIPTION' 	=> generate_text_for_display($site['link_description'], $site['link_uid'], $site['link_bitfield'], $site['link_flags']),
					'THUMB'			=> '<img src="'.$s_thumb.'" alt="'.$this->user->lang['DIR_THUMB'].'" title="'.$site['link_name'].'"/>',
					'NOTE'			=> $s_note,
					'NB_VOTE'		=> $this->user->lang('DIR_NB_VOTES', (int)$site['link_vote']),
					'VOTE'			=> $s_vote,
					'PAGERANK'		=> $s_pr,
					'COMMENT'		=> $s_comment,
					'BANNER'		=> $s_banner,
					'RSS'			=> $s_rss,
					'COUNT'			=> $this->user->lang('DIR_NB_CLICKS', (int)$site['link_view']),
					'TIME'			=> ($site['link_time']) ? $this->user->format_date($site['link_time']) : '',
					'NAME'			=> $site['link_name'],

					'S_NEW_LINK'	=> (((time() - $site['link_time']) / 86400) <= $this->config['dir_new_time']) ? true : false,
					'S_HAVE_FLAG'	=> $this->config['dir_activ_flag'] ? true : false,

					'IMG_FLAG'		=> $s_flag,
					'ON_CLICK' 		=> "onclick=\"window.open('".$this->helper->route('phpbbdirectory_view_controller', array('link_id' => (int)$site['link_id']))."'); return false;\"",

					'U_LINK'	=> $site['link_url'],
					'U_EDIT'	=> $edit_allowed ? $this->helper->route('phpbbdirectory_edit_controller', array('cat_id' => (int)$cat_id, 'link_id' => (int)$site['link_id'])) : '',
					'U_DELETE'	=> $edit_allowed ? $this->helper->route('phpbbdirectory_delete_controller', array('cat_id' => (int)$cat_id, 'link_id' => (int)$site['link_id'], '_referer' => $this->helper->get_current_url())) : '',
				));
			}
		}
		else
		{
			$this->template->assign_block_vars('no_draw_link', array());
		}

		$this->categorie->display($cat_id);
		return $this->helper->render('view_cat.html', $this->title . ' - ' . $this->categorie->data['cat_name']);
	}

	public function return_date()
	{
		if (!$this->request->is_ajax())
		{
			return $this->helper->error($this->user->lang['DIR_ERROR_NOT_AUTH'], 403);
		}

		$timestamp = $this->request->variable('timestamp', 0);
		$json_response = new \phpbb\json_response;
		$json_response->send(array(
			'success'	=> true,
			'DATE'		=> $this->user->format_date((int)$timestamp),
		));
	}
}

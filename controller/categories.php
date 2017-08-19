<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use \ernadoo\phpbbdirectory\core\helper;
use E1379\SpeakingUrl\SpeakingUrl;

class categories extends helper
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

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \ernadoo\phpbbdirectory\core\categorie */
	protected $categorie;

	/** @var \ernadoo\phpbbdirectory\core\link */
	protected $link;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface					$db			Database object
	* @param \phpbb\config\config								$config		Config object
	* @param \phpbb\language\language							$language	Language object
	* @param \phpbb\template\template							$template	Template object
	* @param \phpbb\user										$user		User object
	* @param \phpbb\controller\helper							$helper		Controller helper object
	* @param \phpbb\request\request								$request	Request object
	* @param \phpbb\auth\auth									$auth		Auth object
	* @param \phpbb\pagination									$pagination	Pagination object
	* @param \ernadoo\phpbbdirectory\core\categorie				$categorie	PhpBB Directory extension categorie object
	* @param \ernadoo\phpbbdirectory\core\link					$link		PhpBB Directory extension link object
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\language\language $language, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\auth\auth $auth, \phpbb\pagination $pagination, \ernadoo\phpbbdirectory\core\categorie $categorie, \ernadoo\phpbbdirectory\core\link $link)
	{
		$this->db			= $db;
		$this->config		= $config;
		$this->language		= $language;
		$this->template		= $template;
		$this->user			= $user;
		$this->helper		= $helper;
		$this->request		= $request;
		$this->auth			= $auth;
		$this->pagination	= $pagination;
		$this->categorie	= $categorie;
		$this->link			= $link;

		$language->add_lang('directory', 'ernadoo/phpbbdirectory');

		$template->assign_vars(array(
			'S_PHPBB_DIRECTORY'	=> true,
		));
	}

	/**
	* Base controller to be accessed with the URL /directory
	*
	* @return	\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	*/
	public function base()
	{
		$this->categorie->display();
		$this->link->recents();

		return $this->helper->render('body.html', $this->language->lang('DIRECTORY'));
	}

	/**
	* Legacy view controller for display a category
	* Used with /directory/categorie/{cat_id}
	* @deprecated 2.0.0 No longer used since dynamic routing.
	*
	* @param	int		$cat_id		The category ID
	* @param	int		$page		Page number taken from the URL
	* @param	int		$sort_days	Specifies the maximum amount of days a link may be old
	* @param	string	$sort_key	is the key of $sort_by_sql for the selected sorting: a|t|r|s|v
	* @param	string	$sort_dir	is either a or d representing ASC and DESC (ascending|descending)
	* @param	string	$mode		watch|unwatch
	* @return	\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	* @throws	\phpbb\exception\http_exception
	*/
	public function view($cat_id, $page, $sort_days, $sort_key, $sort_dir, $mode = '')
	{
		$url = $this->helper->route('ernadoo_phpbbdirectory_dynamic_route_' . $cat_id, array('page' => $page, 'sort_days' => $sort_days, 'sort_key' => $sort_key, 'sort_dir' => $sort_dir));

		return new RedirectResponse($url, 301);
	}

	/**
	* View controller for display a category
	*
	* @param	int		$cat_id		The category ID
	* @param	int		$page		Page number taken from the URL
	* @param	int		$sort_days	Specifies the maximum amount of days a link may be old
	* @param	string	$sort_key	is the key of $sort_by_sql for the selected sorting: a|t|r|s|v|p
	* @param	string	$sort_dir	is either a or d representing ASC and DESC (ascending|descending)
	* @param	string	$mode		watch|unwatch
	* @return	\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	* @throws	\phpbb\exception\http_exception
	*/
	public function view_route($cat_id, $page = 1, $sort_days = 0, $sort_key = '', $sort_dir = '', $mode = '')
	{
		if (false === $this->categorie->get($cat_id))
		{
			throw new \phpbb\exception\http_exception(404, 'DIR_ERROR_NO_CATS');
		}

		$start = ($page - 1) * $this->config['dir_show'];

		$default_sort_days	= 0;
		$default_sort_key	= (string) substr($this->config['dir_default_order'], 0, 1);
		$default_sort_dir	= (string) substr($this->config['dir_default_order'], 2);

		$sort_days	= (!$sort_days) ? $this->request->variable('st', $default_sort_days) : $sort_days;
		$sort_key 	= (!$sort_key) ? $this->request->variable('sk', $default_sort_key) : $sort_key;
		$sort_dir	= (!$sort_dir) ? $this->request->variable('sd', $default_sort_dir) : $sort_dir;
		$link_list = $rowset = array();

		// Categorie ordering options
		$limit_days		= array(0 => $this->language->lang('SEE_ALL'), 1 => $this->language->lang('1_DAY'), 7 => $this->language->lang('7_DAYS'), 14 => $this->language->lang('2_WEEKS'), 30 => $this->language->lang('1_MONTH'), 90 => $this->language->lang('3_MONTHS'), 180 => $this->language->lang('6_MONTHS'), 365 => $this->language->lang('1_YEAR'));
		$sort_by_text	= array('a' => $this->language->lang('AUTHOR'), 't' => $this->language->lang('POST_TIME'), 'r' => $this->language->lang('DIR_COMMENTS_ORDER'), 's' =>  $this->language->lang('DIR_NAME_ORDER'), 'v' => $this->language->lang('DIR_NB_CLICKS_ORDER'));
		$sort_by_sql	= array('a' => 'u.username_clean', 't' => array('l.link_time', 'l.link_id'), 'r' => 'l.link_comment', 's' => 'LOWER(l.link_name)', 'v' => 'l.link_view');

		$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param, $default_sort_days, $default_sort_key, $default_sort_dir);

		$u_sort_param = ($sort_days === $default_sort_days && $sort_key == $default_sort_key && $sort_dir == $default_sort_dir) ? array() : array('sort_days' => $sort_days, 'sort_key' => $sort_key, 'sort_dir' => $sort_dir);

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

			if (($message = $this->categorie->watch_categorie($mode, $s_watching_categorie, $this->user->data['user_id'], $cat_id, $notify_status)))
			{
				return $this->helper->message($message);
			}
		}

		// A deadline has been selected
		if ($sort_days)
		{
			$min_post_time = time() - ($sort_days * 86400);

			$sql = 'SELECT COUNT(link_id) AS nb_links
				FROM ' . $this->links_table . '
				WHERE link_cat = ' . (int) $cat_id . '
					AND link_time >= ' . $min_post_time;
			$result = $this->db->sql_query($sql);
			$nb_links = (int) $this->db->sql_fetchfield('nb_links');
			$this->db->sql_freeresult($result);

			if ($this->request->is_set_post('sort'))
			{
				$start = 0;
			}
			$sql_limit_time = " AND l.link_time >= $min_post_time";
		}
		else
		{
			$sql_limit_time = '';
			$nb_links		= (int) $this->categorie->data['cat_links'];
		}

		// Make sure $start is set to the last page if it exceeds the amount
		$start = $this->pagination->validate_start($start, $this->config['dir_show'], $nb_links);

		// Build navigation links
		$this->categorie->generate_dir_nav($this->categorie->data);

		// Jumpbox
		$this->categorie->make_cat_jumpbox();

		$base_url = array(
			'routes'	=> 'ernadoo_phpbbdirectory_dynamic_route_' . $cat_id,
			'params'	=> array_merge(array('cat_id' => $cat_id), $u_sort_param),
		);

		$this->pagination->generate_template_pagination($base_url, 'pagination', 'page', $nb_links, $this->config['dir_show'], $start);

		$this->template->assign_vars(array(
			'CAT_NAME'				=> $this->categorie->data['cat_name'],

			'S_SELECT_SORT_DIR'		=> $s_sort_dir,
			'S_SELECT_SORT_KEY'		=> $s_sort_key,
			'S_SELECT_SORT_DAYS'	=> $s_limit_days,
			'S_CATLIST'				=> $this->categorie->make_cat_select($cat_id),
			'S_PAGE_ACTION'			=> $this->helper->route('ernadoo_phpbbdirectory_dynamic_route_' . $cat_id, array('page' => $page)),
			'S_CAT_ID'				=> $cat_id,

			'TOTAL_LINKS'			=> $this->language->lang('DIR_NB_LINKS', (int) $nb_links),

			'U_NEW_SITE' 			=> $this->helper->route('ernadoo_phpbbdirectory_new_controller', array('cat_id' => $cat_id)),

			'U_VIEW_CAT'			=> $this->helper->route('ernadoo_phpbbdirectory_dynamic_route_' . $cat_id),
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
					$this->links_table	=> 'l'),
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
			$link_list[] = (int) $row['link_id'];
		}
		$this->db->sql_freeresult($result);

		if (sizeof($link_list))
		{
			// We get links, informations about poster, votes and number of comments
			$sql_array = array(
				'SELECT'	=> 'l.link_id, l.link_cat, l.link_url, l.link_user_id, l.link_comment, l. link_description, l.link_banner, l.link_rss, l. link_uid, l.link_bitfield, l.link_flags, l.link_vote, l.link_note, l.link_view, l.link_time, l.link_name, l.link_flag, l.link_thumb, u.user_id, u.username, u.user_colour, v.vote_user_id',
				'FROM'		=> array(
						$this->links_table	=> 'l'),
				'LEFT_JOIN'	=> array(
						array(
							'FROM'	=> array(USERS_TABLE	=> 'u'),
							'ON'	=> 'l.link_user_id = u.user_id'
						),
						array(
							'FROM'	=> array($this->votes_table => 'v'),
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
				$s_vote		= $this->link->display_vote($site);
				$s_banner	= $this->link->display_bann($site);
				$s_rss		= $this->link->display_rss($site);

				$edit_allowed 	= ($this->user->data['is_registered'] && ($this->auth->acl_get('m_edit_dir') || ($this->user->data['user_id'] == $site['link_user_id'] && $this->auth->acl_get('u_edit_dir'))));
				$delete_allowed = ($this->user->data['is_registered'] && ($this->auth->acl_get('m_delete_dir') || ($this->user->data['user_id'] == $site['link_user_id'] && $this->auth->acl_get('u_delete_dir'))));

				$this->template->assign_block_vars('site', array(
					'BANNER'		=> $s_banner,
					'COUNT'			=> $this->language->lang('DIR_NB_CLICKS', (int) $site['link_view']),
					'DESCRIPTION' 	=> generate_text_for_display($site['link_description'], $site['link_uid'], $site['link_bitfield'], $site['link_flags']),
					'LINK_ID'		=> $site['link_id'],
					'NAME'			=> $site['link_name'],
					'NB_COMMENT'	=> ($comments_status) ? $this->language->lang('DIR_NB_COMMS', (int) $site['link_comment']) : '',
					'NB_VOTE'		=> $this->language->lang('DIR_NB_VOTES', (int) $site['link_vote']),
					'NOTE'			=> $s_note,
					'RSS'			=> $s_rss,
					'TIME'			=> ($site['link_time']) ? $this->user->format_date($site['link_time']) : '',
					'USER'			=> get_username_string('full', $site['link_user_id'], $site['username'], $site['user_colour']),
					'VOTE_LIST'		=> ($votes_status) ? $s_vote : '',

					'IMG_FLAG'		=> $s_flag,
					'ON_CLICK' 		=> "onclick=\"window.open('".$this->helper->route('ernadoo_phpbbdirectory_view_controller', array('link_id' => (int) $site['link_id']))."'); return false;\"",

					'S_NEW_LINK'	=> (((time() - $site['link_time']) / 86400) <= $this->config['dir_new_time']) ? true : false,

					'U_COMMENT'		=> ($comments_status) ? $this->helper->route('ernadoo_phpbbdirectory_comment_view_controller', array('link_id' => (int) $site['link_id'])) : '',
					'U_DELETE'		=> $delete_allowed ? $this->helper->route('ernadoo_phpbbdirectory_delete_controller', array('cat_id' => (int) $cat_id, 'link_id' => (int) $site['link_id'], '_referer' => $this->helper->get_current_url())) : '',
					'U_EDIT'		=> $edit_allowed ? $this->helper->route('ernadoo_phpbbdirectory_edit_controller', array('cat_id' => (int) $cat_id, 'link_id' => (int) $site['link_id'])) : '',
					'U_FORM_VOTE'	=> ($votes_status) ? $this->helper->route('ernadoo_phpbbdirectory_vote_controller', array('cat_id' => (int) $site['link_cat'], 'link_id' => (int) $site['link_id'])) : '',
					'U_LINK'		=> $site['link_url'],
					'U_THUMB'		=> $s_thumb,
				));
			}
		}
		else
		{
			$this->template->assign_block_vars('no_draw_link', array());
		}

		$page_title = $this->language->lang('DIRECTORY') . ' - ' . $this->categorie->data['cat_name'];

		$this->categorie->display();

		return $this->helper->render('view_cat.html', $page_title);
	}

	/**
	* date controller for return a date
	*
	* @return	\phpbb\json_response	A Json Response
	* @throws	\phpbb\exception\http_exception
	*/
	public function return_date()
	{
		if (!$this->request->is_ajax())
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
		}

		$timestamp = $this->request->variable('timestamp', 0);
		$json_response = new \phpbb\json_response;
		$json_response->send(array(
			'success'	=> true,
			'DATE'		=> $this->user->format_date((int) $timestamp),
		));
	}

	/**
	* slug controller for return a slugify category name
	*
	* @return	\phpbb\json_response	A Json Response
	* @throws	\phpbb\exception\http_exception
	*/
	public function return_slug()
	{
		if (!$this->request->is_ajax())
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
		}

		$slug = new SpeakingUrl();
		$cat_name = $this->request->variable('cat_name', '', true);

		$json_response = new \phpbb\json_response;
		$json_response->send(array(
			'success'	=> true,
			'SLUG'		=> $slug->getSlug(html_entity_decode($cat_name), array('lang' => $this->config['default_lang'], 'symbols' => true)),
		));
	}
}

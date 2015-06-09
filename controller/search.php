<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <https://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\controller;

class search
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

	/** @var \ernadoo\phpbbdirectory\search\fulltext_directory */
	protected $search;

	/** @var \ernadoo\phpbbdirectory\core\categorie */
	protected $categorie;

	/** @var \ernadoo\phpbbdirectory\core\link */
	protected $link;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface 					$db				Database object
	* @param \phpbb\config\config 								$config			Config object
	* @param \phpbb\template\template 							$template		Template object
	* @param \phpbb\user 										$user			User object
	* @param \phpbb\controller\helper 							$helper			Controller helper object
	* @param \phpbb\request\request 							$request		Request object
	* @param \phpbb\auth\auth 									$auth			Auth object
	* @param \phpbb\pagination 									$pagination		Pagination object
	* @param \ernadoo\phpbbdirectory\search\fulltext_directory	$search			PhpBB Directory extension search object
	* @param \ernadoo\phpbbdirectory\core\categorie				$categorie		PhpBB Directory extension categorie object
	* @param \ernadoo\phpbbdirectory\core\link					$link			PhpBB Directory extension link object
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\auth\auth $auth, \phpbb\pagination $pagination, \ernadoo\phpbbdirectory\search\fulltext_directory $search, \ernadoo\phpbbdirectory\core\categorie $categorie, \ernadoo\phpbbdirectory\core\link $link)
	{
		$this->db			= $db;
		$this->config		= $config;
		$this->template		= $template;
		$this->user			= $user;
		$this->helper		= $helper;
		$this->request		= $request;
		$this->auth			= $auth;
		$this->pagination	= $pagination;
		$this->search		= $search;
		$this->categorie	= $categorie;
		$this->link			= $link;

		$this->user->add_lang_ext('ernadoo/phpbbdirectory', array('directory'));
		$this->user->add_lang('search');

		$this->template->assign_vars(array(
			'S_PHPBB_DIRECTORY'				=> true,
			'DIRECTORY_TRANSLATION_INFO'	=> (!empty($user->lang['DIRECTORY_TRANSLATION_INFO'])) ? $user->lang['DIRECTORY_TRANSLATION_INFO'] : '',
		));
	}

	/**
	* Search controller
	*
	* @param	int	$page	Page number taken from the URL
	* @return	\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	* @throws	\phpbb\exception\http_exception
	*/
	public function main($page)
	{
		if (!$this->auth->acl_get('u_search_dir'))
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
		}

		$cat_id				= $this->request->variable('cat_id', 0);
		$keywords			= $this->request->variable('keywords', '', true);
		$search_terms		= $this->request->variable('terms', 'all');
		$search_category	= $this->request->variable('cid', array(0));
		$search_fields		= $this->request->variable('sf', 'all');
		$search_child		= $this->request->variable('sc', true);
		$sort_days			= $this->request->variable('st', 0);
		$sort_key			= $this->request->variable('sk', 't');
		$sort_dir			= $this->request->variable('sd', 'd');
		$start				= ($page - 1) * (int) $this->config['dir_show'];

		$default_sort_days	= 0;
		$default_sort_key	= (string) substr($this->config['dir_default_order'], 0, 1);
		$default_sort_dir	= (string) substr($this->config['dir_default_order'], 2);

		// Categorie ordering options
		$limit_days		= array(0 => $this->user->lang['ALL_RESULTS'], 1 => $this->user->lang['1_DAY'], 7 => $this->user->lang['7_DAYS'], 14 => $this->user->lang['2_WEEKS'], 30 => $this->user->lang['1_MONTH'], 90 => $this->user->lang['3_MONTHS'], 180 => $this->user->lang['6_MONTHS'], 365 => $this->user->lang['1_YEAR']);
		$sort_by_text	= array('a' => $this->user->lang['AUTHOR'], 't' => $this->user->lang['POST_TIME'], 'r' => $this->user->lang['DIR_COMMENTS_ORDER'], 's' =>  $this->user->lang['DIR_NAME_ORDER'], 'v' => $this->user->lang['DIR_NB_CLICKS_ORDER']);
		$sort_by_sql	= array('a' => 'u.username_clean', 't' => array('l.link_time', 'l.link_id'), 'r' => 'l.link_comment', 's' => 'l.link_name', 'v' => 'l.link_view');

		if ($this->config['dir_activ_pagerank'])
		{
			$sort_by_text['p'] = $this->user->lang['DIR_PR_ORDER'];
			$sort_by_sql['p'] = 'l.link_pagerank';
		}

		$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

		$u_sort_param = ($sort_days === $default_sort_days && $sort_key == $default_sort_key && $sort_dir == $default_sort_dir) ? array() : array('sort_days' => $sort_days, 'sort_key' => $sort_key, 'sort_dir' => $sort_dir);

		/*
		** search form submited
		*/
		if ($this->request->is_set_post('submit') || $keywords)
		{
			// clear arrays
			$id_ary = $u_search = array();
			$keywords_ary = ($keywords) ? explode(' ', $keywords) : array();

			if (!sizeof($keywords_ary))
			{
				return $this->helper->message('DIR_ERROR_KEYWORD');
			}

			$ex_cid_ary = $this->_get_exclude_categories($search_category, $search_child);

			$total_match_count = $this->search->keyword_search($keywords_ary, $search_fields, $search_terms, $sort_by_sql, $sort_key, $sort_dir, $sort_days, $ex_cid_ary, $cat_id, $id_ary, $start, $this->config['dir_show']);

			$l_search_matches = $this->user->lang('FOUND_SEARCH_MATCHES', (int) $total_match_count);

			if (sizeof($id_ary))
			{
				$sql_where = $this->db->sql_in_set('l.link_id', $id_ary);
			}
			else
			{
				return $this->helper->message('DIR_SEARCH_NO_RESULT');
			}

			// A single wildcard will make the search results look ugly
			$hilit = phpbb_clean_search_string(str_replace(array('+', '-', '|', '(', ')', '&quot;'), ' ', $keywords));
			$hilit = str_replace(' ', '|', $hilit);

			$u_hilit = urlencode(htmlspecialchars_decode(str_replace('|', ' ', $hilit)));

			($u_hilit) 					? $u_search['keywords']		= urlencode(htmlspecialchars_decode($keywords)) : '';
			($search_terms != 'all') 	? $u_search['terms']		= $search_terms : '';
			($cat_id)					? $u_search['cat_id']		= $cat_id : '';
			($search_category)			? $u_search['cid']			= $search_category : '';
			(!$search_child)			? $u_search['sc']			= 0 : '';
			($search_fields != 'all')	? $u_search['sf'] 			= $search_fields : '';

			$base_url = array(
				'routes'	=> 'ernadoo_phpbbdirectory_search_controller',
				'params'	=> array_merge($u_search, $u_sort_param),
			);

			$u_search = $this->helper->route('ernadoo_phpbbdirectory_search_controller', array_merge($u_search, $u_sort_param));

			$this->pagination->generate_template_pagination($base_url, 'pagination', 'page', $total_match_count, $this->config['dir_show'], $start);

			$this->template->assign_vars(array(
				'SEARCH_MATCHES'	=> $l_search_matches,
				'SEARCH_WORDS'		=> $keywords,

				'TOTAL_MATCHES'		=> $total_match_count,

				'S_SELECT_SORT_DIR'		=> $s_sort_dir,
				'S_SELECT_SORT_KEY'		=> $s_sort_key,
				'S_SELECT_SORT_DAYS'	=> $s_limit_days,
				'S_SEARCH_ACTION'		=> $u_search,

				'U_DIR_SEARCH'			=> $this->helper->route('ernadoo_phpbbdirectory_search_controller'),
				'U_SEARCH_WORDS'		=> $u_search,
			));

			if ($cat_id)
			{
				$this->template->assign_vars(array(
					'SEARCH_CATEGORY'	=> $this->user->lang('RETURN_TO', strip_tags(\ernadoo\phpbbdirectory\core\categorie::getname((int) $cat_id))),
					'U_SEARCH_CATEGORY'	=> $this->helper->route('ernadoo_phpbbdirectory_page_controller', array('cat_id' => (int) $cat_id)),
				));
			}

			if ($sql_where)
			{
				$sql_array = array(
					'SELECT'	=> 'l.link_name, l.link_description, l.link_url, l.link_uid, l.link_bitfield, l.link_flags, l.link_view, l.link_user_id, l.link_time, l.link_comment, l.link_flag, l.link_id, l.link_thumb, l.link_banner, c.cat_name, u.user_id, u.username, u.user_colour',
					'FROM'		=> array(
							DIR_LINK_TABLE	=> 'l'),
					'LEFT_JOIN'	=> array(
							array(
								'FROM'	=> array(DIR_CAT_TABLE => 'c'),
								'ON'	=> 'l.link_cat = c.cat_id'
							),
							array(
								'FROM'	=> array(USERS_TABLE => 'u'),
								'ON'	=> 'u.user_id = l.link_user_id'
							)
					),
					'WHERE'		=> $sql_where);

				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);

				$rowset = $this->db->sql_fetchrowset($result);

				if (sizeof($rowset))
				{
					if ($hilit)
					{
						// Remove bad highlights
						$hilit_array = array_filter(explode('|', $hilit), 'strlen');
						foreach ($hilit_array as $key => $value)
						{
							$hilit_array[$key] = phpbb_clean_search_string($value);
							$hilit_array[$key] = str_replace('\*', '\w*?', preg_quote($hilit_array[$key], '#'));
							$hilit_array[$key] = preg_replace('#(^|\s)\\\\w\*\?(\s|$)#', '$1\w+?$2', $hilit_array[$key]);
						}
						$hilit = implode('|', $hilit_array);
					}

					foreach ($rowset as $data)
					{
						$s_banner	= $this->link->display_bann($data);
						$s_thumb	= $this->link->display_thumb($data);
						$s_flag		= $this->link->display_flag($data);

						$data['link_description'] = generate_text_for_display($data['link_description'], $data['link_uid'], $data['link_bitfield'], $data['link_flags']);

						if ($hilit)
						{
							$data['link_name'] = preg_replace('#(?!<.*)(?<!\w)(' . $hilit . ')(?!\w|[^<>]*(?:</s(?:cript|tyle))?>)#is', '<span class="posthilit">$1</span>', $data['link_name']);
							$data['link_description'] = preg_replace('#(?!<.*)(?<!\w)(' . $hilit . ')(?!\w|[^<>]*(?:</s(?:cript|tyle))?>)#is', '<span class="posthilit">$1</span>', $data['link_description']);
						}

						$this->template->assign_block_vars('results', array(
							'S_SITE'		=> $data['link_name'],
							'S_DESCRIPTION' => $data['link_description'],
							'S_COUNT'		=> $data['link_view'],
							'S_CAT'			=> $data['cat_name'],
							'S_USER'		=> get_username_string('full', $data['link_user_id'], $data['username'], $data['user_colour']),
							'S_TIME'		=> ($data['link_time'] != 0) ? $this->user->format_date($data['link_time']) : '',
							'S_COMMENT'		=> $data['link_comment'],

							'THUMB'			=> '<img src="'.$s_thumb.'" alt="'.$this->user->lang['DIR_THUMB'].'" title="'.strip_tags($data['link_name']).'"/>',
							'IMG_BANNER'	=> $s_banner,
							'IMG_FLAG'		=> $s_flag,
							'ON_CLICK' 		=> "onclick=\"window.open('".$this->helper->route('ernadoo_phpbbdirectory_view_controller', array('link_id' => (int) $data['link_id']))."'); return false;\"",

							'L_DIR_SEARCH_NB_CLICKS'	=> $this->user->lang('DIR_SEARCH_NB_CLICKS', (int) $data['link_view']),
							'L_DIR_SEARCH_NB_COMMS'		=> $this->user->lang('DIR_SEARCH_NB_COMMS', (int) $data['link_comment']),

							'U_COMMENT'		=> $this->helper->route('ernadoo_phpbbdirectory_comment_view_controller', array('link_id' => (int) $data['link_id'])),
							'U_SITE'		=> $data['link_url'],
							'LINK_ID'		=> $data['link_id'],
						));

						if ($s_banner)
						{
							$this->template->assign_block_vars('results.banner', array());
						}

						if ($this->config['dir_activ_flag'])
						{
							$this->template->assign_block_vars('results.switch_dir_flag', array());
						}
					}
					unset($rowset);

					return $this->helper->render('search_results.html', $this->user->lang['DIR_MAKE_SEARCH']);
				}
			}
		}

		$s_catlist = $this->categorie->make_cat_select();

		if (!$s_catlist)
		{
			return $this->helper->message('NO_SEARCH');
		}

		$this->template->assign_vars(array(
			'S_POST_ACTION'			=> build_url(true),
			'S_KEYWORD'				=> $keywords,
			'S_CATLIST'				=> $s_catlist,
			'S_SELECT_SORT_DIR'		=> $s_sort_dir,
			'S_SELECT_SORT_KEY'		=> $s_sort_key,
			'S_SELECT_SORT_DAYS'	=> $s_limit_days,
		));

		return $this->helper->render('search_body.html', $this->user->lang['DIR_MAKE_SEARCH']);
	}

	/**
	*
	* @param	array	$search_category
	* @param	bool	$search_child
	* @return	array	Categories to exclude from search
	*/
	private function _get_exclude_categories(&$search_category, $search_child)
	{
		$sql = 'SELECT cat_id, parent_id, right_id
				FROM ' . DIR_CAT_TABLE . '
				ORDER BY left_id';
		$result = $this->db->sql_query($sql);

		$right_id = 0;
		$reset_search_category = true;
		$ex_cid_ary = array();

		while ($row = $this->db->sql_fetchrow($result))
		{
			if (sizeof($search_category))
			{
				if ($search_child)
				{
					if (in_array($row['cat_id'], $search_category) && $row['right_id'] > $right_id)
					{
						$right_id = (int) $row['right_id'];
					}
					else if ($row['right_id'] < $right_id)
					{
						continue;
					}
				}

				if (!in_array($row['cat_id'], $search_category))
				{
					$ex_cid_ary[] = (int) $row['cat_id'];
					$reset_search_category = false;
				}
			}
		}
		$this->db->sql_freeresult($result);

		if ($reset_search_category)
		{
			$search_category = array();
		}

		return $ex_cid_ary;
	}
}

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

class validation extends helper
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\log\log */
	protected $phpbb_log;

	/** @var \phpbb\notification\manager */
	protected $notification;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \ernadoo\phpbbdirectory\core\categorie */
	protected $categorie;

	/** @var \ernadoo\phpbbdirectory\core\link */
	protected $link;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/** @var string Custom form action */
	protected $u_action;

	/** @var string */
	private $action;

	/** @var array */
	private $affected_link_name = array();

	/** @var array */
	private $cat_data = array();

	/** @var array */
	private $links_data;

	/**
	* Constructor
	*
	* @param \phpbb\config\config								$config				Config object
	* @param \phpbb\db\driver\driver_interface 					$db					Database object
	* @param \phpbb\pagination									$pagination			Pagination object
	* @param \phpbb\language\language							$language			Language object
	* @param \phpbb\log\log										$log				Log object
	* @param \phpbb\notification\manager						$notification		Notification object
	* @param \phpbb\request\request								$request			Request object
	* @param \phpbb\template\template							$template			Template object
	* @param \phpbb\user										$user				User object
	* @param \ernadoo\phpbbdirectory\core\categorie				$categorie			PhpBB Directory extension categorie object
	* @param \ernadoo\phpbbdirectory\core\link					$link				PhpBB Directory extension link object
	* @param string												$root_path			phpBB root path
	* @param string												$php_ext   			phpEx
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\pagination $pagination, \phpbb\language\language $language, \phpbb\log\log $log, \phpbb\notification\manager $notification, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \ernadoo\phpbbdirectory\core\categorie $categorie, \ernadoo\phpbbdirectory\core\link $link, $root_path, $php_ext)
	{
		$this->config		= $config;
		$this->db			= $db;
		$this->pagination	= $pagination;
		$this->language		= $language;
		$this->phpbb_log	= $log;
		$this->notification	= $notification;
		$this->request		= $request;
		$this->template		= $template;
		$this->user			= $user;
		$this->categorie	= $categorie;
		$this->link			= $link;
		$this->root_path	= $root_path;
		$this->php_ext		= $php_ext;

		$this->action		= $this->request->variable('action', '');
	}

	/**
	* Display confirm box
	*
	* @param	array $mark Website selected for (dis)approval
	* @return	null
	*/
	public function display_confirm($mark)
	{
		$s_hidden_fields = array(
			'action'		=> $this->action,
			'link_id'		=> $mark,
			'start'			=> $this->request->variable('start', 0),
		);
		confirm_box(false, $this->language->lang('CONFIRM_OPERATION'), build_hidden_fields($s_hidden_fields));
	}

	/**
	* Display website list for (dis)approval
	*
	* @return null
	*/
	public function display_websites()
	{
		global $phpbb_admin_path;

		// Sort keys
		$sort_days	= $this->request->variable('st', 0);
		$sort_key	= $this->request->variable('sk', 't');
		$sort_dir	= $this->request->variable('sd', 'd');

		// Number of entries to display
		$per_page = $this->request->variable('links_per_page', (int) $this->config['dir_show']);

		$start	= $this->request->variable('start', 0);

		// Categorie ordering options
		$limit_days		= array(0 => $this->language->lang('SEE_ALL'), 1 => $this->language->lang('1_DAY'), 7 => $this->language->lang('7_DAYS'), 14 => $this->language->lang('2_WEEKS'), 30 => $this->language->lang('1_MONTH'), 90 => $this->language->lang('3_MONTHS'), 180 => $this->language->lang('6_MONTHS'), 365 => $this->language->lang('1_YEAR'));
		$sort_by_text	= array('a' => $this->language->lang('AUTHOR'), 't' => $this->language->lang('POST_TIME'));
		$sort_by_sql	= array('a' => 'u.username_clean', 't' => array('l.link_time', 'l.link_id'));

		$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

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
			FROM ' . $this->links_table . '
			WHERE link_active = 0' .
				(($sql_where) ? " AND link_time >= $sql_where" : '');
		$result = $this->db->sql_query($sql);
		$total_links = (int) $this->db->sql_fetchfield('total_links');
		$this->db->sql_freeresult($result);

		// Make sure $start is set to the last page if it exceeds the amount
		$start = $this->pagination->validate_start($start, $per_page, $total_links);

		$sql_array = array(
			'SELECT'	=> 'l.link_id, l.link_name, l.link_url, l.link_description, l.link_cat, l.link_user_id, l.link_guest_email, l.link_uid, l.link_bitfield, l.link_flags, l.link_banner, l.link_time, c.cat_name, u.user_id, u.username, u.user_colour',
			'FROM'		=> array(
				$this->links_table	=> 'l'),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->categories_table => 'c'),
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
			$s_banner = $this->link->display_bann($row);

			$username = ($row['link_user_id'] == ANONYMOUS) ? $row['link_guest_email'] : $row['username'];

			$link_row = array(
				'LINK_URL'			=> $row['link_url'],
				'LINK_NAME'			=> $row['link_name'],
				'LINK_DESC'			=> generate_text_for_display($row['link_description'], $row['link_uid'], $row['link_bitfield'], $row['link_flags']),
				'L_DIR_USER_PROP'	=> $this->language->lang('DIR_USER_PROP', get_username_string('full', $row['link_user_id'], $username, $row['user_colour'], false, append_sid("{$phpbb_admin_path}index.$this->php_ext", 'i=users&amp;mode=overview')), '<select name=c'.$row['link_id'].'>'.$this->categorie->make_cat_select((int) $row['link_cat']).'</select>', $this->user->format_date($row['link_time'])),
				'BANNER'			=> $s_banner,
				'LINK_ID'			=> $row['link_id'],

			);
			$this->template->assign_block_vars('linkrow', $link_row);
		}
		$this->db->sql_freeresult($result);

		$option_ary = array('approved' => 'DIR_LINK_ACTIVATE', 'disapproved' => 'DIR_LINK_DELETE');

		$base_url = $this->u_action . "&amp;$u_sort_param&amp;links_per_page=$per_page";
		$this->pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_links, $per_page, $start);

		$this->template->assign_vars(array(
			'S_LINKS_OPTIONS'	=> build_select($option_ary),

			'S_LIMIT_DAYS'		=> $s_limit_days,
			'S_SORT_KEY'		=> $s_sort_key,
			'S_SORT_DIR'		=> $s_sort_dir,
			'LINKS_PER_PAGE'	=> $per_page,

			'U_ACTION'			=> $this->u_action . "&amp;$u_sort_param&amp;links_per_page=$per_page&amp;start=$start",
		));
	}

	/**
	* Get link's information and call appropriate action
	*
	* @param	array $mark Website selected for (dis)approval
	* @return	null
	*/
	public function exec_action($mark)
	{
		$this->_get_infos_links($mark);

		switch ($this->action)
		{
			case 'approved':
				$this->_action_approved();
				break;

			case 'disapproved':
				$this->_action_disapproved();
				break;

			default:
				return;
		}

		$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_LINK_' . strtoupper($this->action), time(), array(implode(', ', $this->affected_link_name)));
	}

	/**
	* Notify users which had submitted their websites
	*
	* @return null
	*/
	public function notifiy_submiters()
	{
		if (!class_exists('messenger'))
		{
			include($this->root_path . 'includes/functions_messenger.' . $this->php_ext);
		}
		$messenger = new \messenger(false);

		foreach ($this->links_data as $row)
		{
			$this->notification->mark_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_in_queue', (int) $row['link_id'], false);

			// New notification system can't send mail to an anonymous user with an email adress storage in another table than phpbb_users
			if ($row['link_user_id'] == ANONYMOUS)
			{
				$username = $email = $row['link_guest_email'];

				$messenger->template('@ernadoo_phpbbdirectory/directory_website_'.$this->action, $row['user_lang']);
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
					'link_name'			=> $row['link_name'],
					'cat_name'			=> \ernadoo\phpbbdirectory\core\categorie::getname((int) $row['link_cat']),
					'cat_id'			=> (int) $row['link_cat'],
				);

				$this->notification->add_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_'.$this->action, $notification_data);
			}
		}
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
	* Approve action
	*
	* @return null
	*/
	private function _action_approved()
	{
		foreach ($this->links_data as $row)
		{
			$this->_notify_suscribers($row);

			$sql_ary = array(
				'link_active'	=> 1,
				'link_time'		=> time(),
				'link_cat'		=> (int) $row['link_cat'],
			);

			$sql = 'UPDATE ' . $this->links_table . '
							SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
							WHERE link_id = ' . (int) $row['link_id'];
			$this->db->sql_query($sql);
		}

		foreach ($this->cat_data as $cat_id => $count)
		{
			$sql = 'UPDATE ' . $this->categories_table . '
							SET cat_links = cat_links + '.$count.'
							WHERE cat_id = ' . (int) $cat_id;
			$this->db->sql_query($sql);
		}
	}

	/**
	* Disapprove action
	*
	* @return null
	*/
	private function _action_disapproved()
	{
		foreach ($this->links_data as $row)
		{
			if ($row['link_banner'] && !preg_match('/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|www\.).+/si', $row['link_banner']))
			{
				$banner_img = $this->get_banner_path(basename($row['link_banner']));

				if (file_exists($banner_img))
				{
					@unlink($banner_img);
				}
			}

			$sql = 'DELETE FROM ' . $this->links_table . ' WHERE link_id = ' . (int) $row['link_id'];
			$this->db->sql_query($sql);
		}
	}

	/**
	* Get informations about links selected
	*
	* @param	$mark Website selected for (dis)approval
	* @return	null
	*/
	private function _get_infos_links($mark)
	{
		$sql_array = array(
			'SELECT'	=> 'a.link_id, a.link_name, a.link_url, a.link_description, a.link_banner, a.link_user_id, a.link_guest_email, u.username, u.user_email, u.user_lang, u.user_notify_type, c.cat_id, c.cat_name',
			'FROM'		=> array(
				$this->links_table	=> 'a'),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'u.user_id = a.link_user_id'
				),
				array(
					'FROM'	=> array($this->categories_table => 'c'),
					'ON'	=> 'a.link_cat = c.cat_id'
				)
			),
			'WHERE'		=> $this->db->sql_in_set('a.link_id', $mark));

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$row['link_cat'] = $this->request->variable('c'.$row['link_id'], (int) $row['cat_id']);

			$this->links_data[] = $row;

			$this->affected_link_name[] = $row['link_name'];

			$this->cat_data[$row['link_cat']] = isset($this->cat_data[$row['link_cat']]) ? $this->cat_data[$row['link_cat']] + 1 : 1;
		}
	}

	/**
	* Notify users which watch categories
	*
	* @param	$row Informations about website
	* @return	null
	*/
	private function _notify_suscribers($row)
	{
		$notification_data = array(
			'user_from'			=> (int) $row['link_user_id'],
			'link_id'			=> (int) $row['link_id'],
			'link_name'			=> $row['link_name'],
			'link_url'			=> $row['link_url'],
			'link_description'	=> preg_replace('/(\[.*?\])(.*?)(\[\/.*?\])/si', '\\1', $row['link_description']),
			'cat_name'			=> \ernadoo\phpbbdirectory\core\categorie::getname((int) $row['link_cat']),
			'cat_id'			=> (int) $row['link_cat'],
		);

		$this->notification->add_notifications('ernadoo.phpbbdirectory.notification.type.directory_website', $notification_data);
	}
}

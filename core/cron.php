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

class cron extends helper
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\log\log */
	protected $phpbb_log;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\notification\manager */
	protected $notification;

	/** @var \ernadoo\phpbbdirectory\core\link */
	protected $link;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface 	$db					Database object
	* @param \phpbb\config\config 				$config				Config object
	* @param \phpbb\log\log						$phpbb_log			Log object
	* @param \phpbb\user 						$user				User object
	* @param \phpbb\notification\manager		$notification		Notification object
	* @param \ernadoo\phpbbdirectory\core\link	$link				PhpBB Directory extension link object
	* @param string         					$root_path			phpBB root path
	* @param string         					$php_ext			phpEx
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\log\log $phpbb_log, \phpbb\user $user, \phpbb\notification\manager $notification, \ernadoo\phpbbdirectory\core\link $link, $root_path, $php_ext)
	{
		$this->db				= $db;
		$this->config			= $config;
		$this->phpbb_log		= $phpbb_log;
		$this->user				= $user;
		$this->notification		= $notification;
		$this->link				= $link;
		$this->root_path		= $root_path;
		$this->php_ext			= $php_ext;
	}

	/**
	* Method called by cron task.
	*
	* @param	array	$cat_data	Information about category, from db
	* @return	null
	*/
	public function auto_check($cat_data)
	{
		$sql = 'SELECT cat_name
			FROM ' . $this->categories_table . '
			WHERE cat_id = ' . (int) $cat_data['cat_id'];
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row)
		{
			$next_prune = time() + ($cat_data['cat_cron_freq'] * 86400);

			$this->_check($cat_data['cat_id'], $cat_data['cat_cron_nb_check'], $next_prune);

			$sql = 'UPDATE ' . $this->categories_table . "
			SET cat_cron_next = $next_prune
			WHERE cat_id = " . (int) $cat_data['cat_id'];
			$this->db->sql_query($sql);

			$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_AUTO_PRUNE', time(), array($row['cat_name']));
		}

		return;
	}

	/**
	 * Return cron informations about a category.
	 *
	 * @param	int	$cat_id	The category ID
	 * @return	array
	 */
	public function get_cat($cat_id)
	{
		$sql = 'SELECT cat_id, cat_cron_enable, cat_cron_next, cat_cron_freq, cat_cron_nb_check
				FROM ' . $this->categories_table . '
					WHERE cat_id = ' . (int) $cat_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row)
		{
			return $row;
		}
	}

	/**
	* Check, for website with backlink specified, if backlink is always here.
	* After $nb_check verification, website is deleted, otherwise, a notification is send to poster
	*
	* @param	int		$cat_id		The category ID
	* @param	int		$nb_check	Number of check before demete a website
	* @param	int		$next_prune	Date of next auto check
	* @return	null
	*/
	private function _check($cat_id, $nb_check, $next_prune)
	{
		$del_array = $update_array = array();

		$sql_array = array(
			'SELECT'	=> 'link_id, link_cat, link_back, link_guest_email, link_nb_check, link_user_id, link_name, link_url, link_description, u.user_lang, u.user_dateformat',
			'FROM'		=> array(
				$this->links_table	=> 'l'),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE	=> 'u'),
					'ON'	=> 'l.link_user_id = u.user_id'
				)
			),
			'WHERE'		=> 'l.link_back <> "" AND l.link_active = 1 AND l.link_cat = '  . (int) $cat_id);

			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				if ($this->link->validate_link_back($row['link_back'], false, true) !== false)
				{
					if (!$nb_check || ($row['link_nb_check']+1) >= $nb_check)
					{
						$del_array[] = $row['link_id'];
					}
					else
					{
						// A first table containing links ID to update
						$update_array[$row['link_id']] = $row;
					}
				}
			}
			$this->db->sql_freeresult($result);

			if (count($del_array))
			{
				$this->link->del($cat_id, $del_array);
			}
			if (count($update_array))
			{
				$this->_update_check($update_array, $next_prune);
			}
	}

	/**
	* Update website verification number after a missing backlink, and send notificaton
	*
	* @param	array	$u_array	Information about website
	* @param	int		$next_prune	Date of next auto check
	* @return	null
	*/
	private function _update_check($u_array, $next_prune)
	{
		if (!class_exists('messenger'))
		{
			include($this->root_path . 'includes/functions_messenger.' . $this->php_ext);
		}

		$messenger = new \messenger(false);

		// cron.php don't call $user->setup(), so $this->timezone is unset.
		// We need to define it, because we use user->format_date below
		$this->user->timezone = new \DateTimeZone($this->config['board_timezone']);

		$sql = 'UPDATE ' . $this->links_table . '
			SET link_nb_check = link_nb_check + 1
			WHERE ' . $this->db->sql_in_set('link_id', array_keys($u_array));
		$this->db->sql_query($sql);

		foreach ($u_array as $data)
		{
			strip_bbcode($data['link_description']);

			$notification_data = array(
				'cat_name'			=> \ernadoo\phpbbdirectory\core\categorie::getname((int) $data['link_cat']),
				'link_id'			=> $data['link_id'],
				'link_user_id'		=> $data['link_user_id'],
				'link_name'			=> $data['link_name'],
				'link_url'			=> $data['link_url'],
				'link_description'	=> $data['link_description'],
				'next_cron' 		=> $this->user->format_date($next_prune, $data['user_dateformat']),
			);

			if ($data['link_nb_check'])
			{
				$this->notification->delete_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_error_cron', $notification_data);
			}

			// New notification system can't send mail to an anonymous user with an email address stored in another table than phpbb_users
			if ($data['link_user_id'] == ANONYMOUS)
			{
				$username = $email = $data['link_guest_email'];

				$messenger->template('@ernadoo_phpbbdirectory/directory_website_error_cron', $data['user_lang']);
				$messenger->to($email, $username);

				$messenger->assign_vars(array(
					'USERNAME'			=> htmlspecialchars_decode($username),
					'LINK_NAME'			=> $data['link_name'],
					'LINK_URL'			=> $data['link_url'],
					'LINK_DESCRIPTION'	=> $data['link_description'],
					'NEXT_CRON' 		=> $this->user->format_date($next_prune, $data['user_dateformat']),
				));

				$messenger->send(NOTIFY_EMAIL);
			}
			else
			{
				$this->notification->add_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_error_cron', $notification_data);
			}
		}
	}
}

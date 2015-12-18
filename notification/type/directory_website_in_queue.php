<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\notification\type;

/**
* phpbb directory notifications class
* This class handles notifications for links when they are put in the validation queue (for administrators)
*/

class directory_website_in_queue extends \phpbb\notification\type\base
{
	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'ernadoo.phpbbdirectory.notification.type.directory_website_in_queue';
	}

	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	* 					Array of data (including keys 'id', 'lang', and 'group')
	*/
	public static $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_DIR_UCP_IN_MODERATION_QUEUE',
		'group'	=> 'NOTIFICATION_DIR_UCP',
	);

	/**
	* Permission to check for (in find_users_for_notification)
	*
	* @var string Permission name
	*/
	protected $permission = 'a_';

	/** @var \phpbb\user_loader */
	protected $user_loader;

	public function set_user_loader(\phpbb\user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
	}

	/**
	* Is available
	*
	* @return bool True/False whether or not this is available to the user
	*/
	public function is_available()
	{
		$has_permission = $this->auth->acl_get($this->permission, true);

		return (!empty($has_permission));
	}

	/**
	* Get link id
	*
	* @param array $data The data from the link
	* @return int
	*/
	static public function get_item_id($data)
	{
		return (int) $data['link_id'];
	}

	/**
	* Get parent id - it's not used
	*
	* @param array $data The data from the link
	*/
	static public function get_item_parent_id($data)
	{
		// No parent
		return 0;
	}

	/**
	* Find the users who want to receive notifications
	*
	* @param	array	$data		Data from submit link
	* @param	array	$options	Options for finding users for notification
	* @return	array
	*/
	public function find_users_for_notification($data, $options = array())
	{
		$options = array_merge(array(
			'ignore_users'		=> array(),
		), $options);

		// 0 is for global moderator permissions
		$admin_ary = $this->auth->acl_get_list(false, $this->permission);
		$users = (!empty($admin_ary[0][$this->permission])) ? $admin_ary[0][$this->permission] : array();

		if (empty($users))
		{
			return array();
		}

		return $this->check_user_notification_options($users, $options);
	}

	/**
	* Get the user's avatar
	*/
	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('user_from'), false, true);
	}

	/**
	* Get the HTML formatted title of this notification
	*
	* @return string
	*/
	public function get_title()
	{
		$link_name = $this->get_data('link_name');
		$username = $this->user_loader->get_username($this->get_data('user_from'), 'no_profile');

		return $this->language->lang('NOTIFICATION_DIR_WEBSITE_IN_QUEUE', $link_name, $username);
	}

	/**
	* Get email template
	*
	* @return string
	*/
	public function get_email_template()
	{
		return '@ernadoo_phpbbdirectory/directory_website_in_queue';
	}

	/**
	* Get email template variables
	*
	* @return array
	*/
	public function get_email_template_variables()
	{
		return array();
	}

	/**
	* Get the url to this item
	*
	* @return string URL
	*/
	public function get_url()
	{
		global $phpbb_admin_path;

		return append_sid("{$phpbb_admin_path}index.{$this->php_ext}", array('i' => '-ernadoo-phpbbdirectory-acp-phpbbdirectory_module', 'mode' => 'val'), true, $this->user->session_id);
	}

	/**
	* Users needed to query before this notification can be displayed
	*
	* @return array Array of user_ids
	*/
	public function users_to_query()
	{
		return array($this->get_data('user_from'));
	}

	/**
	* Function for preparing the data for insertion in an SQL query
	* (The service handles insertion)
	*
	* @param	array	$data				Data from submit link
	* @param	array	$pre_create_data	Data from pre_create_insert_array()
	* @return	array						Array of data ready to be inserted into the database
	*/
	public function create_insert_array($data, $pre_create_data = array())
	{
		$this->set_data('user_from', $data['user_from']);
		$this->set_data('link_name', $data['link_name']);
		$this->set_data('cat_id', $data['cat_id']);
		$this->set_data('cat_name', $data['cat_name']);

		parent::create_insert_array($data, $pre_create_data);
	}
}

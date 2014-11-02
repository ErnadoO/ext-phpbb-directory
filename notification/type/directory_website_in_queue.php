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
* This class handles notifications for links when they are put in the validation queue (for administratorss)
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

	/**
	* Is available
	*/
	public function is_available()
	{
		$has_permission = $this->auth->acl_get($this->permission, true);

		return (!empty($has_permission));
	}

	/**
	* Get link id
	*/
	public static function get_item_id($data)
	{
		return $data['link_id'];
	}

	/**
	* Get parent id - it's not used
	*/
	public static function get_item_parent_id($data)
	{
		// No parent
		return 0;
	}

	/**
	* Find the users who want to receive notifications
	*
	* @param array $post Data from the post
	*
	* @return array
	*/
	public function find_users_for_notification($post, $options = array())
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
		return $this->user_loader->get_avatar($this->get_data('user_from'));
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

		return $this->user->lang('NOTIFICATION_DIR_WEBSITE_IN_QUEUE', $link_name, $username);
	}

	/**
	* Get email template
	*
	* @return string|bool
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
		//return append_sid($this->phpbb_root_path . 'directory/categorie/' .  (int)$this->get_data('cat_id'));
		return '';
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
	* @param array $post Data from submit_post
	* @param array $pre_create_data Data from pre_create_insert_array()
	*
	* @return array Array of data ready to be inserted into the database
	*/
	public function create_insert_array($data, $pre_create_data = array())
	{
		$this->set_data('user_from', $data['user_from']);
		$this->set_data('link_name', $data['link_name']);
		$this->set_data('cat_id', $data['cat_id']);
		$this->set_data('cat_name', $data['cat_name']);

		return parent::create_insert_array($data, $pre_create_data);
	}
}

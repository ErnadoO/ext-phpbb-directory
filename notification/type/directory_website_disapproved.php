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
* This class handles notifications for links when they are disapproved (for posters)
*/

class directory_website_disapproved extends \phpbb\notification\type\base
{
	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'ernadoo.phpbbdirectory.notification.type.directory_website_disapproved';
	}

	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	* 					Array of data (including keys 'id', 'lang', and 'group')
	*/
	public static $notification_option = array(
		'id'	=> 'dir_moderation_queue',
		'lang'	=> 'NOTIFICATION_TYPE_DIR_UCP_MODERATION_QUEUE',
		'group'	=> 'NOTIFICATION_DIR_UCP',
	);

	/**
	* Permission to check for (in find_users_for_notification)
	*
	* @var string Permission name
	*/
	protected $permission = array('a_', 'm_');

	/**
	* Is available
	*
	* @return bool True/False whether or not this is available to the user
	*/
	public function is_available()
	{
		$has_permission = $this->auth->acl_gets($this->permission, true);

		return (empty($has_permission));
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

		$users = array($data['user_from']);

		$notify_users = $this->check_user_notification_options($users, array_merge($options, array(
			'item_type'		=> self::$notification_option['id'],
		)));

		return $notify_users;
	}

	/**
	* Get the HTML formatted title of this notification
	*
	* @return string
	*/
	public function get_title()
	{
		$link_name = $this->get_data('link_name');
		$cat_name = $this->get_data('cat_name');

		return $this->user->lang('NOTIFICATION_DIR_WEBSITE_DISAPPROVED', $link_name, $cat_name);
	}

	/**
	* Get email template
	*
	* @return string
	*/
	public function get_email_template()
	{
		return '@ernadoo_phpbbdirectory/directory_website_disapproved';
	}

	/**
	* Get email template variables
	*
	* @return array
	*/
	public function get_email_template_variables()
	{
		return array(
			'LINK_NAME'	=> htmlspecialchars_decode($this->get_data('link_name')),
		);
	}

	/**
	* Get the url to this item
	*
	* @return string URL
	*/
	public function get_url()
	{
		return '';
	}

	/**
	* Users needed to query before this notification can be displayed
	*
	* @return array Array of user_ids
	*/
	public function users_to_query()
	{
		return array();
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
		$this->set_data('link_name', $data['link_name']);
		$this->set_data('cat_id', $data['cat_id']);
		$this->set_data('cat_name', $data['cat_name']);

		return parent::create_insert_array($data, $pre_create_data);
	}
}

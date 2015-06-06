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
* This class handles notifications for links which dont' contain a link back (for posters)
*/

class directory_website_error_cron extends \phpbb\notification\type\base
{
	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'ernadoo.phpbbdirectory.notification.type.directory_website_error_cron';
	}

	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	* 					Array of data (including keys 'id', 'lang', and 'group')
	*/
	public static $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_DIR_UCP_ERROR_CHECK',
		'group'	=> 'NOTIFICATION_DIR_UCP',
	);

	/**
	* Is available
	*
	* @return bool True/False whether or not this is available to the user
	*/
	public function is_available()
	{
		return true;
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
		$users = array();

		$sql = 'SELECT link_user_id
			FROM ' . DIR_LINK_TABLE . '
			WHERE link_id = ' . (int) $data['link_id'];
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$users[] = (int) $row['link_user_id'];
		}
		$this->db->sql_freeresult($result);

		if (empty($users))
		{
			return array();
		}

		sort($users);

		$notify_users = $this->check_user_notification_options($users, $options);

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

		return $this->user->lang('NOTIFICATION_DIR_WEBSITE_ERROR_CHECK', $link_name, $cat_name);
	}

	/**
	* Get email template
	*
	* @return string
	*/
	public function get_email_template()
	{
		return '@ernadoo_phpbbdirectory/directory_website_error_cron';
	}

	/**
	* Get email template variables
	*
	* @return array
	*/
	public function get_email_template_variables()
	{
		return array(
				'LINK_NAME'			=> $this->get_data('link_name'),
				'LINK_URL'			=> $this->get_data('link_url'),
				'LINK_DESCRIPTION'	=> $this->get_data('link_description'),
				'NEXT_CRON' 		=> $this->get_data('next_cron'),
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
		$this->set_data('cat_name', $data['cat_name']);

		$this->set_data('link_url', $data['link_url']);
		$this->set_data('link_description', $data['link_description']);
		$this->set_data('next_cron', $data['next_cron']);

		return parent::create_insert_array($data, $pre_create_data);
	}

	/**
	* Function for preparing the data for update in an SQL query
	* (The service handles insertion)
	*
	* @param array $type_data Data unique to this notification type
	* @return array Array of data ready to be updated in the database
	*/
	public function create_update_array($type_data)
	{
		$data = $this->create_insert_array($type_data);

		$data['notification_time'] = time();
		$data['notification_read'] = 0;

		return $data;
	}
}

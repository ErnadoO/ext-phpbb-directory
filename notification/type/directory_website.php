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
* This class handles notifications for new links
*/

class directory_website extends \phpbb\notification\type\base
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string */
	protected $watch_table;

	/**
	* Set the controller helper
	*
	* @param \phpbb\controller\helper $helper
	* @return null
	*/
	public function set_controller_helper(\phpbb\controller\helper $helper)
	{
		$this->helper = $helper;
	}

	/**
	 * Set the watch_table
	 *
	 * @param string $watch_table
	 * @return null
	 */
	public function set_watch_table($watch_table)
	{
		$this->watch_table = $watch_table;
	}

	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'ernadoo.phpbbdirectory.notification.type.directory_website';
	}

	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	* 					Array of data (including keys 'id', 'lang', and 'group')
	*/
	public static $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_DIR_UCP_WEBSITE',
		'group'	=> 'NOTIFICATION_DIR_UCP',
	);

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
		$options = array_merge(array(
			'ignore_users'		=> array(),
		), $options);

		$users = array();

		$sql = 'SELECT user_id
			FROM ' . $this->watch_table . '
			WHERE cat_id = ' . (int) $data['cat_id'] . '
				AND notify_status = ' . NOTIFY_YES . '
				AND user_id <> ' . (int) $data['user_from'];
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$users[] = (int) $row['user_id'];
		}
		$this->db->sql_freeresult($result);

		if (empty($users))
		{
			return array();
		}

		sort($users);

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
		$cat_name = $this->get_data('cat_name');

		return $this->language->lang('NOTIFICATION_DIR_NEW', $username, $link_name, $cat_name);
	}


	/**
	* Get email template
	*
	* @return string
	*/
	public function get_email_template()
	{
		return '@ernadoo_phpbbdirectory/directory_website';
	}

	/**
	* Get email template variables
	*
	* @return array
	*/
	public function get_email_template_variables()
	{
		return array(
			'CAT_NAME'			=> $this->get_data('cat_name'),
			'LINK_NAME'			=> htmlspecialchars_decode($this->get_data('link_name')),
			'LINK_URL'			=> $this->get_data('link_url'),
			'LINK_DESCRIPTION'	=> $this->get_data('link_description'),
		);
	}

	/**
	* Get the url to this item
	*
	* @return string URL
	*/
	public function get_url()
	{
		return $this->helper->route('ernadoo_phpbbdirectory_dynamic_route_' . $this->get_data('cat_id'));
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
		$this->set_data('link_name', $data['link_name']);
		$this->set_data('link_url', $data['link_url']);
		$this->set_data('link_description', $data['link_description']);
		$this->set_data('user_from', $data['user_from']);
		$this->set_data('cat_id', $data['cat_id']);
		$this->set_data('cat_name', $data['cat_name']);

		parent::create_insert_array($data, $pre_create_data);
	}
}

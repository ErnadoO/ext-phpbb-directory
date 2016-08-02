<?php
/**
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*/
namespace ernadoo\phpbbdirectory\notification\type;

/**
 * phpbb directory notifications class
 * This class handles notifications for links when they are approved (for posters).
 */
class directory_website_approved extends \phpbb\notification\type\base
{
    /**
     * Get notification type name.
     *
     * @return string
     */
    public function get_type()
    {
        return 'ernadoo.phpbbdirectory.notification.type.directory_website_approved';
    }

    /**
     * Notification option data (for outputting to the user).
     *
     * @var bool|array False if the service should use it's default data
     *                 Array of data (including keys 'id', 'lang', and 'group')
     */
    public static $notification_option = [
        'id'       => 'dir_moderation_queue',
        'lang'     => 'NOTIFICATION_TYPE_DIR_UCP_MODERATION_QUEUE',
        'group'    => 'NOTIFICATION_DIR_UCP',
    ];

    /**
     * Permission to check for (in find_users_for_notification).
     *
     * @var string Permission name
     */
    protected $permission = ['a_', 'm_'];

    /**
     * Is available.
     *
     * @return bool True/False whether or not this is available to the user
     */
    public function is_available()
    {
        $has_permission = $this->auth->acl_gets($this->permission, true);

        return empty($has_permission);
    }

    /**
     * Get link id.
     *
     * @param array $data The data from the link
     *
     * @return int
     */
    public static function get_item_id($data)
    {
        return (int) $data['link_id'];
    }

    /**
     * Get parent id - it's not used.
     *
     * @param array $data The data from the link
     */
    public static function get_item_parent_id($data)
    {
        // No parent
        return 0;
    }

    /**
     * Find the users who want to receive notifications.
     *
     * @param array $data    Data from submit link
     * @param array $options Options for finding users for notification
     *
     * @return array
     */
    public function find_users_for_notification($data, $options = [])
    {
        $options = array_merge([
            'ignore_users'        => [],
        ], $options);

        $users = [$data['user_from']];

        return $this->check_user_notification_options($users, array_merge($options, [
            'item_type'        => self::$notification_option['id'],
        ]));
    }

    /**
     * Get the HTML formatted title of this notification.
     *
     * @return string
     */
    public function get_title()
    {
        $link_name = $this->get_data('link_name');
        $cat_name = $this->get_data('cat_name');

        return $this->user->lang('NOTIFICATION_DIR_WEBSITE_APPROVED', $link_name, $cat_name);
    }

    /**
     * Get email template.
     *
     * @return string
     */
    public function get_email_template()
    {
        return '@ernadoo_phpbbdirectory/directory_website_approved';
    }

    /**
     * Get email template variables.
     *
     * @return array
     */
    public function get_email_template_variables()
    {
        return [
            'LINK_NAME'    => htmlspecialchars_decode($this->get_data('link_name')),
        ];
    }

    /**
     * Get the url to this item.
     *
     * @return string URL
     */
    public function get_url()
    {
        return append_sid($this->phpbb_root_path.'directory/categorie/'.(int) $this->get_data('cat_id'));
    }

    /**
     * Users needed to query before this notification can be displayed.
     *
     * @return array Array of user_ids
     */
    public function users_to_query()
    {
        return [];
    }

    /**
     * Function for preparing the data for insertion in an SQL query
     * (The service handles insertion).
     *
     * @param array $data            Data from submit link
     * @param array $pre_create_data Data from pre_create_insert_array()
     *
     * @return array Array of data ready to be inserted into the database
     */
    public function create_insert_array($data, $pre_create_data = [])
    {
        $this->set_data('link_name', $data['link_name']);
        $this->set_data('cat_id', $data['cat_id']);
        $this->set_data('cat_name', $data['cat_name']);

        return parent::create_insert_array($data, $pre_create_data);
    }
}

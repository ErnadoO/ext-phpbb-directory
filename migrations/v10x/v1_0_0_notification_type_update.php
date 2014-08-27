<?php
/**
 *
 * @package phpBB Directory
 * @copyright (c) 2014 ErnadoO
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

namespace ernadoo\phpbbdirectory\migrations\v10x;

class v1_0_0_notification_type_update extends \phpbb\db\migration\migration
{
	protected $notification_types = array(
		'directory_website',
		'directory_website_activate',
		'directory_website_delete',
		'directory_website_error_cron',
		'directory_website_in_queue');

	static public function depends_on()
	{
		return array(
			'\ernadoo\phpbbdirectory\migrations\v10x\v1_0_0',
			'\phpbb\db\migration\data\v310\notifications_use_full_name'
		);
	}

	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'update_notifications_name'))),
		);
	}

	public function revert_data()
	{
		return array(
			array('custom', array(array($this, 'revert_notifications_name'))),
		);
	}

	public function update_notifications_name()
	{
		foreach ($this->notification_types as $notification_type)
		{
			$sql = 'UPDATE ' . NOTIFICATION_TYPES_TABLE . "
				SET notification_type_name = 'ernadoo.phpbbdirectory.notification.type.{$notification_type}',
					notification_type_enabled = 1
				WHERE notification_type_name = '{$notification_type}'";
			$this->db->sql_query($sql);

			$sql = 'UPDATE ' . USER_NOTIFICATIONS_TABLE . "
				SET item_type = 'ernadoo.phpbbdirectory.notification.type.{$notification_type}'
				WHERE item_type = '{$notification_type}'";
			$this->db->sql_query($sql);
		}
	}

	public function revert_notifications_name()
	{
		foreach ($this->notification_types as $notification_type)
		{
			$sql = 'UPDATE ' . NOTIFICATION_TYPES_TABLE . "
				SET notification_type_name = '{$notification_type}'
					notification_type_enabled = 0
				WHERE notification_type_name = 'ernadoo.phpbbdirectory.notification.type.{$notification_type}'";
			$this->db->sql_query($sql);

			$sql = 'UPDATE ' . USER_NOTIFICATIONS_TABLE . "
				SET item_type = '{$notification_type}'
				WHERE item_type = 'ernadoo.phpbbdirectory.notification.type.{$notification_type}'";
			$this->db->sql_query($sql);
		}
	}
}

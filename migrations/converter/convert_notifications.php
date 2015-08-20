<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\migrations\converter;

/**
* Convert module
*/
class convert_notifications extends \phpbb\db\migration\migration
{
	/**
	* Skip this migration if phpbb_directory_notifications table does not exist
	*
	* @return	bool	True if table does not exist
	* @access	public
	*/
	public function effectively_installed()
	{
		return !$this->db_tools->sql_table_exists($this->table_prefix . 'directory_notifications');
	}

	static public function depends_on()
	{
		return array(
			'\ernadoo\phpbbdirectory\migrations\v10x\v1_0_0',
		);
	}

	/**
	* Add or update data in the database
	*
	* @return	array Array of table data
	* @access	public
	*/
	public function update_data()
	{
		return array(
			array('custom', array(array(&$this, 'copy_from_notifications'))),
		);
	}

	/**
	* Copy category track from 3.0.x table
	*
	* @return null
	*/
	public function copy_from_notifications()
	{
		$sql = 'SELECT n_user_id, n_cat_id
			FROM ' . $this->table_prefix . 'directory_notifications';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$data = array(
				'cat_id'		=> (int) $row['n_cat_id'],
				'user_id'		=> (int) $row['n_user_id'],
				'notify_status'	=> 1,
			);

			$sql = 'INSERT INTO ' . $this->table_prefix . 'directory_watch ' . $this->db->sql_build_array('INSERT', $data);
			$this->db->sql_query($sql);
		}
		$this->db->sql_freeresult($result);
	}
}

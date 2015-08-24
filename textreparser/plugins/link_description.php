<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\textreparser\plugins;

class link_description extends \phpbb\textreparser\row_based_plugin
{
	/** @var string */
	protected $dir_link_table;

	/**
	* Set the directory links database table name
	*
	* @param	string	$dir_link_table
	* @return	null
	*/
	public function set_table_name($dir_link_table)
	{
		$this->dir_link_table = $dir_link_table;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_columns()
	{
		return array(
			'id'         => 'link_id',
			'text'       => 'link_description',
			'bbcode_uid' => 'link_uid',
			'options'    => 'link_flags',
		);
	}

	/**
	* {@inheritdoc}
	*/
	public function get_table_name()
	{
		return $this->dir_link_table;
	}
}

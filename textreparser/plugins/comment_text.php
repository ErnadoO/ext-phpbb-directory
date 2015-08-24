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

class comment_text extends \phpbb\textreparser\row_based_plugin
{
	/** @var string */
	protected $dir_comment_table;

	/**
	* Set the directory comments database table name
	*
	* @param	string	$dir_comment_table
	* @return	null
	*/
	public function set_table_name($dir_comment_table)
	{
		$this->dir_comment_table = $dir_comment_table;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_columns()
	{
		return array(
			'id'         => 'comment_id',
			'text'       => 'comment_text',
			'bbcode_uid' => 'comment_uid',
			'options'    => 'comment_flags',
		);
	}

	/**
	* {@inheritdoc}
	*/
	public function get_table_name()
	{
		return $this->dir_comment_table;
	}
}

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

class cat_description extends \phpbb\textreparser\row_based_plugin
{
	/** @var string */
	protected $dir_cat_table;

	/**
	* Set the directory categories database table name
	*
	* @param	string	$dir_cat_table
	* @return	null
	*/
	public function set_table_name($dir_cat_table)
	{
		$this->dir_cat_table = $dir_cat_table;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_columns()
	{
		return array(
			'id'         => 'cat_id',
			'text'       => 'cat_desc',
			'bbcode_uid' => 'cat_desc_uid',
			'options'    => 'cat_desc_options',
		);
	}

	/**
	* {@inheritdoc}
	*/
	public function get_table_name()
	{
		return $this->dir_cat_table;
	}
}

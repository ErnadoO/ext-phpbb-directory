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
	protected function get_records_by_range_query($min_id, $max_id)
	{
		$columns = $this->get_columns();
		$fields  = array();
		foreach ($columns as $field_name => $column_name)
		{
			if ($column_name === $field_name)
			{
				$fields[] = $column_name;
			}
			else
			{
				$fields[] = $column_name . ' AS ' . $field_name;
			}
		}

		$sql = 'SELECT ' . implode(', ', $fields) . '
			FROM ' . $this->table . '
			WHERE ' . $columns['id'] . ' BETWEEN ' . $min_id . ' AND ' . $max_id . '
				AND ' . $columns['text'] . ' <> ""';

		return $sql;
	}
}

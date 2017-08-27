<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\migrations\v20x;

class add_cat_route_field extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\ernadoo\phpbbdirectory\migrations\v10x\v1_0_0',
		);
	}

	/**
	* @inheritDoc
	*/
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'directory_cats' => array(
					'cat_route'	=> array('VCHAR', '', 'after' => 'cat_name'),
				)
			),
		);
	}

	/**
	* @inheritDoc
	*/
	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'rewrite_cat_name'))),
		);
	}

	/**
	* @inheritDoc
	*/
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'directory_cats' => array(
					'cat_route',
				),
			),
		);
	}

	public function rewrite_cat_name()
	{
		$slug = new \E1379\SpeakingUrl\SpeakingUrl();

		$sql = 'SELECT cat_id, cat_name FROM ' . $this->table_prefix . 'directory_cats';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$sql = 'UPDATE ' . $this->table_prefix . 'directory_cats' . '
				SET cat_route = "' . (string) $slug->getSlug($row['cat_name'], array('lang' => $this->config['default_lang'], 'symbols' => true)). '"
				WHERE cat_id = ' . (int) $row['cat_id'];
			$this->db->sql_query($sql);
		}
	}
}

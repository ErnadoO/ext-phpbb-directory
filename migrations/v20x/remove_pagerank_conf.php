<?php
/**
 *
 * phpBB Directory extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017 ErnadoO <http://www.phpbb-services.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ernadoo\phpbbdirectory\migrations\v20x;

class remove_pagerank_conf extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\ernadoo\phpbbdirectory\migrations\v10x\v1_0_0',
		);
	}

	public function update_data()
	{
		return array(
			array('config.remove', array('dir_activ_pagerank')),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.add', array('dir_activ_pagerank', '1')),
		);
	}
	
	public function update_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'directory_links'	=> array(
					'link_pagerank',
				),
			),
		);
	}
	
	public function revert_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'directory_links'	=> array(
					'link_pagerank' => array('CHAR:2', ''),
				),
			),
		);
	}
}

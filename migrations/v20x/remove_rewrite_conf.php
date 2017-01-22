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

class remove_rewrite_conf extends \phpbb\db\migration\migration
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
			array('config.remove', array('dir_activ_rewrite')),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.add', array('dir_activ_rewrite', '0')),
		);
	}
}

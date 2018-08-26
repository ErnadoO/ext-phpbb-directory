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

class fix_thumbnails extends \phpbb\db\migration\migration
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
	public function update_data()
	{
		return array(
			array('if', array(
				(preg_match('#^http:#', $this->config['dir_thumb_service'])),
				array('config.update', array('dir_thumb_service', str_replace('http:', 'https:', $this->config['dir_thumb_service']))),
			)),
		);
	}
}

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
}

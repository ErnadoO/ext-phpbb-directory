<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

namespace ernadoo\phpbbdirectory\core;

class nestedset_category extends \phpbb\tree\nestedset
{
	/**
	* Construct
	*
	* @param \phpbb\db\driver\driver_interface	$db				Database connection
	* @param \phpbb\lock\db						$lock			Lock class used to lock the table when moving forums around
	* @param string								$table_name		Table name
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\lock\db $lock, $table_name)
	{
		parent::__construct(
			$db,
			$lock,
			$table_name,
			'DIR_NESTEDSET_',
			'',
			array(
				'cat_id',
				'cat_name',
			),
			array(
				'item_id'		=> 'cat_id',
				'parent_id'		=> 'parent_id',
				'item_parents'	=> 'cat_parents',
			)
		);
	}
}

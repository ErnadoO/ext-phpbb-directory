<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\core;

class nestedset_category extends \phpbb\tree\nestedset
{
	/**
	* Construct
	*
	* @param \phpbb\db\driver\driver_interface	$db					Database connection
	* @param \phpbb\lock\db						$lock				Lock class used to lock the table when moving forums around
	* @param string								$categorie_table	Categories table
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\lock\db $lock, $categorie_table)
	{
		parent::__construct(
			$db,
			$lock,
			$categorie_table,
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

<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
* Russian translation by HD321kbps
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACL_CAT_DIRECTORY'			=> 'Каталог',

	'ACL_M_DELETE_DIR'			=> 'Может удалять сайты',
	'ACL_M_DELETE_COMMENT_DIR'	=> 'Может удалять комментарии',
	'ACL_M_EDIT_DIR'			=> 'Может редактировать сайты',
	'ACL_M_EDIT_COMMENT_DIR'	=> 'Может редактировать комментарии',
	'ACL_U_COMMENT_DIR'			=> 'Может оставить комментарий (если комментарии разрешены в категории)',
	'ACL_U_DELETE_DIR'			=> 'Может удалять свои ссылки',
	'ACL_U_DELETE_COMMENT_DIR'	=> 'Может удалять свои комментарии',
	'ACL_U_EDIT_DIR'			=> 'Может редактировать свои ссылки',
	'ACL_U_EDIT_COMMENT_DIR'	=> 'Может редактировать свои комментарии',
	'ACL_U_SEARCH_DIR'			=> 'Может использовать поиск по каталогу',
	'ACL_U_SUBMIT_DIR'			=> 'Может одобрять сайты в каталоге',
	'ACL_U_VOTE_DIR'			=> 'Может голосовать за сайт в каталоге',
));

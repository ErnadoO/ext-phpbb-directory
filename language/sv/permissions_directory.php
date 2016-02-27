<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
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
	'ACL_CAT_DIRECTORY'			=> 'Länklista',

	'ACL_M_DELETE_DIR'			=> 'Kan ta bort en hemsida',
	'ACL_M_DELETE_COMMENT_DIR'	=> 'Kan ta bort kommentarer',
	'ACL_M_EDIT_DIR'			=> 'Kan redigera en hemsida',
	'ACL_M_EDIT_COMMENT_DIR'	=> 'Kan redigera kommentarer',
	'ACL_U_COMMENT_DIR'			=> 'Kan lägga till en kommentar (om kommentarer tillåts i kategorin)',
	'ACL_U_DELETE_DIR'			=> 'Kan ta bort egna länkar',
	'ACL_U_DELETE_COMMENT_DIR'	=> 'Kan ta bort egna kommentarer',
	'ACL_U_EDIT_DIR'			=> 'Kan redigera egna länkar',
	'ACL_U_EDIT_COMMENT_DIR'	=> 'Kan redigera egna kommentarer',
	'ACL_U_SEARCH_DIR'			=> 'Kan söka i länklistan',
	'ACL_U_SUBMIT_DIR'			=> 'Kan lägga till en hemsida till länklistan',
	'ACL_U_VOTE_DIR'			=> 'Kan rösta på en hemsida i länklistan',
));

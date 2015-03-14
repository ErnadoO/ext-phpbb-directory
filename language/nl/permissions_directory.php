<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
* Dutch translation by Dutch Translators (https://github.com/dutch-translators)
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
	'ACL_CAT_DIRECTORY'			=> 'Directory',

	'ACL_M_DELETE_DIR'			=> 'Kan website verwijderen',
	'ACL_M_DELETE_COMMENT_DIR'	=> 'Kan reacties verwijderen',
	'ACL_M_EDIT_DIR'			=> 'Kan website wijzigen',
	'ACL_M_EDIT_COMMENT_DIR'	=> 'Kan reacties wijzigen',
	'ACL_U_COMMENT_DIR'			=> 'Kan reactie plaatsen (als reacties zijn toegestaan in de categorie)',
	'ACL_U_DELETE_DIR'			=> 'Kan eigen links verwijderen',
	'ACL_U_DELETE_COMMENT_DIR'	=> 'Kan eigen reacties verwijderen',
	'ACL_U_EDIT_DIR'			=> 'Kan eigen links wijzigen',
	'ACL_U_EDIT_COMMENT_DIR'	=> 'Kan eigen reacties wijzigen',
	'ACL_U_SEARCH_DIR'			=> 'Kan zoeken in de directory',
	'ACL_U_SUBMIT_DIR'			=> 'Kan een website opgeven in de directory',
	'ACL_U_VOTE_DIR'			=> 'Kan stemmen op een website in de directory',
));

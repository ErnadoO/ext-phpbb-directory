<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* Übersetzt von franki (http://dieahnen.de/ahnenforum/)
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
	'ACL_CAT_DIRECTORY'			=> 'Linkverzeichnis',

	'ACL_M_DELETE_DIR'			=> 'Kann einen Link löschen.',
	'ACL_M_DELETE_COMMENT_DIR'	=> 'Kann Kommentare löschen.',
	'ACL_M_EDIT_DIR'			=> 'Kann Links bearbeiten.',
	'ACL_M_EDIT_COMMENT_DIR'	=> 'Kann Kommentare bearbeiten',
	'ACL_U_COMMENT_DIR'			=> 'Kann Kommentare posten (sofern dies in der Kategorie aktiviert wurde).',
	'ACL_U_DELETE_DIR'			=> 'Kann eigene Links löschen.',
	'ACL_U_DELETE_COMMENT_DIR'	=> 'Kann eigene Kommentare löschen.',
	'ACL_U_EDIT_DIR'			=> 'Kann eigene Links bearbeiten',
	'ACL_U_EDIT_COMMENT_DIR'	=> 'Kann eigene Kommentare bearbeiten',
	'ACL_U_SEARCH_DIR'			=> 'Kann das Linkverzeichnis durchsuchen.',
	'ACL_U_SUBMIT_DIR'			=> 'Kann einen Link vorschlagen.',
	'ACL_U_VOTE_DIR'			=> 'Kann für einen Link abstimmen.',
));

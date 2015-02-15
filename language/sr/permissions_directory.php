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

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACL_CAT_DIRECTORY'				=> 'Direktorijuma',

	'ACL_M_DELETE_DIR'				=> 'Nemoguće je izbrisati vebsajt',
	'ACL_M_DELETE_COMMENT_DIR'		=> 'Nemoguće je izbrisati komentare',
	'ACL_M_EDIT_DIR' 				=> 'Nemoguće je urediti vebsajt',
	'ACL_M_EDIT_COMMENT_DIR'		=> 'Nemoguće je urediti komentare',
	'ACL_U_COMMENT_DIR'				=> 'Možete ostaviti komentar (ako je to dozvoljeno u ovoj kategoriji)',
	'ACL_U_DELETE_DIR'				=> 'Možete izbrisati sopstvene linkove',
	'ACL_U_DELETE_COMMENT_DIR'		=> 'Možete izbrisati sopstvene komentare',
	'ACL_U_EDIT_DIR'				=> 'Možete modifikovati sopstvene linkove',
	'ACL_U_EDIT_COMMENT_DIR'		=> 'Možete modifikovati sopstvene komentare',
	'ACL_U_SEARCH_DIR'				=> 'Možete pretražiti direktorijum',
	'ACL_U_SUBMIT_DIR'				=> 'Možete uneti vebsajt u direktorijum',
	'ACL_U_VOTE_DIR'				=> 'Možete glasati za vebsajt u direktorijumu',
));

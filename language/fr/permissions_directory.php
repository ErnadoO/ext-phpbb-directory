<?php
/**
*
* permissions_directory [French]
*
* @package language
* @version $Id$
* @copyright (c) 2011 http://www.phpbb-services.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
	'ACL_CAT_DIRECTORY'			=> 'Annuaire',

	'ACL_M_DELETE_DIR'			=> 'Peut supprimer un lien de l’annuaire',
	'ACL_M_DELETE_COMMENT_DIR'	=> 'Peut supprimer un commentaire',
	'ACL_M_EDIT_DIR'			=> 'Peut éditer un lien de l’annuaire',
	'ACL_M_EDIT_COMMENT_DIR'	=> 'Peut éditer un commentaire',
	'ACL_U_COMMENT_DIR'			=> 'Peut poster des commentaires (sous réserve que ceux-ci soit activés dans la catégorie)',
	'ACL_U_DELETE_DIR'			=> 'Peut supprimer ses propres liens',
	'ACL_U_DELETE_COMMENT_DIR'	=> 'Peut supprimer ses propres commentaires',
	'ACL_U_EDIT_DIR'			=> 'Peut éditer ses propres liens',
	'ACL_U_EDIT_COMMENT_DIR'	=> 'Peut éditer ses propres commentaires',
	'ACL_U_SEARCH_DIR'			=> 'Peut effectuer une recherche dans l’annuaire',
	'ACL_U_SUBMIT_DIR'			=> 'Peut proposer un lien dans l’annuaire',
	'ACL_U_VOTE_DIR'			=> 'Peut voter pour un lien de l’annuaire',
));

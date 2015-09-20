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
	'DIR_ARE_WATCHING_CAT'					=> 'Vous êtes désormais abonné à cette catégorie',
	'DIR_BANNER_DISALLOWED_CONTENT'			=> 'L’envoi a été rejeté car le fichier envoyé a été identifié comme un vecteur éventuel d’attaque.',
	'DIR_BANNER_DISALLOWED_EXTENSION'		=> 'Ce fichier ne peut pas être affiché car l’extension <strong>%s</strong> n’est pas autorisée.',
	'DIR_BANNER_EMPTY_FILEUPLOAD'			=> 'Le fichier bannière transféré est vide.',
	'DIR_BANNER_EMPTY_REMOTE_DATA'			=> 'La bannière indiquée n’a pas pu être transférée car les données distantes semblent être invalides ou corrompues.',
	'DIR_BANNER_IMAGE_FILETYPE_MISMATCH'	=> 'Type de bannière incorrect: l’extension %1$s était attendue mais l’extension %2$s a été trouvée.',
	'DIR_BANNER_INVALID_FILENAME'			=> '%s est un nom de fichier invalide.',
	'DIR_BANNER_NOT_UPLOADED'				=> 'La bannière n’a pas pu être transférée.',
	'DIR_BANNER_PARTIAL_UPLOAD'				=> 'Le fichier n’a pu être que partiellement transféré.',
	'DIR_BANNER_PHP_SIZE_NA'				=> 'La taille de la bannière est trop importante.<br />La taille maximum réglée dans php.ini n’a pas pu être déterminée.',
	'DIR_BANNER_PHP_SIZE_OVERRUN'			=> 'La taille de la bannière trop importante. La taille maximum de transfert autorisée est %d Mo.<br />Notez que ce paramètre est inscrit dans php.ini et ne peut pas être dépassé.',
	'DIR_BANNER_REMOTE_UPLOAD_TIMEOUT'		=> 'La bannière spécifiée n’a pas été récupérée car la requête a éxpirée.',
	'DIR_BANNER_UNABLE_GET_IMAGE_SIZE'		=> 'Impossible de déterminer les dimensions de la bannière.',
	'DIR_BANNER_URL_INVALID'				=> 'L’URL de la bannière est invalide.',
	'DIR_BANNER_URL_NOT_FOUND'				=> 'Le fichier indiqué est introuvable.',
	'DIR_BANNER_WRONG_FILESIZE'				=> 'La taille de la bannière doit être comprise entre 0 et %1d %2s.',
	'DIR_BANNER_WRONG_SIZE'					=> 'La bannière envoyée a une largeur de %3$d pixels et une hauteur de %4$d pixels. Les bannières doivent faire au plus %1$d pixels de large et %2$d pixels de haut.',
	'DIR_BUTTON_NEW_SITE'					=> 'Nouveau lien',
	'DIR_CAT'								=> 'Catégorie',
	'DIR_CAT_NAME'							=> 'Nom de la catégorie',
	'DIR_CAT_TITLE'							=> 'Catégories de l’annuaire',
	'DIR_CAT_TOOLS'							=> 'Outils de la catégorie',
	'DIR_CLICK_RETURN_DIR'					=> 'Cliquez %sici%s pour retourner au sommaire de l’annuaire',
	'DIR_CLICK_RETURN_CAT'					=> 'Cliquez %sici%s pour retourner dans la catégorie',
	'DIR_CLICK_RETURN_COMMENT'				=> 'Cliquez %sici%s pour revenir aux commentaires',
	'DIR_COMMENTS_ORDER'					=> 'Commentaires',
	'DIR_COMMENT_TITLE'						=> 'Voir/poster un commentaire',
	'DIR_COMMENT_DELETE'					=> 'Supprimer le commentaire',
	'DIR_COMMENT_DELETE_CONFIRM'			=> 'Êtes vous certain de vouloir supprimer le commentaire ?',
	'DIR_COMMENT_DELETE_OK'					=> 'Le commentaire a bien été supprimé.',
	'DIR_COMMENT_EDIT'						=> 'Modifier le commentaire',
	'DIR_DELETE_BANNER'						=> 'Supprimer la bannière',
	'DIR_DELETE_OK'							=> 'Le site a bien été supprimé',
	'DIR_DELETE_SITE'						=> 'Supprimer le site',
	'DIR_DELETE_SITE_CONFIRM'				=> 'Êtes vous certain de vouloir supprimer le site ?',
	'DIR_DESCRIPTION'						=> 'Description',
	'DIR_DESCRIPTION_EXP'					=> 'Une courte description de votre site, celle-ci ne peut pas dépasser %d caractères.',
	'DIR_DISPLAY_LINKS'						=> 'Afficher les liens précédents',
	'DIR_EDIT'								=> 'Modifier',
	'DIR_EDIT_COMMENT_OK'					=> 'Le commentaire a bien été modifié',
	'DIR_EDIT_SITE'							=> 'Modifier un site',
	'DIR_EDIT_SITE_ACTIVE'					=> 'Votre site a bien été modifié, cependant le site n’apparaîtra dans l’annuaire qu’après validation',
	'DIR_EDIT_SITE_OK'						=> 'Le site a bien été modifié',
	'DIR_ERROR_AUTH_COMM'					=> 'Vous n’êtes pas autorisé à poster des commentaires',
	'DIR_ERROR_CAT'							=> 'Impossible de récupérer les données de la catégorie actuelle',
	'DIR_ERROR_CHECK_URL'					=> 'Cette URL semble injoignable',
	'DIR_ERROR_COMM_LOGGED'					=> 'Vous devez être connecté pour pouvoir poster un commentaire',
	'DIR_ERROR_KEYWORD'						=> 'Vous devez entrer des mots clés pour faire une recherche.',
	'DIR_ERROR_NOT_AUTH'					=> 'Vous n’avez pas l’autorisation requise pour cette opération',
	'DIR_ERROR_NOT_FOUND_BACK'				=> 'La page spécifiée pour le lien réciproque est introuvable.',
	'DIR_ERROR_NO_CATS'						=> 'Cette catégorie n’existe pas',
	'DIR_ERROR_NO_LINK'						=> 'Le site recherché n’existe pas',
	'DIR_ERROR_NO_LINKS'					=> 'Ce site n’existe pas',
	'DIR_ERROR_NO_LINK_BACK'				=> 'Le lien réciproque n’a pas été trouvé sur la page que vous avez spécifiée.',
	'DIR_ERROR_SUBMIT_TYPE'					=> 'Type de données incorrect dans la fonction <em>dir_submit_type</em>',
	'DIR_ERROR_URL'							=> 'Vous devez entrer une URL valide.',
	'DIR_ERROR_VOTE'						=> 'Vous avez déjà voté pour ce site',
	'DIR_ERROR_VOTE_LOGGED'					=> 'Vous devez être connecté pour pouvoir voter',
	'DIR_ERROR_WRONG_DATA_BACK'				=> 'L’adresse du lien retour doit être une URL valide, incluant le protocole. Par exemple http://www.exemple.com/.',
	'DIR_FIELDS'							=> 'Veuillez remplir les champs marqués d’une *',
	'DIR_FLAG'								=> 'Drapeau',
	'DIR_FLAG_EXP'							=> 'Choisissez un drapeaux, qui indiquera la nationalité du site',
	'DIR_FROM_TEN'							=> '%s/10',
	'DIR_GUEST_EMAIL'						=> 'Votre adresse e-mail',
	'DIR_MAKE_SEARCH'						=> 'Rechercher un site',
	'DIR_NAME_ORDER'						=> 'Nom',
	'DIR_NEW_COMMENT_OK'					=> 'Votre commentaire a bien été ajouté',
	'DIR_NEW_SITE'							=> 'Ajouter un site à l’annuaire',
	'DIR_NEW_SITE_ACTIVE'					=> 'Votre site a bien été ajouté, cependant le site n’apparaîtra dans l’annuaire qu’après validation',
	'DIR_NEW_SITE_OK'						=> 'Votre site a bien été ajouté à l’annuaire',
	'DIR_NB_CLICKS'							=> array(
													1 => '%d clic',
													2 => '%d clics',
												),
	'DIR_NB_CLICKS_ORDER'					=> 'Clics',
	'DIR_NB_COMMS'							=> array(
													1 => '%d commentaire',
													2 => '%d commentaires',
												),
	'DIR_NB_LINKS'							=> array(
													1 => '%d lien',
													2 => '%d liens',
												),
	'DIR_NB_VOTES'							=> array(
													1 => '%d vote',
													2 => '%d votes',
												),
	'DIR_NONE'								=> 'Aucune',
	'DIR_NOTE'								=> 'Note',
	'DIR_NO_COMMENT'						=> 'Il n’y a aucun commentaire sur ce site',
	'DIR_NO_DRAW_CAT'						=> 'Il n’y a aucune catégorie',
	'DIR_NO_DRAW_LINK'						=> 'Il n’y a aucun site dans cette catégorie',
	'DIR_NO_NOTE'							=> 'Aucune',
	'DIR_NOT_WATCHING_CAT'					=> 'Vous ne surveillez plus cette catégorie',

	'DIR_PAGERANK'							=> 'Pr',
	'DIR_PAGERANK_NOT_AVAILABLE'			=> 'n/a',
	'DIR_PR_ORDER'							=> 'PageRank',
	'DIR_REPLY_EXP'							=> 'Votre commentaire ne peut pas dépasser %d caractères.',
	'DIR_REPLY_TITLE'						=> 'Poster un commentaire',
	'DIR_RSS'								=> 'Flux de',
	'DIR_SEARCH_CATLIST'					=> 'Chercher dans une catégorie',
	'DIR_SEARCH_DESC_ONLY'					=> 'Description uniquement',
	'DIR_SEARCH_METHOD'						=> 'Méthode',
	'DIR_SEARCH_NB_CLICKS'					=> array(
													1 => 'Clic',
													2 => 'Clics',
												),
	'DIR_SEARCH_NB_COMMS'					=> array(
													1 => 'Commentaire',
													2 => 'Commentaires',
												),
	'DIR_SEARCH_NO_RESULT'					=> 'Aucun résultat trouvé pour la recherche',
	'DIR_SEARCH_RESULT'						=> 'Résultat(s) de la recherche',
	'DIR_SEARCH_SUBCATS'					=> 'Rechercher dans les sous-catégories',
	'DIR_SEARCH_TITLE_DESC'					=> 'Nom et description',
	'DIR_SEARCH_TITLE_ONLY'					=> 'Nom uniquement',
	'DIR_SITE_BACK'							=> 'URL de votre page où se trouve le lien réciproque',
	'DIR_SITE_BACK_EXPLAIN'					=> 'Dans cette catégorie, il est demandé que les sites proposés fassent un lien en retour. Veuillez spécifier ici l’url de la page où se trouve le lien en question.',
	'DIR_SITE_BANN'							=> 'Lier une bannière ',
	'DIR_SITE_BANN_EXP'						=> 'Vous devez entrer ici l’URL complète de votre bannière. Notez que ce champ n’est pas obligatoire. La taille maximale autorisée est <b>%d x %d</b> pixels, la bannière sera automatiquement redimensionnée si cette taille est dépassée.',
	'DIR_SITE_NAME'							=> 'Nom du site',
	'DIR_SITE_RSS'							=> 'Flux RSS',
	'DIR_SITE_RSS_EXPLAIN'					=> 'Vous avez la possibilité de spécifier l’URL du Flux RSS du site s’il en existe un. Une icône rss apparaitra à coté de votre site, permettant de s’abonner à votre flux.',
	'DIR_SITE_URL'							=> 'URL',
	'DIR_SOMMAIRE'							=> 'Sommaire de l’annuaire',
	'DIR_START_WATCHING_CAT'				=> 'S’abonner à cette catégorie',
	'DIR_STOP_WATCHING_CAT'					=> 'Se désabonner de cette catégorie',
	'DIR_SUBMIT_TYPE_1'						=> 'Votre site devra être validé par un administrateur.',
	'DIR_SUBMIT_TYPE_2'						=> 'Votre site apparaîtra directement dans l’annuaire.',
	'DIR_SUBMIT_TYPE_3'						=> 'Vous êtes administrateur, votre site sera automatiquement ajouté.',
	'DIR_SUBMIT_TYPE_4'						=> 'Vous êtes modérateur, votre site sera automatiquement ajouté.',
	'DIR_THUMB'								=> 'Miniature du site',
	'DIR_USER_PROP'							=> 'Site proposé par',
	'DIR_VOTE'								=> 'Voter',
	'DIR_VOTE_OK'							=> 'Votre vote a bien été pris en compte',
	'DIR_POST'								=> 'Poster',

	'DIRECTORY_TRANSLATION_INFO'			=> '',

	'RECENT_LINKS'							=> 'Derniers sites ajoutés',

	// Ne pas traduire cette ligne!
	'SEED'									=> 'Mining PageRank is AGAINST GOOGLE’S TERMS OF SERVICE. Yes, I’m talking to you, scammer.',

	'TOO_LONG_BACK'							=> 'L’URL de la page contenant le lien réciproque est trop longue (255 caractères maximum)',
	'TOO_LONG_DESCRIPTION'					=> 'Votre description est trop longue',
	'TOO_LONG_REPLY'						=> 'Votre commentaire est trop long',
	'TOO_LONG_RSS'							=> 'L’URL du flux RSS est trop longue',
	'TOO_LONG_SITE_NAME'					=> 'Vous avez indiqué un nom de site trop long (100 caractères maximum)',
	'TOO_LONG_URL'							=> 'Vous avez indiqué une URL trop longue pour ce site (255 caractères maximum)',
	'TOO_MANY_ADDS'							=> 'Vous avez atteint le nombre maximum de tentatives de soumission. Réessayez plus tard.',
	'TOO_SHORT_BACK'						=> 'Vous devez indiquer l’URL de la page contenant le lien réciproque',
	'TOO_SHORT_DESCRIPTION'					=> 'Vous devez entrer une description',
	'TOO_SHORT_REPLY'						=> 'Votre commentaire est trop court',
	'TOO_SHORT_RSS'							=> 'L’URL du flux RSS est trop courte',
	'TOO_SHORT_SITE_NAME'					=> 'Vous devez indiquer un nom pour le site',
	'TOO_SHORT_URL'							=> 'Vous devez indiquer une URL pour ce site',
	'TOO_SMALL_CAT'							=> 'Vous devez choisir une catégorie',

	'WRONG_DATA_RSS'						=> 'Le flux RSS doit être une URL valide, incluant le protocole. Par exemple http://www.exemple.com/.',
	'WRONG_DATA_WEBSITE'					=> 'L’adresse du site Internet doit être une URL valide, incluant le protocole. Par exemple http://www.exemple.com/.',
));

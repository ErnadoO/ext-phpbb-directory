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
	'DIRECTORY'			=> 'Annuaire',

	'NOTIFICATION_DIR_NEW'								=> '%1$s a posté un nouveau lien « %2$s » dans la catégorie « %3$s ».',
	'NOTIFICATION_DIR_UCP'								=> 'Notifications de phpBB Annuaire',
	'NOTIFICATION_TYPE_DIR_UCP_ERROR_CHECK'				=> 'Le lien retour de l’un de vos sites web n’a pas été trouvé',
	'NOTIFICATION_TYPE_DIR_UCP_IN_MODERATION_QUEUE'		=> 'Un nouveau site est en attente de validation',
	'NOTIFICATION_TYPE_DIR_UCP_MODERATION_QUEUE'		=> 'Votre site web a été approuvé ou désapprouvé par un administrateur',
	'NOTIFICATION_TYPE_DIR_UCP_WEBSITE'					=> 'Quelqu’un a soumis un site web dans une catégorie que vous surveillez',

	'NOTIFICATION_DIR_WEBSITE_APPROVED'					=> 'Votre site web « %1$s » posté dans la catégorie « %2$s » a été validé.',
	'NOTIFICATION_DIR_WEBSITE_DISAPPROVED'				=> 'Votre site web « %1$s » posté dans la catégorie « %2$s » n’a pas été validé.',
	'NOTIFICATION_DIR_WEBSITE_ERROR_CHECK'				=> 'Le lien retour sur « %1$s » posté dans la catégorie « %2$s » n’a pas été trouvé.',
	'NOTIFICATION_DIR_WEBSITE_IN_QUEUE'					=> 'Un nouveau site web nommé « %1$s » a été posté par %2$s et nécéssite une validation.',
));

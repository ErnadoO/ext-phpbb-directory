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
	'DIRECTORY'			=> 'Directory',

	'NOTIFICATION_DIR_NEW'								=> '%1$s plaatste een nieuwe website "%2$s" in de categorie "%3$s".',
	'NOTIFICATION_DIR_UCP'								=> 'PhpBB Directory Notificatie',
	'NOTIFICATION_TYPE_DIR_UCP_ERROR_CHECK'				=> 'De terugkoppeling naar dit forum op één van mijn links is niet gevonden',
	'NOTIFICATION_TYPE_DIR_UCP_IN_MODERATION_QUEUE'		=> 'Een nieuwe website heeft goedkeuring nodig',
	'NOTIFICATION_TYPE_DIR_UCP_MODERATION_QUEUE'		=> 'Je website is goedgekeurd of afgekeurd door een moderator',
	'NOTIFICATION_TYPE_DIR_UCP_WEBSITE'					=> 'Iemand heeft een website toegevoegd aan de categorie waarop je bent geabonneerd',

	'NOTIFICATION_DIR_WEBSITE_APPROVED'					=> 'Je website "%1$s" in de categorie "%2$s" is goedgekeurd.',
	'NOTIFICATION_DIR_WEBSITE_DISAPPROVED'				=> 'Je website "%1$s" in de categorie "%2$s" is afgekeurd.',
	'NOTIFICATION_DIR_WEBSITE_ERROR_CHECK'				=> 'De terugkoppeling op "%1$s" in de categorie "%2$s" is niet gevonden',
	'NOTIFICATION_DIR_WEBSITE_IN_QUEUE'					=> 'Een nieuwe website genaamd "%1$s" is geplaatst door %2$s en heeft goedkeuring nodig.',
));

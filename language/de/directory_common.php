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
	'DIRECTORY'			=> 'Directory',

	'NOTIFICATION_DIR_NEW'								=> '%1$s hat einen neuen Link "%2$s" in der Kategorie "%3$s" gepostet.',
	'NOTIFICATION_DIR_UCP'								=> 'PhpBB-Verzeichnisankündigungen',
	'NOTIFICATION_TYPE_DIR_UCP_ERROR_CHECK'				=> 'Der backlink zu diesem Forum auf einer meiner Links wird nicht gefunden',
	'NOTIFICATION_TYPE_DIR_UCP_IN_MODERATION_QUEUE'		=> 'Ein neuer Link braucht Genehmigung',
	'NOTIFICATION_TYPE_DIR_UCP_MODERATION_QUEUE'		=> 'Dein Link wird durch einen Moderator genehmigt oder abgelehnt',
	'NOTIFICATION_TYPE_DIR_UCP_WEBSITE'					=> 'Jemand legt einen Link in eine Kategorie die Du abonniert hast',

	'NOTIFICATION_DIR_WEBSITE_APPROVED'					=> 'Dein Link "%1$s" in der Kategorie "%2$s" wurde genehmigt.',
	'NOTIFICATION_DIR_WEBSITE_DISAPPROVED'				=> 'Dein Link "%1$s" in der Kategorie "%2$s" wurde abgelehnt.',
	'NOTIFICATION_DIR_WEBSITE_ERROR_CHECK'				=> 'Der Backlink von "%1$s" in der Kategorie "%2$s" wurde nicht gefunden',
	'NOTIFICATION_DIR_WEBSITE_IN_QUEUE'					=> 'Ein neuer Link mit Namen "%1$s" wurde eingetragen von %2$s und wartet auf Genehmigung.',
));

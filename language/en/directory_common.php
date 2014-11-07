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
	'DIRECTORY'			=> 'Directory',

	'NOTIFICATION_DIR_NEW'								=> '%1$s posted a new website "%2$s" in the categorie "%3$s".',
	'NOTIFICATION_DIR_UCP'								=> 'PhpBB Directory Notifications',
	'NOTIFICATION_TYPE_DIR_UCP_ERROR_CHECK'				=> 'The backlink to this forum on one of my links is not found',
	'NOTIFICATION_TYPE_DIR_UCP_IN_MODERATION_QUEUE'		=> 'A new website needs approval',
	'NOTIFICATION_TYPE_DIR_UCP_MODERATION_QUEUE'		=> 'Your website are approved or disapproved by a moderator',
	'NOTIFICATION_TYPE_DIR_UCP_WEBSITE'					=> 'Someone submits a website in a categorie to which you are subscribed',

	'NOTIFICATION_DIR_WEBSITE_APPROVED'					=> 'Your website "%1$s" in the categorie "%2$s" was approved.',
	'NOTIFICATION_DIR_WEBSITE_DISAPPROVED'				=> 'Your website "%1$s" in the categorie "%2$s" was disapproved.',
	'NOTIFICATION_DIR_WEBSITE_ERROR_CHECK'				=> 'The backlink on "%1$s" in the categorie "%2$s" is not found',
	'NOTIFICATION_DIR_WEBSITE_IN_QUEUE'					=> 'A new website named "%1$s" was posted by %2$s and needs approval.',
));

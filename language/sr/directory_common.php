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
	'DIRECTORY'										=> 'Direktorijuma',

	'NOTIFICATION_DIR_NEW'							=> '%1$s je objavio novi link "%2$s" u kategoriji "%3$s".',
	'NOTIFICATION_DIR_UCP'							=> 'Notifikacije PhpBB direktorijuma',
	'NOTIFICATION_TYPE_DIR_UCP_ERROR_CHECK'			=> 'Povratni link jednog od vaših sajtova nije pronadjen',
	'NOTIFICATION_TYPE_DIR_UCP_IN_MODERATION_QUEUE'	=> 'Jedan novi sajt ceka potvrdu',
	'NOTIFICATION_TYPE_DIR_UCP_MODERATION_QUEUE'	=> 'Vaš vebsajt je potvrdjen ili nije potvrdjen od strane administratora',
	'NOTIFICATION_TYPE_DIR_UCP_WEBSITE'				=> 'Novi link je objavljen u kategoriji koju pratite',

	'NOTIFICATION_DIR_WEBSITE_APPROVED'				=> 'Vaš vebsajt "%1$s" objavljen u kategoriji "%2$s"je potvrdjen.',
	'NOTIFICATION_DIR_WEBSITE_DISAPPROVED'			=> 'Vaš vebsajt "%1$s" objavljen u kategoriji "%2$s"nije potvrdjen.',
	'NOTIFICATION_DIR_WEBSITE_ERROR_CHECK'			=> 'Povratni link za "%1$s"objavljen u kategoriji "%2$s"nije pronadjen.',
	'NOTIFICATION_DIR_WEBSITE_IN_QUEUE'				=> 'Novi vebsajt pod nazivom "%1$s" je objavljen od strane %2$s i čeka potvrdu.',
));

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
	'DIRECTORY'			=> 'Länklista',

	'NOTIFICATION_DIR_NEW'								=> '%1$s la till en ny hemsida "%2$s" i kategorin "%3$s".',
	'NOTIFICATION_DIR_UCP'								=> 'PhpBB Länklista Notifieringar',
	'NOTIFICATION_TYPE_DIR_UCP_ERROR_CHECK'				=> 'Tillbakalänken till detta forum på en av mina länkar kunde inte hittas',
	'NOTIFICATION_TYPE_DIR_UCP_IN_MODERATION_QUEUE'		=> 'En ny hemsida behöver godkännande',
	'NOTIFICATION_TYPE_DIR_UCP_MODERATION_QUEUE'		=> 'Din hemsida godkänns (eller godkänns ej) av en moderator',
	'NOTIFICATION_TYPE_DIR_UCP_WEBSITE'					=> 'Någon la till en hemsida i en kategori som du prenumererar på',

	'NOTIFICATION_DIR_WEBSITE_APPROVED'					=> 'Din hemsida "%1$s" i kategorin "%2$s" godkändes.',
	'NOTIFICATION_DIR_WEBSITE_DISAPPROVED'				=> 'Din hemsida "%1$s" i kategorin "%2$s" godkändes ej.',
	'NOTIFICATION_DIR_WEBSITE_ERROR_CHECK'				=> 'Tillbakalänken på "%1$s" i kategorin "%2$s" hittades inte',
	'NOTIFICATION_DIR_WEBSITE_IN_QUEUE'					=> 'En ny hemsida med namn "%1$s" blev tillagd av %2$s och behöver godkännande.',
));

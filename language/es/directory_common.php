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
	'DIRECTORY'			=> 'Directorio',

	'NOTIFICATION_DIR_NEW'								=> '%1$s publicó un nuevo sitio web "%2$s" en la categoría "%3$s".',
	'NOTIFICATION_DIR_UCP'								=> 'Notificaciones de Directorio de phpBB',
	'NOTIFICATION_TYPE_DIR_UCP_ERROR_CHECK'				=> 'El enlace de retroceso a este foro en uno de mis enlaces no se encuentra',
	'NOTIFICATION_TYPE_DIR_UCP_IN_MODERATION_QUEUE'		=> 'Un nuevo sitio web necesita aprobación',
	'NOTIFICATION_TYPE_DIR_UCP_MODERATION_QUEUE'		=> 'Su sitio web ha sido aprobado o rechazado por un moderador',
	'NOTIFICATION_TYPE_DIR_UCP_WEBSITE'					=> 'Alguien envía un sitio web en una categoría a la que está suscrito',

	'NOTIFICATION_DIR_WEBSITE_APPROVED'					=> 'Su sitio web "%1$s" en la categoría "%2$s" fue aprobado.',
	'NOTIFICATION_DIR_WEBSITE_DISAPPROVED'				=> 'Su sitio web "%1$s" en la categoría "%2$s" fue rechazado.',
	'NOTIFICATION_DIR_WEBSITE_ERROR_CHECK'				=> 'El vínculo de retroceso en "%1$s" en la categoría "%2$s" no funciona',
	'NOTIFICATION_DIR_WEBSITE_IN_QUEUE'					=> 'Un nuevo sitio web llamado "%1$s" fue publicado por %2$s y necesita aprobación.',
));

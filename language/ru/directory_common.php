<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
* Russian translation by HD321kbps
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
	'DIRECTORY'			=> 'Каталог',

	'NOTIFICATION_DIR_NEW'								=> '%1$s добавлен новый сайт "%2$s" в категории "%3$s".',
	'NOTIFICATION_DIR_UCP'								=> 'Уведомления каталога',
	'NOTIFICATION_TYPE_DIR_UCP_ERROR_CHECK'				=> 'Обратной ссылки на форум в одной ссылке из моих представленных не найдено',
	'NOTIFICATION_TYPE_DIR_UCP_IN_MODERATION_QUEUE'		=> 'Новый сайт нуждается в одобрении',
	'NOTIFICATION_TYPE_DIR_UCP_MODERATION_QUEUE'		=> 'Ваш сайт будет одобрены или отклонен модератором после проверки',
	'NOTIFICATION_TYPE_DIR_UCP_WEBSITE'					=> 'Добавлен сайт в категории на которую вы подписаны',

	'NOTIFICATION_DIR_WEBSITE_APPROVED'					=> 'Ваш сайт "%1$s" из категории "%2$s" одобрен.',
	'NOTIFICATION_DIR_WEBSITE_DISAPPROVED'				=> 'Ваш сайт "%1$s" из категории "%2$s" был отклонен.',
	'NOTIFICATION_DIR_WEBSITE_ERROR_CHECK'				=> 'Обратная ссылка на "%1$s" в категории "%2$s" не найдена',
	'NOTIFICATION_DIR_WEBSITE_IN_QUEUE'					=> 'Новое имя сайта "%1$s" было добавлено %2$s и нуждаться в одобрении.',
));

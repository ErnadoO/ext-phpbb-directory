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
	'DIR_ARE_WATCHING_CAT'					=> 'Вы подписаны на получение уведомлений о новых сайтах в этой категории.',
	'DIR_BANNER_DISALLOWED_CONTENT'			=> 'Передача была прервана, так как ваш файл был определен как опасный.',
	'DIR_BANNER_DISALLOWED_EXTENSION'		=> 'Этот файл не может быть показан, так как его расширение <strong>%s</strong> не допустимо.',
	'DIR_BANNER_EMPTY_FILEUPLOAD'			=> 'Файл баннера пуст.',
	'DIR_BANNER_EMPTY_REMOTE_DATA'			=> 'Представленный баннер не может быть добавлен, поскольку он отображается неправильно или поврежден.',
	'DIR_BANNER_IMAGE_FILETYPE_MISMATCH'	=> 'Баннер несоответствует разрешенным типам файлов: ожидаемое расширение файла должно быть %1$s, а у вас %2$s.',
	'DIR_BANNER_INVALID_FILENAME'			=> '%s является недопустимым именем файла.',
	'DIR_BANNER_NOT_UPLOADED'				=> 'Баннер не может быть перенесен',
	'DIR_BANNER_PARTIAL_UPLOAD'				=> 'Файл не может быть полностью перенесен.',
	'DIR_BANNER_PHP_SIZE_NA'				=> 'Размер баннера слишком велик.<br />Максимальный размер задается в php.ini не может быть другим".',
	'DIR_BANNER_PHP_SIZE_OVERRUN'			=> 'Размер баннера слишком велик. Максимальный допустимый размер %d М.<br />Обратите внимание, что этот параметр записывается в php.ini и не может быть превышен.',
	'DIR_BANNER_REMOTE_UPLOAD_TIMEOUT'		=> 'Указанный баннер не может быть загружен, так как истекло время запроса.',
	'DIR_BANNER_UNABLE_GET_IMAGE_SIZE'		=> 'Невозможно определить размеры баннера',
	'DIR_BANNER_URL_INVALID'				=> 'Адрес баннера является недействительным',
	'DIR_BANNER_URL_NOT_FOUND'				=> 'Файл не найден.',
	'DIR_BANNER_WRONG_FILESIZE'				=> 'Размер баннера должен быть от 0 до %1d %2s.',
	'DIR_BANNER_WRONG_SIZE'					=> 'Указанный баннер имеет ширину %3$d пикселей и высоту %4$d пикселей. Баннер не может быть болше %1$d пикселей по ширине и %2$d пикселей по высоте.',
	'DIR_BUTTON_NEW_SITE'					=> 'Новый сайт',
	'DIR_CAT'								=> 'Категория',
	'DIR_CAT_NAME'							=> 'Название категории',
	'DIR_CAT_TITLE'							=> 'Категории каталога',
	'DIR_CAT_TOOLS'							=> 'Инструменты категорий',
	'DIR_CLICK_RETURN_DIR'					=> 'Нажмите %sсюда%s, чтобы вернуться в каталог',
	'DIR_CLICK_RETURN_CAT'					=> 'Нажмите %sсюда%s, чтобы вернуться в категорию',
	'DIR_CLICK_RETURN_COMMENT'				=> 'Нажмите %sсюда%s, чтобы вернуться на страницу с комментариями',
	'DIR_COMMENTS_ORDER'					=> 'Комментарии',
	'DIR_COMMENT_TITLE'						=> 'Прочитать/написать комментарий',
	'DIR_COMMENT_DELETE'					=> 'Удалить комментарий',
	'DIR_COMMENT_DELETE_CONFIRM'			=> 'Вы уверены, что хотите удалить комментарий?',
	'DIR_COMMENT_DELETE_OK'					=> 'Ваш комментарий успешно удален.',
	'DIR_COMMENT_EDIT'						=> 'Редактировать комментарий',
	'DIR_DELETE_BANNER'						=> 'Удалить баннер',
	'DIR_DELETE_OK'							=> 'Сайт был удален',
	'DIR_DELETE_SITE'						=> 'Удалить сайт',
	'DIR_DELETE_SITE_CONFIRM'				=> 'Вы уверены, что хотите удалить сайт?',
	'DIR_DESCRIPTION'						=> 'Описание',
	'DIR_DESCRIPTION_EXP'					=> 'Краткое описание вашего сайта, Максимальное количество символов %d.',
	'DIR_DISPLAY_LINKS'						=> 'Показать сайты за',
	'DIR_EDIT'								=> 'Редактировать',
	'DIR_EDIT_COMMENT_OK'					=> 'Этот комментарий был успешно отредактирован',
	'DIR_EDIT_SITE'							=> 'Редактировать сайт',
	'DIR_EDIT_SITE_ACTIVE'					=> 'Ваш сайт был отредактирован, но оно должно быть одобрен перед тем, как появиться в каталоге',
	'DIR_EDIT_SITE_OK'						=> 'Этот сайт был успешно отредактирован',
	'DIR_ERROR_AUTH_COMM'					=> 'Вы не можете оставить комментарий',
	'DIR_ERROR_CAT'							=> 'Ошибка при попытке восстановить данные из текущей категории.',
	'DIR_ERROR_CHECK_URL'					=> 'Этот URL недопустим',
	'DIR_ERROR_COMM_LOGGED'					=> 'Вам нужно войти чтобы оставить комментарий',
	'DIR_ERROR_KEYWORD'						=> 'Вам нужно вести ключевые слова для поиска.',
	'DIR_ERROR_NOT_AUTH'					=> 'Вам не разрешено делать эту операцию',
	'DIR_ERROR_NOT_FOUND_BACK'				=> 'Указанная страница из обратной ссылки не найдена.',
	'DIR_ERROR_NO_CATS'						=> 'Эта категория не существует',
	'DIR_ERROR_NO_LINK'						=> 'Сайт, который вы ищите не существует',
	'DIR_ERROR_NO_LINKS'					=> 'Этот сайт не существует',
	'DIR_ERROR_NO_LINK_BACK'				=> 'Указанная вами обратная ссылка не была найдена',
	'DIR_ERROR_SUBMIT_TYPE'					=> 'Неправильный тип данных в функции dir_submit_type',
	'DIR_ERROR_URL'							=> 'Вы ввели правильный URL',
	'DIR_ERROR_VOTE'						=> 'Вы уже голосовали за этот сайт',
	'DIR_ERROR_VOTE_LOGGED'					=> 'Вы должны войти, чтобы голосовать',
	'DIR_ERROR_WRONG_DATA_BACK'				=> 'Адрес URL на обратные ссылки должен быть правильным, включая протокол. Например: http://www.example.com/.',
	'DIR_FIELDS'							=> 'Пожалуйста, заполните все поля, отмеченные *',
	'DIR_FLAG'								=> 'Флаг',
	'DIR_FLAG_EXP'							=> 'Выберите флаг, которым указывает национальность сайта',
	'DIR_FROM_TEN'							=> '%s/10',
	'DIR_GUEST_EMAIL'						=> 'Ваш email адрес',
	'DIR_MAKE_SEARCH'						=> 'Найти сайт',
	'DIR_NAME_ORDER'						=> 'Название',
	'DIR_NEW_COMMENT_OK'					=> 'Этот комментарий был успешно добавлен',
	'DIR_NEW_SITE'							=> 'Добавление сайт в каталог',
	'DIR_NEW_SITE_ACTIVE'					=> 'Ваш сайт был добавлен, но оно должно быть одобрен перед тем, как появиться в каталоге',
	'DIR_NEW_SITE_OK'						=> 'Ваш сайт был добавлен в каталог',
	'DIR_NB_CLICKS'							=> array(
													1 => '%d переход',
													2 => '%d перехода',
													3 => '%d переходов',
												),
	'DIR_NB_CLICKS_ORDER'					=> 'Переходы',
	'DIR_NB_COMMS'							=> array(
													1 => '%d комментарий',
													2 => '%d комментария',
													3 => '%d комментариев',
												),
	'DIR_NB_LINKS'							=> array(
													1 => '%d сайт',
													2 => '%d сайта',
													3 => '%d сайтов',
												),
	'DIR_NB_VOTES'							=> array(
													1 => '%d голос',
													2 => '%d голоса',
													2 => '%d голосов',
												),
	'DIR_NONE'								=> 'Нет',
	'DIR_NOTE'								=> 'Рейтинг',
	'DIR_NO_COMMENT'						=> 'Нет комментариев для этого сайта',
	'DIR_NO_DRAW_CAT'						=> 'Нет категорий',
	'DIR_NO_DRAW_LINK'						=> 'Нет сайтов в категории',
	'DIR_NO_NOTE'							=> 'Нет',
	'DIR_NOT_WATCHING_CAT'					=> 'Вы больше не подписаны на категорию.',

	'DIR_REPLY_EXP'							=> 'Ваш комментарий не может быть больше %d символов по длине.',
	'DIR_REPLY_TITLE'						=> 'Добавить комментарий',
	'DIR_RSS'								=> 'RSS для',
	'DIR_SEARCH_CATLIST'					=> 'Поиск в определенной категории',
	'DIR_SEARCH_DESC_ONLY'					=> 'Только в описании',
	'DIR_SEARCH_METHOD'						=> 'Способ',
	'DIR_SEARCH_NB_CLICKS'					=> array(
													1 => 'Переход',
													2 => 'Перехода',
													3 => 'Переходов',
												),
	'DIR_SEARCH_NB_COMMS'					=> array(
													1 => 'Комментарий',
													2 => 'Комментария',
													3 => 'Комментариев',
												),
	'DIR_SEARCH_NO_RESULT'					=> 'Нет результатов поиска',
	'DIR_SEARCH_RESULT'						=> 'Результаты поиска',
	'DIR_SEARCH_SUBCATS'					=> 'Поиск в под категориях',
	'DIR_SEARCH_TITLE_DESC'					=> 'В названии и описании',
	'DIR_SEARCH_TITLE_ONLY'					=> 'Только в названии',
	'DIR_SITE_BACK'							=> 'URL обратная ссылка на сайт',
	'DIR_SITE_BACK_EXPLAIN'					=> 'В этой строке, укажите обратную ссылку на ваш сайт. Пожалуйста, укажите URL сайта, на который можно по ссылке.',
	'DIR_SITE_BANN'							=> 'Добавить баннер',
	'DIR_SITE_BANN_EXP'						=> 'Вы должны ввести здесь полный URL вашего баннера. Обратите внимание, что это поле не обязательно. Максимальный допустимый размер <b>%d x %d</b> пикселей, баннер будет автоматически изменен, если допустимый размер превышен.',
	'DIR_SITE_NAME'							=> 'Название сайта',
	'DIR_SITE_RSS'							=> 'RSS канал',
	'DIR_SITE_RSS_EXPLAIN'					=> 'Вы можете добавить адрес RSS канала, если есть один. А RSS иконка будет отображаться рядом с вашим сайтом, позволяя людям по ней подписаться',
	'DIR_SITE_URL'							=> 'URL',
	'DIR_SOMMAIRE'							=> 'Главная каталога',
	'DIR_START_WATCHING_CAT'				=> 'Подписаться на категорию',
	'DIR_STOP_WATCHING_CAT'					=> 'Отписаться от категории',
	'DIR_SUBMIT_TYPE_1'						=> 'Ваш сайт должен быть одобрен администратором.',
	'DIR_SUBMIT_TYPE_2'						=> 'Ваш сайт сразу появляются в каталоге.',
	'DIR_SUBMIT_TYPE_3'						=> 'Вы администратор, ваш сайт будет добавлен автоматически.',
	'DIR_SUBMIT_TYPE_4'						=> 'Вы модератор, ваш сайт будет добавлен автоматически.',
	'DIR_THUMB'								=> 'Миниатюра сайта',
	'DIR_USER_PROP'							=> 'Сайт добавлен',
	'DIR_VOTE'								=> 'Голос',
	'DIR_VOTE_OK'							=> 'Ваш голос был учтен',
	'DIR_POST'								=> 'Сообщение',

	'DIRECTORY_TRANSLATION_INFO'			=> '',

	'RECENT_LINKS'							=> 'Последние сайты',

	'TOO_LONG_BACK'							=> 'Адрес содержащий обратную ссылку слишком длинный (255 символов максимум)',
	'TOO_LONG_DESCRIPTION'					=> 'Ваше описание слишком длинное',
	'TOO_LONG_REPLY'						=> 'Ваш комментарий слишком длинный',
	'TOO_LONG_RSS'							=> 'URL для RSS канала слишком длинный',
	'TOO_LONG_SITE_NAME'					=> 'Имя, которое вы ввели слишком длинное (100 символов максимум)',
	'TOO_LONG_URL'							=> 'URL, который вы ввели слишком длинный (255 символов максимум)',
	'TOO_MANY_ADDS'							=> 'Вы достигли общего количества попыток для добавления сайтов. Попробуйте еще позже.',
	'TOO_SHORT_BACK'						=> 'Вы должны ввести адрес сайта, на которую ведет обратная ссылка.',
	'TOO_SHORT_DESCRIPTION'					=> 'Вы должны ввести описание',
	'TOO_SHORT_REPLY'						=> 'Ваш комментарий слишком короткий',
	'TOO_SHORT_RSS'							=> 'URL для RSS канала слишком длинный',
	'TOO_SHORT_SITE_NAME'					=> 'Вы должны ввести название сайта',
	'TOO_SHORT_URL'							=> 'Вы должны ввести адрес сайта',
	'TOO_SMALL_CAT'							=> 'Вы должны выбрать категорию',

	'WRONG_DATA_RSS'						=> 'Адрес URL на RSS канал должен быть правильным, включая протокол. Например: http://www.example.com/.',
	'WRONG_DATA_WEBSITE'					=> 'Адрес URL на сайт должен быть правильным, включая протокол. Например: http://www.example.com/.',
));

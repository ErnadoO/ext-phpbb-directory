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
	'DIR_ARE_WATCHING_CAT'					=> 'Te has suscrito a ser notificado de un nuevo sitio web en esta categoría.',
	'DIR_BANNER_DISALLOWED_CONTENT'			=> 'La transferencia se ha interrumpido porque el archivo se ha identificado como una amenaza potencial.',
	'DIR_BANNER_DISALLOWED_EXTENSION'		=> 'No se puede mostrar este archivo porque la extensión <strong>%s</strong> no está permitida.',
	'DIR_BANNER_EMPTY_FILEUPLOAD'			=> 'El archivo de banner está vacío.',
	'DIR_BANNER_EMPTY_REMOTE_DATA'			=> 'El banner enviado no puede ser transferido porque los datos parecen incorrectos o corrompidos.',
	'DIR_BANNER_IMAGE_FILETYPE_MISMATCH'	=> 'Incompatibilidad de tipo de archivo de Banner: extensión esperada %1$s pero extensión %2$s dada.',
	'DIR_BANNER_INVALID_FILENAME'			=> '%s es un nombre de archivo no válido.',
	'DIR_BANNER_NOT_UPLOADED'				=> 'El banner no se puede transferir',
	'DIR_BANNER_PARTIAL_UPLOAD'				=> 'El archivo no puede ser totalmente transferido.',
	'DIR_BANNER_PHP_SIZE_NA'				=> 'El tamaño del banner es demasiado grande.<br />No se pudo determinar el tamaño máximo establecido en php.ini.".',
	'DIR_BANNER_PHP_SIZE_OVERRUN'			=> 'El tamaño del banner es demasiado grande. El tamaño máximo permitido es %d Mo.<br />Tenga en cuenta que esta configuración está escrita en php.ini y no puede ser comunicada.',
	'DIR_BANNER_REMOTE_UPLOAD_TIMEOUT'		=> 'No se pudo cargar el banner especificado porque la solicitud se había agotado.',
	'DIR_BANNER_UNABLE_GET_IMAGE_SIZE'		=> 'No fue posible determinar las dimensiones del banner',
	'DIR_BANNER_URL_INVALID'				=> 'La dirección del banner no es válida',
	'DIR_BANNER_URL_NOT_FOUND'				=> 'No se ha encontrado el archivo.',
	'DIR_BANNER_WRONG_FILESIZE'				=> 'El tamaño del banner debe estar entre 0 y %1d %2s.',
	'DIR_BANNER_WRONG_SIZE'					=> 'El banner especificado tiene un ancho de %3$d píxeles y una altura de %4$d píxeles. El banner no puede tener más de %1$d de ancho de píxeles y %2$d de altura.',
	'DIR_BUTTON_NEW_SITE'					=> 'Nuevo enlace',
	'DIR_CAT'								=> 'Categoría',
	'DIR_CAT_NAME'							=> 'Nombre de categoría',
	'DIR_CAT_TITLE'							=> 'Directorio categorías',
	'DIR_CAT_TOOLS'							=> 'Herramientas de la categoría',
	'DIR_CLICK_RETURN_DIR'					=> 'Haga clic %saquí%s para volver al directorio de inicio',
	'DIR_CLICK_RETURN_CAT'					=> 'Haga clic %saquí%s para volver a la categoría',
	'DIR_CLICK_RETURN_COMMENT'				=> 'Haga clic %saquí%s para volver a la página de comentarios',
	'DIR_COMMENTS_ORDER'					=> 'Comentarios',
	'DIR_COMMENT_TITLE'						=> 'Leer / Publicar un comentario',
	'DIR_COMMENT_DELETE'					=> 'Eliminar el comentario',
	'DIR_COMMENT_DELETE_CONFIRM'			=> '¿Seguro que quieres eliminar el comentario?',
	'DIR_COMMENT_DELETE_OK'					=> 'Este comentario se ha eliminado correctamente.',
	'DIR_COMMENT_EDIT'						=> 'Editar el comentario',
	'DIR_DELETE_BANNER'						=> 'Eliminar el banner',
	'DIR_DELETE_OK'							=> 'El sitio web ha sido eliminado',
	'DIR_DELETE_SITE'						=> 'Eliminado el sitio web',
	'DIR_DELETE_SITE_CONFIRM'				=> '¿Seguro que quieres eliminar el sitio web?',
	'DIR_DESCRIPTION'						=> 'Descripción',
	'DIR_DESCRIPTION_EXP'					=> 'Una breve descripción de su sitio web, %d caracteres max.',
	'DIR_DISPLAY_LINKS'						=> 'Mostrar los enlaces anteriores',
	'DIR_EDIT'								=> 'Editar',
	'DIR_EDIT_COMMENT_OK'					=> 'Este comentario ha sido editado correctamente',
	'DIR_EDIT_SITE'							=> 'Editar un sitio web',
	'DIR_EDIT_SITE_ACTIVE'					=> 'Su sitio web ha sido editado pero debe ser aprobado antes de aparecer en el directorio',
	'DIR_EDIT_SITE_OK'						=> 'El sitio web ha sido editado',
	'DIR_ERROR_AUTH_COMM'					=> 'No puedes publicar un comentario',
	'DIR_ERROR_CAT'							=> 'Error al intentar recuperar datos de la categoría actual.',
	'DIR_ERROR_CHECK_URL'					=> 'Esta URL parece inaccesible',
	'DIR_ERROR_COMM_LOGGED'					=> 'Debes registrarte para escribir un comentario',
	'DIR_ERROR_KEYWORD'						=> 'Debe introducir palabras clave para buscar.',
	'DIR_ERROR_NOT_AUTH'					=> 'No puedes hacer esta operación',
	'DIR_ERROR_NOT_FOUND_BACK'				=> 'No se ha encontrado la página especificada para el enlace.',
	'DIR_ERROR_NO_CATS'						=> 'Esta categoría no existe',
	'DIR_ERROR_NO_LINK'						=> 'El sitio web que estás buscando no existe',
	'DIR_ERROR_NO_LINKS'					=> 'Este sitio web no existe',
	'DIR_ERROR_NO_LINK_BACK'				=> 'No se ha encontrado el enlace en la página que ha especificado',
	'DIR_ERROR_SUBMIT_TYPE'					=> 'Tipo de datos incorrecto en la función dir_submit_type',
	'DIR_ERROR_URL'							=> 'Debe introducir una URL correcta',
	'DIR_ERROR_VOTE'						=> 'Ya has votado por este sitio web',
	'DIR_ERROR_VOTE_LOGGED'					=> 'Debes iniciar sesión para votar',
	'DIR_ERROR_WRONG_DATA_BACK'				=> 'La dirección del enlace debe ser una URL válida incluyendo el protocolo. Por ejemplo http://www.example.com/.',
	'DIR_FIELDS'							=> 'Por favor llene todos los campos con un *',
	'DIR_FLAG'								=> 'Bandera',
	'DIR_FLAG_EXP'							=> 'Seleccione una bandera que indique la nacionalidad del sitio',
	'DIR_FROM_TEN'							=> '%s/10',
	'DIR_GUEST_EMAIL'						=> 'Tu dirección de correo electrónico',
	'DIR_MAKE_SEARCH'						=> 'Buscar en un sitio web',
	'DIR_NAME_ORDER'						=> 'Nombre',
	'DIR_NEW_COMMENT_OK'					=> 'Este comentario ha sido publicado correctamente',
	'DIR_NEW_SITE'							=> 'Añadir un sitio web al directorio',
	'DIR_NEW_SITE_ACTIVE'					=> 'Su sitio web ha sido añadido pero debe ser aprobado antes de aparecer en el directorio',
	'DIR_NEW_SITE_OK'						=> 'Tu sitio web ha sido añadido al directorio',
	'DIR_NB_CLICKS'							=> array(
													1 => '%d clic',
													2 => '%d clics',
												),
	'DIR_NB_CLICKS_ORDER'					=> 'Clicks',
	'DIR_NB_COMMS'							=> array(
													1 => '%d comentario',
													2 => '%d comentarios',
												),
	'DIR_NB_LINKS'							=> array(
													1 => '%d enlace',
													2 => '%d enlaces',
												),
	'DIR_NB_VOTES'							=> array(
													1 => '%d voto',
													2 => '%d votos',
												),
	'DIR_NONE'								=> 'Ninguno',
	'DIR_NOTE'								=> 'Notación',
	'DIR_NO_COMMENT'						=> 'No hay comentarios para este sitio web',
	'DIR_NO_DRAW_CAT'						=> 'No hay categoría',
	'DIR_NO_DRAW_LINK'						=> 'No hay sitio web en la categoría',
	'DIR_NO_NOTE'							=> 'Ninguno',
	'DIR_NOT_WATCHING_CAT'					=> 'Ya no estás suscrito a esta categoría.',

	'DIR_REPLY_EXP'							=> 'Tu comentario no puede tener más de %d caracteres de longitud.',
	'DIR_REPLY_TITLE'						=> 'Publicar un comentario',
	'DIR_RSS'								=> 'De RSS',
	'DIR_SEARCH_CATLIST'					=> 'Buscar en una categoría específica',
	'DIR_SEARCH_DESC_ONLY'					=> 'Sólo descripción',
	'DIR_SEARCH_METHOD'						=> 'Método',
	'DIR_SEARCH_NB_CLICKS'					=> array(
													1 => 'Clic',
													2 => 'Clics',
												),
	'DIR_SEARCH_NB_COMMS'					=> array(
													1 => 'Comentario',
													2 => 'Comentarios',
												),
	'DIR_SEARCH_NO_RESULT'					=> 'No hay resultado para la investigación',
	'DIR_SEARCH_RESULT'						=> 'Resultados de la búsqueda',
	'DIR_SEARCH_SUBCATS'					=> 'Buscar subcategorías',
	'DIR_SEARCH_TITLE_DESC'					=> 'Nombre y descripción',
	'DIR_SEARCH_TITLE_ONLY'					=> 'Sólo nombre',
	'DIR_SITE_BACK'							=> 'URL de la página del enlace anterior',
	'DIR_SITE_BACK_EXPLAIN'					=> 'En esta categoría se le pide que el propietario del sitio web agregue un enlace. Por favor especifique la URL de la página donde podemos encontrar el enlace',
	'DIR_SITE_BANN'							=> 'Añadir un banner ',
	'DIR_SITE_BANN_EXP'						=> 'Debe ingresar aquí la URL completa de su banner. Tenga en cuenta que este campo no es obligatorio. El tamaño máximo permitido es <b>%d x %d</b> píxeles, el banner se redimensionará automáticamente si se alcanza el tamaño.',
	'DIR_SITE_NAME'							=> 'Nombre del sitio web',
	'DIR_SITE_RSS'							=> 'Fuentes RSS',
	'DIR_SITE_RSS_EXPLAIN'					=> 'Puede agregar la dirección de los canales RSS si hay uno. Se mostrará un icono RSS junto a su sitio web, permitiendo a las personas suscribirse a él',
	'DIR_SITE_URL'							=> 'URL',
	'DIR_SOMMAIRE'							=> 'Inicio del Directorio',
	'DIR_START_WATCHING_CAT'				=> 'Suscribir categoría',
	'DIR_STOP_WATCHING_CAT'					=> 'Descartar categoría',
	'DIR_SUBMIT_TYPE_1'						=> 'Su sitio web debe ser aprobado por un adminsitrador.',
	'DIR_SUBMIT_TYPE_2'						=> 'Tu sitio web aparecerá inmediatamente en el directorio.',
	'DIR_SUBMIT_TYPE_3'						=> 'Usted es administrador su sitio web se agregará automáticamente.',
	'DIR_SUBMIT_TYPE_4'						=> 'Usted es moderador su sitio web se agregará automáticamente.',
	'DIR_THUMB'								=> 'Miniatura del sitio web',
	'DIR_USER_PROP'							=> 'Sitio web enviado por',
	'DIR_VOTE'								=> 'Voto',
	'DIR_VOTE_OK'							=> 'Su voto ha sido enviado',
	'DIR_POST'								=> 'Publicar',

	'DIRECTORY_TRANSLATION_INFO'			=> '',

	'RECENT_LINKS'							=> 'Últimos sitios web agregados',

	'TOO_LONG_BACK'							=> 'La dirección que contiene el enlace es demasiado larga (255 caracteres como máximo)',
	'TOO_LONG_DESCRIPTION'					=> 'Tu descripción es demasiado larga',
	'TOO_LONG_REPLY'						=> 'Tu comentario es demasiado largo',
	'TOO_LONG_RSS'							=> 'La URL de los canales RSS es demasiado larga',
	'TOO_LONG_SITE_NAME'					=> 'El nombre que has ingresado es demasiado largo (máximo 100 caracteres)',
	'TOO_LONG_URL'							=> 'La URL que has ingresado es demasiado larga (255 caracteres máx.)',
	'TOO_MANY_ADDS'							=> 'Has alcanzado el número total de intentos de envío de un sitio web. Inténtalo de nuevo más tarde.',
	'TOO_SHORT_BACK'						=> 'Debe ingresar la dirección de la página donde está el vínculo de retroceso.',
	'TOO_SHORT_DESCRIPTION'					=> 'Debe introducir una descripción',
	'TOO_SHORT_REPLY'						=> 'Tu comentario es demasiado corto',
	'TOO_SHORT_RSS'							=> 'La URL de los canales RSS es demasiado corta',
	'TOO_SHORT_SITE_NAME'					=> 'Debe introducir un nombre para el sitio web',
	'TOO_SHORT_URL'							=> 'Debe introducir una dirección para el sitio web',
	'TOO_SMALL_CAT'							=> 'Debes seleccionar una categoría',

	'WRONG_DATA_RSS'						=> 'Los feeds RSS deben ser una URL válida incluyendo el protocolo. Por ejemplo http://www.example.com/.',
	'WRONG_DATA_WEBSITE'					=> 'La dirección del sitio web tiene que ser una URL válida incluyendo el protocolo. Por ejemplo http://www.example.com/.',
));

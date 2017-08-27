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
	'ACL_CAT_DIRECTORY'			=> 'Directorio',

	'ACL_M_DELETE_DIR'			=> 'Puede eliminar un sitio web',
	'ACL_M_DELETE_COMMENT_DIR'	=> 'Puede eliminar comentarios',
	'ACL_M_EDIT_DIR'			=> 'Puede editar un sitio web',
	'ACL_M_EDIT_COMMENT_DIR'	=> 'Puede editar comentarios',
	'ACL_U_COMMENT_DIR'			=> 'Puede publicar un comentario (si se permiten comentarios en la categoría)',
	'ACL_U_DELETE_DIR'			=> 'Puede eliminar sus propios enlaces',
	'ACL_U_DELETE_COMMENT_DIR'	=> 'Puede eliminar sus propios comentarios',
	'ACL_U_EDIT_DIR'			=> 'Puede editar sus propios enlaces',
	'ACL_U_EDIT_COMMENT_DIR'	=> 'Puede editar sus propios comentarios',
	'ACL_U_SEARCH_DIR'			=> 'Puede buscar en el directorio',
	'ACL_U_SUBMIT_DIR'			=> 'Puede enviar un sitio web en el directorio',
	'ACL_U_VOTE_DIR'			=> 'Puede votar por un sitio web del directorio',
));

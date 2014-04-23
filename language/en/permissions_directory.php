<?php
/**
 *
 * permissions_directory [English]
 *
 * @package language
 * @version $Id$
 * @copyright (c) 2011 http://www.phpbb-services.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
	'ACL_CAT_DIRECTORY'			=> 'Directory',

	'ACL_M_DELETE_DIR'			=> 'Can delete a website',
	'ACL_M_DELETE_COMMENT_DIR'	=> 'Can delete comments',
	'ACL_M_EDIT_DIR'			=> 'Can edit a website',
	'ACL_M_EDIT_COMMENT_DIR'	=> 'Can edit comments',
	'ACL_U_COMMENT_DIR'			=> 'Can post a comment (if comments are allowed in the category)',
	'ACL_U_DELETE_DIR'			=> 'Can delete own links',
	'ACL_U_DELETE_COMMENT_DIR'	=> 'Can delete own comments',
	'ACL_U_EDIT_DIR'			=> 'Can edit own links',
	'ACL_U_EDIT_COMMENT_DIR'	=> 'Can edit own comments',
	'ACL_U_SEARCH_DIR'			=> 'Can search in the directory',
	'ACL_U_SUBMIT_DIR'			=> 'Can submit a website in the directory',
	'ACL_U_VOTE_DIR'			=> 'Can vote for a website of the directory',
));

<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\core;

/**
 * comment class
 */
class comment
{
	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface	$db		Database object
	* @param \phpbb\user 						$user	User object
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\user $user)
	{
		$this->db	= $db;
		$this->user = $user;
	}

	/**
	* Add a comment
	*
	* @param	array	$data	Link's data from db
	* @return	null
	*/
	public function add($data)
	{
		$this->db->sql_transaction('begin');

		$sql = 'INSERT INTO ' . DIR_COMMENT_TABLE . ' ' . $this->db->sql_build_array('INSERT', $data);
		$this->db->sql_query($sql);

		$sql = 'UPDATE ' . DIR_LINK_TABLE . '
			SET link_comment = link_comment + 1
			WHERE link_id = ' . (int) $data['comment_link_id'];
		$this->db->sql_query($sql);

		$this->db->sql_transaction('commit');
	}

	/**
	* Edit a comment
	*
	* @param	array	$data		Data to edit
	* @param	int		$comment_id	The comment ID
	* @return	null
	*/
	public function edit($data, $comment_id)
	{
		$sql = 'UPDATE ' . DIR_COMMENT_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $data) . '
			WHERE comment_id = ' . (int) $comment_id;
		$this->db->sql_query($sql);
	}

	/**
	* Delete a comment
	*
	* @param	string	$link_id	The link ID
	* @param	string	$comment_id	The comment ID
	* @return	null
	*/
	public function del($link_id, $comment_id)
	{
		global $request;

		$this->db->sql_transaction('begin');

		$sql = 'DELETE FROM ' . DIR_COMMENT_TABLE . ' WHERE comment_id = ' . (int) $comment_id;
		$this->db->sql_query($sql);

		$sql = 'UPDATE ' . DIR_LINK_TABLE . '
			SET link_comment = link_comment - 1
			WHERE link_id = ' . (int) $link_id;
		$this->db->sql_query($sql);

		$this->db->sql_transaction('commit');

		if ($request->is_ajax())
		{
			$sql = 'SELECT COUNT(comment_id) AS nb_comments
				FROM ' . DIR_COMMENT_TABLE . '
				WHERE comment_link_id = ' . (int) $link_id;
			$result = $this->db->sql_query($sql);
			$nb_comments = (int) $this->db->sql_fetchfield('nb_comments');
			$this->db->sql_freeresult($result);

			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success' => true,

				'MESSAGE_TITLE'		=> $this->user->lang['INFORMATION'],
				'MESSAGE_TEXT'		=> $this->user->lang['DIR_COMMENT_DELETE_OK'],
				'COMMENT_ID'		=> $comment_id,
				'TOTAL_COMMENTS'	=> $this->user->lang('DIR_NB_COMMS', $nb_comments),
			));
		}
	}
}

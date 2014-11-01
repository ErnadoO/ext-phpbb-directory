<?php
/**
 *
 * @package phpBB Directory
 * @copyright (c) 2014 ErnadoO
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace ernadoo\phpbbdirectory\core;

/**
 * comment class
 * @package phpBB3
 */
class comment
{
	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface	$db	Database object
	 */
	function __construct(\phpbb\db\driver\driver_interface $db)
	{
		$this->db = $db;
	}

	/**
	 * Add a comment
	 *
	 * @param array $data is link's data from db
	 */
	function add($data)
	{
		$this->db->sql_transaction('begin');

		$sql = 'INSERT INTO ' . DIR_COMMENT_TABLE . ' ' . $this->db->sql_build_array('INSERT', $data);
		$this->db->sql_query($sql);

		$sql = 'UPDATE ' . DIR_LINK_TABLE . ' SET link_comment = link_comment + 1 WHERE link_id = ' . (int)$data['comment_link_id'];
		$this->db->sql_query($sql);

		$this->db->sql_transaction('commit');
	}

	/**
	 * Edit a comment
	 *
	 * @param array $data is datas to edit
	 * @param $id comment_id from db
	 *
	 */
	function edit($data, $comment_id)
	{
		$sql = 'UPDATE ' . DIR_COMMENT_TABLE . ' SET ' . $this->db->sql_build_array('UPDATE', $data) . ' WHERE comment_id = ' . (int)$comment_id;
		$this->db->sql_query($sql);
	}

	/**
	 * Delete a comment
	 *
	 * @param string $id is comment_id from db
	 * @param string $u is link_db
	 */
	function del($link_id, $comment_id)
	{
		global $request, $user;

		$this->db->sql_transaction('begin');

		$requete = 'DELETE FROM ' . DIR_COMMENT_TABLE . ' WHERE comment_id = ' . (int)$comment_id;
		$this->db->sql_query($requete);

		$sql = 'UPDATE ' . DIR_LINK_TABLE . ' SET link_comment = link_comment - 1 WHERE link_id = ' . (int)$link_id;
		$this->db->sql_query($sql);

		$this->db->sql_transaction('commit');

		if ($request->is_ajax())
		{
			$sql = 'SELECT COUNT(comment_id) AS nb_comments
				FROM ' . DIR_COMMENT_TABLE . '
				WHERE comment_link_id = ' . (int)$link_id;
			$result = $this->db->sql_query($sql);
			$nb_comments = (int) $this->db->sql_fetchfield('nb_comments');
			$this->db->sql_freeresult($result);

			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success' => true,

				'MESSAGE_TITLE'		=> $user->lang['INFORMATION'],
				'MESSAGE_TEXT'		=> $user->lang['DIR_COMMENT_DELETE_OK'],
				'COMMENT_ID'		=> $comment_id,
				'TOTAL_COMMENTS'	=> $user->lang('DIR_NB_COMMS', $nb_comments),
			));
		}
	}
}
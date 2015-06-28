<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\controller\acp;

class main
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \ernadoo\phpbbdirectory\core\helper */
	protected $dir_helper;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface 		$db			Database object
	* @param \phpbb\request\request					$request	Request object
	* @param \phpbb\template\template				$template	Template object
	* @param \phpbb\user							$user		User object
	* @param \ernadoo\phpbbdirectory\core\helper	$dir_helper	PhpBB Directory extension helper object
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \ernadoo\phpbbdirectory\core\helper $dir_helper)
	{
		$this->db			= $db;
		$this->template		= $template;
		$this->user			= $user;
		$this->request		= $request;
		$this->dir_helper	= $dir_helper;
	}

	/**
	* Display confirm box
	*
	* @param	string $action Requested action
	* @return	null
	*/
	public function display_confirm($action)
	{
		switch ($action)
		{
			case 'votes':
				$confirm = true;
				$confirm_lang = 'DIR_RESET_VOTES_CONFIRM';
				break;

			case 'comments':
				$confirm = true;
				$confirm_lang = 'DIR_RESET_COMMENTS_CONFIRM';
				break;

			case 'clicks':
				$confirm = true;
				$confirm_lang = 'DIR_RESET_CLICKS_CONFIRM';
				break;

			case 'orphans':
				$confirm = true;
				$confirm_lang = 'DIR_DELETE_ORPHANS';
				break;

			default:
				$confirm = false;
		}

		if ($confirm)
		{
			confirm_box(false, $this->user->lang[$confirm_lang], build_hidden_fields(array(
				'action'	=> $action,
			)));
		}
	}

	/**
	* Display phpBB Directory statistics
	*
	* @return null
	*/
	public function display_stats()
	{
		// Count number of categories
		$sql = 'SELECT COUNT(cat_id) AS nb_cats
			FROM ' . DIR_CAT_TABLE;
		$result = $this->db->sql_query($sql);
		$total_cats = (int) $this->db->sql_fetchfield('nb_cats');
		$this->db->sql_freeresult($result);

		// Cont number of links
		$sql = 'SELECT link_id, link_active
			FROM ' . DIR_LINK_TABLE;
		$result = $this->db->sql_query($sql);
		$total_links = $waiting_links = 0;
		while ($row = $this->db->sql_fetchrow($result))
		{
			$total_links++;

			if (!$row['link_active'])
			{
				$waiting_links++;
			}
		}
		$this->db->sql_freeresult($result);

		// Comments number calculating
		$sql = 'SELECT COUNT(comment_id) AS nb_comments
			FROM ' . DIR_COMMENT_TABLE;
		$result = $this->db->sql_query($sql);
		$total_comments = (int) $this->db->sql_fetchfield('nb_comments');
		$this->db->sql_freeresult($result);

		// Votes number calculating
		$sql = 'SELECT COUNT(vote_id) AS nb_votes
			FROM ' . DIR_VOTE_TABLE;
		$result = $this->db->sql_query($sql);
		$total_votes = (int) $this->db->sql_fetchfield('nb_votes');
		$this->db->sql_freeresult($result);

		// Click number calculating
		$sql = 'SELECT SUM(link_view) AS nb_clicks
			FROM ' . DIR_LINK_TABLE;
		$result = $this->db->sql_query($sql);
		$total_clicks = (int) $this->db->sql_fetchfield('nb_clicks');
		$this->db->sql_freeresult($result);

		$banners_dir_size = 0;

		$banners_path = $this->dir_helper->get_banner_path();

		if ($banners_dir = @opendir($banners_path))
		{
			while (($file = readdir($banners_dir)) !== false)
			{
				if ($file[0] != '.' && $file[0] != '..' && strpos($file, 'index.') === false && strpos($file, '.db') === false)
				{
					$banners_dir_size += filesize($banners_path . $file);
				}
			}
			closedir($banners_dir);

			$banners_dir_size = get_formatted_filesize($banners_dir_size);
		}
		else
		{
			// Couldn't open banners dir.
			$banners_dir_size = $this->user->lang['NOT_AVAILABLE'];
		}

		$total_orphan = $this->_orphan_files();

		$this->template->assign_vars(array(
			'U_ACTION'			=> $this->u_action,

			'TOTAL_CATS'		=> $total_cats,
			'TOTAL_LINKS'		=> $total_links-$waiting_links,
			'WAITING_LINKS'		=> $waiting_links,
			'TOTAL_COMMENTS'	=> $total_comments,
			'TOTAL_VOTES'		=> $total_votes,
			'TOTAL_CLICKS'		=> $total_clicks,
			'TOTAL_ORPHANS'		=> $total_orphan,
			'BANNERS_DIR_SIZE'	=> $banners_dir_size,
		));
	}

	/**
	* Execute action requested
	*
	* @param	string $action Requested action
	* @return	null
	*/
	public function exec_action($action)
	{
		switch ($action)
		{
			case 'votes':
				switch ($this->db->get_sql_layer())
				{
					case 'sqlite':
					case 'firebird':
						$this->db->sql_query('DELETE FROM ' . DIR_VOTE_TABLE);
					break;

					default:
						$this->db->sql_query('TRUNCATE TABLE ' . DIR_VOTE_TABLE);
					break;
				}

				$sql = 'UPDATE ' . DIR_LINK_TABLE . '
					SET link_vote = 0, link_note = 0';
				$this->db->sql_query($sql);

				if ($this->request->is_ajax())
				{
					trigger_error('DIR_RESET_VOTES_SUCCESS');
				}
			break;

			case 'comments':
				switch ($this->db->get_sql_layer())
				{
					case 'sqlite':
					case 'firebird':
						$this->db->sql_query('DELETE FROM ' . DIR_COMMENT_TABLE);
					break;

					default:
						$this->db->sql_query('TRUNCATE TABLE ' . DIR_COMMENT_TABLE);
					break;
				}

				$sql = 'UPDATE ' . DIR_LINK_TABLE . '
					SET link_comment = 0';
				$this->db->sql_query($sql);

				if ($this->request->is_ajax())
				{
					trigger_error('DIR_RESET_COMMENTS_SUCCESS');
				}

				break;

			case 'clicks':
				$sql = 'UPDATE ' . DIR_LINK_TABLE . '
					SET link_view = 0';
				$this->db->sql_query($sql);

				if ($this->request->is_ajax())
				{
					trigger_error('DIR_RESET_CLICKS_SUCCESS');
				}
			break;

			case 'orphans':
				$this->_orphan_files(true);

				if ($this->request->is_ajax())
				{
					trigger_error('DIR_DELETE_ORPHANS_SUCCESS');
				}
			break;
		}
	}

	/**
	* Set page url
	*
	* @param	string $u_action Custom form action
	* @return	null
	* @access	public
	*/
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}

	/**
	* Get orphan banners
	*
	* @param	bool		$delete	True if we want to delete banners, else false
	* @return	null|int	Number of orphan files, else null
	*/
	private function _orphan_files($delete = false)
	{
		$banner_path = $this->dir_helper->get_banner_path();
		$imglist = filelist($banner_path);
		$physical_files = $logical_files = $orphan_files = array();

		if (!empty($imglist['']))
		{
			$imglist = array_values($imglist);
			$imglist = $imglist[0];

			foreach ($imglist as $img)
			{
				$physical_files[] = $img;
			}
			$sql = 'SELECT link_banner FROM ' . DIR_LINK_TABLE . '
				WHERE link_banner <> \'\'';
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				if (!preg_match('/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|www\.).+/si', $row['link_banner']))
				{
					$logical_files[] = basename($row['link_banner']);
				}
			}
			$this->db->sql_freeresult($result);

			$orphan_files = array_diff($physical_files, $logical_files);
		}

		if (!$delete)
		{
			return sizeof($orphan_files);
		}

		$dh = @opendir($banner_path);
		while (($file = readdir($dh)) !== false)
		{
			if (in_array($file, $orphan_files))
			{
				@unlink($this->dir_helper->get_banner_path($file));
			}
		}
	}
}

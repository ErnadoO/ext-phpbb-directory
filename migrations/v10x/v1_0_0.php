<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\migrations\v10x;

class v1_0_0 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
				'\ernadoo\phpbbdirectory\migrations\converter\convert_module',
		);
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'directory_cats' => array(
					'COLUMNS' => array(
						'cat_id'				=> array('UINT', null, 'auto_increment'),
						'parent_id'				=> array('UINT', 0),
						'left_id'				=> array('UINT', 0),
						'right_id'				=> array('UINT', 0),
						'cat_parents'			=> array('MTEXT_UNI', ''),
						'cat_name'				=> array('VCHAR', ''),
						'cat_desc'				=> array('TEXT_UNI', ''),
						'cat_desc_bitfield'		=> array('VCHAR', ''),
						'cat_desc_options'		=> array('TIMESTAMP', 7),
						'cat_desc_uid'			=> array('VCHAR:8', ''),
						'cat_links'				=> array('UINT', 0),
						'cat_icon'				=> array('VCHAR', ''),
						'display_subcat_list'	=> array('BOOL', 1),
						'cat_allow_comments'	=> array('BOOL', 1),
						'cat_allow_votes'		=> array('BOOL', 1),
						'cat_must_describe'		=> array('BOOL', 1),
						'cat_count_all'			=> array('BOOL', 0),
						'cat_validate'			=> array('BOOL', 1),
						'cat_link_back'			=> array('BOOL', 0),
						'cat_cron_enable'		=> array('BOOL', 0),
						'cat_cron_next'			=> array('TIMESTAMP', 0),
						'cat_cron_freq'			=> array('UINT', 7),
						'cat_cron_nb_check'		=> array('UINT', 1),
					),

					'PRIMARY_KEY'	=> array('cat_id'),

					'KEYS'		=> array(
						'l_r_id' => array('INDEX', array('left_id', 'right_id')),
					),
				),

				$this->table_prefix . 'directory_comments' => array(
					'COLUMNS' => array(
						'comment_id' 		=> array('UINT', null, 'auto_increment'),
						'comment_date' 		=> array('TIMESTAMP', 0),
						'comment_link_id' 	=> array('UINT', 0),
						'comment_user_id' 	=> array('UINT', 0),
						'comment_user_ip' 	=> array('VCHAR:40', ''),
						'comment_text' 		=> array('MTEXT_UNI', ''),
						'comment_uid' 		=> array('VCHAR:8', 0),
						'comment_flags' 	=> array('TIMESTAMP', 0),
						'comment_bitfield' 	=> array('VCHAR', ''),
					),

					'PRIMARY_KEY'	=> array('comment_id'),
				),

				$this->table_prefix . 'directory_links' => array(
					'COLUMNS' => array(
						'link_id'			=> array('UINT', null, 'auto_increment'),
						'link_time' 		=> array('TIMESTAMP', 0),
						'link_uid' 			=> array('VCHAR:8', ''),
						'link_flags' 		=> array('TIMESTAMP', 0),
						'link_bitfield' 	=> array('VCHAR', ''),
						'link_url' 			=> array('VCHAR', ''),
						'link_description' 	=> array('MTEXT_UNI', ''),
						'link_view' 		=> array('UINT', 0),
						'link_active' 		=> array('BOOL', 0),
						'link_cat' 			=> array('UINT', 0),
						'link_user_id'		=> array('UINT', 0),
						'link_name' 		=> array('XSTEXT_UNI', ''),
						'link_rss' 			=> array('VCHAR', ''),
						'link_banner' 		=> array('VCHAR', ''),
						'link_back' 		=> array('VCHAR', ''),
						'link_nb_check' 	=> array('TINT:3', 0),
						'link_flag' 		=> array('VCHAR', ''),
						'link_guest_email' 	=> array('XSTEXT_UNI', ''),
						'link_vote' 		=> array('UINT', 0),
						'link_comment' 		=> array('TIMESTAMP', 0),
						'link_note' 		=> array('UINT', 0),
						'link_pagerank' 	=> array('CHAR:2', ''),
						'link_thumb' 		=> array('VCHAR', ''),
					),

					'PRIMARY_KEY'	=> array('link_id'),

					'KEYS'		=> array(
						'link_id'			=> array('UNIQUE', array('link_id')),
						'link_c_a' 			=> array('INDEX', array('link_cat', 'link_active')),
						'link_time' 		=> array('INDEX', array('link_time')),
						'link_u_id' 		=> array('INDEX', array('link_user_id')),
					),
				),

				$this->table_prefix . 'directory_watch' => array(
					'COLUMNS' => array(
						'cat_id'		=> array('UINT', 0),
						'user_id'		=> array('UINT', 0),
						'notify_status'	=> array('UINT', 0),
					),

					'KEYS'		=> array(
						'c_id'		=> array('INDEX', array('cat_id')),
						'u_id'		=> array('INDEX', array('user_id')),
						'n_stat'	=> array('INDEX', array('notify_status')),
					),

				),

				$this->table_prefix . 'directory_votes' => array(
					'COLUMNS' => array(
						'vote_id'			=> array('UINT', null, 'auto_increment'),
						'vote_link_id'		=> array('UINT', 0),
						'vote_user_id'		=> array('UINT', 0),
						'vote_note'			=> array('TINT:2', 0),
					),

					'PRIMARY_KEY'	=> array('vote_id'),

					'KEYS'		=> array(
						'v_l_id'	=> array('INDEX', array('vote_link_id')),
						'v_u_id'	=> array('INDEX', array('vote_user_id')),
					),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'	=> array(
				$this->table_prefix . 'directory_cats',
				$this->table_prefix . 'directory_comments',
				$this->table_prefix . 'directory_links',
				$this->table_prefix . 'directory_votes',
				$this->table_prefix . 'directory_watch',
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('dir_mail', '1')),
			array('config.add', array('dir_activ_flag', '1')),
			array('config.add', array('dir_show', '10')),
			array('config.add', array('dir_default_order', 't d')),
			array('config.add', array('dir_allow_bbcode', '1')),
			array('config.add', array('dir_allow_flash', '1')),
			array('config.add', array('dir_allow_links', '1')),
			array('config.add', array('dir_allow_smilies', '1')),
			array('config.add', array('dir_length_describe', '255')),
			array('config.add', array('dir_activ_banner', '1')),
			array('config.add', array('dir_banner_height', '60')),
			array('config.add', array('dir_banner_width', '480')),
			array('config.add', array('dir_activ_checkurl', '1')),
			array('config.add', array('dir_activ_pagerank', '1')),
			array('config.add', array('dir_activ_thumb', '1')),
			array('config.add', array('dir_activ_thumb_remote', '1')),
			array('config.add', array('dir_visual_confirm', '1')),
			array('config.add', array('dir_visual_confirm_max_attempts', '3')),
			array('config.add', array('dir_length_comments', '256')),
			array('config.add', array('dir_new_time', '7')),
			array('config.add', array('dir_comments_per_page', '10')),
			array('config.add', array('dir_storage_banner', '1')),
			array('config.add', array('dir_banner_filesize', '30000')),
			array('config.add', array('dir_thumb_service', 'http://www.apercite.fr/apercite/120x90/oui/oui/')),
			array('config.add', array('dir_thumb_service_reverse', '0')),
			array('config.add', array('dir_activ_rss', '1')),
			array('config.add', array('dir_recent_block', '1')),
			array('config.add', array('dir_recent_exclude', '1')),
			array('config.add', array('dir_recent_rows', '1')),
			array('config.add', array('dir_recent_columns', '5')),
			array('config.add', array('dir_root_path', './')),
			array('config.add', array('dir_activ_rewrite', '0')),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_DIRECTORY'
			)),

			array('module.add', array(
				'acp',
				'ACP_DIRECTORY',
				array(
					'module_basename'	=> '\ernadoo\phpbbdirectory\acp\phpbbdirectory_module',
					'modes'				=> array('main', 'settings', 'cat', 'val'),
				),
			)),

			array('permission.add', array('u_comment_dir')),
			array('permission.add', array('u_search_dir')),
			array('permission.add', array('u_submit_dir')),
			array('permission.add', array('u_vote_dir')),
			array('permission.add', array('u_edit_comment_dir')),
			array('permission.add', array('u_delete_comment_dir')),
			array('permission.add', array('u_edit_dir')),
			array('permission.add', array('u_delete_dir')),
			array('permission.add', array('m_edit_dir')),
			array('permission.add', array('m_delete_dir')),
			array('permission.add', array('m_edit_comment_dir')),
			array('permission.add', array('m_delete_comment_dir')),

			array('permission.permission_set',
				array('ROLE_USER_FULL',
					array(
						'u_comment_dir',
						'u_search_dir',
						'u_submit_dir',
						'u_vote_dir',
						'u_edit_comment_dir',
						'u_delete_comment_dir',
						'u_edit_dir',
						'u_delete_dir',
					)
				)
			),

			array('permission.permission_set',
				array('ROLE_MOD_FULL',
					array(
						'm_edit_dir',
						'm_delete_dir',
						'm_edit_comment_dir',
						'm_delete_comment_dir',
					)
				)
			),

			array('custom', array(array(&$this, 'create_directories'))),
		);
	}

	public function revert_data()
	{
		return array(
			array('custom', array(array(&$this, 'remove_directories'))),
		);
	}

	/**
	* Create directories for banners/icons uploaded
	*
	* @return null
	*/
	public function create_directories()
	{
		$directories = array(
			'files/ext/ernadoo/phpbbdirectory/banners/',
			'files/ext/ernadoo/phpbbdirectory/icons/',
		);

		foreach ($directories as $dir)
		{
			if (!file_exists($this->phpbb_root_path . $dir))
			{
				@mkdir($this->phpbb_root_path . $dir, 0777, true);
				phpbb_chmod($this->phpbb_root_path . $dir, CHMOD_READ | CHMOD_WRITE);
			}
		}
	}

	/**
	* Remove directories for banners/icons uploaded
	*
	* @return null
	*/
	public function remove_directories()
	{
		$dir = $this->phpbb_root_path . 'files/ext/ernadoo/phpbbdirectory/';

		$this->_recursive_rmdir($dir);
	}

	/**
	* Attempts to remove recursively the directory named by dirname.
	*
	* @author Mehdi Kabab <http://pioupioum.fr>
	* @copyright Copyright (C) 2009 Mehdi Kabab
	* @license http://www.gnu.org/licenses/gpl.html  GNU GPL version 3 or later
	*
	* @param	string	$dirname		Path to the directory.
	* @return	null
	*/
	private function _recursive_rmdir($dirname)
	{
		if (is_dir($dirname) && !is_link($dirname))
		{
			$iterator = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($dirname),
				\RecursiveIteratorIterator::CHILD_FIRST
			);

			while ($iterator->valid())
			{
				if (!$iterator->isDot())
				{
					if ($iterator->isFile())
					{
						unlink($iterator->getPathName());
					}
					else if ($iterator->isDir())
					{
						rmdir($iterator->getPathName());
					}
				}

				$iterator->next();
			}
			unset($iterator); // Fix for Windows.

			rmdir($dirname);
		}
	}
}

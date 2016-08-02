<?php
/**
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*/
namespace ernadoo\phpbbdirectory\migrations\v10x;

class v1_0_0 extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return [
                '\ernadoo\phpbbdirectory\migrations\converter\convert_module',
        ];
    }

    public function update_schema()
    {
        return [
            'add_tables' => [
                $this->table_prefix.'directory_cats' => [
                    'COLUMNS' => [
                        'cat_id'                   => ['UINT', null, 'auto_increment'],
                        'parent_id'                => ['UINT', 0],
                        'left_id'                  => ['UINT', 0],
                        'right_id'                 => ['UINT', 0],
                        'cat_parents'              => ['MTEXT_UNI', ''],
                        'cat_name'                 => ['VCHAR', ''],
                        'cat_desc'                 => ['TEXT_UNI', ''],
                        'cat_desc_bitfield'        => ['VCHAR', ''],
                        'cat_desc_options'         => ['TIMESTAMP', 7],
                        'cat_desc_uid'             => ['VCHAR:8', ''],
                        'cat_links'                => ['UINT', 0],
                        'cat_icon'                 => ['VCHAR', ''],
                        'display_subcat_list'      => ['BOOL', 1],
                        'cat_allow_comments'       => ['BOOL', 1],
                        'cat_allow_votes'          => ['BOOL', 1],
                        'cat_must_describe'        => ['BOOL', 1],
                        'cat_count_all'            => ['BOOL', 0],
                        'cat_validate'             => ['BOOL', 1],
                        'cat_link_back'            => ['BOOL', 0],
                        'cat_cron_enable'          => ['BOOL', 0],
                        'cat_cron_next'            => ['TIMESTAMP', 0],
                        'cat_cron_freq'            => ['UINT', 7],
                        'cat_cron_nb_check'        => ['UINT', 1],
                    ],

                    'PRIMARY_KEY'    => ['cat_id'],

                    'KEYS'        => [
                        'l_r_id' => ['INDEX', ['left_id', 'right_id']],
                    ],
                ],

                $this->table_prefix.'directory_comments' => [
                    'COLUMNS' => [
                        'comment_id'          => ['UINT', null, 'auto_increment'],
                        'comment_date'        => ['TIMESTAMP', 0],
                        'comment_link_id'     => ['UINT', 0],
                        'comment_user_id'     => ['UINT', 0],
                        'comment_user_ip'     => ['VCHAR:40', ''],
                        'comment_text'        => ['MTEXT_UNI', ''],
                        'comment_uid'         => ['VCHAR:8', 0],
                        'comment_flags'       => ['TIMESTAMP', 0],
                        'comment_bitfield'    => ['VCHAR', ''],
                    ],

                    'PRIMARY_KEY'    => ['comment_id'],
                ],

                $this->table_prefix.'directory_links' => [
                    'COLUMNS' => [
                        'link_id'             => ['UINT', null, 'auto_increment'],
                        'link_time'           => ['TIMESTAMP', 0],
                        'link_uid'            => ['VCHAR:8', ''],
                        'link_flags'          => ['TIMESTAMP', 0],
                        'link_bitfield'       => ['VCHAR', ''],
                        'link_url'            => ['VCHAR', ''],
                        'link_description'    => ['MTEXT_UNI', ''],
                        'link_view'           => ['UINT', 0],
                        'link_active'         => ['BOOL', 0],
                        'link_cat'            => ['UINT', 0],
                        'link_user_id'        => ['UINT', 0],
                        'link_name'           => ['XSTEXT_UNI', ''],
                        'link_rss'            => ['VCHAR', ''],
                        'link_banner'         => ['VCHAR', ''],
                        'link_back'           => ['VCHAR', ''],
                        'link_nb_check'       => ['TINT:3', 0],
                        'link_flag'           => ['VCHAR', ''],
                        'link_guest_email'    => ['XSTEXT_UNI', ''],
                        'link_vote'           => ['UINT', 0],
                        'link_comment'        => ['TIMESTAMP', 0],
                        'link_note'           => ['UINT', 0],
                        'link_pagerank'       => ['CHAR:2', ''],
                        'link_thumb'          => ['VCHAR', ''],
                    ],

                    'PRIMARY_KEY'    => ['link_id'],

                    'KEYS'        => [
                        'link_id'             => ['UNIQUE', ['link_id']],
                        'link_c_a'            => ['INDEX', ['link_cat', 'link_active']],
                        'link_time'           => ['INDEX', ['link_time']],
                        'link_u_id'           => ['INDEX', ['link_user_id']],
                    ],
                ],

                $this->table_prefix.'directory_watch' => [
                    'COLUMNS' => [
                        'cat_id'           => ['UINT', 0],
                        'user_id'          => ['UINT', 0],
                        'notify_status'    => ['UINT', 0],
                    ],

                    'KEYS'        => [
                        'c_id'        => ['INDEX', ['cat_id']],
                        'u_id'        => ['INDEX', ['user_id']],
                        'n_stat'      => ['INDEX', ['notify_status']],
                    ],

                ],

                $this->table_prefix.'directory_votes' => [
                    'COLUMNS' => [
                        'vote_id'              => ['UINT', null, 'auto_increment'],
                        'vote_link_id'         => ['UINT', 0],
                        'vote_user_id'         => ['UINT', 0],
                        'vote_note'            => ['TINT:2', 0],
                    ],

                    'PRIMARY_KEY'    => ['vote_id'],

                    'KEYS'        => [
                        'v_l_id'    => ['INDEX', ['vote_link_id']],
                        'v_u_id'    => ['INDEX', ['vote_user_id']],
                    ],
                ],
            ],
        ];
    }

    public function revert_schema()
    {
        return [
            'drop_tables'    => [
                $this->table_prefix.'directory_cats',
                $this->table_prefix.'directory_comments',
                $this->table_prefix.'directory_links',
                $this->table_prefix.'directory_votes',
                $this->table_prefix.'directory_watch',
            ],
        ];
    }

    public function update_data()
    {
        return [
            ['config.add', ['dir_mail', '1']],
            ['config.add', ['dir_activ_flag', '1']],
            ['config.add', ['dir_show', '10']],
            ['config.add', ['dir_default_order', 't d']],
            ['config.add', ['dir_allow_bbcode', '1']],
            ['config.add', ['dir_allow_flash', '1']],
            ['config.add', ['dir_allow_links', '1']],
            ['config.add', ['dir_allow_smilies', '1']],
            ['config.add', ['dir_length_describe', '255']],
            ['config.add', ['dir_activ_banner', '1']],
            ['config.add', ['dir_banner_height', '60']],
            ['config.add', ['dir_banner_width', '480']],
            ['config.add', ['dir_activ_checkurl', '1']],
            ['config.add', ['dir_activ_pagerank', '1']],
            ['config.add', ['dir_activ_thumb', '1']],
            ['config.add', ['dir_activ_thumb_remote', '1']],
            ['config.add', ['dir_visual_confirm', '1']],
            ['config.add', ['dir_visual_confirm_max_attempts', '3']],
            ['config.add', ['dir_length_comments', '256']],
            ['config.add', ['dir_new_time', '7']],
            ['config.add', ['dir_comments_per_page', '10']],
            ['config.add', ['dir_storage_banner', '1']],
            ['config.add', ['dir_banner_filesize', '30000']],
            ['config.add', ['dir_thumb_service', 'http://www.apercite.fr/apercite/120x90/oui/oui/']],
            ['config.add', ['dir_thumb_service_reverse', '0']],
            ['config.add', ['dir_activ_rss', '1']],
            ['config.add', ['dir_recent_block', '1']],
            ['config.add', ['dir_recent_exclude', '1']],
            ['config.add', ['dir_recent_rows', '1']],
            ['config.add', ['dir_recent_columns', '5']],
            ['config.add', ['dir_root_path', './']],
            ['config.add', ['dir_activ_rewrite', '0']],

            ['module.add', [
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_DIRECTORY',
            ]],

            ['module.add', [
                'acp',
                'ACP_DIRECTORY',
                [
                    'module_basename'      => '\ernadoo\phpbbdirectory\acp\phpbbdirectory_module',
                    'modes'                => ['main', 'settings', 'cat', 'val'],
                ],
            ]],

            ['permission.add', ['u_comment_dir']],
            ['permission.add', ['u_search_dir']],
            ['permission.add', ['u_submit_dir']],
            ['permission.add', ['u_vote_dir']],
            ['permission.add', ['u_edit_comment_dir']],
            ['permission.add', ['u_delete_comment_dir']],
            ['permission.add', ['u_edit_dir']],
            ['permission.add', ['u_delete_dir']],
            ['permission.add', ['m_edit_dir']],
            ['permission.add', ['m_delete_dir']],
            ['permission.add', ['m_edit_comment_dir']],
            ['permission.add', ['m_delete_comment_dir']],

            ['permission.permission_set',
                ['ROLE_USER_FULL',
                    [
                        'u_comment_dir',
                        'u_search_dir',
                        'u_submit_dir',
                        'u_vote_dir',
                        'u_edit_comment_dir',
                        'u_delete_comment_dir',
                        'u_edit_dir',
                        'u_delete_dir',
                    ],
                ],
            ],

            ['permission.permission_set',
                ['ROLE_MOD_FULL',
                    [
                        'm_edit_dir',
                        'm_delete_dir',
                        'm_edit_comment_dir',
                        'm_delete_comment_dir',
                    ],
                ],
            ],

            ['custom', [[&$this, 'create_directories']]],
        ];
    }

    public function revert_data()
    {
        return [
            ['custom', [[&$this, 'remove_directories']]],
        ];
    }

    /**
     * Create directories for banners/icons uploaded.
     *
     * @return null
     */
    public function create_directories()
    {
        $directories = [
            'files/ext/ernadoo/phpbbdirectory/banners/',
            'files/ext/ernadoo/phpbbdirectory/icons/',
        ];

        foreach ($directories as $dir) {
            if (!file_exists($this->phpbb_root_path.$dir)) {
                @mkdir($this->phpbb_root_path.$dir, 0777, true);
                phpbb_chmod($this->phpbb_root_path.$dir, CHMOD_READ | CHMOD_WRITE);
            }
        }
    }

    /**
     * Remove directories for banners/icons uploaded.
     *
     * @return null
     */
    public function remove_directories()
    {
        $dir = $this->phpbb_root_path.'files/ext/ernadoo/phpbbdirectory/';

        $this->_recursive_rmdir($dir);
    }

    /**
     * Attempts to remove recursively the directory named by dirname.
     *
     * @author Mehdi Kabab <http://pioupioum.fr>
     * @copyright Copyright (C) 2009 Mehdi Kabab
     * @license http://www.gnu.org/licenses/gpl.html  GNU GPL version 3 or later
     *
     * @param string $dirname Path to the directory.
     *
     * @return null
     */
    private function _recursive_rmdir($dirname)
    {
        if (is_dir($dirname) && !is_link($dirname)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dirname),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            while ($iterator->valid()) {
                if (!$iterator->isDot()) {
                    if ($iterator->isFile()) {
                        unlink($iterator->getPathName());
                    } elseif ($iterator->isDir()) {
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

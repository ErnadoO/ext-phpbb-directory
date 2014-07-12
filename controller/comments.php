<?php
/**
 *
 * @package phpBB Directory
 * @copyright (c) 2014 ErnadoO
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace ernadoo\phpbbdirectory\controller;

class comments
{
	private $captcha;
	private $bbcode_status;
	private $smilies_status;
	private $img_status;
	private $url_status;

	private $s_comment			= array();
	private $s_hidden_fields 	= array();

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var \phpbb\ext\ernadoo\phpbbdirectory\core\categorie */
	protected $categorie;

	/** @var \phpbb\ext\ernadoo\phpbbdirectory\core\comment */
	protected $comment;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\config\config $config
	 * @param \phpbb\template\template $template
	 * @param \phpbb\user $user
	 * @param \phpbb\controller\helper $controller_helper
	 * @param \phpbb\request\request $request
	 * @param \phpbb\auth\auth $auth
	 * @param \phpbb\pagination $pagination
	 * @param string         $root_path   phpBB root path
	 * @param string         $php_ext   phpEx
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\auth\auth $auth, \phpbb\pagination $pagination, $root_path, $php_ext, $categorie, $comment)
	{
		$this->db			= $db;
		$this->config		= $config;
		$this->template		= $template;
		$this->user			= $user;
		$this->helper		= $helper;
		$this->request		= $request;
		$this->auth			= $auth;
		$this->pagination	= $pagination;
		$this->root_path	= $root_path;
		$this->php_ext		= $php_ext;
		$this->categorie	= $categorie;
		$this->comment		= $comment;

		$this->user->add_lang_ext('ernadoo/phpbbdirectory', array('directory', 'help' => 'directory_flags'));
		$user->add_lang(array('ucp', 'posting'));

		$this->template->assign_vars(array(
			'S_PHPBB_DIRECTORY'				=> true,
			'DIRECTORY_TRANSLATION_INFO'	=> (!empty($user->lang['DIRECTORY_TRANSLATION_INFO'])) ? $user->lang['DIRECTORY_TRANSLATION_INFO'] : '',
			'S_SIMPLE_MESSAGE' 				=> true,
		));

		// The CAPTCHA kicks in here. We can't help that the information gets lost on language change.
		if(!$this->user->data['is_registered'] && $this->config['dir_visual_confirm'])
		{
			include($this->root_path . 'includes/captcha/captcha_factory.' . $this->php_ext);
			$this->captcha = \phpbb_captcha_factory::get_instance($this->config['captcha_plugin']);
			$this->captcha->init(CONFIRM_POST);
		}

		// get config for options
		$this->bbcode_status	= $this->config['dir_allow_bbcode'] ? true : false;
		$this->smilies_status	= $this->config['dir_allow_smilies'] ? true : false;
		$this->img_status		= $this->bbcode_status ? true : false;
		$this->url_status		= $this->config['dir_allow_links'] ? true : false;
	}

	public function delete_comment($link_id, $comment_id)
	{
		if($this->_check_comments_enable($link_id) === false)
		{
			return $this->helper->error($this->user->lang['DIR_ERROR_NOT_AUTH'], 410);
		}

		if($this->request->is_set_post('cancel'))
		{
			$redirect = $this->helper->route('phpbbdirectory_comment_view_controller', array('link_id' => (int)$link_id));
			redirect($redirect);
		}

		$sql = 'SELECT * FROM ' . DIR_COMMENT_TABLE . ' WHERE comment_id = ' . $comment_id;
		$result = $this->db->sql_query($sql);
		$value = $this->db->sql_fetchrow($result);

		if (!$this->auth->acl_get('m_delete_comment_dir') && (!$this->auth->acl_get('u_delete_comment_dir') || $this->user->data['user_id'] != $value['comment_user_id']))
		{
			return $this->helper->error($this->user->lang['DIR_ERROR_NOT_AUTH'], 410);
		}

		if (confirm_box(true))
		{
			$this->comment->del($link_id, $comment_id);

			$meta_info = $this->helper->route('phpbbdirectory_comment_view_controller', array('link_id' => (int)$link_id));
			meta_refresh(3, $meta_info);
			$message = $this->user->lang['DIR_COMMENT_DELETE_OK'];
			$message = $message . "<br /><br />" . $this->user->lang('DIR_CLICK_RETURN_COMMENT', '<a href="' . $meta_info . '">', '</a>');
			return $this->helper->error($message, 200);
		}
		else
		{
			confirm_box(false, 'DIR_COMMENT_DELETE');
		}
	}

	public function edit_comment($link_id, $comment_id)
	{
		if($this->_check_comments_enable($link_id) === false)
		{
			return $this->helper->error($this->user->lang['DIR_ERROR_NOT_AUTH'], 410);
		}

		$sql = 'SELECT * FROM ' . DIR_COMMENT_TABLE . ' WHERE comment_id = ' . (int)$comment_id;
		$result = $this->db->sql_query($sql);
		$value = $this->db->sql_fetchrow($result);

		if (!$this->auth->acl_get('m_edit_comment_dir') && (!$this->auth->acl_get('u_edit_comment_dir') || $this->user->data['user_id'] != $value['comment_user_id']))
		{
			return $this->helper->error($this->user->lang['DIR_ERROR_NOT_AUTH'], 410);
		}

		$this->s_comment = generate_text_for_edit($value['comment_text'], $value['comment_uid'], $value['comment_flags']);

		$submit	= $this->request->is_set_post('update_comment') ? true : false;

		// If form is done
		if ($submit)
		{
			return $this->_data_processing($link_id, $comment_id, $mode = 'edit');
		}

		return $this->view($link_id, 1, 'edit');
	}

	public function new_comment($link_id)
	{
		if($this->_check_comments_enable($link_id) === false)
		{
			return $this->helper->error($this->user->lang['DIR_ERROR_NOT_AUTH'], 410);
		}

		if(!$this->auth->acl_get('u_comment_dir'))
		{
			return $this->helper->error($this->user->lang['DIR_ERROR_NOT_AUTH'], 410);
		}

		$submit		= $this->request->is_set_post('submit_comment') ? true : false;
		$refresh	= $this->request->is_set_post('refresh_vc') ? true : false;

		// If form is done
		if ($submit || $refresh)
		{
			return $this->_data_processing($link_id);
		}
		else
		{
			$redirect = $this->helper->route('phpbbdirectory_comment_view_controller', array('link_id' => (int)$link_id));
			redirect($redirect);
		}
	}

	public function view($link_id, $page, $mode = 'new')
	{
		if($this->_check_comments_enable($link_id) === false)
		{
			return $this->helper->error($this->user->lang['DIR_ERROR_NOT_AUTH'], 410);
		}

		$comment_id = $this->request->variable('c', 0);
		$view 		= $this->request->variable('view', '');
		$start 		= ($page - 1) * $this->config['dir_comments_per_page'];

		$this->s_hidden_fields = array_merge($this->s_hidden_fields, array('page' => $page));

		$this->_populate_form($link_id, $mode);

		$sql = 'SELECT COUNT(comment_id) AS nb_comments
			FROM ' . DIR_COMMENT_TABLE . '
			WHERE comment_link_id = ' . (int)$link_id;
		$result = $this->db->sql_query($sql);
		$nb_comments = (int) $this->db->sql_fetchfield('nb_comments');
		$this->db->sql_freeresult($result);

		// Make sure $start is set to the last page if it exceeds the amount
		$start = $this->pagination->validate_start($start, $this->config['dir_comments_per_page'], $nb_comments);

		$sql_array = array(
			'SELECT'	=> 'a.comment_id, a.comment_user_id, a. comment_user_ip, a.comment_date, a.comment_text, a.comment_uid, a.comment_bitfield, a.comment_flags, u.username, u.user_id, u.user_colour, z.foe',
			'FROM'		=> array(
					DIR_COMMENT_TABLE	=> 'a'),
			'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(USERS_TABLE => 'u'),
						'ON'	=> 'a.comment_user_id = u.user_id'
					),
					array(
						'FROM'	=> array(ZEBRA_TABLE => 'z'),
						'ON'	=> 'z.user_id = ' . $this->user->data['user_id'] . ' AND z.zebra_id = a.comment_user_id'
					)
			),
			'WHERE'		=> 'a.comment_link_id = ' . (int)$link_id,
			'ORDER_BY'	=> 'a.comment_date DESC');
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $this->config['dir_comments_per_page'], $start);

		$have_result = false;

		while ($comments = $this->db->sql_fetchrow($result))
		{
			$have_result = true;

			$edit_allowed = ($this->user->data['is_registered'] && ($this->auth->acl_get('m_edit_comment_dir') || (
				$this->user->data['user_id'] == $comments['comment_user_id'] &&
				$this->auth->acl_get('u_edit_comment_dir')
			)));

			$delete_allowed = ($this->user->data['is_registered'] && ($this->auth->acl_get('m_delete_comment_dir') || (
				$this->user->data['user_id'] == $comments['comment_user_id'] &&
				$this->auth->acl_get('u_delete_comment_dir')
			)));

			$this->template->assign_block_vars('comment', array(
				'MINI_POST_IMG'		=> $this->user->img('icon_post_target', 'POST'),
				'S_USER'			=> get_username_string('full', $comments['comment_user_id'], $comments['username'], $comments['user_colour']),
				'S_USER_IP'			=> $comments['comment_user_ip'],
				'S_DATE'			=> $this->user->format_date($comments['comment_date']),
				'S_COMMENT'			=> generate_text_for_display($comments['comment_text'], $comments['comment_uid'], $comments['comment_bitfield'], $comments['comment_flags']),
				'S_ID'				=> $comments['comment_id'],

				'U_EDIT'			=> ($edit_allowed) 		? $this->helper->route('phpbbdirectory_comment_edit_controller', array('link_id' => (int)$link_id, 'comment_id' => (int)$comments['comment_id'])) : '',
				'U_DELETE'			=> ($delete_allowed) 	? $this->helper->route('phpbbdirectory_comment_delete_controller', array('link_id' => (int)$link_id, 'comment_id' => (int)$comments['comment_id'], '_referer' => $this->helper->get_current_url())) : '',

				'S_IGNORE_POST'		=> ($comments['foe'] && ($view != 'show' || $comment_id != $comments['comment_id'])) ? true : false,
				'L_IGNORE_POST'		=> ($comments['foe']) ? $this->user->lang('POST_BY_FOE', get_username_string('full', $comments['comment_user_id'], $comments['username'], $comments['user_colour']), '<a href="'.$this->helper->url('directory/link/'.$link_id.'/comment'.(($page > 1) ? '/'.$page : '').'?view=show#c'.(int)$comments['comment_id']).'">', '</a>') : '',
				'L_POST_DISPLAY'	=> ($comments['foe']) ? $this->user->lang('POST_DISPLAY', '<a class="display_post" data-post-id="' . $comments['comment_id'] . '" href="' . $this->helper->url('directory/link/'.$link_id.'/comment'.(($page > 1) ? '/'.$page : '').'?c='.(int)$comments['comment_id'] . '&view=show#c'.(int)$comments['comment_id']).'">', '</a>') : '',

				'S_INFO'			=> $this->auth->acl_get('m_info'),
			));
		}

		$base_url = array(
			'routes'	=> 'phpbbdirectory_comment_view_controller',
			'params'	=> array('link_id' => (int)$link_id),
		);

		$this->pagination->generate_template_pagination($base_url, 'pagination', 'page', $nb_comments, $this->config['dir_comments_per_page'], $start);

		$this->template->assign_vars( array(
			'TOTAL_COMMENTS'	=> $this->user->lang('DIR_NB_COMMS', (int)$nb_comments),
			'S_HAVE_RESULT'		=> $have_result ? true : false,
		));

		return $this->helper->render('comments.html', $this->user->lang['DIR_COMMENT_TITLE']);
	}

	private function _data_processing($link_id, $comment_id = 0, $mode = 'new')
	{
		if (!check_form_key('dir_form_comment'))
		{
			return $this->helper->error($this->user->lang['FORM_INVALID']);
		}

		$reply = $this->request->variable('message', '', true);
		include($this->root_path . 'includes/functions_user.' . $this->php_ext);

		$error = validate_data(
			array(
				'reply' => $reply),
			array(
				'reply' => array(
					array('string', false, 1, $this->config['dir_length_comments'])
				)
			)
		);

		$error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$this->user->lang['\\1'])) ? \$this->user->lang['\\1'] : '\\1'", $error);

		if (!$this->user->data['is_registered'] && $this->config['dir_visual_confirm'])
		{
			$vc_response = $this->captcha->validate();
			if ($vc_response !== false)
			{
				$error[] = $vc_response;
			}

			if ($this->config['dir_visual_confirm_max_attempts'] && $this->captcha->get_attempt_count() > $this->config['dir_visual_confirm_max_attempts'])
			{
				$error[] = $user->lang['TOO_MANY_ADDS'];
			}
		}

		if(!$error)
		{
			$poll = $uid = $bitfield = '';
			$flags = (($this->bbcode_status) ? OPTION_FLAG_BBCODE : 0) + (($this->smilies_status) ? OPTION_FLAG_SMILIES : 0) + (($this->url_status) ? OPTION_FLAG_LINKS : 0);
			generate_text_for_storage($reply, $uid, $bitfield, $flags, $this->config['dir_allow_bbcode'], $this->config['dir_allow_links'], $this->config['dir_allow_smilies']);

			$data_edit = array(
				'comment_text'		=> $reply,
				'comment_uid'		=> $uid,
				'comment_flags'		=> $flags,
				'comment_bitfield'	=> $bitfield,
			);

			if ($mode == 'edit')
			{
				$this->comment->edit($data_edit, $comment_id);
			}
			else
			{
				$data_add = array(
					'comment_link_id'	=> (int)$link_id,
					'comment_date'		=> time(),
					'comment_user_id'	=> $this->user->data['user_id'],
					'comment_user_ip'	=> $this->user->ip,
				);

				$data_add = array_merge($data_edit, $data_add);

				$this->comment->add($data_add);
			}

			$meta_info = $this->helper->route('phpbbdirectory_comment_view_controller', array('link_id' => (int)$link_id));
			meta_refresh(3, $meta_info);
			$message = $this->user->lang['DIR_'.strtoupper($mode).'_COMMENT_OK'];
			$message = $message . "<br /><br />" . $this->user->lang('DIR_CLICK_RETURN_COMMENT', '<a href="' . $meta_info . '">', '</a>');
			return $this->helper->error($message, 200);
		}
		else
		{
			$this->template->assign_vars(array(
				'ERROR'	=> (sizeof($error)) ? implode('<br />', $error) : ''
			));

			return $this->view($link_id, $this->request->variable('page', 0), $mode);
		}
	}

	private function _check_comments_enable($link_id)
	{
		$sql = 'SELECT link_cat
			FROM ' . DIR_LINK_TABLE . '
				WHERE link_id = ' . (int)$link_id;
		$result = $this->db->sql_query($sql);
		$cat_id = (int) $this->db->sql_fetchfield('link_cat');
		$this->db->sql_freeresult($result);

		if(!$cat_id)
		{
			return false;
		}

		$this->categorie->get($cat_id);

		if(!$this->categorie->data['cat_allow_comments'])
		{
			return false;
		}
	}

	private function _populate_form($link_id, $mode)
	{
		if (!$this->user->data['is_registered'] && $this->config['dir_visual_confirm'] && $mode != 'edit')
		{
			$this->s_hidden_fields = array_merge($this->s_hidden_fields, $this->captcha->get_hidden_fields());

			$this->template->assign_vars(array(
				'S_CONFIRM_CODE'		=> true,
				'CAPTCHA_TEMPLATE'		=> $this->captcha->get_template(),
			));
		}

		if(!function_exists('generate_smilies'))
		{
			include($this->root_path . 'includes/functions_posting.' . $this->php_ext);
		}
		if(!function_exists('display_custom_bbcodes'))
		{
			include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		}

		generate_smilies('inline', 0);
		display_custom_bbcodes();
		add_form_key('dir_form_comment');

		$this->template->assign_vars( array(
			'S_AUTH_COMM' 		=> $this->auth->acl_get('u_comment_dir'),

			'BBCODE_STATUS'		=> ($this->bbcode_status) 	? $this->user->lang('BBCODE_IS_ON', '<a href="' . append_sid($this->root_path."faq.$this->php_ext", 'mode=bbcode') . '">', '</a>') : $this->user->lang('BBCODE_IS_OFF', '<a href="' . append_sid($this->root_path."faq.$this->php_ext", 'mode=bbcode') . '">', '</a>'),
			'IMG_STATUS'		=> ($this->img_status) 		? $this->user->lang['IMAGES_ARE_ON'] : $this->user->lang['IMAGES_ARE_OFF'],
			'SMILIES_STATUS'	=> ($this->smilies_status)	? $this->user->lang['SMILIES_ARE_ON'] : $this->user->lang['SMILIES_ARE_OFF'],
			'URL_STATUS'		=> ($this->bbcode_status && $this->url_status) ? $this->user->lang['URL_IS_ON'] : $this->user->lang['URL_IS_OFF'],

			'L_DIR_REPLY_EXP'	=> $this->user->lang('DIR_REPLY_EXP', $this->config['dir_length_comments']),

			'S_COMMENT' 		=> isset($this->s_comment['text']) ? $this->s_comment['text'] : '',
			'S_BBCODE_ALLOWED' 	=> $this->bbcode_status,
			'S_SMILIES_ALLOWED' => $this->smilies_status,
			'S_HIDDEN_FIELDS'	=> build_hidden_fields($this->s_hidden_fields),
			'S_BUTTON_NAME'		=> ($mode == 'edit') ? 'update_comment' : 'submit_comment',
			'S_POST_ACTION' 	=> ($mode == 'edit') ? '' : $this->helper->route('phpbbdirectory_comment_new_controller', array('link_id' => (int)$link_id)),
		));
	}
}

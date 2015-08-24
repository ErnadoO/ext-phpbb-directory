<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\controller;

class comments
{
	private $captcha;
	private $s_comment;
	private $s_hidden_fields = array();

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

	/** @var \phpbb\captcha\factory */
	protected $captcha_factory;

	/** @var \ernadoo\phpbbdirectory\core\categorie */
	protected $categorie;

	/** @var \ernadoo\phpbbdirectory\core\comment */
	protected $comment;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface					$db					Database object
	* @param \phpbb\config\config								$config				Config object
	* @param \phpbb\template\template							$template			Template object
	* @param \phpbb\user										$user				User object
	* @param \phpbb\controller\helper							$helper				Controller helper object
	* @param \phpbb\request\request								$request			Request object
	* @param \phpbb\auth\auth									$auth				Auth object
	* @param \phpbb\pagination									$pagination			Pagination object
	* @param \phpbb\captcha\factory								$captcha_factory	Captcha object
	* @param \ernadoo\phpbbdirectory\core\categorie				$categorie			PhpBB Directory extension categorie object
	* @param \ernadoo\phpbbdirectory\core\comment				$comment			PhpBB Directory extension comment object
	* @param string												$root_path			phpBB root path
	* @param string												$php_ext			phpEx
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\auth\auth $auth, \phpbb\pagination $pagination, \phpbb\captcha\factory $captcha_factory, \ernadoo\phpbbdirectory\core\categorie $categorie, \ernadoo\phpbbdirectory\core\comment $comment, $root_path, $php_ext)
	{
		$this->db				= $db;
		$this->config			= $config;
		$this->template			= $template;
		$this->user				= $user;
		$this->helper			= $helper;
		$this->request			= $request;
		$this->auth				= $auth;
		$this->pagination		= $pagination;
		$this->captcha_factory 	= $captcha_factory;
		$this->categorie		= $categorie;
		$this->comment			= $comment;
		$this->root_path		= $root_path;
		$this->php_ext			= $php_ext;

		$this->user->add_lang_ext('ernadoo/phpbbdirectory', 'directory');
		$user->add_lang(array('ucp', 'posting'));

		$this->template->assign_vars(array(
			'S_PHPBB_DIRECTORY'				=> true,
			'DIRECTORY_TRANSLATION_INFO'	=> (!empty($user->lang['DIRECTORY_TRANSLATION_INFO'])) ? $user->lang['DIRECTORY_TRANSLATION_INFO'] : '',
			'S_SIMPLE_MESSAGE' 				=> true,
		));

		// The CAPTCHA kicks in here. We can't help that the information gets lost on language change.
		if (!$this->user->data['is_registered'] && $this->config['dir_visual_confirm'])
		{
			$this->captcha = $this->captcha_factory->get_instance($this->config['captcha_plugin']);
			$this->captcha->init(CONFIRM_POST);
		}
	}

	/**
	* Populate form when an error occurred
	*
	* @param	int		$link_id		The link ID
	* @param	int		$comment_id		The comment ID
	* @return	\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	* @throws	\phpbb\exception\http_exception
	*/
	public function delete_comment($link_id, $comment_id)
	{
		$this->_check_comments_enable($link_id);

		if ($this->request->is_set_post('cancel'))
		{
			$redirect = $this->helper->route('ernadoo_phpbbdirectory_comment_view_controller', array('link_id' => (int) $link_id));
			redirect($redirect);
		}

		$sql = 'SELECT *
			FROM ' . DIR_COMMENT_TABLE . '
			WHERE comment_id = ' . (int) $comment_id;
		$result = $this->db->sql_query($sql);
		$value = $this->db->sql_fetchrow($result);

		if (!$this->user->data['is_registered'] || !$this->auth->acl_get('m_delete_comment_dir') && (!$this->auth->acl_get('u_delete_comment_dir') || $this->user->data['user_id'] != $value['comment_user_id']))
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
		}

		if (confirm_box(true))
		{
			$this->comment->del($link_id, $comment_id);

			$meta_info = $this->helper->route('ernadoo_phpbbdirectory_comment_view_controller', array('link_id' => (int) $link_id));
			meta_refresh(3, $meta_info);
			$message = $this->user->lang['DIR_COMMENT_DELETE_OK'];
			$message = $message . '<br /><br />' . $this->user->lang('DIR_CLICK_RETURN_COMMENT', '<a href="' . $meta_info . '">', '</a>');
			return $this->helper->message($message);
		}
		else
		{
			confirm_box(false, 'DIR_COMMENT_DELETE');
		}
	}

	/**
	* Edit a comment
	*
	* @param	int		$link_id		The category ID
	* @param	int		$comment_id		The comment ID
	* @return	null|\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	* @throws	\phpbb\exception\http_exception
	*/
	public function edit_comment($link_id, $comment_id)
	{
		$this->_check_comments_enable($link_id);

		$sql = 'SELECT *
			FROM ' . DIR_COMMENT_TABLE . '
			WHERE comment_id = ' . (int) $comment_id;
		$result = $this->db->sql_query($sql);
		$value = $this->db->sql_fetchrow($result);

		if (!$this->user->data['is_registered'] || !$this->auth->acl_get('m_edit_comment_dir') && (!$this->auth->acl_get('u_edit_comment_dir') || $this->user->data['user_id'] != $value['comment_user_id']))
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
		}

		$comment_text = generate_text_for_edit($value['comment_text'], $value['comment_uid'], $value['comment_flags']);
		$this->s_comment = $comment_text['text'];

		$submit	= $this->request->is_set_post('update_comment') ? true : false;

		// If form is done
		if ($submit)
		{
			return $this->_data_processing($link_id, $comment_id, 'edit');
		}

		return $this->view($link_id, 1, 'edit');
	}

	/**
	* Post a new comment
	*
	* @param	int	$link_id		The category ID
	* @return	null
	* @throws	\phpbb\exception\http_exception
	*/
	public function new_comment($link_id)
	{
		$this->_check_comments_enable($link_id);

		if (!$this->auth->acl_get('u_comment_dir'))
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
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
			$redirect = $this->helper->route('ernadoo_phpbbdirectory_comment_view_controller', array('link_id' => (int) $link_id));
			redirect($redirect);
		}
	}

	/**
	* Display popup comment
	*
	* @param	int		$link_id		The category ID
	* @param	int		$page			Page number taken from the URL
	* @param	string	$mode			add|edit
	* @return	\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	* @throws	\phpbb\exception\http_exception
	*/
	public function view($link_id, $page, $mode = 'new')
	{
		$this->_check_comments_enable($link_id);

		$comment_id = $this->request->variable('c', 0);
		$view 		= $this->request->variable('view', '');
		$start 		= ($page - 1) * $this->config['dir_comments_per_page'];

		$this->s_hidden_fields = array_merge($this->s_hidden_fields, array('page' => $page));

		$this->_populate_form($link_id, $mode);

		$sql = 'SELECT COUNT(comment_id) AS nb_comments
			FROM ' . DIR_COMMENT_TABLE . '
			WHERE comment_link_id = ' . (int) $link_id;
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
			'WHERE'		=> 'a.comment_link_id = ' . (int) $link_id,
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

				'U_EDIT'			=> ($edit_allowed) 		? $this->helper->route('ernadoo_phpbbdirectory_comment_edit_controller', array('link_id' => (int) $link_id, 'comment_id' => (int) $comments['comment_id'])) : '',
				'U_DELETE'			=> ($delete_allowed) 	? $this->helper->route('ernadoo_phpbbdirectory_comment_delete_controller', array('link_id' => (int) $link_id, 'comment_id' => (int) $comments['comment_id'], '_referer' => $this->helper->get_current_url())) : '',

				'S_IGNORE_POST'		=> ($comments['foe'] && ($view != 'show' || $comment_id != $comments['comment_id'])) ? true : false,
				'L_IGNORE_POST'		=> ($comments['foe']) ? $this->user->lang('POST_BY_FOE', get_username_string('full', $comments['comment_user_id'], $comments['username'], $comments['user_colour']), '<a href="'.$this->helper->url('directory/link/'.$link_id.'/comment'.(($page > 1) ? '/'.$page : '').'?view=show#c'.(int) $comments['comment_id']).'">', '</a>') : '',
				'L_POST_DISPLAY'	=> ($comments['foe']) ? $this->user->lang('POST_DISPLAY', '<a class="display_post" data-post-id="' . $comments['comment_id'] . '" href="' . $this->helper->url('directory/link/'.$link_id.'/comment'.(($page > 1) ? '/'.$page : '').'?c='.(int) $comments['comment_id'] . '&view=show#c'.(int) $comments['comment_id']).'">', '</a>') : '',

				'S_INFO'			=> $this->auth->acl_get('m_info'),
			));
		}

		$base_url = array(
			'routes'	=> 'ernadoo_phpbbdirectory_comment_view_controller',
			'params'	=> array('link_id' => (int) $link_id),
		);

		$this->pagination->generate_template_pagination($base_url, 'pagination', 'page', $nb_comments, $this->config['dir_comments_per_page'], $start);

		$this->template->assign_vars(array(
			'TOTAL_COMMENTS'	=> $this->user->lang('DIR_NB_COMMS', (int) $nb_comments),
			'S_HAVE_RESULT'		=> $have_result ? true : false,
		));

		return $this->helper->render('comments.html', $this->user->lang['DIR_COMMENT_TITLE']);
	}

	/**
	* Routine
	*
	* @param	int		$link_id		The link ID
	* @param	int		$comment_id		The comment ID
	* @param	string	$mode			new|edit
	* @return	\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	*/
	private function _data_processing($link_id, $comment_id = 0, $mode = 'new')
	{
		if (!check_form_key('dir_form_comment'))
		{
			return $this->helper->message('FORM_INVALID');
		}

		$this->s_comment = $this->request->variable('message', '', true);

		if (!function_exists('validate_data'))
		{
			include($this->root_path . 'includes/functions_user.' . $this->php_ext);
		}

		$error = validate_data(
			array(
				'reply' => $this->s_comment),
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
				$error[] = $this->user->lang['TOO_MANY_ADDS'];
			}
		}

		if (!$error)
		{
			$uid = $bitfield = $flags = '';
			generate_text_for_storage($this->s_comment, $uid, $bitfield, $flags, (bool) $this->config['dir_allow_bbcode'], (bool) $this->config['dir_allow_links'], (bool) $this->config['dir_allow_smilies']);

			$data_edit = array(
				'comment_text'		=> $this->s_comment,
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
					'comment_link_id'	=> (int) $link_id,
					'comment_date'		=> time(),
					'comment_user_id'	=> $this->user->data['user_id'],
					'comment_user_ip'	=> $this->user->ip,
				);

				$data_add = array_merge($data_edit, $data_add);

				$this->comment->add($data_add);
			}

			$meta_info = $this->helper->route('ernadoo_phpbbdirectory_comment_view_controller', array('link_id' => (int) $link_id));
			meta_refresh(3, $meta_info);
			$message = $this->user->lang['DIR_'.strtoupper($mode).'_COMMENT_OK'];
			$message = $message . '<br /><br />' . $this->user->lang('DIR_CLICK_RETURN_COMMENT', '<a href="' . $meta_info . '">', '</a>');
			return $this->helper->message($message);
		}
		else
		{
			$this->template->assign_vars(array(
				'ERROR'	=> (sizeof($error)) ? implode('<br />', $error) : ''
			));

			return $this->view($link_id, $this->request->variable('page', 1), $mode);
		}
	}

	/**
	* Check if comments are enable in a category
	*
	* @param	int		$link_id		The link ID
	* @return	null					Retun null if comments are allowed, http_exception if not
	* @throws	\phpbb\exception\http_exception
	*/
	private function _check_comments_enable($link_id)
	{
		$sql = 'SELECT link_cat
			FROM ' . DIR_LINK_TABLE . '
			WHERE link_id = ' . (int) $link_id;
		$result = $this->db->sql_query($sql);
		$cat_id = (int) $this->db->sql_fetchfield('link_cat');
		$this->db->sql_freeresult($result);

		if ($cat_id)
		{
			$this->categorie->get($cat_id);

			if ($this->categorie->data['cat_allow_comments'])
			{
				return;
			}
		}

		throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
	}

	/**
	* Populate form when an error occurred
	*
	* @param	int		$link_id		The link ID
	* @param	string	$mode			add|edit
	* @return	null
	*/
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

		if (!function_exists('generate_smilies'))
		{
			include($this->root_path . 'includes/functions_posting.' . $this->php_ext);
		}
		if (!function_exists('display_custom_bbcodes'))
		{
			include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		}

		generate_smilies('inline', 0);
		display_custom_bbcodes();
		add_form_key('dir_form_comment');

		$this->template->assign_vars(array(
			'S_AUTH_COMM' 		=> $this->auth->acl_get('u_comment_dir'),

			'BBCODE_STATUS'		=> ($this->config['dir_allow_bbcode']) 	? $this->user->lang('BBCODE_IS_ON', '<a href="' . append_sid($this->root_path."faq.$this->php_ext", 'mode=bbcode') . '">', '</a>') : $this->user->lang('BBCODE_IS_OFF', '<a href="' . append_sid($this->root_path."faq.$this->php_ext", 'mode=bbcode') . '">', '</a>'),
			'IMG_STATUS'		=> ($this->config['dir_allow_bbcode']) 	? $this->user->lang['IMAGES_ARE_ON'] : $this->user->lang['IMAGES_ARE_OFF'],
			'SMILIES_STATUS'	=> ($this->config['dir_allow_smilies'])	? $this->user->lang['SMILIES_ARE_ON'] : $this->user->lang['SMILIES_ARE_OFF'],
			'URL_STATUS'		=> ($this->config['dir_allow_links'])	? $this->user->lang['URL_IS_ON'] : $this->user->lang['URL_IS_OFF'],
			'FLASH_STATUS'		=> ($this->config['dir_allow_bbcode'] && $this->config['dir_allow_flash'])	? $this->user->lang['FLASH_IS_ON'] : $this->user->lang['FLASH_IS_OFF'],

			'L_DIR_REPLY_EXP'	=> $this->user->lang('DIR_REPLY_EXP', $this->config['dir_length_comments']),

			'S_COMMENT' 		=> isset($this->s_comment) ? $this->s_comment : '',

			'S_BBCODE_ALLOWED' 	=> (bool) $this->config['dir_allow_bbcode'],
			'S_BBCODE_IMG'		=> (bool) $this->config['dir_allow_bbcode'],
			'S_BBCODE_FLASH'	=> ($this->config['dir_allow_bbcode'] && $this->config['dir_allow_flash']) ? true : false,
			'S_BBCODE_QUOTE'	=> true,
			'S_LINKS_ALLOWED'	=> (bool) $this->config['dir_allow_links'],
			'S_SMILIES_ALLOWED' => (bool) $this->config['dir_allow_smilies'],

			'S_HIDDEN_FIELDS'	=> build_hidden_fields($this->s_hidden_fields),
			'S_BUTTON_NAME'		=> ($mode == 'edit') ? 'update_comment' : 'submit_comment',
			'S_POST_ACTION' 	=> ($mode == 'edit') ? '' : $this->helper->route('ernadoo_phpbbdirectory_comment_new_controller', array('link_id' => (int) $link_id)),
		));
	}
}

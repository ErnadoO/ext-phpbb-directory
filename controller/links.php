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

class links
{
	private $link_user_id;
	private $site_name;
	private $url;
	private $description;
	private $guest_email;
	private $rss;
	private $banner;
	private $back;
	private $flag;

	private $captcha;
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

	/** @var \phpbb\captcha\factory */
	protected $captcha_factory;

	/** @var \ernadoo\phpbbdirectory\core\categorie */
	protected $categorie;

	/** @var \ernadoo\phpbbdirectory\core\link */
	protected $link;

	/** @var \ernadoo\phpbbdirectory\core\helper */
	protected $dir_helper;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface 		$db					Database object
	* @param \phpbb\config\config					$config				Config object
	* @param \phpbb\template\template				$template			Template object
	* @param \phpbb\user							$user				User object
	* @param \phpbb\controller\helper				$helper				Controller helper object
	* @param \phpbb\request\request					$request			Request object
	* @param \phpbb\auth\auth						$auth				Auth object
	* @param \phpbb\captcha\factory					$captcha_factory	Captcha object
	* @param \ernadoo\phpbbdirectory\core\categorie	$categorie			PhpBB Directory extension categorie object
	* @param \ernadoo\phpbbdirectory\core\link		$link				PhpBB Directory extension link object
	* @param \ernadoo\phpbbdirectory\core\helper	$dir_helper			PhpBB Directory extension helper object
	* @param string									$root_path			phpBB root path
	* @param string									$php_ext   			phpEx
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\auth\auth $auth, \phpbb\captcha\factory $captcha_factory, \ernadoo\phpbbdirectory\core\categorie $categorie, \ernadoo\phpbbdirectory\core\link $link, \ernadoo\phpbbdirectory\core\helper $dir_helper, $root_path, $php_ext)
	{
		$this->db				= $db;
		$this->config			= $config;
		$this->template			= $template;
		$this->user				= $user;
		$this->helper			= $helper;
		$this->request			= $request;
		$this->auth				= $auth;
		$this->captcha_factory 	= $captcha_factory;
		$this->categorie		= $categorie;
		$this->link				= $link;
		$this->dir_helper		= $dir_helper;
		$this->root_path		= $root_path;
		$this->php_ext			= $php_ext;

		$this->user->add_lang_ext('ernadoo/phpbbdirectory', 'directory');

		$this->template->assign_vars(array(
			'S_PHPBB_DIRECTORY'				=> true,
			'DIRECTORY_TRANSLATION_INFO'	=> (!empty($user->lang['DIRECTORY_TRANSLATION_INFO'])) ? $user->lang['DIRECTORY_TRANSLATION_INFO'] : '',
		));
	}

	/**
	* Delete a link
	*
	* @param	int	$cat_id		The category ID
	* @param	int	$link_id		The link ID
	* @return	null|\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	*/
	public function delete_link($cat_id, $link_id)
	{
		if ($this->request->is_set_post('cancel'))
		{
			$redirect = $this->helper->route('ernadoo_phpbbdirectory_page_controller', array('cat_id' => (int) $cat_id));
			redirect($redirect);
		}

		$sql = 'SELECT link_user_id
			FROM ' . DIR_LINK_TABLE . '
			WHERE link_id = ' . (int) $link_id;
		$result = $this->db->sql_query($sql);
		$link_data = $this->db->sql_fetchrow($result);

		if (empty($link_data))
		{
			throw new \phpbb\exception\http_exception(404, 'DIR_ERROR_NO_LINKS');
		}

		$delete_allowed = $this->user->data['is_registered'] && ($this->auth->acl_get('m_delete_dir') || ($this->user->data['user_id'] == $link_data['link_user_id'] && $this->auth->acl_get('u_delete_dir')));

		if (!$delete_allowed)
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
		}

		if (confirm_box(true))
		{
			$this->link->del($cat_id, $link_id);

			$meta_info = $this->helper->route('ernadoo_phpbbdirectory_page_controller', array('cat_id' => (int) $cat_id));
			meta_refresh(3, $meta_info);
			$message = $this->user->lang['DIR_DELETE_OK'] . '<br /><br />' . $this->user->lang('DIR_CLICK_RETURN_DIR', '<a href="' . $this->helper->route('ernadoo_phpbbdirectory_base_controller') . '">', '</a>') . '<br /><br />' . $this->user->lang('DIR_CLICK_RETURN_CAT', '<a href="' . $this->helper->route('ernadoo_phpbbdirectory_page_controller', array('cat_id' => (int) $cat_id)) . '">', '</a>');
			return $this->helper->message($message);
		}
		else
		{
			confirm_box(false, 'DIR_DELETE_SITE');
		}
	}

	/**
	* Edit a link
	*
	* @param	int	$cat_id		The category ID
	* @param	int	$link_id	The link ID
	* @return	null|\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	* @throws	\phpbb\exception\http_exception
	*/
	public function edit_link($cat_id, $link_id)
	{
		$sql = 'SELECT link_user_id
			FROM ' . DIR_LINK_TABLE . '
			WHERE link_id = ' . (int) $link_id;
		$result = $this->db->sql_query($sql);
		$link_data = $this->db->sql_fetchrow($result);
		$this->link_user_id = (int) $link_data['link_user_id'];

		$edit_allowed = ($this->user->data['is_registered'] && ($this->auth->acl_get('m_edit_dir') || ($this->user->data['user_id'] == (int) $link_data['link_user_id'] && $this->auth->acl_get('u_edit_dir'))));

		if (!$edit_allowed)
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
		}

		$cat_id		= $this->request->variable('id', $cat_id);
		$submit		= $this->request->is_set_post('submit') ? true : false;
		$refresh	= $this->request->is_set_post('refresh_vc') ? true : false;
		$title		= $this->user->lang['DIR_EDIT_SITE'];

		$this->template->assign_block_vars('dir_navlinks', array(
			'FORUM_NAME'	=> $title,
			'U_VIEW_FORUM'	=> $this->helper->route('ernadoo_phpbbdirectory_edit_controller', array('cat_id' => (int) $cat_id, 'link_id' => $link_id))
		));

		$this->categorie->get($cat_id);

		// If form is done
		if ($submit || $refresh)
		{
			if (false != ($result = $this->_data_processing($cat_id, $link_id, 'edit')))
			{
				return $result;
			}
		}
		else
		{
			$sql = 'SELECT link_id, link_uid, link_flags, link_bitfield, link_cat, link_url, link_description, link_guest_email, link_name, link_rss, link_back, link_banner, link_flag, link_cat, link_time
				FROM ' . DIR_LINK_TABLE . '
				WHERE link_id = ' . (int) $link_id;
			$result = $this->db->sql_query($sql);

			$site = $this->db->sql_fetchrow($result);

			if (empty($site['link_id']))
			{
				throw new \phpbb\exception\http_exception(404, 'DIR_ERROR_NO_LINKS');
			}

			$this->s_hidden_fields = array(
				'old_cat_id'	=> $site['link_cat'],
				'old_banner'	=> $site['link_banner'],
			);

			$site_description		= generate_text_for_edit($site['link_description'], $site['link_uid'], $site['link_flags']);
			$site['link_banner'] 	= (preg_match('/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|www\.).+/si', $site['link_banner'])) ? $site['link_banner'] : '';

			$this->url			= $site['link_url'];
			$this->site_name	= $site['link_name'];
			$this->description	= $site_description['text'];
			$this->guest_email	= $site['link_guest_email'];
			$this->rss			= $site['link_rss'];
			$this->banner 		= $site['link_banner'];
			$this->back			= $site['link_back'];
			$this->flag 		= $site['link_flag'];
		}

		$this->_populate_form($cat_id, 'edit', $title);

		return $this->helper->render('add_site.html', $title);
	}

	/**
	* Display add form
	*
	* @param	int	$cat_id		The category ID
	* @return	\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	* @throws	\phpbb\exception\http_exception
	*/
	public function new_link($cat_id)
	{
		if (!$this->auth->acl_get('u_submit_dir'))
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
		}

		$cat_id		= $this->request->variable('id', $cat_id);
		$submit		= $this->request->is_set_post('submit') ? true : false;
		$refresh	= $this->request->is_set_post('refresh_vc') ? true : false;
		$title		= $this->user->lang['DIR_NEW_SITE'];

		$this->template->assign_block_vars('dir_navlinks', array(
			'FORUM_NAME'	=> $title,
			'U_VIEW_FORUM'	=> $this->helper->route('ernadoo_phpbbdirectory_new_controller', array('cat_id' => (int) $cat_id))
		));

		$this->categorie->get($cat_id);

		// The CAPTCHA kicks in here. We can't help that the information gets lost on language change.
		if (!$this->user->data['is_registered'] && $this->config['dir_visual_confirm'])
		{
			$this->captcha = $this->captcha_factory->get_instance($this->config['captcha_plugin']);
			$this->captcha->init(CONFIRM_POST);
		}

		// If form is done
		if ($submit || $refresh)
		{
			if (false != ($result = $this->_data_processing($cat_id)))
			{
				return $result;
			}
		}

		$this->_populate_form($cat_id, 'new', $title);

		return $this->helper->render('add_site.html', $title);
	}

	/**
	* View link controller
	*
	* @param	int	$link_id		The link ID
	* @return	\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	*/
	public function view_link($link_id)
	{
		return $this->link->view($link_id);
	}

	/**
	* Vote for a link
	*
	* @param	int $cat_id		The category ID
	* @param	int $link_id	The link ID
	* @return	\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	*/
	public function vote_link($cat_id, $link_id)
	{
		$this->categorie->get($cat_id);

		if (!$this->auth->acl_get('u_vote_dir') || !$this->categorie->data['cat_allow_votes'])
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
		}

		$data = array(
			'vote_link_id' 		=> (int) $link_id,
			'vote_user_id' 		=> (int) $this->user->data['user_id'],
		);

		// We check if user had already vot for this website.
		$sql = 'SELECT vote_link_id
			FROM ' . DIR_VOTE_TABLE . '
			WHERE ' . $this->db->sql_build_array('SELECT', $data);
		$result = $this->db->sql_query($sql);
		$data = $this->db->sql_fetchrow($result);

		if (!empty($data['vote_link_id']))
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_VOTE');
		}

		$this->link->add_vote($link_id);

		$meta_info = $this->helper->route('ernadoo_phpbbdirectory_page_controller', array('cat_id' => (int) $cat_id));
		meta_refresh(3, $meta_info);
		$message = $this->user->lang['DIR_VOTE_OK'] . '<br /><br />' . $this->user->lang('DIR_CLICK_RETURN_CAT', '<a href="' . $meta_info . '">', '</a>');
		return $this->helper->message($message);
	}

	/**
	* Routine
	*
	* @param	int		$cat_id		The category ID
	* @param	int		$link_id	The link ID
	* @param	string	$mode		add|edit
	* @return	null|\Symfony\Component\HttpFoundation\Response	A Symfony Response object
	* @throws	\phpbb\exception\http_exception
	*/
	private function _data_processing($cat_id, $link_id = 0, $mode = 'new')
	{
		if (($mode == 'edit' && !$this->auth->acl_get('m_edit_dir') && !$this->auth->acl_get('u_edit_dir')) || ($mode == 'new' && !$this->auth->acl_get('u_submit_dir')))
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
		}

		if (!check_form_key('dir_form'))
		{
			return $this->helper->message('FORM_INVALID');
		}

		$this->url			= $this->request->variable('url', '');
		$this->site_name	= $this->request->variable('site_name', '', true);
		$this->description	= $this->request->variable('description', '', true);
		$this->guest_email	= $this->request->variable('guest_email', '');
		$this->rss			= $this->request->variable('rss', '');
		$this->banner 		= $this->request->variable('banner', '');
		$this->back			= $this->request->variable('back', '');
		$this->flag 		= $this->request->variable('flag', '');

		if (!function_exists('validate_data'))
		{
			include($this->root_path . 'includes/functions_user.' . $this->php_ext);
		}

		// We define variables to check
		$data = array(
			'email'			=> $this->guest_email,
			'site_name'		=> $this->site_name,
			'website'		=> $this->url,
			'description'	=> $this->description,
			'rss'			=> $this->rss,
			'banner'		=> $this->banner,
			'back'			=> $this->back,
			'cat'			=> (int) $cat_id,
		);

		// We define verification type for each variable
		$data2 = array(
			'email'			=>		array(
				array('string', $this->user->data['is_registered'], 6, 60),
				array('user_email', '')),
			'site_name' =>			array(
				array('string', false, 1, 100)),
			'website'		=>		array(
				array('string',	false, 12, 255),
				array('match',	true, '#^http[s]?://(.*?\.)*?[a-z0-9\-]+\.[a-z]{2,4}#i')),
			'description'	=>		array(
				array('string', !$this->categorie->data['cat_must_describe'], 1, $this->config['dir_length_describe'])),
			'rss'			=>		array(
				array('string', true, 12, 255),
				array('match',	empty($this->rss), '#^http[s]?://(.*?\.)*?[a-z0-9\-]+\.[a-z]{2,4}#i')),
			'banner'		=>		array(
				array('string', true, 5, 255)),
			'back'			=>		array(
				array('string',	!$this->categorie->data['cat_link_back'], 12, 255),
				array(array($this->link, 'link_back'), true)),
			'cat'			=>		array(
				array('num', '', 1))
		);

		$this->user->add_lang('ucp');
		$error = validate_data($data, $data2);
		$error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$this->user->lang['\\1'])) ? \$this->user->lang['\\1'] : '\\1'", $error);

		// We check that url have good format
		if (preg_match('/^(http|https):\/\//si', $this->url) && $this->config['dir_activ_checkurl'] && !$this->link->checkurl($this->url))
		{
			$error[] = $this->user->lang['DIR_ERROR_CHECK_URL'];
		}

		if (!$this->user->data['is_registered'] && $this->config['dir_visual_confirm'])
		{
			$vc_response = $this->captcha->validate($data);
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
			/**
			* No errrors, we execute heavy tasks wich need a valid url
			*/

			// Banner
			$this->link->banner_process($this->banner, $error);

			// PageRank
			$pagerank = $this->link->pagerank_process($this->url);

			// Thumb ;)
			$thumb = $this->link->thumb_process($this->url);
		}

		// Still no errors?? So let's go!
		if (!$error)
		{
			$uid = $bitfield = $flags	= '';
			generate_text_for_storage($this->description, $uid, $bitfield, $flags, (bool) $this->config['allow_bbcode'], (bool) $this->config['allow_post_links'], (bool) $this->config['allow_smilies']);

			$this->banner	= (!$this->banner && !$this->request->is_set_post('delete_banner')) ? $this->request->variable('old_banner', '') : $this->banner;
			$this->url		= $this->link->clean_url($this->url);

			$data_edit = array(
				'link_user_id'		=> $this->link_user_id,
				'link_guest_email'	=> $this->guest_email,
				'link_name'			=> $this->site_name,
				'link_url'			=> $this->url,
				'link_description'	=> $this->description,
				'link_cat'			=> (int) $cat_id,
				'link_rss'			=> $this->rss,
				'link_banner'		=> $this->banner,
				'link_back'			=> $this->back,
				'link_uid'			=> $uid,
				'link_flags'		=> $flags,
				'link_flag'			=> $this->flag,
				'link_bitfield'		=> $bitfield,
				'link_pagerank'		=> (int) $pagerank,
				'link_thumb'		=> $thumb,
			);

			$need_approval = ($this->categorie->need_approval() && !$this->auth->acl_get('a_') && !$this->auth->acl_get('m_')) ? true : false;

			if ($mode == 'edit')
			{
				$data_edit['link_cat_old'] = $this->request->variable('old_cat_id', 0);
				$this->link->edit($data_edit, $link_id, $need_approval);
			}
			else
			{
				$data_add = array(
					'link_time'			=> time(),
					'link_view'			=> 0,
					'link_active'		=> $need_approval ? false : true,
					'link_user_id'		=> $this->user->data['user_id'],
				);

				$data_add = array_merge($data_edit, $data_add);

				$this->link->add($data_add, $need_approval);
			}

			$meta_info = $this->helper->route('ernadoo_phpbbdirectory_page_controller', array('cat_id' => (int) $cat_id));
			meta_refresh(3, $meta_info);
			$message	= ($need_approval) ? $this->user->lang['DIR_'.strtoupper($mode).'_SITE_ACTIVE'] : $this->user->lang['DIR_'.strtoupper($mode).'_SITE_OK'];
			$message	= $message . '<br /><br />' . $this->user->lang('DIR_CLICK_RETURN_DIR', '<a href="' . $this->helper->route('ernadoo_phpbbdirectory_base_controller') . '">', '</a>') . '<br /><br />' . $this->user->lang('DIR_CLICK_RETURN_CAT', '<a href="' . $this->helper->route('ernadoo_phpbbdirectory_page_controller', array('cat_id' => (int) $cat_id)) . '">', '</a>');
			return $this->helper->message($message);
		}
		else
		{
			if ($mode == 'edit')
			{
				$this->s_hidden_fields = array(
					'old_cat_id'	=> $this->request->variable('old_cat_id', 0),
					'old_banner'	=> $this->request->variable('old_banner', '')
				);
			}

			$this->template->assign_vars(array(
				'ERROR'	=> (isset($error)) ? implode('<br />', $error) : ''
			));
		}
	}

	/**
	* Display a banner
	*
	* @param	string $banner_img		Path to banner file
	* @return	null
	*/
	public function return_banner($banner_img)
	{
		if (!function_exists('phpbb_is_greater_ie_version'))
		{
			include($this->root_path . 'includes/functions_download.'.$this->php_ext);
		}

		$browser = strtolower($this->request->header('User-Agent', 'msie 6.0'));

		// Adjust image_dir path (no trailing slash)
		if (substr($banner_img, -1, 1) == '/' || substr($banner_img, -1, 1) == '\\')
		{
			$banner_img = substr($banner_img, 0, -1) . '/';
		}
		$banner_img = str_replace(array('../', '..\\', './', '.\\'), '', $banner_img);

		if ($banner_img && ($banner_img[0] == '/' || $banner_img[0] == '\\'))
		{
			$banner_img = '';
		}
		$file_path = $this->dir_helper->get_banner_path($banner_img);

		if ((@file_exists($file_path) && @is_readable($file_path)) && !headers_sent())
		{
			header('Pragma: public');

			$image_data = @getimagesize($file_path);

			header('Content-Type: ' . image_type_to_mime_type($image_data[2]));

			if ((strpos(strtolower($this->user->browser), 'msie') !== false) && !phpbb_is_greater_ie_version($browser, 7))
			{
				header('Content-Disposition: attachment; ' . header_filename($banner_img));

				if (strpos(strtolower($browser), 'msie 6.0') !== false)
				{
					header('Expires: -1');
				}
				else
				{
					header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
				}
			}
			else
			{
				header('Content-Disposition: inline; ' . header_filename($banner_img));
				header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
			}

			$size = @filesize($file_path);
			if ($size)
			{
				header("Content-Length: $size");
			}

			if (@readfile($file_path) == false)
			{
				$fp = @fopen($file_path, 'rb');

				if ($fp !== false)
				{
					while (!feof($fp))
					{
						// Sorry EPV
						echo fread($fp, 8192);
					}
					fclose($fp);
				}
				else
				{
					@readfile($file_path);
				}
			}

			flush();
		}
		else
		{
			header('HTTP/1.0 404 Not Found');
		}
		file_gc();
	}

	/**
	* Populate form when an error occurred
	*
	* @param	int		$cat_id		The category ID
	* @param	string	$mode		add|edit
	* @param	string	$title		Page title (depends of $mode)
	* @return	null
	*/
	private function _populate_form($cat_id, $mode, $title)
	{
		global $phpbb_extension_manager;

		if (!$this->user->data['is_registered'] && $this->config['dir_visual_confirm'] && $mode == 'new')
		{
			$this->s_hidden_fields = array_merge($this->s_hidden_fields, $this->captcha->get_hidden_fields());

			$this->user->add_lang('ucp');

			$this->template->assign_vars(array(
				'CAPTCHA_TEMPLATE'		=> $this->captcha->get_template(),
			));
		}

		$this->user->add_lang('posting');

		if (!function_exists('display_custom_bbcodes'))
		{
			include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		}
		display_custom_bbcodes();
		add_form_key('dir_form');

		$ext_path = $phpbb_extension_manager->get_extension_path('ernadoo/phpbbdirectory', false);
		$flag_path = $ext_path.'images/flags/';

		$s_guest	= (!$this->user->data['is_registered'] || !empty($this->guest_email));
		$s_rss		= $this->config['dir_activ_rss'];
		$s_banner	= $this->config['dir_activ_banner'];
		$s_back		= $this->categorie->data['cat_link_back'];
		$s_flag		= $this->config['dir_activ_flag'];

		$this->template->assign_vars(array(
			'BBCODE_STATUS'			=> ($this->config['allow_bbcode']) 	? $this->user->lang('BBCODE_IS_ON', '<a href="' . append_sid($this->root_path."faq.$this->php_ext", 'mode=bbcode') . '">', '</a>') : $this->user->lang('BBCODE_IS_OFF', '<a href="' . append_sid($this->root_path."faq.$this->php_ext", 'mode=bbcode') . '">', '</a>'),
			'IMG_STATUS'			=> ($this->config['allow_bbcode'])	? $this->user->lang['IMAGES_ARE_ON'] : $this->user->lang['IMAGES_ARE_OFF'],
			'SMILIES_STATUS'		=> ($this->config['allow_smilies']) ? $this->user->lang['SMILIES_ARE_ON'] : $this->user->lang['SMILIES_ARE_OFF'],
			'URL_STATUS'			=> ($this->config['allow_post_links']) ? $this->user->lang['URL_IS_ON'] : $this->user->lang['URL_IS_OFF'],
			'FLASH_STATUS'			=> ($this->config['allow_bbcode'] && $this->config['allow_post_flash'])	? $this->user->lang['FLASH_IS_ON'] : $this->user->lang['FLASH_IS_OFF'],

			'L_TITLE'				=> $title,
			'L_DIR_DESCRIPTION_EXP'	=> $this->user->lang('DIR_DESCRIPTION_EXP', $this->config['dir_length_describe']),
			'L_DIR_SUBMIT_TYPE'		=> $this->categorie->dir_submit_type($this->categorie->need_approval()),
			'L_DIR_SITE_BANN_EXP'	=> $this->user->lang('DIR_SITE_BANN_EXP', $this->config['dir_banner_width'], $this->config['dir_banner_height']),

			'S_GUEST'				=> $s_guest ? true : false,
			'S_RSS'					=> $s_rss ? true : false,
			'S_BANNER'				=> $s_banner ? true : false,
			'S_BACK'				=> $s_back ? true : false,
			'S_FLAG'				=> $s_flag ? true : false,
			'S_BBCODE_ALLOWED' 		=> (bool) $this->config['allow_bbcode'],
			'S_BBCODE_IMG'			=> (bool) $this->config['allow_bbcode'],
			'S_BBCODE_FLASH'		=> ($this->config['allow_bbcode'] && $this->config['allow_post_flash']) ? true : false,
			'S_BBCODE_QUOTE'		=> true,
			'S_LINKS_ALLOWED'		=> (bool) $this->config['allow_post_links'],

			'DIR_FLAG_PATH'			=> $flag_path,
			'DIR_FLAG_IMAGE'		=> $this->flag ? $this->dir_helper->get_img_path('flags', $this->flag) : '',

			'EDIT_MODE'				=> ($mode == 'edit') ? true : false,

			'SITE_NAME'				=> isset($this->site_name) ? $this->site_name : '',
			'SITE_URL'				=> isset($this->url) ? $this->url : '',
			'DESCRIPTION'			=> isset($this->description) ? $this->description : '',
			'GUEST_EMAIL'			=> isset($this->guest_email) ? $this->guest_email : '',
			'RSS'					=> isset($this->rss) ? $this->rss : '',
			'BANNER'				=> isset($this->banner) ? $this->banner : '',
			'BACK'					=> isset($this->back) ? $this->back : '',
			'S_POST_ACTION'			=> '',
			'S_CATLIST'				=> $this->categorie->make_cat_select($cat_id),
			'S_LIST_FLAG'			=> $this->link->get_dir_flag_list($flag_path, $this->flag),
			'S_DESC_STAR'			=> (@$this->categorie->data['cat_must_describe']) ? '*' : '',
			'S_ROOT'				=> $cat_id,
			'S_HIDDEN_FIELDS'		=> build_hidden_fields($this->s_hidden_fields),
		));
	}
}

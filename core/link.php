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

class link
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\language\language */
	protected $language;

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

	/** @var \phpbb\notification\manager */
	protected $notification;

	/** @var \phpbb\filesystem\filesystem_interface */
	protected $filesystem;

	/** @var \FastImageSize\FastImageSize */
	protected $imagesize;

	/** @var \phpbb\files\factory */
	protected $files_factory;

	/** @var \ernadoo\phpbbdirectory\core\helper */
	protected $dir_helper;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface 					$db					Database object
	* @param \phpbb\config\config 								$config				Config object
	* @param \phpbb\language\language							$language			Language object
	* @param \phpbb\template\template 							$template			Template object
	* @param \phpbb\user 										$user				User object
	* @param \phpbb\controller\helper 							$helper				Controller helper object
	* @param \phpbb\request\request 							$request			Request object
	* @param \phpbb\auth\auth 									$auth				Auth object
	* @param \phpbb\notification\manager						$notification		Notification object
	* @param \phpbb\filesystem\filesystem_interface				$filesystem			phpBB filesystem helper
	* @param \FastImageSize\FastImageSize						$imagesize 			FastImageSize class
	* @param \phpbb\files\factory								$files_factory		File classes factory
	* @param \ernadoo\phpbbdirectory\core\helper				$dir_helper			PhpBB Directory extension helper object
	* @param string         									$root_path			phpBB root path
	* @param string         									$php_ext			phpEx
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\language\language $language, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\auth\auth $auth, \phpbb\notification\manager $notification, \phpbb\filesystem\filesystem_interface $filesystem, \FastImageSize\FastImageSize $imagesize, \phpbb\files\factory $files_factory, \ernadoo\phpbbdirectory\core\helper $dir_helper, $root_path, $php_ext)
	{
		$this->db				= $db;
		$this->config			= $config;
		$this->language			= $language;
		$this->template			= $template;
		$this->user				= $user;
		$this->helper			= $helper;
		$this->request			= $request;
		$this->auth				= $auth;
		$this->notification		= $notification;
		$this->filesystem		= $filesystem;
		$this->imagesize		= $imagesize;
		$this->files_factory 	= $files_factory;
		$this->dir_helper		= $dir_helper;
		$this->root_path		= $root_path;
		$this->php_ext			= $php_ext;
	}

	/**
	* Add a link into db
	*
	* @param	array	$data			Contains all data to insert in db
	* @param	bool	$need_approval	Links needs to be approved?
	* @return	null
	*/
	public function add($data, $need_approval)
	{
		$notification_data = array();

		$this->db->sql_transaction('begin');

		$sql = 'INSERT INTO ' . DIR_LINK_TABLE . ' ' . $this->db->sql_build_array('INSERT', $data);
		$this->db->sql_query($sql);
		$notification_data['link_id'] = $this->db->sql_nextid();

		if (!$need_approval || $this->auth->acl_get('a_') || $this->auth->acl_get('m_'))
		{
			$sql = 'UPDATE ' . DIR_CAT_TABLE . '
				SET cat_links = cat_links + 1
				WHERE cat_id = ' . (int) $data['link_cat'];
			$this->db->sql_query($sql);

			$notification_type = 'ernadoo.phpbbdirectory.notification.type.directory_website';
		}
		else if ($this->config['dir_mail'])
		{
			$notification_type = 'ernadoo.phpbbdirectory.notification.type.directory_website_in_queue';
		}

		$this->db->sql_transaction('commit');

		if (isset($notification_type))
		{
			$notification_data = array_merge($notification_data,
				array(
					'user_from'			=> (int) $data['link_user_id'],
					'link_name'			=> $data['link_name'],
					'link_url'			=> $data['link_url'],
					'link_description'	=> $data['link_description'],
					'cat_id'			=> (int) $data['link_cat'],
					'cat_name'			=> \ernadoo\phpbbdirectory\core\categorie::getname((int) $data['link_cat']),
				)
			);

			$this->notification->add_notifications($notification_type, $notification_data);
		}
	}

	/**
	* Edit a link of the db
	*
	* @param	array	$data			Contains all data to edit in db
	* @param	int		$link_id		is link's id, for WHERE clause
	* @param	bool	$need_approval	Links needs to be approved?
	* @return	null
	*/
	public function edit($data, $link_id, $need_approval)
	{
		$notification_data = array(
			'link_id'			=> (int) $link_id,
			'user_from'			=> (int) $data['link_user_id'],
			'link_name'			=> $data['link_name'],
			'link_description'	=> $data['link_description'],
			'cat_id'			=> (int) $data['link_cat'],
			'cat_name'			=> \ernadoo\phpbbdirectory\core\categorie::getname((int) $data['link_cat']),
		);

		$old_cat = array_pop($data);

		if ($old_cat != $data['link_cat'] || $need_approval)
		{
			$this->notification->delete_notifications('ernadoo.phpbbdirectory.notification.type.directory_website', (int) $link_id);

			$this->db->sql_transaction('begin');

			$sql = 'UPDATE ' . DIR_CAT_TABLE . '
				SET cat_links = cat_links - 1
				WHERE cat_id = ' . (int) $old_cat;
			$this->db->sql_query($sql);

			if (!$need_approval)
			{
				$sql = 'UPDATE ' . DIR_CAT_TABLE . '
					SET cat_links = cat_links + 1
					WHERE cat_id = ' . (int) $data['link_cat'];
				$this->db->sql_query($sql);

				$notification_type = 'ernadoo.phpbbdirectory.notification.type.directory_website';
			}
			else
			{
				$data['link_active'] = false;
				$notification_type = 'ernadoo.phpbbdirectory.notification.type.directory_website_in_queue';
			}

			$this->db->sql_transaction('commit');

			$this->notification->add_notifications($notification_type, $notification_data);
		}

		$sql = 'UPDATE ' . DIR_LINK_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $data) . '
			WHERE link_id = ' . (int) $link_id;
		$this->db->sql_query($sql);
	}

	/**
	* Delete a link of the db
	*
	* @param	int 	$cat_id		The category ID
	* @param	mixed 	$link_id	Link's id, for WHERE clause
	* @return	null
	*/
	public function del($cat_id, $link_id)
	{
		$this->db->sql_transaction('begin');

		$url_array = is_array($link_id) ? $link_id : array($link_id);

		// Delete links datas
		$link_datas_ary = array(
			DIR_LINK_TABLE		=> 'link_id',
			DIR_COMMENT_TABLE	=> 'comment_link_id',
			DIR_VOTE_TABLE		=> 'vote_link_id',
		);

		$sql = 'SELECT link_banner
			FROM ' . DIR_LINK_TABLE . '
			WHERE '. $this->db->sql_in_set('link_id', $url_array);
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($row['link_banner'] && !preg_match('/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|www\.).+/si', $row['link_banner']))
			{
				$banner_img = $this->dir_helper->get_banner_path(basename($row['link_banner']));

				if (file_exists($banner_img))
				{
					@unlink($banner_img);
				}
			}
		}

		foreach ($link_datas_ary as $table => $field)
		{
			$this->db->sql_query("DELETE FROM $table WHERE ".$this->db->sql_in_set($field, $url_array));
		}

		$sql = 'UPDATE ' . DIR_CAT_TABLE . '
			SET cat_links = cat_links - '.sizeof($url_array).'
			WHERE cat_id = ' . (int) $cat_id;
		$this->db->sql_query($sql);

		$this->db->sql_transaction('commit');

		foreach ($url_array as $link_id)
		{
			$this->notification->delete_notifications(array(
				'ernadoo.phpbbdirectory.notification.type.directory_website',
				'ernadoo.phpbbdirectory.notification.type.directory_website_in_queue'
			), $link_id);
		}

		if ($this->request->is_ajax())
		{
			$sql = 'SELECT cat_links
				FROM ' . DIR_CAT_TABLE . '
				WHERE cat_id = ' . (int) $cat_id;
			$result = $this->db->sql_query($sql);
			$data = $this->db->sql_fetchrow($result);

			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success' => true,

				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('DIR_DELETE_OK'),
				'LINK_ID'		=> $link_id,
				'TOTAL_LINKS'	=> $this->language->lang('DIR_NB_LINKS', (int) $data['cat_links']),
			));
		}
	}

	/**
	* Increments link view counter
	*
	* @param	int		$link_id	Link's id, for WHERE clause
	* @return	null
	* @throws	\phpbb\exception\http_exception
	*/
	public function view($link_id)
	{
		$sql = 'SELECT link_id, link_url
			FROM ' . DIR_LINK_TABLE . '
			WHERE link_id = ' . (int) $link_id;
		$result = $this->db->sql_query($sql);
		$data = $this->db->sql_fetchrow($result);

		if (empty($data['link_id']))
		{
			throw new \phpbb\exception\http_exception(404, 'DIR_ERROR_NO_LINKS');
		}

		$sql = 'UPDATE ' . DIR_LINK_TABLE . '
			SET link_view = link_view + 1
			WHERE link_id = ' . (int) $link_id;
		$this->db->sql_query($sql);

		redirect($data['link_url'], false, true);
		return;
	}

	/**
	* Verify that an URL exist before add into db
	*
	* @param	string	$url	The URL to check
	* @return	bool			True if url is reachable, else false.
	*/
	public function checkurl($url)
	{
		$details = parse_url($url);

		if (!isset($details['port']))
		{
			$details['port'] = 80;
		}
		if (!isset($details['path']))
		{
			$details['path'] = '/';
		}

		if ($sock = @fsockopen($details['host'], $details['port'], $errno, $errstr, 1))
		{
			$requete = 'GET '.$details['path']." HTTP/1.1\r\n";
			$requete .= 'Host: '.$details['host']."\r\n\r\n";

			// Send a HTTP GET header
			fputs($sock, $requete);
			// answer from server
			$str = fgets($sock, 1024);
			preg_match("'HTTP/1\.. (.*) (.*)'U", $str, $parts);
			fclose($sock);

			if ($parts[1] == '404')
			{
				return false;
			}

			return true;
		}
		return false;
	}

	/**
	* Delete the final '/', if no path
	*
	* @param	string	$url	URL to clean
	* @return	string	$url	The correct string.
	*/
	public function clean_url($url)
	{
		$details = parse_url($url);

		if (isset($details['path']) && $details['path'] == '/' && !isset($details['query']))
		{
			return substr($url, 0, -1);
		}
		return $url;
	}

	/**
	* Display a flag
	*
	* @param	array	$data	Link's data from db
	* @return	string			Flag path.
	*/
	public function display_flag($data)
	{
		global $phpbb_extension_manager;

		$ext_path = $phpbb_extension_manager->get_extension_path('ernadoo/phpbbdirectory', false);
		$flag_path = $ext_path.'images/flags/';
		$img_flag = 'no_flag.png';

		if ($this->config['dir_activ_flag'] && !empty($data['link_flag']) && file_exists($flag_path . $data['link_flag']))
		{
			$img_flag = $data['link_flag'];
		}

		return $this->dir_helper->get_img_path('flags', $img_flag);
	}

	/**
	* Calculate the link's note
	*
	* @param	int		$total_note		Sum of all link's notes
	* @param	int		$nb_vote		Number of votes
	* @param	bool	$votes_status	Votes are enable in this category?
	* @return	string	$note			The calculated note.
	*/
	public function display_note($total_note, $nb_vote, $votes_status)
	{
		if (!$votes_status)
		{
			return;
		}

		$note = ($nb_vote < 1) ? '' : $total_note / $nb_vote;
		$note = (strlen($note) > 2) ? number_format($note, 1) : $note;

		return ($nb_vote) ? $this->language->lang('DIR_FROM_TEN', $note) : $this->language->lang('DIR_NO_NOTE');
	}

	/**
	* Display the vote form for auth users
	*
	* @param	array	$data	Link's data from db
	* @return	null|string		Html combo list or nothing if votes are not available.
	*/
	public function display_vote($data)
	{
		if ($this->user->data['is_registered'] && $this->auth->acl_get('u_vote_dir') && empty($data['vote_user_id']))
		{
			$list = '<select name="vote">';
			for ($i = 0; $i <= 10; $i++)
			{
				$list .= '<option value="' . $i . '"' . (($i == 5) ? ' selected="selected"' : '') . '>' . $i . '</option>';
			}
			$list .= '</select>';

			return $list;
		}
	}

	/**
	* Display the RSS icon
	*
	* @param	array	$data	Link's data from db
	* @return	null|string		RSS feed URL or nothing.
	*/
	public function display_rss($data)
	{
		if ($this->config['dir_activ_rss'] && !empty($data['link_rss']))
		{
				return $data['link_rss'];
		}
	}

	/**
	* Display link's thumb if thumb service enabled.
	* if thumb don't exists in db or if a new service was choosen in acp
	* thumb is research
	*
	* @param	array		$data	Link's data from db
	* @return	string|null			Thumb or null.
	*/
	public function display_thumb($data)
	{
		if ($this->config['dir_activ_thumb'])
		{
			if (!$data['link_thumb'] || ($this->config['dir_thumb_service_reverse'] && (!strstr($data['link_thumb'], 'ascreen.jpg') && (!strstr($data['link_thumb'], $this->config['dir_thumb_service'])))))
			{
				$thumb = $this->thumb_process($data['link_url']);

				$sql = 'UPDATE ' . DIR_LINK_TABLE . '
					SET link_thumb = "' . $this->db->sql_escape($thumb) . '"
					WHERE link_id = ' . (int) $data['link_id'];
				$this->db->sql_query($sql);

				return $thumb;
			}
			return $data['link_thumb'];
		}
	}

	/**
	* Display and calculate PageRank if needed
	*
	* @param	array	$data	Link's data from db
	* @return	string			Pagerank, 'n/a' or false
	*/
	public function display_pagerank($data)
	{
		if ($this->config['dir_activ_pagerank'])
		{
			if ($data['link_pagerank'] == '')
			{
				$pagerank = $this->pagerank_process($data['link_url']);

				$sql = 'UPDATE ' . DIR_LINK_TABLE . '
					SET link_pagerank = ' . (int) $pagerank . '
					WHERE link_id = ' . (int) $data['link_id'];
				$this->db->sql_query($sql);
			}
			else
			{
				$pagerank = (int) $data['link_pagerank'];
			}

			$prpos=40*$pagerank/10;
			$prneg=40-$prpos;
			$html='<img src="http://www.google.com/images/pos.gif" width="'.$prpos.'" height="4" alt="'.$pagerank.'" /><img src="http://www.google.com/images/neg.gif" width="'.$prneg.'" height="4" alt="'.$pagerank.'" /> ';

			$pagerank = $pagerank == '-1' ? $this->language->lang('DIR_PAGERANK_NOT_AVAILABLE') : $this->language->lang('DIR_FROM_TEN', $pagerank);
			return $html.$pagerank;
		}
		return false;
	}

	/**
	* Display and resize a banner
	*
	* @param	array	$data		link's data from db
	* @return	string	$s_banner	html code.
	*/
	public function display_bann($data)
	{
		if (!empty($data['link_banner']))
		{
			if (!preg_match('/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|www\.).+/si', $data['link_banner']))
			{
				$img_src = $this->helper->route('ernadoo_phpbbdirectory_banner_controller', array('banner_img' => $data['link_banner']));
				$physical_path = $this->dir_helper->get_banner_path($data['link_banner']);
			}
			else
			{
				$img_src = $physical_path = $data['link_banner'];
			}

			if (($image_data = $this->imagesize->getImageSize($physical_path)) === false)
			{
				return '';
			}

			$width = $image_data['width'];
			$height = $image_data['height'];

			if (($width > $this->config['dir_banner_width'] || $height > $this->config['dir_banner_height']) && $this->config['dir_banner_width'] > 0 && $this->config['dir_banner_height'] > 0)
			{
				$coef_w = $width / $this->config['dir_banner_width'];
				$coef_h = $height / $this->config['dir_banner_height'];
				$coef_max = max($coef_w, $coef_h);
				$width /= $coef_max;
				$height /= $coef_max;
			}

			return '<img src="' . $img_src . '" width="' . $width . '" height="' . $height . '" alt="'.$data['link_name'].'" title="'.$data['link_name'].'" />';
		}
		return '';
	}

	/**
	* Add a vote in db, for a specifi link
	*
	* @param	int		$link_id	Link_id from db
	* @return	null
	*/
	public function add_vote($link_id)
	{
		$data = array(
			'vote_link_id' 		=> (int) $link_id,
			'vote_user_id' 		=> $this->user->data['user_id'],
			'vote_note'			=> $this->request->variable('vote', 0),
		);

		$this->db->sql_transaction('begin');

		$sql = 'INSERT INTO ' . DIR_VOTE_TABLE . ' ' . $this->db->sql_build_array('INSERT', $data);
		$this->db->sql_query($sql);

		$sql = 'UPDATE ' . DIR_LINK_TABLE . '
			SET link_vote = link_vote + 1,
			link_note = link_note + ' . (int) $data['vote_note'] . '
		WHERE link_id = ' . (int) $link_id;
		$this->db->sql_query($sql);

		$this->db->sql_transaction('commit');

		if ($this->request->is_ajax())
		{
			$sql= 'SELECT link_vote, link_note FROM ' . DIR_LINK_TABLE . ' WHERE link_id = ' . (int) $link_id;
			$result = $this->db->sql_query($sql);
			$data = $this->db->sql_fetchrow($result);

			$note = $this->display_note($data['link_note'], $data['link_vote'], true);

			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success' => true,

				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('DIR_VOTE_OK'),
				'NOTE'			=> $note,
				'NB_VOTE'		=> $this->language->lang('DIR_NB_VOTES', (int) $data['link_vote']),
				'LINK_ID'		=> $link_id
			));
		}
	}

	/**
	* Search an appropriate thumb for url
	*
	* @param	string	$url	Link's url
	* @return	string			The thumb url
	*/
	public function thumb_process($url)
	{
		if (!$this->config['dir_activ_thumb'])
		{
			return $this->root_path.'images/directory/nothumb.gif';
		}

		$details = parse_url($url);

		$root_url		= $details['scheme'].'://'.$details['host'];
		$absolute_url	= isset($details['path']) ? $root_url.$details['path'] : $root_url;

		if ($this->config['dir_activ_thumb_remote'] && $this->_ascreen_exist($details['scheme'], $details['host']))
		{
			return $root_url.'/ascreen.jpg';
		}
		return $this->config['dir_thumb_service'].$absolute_url;
	}

	/**
	* Check if ascreen thumb exists
	*
	* @param	string	$protocol	The protocol
	* @param	string	$host		The hostname
	* @return	bool				True if ascreen file exixts, else false
	*/
	private function _ascreen_exist($protocol, $host)
	{
		if (($thumb_info = $this->imagesize->getImageSize($protocol.'://'.$host.'/ascreen.jpg')) !== false)
		{
			// Obviously this is an image, we did some additional tests
			if ($thumb_info['width'] == '120' && $thumb_info['height'] == '90' && $thumb_info['type'] == 2)
			{
				return true;
			}
		}
		return false;
	}

	/**
	* Primary work on banner, can edit, copy or check a banner
	*
	* @param	string	$banner	The banner's remote url
	* @param	array	$error	The array error, passed by reference
	* @return	null
	*/
	public function banner_process(&$banner, &$error)
	{
		$old_banner = $this->request->variable('old_banner', '');

		$destination = $this->dir_helper->get_banner_path();

		// Can we upload?
		$can_upload = ($this->config['dir_storage_banner'] && $this->filesystem->exists($this->root_path . $destination) && $this->filesystem->is_writable($this->root_path . $destination) && (@ini_get('file_uploads') || strtolower(@ini_get('file_uploads')) == 'on')) ? true : false;

		if ($banner && $can_upload)
		{
			$file = $this->_banner_upload($banner, $error);
		}
		else if ($banner)
		{
			$file = $this->_banner_remote($banner, $error);
		}
		else if ($this->request->is_set_post('delete_banner') && $old_banner)
		{
			$this->_banner_delete($old_banner);
			return;
		}

		if (!sizeof($error))
		{
			if ($banner && $old_banner && !preg_match('/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|www\.).+/si', $old_banner))
			{
				$this->_banner_delete($old_banner);
			}

			$banner = !empty($file) ? $file : '';
		}
	}

	/**
	* Copy a remonte banner to server.
	* called by banner_process()
	*
	* @param	string	$banner The banner's remote url
	* @param	array	$error	The array error, passed by reference
	* @return	false|string	String if no errors, else false
	*/
	private function _banner_upload($banner, &$error)
	{
		/** @var \phpbb\files\upload $upload */
		$upload = $this->files_factory->get('upload')
			->set_error_prefix('DIR_BANNER_')
			->set_allowed_extensions(array('jpg', 'jpeg', 'gif', 'png'))
			->set_max_filesize($this->config['dir_banner_filesize'])
			->set_disallowed_content((isset($this->config['mime_triggers']) ? explode('|', $this->config['mime_triggers']) : false));

		$file = $upload->handle_upload('files.types.remote', $banner);

		$prefix = unique_id() . '_';
		$file->clean_filename('real', $prefix);

		if (sizeof($file->error))
		{
			$file->remove();
			$error = array_merge($error, $file->error);
			return false;
		}

		$destination = $this->dir_helper->get_banner_path();

		// Move file and overwrite any existing image
		$file->move_file($destination, true);

		return strtolower($file->get('realname'));
	}

	/**
	* Check than remote banner exists
	* called by banner_process()
	*
	* @param	string	$banner	The banner's remote url
	* @param	array	$error	The array error, passed by reference
	* @return	false|string	String if no errors, else false
	*/
	private function _banner_remote($banner, &$error)
	{
		if (!preg_match('#^(http|https|ftp)://#i', $banner))
		{
			$banner = 'http://' . $banner;
		}
		if (!preg_match('#^(http|https|ftp)://(?:(.*?\.)*?[a-z0-9\-]+?\.[a-z]{2,4}|(?:\d{1,3}\.){3,5}\d{1,3}):?([0-9]*?).*?\.(gif|jpg|jpeg|png)$#i', $banner))
		{
			$error[] = $this->language->lang('DIR_BANNER_URL_INVALID');
			return false;
		}

		// Get image dimensions
		if (($image_data = $this->imagesize->getImageSize($banner)) === false)
		{
			$error[] = $this->language->lang('DIR_BANNER_UNABLE_GET_IMAGE_SIZE');
			return false;
		}

		if (!empty($image_data) && ($image_data['width'] < 2 || $image_data['height'] < 2))
		{
			$error[] = $this->language->lang('DIR_BANNER_UNABLE_GET_IMAGE_SIZE');
			return false;
		}

		$width = $image_data['width'];
		$height = $image_data['height'];

		if ($width <= 0 || $height <= 0)
		{
			$error[] = $this->language->lang('DIR_BANNER_UNABLE_GET_IMAGE_SIZE');
			return false;
		}

		// Check image type
		$types		= \phpbb\files\upload::image_types();
		$extension	= strtolower(\phpbb\files\filespec::get_extension($banner));

		// Check if this is actually an image
		if ($file_stream = @fopen($banner, 'r'))
		{
			// Timeout after 1 second
			stream_set_timeout($file_stream, 1);
			// read some data to ensure headers are present
			fread($file_stream, 1024);
			$meta = stream_get_meta_data($file_stream);
			if (isset($meta['wrapper_data']['headers']) && is_array($meta['wrapper_data']['headers']))
			{
				$headers = $meta['wrapper_data']['headers'];
			}
			else if (isset($meta['wrapper_data']) && is_array($meta['wrapper_data']))
			{
				$headers = $meta['wrapper_data'];
			}
			else
			{
				$headers = array();
			}

			foreach ($headers as $header)
			{
				$header = preg_split('/ /', $header, 2);
				if (strtr(strtolower(trim($header[0], ':')), '_', '-') === 'content-type')
				{
					if (strpos($header[1], 'image/') !== 0)
					{
						$error[] = 'DIR_BANNER_URL_INVALID';
						fclose($file_stream);
						return false;
					}
					else
					{
						fclose($file_stream);
						break;
					}
				}
			}
		}
		else
		{
			$error[] = 'DIR_BANNER_URL_INVALID';
			return false;
		}

		if (!empty($image_data) && (!isset($types[$image_data['type']]) || !in_array($extension, $types[$image_data['type']])))
		{
			if (!isset($types[$image_data['type']]))
			{
				$error[] = $this->language->lang('UNABLE_GET_IMAGE_SIZE');
			}
			else
			{
				$error[] = $this->language->lang('DIR_BANNER_IMAGE_FILETYPE_MISMATCH', $types[$image_data['type']][0], $extension);
			}
			return false;
		}

		if (($this->config['dir_banner_width'] || $this->config['dir_banner_height']) && ($width > $this->config['dir_banner_width'] || $height > $this->config['dir_banner_height']))
		{
			$error[] = $this->language->lang('DIR_BANNER_WRONG_SIZE', $this->config['dir_banner_width'], $this->config['dir_banner_height'], $width, $height);
			return false;
		}

		return $banner;
	}

	/**
	* Delete a banner from server
	*
	* @param	string	$file	The file's name
	* @return	bool			True if delete success, else false
	*/
	private function _banner_delete($file)
	{
		if (file_exists($this->dir_helper->get_banner_path($file)))
		{
			@unlink($this->dir_helper->get_banner_path($file));
			return true;
		}

		return false;
	}

	/**
	* PageRank Lookup (Based on Google Toolbar for Mozilla Firefox)
	*
	* @copyright 2012 HM2K <hm2k@php.net>
	* @link http://pagerank.phurix.net/
	* @author James Wade <hm2k@php.net>
	* @version $Revision: 2.1 $
	* @require PHP 4.3.0 (file_get_contents)
	* @updated 06/10/11
	*
	* @param	string		$q	The website URL
	* @return	string			The calculated pagerank, or -1
	*/
	public function pagerank_process($q)
	{
		$googleDomains	= array('.com', '.com.tr', '.de', '.fr', '.be', '.ca', '.ro', '.ch');
		$seed			= $this->language->lang('SEED');
		$result			= 0x01020345;
		$len			= strlen($q);

		for ($i=0; $i<$len; $i++)
		{
			$result ^= ord($seed{$i%strlen($seed)}) ^ ord($q{$i});
			$result = (($result >> 23) & 0x1ff) | $result << 9;
		}

		if (PHP_INT_MAX != 2147483647)
		{
			$result = -(~($result & 0xFFFFFFFF) + 1);
		}

		$ch		= sprintf('8%x', $result);
		$url	= 'http://%s/tbr?client=navclient-auto&ch=%s&features=Rank&q=info:%s';
		$host	= 'toolbarqueries.google'.$googleDomains[mt_rand(0,count($googleDomains)-1)];

		$url	= sprintf($url,$host,$ch,$q);
		@$pr	= trim(file_get_contents($url,false));

		if (is_numeric(substr(strrchr($pr, ':'), 1)))
		{
			return substr(strrchr($pr, ':'), 1);
		}
		return '-1';
	}

	/**
	* List flags
	*
	* @param	string	$flag_path	The flag directory path
	* @param	string	$value		Selected flag
	* @return	string	$list		Html code
	*/
	public function get_dir_flag_list($flag_path, $value)
	{
		$list = '';

		$this->language->add_lang('directory_flags', 'ernadoo/phpbbdirectory');

		$flags = $this->dir_helper->preg_grep_keys('/^DIR_FLAG_CODE_/i', $this->language->get_lang_array());

		if (extension_loaded('intl'))
		{
			$locale = $this->language->lang('USER_LANG');

			$col = new \Collator($locale);
			$col->asort($flags);
		}
		else
		{
			asort($flags);
		}

		foreach ($flags as $file => $name)
		{
			$img_file = strtolower(substr(strrchr($file, '_'), 1)).'.png';

			if (file_exists($flag_path.$img_file))
			{
				$list .= '<option value="' . $img_file . '" ' . (($img_file == $value) ? 'selected="selected"' : '') . '>' . $name . '</option>';
			}
		}

		return $list;
	}

	/**
	* Display recents links added
	*
	* @return	null
	*/
	public function recents()
	{
		if ($this->config['dir_recent_block'])
		{
			$limit_sql		= $this->config['dir_recent_rows'] * $this->config['dir_recent_columns'];
			$exclude_array	= explode(',', str_replace(' ', '', $this->config['dir_recent_exclude']));

			$sql_array = array(
				'SELECT'	=> 'l.link_id, l.link_cat, l.link_url, l.link_user_id, l.link_comment, l. link_description, l.link_vote, l.link_note, l.link_view, l.link_time, l.link_name, l.link_thumb, u.user_id, u.username, u.user_colour, c.cat_name',
				'FROM'		=> array(
						DIR_LINK_TABLE	=> 'l'),
				'LEFT_JOIN'	=> array(
						array(
							'FROM'	=> array(USERS_TABLE	=> 'u'),
							'ON'	=> 'l.link_user_id = u.user_id'
						),
						array(
							'FROM'	=> array(DIR_CAT_TABLE => 'c'),
							'ON'	=> 'l.link_cat = c.cat_id'
						)
				),
				'WHERE'		=> $this->db->sql_in_set('l.link_cat', $exclude_array, true).' AND l.link_active = 1',
				'ORDER_BY'	=> 'l.link_time DESC, l.link_id DESC');

			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query_limit($sql, $limit_sql, 0);
			$num = 0;
			$rowset = array();

			while ($site = $this->db->sql_fetchrow($result))
			{
				$rowset[$site['link_id']] = $site;
			}
			$this->db->sql_freeresult($result);

			if (sizeof($rowset))
			{
				$this->template->assign_block_vars('block', array(
					'S_COL_WIDTH'			=> (100 / $this->config['dir_recent_columns']) . '%',
				));

				foreach ($rowset as $row)
				{
					if (($num % $this->config['dir_recent_columns']) == 0)
					{
						$this->template->assign_block_vars('block.row', array());
					}

					$this->template->assign_block_vars('block.row.col', array(
						'UC_THUMBNAIL'            => '<a href="'.$row['link_url'].'" onclick="window.open(\''.$this->helper->route('ernadoo_phpbbdirectory_view_controller', array('link_id' => (int) $row['link_id'])).'\'); return false;"><img src="'.$row['link_thumb'].'" title="'.$row['link_name'].'" alt="'.$row['link_name'].'" /></a>',
						'NAME'                    => $row['link_name'],
						'USER'                    => get_username_string('full', $row['link_user_id'], $row['username'], $row['user_colour']),
						'TIME'                    => ($row['link_time']) ? $this->user->format_date($row['link_time']) : '',
						'CAT'                     => $row['cat_name'],
						'COUNT'					  => $row['link_view'],
						'COMMENT'                 => $row['link_comment'],

						'U_CAT'                   => $this->helper->route('ernadoo_phpbbdirectory_page_controller', array('cat_id' => (int) $row['link_cat'])),
						'U_COMMENT'               => $this->helper->route('ernadoo_phpbbdirectory_comment_view_controller', array('link_id' => (int) $row['link_id'])),

						'L_DIR_SEARCH_NB_CLICKS'	=> $this->language->lang('DIR_SEARCH_NB_CLICKS', (int) $row['link_view']),
						'L_DIR_SEARCH_NB_COMMS'		=> $this->language->lang('DIR_SEARCH_NB_COMMS', (int) $row['link_comment']),
					));
					$num++;
				}

				while (($num % $this->config['dir_recent_columns']) != 0)
				{
					$this->template->assign_block_vars('block.row.col2', array());
					$num++;
				}
			}
		}
	}

	/**
	* Validate back link
	*
	* @param	string		$remote_url	Page URL contains the backlink
	* @param	bool		$optional	Link back is optional in this category?
	* @param	bool		$cron		This methos is called by con process?
	* @return	false|string			Either false if validation succeeded or a string which will be used as the error message (with the variable name appended)
	*/
	public function validate_link_back($remote_url, $optional, $cron = false)
	{
		if (!$cron)
		{
			if (empty($remote_url) && $optional)
			{
				return false;
			}

			if (!preg_match('#^http[s]?://(.*?\.)*?[a-z0-9\-]+\.[a-z]{2,4}#i', $remote_url))
			{
				return 'DIR_ERROR_WRONG_DATA_BACK';
			}
		}

		if (false === ($handle = @fopen($remote_url, 'r')))
		{
			return 'DIR_ERROR_NOT_FOUND_BACK';
		}

		$buff = '';

		// Read by packet, faster than file_get_contents()
		while (!feof($handle))
		{
			$buff .= fgets($handle, 256);

			if (stristr($buff, $this->config['server_name']))
			{
				@fclose($handle);
				return false;
			}
		}
		@fclose($handle);

		return 'DIR_ERROR_NO_LINK_BACK';
	}

	/**
	* Check, for website with backlink specified, if backlink is always here.
	* After $nb_check verification, website is deleted, otherwise, a notification is send to poster
	*
	* @param	int		$cat_id		The categoryID
	* @param	int		$nb_check	Number of check before demete a website
	* @param	int		$next_prune	Date of next auto check
	* @return	null
	*/
	private function _check($cat_id, $nb_check, $next_prune)
	{
		$del_array = $update_array = array();

		$sql_array = array(
			'SELECT'	=> 'link_id, link_cat, link_back, link_guest_email, link_nb_check, link_user_id, link_name, link_url, link_description, u.user_lang, u.user_dateformat',
			'FROM'		=> array(
					DIR_LINK_TABLE	=> 'l'),
			'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(USERS_TABLE	=> 'u'),
						'ON'	=> 'l.link_user_id = u.user_id'
					)
			),
			'WHERE'		=> 'l.link_back <> "" AND l.link_active = 1 AND l.link_cat = '  . (int) $cat_id);

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($this->validate_link_back($row['link_back'], false, true) !== false)
			{
				if (!$nb_check || ($row['link_nb_check']+1) >= $nb_check)
				{
					$del_array[] = $row['link_id'];
				}
				else
				{
					// A first table containing links ID to update
					$update_array[$row['link_id']] = $row;
				}
			}
		}
		$this->db->sql_freeresult($result);

		if (sizeof($del_array))
		{
			$this->del($cat_id, $del_array);
		}
		if (sizeof($update_array))
		{
			$this->_update_check($update_array, $next_prune);
		}
	}

	/**
	* Method called by cron task.
	*
	* @param	array	$cat_data	Information about category, from db
	* @return	null
	*/
	public function auto_check($cat_data)
	{
		global $phpbb_log;

		$sql = 'SELECT cat_name
			FROM ' . DIR_CAT_TABLE . '
			WHERE cat_id = ' . (int) $cat_data['cat_id'];
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row)
		{
			$next_prune = time() + ($cat_data['cat_cron_freq'] * 86400);

			$this->_check($cat_data['cat_id'], $cat_data['cat_cron_nb_check'], $next_prune);

			$sql = 'UPDATE ' . DIR_CAT_TABLE . "
				SET cat_cron_next = $next_prune
				WHERE cat_id = " . (int) $cat_data['cat_id'];
			$this->db->sql_query($sql);

			$phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DIR_AUTO_PRUNE', time(), array($row['cat_name']));
		}

		return;
	}

	/**
	* Update website verification number after a missing backlink, and send notificaton
	*
	* @param	array	$u_array	Information about website
	* @param	int		$next_prune	Date of next auto check
	* @return	null
	*/
	private function _update_check($u_array, $next_prune)
	{
		if (!class_exists('messenger'))
		{
			include($this->root_path . 'includes/functions_messenger.' . $this->php_ext);
		}

		$messenger = new \messenger(false);

		$sql = 'UPDATE ' . DIR_LINK_TABLE . '
			SET link_nb_check = link_nb_check + 1
			WHERE ' . $this->db->sql_in_set('link_id', array_keys($u_array));
		$this->db->sql_query($sql);

		foreach ($u_array as $data)
		{
			strip_bbcode($data['link_description']);

			$notification_data = array(
					'cat_name'			=> \ernadoo\phpbbdirectory\core\categorie::getname((int) $data['link_cat']),
					'link_id'			=> $data['link_id'],
					'link_user_id'		=> $data['link_user_id'],
					'link_name'			=> $data['link_name'],
					'link_url'			=> $data['link_url'],
					'link_description'	=> $data['link_description'],
					'next_cron' 		=> $this->user->format_date($next_prune, $data['user_dateformat']),
			);

			if ($data['link_nb_check'])
			{
				$this->notification->delete_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_error_cron', $notification_data);
			}

			// New notification system can't send mail to an anonymous user with an email address stored in another table than phpbb_users
			if ($data['link_user_id'] == ANONYMOUS)
			{
				$username = $email = $data['link_guest_email'];

				$messenger->template('@ernadoo_phpbbdirectory/directory_website_error_cron', $data['user_lang']);
				$messenger->to($email, $username);

				$messenger->assign_vars(array(
					'USERNAME'			=> htmlspecialchars_decode($username),
					'LINK_NAME'			=> $data['link_name'],
					'LINK_URL'			=> $data['link_url'],
					'LINK_DESCRIPTION'	=> $data['link_description'],
					'NEXT_CRON' 		=> $this->user->format_date($next_prune, $data['user_dateformat']),
				));

				$messenger->send(NOTIFY_EMAIL);
			}
			else
			{
				$this->notification->add_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_error_cron', $notification_data);
			}
		}
	}
}

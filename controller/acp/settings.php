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

class settings
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string Custom form action */
	protected $u_action;

	/** @var array */
	private $new_config;

	/** @var array */
	private $display_vars;

	/**
	* Constructor
	*
	* @param \phpbb\config\config		$config		Config object
	* @param \phpbb\log\log				$log		Log object
	* @param \phpbb\request\request		$request	Request object
	* @param \phpbb\template\template	$template	Template object
	* @param \phpbb\user				$user		User object
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\log\log $log, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->config	= $config;
		$this->log		= $log;
		$this->template	= $template;
		$this->user		= $user;
		$this->request	= $request;

		$this->_generate_config();
	}

	/**
	* Display config page
	*
	* @return null
	*/
	public function display_config()
	{
		// Output relevant page
		foreach ($this->display_vars['vars'] as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$this->template->assign_block_vars('options', array(
					'S_LEGEND'	=> true,
					'LEGEND'	=> (isset($this->user->lang[$vars])) ? $this->user->lang[$vars] : $vars)
				);

				continue;
			}

			$type = explode(':', $vars['type']);

			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain']))
			{
				$l_explain = (isset($this->user->lang[$vars['lang_explain']])) ? $this->user->lang[$vars['lang_explain']] : $vars['lang_explain'];
			}
			else if ($vars['explain'])
			{
				$l_explain = (isset($this->user->lang[$vars['lang'] . '_EXPLAIN'])) ? $this->user->lang[$vars['lang'] . '_EXPLAIN'] : '';
			}

			$this->template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($this->user->lang[$vars['lang']])) ? $this->user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'		=> $vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'		=> build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars),
			));

			unset($this->display_vars['vars'][$config_key]);
		}
	}

	/**
	* Validate config vars and update config table if needed
	*
	* @return null
	*/
	public function process()
	{
		$submit	= ($this->request->is_set_post('submit')) ? true : false;

		$this->new_config = $this->config;
		$cfg_array = ($this->request->is_set('config')) ? $this->request->variable('config', array('' => ''), true) : $this->new_config;
		$error = array();

		// We validate the complete config if whished
		validate_config_vars($this->display_vars['vars'], $cfg_array, $error);

		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		foreach ($this->display_vars['vars'] as $config_name => $null)
		{
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}

			$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

			if ($submit)
			{
				$this->config->set($config_name, $config_value);
			}
		}

		if ($submit)
		{
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'DIR_CONFIG_SETTINGS');

			trigger_error($this->user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->template->assign_vars(array(
			'L_TITLE'			=> $this->user->lang[$this->display_vars['title']],
			'L_TITLE_EXPLAIN'	=> $this->user->lang[$this->display_vars['title'] . '_EXPLAIN'],

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),

			'U_ACTION'			=> $this->u_action)
		);
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
	* Build config matrice
	*
	* @return null
	*/
	private function _generate_config()
	{
		$this->display_vars = array(
			'title'	=> 'ACP_DIRECTORY_SETTINGS',
			'vars'	=> array(
				'legend1' => 'DIR_PARAM',

				'dir_banner_width'					=> '',
				'dir_banner_height'					=> '',

				'dir_mail'							=> array('lang' => 'DIR_MAIL_VALIDATION',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
				'dir_activ_checkurl'				=> array('lang' => 'DIR_ACTIVE_CHECK',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
				'dir_activ_flag'					=> array('lang' => 'DIR_ACTIV_FLAG',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
				'dir_activ_rss'						=> array('lang' => 'DIR_ACTIV_RSS',			'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
				'dir_activ_pagerank'				=> array('lang' => 'DIR_ACTIV_PAGERANK',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
				'dir_show'							=> array('lang' => 'DIR_SHOW',				'validate' => 'int:1', 	'type' => 'text:3:3',		'explain' => false),
				'dir_length_describe'				=> array('lang' => 'DIR_MAX_DESC',			'validate' => 'int:1', 	'type' => 'text:3:3',		'explain' => false),
				'dir_new_time'						=> array('lang' => 'DIR_NEW_TIME',			'validate' => 'int', 	'type' => 'text:3:3',		'explain' => true),
				'dir_default_order'					=> array('lang' => 'DIR_DEFAULT_ORDER',		'validate' => 'string', 'type' => 'select',			'explain' => true, 'method' => 'get_order_list', 'params' => array('{CONFIG_VALUE}')),

				'legend2'							=> 'DIR_RECENT_GUEST',
				'dir_recent_block'					=> array('lang' => 'DIR_RECENT_ENABLE',		'validate' => 'bool',		'type' => 'radio:yes_no',	'explain' => true),
				'dir_recent_rows'					=> array('lang' => 'DIR_RECENT_ROWS',		'validate' => 'int:1',		'type' => 'text:3:3',		'explain' => false),
				'dir_recent_columns'				=> array('lang' => 'DIR_RECENT_COLUMNS',	'validate' => 'int:1',		'type' => 'text:3:3',		'explain' => false),
				'dir_recent_exclude'				=> array('lang' => 'DIR_RECENT_EXCLUDE',	'validate' => 'string',		'type' => 'text:6:99',			'explain' => true),

				'legend3'							=> 'DIR_ADD_GUEST',
				'dir_visual_confirm'				=> array('lang' => 'DIR_VISUAL_CONFIRM',	'validate' => 'bool',		'type' => 'radio:yes_no',	'explain' => true),
				'dir_visual_confirm_max_attempts'	=> array('lang' => 'DIR_MAX_ADD_ATTEMPTS',	'validate' => 'int:1:10',	'type' => 'text:3:3',		'explain' => true),

				'legend4'							=> 'DIR_THUMB_PARAM',
				'dir_activ_thumb'					=> array('lang' => 'DIR_ACTIVE_THUMB',			'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
				'dir_activ_thumb_remote'			=> array('lang' => 'DIR_ACTIVE_THUMB_REMOTE',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
				'dir_thumb_service'					=> array('lang' => 'DIR_THUMB_SERVICE',			'validate' => 'string', 'type' => 'select',			'explain' => true, 'method' => 'get_thumb_service_list', 'params' => array('{CONFIG_VALUE}')),
				'dir_thumb_service_reverse'			=> array('lang' => 'DIR_THUMB_SERVICE_REVERSE',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),

				'legend5'							=> 'DIR_COMM_PARAM',
				'dir_allow_bbcode'					=> array('lang' => 'DIR_ALLOW_BBCODE',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
				'dir_allow_flash'					=> array('lang' => 'DIR_ALLOW_FLASH',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
				'dir_allow_links'					=> array('lang' => 'DIR_ALLOW_LINKS',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
				'dir_allow_smilies'					=> array('lang' => 'DIR_ALLOW_SMILIES',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
				'dir_length_comments'				=> array('lang' => 'DIR_LENGTH_COMMENTS',	'validate' => 'int:2',	'type' => 'text:3:3',		'explain' => true),
				'dir_comments_per_page'				=> array('lang' => 'DIR_COMM_PER_PAGE',		'validate' => 'int:1',	'type' => 'text:3:3',		'explain' => false),

				'legend6'							=> 'DIR_BANN_PARAM',
				'dir_activ_banner'					=> array('lang' => 'DIR_ACTIV_BANNER',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
				'dir_banner'						=> array('lang' => 'DIR_MAX_BANN',			'validate' => 'int',	'type' => 'dimension:0',	'explain' => true, 'append' => ' ' . $this->user->lang['PIXEL']),
				'dir_banner_filesize'				=> array('lang' => 'DIR_MAX_SIZE',			'validate' => 'int:0',	'type' => 'number:0',		'explain' => true, 'append' => ' ' . $this->user->lang['BYTES']),
				'dir_storage_banner'				=> array('lang' => 'DIR_STORAGE_BANNER',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true),
			)
		);
	}
}

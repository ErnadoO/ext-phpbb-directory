<?php
/**
 *
 * @package Auto Backup
 * @copyright (c) 2013 Pico88
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace ernadoo\phpbbdirectory\cron\task\core;

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

class prune_categorie extends \phpbb\cron\task\base implements \phpbb\cron\task\parametrized
{
	/** @var \phpbb\db\driver\driver */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var string */
	protected $php_ext;

	/** @var \ernadoo\phpbbdirectory\core\link */
	protected $directory_cron;

	/** @var array */
	protected $cat_data;

	/**
	 * Constructor.
	 *
	 * @param \phpbb\config\config $config
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param string $php_ext
	 * @param \ernadoo\phpbbdirectory\core\link	directory link object
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, $php_ext, \ernadoo\phpbbdirectory\core\link $directory_cron)
	{
		$this->db 				= $db;
		$this->config 			= $config;
		$this->php_ext 			= $php_ext;
		$this->dir_cron 		= $directory_cron;
	}

	/**
	 * Manually set categorie data.
	 *
	 * @param array $cat_data Information about a categorie to be pruned.
	 */
	public function set_categorie_data($cat_data)
	{
		$this->cat_data = $cat_data;
	}

	/**
	 * Runs this cron task.
	 *
	 * @return null
	 */
	public function run()
	{
		$this->dir_cron->auto_check($this->cat_data);
	}

	/**
	 * Returns whether this cron task can run, given current board configuration.
	 *
	 * @return bool
	 */
	public function is_runnable()
	{
		return !$this->config['use_system_cron'] && $this->cat_data;
	}

	/**
	 * Returns whether this cron task should run now, because enough time
	 * has passed since it was last run.
	 *
	 * @return bool
	 */
	public function should_run()
	{
		return $this->cat_data['cat_cron_enable'] && $this->cat_data['cat_cron_next'] < time();
	}

	/**
	 * Returns parameters of this cron task as an array.
	 * The array has one key, c, whose value is id of the categorie to be pruned.
	 *
	 * @return array
	 */
	public function get_parameters()
	{
		return array('c' => $this->cat_data['cat_id']);
	}

	/**
	 * Parses parameters found in $request, which is an instance of
	 * \phpbb\request\request_interface.
	 *
	 * It is expected to have a key f whose value is id of the forum to be pruned.
	 *
	 * @param \phpbb\request\request_interface $request Request object.
	 *
	 * @return null
	 */
	public function parse_parameters(\phpbb\request\request_interface $request)
	{
		$this->cat_data = null;
		if ($request->is_set('c'))
		{
			$cat_id = $request->variable('c', 0);

			$sql = 'SELECT cat_id, cat_cron_enable, cat_cron_next, cat_cron_freq, cat_cron_nb_check
				FROM ' . DIR_CAT_TABLE . "
				WHERE cat_id = $cat_id";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row)
			{
				$this->cat_data = $row;
			}
		}
	}
}

<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\cron\task\core;

class prune_categorie extends \phpbb\cron\task\base implements \phpbb\cron\task\parametrized
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \ernadoo\phpbbdirectory\core\cron */
	protected $dir_cron;

	/** @var array */
	private $cat_data;

	/**
	* Constructor.
	*
	* @param \phpbb\config\config 				$config			Config object
	* @param \ernadoo\phpbbdirectory\core\cron	$directory_cron	PhpBB Directory extension link object
	*/
	public function __construct(\phpbb\config\config $config, \ernadoo\phpbbdirectory\core\cron $directory_cron)
	{
		$this->config 		= $config;
		$this->dir_cron 	= $directory_cron;
	}

	/**
	* Manually set categorie data.
	*
	* @param array $cat_data Information about a category to be pruned.
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
		return !$this->config['use_system_cron'] && !empty($this->cat_data);
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
	* The array has one key, c, whose value is id of the category to be pruned.
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
	* @param	\phpbb\request\request_interface	$request Request object.
	* @return	null
	*/
	public function parse_parameters(\phpbb\request\request_interface $request)
	{
		$this->cat_data = array();

		if ($request->is_set('c'))
		{
			$cat_id = $request->variable('c', 0);

			$this->cat_data = $this->dir_cron->get_cat($cat_id);
		}
	}
}

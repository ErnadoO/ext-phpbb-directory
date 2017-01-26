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

abstract class helper
{
	/** @var \phpbb\extension\manager */
	private $extension_manager;

	/** @var \phpbb\path_helper */
	private $path_helper;

	/** @var string */
	public $categories_table;

	/** @var string */
	public $comments_table;

	/** @var string */
	public $links_table;

	/** @var string */
	public $votes_table;

	/** @var string */
	public $watch_table;

	/** @var string */
	private $ext_name = 'ernadoo/phpbbdirectory';

	/**
	* Set the extension manager
	*
	* @param \phpbb\extension\manager	$phpbb_extension_manager
	* @return null
	*/
	public function set_extension_manager(\phpbb\extension\manager $phpbb_extension_manager)
	{
		$this->extension_manager	= $phpbb_extension_manager;
	}

	/**
	* Set the path helper
	*
	* @param \phpbb\path_helper	$path_helper
	* @return null
	*/
	public function set_path_helper(\phpbb\path_helper $path_helper)
	{
		$this->path_helper = $path_helper;
	}

	/**
	* Set the tables names
	*
	* @param string	$categories_table
	* @param string	$comments_table
	* @param string	$links_table
	* @param string	$votes_table
	* @param string	$watch_table
	* @return null
	*/
	public function set_tables($categories_table, $comments_table, $links_table, $votes_table, $watch_table)
	{
		$this->comments_table		= $comments_table;
		$this->links_table			= $links_table;
		$this->votes_table			= $votes_table;
		$this->watch_table			= $watch_table;
		$this->categories_table		= $categories_table;
	}
	/**
	* Return ext name
	*
	* @param	bool	$web_root_path Whether the path should be relative to web root
	* @return	string					Path to an extension
	*/
	public function get_ext_name($web_root_path = false)
	{
		return (($web_root_path) ? $this->path_helper->get_web_root_path() : '') . $this->extension_manager->get_extension_path($this->ext_name);
	}

	/**
	* Return path to resource image
	*
	* @param	string $type	is ressource type (flags|icons)
	* @param	string $image	is the resource to display
	* @return	string			The relative path to ressource
	*/
	public function get_img_path($type, $image = '')
	{
		return $this->get_ext_name(true) . 'images/' . $type . '/' . $image;
	}

	/**
	* Return path to banner
	*
	* @param	string	$banner	is the physical name
	* @return	string			The relative path to banner
	*/
	public function get_banner_path($banner = '')
	{
		return 'files/' . $this->get_ext_name() . 'banners/' . $banner;
	}

	/**
	* Return array entries that match the pattern
	*
	* @link http://php.net/manual/fr/function.preg-grep.php#95787
	*
	* @param 	string	$pattern	The pattern to search for
	* @param	array	$input		The input array
	* @return	array	$vals		Returns an array indexed using the keys from the input array
	*/
	public function preg_grep_keys($pattern, $input)
	{
		$keys = preg_grep($pattern, array_keys($input));
		$vals = array();
		foreach ($keys as $key)
		{
			$vals[$key] = $input[$key];
		}
		return $vals;
	}
}

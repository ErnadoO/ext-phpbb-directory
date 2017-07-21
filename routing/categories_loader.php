<?php
/**
 *
 * Pages extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ernadoo\phpbbdirectory\routing;

use phpbb\db\driver\driver_interface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
* Loads routes defined in Page's database.
*/
class categories_loader extends Loader
{
	/** @var driver_interface */
	protected $db;

	/** @var string */
	protected $pages_table;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db          Database connection
	* @param string                               $pages_table Table name
	* @access public
	*/
	public function __construct(driver_interface $db, $pages_table)
	{
		$this->db          = $db;
		$this->pages_table = $pages_table;
	}

	/**
	* Loads routes defined in directory_cats database.
	*
	* @param string      $resource Resource (not used, but required by parent interface)
	* @param string|null $type     The resource type
	*
	* @return RouteCollection A RouteCollection instance
	*
	* @api
	*/
	public function load($resource, $type = null)
	{
		$collection = new RouteCollection();

		$defaults = array(
			'_controller'	=> 'ernadoo.phpbbdirectory.controller.categories:view_route',
			'page'			=> 1,
			'sort_days'		=> 0,
			'sort_key'		=> 0,
			'sort_dir'		=> 0
		);

		$requirements = array(
			'cat_id'	=> '\d+',
			'page'		=> '\d+',
			'sort_days'	=> '\d+',
			'sort_key'	=> 'a|t|r|s|v|p',
			'sort_dir'	=> 'a|d',
		);

		$sql = 'SELECT cat_id, cat_route
			FROM ' . $this->pages_table;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$defaults['cat_id'] = $row['cat_id'];

			$route = new Route('directory/' . $row['cat_route'] . '/{page}/{sort_days}/{sort_key}/{sort_dir}');
			$route->setDefaults($defaults)->setRequirements($requirements);
			$collection->add('ernadoo_phpbbdirectory_dynamic_route_' . $row['cat_id'], $route);
		}
		$this->db->sql_freeresult();

		return $collection;
	}

	/**
	* {@inheritdoc}
	*
	* @api
	*/
	public function supports($resource, $type = null)
	{
		return $type === 'ernadoo_phpbbdirectory_route';
	}
}

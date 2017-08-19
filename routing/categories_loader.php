<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
* Loads routes defined in directory_cats table.
*/
class categories_loader extends Loader
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
	protected $categories_table;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db          		Database connection
	* @param string                               $categories_table Table name
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $categories_table)
	{
		$this->db          		= $db;
		$this->categories_table = $categories_table;
	}

	/**
	* {@inheritdoc}
	*
	* @api
	*/
	public function load($resource, $type = null)
	{
		$routes = new RouteCollection();

		$defaults = array(
			'_controller'	=> 'ernadoo.phpbbdirectory.controller.categories:view_route',
			'page'			=> 1,
			'sort_days'		=> 0,
			'sort_key'		=> '',
			'sort_dir'		=> '',
		);

		$requirements = array(
			'page'		=> '\d+',
			'sort_days'	=> '\d+',
		);

		$sql = 'SELECT cat_id, cat_route
			FROM ' . $this->categories_table;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$defaults['cat_id'] = $row['cat_id'];
			$path = 'directory/' . $row['cat_route'] . '/{page}/{sort_days}/{sort_key}/{sort_dir}';

			$route = new Route($path, $defaults, $requirements);
			$routes->add('ernadoo_phpbbdirectory_dynamic_route_' . $row['cat_id'], $route);
		}
		$this->db->sql_freeresult();

		return $routes;
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

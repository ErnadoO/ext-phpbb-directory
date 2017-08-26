<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\tests\routing;

class categories_routing_loader_test extends \phpbb_database_test_case
{
	/**
	* Define the extensions to be tested
	*
	* @return array vendor/name of extension(s) to test
	*/
	static protected function setup_extensions()
	{
		return array('ernadoo/phpbbdirectory');
	}

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\pages\routing\page_loader */
	protected $loader;

	/** @var \Symfony\Component\Routing\RouteCollection */
	protected $collection;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__ . '/fixtures/categories.xml');
	}

	public function setUp()
	{
		parent::setUp();

		$this->db = $this->new_dbal();

		// Instantiate the categories route loader
		$this->loader = new \ernadoo\phpbbdirectory\routing\categories_loader($this->db, 'phpbb_directory_cats');

		// Get a collection of categories' routes
		$this->collection = $this->get_categories_route_collection();
	}

	/**
	* Get the route collection from the categories_loader
	*
	* @return \Symfony\Component\Routing\RouteCollection
	*/
	public function get_categories_route_collection()
	{
		$collection = $this->loader->load('ernadoo_phpbbdirectory_route_controller', 'pages_extension');

		// Assert the collection is an instance of RouteCollection
		$this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $collection, 'A route collection instance could not be made.');

		return $collection;
	}

	/**
	* Data set for test_page_loader
	*
	* @return array
	*/
	public function page_loader_data()
	{
		return array(
			array(1, '/directory/categorie-1/{page}/{sort_days}/{sort_key}/{sort_dir}'),
			array(2, '/directory/categorie-2/{page}/{sort_days}/{sort_key}/{sort_dir}'),
			array(3, '/directory/categorie-and-stuff/{page}/{sort_days}/{sort_key}/{sort_dir}'),
		);
	}

	/**
	* @dataProvider page_loader_data
	*
	* @param int    $id       Id of a categorie
	* @param string $expected Expected route of a categorie
	*/
	public function test_page_loader($id, $expected)
	{
		// Get a route instance
		$route = $this->collection->get('ernadoo_phpbbdirectory_dynamic_route_' . $id);

		// Assert the roue is an instance of Route
		$this->assertInstanceOf('Symfony\Component\Routing\Route', $route, 'A route instance could not be made.');

		// Assert the route contains the expected path
		$this->assertSame($expected, $route->getPath());
	}
}

<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\tests\controller;

/**
* @group controller
*/

class phpbbdirectory_cats_test extends controller_base
{
	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__ . '/fixtures/fixture_categories.xml');
	}

	/**
	* Setup test environment
	*/
	public function setUp()
	{
		parent::setUp();

		global $phpbb_dispatcher, $phpbb_container;

		$phpbb_dispatcher = $this->dispatcher;

		$this->user->data['user_id'] = 2;
		$this->user->style['style_path'] = 'prosilver';

		$this->config['dir_default_order'] = 't d';
		$this->config['dir_show'] = 5;
	}

	public function get_controller()
	{
		global $table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch;
		global $phpbb_path_helper, $phpbb_extension_manager;

		$controller = new \ernadoo\phpbbdirectory\controller\categories(
			$this->db,
			$this->config,
			$this->lang,
			$this->template,
			$this->user,
			$this->helper,
			$this->request,
			$this->auth,
			$this->pagination,
			$this->core_categorie,
			$this->core_link
		);
		$controller->set_tables($table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch);
		$controller->set_path_helper($phpbb_path_helper);
		$controller->set_extension_manager($phpbb_extension_manager);

		return $controller;
	}

	/**
	* Test data for the test_display_cat_by_id() function
	*
	* @return array Array of test data
	*/
	public function display_cat_by_id_data()
	{
		return array(
			array(1, 1, 301), // old viewable cat
		);
	}

	/**
	* Test controller display
	*
	* @dataProvider display_cat_by_id_data
	*/
	public function test_display_cat_by_id($cat_id, $page, $status_code)
	{
		$controller = $this->get_controller();

		$response = $controller->view($cat_id, $page, 0, 0, 0);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals($status_code, $response->getStatusCode());
	}

	/**
	 * Test data for the test_display_cat_by_route() function
	 *
	 * @return array Array of test data
	 */
	public function display_cat_by_route_data()
	{
		return array(
			array(1, 1, 200, 'view_cat.html'), // viewable cat
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider display_cat_by_route_data
	 */
	public function test_display_cat_by_route($cat_id, $page, $status_code, $page_content)
	{
		$this->config['dir_default_order'] = 't d';
		$this->user->data['user_id'] = 1;

		$controller = $this->get_controller();

		$response = $controller->view_route($cat_id, $page, 0, 0, 0);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals($status_code, $response->getStatusCode());
		$this->assertEquals($page_content, $response->getContent());
	}

	/**
	* Test data for the test_display_cat_fails() function
	*
	* @return array Array of test data
	*/
	public function display_cat_fails_data()
	{
		return array(
			array(5, 1, 404, 'DIR_ERROR_NO_CATS'),
		);
	}

	/**
	* Test controller display throws 404 exceptions
	*
	* @dataProvider display_cat_fails_data
	*/
	public function test_display_cat_fails($cat_id, $page, $status_code, $page_content)
	{
		$controller = $this->get_controller();
		try
		{
			$controller->view($cat_id, $page, 0, 0, 0);
			$this->fail('The expected \phpbb\exception\http_exception was not thrown');
		}
		catch (\phpbb\exception\http_exception $exception)
		{
			$this->assertEquals($status_code, $exception->getStatusCode());
			$this->assertEquals($page_content, $exception->getMessage());
		}
	}

	/**
	* Test data for the test_category_one_page() function
	*
	* @return array Array of test data
	*/
	public function category_one_page_data()
	{
		return array(
			array(3, 'Catégorie 3', 1, 1),
		);
	}

	/**
	* Test base case scenario
	*
	* @dataProvider category_one_page_data
	*/
	function test_category_one_page($cat_id, $cat_name, $parent_cat_id, $nb_links)
	{
		$controller = $this->get_controller();
		$response = $controller->view_route($cat_id);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}

	/**
	* Test data for the test_category_no_links() function
	*
	* @return array Array of test data
	*/
	public function category_no_links_data()
	{
		return array(
			array(1, 'Catégorie 1', 0),
		);
	}

	/**
	* Test base case scenario
	*
	* @dataProvider category_no_links_data
	*/
	function test_category_no_links($cat_id, $cat_name, $nb_links)
	{
		$this->template->expects($this->at(3))
			->method('assign_block_vars')
			->withConsecutive(
			array('no_draw_link')
		);

		$controller = $this->get_controller();
		$response = $controller->view_route($cat_id);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}

	/**
	* Test data for the test_category_with_pages() function
	*
	* @return array Array of test data
	*/
	public function category_with_pages_data()
	{
		return array(
			array(2, 'Catégorie 2', 1, 'Catégorie 1', 6),
			array(2, 'Catégorie 2', 1, 'Catégorie 1', 6, 2),
		);
	}

	/**
	* Test base case scenario
	*
	* @dataProvider category_with_pages_data
	*/
	public function test_category_with_pages($cat_id, $cat_name, $parent_cat_id, $parent_cat_name, $nb_links, $page = 1, $sort_days = 0)
	{
		$controller = $this->get_controller();
		$response = $controller->view_route($cat_id, $page, $sort_days);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}

	protected function tearDown()
	{
		parent::tearDown();
	}
}

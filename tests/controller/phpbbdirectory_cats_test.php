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
		return $this->createXMLDataSet(__DIR__ . '/fixtures/fixture_categories.xml');
	}

	/**
	* Setup test environment
	*/
	public function setUp()
	{
		parent::setUp();

		$this->config['dir_default_order']		= 't d';
		$this->config['dir_banner_width']		= 1;
		$this->config['dir_banner_height']		= 1;
		$this->config['dir_show']				= 5;
		$this->config['dir_activ_flag']			= 1;
		$this->config['dir_activ_rss']			= 1;
		$this->config['dir_activ_thumb']		= 1;
		$this->config['dir_activ_thumb_remote']	= 1;
	}

	public function get_controller($user_id = 1)
	{
		global $table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch;
		global $phpEx, $phpbb_root_path;

		$this->user->data['user_id']		= $user_id;
		$this->user->data['is_registered']	= ($this->user->data['user_id'] != ANONYMOUS && ($this->user->data['user_type'] == USER_NORMAL || $this->user->data['user_type'] == USER_FOUNDER)) ? true : false;

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
		$controller->set_path_helper($this->phpbb_path_helper);
		$controller->set_extension_manager($this->phpbb_extension_manager);

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
		$response = $controller->view($cat_id, $page);

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
		$controller = $this->get_controller();
		$response = $controller->view_route($cat_id, $page);

		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals($status_code, $response->getStatusCode());
		$this->assertEquals($page_content, $response->getContent());
	}

	/**
	* Test data for the test_display_cat_fails() function
	*
	* @return array Array of test data
	*/
	public function display_cat_fails_error()
	{
		return array(
			array(5, 1, 404, 'DIR_ERROR_NO_CATS'),
		);
	}

	/**
	* Test controller display throws 404 exceptions
	*
	* @dataProvider display_cat_fails_error
	*/
	public function test_display_cat_fails($cat_id, $page, $status_code, $page_content)
	{
		$controller = $this->get_controller();

		try
		{
			$controller->view($cat_id, $page);
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
			array(3, 1, '200', 'view_cat.html'),
			array(3, 2, '200', 'view_cat.html'),
		);
	}

	/**
	* Test base case scenario
	*
	* @dataProvider category_one_page_data
	*/
	function test_category_one_page($cat_id, $user_id, $status_code, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$controller = $this->get_controller($user_id);
		$response = $controller->view_route($cat_id);

		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals($status_code, $response->getStatusCode());
		$this->assertEquals($page_content, $response->getContent());
	}

	/**
	* Test data for the test_category_no_links() function
	*
	* @return array Array of test data
	*/
	public function category_no_links_data()
	{
		return array(
			array(1, 200, 'view_cat.html'),
		);
	}

	/**
	* Test base case scenario
	*
	* @dataProvider category_no_links_data
	*/
	function test_category_no_links($cat_id, $status_code, $page_content)
	{
		$this->template->expects($this->at(3))
			->method('assign_block_vars')
			->withConsecutive(
			array('no_draw_link')
		);

		$controller = $this->get_controller();
		$response = $controller->view_route($cat_id);

		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals($status_code, $response->getStatusCode());
		$this->assertEquals($page_content, $response->getContent());
	}

	/**
	* Test data for the test_category_with_pages() function
	*
	* @return array Array of test data
	*/
	public function category_with_pages_data()
	{
		return array(
			array(2, 1, 1, 200, 'view_cat.html'),
			array(2, 1, 2, 200, 'view_cat.html'),
			array(2, 2, 2, 200, 'view_cat.html'),
		);
	}

	/**
	* Test base case scenario
	*
	* @dataProvider category_with_pages_data
	*/
	public function test_category_with_pages($cat_id, $page, $user_id, $status_code, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$controller = $this->get_controller($user_id);
		$response = $controller->view_route($cat_id, $page);

		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals($status_code, $response->getStatusCode());
		$this->assertEquals($page_content, $response->getContent());
	}

	protected function tearDown()
	{
		parent::tearDown();
	}
}

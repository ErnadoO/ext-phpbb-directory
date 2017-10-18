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

class phpbbdirectory_links_test extends controller_base
{
	public function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/fixtures/fixture_links.xml');
	}

	/**
	 * Setup test environment
	 */
	public function setUp()
	{
		parent::setUp();

		$this->config = new \phpbb\config\config(array(
			'dir_visual_confirm'	=> 1,
			'captcha_plugin' 		=> 'core.captcha.plugins.nogd',
		));
	}

	public function get_controller($user_id = 1)
	{
		global $table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch;
		global $phpEx, $phpbb_root_path;

		$this->user->data['user_id']		= $user_id;
		$this->user->data['is_registered']	= ($this->user->data['user_id'] != ANONYMOUS && ($this->user->data['user_type'] == USER_NORMAL || $this->user->data['user_type'] == USER_FOUNDER)) ? true : false;

			$controller = new \ernadoo\phpbbdirectory\controller\links(
				$this->db,
				$this->config,
				$this->lang,
				$this->template,
				$this->user,
				$this->helper,
				$this->request,
				$this->auth,
				$this->captcha_factory,
				$this->core_categorie,
				$this->core_link,
				$phpbb_root_path,
				$phpEx
			);
			$controller->set_tables($table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch);
			$controller->set_path_helper($this->phpbb_path_helper);
			$controller->set_extension_manager($this->phpbb_extension_manager);

			return $controller;
	}

	/**
	 * Test data for the test_delete_link_error() function
	 *
	 * @return array Array of test data
	 */
	public function delete_link_error_data()
	{
		return array(
			array(1, 1, 1, 403, 'DIR_ERROR_NOT_AUTH'),
			array(1, 111, 1, 404, 'DIR_ERROR_NO_LINKS'),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider delete_link_error_data
	 */
	public function test_delete_link_error($cat_id, $link_id, $user_id, $status_code, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$controller = $this->get_controller($user_id);

		try
		{
			$response = $controller->delete_link($cat_id, $link_id);
			$this->fail('The expected \phpbb\exception\http_exception was not thrown');
		}
		catch (\phpbb\exception\http_exception $exception)
		{
			$this->assertEquals($status_code, $exception->getStatusCode());
			$this->assertEquals($page_content, $exception->getMessage());
		}
	}

	/**
	 * Test data for the test_edit_link() function
	 *
	 * @return array Array of test data
	 */
	public function edit_link_data()
	{
		return array(
			array(1, 1, 2, 200, 'add_site.html'),
			array(2, 2, 2, 200, 'add_site.html'),
			array(2, 5, 3, 200, 'add_site.html'),
			array(2, 5, 2, 200, 'add_site.html'),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider edit_link_data
	 */
	public function test_edit_link($cat_id, $link_id, $user_id, $status_code, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$controller = $this->get_controller($user_id);
		$response = $controller->edit_link($cat_id, $link_id);

		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals($status_code, $response->getStatusCode());
		$this->assertEquals($page_content, $response->getContent());
	}

	/**
	 * Test data for the test_edit_link_error() function
	 *
	 * @return array Array of test data
	 */
	public function edit_link_error_data()
	{
		return array(
			array(1, 1, 1, 403, 'DIR_ERROR_NOT_AUTH'),
			array(1, 111, 1, 404, 'DIR_ERROR_NO_LINKS'),
			array(1, 111, 2, 404, 'DIR_ERROR_NO_LINKS'),
			array(1, 1, 3, 403, 'DIR_ERROR_NOT_AUTH'),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider edit_link_error_data
	 */
	public function test_edit_link_error($cat_id, $link_id, $user_id, $status_code, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$controller = $this->get_controller($user_id);

		try
		{
			$response = $controller->edit_link($cat_id, $link_id);
			$this->fail('The expected \phpbb\exception\http_exception was not thrown');
		}
		catch (\phpbb\exception\http_exception $exception)
		{
			$this->assertEquals($status_code, $exception->getStatusCode());
			$this->assertEquals($page_content, $exception->getMessage());
		}
	}

	/**
	 * Test data for the test_new_link() function
	 *
	 * @return array Array of test data
	 */
	public function new_link_data()
	{
		return array(
			array(1 ,2, 200, 'add_site.html'),
			array(2 ,2, 200, 'add_site.html'),
			array(3 ,2, 200, 'add_site.html'),
			array(1 ,1, 200, 'add_site.html'),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider new_link_data
	 */
	public function test_new_link($cat_id, $user_id, $status_code, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$controller = $this->get_controller($user_id);
		$response = $controller->new_link($cat_id);

		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals($status_code, $response->getStatusCode());
		$this->assertEquals($page_content, $response->getContent());
	}

	/**
	 * Test data for the test_new_link_error() function
	 *
	 * @return array Array of test data
	 */
	public function new_link_error_data()
	{
		return array(
			array(1 ,4, 403, 'DIR_ERROR_NOT_AUTH'),
			array(2 ,4, 403, 'DIR_ERROR_NOT_AUTH'),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider new_link_error_data
	 */
	public function test_new_link_error($cat_id, $user_id, $status_code, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$controller = $this->get_controller($user_id);

		try
		{
			$response = $controller->new_link($cat_id);
			$this->fail('The expected \phpbb\exception\http_exception was not thrown');
		}
		catch (\phpbb\exception\http_exception $exception)
		{
			$this->assertEquals($status_code, $exception->getStatusCode());
			$this->assertEquals($page_content, $exception->getMessage());
		}
	}

	/**
	 * Test data for the test_view_link() function
	 *
	 * @return array Array of test data
	 */
	public function view_link_data()
	{
		return array(
			array(1, 301),
		);
	}

	/**
	 * Test controller display
	 *
	 * @runInSeparateProcess
	 * @dataProvider view_link_data
	 */
	public function test_view_link($link_id, $status_code)
	{
		$controller = $this->get_controller();

		$response = $controller->view_link($link_id);
		$this->assertEquals($status_code, $response->getStatusCode());
	}

	/**
	 * Test data for the test_view_link_error() function
	 *
	 * @return array Array of test data
	 */
	public function view_link_error_data()
	{
		return array(
			array(10, 404, 'DIR_ERROR_NO_LINKS'),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider view_link_error_data
	 */
	public function test_view_link_error($link_id, $status_code, $page_content)
	{
		$controller = $this->get_controller();

		try
		{
			$response = $controller->view_link($link_id);
			$this->fail('The expected \phpbb\exception\http_exception was not thrown');
		}
		catch (\phpbb\exception\http_exception $exception)
		{
			$this->assertEquals($status_code, $exception->getStatusCode());
			$this->assertEquals($page_content, $exception->getMessage());
		}
	}

	/**
	 * Test data for the test_vote_link() function
	 *
	 * @return array Array of test data
	 */
	public function vote_link_data()
	{
		return array(
			array(1, 1, 2, 200, 'DIR_VOTE_OK<br /><br />DIR_CLICK_RETURN_CAT'),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider vote_link_data
	 */
	public function test_vote_link($cat_id, $link_id, $user_id, $status_code, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$controller = $this->get_controller($user_id);
		$response = $controller->vote_link($cat_id, $link_id);

		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals($status_code, $response->getStatusCode());
		$this->assertEquals($page_content, $response->getContent());
	}

	/**
	 * Test data for the test_vote_link_error() function
	 *
	 * @return array Array of test data
	 */
	public function vote_link_error_data()
	{
		return array(
			array(1, 1, 3, 403, 'DIR_ERROR_NOT_AUTH'),
			array(2, 5, 2, 403, 'DIR_ERROR_NOT_AUTH'),
			array(1, 4, 2, 403, 'DIR_ERROR_VOTE'),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider vote_link_error_data
	 */
	public function test_vote_link_error($cat_id, $link_id, $user_id, $status_code, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$controller = $this->get_controller($user_id);

		try
		{
			$response = $controller->vote_link($cat_id, $link_id);
			$this->fail('The expected \phpbb\exception\http_exception was not thrown');
		}
		catch (\phpbb\exception\http_exception $exception)
		{
			$this->assertEquals($status_code, $exception->getStatusCode());
			$this->assertEquals($page_content, $exception->getMessage());
		}
	}

	/**
	 * Test data for the test_return_banner() function
	 *
	 * @return array Array of test data
	 */
	public function return_banner_data()
	{
		return array(
			array('jpg', 200),
			array('png', 404),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider return_banner_data
	 */
	public function test_return_banner($banner, $status_code)
	{
		$controller = $this->get_controller();

		$response = $controller->return_banner($banner);
		$this->assertEquals($status_code, $response->getStatusCode());
	}

	protected function tearDown()
	{
		parent::tearDown();
	}
}

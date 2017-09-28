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

class phpbbdirectory_comments_test extends controller_base
{
	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__ . '/fixtures/fixture_comments.xml');
	}

	/**
	* Setup test environment
	*/
	public function setUp()
	{
		parent::setUp();

		$this->user->data['user_id'] = 2;
		$this->user->style['style_path'] = 'prosilver';

		$this->config['dir_default_order'] = 't d';
		$this->config['dir_comments_per_page'] = 5;
	}

	public function get_controller()
	{
		global $table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch;
		global $phpEx, $phpbb_root_path;

		$controller = new \ernadoo\phpbbdirectory\controller\comments(
			$this->db,
			$this->config,
			$this->lang,
			$this->template,
			$this->user,
			$this->helper,
			$this->request,
			$this->auth,
			$this->pagination,
		    $this->captcha_factory,
			$this->core_categorie,
			$this->core_comment,
		    $phpbb_root_path,
		    $phpEx
		);
		$controller->set_tables($table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch);
		$controller->set_path_helper($this->phpbb_path_helper);
		$controller->set_extension_manager($this->phpbb_extension_manager);

		return $controller;
	}

	/**
	* Test data for the test_display_comments() function
	*
	* @return array Array of test data
	*/
	public function display_comments_data()
	{
		return array(
		    array(1, 1, 200, 'comments.html'),
		    array(1, 2, 200, 'comments.html'),
		    array(4, 1, 200, 'comments.html'),
		);
	}

	/**
	* Test controller display
	*
	* @dataProvider display_comments_data
	*/
	public function test_display_comments($link_id, $page, $status_code, $page_content)
	{
		$controller = $this->get_controller();

		$response = $controller->view($link_id, $page);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals($status_code, $response->getStatusCode());
		$this->assertEquals($page_content, $response->getContent());
	}

	/**
	* Test data for the test_display_comments_no_link() function
	*
	* @return array Array of test data
	*/
	public function display_comments_no_link_data()
	{
	    return array(
	        array(3, 1, 404, 'DIR_ERROR_NO_LINKS'),
	    );
	}

	/**
	* Test controller display
	*
	* @dataProvider display_comments_no_link_data
	*/
	public function test_display_comments_no_link($link_id, $page, $status_code, $page_content)
	{
	    $controller = $this->get_controller();

	    try
	    {
	        $response = $controller->view($link_id, $page);
	        $this->fail('The expected \phpbb\exception\http_exception was not thrown');
	    }
	    catch (\phpbb\exception\http_exception $exception)
	    {
	        $this->assertEquals($status_code, $exception->getStatusCode());
	        $this->assertEquals($page_content, $exception->getMessage());
	    }
	}

	/**
	* Test data for the test_display_comments_disabled() function
	*
	* @return array Array of test data
	*/
	public function display_comments_disabled_data()
	{
	    return array(
	        array(2, 1, 403, 'DIR_ERROR_NOT_AUTH'),
	        array(2, 2, 403, 'DIR_ERROR_NOT_AUTH'),
	    );
	}

	/**
	* Test controller display
	*
	* @dataProvider display_comments_disabled_data
	*/
	public function test_display_comments_disabled($link_id, $page, $status_code, $page_content)
	{
	    $controller = $this->get_controller();

	    try
	    {
	        $response = $controller->view($link_id, $page);
	        $this->fail('The expected \phpbb\exception\http_exception was not thrown');
	    }
	    catch (\phpbb\exception\http_exception $exception)
	    {
	        $this->assertEquals($status_code, $exception->getStatusCode());
	        $this->assertEquals($page_content, $exception->getMessage());
	    }
	}

	/**
	* Test data for the test_display_new_comment() function
	*
	* @return array Array of test data
	*/
	public function display_edit_comment_data()
	{
	    return array(
	        array(1, 1, 2, 200, 'comments.html'),
	    );
	}

	/**
	* Test controller display
	*
	* @dataProvider display_edit_comment_data
	*/
	public function test_display_edit_comment($link_id, $comment_id, $user_id, $status_code, $page_content)
	{
	    $this->user->data['user_id'] = $user_id;
	    $this->user->data['is_registered'] = true;

	    $user_data = $this->auth->obtain_user_data($user_id);
	    $this->auth->acl($user_data);

	    $controller = $this->get_controller();
	    $response = $controller->edit_comment($link_id, $comment_id);

	    $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
	    $this->assertEquals($status_code, $response->getStatusCode());
	    $this->assertEquals($page_content, $response->getContent());
	}

	/**
	* Test data for the test_display_edit_comment_not_auth() function
	*
	* @return array Array of test data
	*/
	public function display_edit_comment_not_auth_data()
	{
	    return array(
	        array(1, 1, 1, 403, 'DIR_ERROR_NOT_AUTH'),
	    );
	}

	/**
	* Test controller display
	*
	* @dataProvider display_edit_comment_not_auth_data
	*/
	public function test_display_edit_comment_not_auth($link_id, $comment_id, $user_id, $status_code, $page_content)
	{
	    $this->user->data['user_id'] = $user_id;
	    $this->user->data['is_registered'] = true;

	    $user_data = $this->auth->obtain_user_data($user_id);
	    $this->auth->acl($user_data);

	    $controller = $this->get_controller();

	    try
	    {
	        $response = $controller->edit_comment($link_id, $comment_id);
	        $this->fail('The expected \phpbb\exception\http_exception was not thrown');
	    }
	    catch (\phpbb\exception\http_exception $exception)
	    {
	        $this->assertEquals($status_code, $exception->getStatusCode());
	        $this->assertEquals($page_content, $exception->getMessage());
	    }
	}

	/**
	 * Test data for the test_display_delete_comment_not_auth() function
	 *
	 * @return array Array of test data
	 */
	public function display_delete_comment_not_auth_data()
	{
		return array(
			array(1, 1, 1, 403, 'DIR_ERROR_NOT_AUTH'),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider display_delete_comment_not_auth_data
	 */
	public function test_display_delete_comment_not_auth($link_id, $comment_id, $user_id, $status_code, $page_content)
	{
		$this->user->data['user_id'] = $user_id;
		$this->user->data['is_registered'] = true;

		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$controller = $this->get_controller();
		try
		{
			$response = $controller->delete_comment($link_id, $comment_id);
			$this->fail('The expected \phpbb\exception\http_exception was not thrown');
		}
		catch (\phpbb\exception\http_exception $exception)
		{
			$this->assertEquals($status_code, $exception->getStatusCode());
			$this->assertEquals($page_content, $exception->getMessage());
		}
	}

	protected function tearDown()
	{
		parent::tearDown();
	}
}

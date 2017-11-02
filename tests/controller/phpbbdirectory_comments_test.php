<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\tests\controller
{

/**
* @group controller
*/

class phpbbdirectory_comments_test extends controller_base
{
	public function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/fixtures/fixture_comments.xml');
	}

	/**
	* Setup test environment
	*/
	public function setUp()
	{
		parent::setUp();

		$this->config = new \phpbb\config\config(array(
			'dir_default_order'					=> ' t d',
			'dir_comments_per_page' 			=> 5,
			'dir_visual_confirm'				=> 1,
			'captcha_plugin' 					=> 'core.captcha.plugins.nogd',
			'dir_length_comments'				=> 255,
			'dir_visual_confirm_max_attempts'	=> 2
		));
	}

	public function get_controller($user_id = 1)
	{
		global $table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch;
		global $phpEx, $phpbb_root_path;

		$this->user->data['user_id']		= $user_id;
		$this->user->data['is_registered']	= ($this->user->data['user_id'] != ANONYMOUS && ($this->user->data['user_type'] == USER_NORMAL || $this->user->data['user_type'] == USER_FOUNDER)) ? true : false;

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
		    array(1, 2, 1, 200, 'comments.html'),
		    array(1, 2, 2, 200, 'comments.html'),
		    array(4, 2, 1, 200, 'comments.html'),
			array(4, 1, 1, 200, 'comments.html'),
		);
	}

	/**
	* Test controller display
	*
	* @dataProvider display_comments_data
	*/
	public function test_display_comments($link_id, $user_id, $page, $status_code, $page_content)
	{
		$controller = $this->get_controller($user_id);

		$response = $controller->view($link_id, $page);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals($status_code, $response->getStatusCode());
		$this->assertEquals($page_content, $response->getContent());
	}

	/**
	* Test data for the test_display_comments_error() function
	*
	* @return array Array of test data
	*/
	public function display_comments_error_data()
	{
	    return array(
	        array(3, 1, 404, 'DIR_ERROR_NO_LINKS'),
	    	array(2, 1, 403, 'DIR_ERROR_NOT_AUTH'),
	    	array(2, 2, 403, 'DIR_ERROR_NOT_AUTH'),
	    );
	}

	/**
	* Test controller display
	*
	* @dataProvider display_comments_error_data
	*/
	public function test_display_comments_error($link_id, $page, $status_code, $page_content)
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
	* Test data for the test_display_edit_comment() function
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
	    $user_data = $this->auth->obtain_user_data($user_id);
	    $this->auth->acl($user_data);

	    $controller = $this->get_controller($user_id);
	    $response = $controller->edit_comment($link_id, $comment_id);

	    $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
	    $this->assertEquals($status_code, $response->getStatusCode());
	    $this->assertEquals($page_content, $response->getContent());
	}

	/**
	 * Test data for the test_submit_comment() function
	 *
	 * @return array Array of test data
	 */
	public function submit_comment_data()
	{
		return array(
			array(1, 1, 2, 'Bar', 200, 'DIR_EDIT_COMMENT_OK<br /><br />DIR_CLICK_RETURN_COMMENT'),
			array(1, false, 2, 'Foo', 200, 'DIR_NEW_COMMENT_OK<br /><br />DIR_CLICK_RETURN_COMMENT'),
			array(1, false, 2, '', 200, 'comments.html'),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider submit_comment_data
	 */
	public function test_submit_comment($link_id, $comment_id, $user_id, $text, $status_code, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		if ($comment_id)
		{
			$func = 'edit_comment';
			$_POST['update_comment'] = true;
		}
		else
		{
			$func = 'new_comment';
			$_POST['submit_comment'] = true;
		}

		$_POST['message'] = $text;

		$this->type_cast_helper = $this->createMock('\phpbb\request\type_cast_helper_interface');
		$this->request = new \phpbb\request\request($this->type_cast_helper, false);

		$controller = $this->get_controller($user_id);
		$response = $controller->{$func}($link_id, $comment_id);

		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals($status_code, $response->getStatusCode());
		$this->assertEquals($page_content, $response->getContent());
	}

	/**
	 * Test data for the test_submit_comment_error() function
	 *
	 * @return array Array of test data
	 */
	public function submit_comment_error_data()
	{
		return array(
			array(1, 1, 1, 403, 'DIR_ERROR_NOT_AUTH'),
			array(1, false, 1, 403, 'DIR_ERROR_NOT_AUTH'),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider submit_comment_error_data
	 */
	public function test_submit_comment_error($link_id, $comment_id, $user_id, $status_code, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		if ($comment_id)
		{
			$func = 'edit_comment';
		}
		else
		{
			$func = 'new_comment';
		}

		$this->type_cast_helper = $this->createMock('\phpbb\request\type_cast_helper_interface');
		$this->request = new \phpbb\request\request($this->type_cast_helper, false);

		$controller = $this->get_controller($user_id);


		try
		{
			$response = $controller->{$func}($link_id, $comment_id);
			$this->fail('The expected \phpbb\exception\http_exception was not thrown');
		}
		catch (\phpbb\exception\http_exception $exception)
		{
			$this->assertEquals($status_code, $exception->getStatusCode());
			$this->assertEquals($page_content, $exception->getMessage());
		}
	}

	/**
	* Test data for the test_display_edit_comment_error() function
	*
	* @return array Array of test data
	*/
	public function display_edit_comment_error_data()
	{
	    return array(
	        array(1, 1, 1, 403, 'DIR_ERROR_NOT_AUTH'),
	    );
	}

	/**
	* Test controller display
	*
	* @dataProvider display_edit_comment_error_data
	*/
	public function test_display_edit_comment_error($link_id, $comment_id, $user_id, $status_code, $page_content)
	{
	    $user_data = $this->auth->obtain_user_data($user_id);
	    $this->auth->acl($user_data);

	    $controller = $this->get_controller($user_id);

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
	 * Test data for the test_display_delete_comment() function
	 *
	 * @return array Array of test data
	 */
	public function display_delete_comment_data()
	{
		return array(
			array(1, 1, 2, 200, 'DIR_COMMENT_DELETE_OK<br /><br />DIR_CLICK_RETURN_COMMENT'),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider display_delete_comment_data
	 */
	public function test_display_delete_comment($link_id, $comment_id, $user_id, $status_code, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$controller = $this->get_controller($user_id);
		$response = $controller->delete_comment($link_id, $comment_id);

		$this->assertEquals($status_code, $response->getStatusCode());
		$this->assertEquals($page_content, $response->getContent());

	}

	/**
	 * Test data for the test_display_delete_comment_error() function
	 *
	 * @return array Array of test data
	 */
	public function display_delete_comment_error_data()
	{
		return array(
			array(1, 1, 1, 403, 'DIR_ERROR_NOT_AUTH'),
		);
	}

	/**
	 * Test controller display
	 *
	 * @dataProvider display_delete_comment_error_data
	 */
	public function test_display_delete_comment_error($link_id, $comment_id, $user_id, $status_code, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$controller = $this->get_controller($user_id);

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
}
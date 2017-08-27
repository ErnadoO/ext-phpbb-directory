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

class phpbbdirectory_base_test extends controller_base
{
	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__ . '/fixtures/fixture_base.xml');
	}

	/**
	* Setup test environment
	*/
	public function setUp()
	{
		parent::setUp();

		global $phpbb_dispatcher, $phpbb_container;

		$phpbb_dispatcher = $this->dispatcher;

		$this->user->style['style_path'] = 'prosilver';
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
	* Test data for the test_display_base() function
	*
	* @return array Array of test data
	*/
	public function display_base_data()
	{
		return array(
			array(200, 'body.html'),
		);
	}

	/**
	* Test controller display
	*
	* @dataProvider display_base_data
	*/
	public function test_display_base($status_code, $page_content)
	{
		$controller = $this->get_controller();
		$response = $controller->base();
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals($status_code, $response->getStatusCode());
		$this->assertEquals($page_content, $response->getContent());
	}

	/**
	* Test base case scenario
	*
	*/
	public function test_for_root_categories()
	{
		$this->template->expects($this->exactly(2))
		->method('assign_vars')
		->withConsecutive(
			array(
				array( // navlinks
					'S_PHPBB_DIRECTORY'	=> true,
				),
			),
			array(
				array(
					'S_AUTH_ADD'		=> null,
					'S_AUTH_SEARCH'		=> null,
					'S_HAS_SUBCAT'		=> true,
					'S_ROOT'			=> true,

					'U_MAKE_SEARCH'		=> 'ernadoo_phpbbdirectory_search_controller',
				)
			)
		);

		$controller = $this->get_controller();

		$this->template->expects($this->exactly(2))
		->method('assign_block_vars')
		->withConsecutive(
			array('cat',
				array( //expected
					'CAT_NAME'				=> 'Catégorie 1',
					'CAT_DESC'				=> 'Description_1',
					'CAT_LINKS'				=> 7,
					'CAT_IMG'				=> $controller->get_img_path('icons', 'icon_maison.gif'),

					'U_CAT'					=> 'ernadoo_phpbbdirectory_dynamic_route_1',
				)
			),
			array('cat.subcat',
				array( //expected
					'U_CAT'		=> 'ernadoo_phpbbdirectory_dynamic_route_2',
					'CAT_NAME'	=> 'Catégorie 2',
					'CAT_LINKS'	=> 6
				)
			)
		);

		$response = $controller->base();
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}

	protected function tearDown()
	{
		parent::tearDown();
	}
}

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

		$this->user->style['style_path'] 	= 'prosilver';
		$this->config['dir_recent_block']	= 1;
		$this->config['dir_recent_rows']	= 1;
		$this->config['dir_recent_columns'] = 3;
		$this->config['dir_recent_exclude'] = '';
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
		$assign_block_vars = 2;

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

		$this->template->expects($this->exactly(7))
		->method('assign_block_vars')
		->withConsecutive(
			array('cat'),
			array('cat.subcat'),
			array('block'),
			array('block.row'),
			array('block.row.col', array(
				'UC_THUMBNAIL'            => '<a href="" onclick="window.open(\'ernadoo_phpbbdirectory_view_controller\'); return false;"><img src="" title="phpbb-services" alt="phpbb-services" /></a>',
				'NAME'                    => 'phpbb-services',
				'USER'                    => '<a href="phpBB/memberlist.php?mode=viewprofile&amp;u=2" class="username"></a>',
				'TIME'                    => '',
				'CAT'                     => 'Catégorie 2',
				'COUNT'					  => 0,
				'COMMENT'                 => 1,

				'U_CAT'                   => $this->helper->route('ernadoo_phpbbdirectory_dynamic_route_2'),
				'U_COMMENT'               => $this->helper->route('ernadoo_phpbbdirectory_comment_view_controller'),

				'L_DIR_SEARCH_NB_CLICKS'	=> 'DIR_SEARCH_NB_CLICKS',
				'L_DIR_SEARCH_NB_COMMS'		=> 'DIR_SEARCH_NB_COMMS',
				)
			),
			array('block.row.col'),
			array('block.row.col2')
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

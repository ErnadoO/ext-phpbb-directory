<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\tests\core;

/**
* @group controller
*/

class phpbbdirectory_cat_test extends controller_base
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
		$this->config['email_enable']			= 1;
	}

	public function get_core_categorie($user_id = 1)
	{
		global $table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch;
		global $phpEx, $phpbb_root_path;

		$this->user->data['user_id']		= $user_id;
		$this->user->data['is_registered']	= ($this->user->data['user_id'] != ANONYMOUS && ($this->user->data['user_type'] == USER_NORMAL || $this->user->data['user_type'] == USER_FOUNDER)) ? true : false;

		$this->core_categorie = new \ernadoo\phpbbdirectory\core\categorie(
			$this->db,
			$this->config,
			$this->lang,
			$this->template,
			$this->user,
			$this->helper,
			$this->request,
			$this->auth,
			$this->cron
		);
		$this->core_categorie->set_tables($table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch);
		$this->core_categorie->set_path_helper($this->phpbb_path_helper);
		$this->core_categorie->set_extension_manager($this->phpbb_extension_manager);

		return $this->core_categorie;
	}

	function test_make_cat_jumpbox()
	{
		$this->template->expects($this->exactly(8))
		->method('assign_block_vars')
		->withConsecutive(
			array('jumpbox_forums', array(
				'FORUM_ID'			=> 1,
				'FORUM_NAME'		=> 'Catégorie 1',
				'S_FORUM_COUNT' 	=> 0,
				'LINK'				=> 'ernadoo_phpbbdirectory_dynamic_route_1',
			)),
			array('jumpbox_forums', array(
				'FORUM_ID'			=> 2,
				'FORUM_NAME'		=> 'Catégorie 2',
				'S_FORUM_COUNT'		=> 1,
				'LINK'				=> 'ernadoo_phpbbdirectory_dynamic_route_2',
			)),
			array('jumpbox_forums.level', array(
			)),
			array('jumpbox_forums', array(
				'FORUM_ID'			=> 3,
				'FORUM_NAME'		=> 'Catégorie 3',
				'S_FORUM_COUNT'		=> 2,
				'LINK'				=> 'ernadoo_phpbbdirectory_dynamic_route_3',
			)),
			array('jumpbox_forums.level', array(
			)),
			array('jumpbox_forums', array(
				'FORUM_ID'			=> 4,
				'FORUM_NAME'		=> 'Catégorie 4',
				'S_FORUM_COUNT'		=> 3,
				'LINK'				=> 'ernadoo_phpbbdirectory_dynamic_route_4',
			)),
			array('jumpbox_forums.level', array(
			))
		);

		$response = $this->get_core_categorie()->make_cat_jumpbox();
	}

	/**
	* Test data for the make_cat_select() function
	*
	* @return array Array of test data
	*/
	public function make_cat_select_data()
	{
		return array(
			array(0, 0, '<option value="1">Catégorie 1</option><option value="2">&nbsp; &nbsp;Catégorie 2</option><option value="3">&nbsp; &nbsp;Catégorie 3</option><option value="4">&nbsp; &nbsp;&nbsp; &nbsp;Catégorie 4</option>'),
			array(0, array(1, 4), '<option value="1" disabled="disabled" class="disabled-option">Catégorie 1</option><option value="2">&nbsp; &nbsp;Catégorie 2</option><option value="3">&nbsp; &nbsp;Catégorie 3</option><option value="4" disabled="disabled" class="disabled-option">&nbsp; &nbsp;&nbsp; &nbsp;Catégorie 4</option>'),
			array(1, 0, '<option value="1" selected="selected">Catégorie 1</option><option value="2">&nbsp; &nbsp;Catégorie 2</option><option value="3">&nbsp; &nbsp;Catégorie 3</option><option value="4">&nbsp; &nbsp;&nbsp; &nbsp;Catégorie 4</option>'),
		);
	}

	/**
	*
	* @dataProvider make_cat_select_data
	*/
	function test_make_cat_select($select_id, $ignore_id, $page_content)
	{
		$response = $this->get_core_categorie()->make_cat_select($select_id, $ignore_id);

		$this->assertEquals($page_content, $response);
	}

	/**
	* Test data for the test_dir_submit_type() function
	*
	* @return array Array of test data
	*/
	public function dir_submit_type_data()
	{
		return array(
			array(1, true, 'DIR_SUBMIT_TYPE_1'),
			array(1, false, 'DIR_SUBMIT_TYPE_2'),
			array(2, true, 'DIR_SUBMIT_TYPE_3'),
			array(2, false, 'DIR_SUBMIT_TYPE_2'),
			array(3, true, 'DIR_SUBMIT_TYPE_4'),
			array(3, false, 'DIR_SUBMIT_TYPE_2'),
		);
	}

	/**
	*
	* @dataProvider dir_submit_type_data
	*/
	function test_dir_submit_type($user_id, $validate, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$response = $this->get_core_categorie($user_id)->dir_submit_type($validate);

		$this->assertEquals($page_content, $response);
	}

	/**
	 * Test data for the test_watch_categorie() function
	 *
	 * @return array Array of test data
	 */
	public function watch_categorie_data()
	{
		return array(
			array('unwatch', array(), 1, 1, NOTIFY_YES, null),
			array('unwatch', array(), 2, 1, NOTIFY_YES, 'DIR_NOT_WATCHING_CAT<br /><br />DIR_CLICK_RETURN_CAT'),
			array('unwatch', array(), 2, 1, NOTIFY_NO, 'DIR_NOT_WATCHING_CAT<br /><br />DIR_CLICK_RETURN_CAT'),
			array('watch', array(), 2, 1, NOTIFY_YES, null),
			array('watch', array(), 2, 1, NOTIFY_NO, null),
			array('watch', array(), 2, 1, null, 'DIR_ARE_WATCHING_CAT<br /><br />DIR_CLICK_RETURN_CAT'),
		);
	}

	/**
	 *
	 * @dataProvider watch_categorie_data
	 */
	function test_watch_categorie($mode, $s_watching, $user_id, $cat_id, $notifiy_status, $page_content)
	{
		$user_data = $this->auth->obtain_user_data($user_id);
		$this->auth->acl($user_data);

		$response = $this->get_core_categorie($user_id)->watch_categorie($mode, $s_watching, $user_id, $cat_id, $notifiy_status);

		$this->assertEquals($page_content, $response);
	}

	/**
	 * Test data for the test_getname() function
	 *
	 * @return array Array of test data
	 */
	public function getname_data()
	{
		return array(
			array(1, 'Catégorie 1'),
			array(69, null),
		);
	}

	/**
	 *
	 * @dataProvider getname_data
	 */
	function test_getname($cat_id, $page_content)
	{
		$response = $this->get_core_categorie($user_id)->getname($cat_id);

		$this->assertEquals($page_content, $response);
	}

	protected function tearDown()
	{
		parent::tearDown();
	}
}

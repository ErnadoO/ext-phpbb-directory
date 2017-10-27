<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\tests\core
{
	/**
	 * @group controller
	 */

	class phpbbdirectory_link_test extends controller_base
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
			global $phpbb_root_path;

			parent::setUp();

			$this->get_test_case_helpers()->copy_dir(__DIR__ . '/fixtures/banners/', $phpbb_root_path . 'files/ext/ernadoo/phpbbdirectory/banners/');

			$this->config['dir_banner_width']	= 300;
			$this->config['dir_banner_height']	= 100;
			$this->config['server_name']		= 'travis-ci.org';
		}

		public function get_core_link($user_id = 1)
		{
			global $table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch;
			global $phpEx, $phpbb_root_path;

			$this->user->data['user_id']		= $user_id;
			$this->user->data['is_registered']	= ($this->user->data['user_id'] != ANONYMOUS && ($this->user->data['user_type'] == USER_NORMAL || $this->user->data['user_type'] == USER_FOUNDER)) ? true : false;

			$this->core_link = new \ernadoo\phpbbdirectory\core\link(
				$this->db,
				$this->config,
				$this->lang,
				$this->template,
				$this->user,
				$this->helper,
				$this->request,
				$this->auth,
				$this->notification_helper,
				$this->filesystem,
				$this->imagesize,
				$this->upload,
				$phpbb_root_path,
				$phpEx
				);
			$this->core_link->set_tables($table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch);
			$this->core_link->set_path_helper($this->phpbb_path_helper);
			$this->core_link->set_extension_manager($this->phpbb_extension_manager);

			return $this->core_link;
		}

		/**
		 * Test data for the test_banner_process() function
		 *
		 * @return array Array of test data
		 */
		public function banner_process_data()
		{
			return array(
				array(true, 'https://www.google.fr/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png', 'mock_googlelogo_color_272x92dp.png', 'banner.png', array()),
				array(false, 'https://www.google.fr/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png', '', 'fake.png', array()),
				array(false, 'https://www.google.com/images/fake.txt', '', '', array('DIR_BANNER_URL_INVALID')),
				array(false, 'https://www.google.com/images/fake.png', '', '', array('DIR_BANNER_UNABLE_GET_IMAGE_SIZE')),
				array(false, 'https://upload.wikimedia.org/wikipedia/commons/c/ca/1x1.png', '', '', array('DIR_BANNER_UNABLE_GET_IMAGE_SIZE')),
				array(false, 'https://www.phpbb.com/assets/images/headers/header_olympus.jpg', '', '', array('DIR_BANNER_WRONG_SIZE')),
			);
		}

		/**
		 *
		 * @dataProvider banner_process_data
		 */
		function test_banner_process($can_upload, $banner, $banner_out, $old_banner, $error_out)
		{
			$error = array();

			$_POST['old_banner'] = $old_banner;

			$this->type_cast_helper = $this->getMock('\phpbb\request\type_cast_helper_interface');
			$this->request = new \phpbb\request\request($this->type_cast_helper, false);

			if ($can_upload)
			{
				$this->config['dir_storage_banner']	= 1;
			}
			else
			{
				$banner_out = $banner;
			}

			$response = $this->get_core_link()->banner_process($banner, $error);
			$this->assertEquals($banner_out, $banner);
			$this->assertEquals($error_out, $error);
		}

		/**
		 * Test data for the test_validate_link_back() function
		 *
		 * @return array Array of test data
		 */
		public function validate_link_back_data()
		{
			return array(
				array('https://www.google.com', true, 'DIR_ERROR_NO_LINK_BACK'),
				array('', true, false),
				array('http://www.mock.fr', true, 'DIR_ERROR_NOT_FOUND_BACK'),
				array('fake', false, 'DIR_ERROR_WRONG_DATA_BACK'),
				array('http://www.fake.com', false, 'DIR_ERROR_NO_LINK_BACK'),
				array('https://travis-ci.org', false, false),
			);
		}

		/**
		 *
		 * @dataProvider validate_link_back_data
		 */
		function test_validate_link_back($remote_url, $optional, $page_content)
		{
			$response = $this->get_core_link()->validate_link_back($remote_url, $optional);
			$this->assertEquals($page_content, $response);
		}

		protected function tearDown()
		{
			parent::tearDown();
		}
	}
}

namespace ernadoo\phpbbdirectory\core
{
	function unique_id()
	{
		return 'mock';
	}
}

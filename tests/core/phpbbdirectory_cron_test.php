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

	class phpbbdirectory_cron_test extends controller_base
	{
		public function getDataSet()
		{
			return $this->createXMLDataSet(__DIR__ . '/fixtures/fixture_cron.xml');
		}

		/**
		 * Test data for the test_auto_check() function
		 *
		 * @return array Array of test data
		 */
		public function auto_check_data()
		{
			return array(
				array(array('cat_id' => 1, 'cat_cron_freq' => 1, 'cat_cron_nb_check' => 5), null),
			);
		}

		/**
		 *
		 * @dataProvider auto_check_data
		 */
		function test_auto_check($cat_data, $page_content)
		{
			$this->config['server_name'] = 'travis-ci.org';
			$this->config['board_timezone']	= 'Europe/Paris';

			$response = $this->core_cron->auto_check($cat_data);
			$this->assertEquals($page_content, $response);
		}

		protected function tearDown()
		{
			parent::tearDown();
		}
	}
}

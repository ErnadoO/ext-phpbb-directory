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
	abstract class controller_base extends \phpbb_database_test_case
	{
		/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\auth\auth */
		protected $auth;

		/** @var \phpbb\config\config */
		protected $config;

		/** @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\DependencyInjection\ContainerInterface */
		protected $container;

		/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\controller\helper */
		protected $helper;

		/** @var \phpbb\language\language */
		protected $lang;

		/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\template\template */
		protected $template;

		/** @var \phpbb\user */
		protected $user;

		/**
		* Define the extensions to be tested
		*
		* @return array vendor/name of extension(s) to test
		*/
		static protected function setup_extensions()
		{
			return array('ernadoo/phpbbdirectory');
		}

		public function setUp()
		{
			global $cache, $phpbb_container, $phpbb_extension_manager, $user, $phpbb_root_path, $phpEx;
			global $auth, $config, $phpbb_filesystem, $template, $db, $phpbb_path_helper, $phpbb_dispatcher;
			global $table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch;

			parent::setUp();

			$table_categories	= 'phpbb_directory_cats';
			$tables_comments	= 'phpbb_directory_comments';
			$tables_links		= 'phpbb_directory_links';
			$tables_votes		= 'phpbb_directory_votes';
			$tables_watch		= 'phpbb_directory_watch';

			//Let's build some deps
			$this->auth			= new \phpbb\auth\auth;

			$this->db			= $this->new_dbal();

			$this->cache		= new \phpbb\cache\service(new \phpbb\cache\driver\dummy(), new \phpbb\config\config(array()), $this->db, $phpbb_root_path, $phpEx);
			$this->cache->purge();

			$this->config		= new \phpbb\config\config(array());

			$this->filesystem	= new \phpbb\filesystem\filesystem();

			$this->phpbb_path_helper = new \phpbb\path_helper(
				new \phpbb\symfony_request(new \phpbb_mock_request()),
				new \phpbb_mock_request(),
				$phpbb_root_path,
				$phpEx
			);

			$this->lang = new \phpbb\language\language(
				new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx)
			);

			$this->user = new \phpbb\user($this->lang, '\phpbb\datetime');

			$this->user->timezone = new \DateTimeZone('UTC');
			$this->user->lang = array(
				'datetime' => array(),
				'DATE_FORMAT' => 'm/d/Y',
			);

			$this->request = $this->createMock('\phpbb\request\request');

			$this->template = $this->getMockBuilder('\phpbb\template\template')->getMock();

			$this->dispatcher = new \phpbb\event\dispatcher(new \phpbb_mock_container_builder());

			$this->helper = $this->getMockBuilder('\phpbb\controller\helper')
				->disableOriginalConstructor()
				->getMock();

			$this->helper
				->method('route')
				->will($this->returnArgument(0));

			$this->notification_helper = $this->getMockBuilder('\phpbb\notification\manager')
				->disableOriginalConstructor()
			->getMock();

			$this->phpbb_extension_manager = new \phpbb_mock_extension_manager($phpbb_root_path);

			$phpbb_container = new \phpbb_mock_container_builder();
			$phpbb_container = $this->get_test_case_helpers()->set_s9e_services($phpbb_container);
			$phpbb_container->set('config', $config);
			$phpbb_container->set('controller.helper', $this->helper);
			$phpbb_container->set('filesystem', $this->filesystem);
			$phpbb_container->set('path_helper', $this->phpbb_path_helper);
			$phpbb_container->set('ext.manager', $this->phpbb_extension_manager);
			$phpbb_container->set('user', $this->user);
			$phpbb_container->setParameter('core.cache_dir', $phpbb_root_path . 'cache/' . PHPBB_ENVIRONMENT . '/');
			$phpbb_container->setParameter('tables.dir.categories', $table_categories);

			$this->imagesize = new \FastImageSize\FastImageSize;

			$phpbb_container->set('files.filespec', new \phpbb\files\filespec($this->filesystem, $this->lang, new \bantu\IniGetWrapper\IniGetWrapper, new \FastImageSize\FastImageSize(), $phpbb_root_path));

			$factory = new \phpbb\files\factory($phpbb_container);

			$phpbb_container->set('files.types.remote', new \phpbb\files\types\remote($this->config, $factory, $this->lang, new \bantu\IniGetWrapper\IniGetWrapper, $this->request, $phpbb_root_path));

			$factory = new \phpbb\files\factory($phpbb_container);

			$this->upload = new \phpbb\files\upload($this->filesystem, $factory, $this->lang, new \bantu\IniGetWrapper\IniGetWrapper, $this->request, $phpbb_root_path);

			$phpbb_log = new \phpbb\log\log($this->db, $this->user, $this->auth, $this->dispatcher, $phpbb_root_path, 'adm/', $phpEx, LOG_TABLE);

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

			$this->core_cron = new \ernadoo\phpbbdirectory\core\cron(
				$this->db,
				$this->config,
				$phpbb_log,
				$this->user,
				$this->notification_helper,
				$this->core_link,
				$phpbb_root_path,
				$phpEx
			);
			$this->core_cron->set_tables($table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch);
			$this->core_cron->set_path_helper($this->phpbb_path_helper);
			$this->core_cron->set_extension_manager($this->phpbb_extension_manager);

			$cron_task = new \ernadoo\phpbbdirectory\cron\task\core\prune_categorie(
				$this->config,
				$this->core_cron,
				$phpEx
			);
			$cron_task->set_name('ernadoo.phpbbdirectory.cron.task.core.prune_categorie');

			$this->cron = $this->create_cron_manager(array($cron_task));
			$phpbb_container->set('cron.manager', $this->cron);

			// Global vars
			$auth						= $this->auth;
			$cache						= $this->cache;
			$config						= $this->config;
			$db							= $this->db;
			$phpbb_dispatcher			= $this->dispatcher;
			$phpbb_extension_manager	= $this->phpbb_extension_manager;
			$phpbb_filesystem			= $this->filesystem;
			$phpbb_path_helper			= $this->phpbb_path_helper;
			$request					= $this->request;
			$template					= $this->template;
			$user						= $this->user;
		}

		protected function mock_request()
		{
			$this->request->expects($this->any())
			->method('is_set_post')
			->willReturnCallback(function ($name) {
				return true;
			});
		}

		private function create_cron_manager($tasks)
		{
			global $phpbb_root_path, $phpEx;

			$mock_config = new \phpbb\config\config(array(
				'force_server_vars' => false,
				'enable_mod_rewrite' => '',
			));

			$mock_router = $this->getMockBuilder('\phpbb\routing\router')
				->setMethods(array('setContext', 'generate'))
				->disableOriginalConstructor()
				->getMock();
			$mock_router->method('setContext')
				->willReturn(true);
			$mock_router->method('generate')
				->willReturn('foobar');

			$request = new \phpbb\request\request();
			$request->enable_super_globals();

			$routing_helper = new \phpbb\routing\helper(
				$mock_config,
				$mock_router,
				new \phpbb\symfony_request($request),
				$request,
				$phpbb_root_path,
				$phpEx
				);

			return new \phpbb\cron\manager($tasks, $routing_helper, $phpbb_root_path, $phpEx);
		}
	}
}

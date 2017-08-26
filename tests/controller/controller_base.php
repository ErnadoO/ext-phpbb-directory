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
		global $cache, $phpbb_container, $phpbb_path_helper, $phpbb_extension_manager, $request, $user, $phpbb_root_path, $cron, $phpEx;
		global $phpbb_dispatcher, $auth, $config, $phpbb_filesystem, $template;
		global $table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch;

		parent::setUp();

		$table_categories	= 'phpbb_directory_cats';
		$tables_comments	= 'phpbb_directory_comments';
		$tables_links		= 'phpbb_directory_links';
		$tables_votes		= 'phpbb_directory_votes';
		$tables_watch		= 'phpbb_directory_watch';

		//Let's build some deps
		$auth = $this->auth = $this->getMock('\phpbb\auth\auth');

		$config = $this->config = new \phpbb\config\config(array());

		$db = $this->db = $this->new_dbal();

		$this->request = $this->getMock('\phpbb\request\request');

		$this->template = $this->getMockBuilder('\phpbb\template\template')->getMock();

		$request = new \phpbb_mock_request();

		$symfony_request = new \phpbb\symfony_request(
			$request
		);

		$phpbb_filesystem = $this->filesystem = new \phpbb\filesystem\filesystem();

		$phpbb_path_helper = $this->phpbb_path_helper = new \phpbb\path_helper(
			$symfony_request,
			$this->filesystem,
			$request,
			$phpbb_root_path,
			$phpEx
		);

		$lang_loader = new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx);
		$this->lang = new \phpbb\language\language($lang_loader);

		$user = $this->user = new \phpbb\user($this->lang, '\phpbb\datetime');
		$this->user->timezone = new \DateTimeZone('UTC');
		$this->user->lang = array(
			'datetime' => array(),
			'DATE_FORMAT' => 'm/d/Y',
		);

		$this->helper = $this->getMockBuilder('\phpbb\controller\helper')
			->disableOriginalConstructor()
			->getMock();

		$this->helper->expects($this->any())
			->method('render')
			->willReturnCallback(function ($template_file, $page_title = '', $status_code = 200, $display_online_list = false) {
				return new \Symfony\Component\HttpFoundation\Response($template_file, $status_code);
		});
		$this->helper
			->method('route')
			->will($this->returnArgument(0));

		$cache = $this->cache = new \phpbb_mock_cache();

		$this->cache->purge();

		$phpbb_dispatcher = $this->dispatcher = new \phpbb_mock_event_dispatcher();

		$this->pagination = new \phpbb\pagination($this->template, $this->user, $this->helper, $phpbb_dispatcher);

		$this->user_loader = $this->getMockBuilder('\phpbb\user_loader')
			->disableOriginalConstructor()
		->getMock();

		$this->notification_helper = $this->getMockBuilder('\phpbb\notification\manager')
			->disableOriginalConstructor()
		->getMock();

		$phpbb_extension_manager = new \phpbb_mock_extension_manager($phpbb_root_path);

		$phpbb_container = new \phpbb_mock_container_builder();
		$phpbb_container->set('config', $config);
		$phpbb_container->set('filesystem', $phpbb_filesystem);
		$phpbb_container->set('path_helper', $phpbb_path_helper);
		$phpbb_container->set('ext.manager', $phpbb_extension_manager);
		$phpbb_container->set('user', $this->user);
		$phpbb_container->setParameter('core.cache_dir', $phpbb_root_path . 'cache/' . PHPBB_ENVIRONMENT . '/');

		$context = new \phpbb\template\context();
		$twig_extension = new \phpbb\template\twig\extension($context, $this->lang);
		$phpbb_container->set('template.twig.extensions.phpbb', $twig_extension);

		$twig_extensions_collection = new \phpbb\di\service_collection($phpbb_container);
		$twig_extensions_collection->add('template.twig.extensions.phpbb');
		$phpbb_container->set('template.twig.extensions.collection', $twig_extensions_collection);

		$phpbb_container->set('ernadoo.phpbbdirectory.core.nestedset_category',
			new \ernadoo\phpbbdirectory\core\nestedset_category(
				$this->db,
				new \phpbb\lock\db(
					'ernadoo.phpbbdirectory.table_lock.directory_cats',
					$this->config,
					$this->db
				),
				$table_categories
			)
		);

		$imagesize = $this->getMockBuilder('\FastImageSize\FastImageSize')
			->getMock();

		$files = $this->getMockBuilder('\phpbb\files\factory')
			->disableOriginalConstructor()
			->getMock();

		$phpbb_log = new \phpbb\log\log($this->db, $this->user, $this->auth, $phpbb_dispatcher, $phpbb_root_path, 'adm/', $phpEx, LOG_TABLE);

		$this->core_link = new \ernadoo\phpbbdirectory\core\link(
			$this->db,
			$config,
			$this->lang,
			$this->template,
			$this->user,
			$this->helper,
			$this->request,
			$auth,
			$this->notification_helper,
			$this->filesystem,
			$imagesize,
			$files,
			$phpbb_root_path,
			$phpEx
		);
		$this->core_link->set_tables($table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch);
		$this->core_link->set_path_helper($phpbb_path_helper);
		$this->core_link->set_extension_manager($phpbb_extension_manager);

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
		$this->core_cron->set_path_helper($phpbb_path_helper);
		$this->core_cron->set_extension_manager($phpbb_extension_manager);

		$cron_task = new \ernadoo\phpbbdirectory\cron\task\core\prune_categorie(
			$this->config,
			$this->core_cron,
			$phpEx
		);
		$cron_task->set_name('ernadoo.phpbbdirectory.cron.task.core.prune_categorie');

		$this->cron = $this->create_cron_manager(array($cron_task));

		$this->core_categorie = new \ernadoo\phpbbdirectory\core\categorie(
			$db,
			$config,
			$this->lang,
			$this->template,
			$this->user,
			$this->helper,
			$this->request,
			$auth,
			$this->cron
		);
		$this->core_categorie->set_tables($table_categories, $tables_comments, $tables_links, $tables_votes, $tables_watch);
		$this->core_categorie->set_path_helper($phpbb_path_helper);
		$this->core_categorie->set_extension_manager($phpbb_extension_manager);
	}

	private function create_cron_manager($tasks)
	{
		global $phpbb_root_path, $phpEx;

		return new \phpbb\cron\manager($tasks, $phpbb_root_path, $phpEx);
	}
}

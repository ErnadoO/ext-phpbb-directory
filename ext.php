<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

// this file is not really needed, when empty it can be omitted
// however you can override the default methods and add custom
// installation logic

namespace ernadoo\phpbbdirectory;

class ext extends \phpbb\extension\base
{
	/**
	* Enable extension if requirements are met
	*
	* @return bool
	* @aceess public
	*/
	public function is_enableable()
	{
		$config = $this->container->get('config');

		// Check phpbb version
		if (!version_compare($config['version'], '3.1.3', '>='))
		{
			return false;
		}

		// Check for getimagesize
		if (!@function_exists('getimagesize'))
		{
			return false;
		}

		// Check for url_fopen
		if (!@ini_get('allow_url_fopen'))
		{
			return false;
		}

		return true;
	}

	/**
	* Single enable step that installs any included migrations
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	public function enable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet

				// Enable board rules notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->enable_notifications('ernadoo.phpbbdirectory.notification.type.directory_website');
				$phpbb_notifications->enable_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_approved');
				$phpbb_notifications->enable_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_disapproved');
				$phpbb_notifications->enable_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_error_cron');
				$phpbb_notifications->enable_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_in_queue');
				return 'notifications';

				break;

			default:

				// Run parent enable step method
				return parent::enable_step($old_state);

				break;
		}
	}

	/**
	* Single disable step that does nothing
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	public function disable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet

				// Disable board rules notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->disable_notifications('ernadoo.phpbbdirectory.notification.type.directory_website');
				$phpbb_notifications->disable_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_approved');
				$phpbb_notifications->disable_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_disapproved');
				$phpbb_notifications->disable_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_error_cron');
				$phpbb_notifications->disable_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_in_queue');
				return 'notifications';

				break;

			default:

				// Run parent disable step method
				return parent::disable_step($old_state);

				break;
		}
	}

	/**
	* Single purge step that reverts any included and installed migrations
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	public function purge_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet

				// Purge board rules notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->purge_notifications('ernadoo.phpbbdirectory.notification.type.directory_website');
				$phpbb_notifications->purge_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_approved');
				$phpbb_notifications->purge_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_disapproved');
				$phpbb_notifications->purge_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_error_cron');
				$phpbb_notifications->purge_notifications('ernadoo.phpbbdirectory.notification.type.directory_website_in_queue');
				return 'notifications';

				break;

			default:

				// Run parent purge step method
				return parent::purge_step($old_state);

				break;
		}
	}
}

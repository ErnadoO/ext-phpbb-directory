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
		$php_ini = $this->container->get('php_ini');

		// Check phpbb version
		if (!phpbb_version_compare(PHPBB_VERSION, '3.2.1', '>='))
		{
			return false;
		}

		if (!file_exists($this->extension_path. 'vendor/autoload.php'))
		{
			return false;
		}

		// Check for url_fopen
		return (bool) $php_ini->getBool('allow_url_fopen');
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

				// Enable notifications
				return $this->notification_handler('enable', array(
					'ernadoo.phpbbdirectory.notification.type.directory_website',
					'ernadoo.phpbbdirectory.notification.type.directory_website_approved',
					'ernadoo.phpbbdirectory.notification.type.directory_website_disapproved',
					'ernadoo.phpbbdirectory.notification.type.directory_website_error_cron',
					'ernadoo.phpbbdirectory.notification.type.directory_website_in_queue',
				));

			break;

			default:

				if (!class_exists('\E1379\SpeakingUrl\SpeakingUrl'))
				{
					// When migration is executed, phpBB doesn't know yet extension dependencies, so we need to manually include autoload file
					require($this->extension_path. 'vendor/autoload.php');
				}

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

				// Disable notifications
				return $this->notification_handler('disable', array(
					'ernadoo.phpbbdirectory.notification.type.directory_website',
					'ernadoo.phpbbdirectory.notification.type.directory_website_approved',
					'ernadoo.phpbbdirectory.notification.type.directory_website_disapproved',
					'ernadoo.phpbbdirectory.notification.type.directory_website_error_cron',
					'ernadoo.phpbbdirectory.notification.type.directory_website_in_queue',
				));

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

				// Purge notifications
				return $this->notification_handler('purge', array(
					'ernadoo.phpbbdirectory.notification.type.directory_website',
					'ernadoo.phpbbdirectory.notification.type.directory_website_approved',
					'ernadoo.phpbbdirectory.notification.type.directory_website_disapproved',
					'ernadoo.phpbbdirectory.notification.type.directory_website_error_cron',
					'ernadoo.phpbbdirectory.notification.type.directory_website_in_queue',
				));

			break;

			default:

				// Run parent purge step method
				return parent::purge_step($old_state);

			break;
		}
	}

	/**
	* Notification handler to call notification enable/disable/purge steps
	*
	* @author VSEphpbb (Matt Friedman)
	* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
	* @license GNU General Public License, version 2 (GPL-2.0)
	* @param string $step The step (enable, disable, purge)
	* @param array $notification_types The notification type names
	* @return string Return notifications as temporary state
	* @access protected
	*/
	protected function notification_handler($step, $notification_types)
	{
		$phpbb_notifications = $this->container->get('notification_manager');

		foreach ($notification_types as $notification_type)
		{
			call_user_func(array($phpbb_notifications, $step . '_notifications'), $notification_type);
		}

		return 'notifications';
	}
}

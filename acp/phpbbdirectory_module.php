<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\acp;

class phpbbdirectory_module
{
	public $page_title;

	public $tpl_name;

	public $u_action;

	/**
	*
	* @param int	$id
	* @param string	$mode
	*/
	public function main($id, $mode)
	{
		global $request, $phpbb_container;

		$action	= $request->variable('action', '');
		$update	= ($request->is_set_post('update')) ? true : false;

		switch ($mode)
		{
			case 'main':

				// Set the page title for our ACP page
				$this->page_title = 'ACP_DIRECTORY';

				// Load a template from adm/style for our ACP page
				$this->tpl_name = 'acp_dir_main';

				// Get an instance of the acp_main controller
				$main_controller = $phpbb_container->get('ernadoo.phpbbdirectory.controller.acp.main');

				// Make the $u_action url available in the acp_main controller
				$main_controller->set_page_url($this->u_action);

				if ($action)
				{
					if (!confirm_box(true))
					{
						$main_controller->display_confirm($action);
					}
					else
					{
						$main_controller->exec_action($action);
					}
				}

				// Display main page
				$main_controller->display_stats();

			break;

			case 'settings':

				// Get an instance of the acp_settings controller
				$settings_controller = $phpbb_container->get('ernadoo.phpbbdirectory.controller.acp.settings');

				// Set the page title for our ACP page
				$this->page_title = 'ACP_DIRECTORY_SETTINGS';

				// Load a template from adm/style for our ACP page
				$this->tpl_name = 'acp_board';

				// Make the $u_action url available in the acp_settings controller
				$settings_controller->set_page_url($this->u_action);

				$settings_controller->process();

				// Display config
				$settings_controller->display_config();

			break;

			case 'cat':

				// Set the page title for our ACP page
				$this->page_title = 'ACP_DIRECTORY';

				// Load a template from adm/style for our ACP page
				$this->tpl_name = 'acp_dir_cat';

				// Get an instance of the acp_cat controller
				$cat_controller = $phpbb_container->get('ernadoo.phpbbdirectory.controller.acp.cat');

				// Make the $u_action url available in the acp_cat controller
				$cat_controller->set_page_url($this->u_action);

				// Major routines
				if ($update)
				{
					$cat_controller->update();
				}

				switch ($action)
				{
					case 'progress_bar':
						$cat_controller->action_progress_bar();
						return;
					break;

					case 'sync':
						$cat_controller->action_sync();
					break;

					case 'sync_cat':
						$cat_controller->action_sync_cat();
					break;

					case 'move_up':
					case 'move_down':
						$cat_controller->action_move();
					break;

					case 'edit':
						$this->page_title = 'DIR_EDIT_CAT';

						$cat_controller->action_edit();
						return;
					break;

					case 'add':
						$this->page_title = 'DIR_ADD_CAT';

						$cat_controller->action_add();
						return;
					break;

					case 'delete':
						$cat_controller->action_delete();
						return;
					break;
				}

				// Display categories
				$cat_controller->display_cats();

			break;

			case 'val':

				// Set the page title for our ACP page
				$this->page_title = 'ACP_DIRECTORY';

				// Load a template from adm/style for our ACP page
				$this->tpl_name = 'acp_dir_val';

				// Get an instance of the acp_validation controller
				$validation_controller = $phpbb_container->get('ernadoo.phpbbdirectory.controller.acp.validation');

				// Make the $u_action url available in the acp_validation controller
				$validation_controller->set_page_url($this->u_action);

				$mark = ($request->is_set_post('link_id')) ? $request->variable('link_id', array(0)) : array();

				if ($action && sizeof($mark))
				{
					if (!confirm_box(true) && $action != 'approved')
					{
						$validation_controller->display_confirm($mark);
					}
					else
					{
						$validation_controller->exec_action($mark);
					}

					$validation_controller->notifiy_submiters();
				}

				// Display websites pending validation
				$validation_controller->display_websites();

			break;
		}
	}

	/**
	* Display thumb services available
	*
	* @param 	string	$url_selected
	* @return 	string
	*/
	public function get_thumb_service_list($url_selected)
	{
		$thumbshot = array(
			'http://www.apercite.fr/apercite/120x90/oui/oui/',
			'http://www.easy-thumb.net/min.html?url=',
		);

		$select_options = '';
		foreach ($thumbshot as $url)
		{
			$selected = ($url == $url_selected) ? 'selected="selected"' : '';

			$select_options .= '<option value="' . $url . '" ' . $selected . '>' . parse_url($url, PHP_URL_HOST) . '</option>';
		}

		return $select_options;
	}

	/**
	* Display order drop-down list
	*
	* @param	string	$order_selected
	* @return	string
	*/
	public function get_order_list($order_selected)
	{
		global $user;

		$order_array = array(
			'a a',
			'a d',
			't a',
			't d',
			'r a',
			'r d',
			's a',
			's d',
			'v a',
			'v d'
		);
		$tpl = '';
		foreach ($order_array as $i)
		{
			$selected = ($i == $order_selected) ? 'selected="selected"' : '';
			$order_substr = trim(str_replace(' ', '_', $i));
			$tpl .= '<option value="' . $i . '" ' . $selected . '>' . $user->lang['DIR_ORDER_' . strtoupper($order_substr)] . '</option>';
		}

		return ($tpl);
	}

	/**
	* Adjust all three max_filesize config vars for display
	*/
	public function max_filesize($value, $key = '')
	{
		// Determine size var and adjust the value accordingly
		$filesize = get_formatted_filesize($value, false, array('mb', 'kb', 'b'));
		$size_var = $filesize['si_identifier'];
		$value = $filesize['value'];

		// size and maxlength must not be specified for input of type number
		return '<input type="number" id="' . $key . '" min="0" max="999999999999999" step="any" name="config[' . $key . ']" value="' . $value . '" /> <select name="' . $key . '">' . size_select_options($size_var) . '</select>';
	}
}

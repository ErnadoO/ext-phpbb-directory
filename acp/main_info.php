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

class main_info
{
    function module()
    {
        return array(
            'filename'		=> '\ernadoo\phpbbdirectory\acp\main_module',
            'title'			=> 'ACP_DIRECTORY',
            'modes'			=> array(
            	''				=> array('title' => 'ACP_DIRECTORY',			'auth'	=> 'ext_ernadoo/phpbbdirectory', 'cat' => array('')),
            	'main'			=> array('title' => 'ACP_DIRECTORY_MAIN',		'auth'	=> 'ext_ernadoo/phpbbdirectory', 'cat' => array('ACP_DIRECTORY')),
				'settings'		=> array('title' => 'ACP_DIRECTORY_SETTINGS',	'auth'	=> 'ext_ernadoo/phpbbdirectory', 'cat' => array('ACP_DIRECTORY')),
				'cat'			=> array('title' => 'ACP_DIRECTORY_CATS',		'auth'	=> 'ext_ernadoo/phpbbdirectory', 'cat' => array('ACP_DIRECTORY')),
				'val'			=> array('title' => 'ACP_DIRECTORY_VAL',		'auth'	=> 'ext_ernadoo/phpbbdirectory', 'cat' => array('ACP_DIRECTORY')),
            ),
        );
    }
}

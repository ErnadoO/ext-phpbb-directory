<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\migrations\v20x;

class v2_0_0 extends \phpbb\db\migration\container_aware_migration
{
	/**
	* @inheritDoc
	*/
	static public function depends_on()
	{
		return array(
			'\ernadoo\phpbbdirectory\migrations\v10x\v1_0_0',
		);
	}

	/**
	* @inheritDoc
	*/
	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'reparse'))),
		);
	}

	public function reparse($resume_data)
	{
		$limit = 100;
		$fast_reparsers = array(
			array('\ernadoo\phpbbdirectory\textreparser\plugins\cat_description', 'directory_cats'),
			array('\ernadoo\phpbbdirectory\textreparser\plugins\comment_text', 'directory_comments'),
			array('\ernadoo\phpbbdirectory\textreparser\plugins\link_description', 'directory_links'),
		);

		if (!is_array($resume_data))
		{
			$default_reparser = new $fast_reparsers[0][0](
				$this->db,
				$this->container->getParameter('core.table_prefix') . $fast_reparsers[0][1]);

			$resume_data = array(
				'reparser'	=> 0,
				'current'	=> $default_reparser->get_max_id()

			);
		}

		$fast_reparsers_size = sizeof($fast_reparsers);
		$processed_records = 0;
		while ($processed_records < $limit && $resume_data['reparser'] < $fast_reparsers_size)
		{
			$reparser = new $fast_reparsers[$resume_data['reparser']][0](
				$this->db,
				$this->container->getParameter('core.table_prefix') . $fast_reparsers[$resume_data['reparser']][1]
				);

			// New reparser
			if ($resume_data['current'] === 0)
			{
				$resume_data['current'] = $reparser->get_max_id();
			}

			$start = max(1, $resume_data['current'] + 1 - ($limit - $processed_records));
			$end = max(1, $resume_data['current']);
			$reparser->reparse_range($start, $end);

			$processed_records = $end - $start + 1;
			$resume_data['current'] = $start - 1;

			if ($start === 1)
			{
				// Prevent CLI command from running these reparsers again
				$reparser_manager = $this->container->get('text_reparser.manager');
				$reparser_manager->update_resume_data($fast_reparsers[$resume_data['reparser']][0], 1, 0, $limit);

				$resume_data['reparser']++;
			}
		}

		if ($resume_data['reparser'] === $fast_reparsers_size)
		{
			return true;
		}

		return $resume_data;
	}
}

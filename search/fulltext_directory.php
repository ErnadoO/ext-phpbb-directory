<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\phpbbdirectory\search;

use \ernadoo\phpbbdirectory\core\helper;

class fulltext_directory extends helper
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db)
	{
		$this->db = $db;
	}

	/**
	* Performs a search on keywords depending on display specific params. You have to run split_keywords() first
	*
	* @param	array		$keywords_ary		contains each words to search
	* @param	string		$fields				contains either titleonly (link titles should be searched), desconly (only description bodies should be searched)
	* @param	string		$terms				is either 'all' (use query as entered, words without prefix should default to "have to be in field") or 'any' (ignore search query parts and just return all posts that contain any of the specified words)
	* @param	array		$sort_by_sql		contains SQL code for the ORDER BY part of a query
	* @param	string		$sort_key			is the key of $sort_by_sql for the selected sorting
	* @param	string		$sort_dir			is either a or d representing ASC and DESC
	* @param	string		$sort_days			specifies the maximum amount of days a post may be old
	* @param	array		$ex_cid_ary			specifies an array of category ids which should not be searched
	* @param	int			$cat_id				is set to 0 or a topic id, if it is not 0 then only posts in this topic should be searched
	* @param	array		&$id_ary			passed by reference, to be filled with ids for the page specified by $start and $per_page, should be ordered
	* @param	int			$start				indicates the first index of the page
	* @param	int			$per_page			number of ids each page is supposed to contain
	* @return	int								total number of results
	*/
	public function keyword_search($keywords_ary, $fields, $terms, $sort_by_sql, $sort_key, $sort_dir, $sort_days, $ex_cid_ary, $cat_id, &$id_ary, $start, $per_page)
	{
		$matches = array();

		switch ($fields)
		{
			case 'titleonly':
				$matches[] = 'l.link_name';
			break;

			case 'desconly':
				$matches[] = 'l.link_description';
			break;

			default:
				$matches = array('l.link_name', 'l.link_description');
		}

		$search_query = '';

		foreach ($keywords_ary as $word)
		{
			$match_search_query = '';
			foreach ($matches as $match)
			{
				$match_search_query .= (($match_search_query) ? ' OR ' : '') . 'LOWER('. $match . ') ';
				$match_search_query .= $this->db->sql_like_expression(str_replace('*', $this->db->get_any_char(), $this->db->get_any_char() . strtolower($word) . $this->db->get_any_char()));
			}
			$search_query .= ((!$search_query) ? '' : (($terms == 'all') ? ' AND ' : ' OR ')) . '(' . $match_search_query . ')';
		}
		$direction = (($sort_dir == 'd') ? 'DESC' : 'ASC');

		if (is_array($sort_by_sql[$sort_key]))
		{
			$sql_sort_order = implode(' ' . $direction . ', ', $sort_by_sql[$sort_key]) . ' ' . $direction;
		}
		else
		{
			$sql_sort_order = $sort_by_sql[$sort_key] . ' ' . $direction;
		}

		$sql_array = array(
			'SELECT'	=> 'l.link_id',
			'FROM'		=> array(
					$this->links_table	=> 'l'),
			'WHERE'		=> 'l.link_active = 1
				' . (($search_query) ? 'AND (' . $search_query . ')' : '') . '
				' . (count($ex_cid_ary) ? ' AND ' . $this->db->sql_in_set('l.link_cat', $ex_cid_ary, true) : '') . '
				' . (($cat_id) ? ' AND ' . $this->db->sql_in_set('l.link_cat', $cat_id) : '') . '
				' . (($sort_days) ? ' AND l.link_time >= ' . (time() - ($sort_days * 86400)) : ''),
			'ORDER_BY'	=> $sql_sort_order
		);

		if ($sql_sort_order[0] == 'u')
		{
			$sql_array['LEFT_JOIN'][] = array(
				'FROM'	=> array(USERS_TABLE => 'u'),
				'ON'	=> 'u.user_id = l.link_user_id'
			);
		}

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$id_ary[] = $row['link_id'];
		}

		$this->db->sql_freeresult($result);

		$total_match_count = count($id_ary);

		$id_ary = array_slice($id_ary, $start, (int) $per_page);

		return $total_match_count;
	}
}

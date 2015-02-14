<?php if (!defined("RARS_BASE_PATH")) die("No direct script access to this content");

/**
 * razorCMS FBCMS
 *
 * Copywrite 2014 to Present Day - Paul Smith (aka smiffy6969, razorcms)
 *
 * @author Paul Smith
 * @site ulsmith.net
 * @created Feb 2014
 */

class ExtensionSearchRazorcmsSiteSearchFind extends RazorAPI
{
	function __construct()
	{
		// REQUIRED IN EXTENDED CLASS TO LOAD DEFAULTS
		parent::__construct();
	}

	// find pages based on search terms
	public function get($phrase)
	{
		if (empty($phrase)) $this->response(null, null, 400);		

		// search database and use scoring system to order. need to do pagination somehow

		$db = new RazorDB();

		// first break the search data down into searchable chunks
		$words = explode(' ', $phrase);

		// find page ids matching query results
		$db->connect('page');
		$options = array('filter' => array('id'));
		$search = array();
		foreach ($words as $word)
		{
			$search[] = array('column' => 'title', 'value' => $word, 'case_insensitive' => true, 'wildcard' => true);
			$search[] = array('column' => 'description', 'value' => $word, 'case_insensitive' => true, 'wildcard' => true);
			$search[] = array('column' => 'keywords', 'value' => $word, 'case_insensitive' => true, 'wildcard' => true);
		}
		$pages = $db->get_rows($search);
		$db->disconnect(); 

		// find content ids matching query results
		$db->connect('content');
		$options = array('filter' => array('id'));
		$search = array();
		foreach ($words as $word) $search[] = array('column' => 'content', 'value' => $word, 'case_insensitive' => true, 'wildcard' => true);
		$content = $db->get_rows($search);
		$db->disconnect(); 

		// find page and content from IDs
		if ($content['count'] > 0 || $pages['count'] > 0)
		{
			$db->connect('page_content');
			$options = array(
				'join' => array(
					array('table' => 'content', 'join_to' => 'content_id'),
					array('table' => 'page', 'join_to' => 'page_id')
				),
				'filter' => array(
					'id',
					'page_id', 
					'content_id', 
					'page_id.title',
					'page_id.description',
					'page_id.keywords',
					'content_id.content',
					'page_id.link',
					'page_id.name'
				)
			);
			$search = array();
			foreach ($content['result'] as $row) $search[] = array('column' => 'content_id', 'value' => $row['id']);
			foreach ($pages['result'] as $row) $search[] = array('column' => 'page_id', 'value' => $row['id']);
			$page_content = $db->get_rows($search, $options);
			$db->disconnect(); 
		}

		if (!isset($page_content) || $page_content['count'] < 1) $this->response(null, null, 404);		

		/* We now have a collection of all matches */

		// loop through results working out scoring, removing duplicates
		$matches = array();
		foreach ($page_content['result'] as $pc)
		{
			// capture page for results and set page score once
			if (!isset($matches[$pc['page_id']]))
			{
				$matches[$pc['page_id']] = $pc;
				$matches[$pc['page_id']]['score'] = $this->get_page_score($pc, $phrase);
			}

			// work out content score each content on page
			$matches[$pc['page_id']]['score'] += $this->get_content_score($pc, $phrase);
		}

		// present the results as an array sorted by score
		usort($matches, array('self', 'sort_score'));

		$this->response(array('results' => $matches), 'json');
	}

	private function get_page_score($page_content, $phrase)
	{
		$score = 0;
		$words = explode(' ', $phrase);

		// work out phrase matches for title, description, times this by word count as phrase is longer
		$count = substr_count(strtolower($page_content['page_id.title']), strtolower($phrase));
		$score += ($count > 0 ? 25 * count($words) * $count : 0);
		$count = substr_count(strtolower($page_content['page_id.title']), strtolower($phrase));
		$score += ($count > 0 ? 15 * count($words) * $count : 0);

		// move on to word matching
		foreach ($words as $word)
		{
			$count = substr_count(strtolower($page_content['page_id.title']), strtolower($word));
			$score += ($count > 0 ? 3 * $count : 0); 
			$count = substr_count(strtolower($page_content['page_id.description']), strtolower($word));
			$score += ($count > 0 ? 3 * $count : 0); 
		}

		return $score;
	}

	private function get_content_score($page_content, $phrase)
	{
		if (!isset($page_content['content_id.content'])) return 0;

		$score = 0;
		$words = explode(' ', $phrase);

		// work out phrase matches for title, description, times this by word count as phrase is longer
		$count = substr_count(strtolower($page_content['content_id.content']), strtolower($phrase));
		$score += ($count > 0 ? 10 * count($words) * $count : 0); 
		
		// move on to word matching
		foreach ($words as $word)
		{
			$count = substr_count(strtolower($page_content['content_id.content']), strtolower($word));
			$score += ($count > 0 ? 1 * $count : 0); 
		}

		return $score;
	}

	private function sort_score($a, $b)
	{
        if ($a['score'] == $b['score']) return 0;
        return ($a['score'] > $b['score'] ? -1 : 1);
	}
}
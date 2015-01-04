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

class ExtensionSocialRazorcmsBlogBook extends RazorAPI
{
	private $db = null;

	function __construct()
	{
		// REQUIRED IN EXTENDED CLASS TO LOAD DEFAULTS
		parent::__construct();

		// open extension db and attach razor db
		$this->db = new RazorPDO('sqlite:'.RAZOR_BASE_PATH.'storage/database/social_razorcms_blog.sqlite');
		$this->db->exec('ATTACH "'.RAZOR_BASE_PATH.'storage/database/razorcms.sqlite" AS razor');
	}

	public function get($blog_name)
	{
		if (empty($blog_name)) $this->response("Blog name not set", null, 400);

		// clean blog name
		$blog_name = preg_replace("/[^a-zA-Z0-9-_]/", "", $blog_name);

		$query = 'SELECT DISTINCT a.id'
			.', a.access_level'
			.', a.active'
			.', a.blog_id'
			.', a.content'
			.', a.title'
			.', a.published'
			.', a.user_id'
			.', c.name as "user_id.name"'
			.' FROM blog_item AS a'
			.' JOIN blog AS b ON b.id = a.blog_id'
			.' JOIN razor.user AS c ON c.id = a.user_id'
			.' WHERE b.name = :blog_name'
			.' ORDER BY a.published DESC';
		$data = $this->db->query_all($query, array(':blog_name' => $blog_name));

		if (empty($data)) $this->response("Blog name not found", null, 404);

		// json encode
		$this->response(array("items" => $data), "json");
	}

	public function put($data)
	{
		// login check - if fail, return no data to stop error flagging to user
		if ((int) $this->check_access() < 9) $this->response(null, null, 401);

		if (empty($data["blog_name"])) $this->response("Blog name not set", null, 400);

		// clean blog name
		$data["blog_name"] = preg_replace("/[^a-zA-Z0-9-_]/", "", $data["blog_name"]);

		// check if name already exists
		$blog = $this->db->get_first('blog', array('id'), array('name' => $data['blog_name']));
		if (!empty($blog)) $this->response("Blog name already created", null, 400);

		// create blog book
		$this->db->add_data('blog', array('active' => 1, 'name' => $data['blog_name'])); 

		// json encode
		$this->response(array("data" => $data["blog_name"]), "json");
	}
}

/* EOF */
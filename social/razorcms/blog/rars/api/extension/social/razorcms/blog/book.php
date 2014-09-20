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
	function __construct()
	{
		// REQUIRED IN EXTENDED CLASS TO LOAD DEFAULTS
		parent::__construct();
	}

	public function get($blog_name)
	{
		if (empty($blog_name)) $this->response("Blog name not set", null, 400);

		// clean blog name
		$blog_name = preg_replace("/[^a-zA-Z0-9-_]/", "", $blog_name);

		// grab blog id
		$db = new RazorDB();
		$db->connect("extension_razorcms_blog");
		$search = array("column" => "name", "value" => $blog_name);
		$blogs = $db->get_rows($search);
		$db->disconnect(); 

		if ($blogs["count"] != 1) $this->response("Blog name not found", null, 404);

		// grab blog items
		$db->connect("extension_razorcms_blog_item");

		// set options
		$options = array(
			"order" => array("column" => "id", "direction" => "desc"),
			"join" => array("table" => "user", "join_to" => "user_id"),
			"filter" => array("id", "access_level", "active", "blog_id", "content", "title", "published", "user_id", "user_id.name")
		);
		$search = array("column" => "blog_id", "value" => $blogs["result"][0]["id"]);
		$items = $db->get_rows($search, $options);
		$db->disconnect(); 

		// json encode
		$this->response(array("items" => $items["result"]), "json");
	}

	public function put($data)
	{
		// login check - if fail, return no data to stop error flagging to user
		if ((int) $this->check_access() < 9) $this->response(null, null, 401);

		if (empty($data["blog_name"])) $this->response("Blog name not set", null, 400);

		// clean blog name
		$data["blog_name"] = preg_replace("/[^a-zA-Z0-9-_]/", "", $data["blog_name"]);

		// create blog book
		$db = new RazorDB();
		$db->connect("extension_razorcms_blog");
		$search = array("column" => "name", "value" => $data["blog_name"]);
		$blogs = $db->get_rows($search);
		$db->disconnect(); 

		if ($blogs["count"] > 0) $this->response("Blog name already created", null, 400);

		// create blog book
		$db->connect("extension_razorcms_blog");
		$row = array("name" => $data["blog_name"], "active" => true);
		$db->add_rows($row);
		$db->disconnect(); 

		// json encode
		$this->response(array("data" => $data["blog_name"]), "json");
	}
}

/* EOF */
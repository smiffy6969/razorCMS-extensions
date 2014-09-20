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

class ExtensionSocialRazorcmsBlogItem extends RazorAPI
{
	function __construct()
	{
		// REQUIRED IN EXTENDED CLASS TO LOAD DEFAULTS
		parent::__construct();
	}

	// get blog item or items
	public function get($id)
	{
		// filter data
		$blog_id = (!empty($id) ? (int) $id : null);

		// grab blog items
		$db = new RazorDB();
		$db->connect("extension_razorcms_blog_item");

		// set options
		$options = array(
			"join" => array("table" => "user", "join_to" => "user_id"),
			"filter" => array("id", "access_level", "active", "blog_id", "content", "title", "published", "user_id", "user_id.name")
		);
		if ($blog_id) $search = array("column" => "id", "value" => $blog_id);
		else $search = array("column" => "id", "value" => null, "not" => true);
		$items = $db->get_rows($search, $options);
		$db->disconnect(); 

		if ($items["count"] < 1) $this->response("Blog item not found", null, 400);

		// json encode
		$this->response(($blog_id ? $items["result"][0] : $items["result"]), "json");
	}

	// save/update blog item
	public function put($data)
	{
		// check can at least contribute and data present
		if ((int) $this->check_access() < 6) $this->response(null, null, 401);
		if (empty($data) || !isset($data["item"]) || !isset($data["blog_name"])) $this->response("Blog data missing", null, 400);

		// filter data
		$data["blog_name"] = preg_replace("/[^a-zA-Z0-9-_]/", "", $data["blog_name"]);

		$db = new RazorDB();
		
		if (isset($data["item"]["id"]))
		{
			// update, check can edit
			if ((int) $this->check_access() < 7) $this->response(null, null, 401);
		
			// edit item
			$db->connect("extension_razorcms_blog_item");
			$search = array("column" => "id", "value" => $data["item"]["id"]);
			$items = $db->edit_rows($search, array(
				"title" => $data["item"]["title"],
				"content" => $data["item"]["content"],
				"updated" => time(),
				"active" => true,
				"user_id" => $this->user["id"]
			));
			$db->disconnect(); 
			if ($items["count"] != 1) $this->response("Blog item not found", null, 404);

			$id = $data["item"]["id"];
		}
		else
		{
			// grab blog
			$db->connect("extension_razorcms_blog");
			$search = array("column" => "name", "value" => $data["blog_name"]);
			$blogs = $db->get_rows($search);
			$db->disconnect(); 
		
			if ($blogs["count"] != 1) $this->response("Blog name not found", null, 404);

			// new item
			$db->connect("extension_razorcms_blog_item");
			$row = array(
				"blog_id" => $blogs["result"][0]["id"],
				"active" => true,
				"user_id" => $this->user["id"],
				"published" => time(),
				"updated" => time(),
				"title" => $data["item"]["title"],
				"content" => $data["item"]["content"],
				"access_level" => 0,
			);
			$items = $db->add_rows($row);

			$id = $items["result"][0]["id"];
		}

		// return item
		$db->connect("extension_razorcms_blog_item");
		$options = array(
			"amount" => 1,
			"join" => array("table" => "user", "join_to" => "user_id")
		);
		$search = array("column" => "id", "value" => $id);
		$items = $db->get_rows($search, $options);
		$db->disconnect(); 

		// json encode
		$this->response($items["result"][0], "json");
	}

	// delete blog item
	public function delete($id)
	{
		if ((int) $this->check_access() < 8) $this->response(null, null, 401);
		if (empty($id)) $this->response("Blog id missing", null, 400);

		// filter data
		$blog_id = (!empty($id) ? (int) $id : null);

		// grab blog items
		$db = new RazorDB();
		$db->connect("extension_razorcms_blog_item");
		$search = array("column" => "id", "value" => $blog_id);
		$db->delete_rows($search);
		$db->disconnect(); 

		// json encode
		$this->response("success", "json");
	}

}
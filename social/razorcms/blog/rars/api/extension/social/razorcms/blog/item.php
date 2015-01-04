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
	private $db = null;

	function __construct()
	{
		// REQUIRED IN EXTENDED CLASS TO LOAD DEFAULTS
		parent::__construct();

		// open extension db and attach razor db
		$this->db = new RazorPDO('sqlite:'.RAZOR_BASE_PATH.'storage/database/social_razorcms_blog.sqlite');
		$this->db->exec('ATTACH "'.RAZOR_BASE_PATH.'storage/database/razorcms.sqlite" AS razor');
	}

	// get blog item or items
	public function get($id)
	{
		$query = 'SELECT a.id, a.access_level'
			.', a.active'
			.', a.blog_id'
			.', a.content'
			.', a.title'
			.', a.published'
			.', a.user_id'
			.', b.name as "user_id.name"'
			.' FROM blog_item AS a'
			.' JOIN razor.user AS b ON b.id = a.user_id'
			.' WHERE a.id = :id';
		$item = $this->db->query_first($query, array(':id' => (int) $id));

		if (empty($item)) $this->response("Blog item not found", null, 400);

		// json encode
		$this->response($item, "json");
	}

	// save/update blog item
	public function put($data)
	{
		// check can at least contribute and data present
		if ((int) $this->check_access() < 6) $this->response(null, null, 401);
		if (empty($data) || !isset($data["item"]) || !isset($data["blog_name"])) $this->response("Blog data missing", null, 400);

		// filter data
		$data["blog_name"] = preg_replace("/[^a-zA-Z0-9-_]/", "", $data["blog_name"]);
		
		if (isset($data["item"]["id"]))
		{
			// update, check can edit
			if ((int) $this->check_access() < 7) $this->response(null, null, 401);
		
			$row = array(
				'title' => $data['item']['title'],
				'content' => $data['item']['content'],
				'updated' => time(),
				'active' => 1,
				'user_id' => $this->user['id']
			);
			$id = $this->db->edit_data('blog_item', $row, array('id' => $data["item"]["id"]), array('id'));
			if (empty($id)) $this->response("Blog item not found", null, 404);
		}
		else
		{
			$blog = $this->db->get_first('blog', array('id'), array('name' => $data['blog_name']));
			if (empty($blog)) $this->response("Blog name not found", null, 404);

			$row = array(
				"blog_id" => $blog['id'],
				"active" => true,
				"user_id" => $this->user["id"],
				"published" => time(),
				"updated" => time(),
				"title" => $data["item"]["title"],
				"content" => $data["item"]["content"],
				"access_level" => 0,
			);
			$id = $this->db->add_data('blog_item', $row, array('id'));
		}

		$query = 'SELECT a.id, a.access_level'
			.', a.active'
			.', a.blog_id'
			.', a.content'
			.', a.title'
			.', a.published'
			.', a.user_id'
			.', b.name as "user_id.name"'
			.' FROM blog_item AS a'
			.' JOIN razor.user AS b ON b.id = a.user_id'
			.' WHERE a.id = :id';
		$item = $this->db->query_first($query, array(':id' => (int) $id[0]['id']));

		// json encode
		$this->response($item, "json");
	}

	// delete blog item
	public function delete($id)
	{
		if ((int) $this->check_access() < 8) $this->response(null, null, 401);
		if (empty($id)) $this->response("Blog id missing", null, 400);

		$this->db->delete_data('blog_item', array('id' => (int) $id));

		// json encode
		$this->response("success", "json");
	}
}
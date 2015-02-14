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

class ExtensionCommunicationRazorcmsContactformEmail extends RazorAPI
{
	function __construct()
	{
		// REQUIRED IN EXTENDED CLASS TO LOAD DEFAULTS
		parent::__construct();

		session_start();
		session_regenerate_id();
	}

	public function post($data)
	{
		// Check details
		if (!isset($_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_USER_AGENT"], $_SERVER["HTTP_REFERER"], $_SESSION["signature"])) $this->response(null, null, 400);
		if (empty($_SERVER["REMOTE_ADDR"]) || empty($_SERVER["HTTP_USER_AGENT"]) || empty($_SERVER["HTTP_REFERER"]) || empty($_SESSION["signature"])) $this->response(null, null, 400);

		// check referer matches the site
		if (strpos($_SERVER["HTTP_REFERER"], RAZOR_BASE_URL) !== 0) $this->response(null, null, 400);

		// check data
		if (!isset($data["signature"], $data["email"], $data["message"], $data["extension"]["type"], $data["extension"]["handle"], $data["extension"]["extension"])) $this->response(null, null, 400);
		if (empty($data["signature"]) || empty($data["email"]) || empty($data["message"]) || empty($data["extension"]["type"]) || empty($data["extension"]["handle"]) || empty($data["extension"]["extension"])) $this->response(null, null, 400);
		if (!isset($data["human"]) || !empty($data["human"])) $this->response("robot", "json", 406);

		// get signature and compare to session
		if ($_SESSION["signature"] !== $data["signature"]) $this->response(null, null, 400);
		unset($_SESSION["signature"]);
		session_destroy();

		// create manifest path for extension that requested email
		$ext_type = preg_replace('/[^A-Za-z0-9-]/', '', $data["extension"]["type"]);
		$ext_handle = preg_replace('/[^A-Za-z0-9-]/', '', $data["extension"]["handle"]);
		$ext_extension = preg_replace('/[^A-Za-z0-9-]/', '', $data["extension"]["extension"]);
		$manifest_path = RAZOR_BASE_PATH."extension/{$ext_type}/{$ext_handle}/{$ext_extension}/{$ext_extension}.manifest.json";

		if (!is_file($manifest_path)) $this->response(null, null, 400);

		$manifest = RazorFileTools::read_file_contents($manifest_path, "json");

        // grab contact form settings
        $where = array(
            "type" => $manifest->type,
            "handle" => $manifest->handle,
            "extension" => $manifest->extension
        );

        $extension = $this->razor_db->get_first('extension', array('json_settings'), $where);

        if (empty($extension)) $this->response(null, null, 400);
		$extension_settings = json_decode($extension['json_settings']);

		// fetch extension settings and look for email
        $where = array(
            array("type" => $manifest->type),
            array("handle" => $manifest->handle),
            array("extension" => $manifest->extension)
        );

        $site = $this->razor_db->get_first('setting', array('value'), array('name' => 'name'));
		$site_name = json_decode($site['value']);

		// clean email data
		$to = $extension_settings->email;
		$from = preg_replace('/[^A-Za-z0-9-_+@.]/', '', $data["email"]);
		$subject = "{$site_name} Contact Form";
		$message = htmlspecialchars($data["message"], ENT_QUOTES);

		// send to email response
		$this->email($from, $to, $subject, $message);

		// return the basic user details
		$this->response("success", "json");
	}
}

/* EOF */

<?php if (!defined("RAZOR_BASE_PATH")) die("No direct script access to this content"); ?>

<?php
	// grab settings for this content area and from that, find folder to use
	$content_ext_settings = json_decode($c_data["json_settings"]);
?>

<!-- module output -->
<?php if (isset($content_ext_settings->ins_element)): ?>
	<div class="<?php echo ($content_ext_settings->class ? $content_ext_settings->class : '') ?>">
		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<?php echo $content_ext_settings->ins_element ?>
		<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
	</div>
<?php endif ?>
<!-- module output -->
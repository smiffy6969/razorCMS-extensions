<?php if (!defined("RAZOR_BASE_PATH")) die("No direct script access to this content"); ?>

<?php
	// restrict file searching to specific files
	$media_files = array("mp4", "mp3", "ogg", "ogv", "webm", "m4a");
	
	// grab settings for this content area and from that, find folder to use
	$content_ext_settings = json_decode($c_data["json_settings"]);

	if (isset($content_ext_settings->playlist_name))
	{
		// check if folders exist
		if (!is_dir(RAZOR_BASE_PATH."storage/files/razorcms")) mkdir(RAZOR_BASE_PATH."storage/files/razorcms");
		if (!is_dir(RAZOR_BASE_PATH."storage/files/razorcms/media-element-player")) mkdir(RAZOR_BASE_PATH."storage/files/razorcms/media-element-player");
		if (!is_dir(RAZOR_BASE_PATH."storage/files/razorcms/media-element-player/{$content_ext_settings->playlist_name}")) mkdir(RAZOR_BASE_PATH."storage/files/razorcms/media-element-player/{$content_ext_settings->playlist_name}");

		$playlist = array();
		$track = null;
		$type = null;
		$video = false;

		// work out media type
		if (isset($content_ext_settings->media_type) && in_array($content_ext_settings->media_type, $media_files))
		{
			$type = $content_ext_settings->media_type;
		}

		if (empty($type) && isset($content_ext_settings->track_name))
		{
			$path_parts = explode(".", $content_ext_settings->track_name);
			if (in_array(strtolower(end($path_parts)), $media_files)) $type = strtolower(end($path_parts));
		}

		if (isset($content_ext_settings->track_name))
		{
			// play single track
			$path = RAZOR_BASE_PATH."storage/files/razorcms/media-element-player/{$content_ext_settings->playlist_name}/{$content_ext_settings->track_name}";
			$url = RAZOR_BASE_URL."storage/files/razorcms/media-element-player/{$content_ext_settings->playlist_name}/{$content_ext_settings->track_name}";
			if (is_file($path))
			{
				$track = $url;
				$path_parts = explode(".", $track);
				$type = ($type ? $type : ".".end($track));
			}
		}

		// grab folder here, load in the files for a particular folder
		$files = RazorFileTools::read_dir_contents(RAZOR_BASE_PATH."storage/files/razorcms/media-element-player/{$content_ext_settings->playlist_name}", 'files');

		// remove anything not an image file ext
		foreach ($files as $key => $file)
		{
			$file_parts = explode(".", $file);
			if (!in_array(strtolower(end($file_parts)), $media_files) || end($file_parts) != (!empty($type) ? $type : "mp3")) continue;

			$playlist[$key] = array(
				"url" => RAZOR_BASE_URL."storage/files/razorcms/media-element-player/{$content_ext_settings->playlist_name}/{$file}",
				"name" => $file
			);
		}

		$playlist = array_values($playlist);

		// one final type check
		if (empty($type) && isset($playlist[0]["name"])) 
		{
			$path_parts = explode(".", $content_ext_settings->track_name);
			if (in_array(strtolower(end($playlist[0]["name"])), $media_files)) $type = strtolower(end($playlist[0]["name"]));
		}

		// detect element to show
		if (!empty($type)) if ($type == "ogv" || $type == "mp4" || $type == "webm") $video = true;
	}

	sort($playlist);
	// $json_playlist = json_encode($playlist);
	$json_playlist = str_replace('"', "'", json_encode(array_values($playlist)));

	// grab any settings
	$media_type = (isset($content_ext_settings->media_type) && !empty($content_ext_settings->media_type) ? $content_ext_settings->media_type : null);
	$player_colour = (isset($content_ext_settings->player_colour) && !empty($content_ext_settings->player_colour) ? $content_ext_settings->player_colour : "");
	$player_justify = (isset($content_ext_settings->player_justify) && !empty($content_ext_settings->player_justify) ? "pull-{$content_ext_settings->player_justify}" : "");
	$player_width = (isset($content_ext_settings->player_width) && !empty($content_ext_settings->player_width) ? "style=\"width: {$content_ext_settings->player_width};\"" : "");
?>

<!-- module output -->
<div class="media-razorcms-media-element-player" class="ng-cloak" ng-controller="mediaElementPlayer">
	<div class="player <?php echo $player_justify ?>  <? echo $player_colour ?>" <?php echo $player_width ?>>
		<?php if ($track || count($playlist) > 0): ?>
			<<?php echo ($video ? "video" : "audio") ?> class="media-element-player" src="<?php echo (!empty($track) ? $track : $playlist[0]["url"]) ?>" data-mejsoptions="{'alwaysShowControls': true}"></<?php echo ($video ? "video" : "audio") ?>>
		<?php endif ?>
		<?php if (count($playlist) > 0 && empty($track)): ?>
			<table class="playlist" ng-init="tracks = <?php echo $json_playlist ?>">
				<tbody>
					<?php foreach($playlist as $key => $item): ?>
						<tr class="track-listing" ng-click="changeTrack('<?php echo $item["url"] ?>', '<?php echo $key +1 ?>')">
							<td class="index"><?php echo $key +1 ?></td>
							<td>
								<i ng-if="selected == '<?php echo $key +1 ?>' && playing != '<?php echo $key +1 ?>'" class="fa fa-circle ng-cloak"></i>
								<i ng-if="selected == '<?php echo $key +1 ?>' && playing == '<?php echo $key +1 ?>'" class="fa fa-refresh fa-spin ng-cloak"></i>
							</td>
							<td class="name">
								<?php echo $item["name"] ?>
							</td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		<?php endif ?>
	</div>
</div>
<!-- module output -->

<!-- load dependancies -->
<?php if (!in_array("media-razorcms-media-element-player", $ext_dep_list)): ?>
	<?php $ext_dep_list[] = "media-razorcms-media-element-player-style" ?>
	<link type="text/css" rel="stylesheet" href="<?php echo RAZOR_BASE_URL ?>extension/media/razorcms/media-element-player/style/style.css">
	<link rel="stylesheet" href="<?php echo RAZOR_BASE_URL ?>extension/media/razorcms/media-element-player/js/media-element/mediaelementplayer.css" />
	<script src="<?php echo RAZOR_BASE_URL ?>extension/media/razorcms/media-element-player/js/module.js"></script>
<?php endif ?>
<!-- load dependancies -->
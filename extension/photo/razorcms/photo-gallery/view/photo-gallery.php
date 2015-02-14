<?php if (!defined("RAZOR_BASE_PATH")) die("No direct script access to this content"); ?>

<?php
	// grab settings for this content area and from that, find folder to use
	$c = json_decode($c_data["json_settings"]);

	// get settings or return defaults
	$m = array();
	foreach ($manifest->content_settings as $m_set) $m[$m_set->name] = (isset($c->{$m_set->name}) && !empty($c->{$m_set->name}) ? $c->{$m_set->name} : $m_set->value);

	// sort out album folder
	if (isset($m["album_name"]))
	{
		if (!is_dir(RAZOR_BASE_PATH.'storage/files')) mkdir(RAZOR_BASE_PATH.'storage/files');
		if (!is_dir(RAZOR_BASE_PATH.'storage/files/extension')) mkdir(RAZOR_BASE_PATH.'storage/files/extension');
		if (!is_dir(RAZOR_BASE_PATH.'storage/files/extension/photo')) mkdir(RAZOR_BASE_PATH.'storage/files/extension/photo');
		if (!is_dir(RAZOR_BASE_PATH.'storage/files/extension/photo/razorcms')) mkdir(RAZOR_BASE_PATH.'storage/files/extension/photo/razorcms');
		if (!is_dir(RAZOR_BASE_PATH.'storage/files/extension/photo/razorcms/photo-gallery')) mkdir(RAZOR_BASE_PATH.'storage/files/extension/photo/razorcms/photo-gallery');
		if (!is_dir(RAZOR_BASE_PATH."storage/files/extension/photo/razorcms/photo-gallery/{$m["album_name"]}")) mkdir(RAZOR_BASE_PATH."storage/files/extension/photo/razorcms/photo-gallery/{$m["album_name"]}");
	}
?>

<!-- module output -->
<div class="photo-razorcms-photo-gallery" class="ng-cloak" ng-controller="photoGallery" ng-init="init('<?php echo $m["album_name"] ?>')">
	<?php if ($this->logged_in >= MANAGER): ?>
		<global-notification></global-notification>
		<button class="btn btn-default manage-images" ng-click="manageImages('<?php echo $m["album_name"] ?>')"><i class="fa fa-picture-o"></i> Upload Photo Gallery Images</button>
	<?php endif ?>
	<div class="photo-gallery-frame text-center" style="height: <?php echo $m["frame_height"] ?>; width: <?php echo $m["frame_width"] ?>;">
		<div class="photo-gallery-canvas">
			<div class="photo-details ng-cloak" ng-if="photoFrame.title || photoFrame.description">
				<div class="details-box" ng-class="{'show-box': showBox}">
					<p class="text-center"><strong>{{photoFrame.title}}</strong></p>
					<p class="text-center">{{photoFrame.description}}</p>
				</div>
			</div>
			<i class="fa fa-chevron-circle-left photo-control change-left" ng-click="scrollPhotos('left')" ng-hide="position == 0"></i>
			<i class="fa fa-chevron-circle-right photo-control change-right" ng-click="scrollPhotos('right')" ng-hide="position == photos.length - 1"></i>
			<div class="center-box" style="line-height: <?php echo $m["frame_height"] ?>;">
				<img ng-show="photoFrame" ng-src="{{photoFrame.url}}" ng-class="{'turn-photo': turnPhoto}">
				<i ng-if="!photoFrame" class="fa fa-picture-o photo-placeholder"></i>
			</div>
		</div>
	</div>
	<div class="photo-gallery-controls" style="width: <?php echo $m["frame_width"] ?>;">
		<i class="fa fa-chevron-circle-left photo-control slide-left" ng-click="scrollThumbs('left')"></i>
		<i class="fa fa-chevron-circle-right photo-control slide-right" ng-click="scrollThumbs('right')"></i>
		<div class="photo-gallery-slider">
			<ul class="photo-gallery-thumbs" ng-style="sliderListStyle">
				<li ng-repeat="p in photos">
					<?php if ($this->logged_in >= MANAGER): ?>
						<i class="fa fa-times icon-remove" ng-click="removeImage('<?php echo $m["album_name"] ?>', $index)"></i>
					<?php endif ?>
					<img ng-src="{{p.url}}" ng-click="selectPhoto($index)" ng-class="{'selected': $index == position}">
				</li>
			</ul>
		</div>
	</div>
</div>
<!-- module output -->

<!-- load dependancies -->
<?php if (!in_array("photo-razorcms-photo-gallery-style", $ext_dep_list)): ?>
	<?php $ext_dep_list[] = "photo-razorcms-photo-gallery-style" ?>
	<link type="text/css" rel="stylesheet" href="<?php echo RAZOR_BASE_URL ?>extension/photo/razorcms/photo-gallery/style/style.css">
<?php endif ?>
<?php if (!in_array("photo-razorcms-photo-gallery-module", $ext_dep_list)): ?>
	<?php $ext_dep_list[] = "photo-razorcms-photo-gallery-module" ?>
	<script src="<?php echo RAZOR_BASE_URL ?>extension/photo/razorcms/photo-gallery/js/module.js"></script>
<?php endif ?>
<!-- load dependancies -->

<?php if (!defined("RAZOR_BASE_PATH")) die("No direct script access to this content"); ?>

<?php
	// grab settings for this content area and from that, find folder to use
	$c = json_decode($c_data["json_settings"]);

	// get settings or return defaults
	$m = array();
	foreach ($manifest->content_settings as $m_set) $m[$m_set->name] = (isset($c->{$m_set->name}) && !empty($c->{$m_set->name}) ? $c->{$m_set->name} : $m_set->value);
?>

<!-- module output -->
<div class="search-razorcms-site-search" class="ng-cloak" ng-controller="siteSearch">
	<form class="form-inline search-razorcms-site-search-form" ng-submit="search()">
		<div class="input-group">
			<input type="text" class="form-control" ng-model="searchPhrase">
			<span class="input-group-btn">
				<button type="submit" class="btn btn-default" type="button">
					<i class="fa fa-search"></i>
				</button>
				<button class="btn btn-default ng-cloak" type="button" ng-click="searchResults = null" ng-show="!!searchResults">
					<i class="fa fa-times"></i>
				</button>
			</span>
	    </div>
	</form>
	<div class="search-results ng-cloak" ng-show="searchResults">
		<ul class="search-razorcms-site-search-results">
			<li ng-repeat="sr in searchResults">
				<a ng-href="{{searchUrl(sr['page_id.link'])}}">
					<h4>
						<i class="fa fa-external-link-square"></i>
						<span class="name label label-info">{{sr['page_id.name']}}</span>
						<span class="title">{{sr['page_id.title']}}</span>
					</h4>
					<p>{{sr['page_id.description']}}</p>
				</a>
			</li>
		</ul>
	</div>
</div>
<!-- module output -->

<!-- load dependancies -->
<?php if (!in_array("search-razorcms-site-search-style", $ext_dep_list)): ?>
	<?php $ext_dep_list[] = "search-razorcms-site-search-style" ?>
	<link type="text/css" rel="stylesheet" href="<?php echo RAZOR_BASE_URL ?>extension/search/razorcms/site-search/style/style.css">
<?php endif ?>
<?php if (!in_array("search-razorcms-site-search-module", $ext_dep_list)): ?>
	<?php $ext_dep_list[] = "search-razorcms-site-search-module" ?>
	<script src="<?php echo RAZOR_BASE_URL ?>extension/search/razorcms/site-search/js/module.js"></script>
<?php endif ?>
<!-- load dependancies -->
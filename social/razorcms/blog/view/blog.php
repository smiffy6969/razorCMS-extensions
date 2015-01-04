<?php if (!defined("RAZOR_BASE_PATH")) die("No direct script access to this content"); ?>

<?php
	// create any new db files
	$blog_db = RAZOR_BASE_PATH."storage/database/social_razorcms_blog.sqlite";
	if (!file_exists($blog_db)) copy(RAZOR_BASE_PATH."extension/social/razorcms/blog/default/social_razorcms_blog.sqlite", $blog_db);

	// grab settings for this instance of the blog tool
	$c = json_decode($c_data["json_settings"]);

	// get settings or return defaults
	$m = array();
	foreach ($manifest->content_settings as $m_set) $m[$m_set->name] = (isset($c->{$m_set->name}) && !empty($c->{$m_set->name}) ? $c->{$m_set->name} : $m_set->value);

	// get any url query data
	$blog_id = (isset($_GET["id"]) && !empty($_GET["id"]) ? (int) $_GET["id"] : "");
?>

<!-- module output -->
<div class="social-razorcms-blog" class="ng-cloak" ng-controller="blog" ng-init="init('<?php echo $m["blog_name"] ?>', '<?php echo $blog_id ?>')">
	<?php if ($this->logged_in >= CONTRIBUTER): ?>
		<global-notification></global-notification>
	<?php endif ?>

	<p class="alert alert-danger ng-cloak" ng-if="blogError"><?php echo $m["blog_error"] ?></p>

	<!-- show single entry -->
	<div class="ng-cloak" ng-if="item">
		<div class="blog-view">
			<div class="row">
				<div class="col-sm-5">
					<a class="btn btn-default" href="?" ng-click="viewAllItems()"><i class="fa fa-arrow-left"></i> <?php echo $m["view_blogs_label"] ?></a>
				</div>
				<div class="blog-edit-controls col-sm-7 text-right">
					<?php if ($this->logged_in >= CONTRIBUTER): ?>
						<?php if ($this->logged_in >= EDITOR): ?>
							<button class="btn btn-default" ng-click="editBlogItem()" ng-if="!editBlogTitle"><i class="fa fa-pencil"></i></button>
							<?php if ($this->logged_in >= EDITOR): ?>
								<button class="btn btn-default" ng-click="deleteBlogItem()" ng-if="!editBlogTitle"><i class="fa fa-trash-o"></i></button>
							<?php endif ?>
						<?php endif ?>
						<button class="btn btn-default" ng-click="saveBlogItem()" ng-if="editBlogTitle"><i class="fa fa-check"></i></button>
						<button class="btn btn-default" ng-click="viewBlogItem(true)" ng-if="editBlogTitle"><i class="fa fa-times"></i></button>
					<?php endif ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group" ng-if="editBlogTitle">
						<div class="input-group">
							<div class="input-group-addon">Title</div>
							<input class="form-control" type="text" ng-model="$parent.$parent.newItemTitle" required></input>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<h3 class="blog-title" ng-if="!editBlogTitle">{{item.title}}</h3>
					<div class="blog-info" ng-show="!editBlogTitle">
						<span class="blog-details" ng-show="!!item.published"><i class="fa fa-user"></i> {{item['user_id.name']}}</span>
						<span class="blog-details" ng-show="!!item.published"><i class="fa fa-calendar"></i> {{item.published * 1000 | date:'d MMM y'}}</span>
						<span class="blog-details" ng-show="!!item.published"><i class="fa fa-clock-o"></i> {{item.published * 1000 | date:'hh:mm a'}}</span>
						<div class="blog-share">
							<a ng-href="http://www.linkedin.com/shareArticle?mini=true&url={{shareLink()}}" title="Share on LinkeIn" target="_blank">
								<i class="share-icon fa fa-linkedin"></i>
							</a>
							<a ng-href="https://plus.google.com/share?url={{shareLink()}}" title="Share on Google+" target="_blank">
								<i class="share-icon fa fa-google-plus"></i>
							</a>
							<a ng-href="http://www.facebook.com/sharer.php?u={{shareLink()}}" title="Share on Facebook" target="_blank">
								<i class="share-icon fa fa-facebook"></i>
							</a>	
							<a ng-href="https://twitter.com/share?url={{shareLink()}}" title="Share on Twitter" target="_blank">
								<i class="share-icon fa fa-twitter"></i>
							</a>
							<a ng-href="http://reddit.com/submit?url={{shareLink()}}&title={{urlEncode(item.title)}}" title="Share on Reddit" target="_blank">
								<i class="share-icon fa fa-reddit"></i>
							</a>
						</div>
					</div>
					<div id="blog-content" class="content" ng-bind-html="bindHtml(item.content)"></div>
					<?php if (!empty($m["disqus_shortname"])): ?>
						<div ng-if="!editBlogTitle" class="disqus-box" ng-init="loadDisqus('<?php echo $m["disqus_shortname"] ?>')">
						    <div id="disqus_thread"></div>
						    <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
						    <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
						</div>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>

	<!-- show most recent entries -->
	<div class="blog-items ng-cloak" ng-if="!item">
		<div class="row">
			<div class="col-sm-6">
				<div class="input-group search-blog-entries">
					<span class="input-group-addon"><i class="fa fa-search"></i></span>
					<input class="form-control" ng-model="searchFilter" ng-keydown="resetQuantity()"/>
				</div>
			</div>
			<div class="col-sm-6">
				<?php if ($this->logged_in >= CONTRIBUTER): ?>
					<button class="btn btn-default add-new-blog pull-right" ng-click="createBlogEntry()"><i class="fa fa-plus"></i></button>
				<?php endif ?>
			</div>
		</div>
		<div class="row">
			<div class="blog-item" ng-class="{'col-sm-12': $index == 0, 'col-sm-6': $index > 0 && $index < 5, 'col-sm-4': $index > 4}" ng-repeat="i in filterItems = (items | filter: searchFilter) | limitTo: itemQuantity">
				<div class="blog-container" ng-click="viewItem(i)">
					<div class="blog-detail">
						<span class="blog-details"><i class="fa fa-user"></i> {{i['user_id.name']}}</span>
						<span class="blog-details"><i class="fa fa-calendar"></i> {{i.published * 1000 | date:'d MMM y'}}</span>
						<span class="blog-details"><i class="fa fa-clock-o"></i> {{i.published * 1000 | date:'hh:mm a'}}</span>
					</div>
					<h3 class="title"><a ng-href="?id={{i.id}}&blog={{i.title}}">{{i.title}}</a></h3>
					<div class="content" ng-bind-html="bindHtml(i.content)"></div>
				</div>
			</div>
			<div class="text-center">
				<button class="btn btn-default" ng-if="filterItems.length > itemQuantity" ng-click="showMore()"><i class="fa fa-chevron-down"></i> <?php echo $m["show_more_label"] ?></button>
			</div>
		</div>
	</div>
</div>
<!-- module output -->

<!-- load dependancies -->
<?php if (!in_array("social-razorcms-blog-style", $ext_dep_list)): ?>
	<?php $ext_dep_list[] = "social-razorcms-blog-style" ?>
	<link type="text/css" rel="stylesheet" href="<?php echo RAZOR_BASE_URL ?>extension/social/razorcms/blog/style/style.css">
<?php endif ?>
<?php if (!in_array("social-razorcms-blog-module", $ext_dep_list)): ?>
	<?php $ext_dep_list[] = "social-razorcms-blog-module" ?>
	<script src="<?php echo RAZOR_BASE_URL ?>extension/social/razorcms/blog/js/module.js"></script>
<?php endif ?>
<!-- load dependancies -->
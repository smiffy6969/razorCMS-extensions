<?php if (!defined("RAZOR_BASE_PATH")) die("No direct script access to this content"); ?>

<!DOCTYPE html>
<html xmlns:ng="http://angularjs.org" id="ng-app">
	<head>
		<meta charset="utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="<?php echo $this->page["description"] ?>">
		<meta name="keywords" content="<?php echo $this->page["keywords"] ?>">

		<title><?php echo $this->site["name"] ?>::<?php echo $this->page["title"] ?></title>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,800italic,400,700,800,600' rel='stylesheet' type='text/css'>

		<!-- resolve base URL/IP/U-AGENT for any js applications -->
		<script type="text/javascript">
			var RAZOR_BASE_URL = "<?php echo RAZOR_BASE_URL ?>";
			var RAZOR_USERS_IP = "<?php echo RAZOR_USERS_IP ?>";
			var RAZOR_USERS_UAGENT = "<?php echo RAZOR_USERS_UAGENT ?>";
			var RAZOR_PAGE_ID = "<?php echo $this->page['id'] ?>";
		</script>

		<!-- require js -->
		<script <?php $this->data_main() ?> src="<?php echo RAZOR_BASE_URL ?>library/js/require.js"></script>
		<script src="<?php echo RAZOR_BASE_URL ?>library/js/require-config.js"></script>

		<!-- load your theme style here -->
		<link type="text/css" rel="stylesheet" href="<?php echo RAZOR_BASE_URL ?>extension/theme/swamper/bootswatch-slate/style/style.css">

		<!-- load bootstrap, style overrides and public css -->
		<!--[if IE 9]><link type="text/css" rel="stylesheet" href="<?php echo RAZOR_BASE_URL ?>library/style/razor/razor_base_ie9.css"><![endif]-->
		<!--[if IE 8]><link type="text/css" rel="stylesheet" href="<?php echo RAZOR_BASE_URL ?>library/style/razor/razor_base_ie8.css"><![endif]-->
		<!--[if IE 9]><link type="text/css" rel="stylesheet" href="<?php echo RAZOR_BASE_URL ?>extension/theme/swamper/style/style_ie9.css"><![endif]-->
		<!--[if IE 8]><link type="text/css" rel="stylesheet" href="<?php echo RAZOR_BASE_URL ?>extension/theme/swamper/style/style_ie8.css"><![endif]-->
	</head>

	<?php $this->body() ?>
		<div class="template-wrapper">
			<div class="template-header">
				<div class="container">
					<div class="row">
						<div class="col-sm-12">
							<div class="template-header-menu">
								<div class="navbar navbar-default navbar-fixed-top" role="navigation">
									<div class="container-fluid">
										<div class="navbar-header">
											<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
												<span class="sr-only">Toggle navigation</span>
												<i class="fa fa-ellipsis-v"></i>
											</button>
											<a class="navbar-brand" href="#"><?php echo $this->site["name"] ?></a>
										</div>

										<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
											<ul class="nav navbar-nav">
												<?php $this->menu("header"); ?>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<div class="template-header-content">
								<?php $this->content("header", 1); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="template-main">
				<div class="container">
					<div class="row">
						<div class="col-sm-4">
							<div class="template-main-content">
								<?php $this->content("main", 1); ?>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="template-main-content">
								<?php $this->content("main", 2); ?>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="template-main-content">
								<?php $this->content("main", 3); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="template-footer">
				<div class="container">
					<div class="row">
						<div class="col-sm-12">
							<div class="template-footer-menu">
								<ul class="nav mobile-hide-block">
									<?php $this->menu("footer"); ?>
								</ul>
								<ul class="nav nav-stacked mobile-show-block">
									<?php $this->menu("footer"); ?>
								</ul>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="template-footer-content">
								<?php $this->content("footer", 1); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>

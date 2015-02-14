<?php if (!defined("RAZOR_BASE_PATH")) die("No direct script access to this content"); ?>

<?php
	// check server details
	$signature = null;
    if (isset($_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_USER_AGENT"]) && !empty($_SERVER["REMOTE_ADDR"]) && !empty($_SERVER["HTTP_USER_AGENT"]))
    {
	    // signature generation
	    $signature = sha1($_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"].rand(0, 100000));
	    $_SESSION["signature"] = $signature;
    }
?>

<!-- module output -->
<div class="communication-razorcms-contact-form" class="ng-cloak" ng-controller="main" ng-init="init()">
	<div class="row">
		<div class="col-sm-12" ng-show="response || robot || error">
			<p class="alert alert-success ng-cloak" ng-show="response"><i class="fa fa-check"></i> Thank you, your message has been sent.</p>
			<p class="alert alert-danger ng-cloak" ng-show="robot"><i class="fa fa-exclamation-triangle"></i> You did not pass the human test, your message was not sent.</p>
			<p class="alert alert-danger ng-cloak" ng-show="error"><i class="fa fa-exclamation-triangle"></i> Could not send message, please try again later.</p>
		</div>			
	</div>
	<div class="row">
		<div class="col-sm-12">
			<form name="form" class="form-horizontal" role="form" ng-class="{'message-sent': response}" novalidate>
				<input type="hidden" ng-model="signature" ng-init="signature = '<?php echo $signature ?>'">
				<div class="form-group">
					<label for="contact-form-email" class="col-sm-3 control-label">Your Email</label>
					<div class="col-sm-7">
						<input id="contact-form-email" name="email" class="form-control" type="text" ng-model="email" placeholder="you@somewhere.com" ng-pattern="/^\S+@\S+\.\S+$/" required>
					</div>
					<div class="col-sm-2 error-block ng-cloak" ng-show="form.email.$dirty && form.email.$invalid">
						<span class="alert alert-danger alert-form" ng-show="form.email.$error.required">Required</span>
						<span class="alert alert-danger alert-form" ng-show="form.email.$error.pattern">Invalid</span>
					</div>
				</div>
				<div class="form-group">
					<label for="contact-form-message" class="col-sm-3 control-label">Message</label>
					<div class="col-sm-7">
						<textarea id="contact-from-message" name="message" class="form-control" type="text" ng-model="message" required></textarea>
					</div>
					<div class="col-sm-2 error-block ng-cloak" ng-show="form.message.$dirty && form.message.$invalid">
						<span class="alert alert-danger alert-form" ng-show="form.message.$error.required">Required</span>
					</div>
				</div>
				<div class="form-group">
					<label for="contact-form-human" class="col-sm-3 control-label">Are You Human?</label>
					<div class="col-sm-7">
						<input id="contact-form-human" name="human" class="form-control" type="text" ng-model="human" ng-init="human = 'DELETE THIS TEXT IF HUMAN'">
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-7">
						<button type="submit" class="btn btn-success" ng-click="send()" ng-disabled="form.$invalid || processing">
							<i class="fa fa-envelope"></i> 
							Send
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- module output -->

<!-- load dependancies -->
<?php if (!in_array("communication-razorcms-contact-form-style", $ext_dep_list)): ?>
	<?php $ext_dep_list[] = "communication-razorcms-contact-form-style" ?>
	<link type="text/css" rel="stylesheet" href="<?php echo RAZOR_BASE_URL ?>extension/communication/razorcms/contact-form/style/style.css">
<?php endif ?>
<?php if (!in_array("communication-razorcms-contact-form-module", $ext_dep_list)): ?>
	<?php $ext_dep_list[] = "communication-razorcms-contact-form-module" ?>
	<script src="<?php echo RAZOR_BASE_URL ?>extension/communication/razorcms/contact-form/js/module.js"></script>
<?php endif ?>
<!-- load dependancies -->
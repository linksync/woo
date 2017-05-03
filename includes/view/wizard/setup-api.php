<h1>API Key configuration</h1>
<hr>
<?php
	$message = get_option('linksync_error_message');
	if($message) {
		?>
		<p class="error-message"><?php echo $message; ?></p>
		<?php
		delete_option('linksync_error_message');
	}
?>
<form class="wizard-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="process" value="wizard" />
	<input type="hidden" name="action" value="apikey" />
	<input type="hidden" name="nextpage" value="2" />
	<p class="form-holder">
		<input type="text" id="api_key" name="linksync[api_key]" class="form-field" placeholder="Enter you API KEY"  value="<?php echo $laid; ?>"/>
	</p>
    <span><em>Your unique API Key is created when you linked your apps at <a href="https://my.linksync.com/" target="_blank">my.linksync.com</a> </em></span>
	<p class="form-holder">
		<input type="submit" name="submit" value="Next Step" />
	</p>
	<div class="clearfix"></div>
</form>

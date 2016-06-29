<form role="search" method="get" id="searchform" class="searchform" action="<?php echo get_option('home'); ?>/">
	<div>
		<label class="screen-reader-text" for="s">Search for:</label>
		<input type="text" value="" name="s" id="s" />
		<?php if(isset($_GET['loc'])) :?>
			<input type="hidden" value="<?php echo $_GET['loc']; ?>" name="loc" />
		<?php endif; ?>
		<input type="submit" id="searchsubmit" value="Search" />
	</div>
</form>
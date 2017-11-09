<?php
	/**
	 * Floating bar template
	 */

	$content = isset($content) ? $content : '';
?>

<div class="anssp-float-bar hidden">
	<?= $content ?>
</div>

<script>
	(function($) {
		"use strict";
		$(function() {
			$('.anssp-float-bar').appendTo('body').removeClass('hidden');
		}); 
	})(jQuery);
</script>
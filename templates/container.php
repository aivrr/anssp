<?php
	/**
	 * Drag container template on settings page
	 */

	$content = isset($content) ? $content : '';
	$id = isset($id) ? $id : '';
	$label = isset($label) ? $label : '';
?>

<div class="anssp-drag-container-wrap">
	<h4><?= $label ?></h4>
	<div class="anssp-drag-container" id="<?= $id ?>">	
		<?= $content ?>
	</div>
</div>

<?php
	/**
	 * Social share button template
	 */

	$svg = isset($svg) ? $svg : '';
	$label = isset($label) ? $label : '';
	$size = isset($size) ? $size : '';
	$share_name = isset($share_name) ? $share_name : '';
	$border_style = !empty($color) ? sprintf('style="border-color: %s"', $color) : '';


	if (isset($link)) {
		?>
			<a class="anssp-button-link" href="<?= $link ?>" target="_blank">
		<?php
	}
?>

<div class="anssp-button <?= $size ?>" data-share-name="<?= $share_name ?>" <?= $border_style ?>>
	<?= $svg ?>
	<label class="anssp-button-label"><?= $label ?></label>
</div>

<?php
	if (isset($link)) {
		?>
			</a>
		<?php
	}

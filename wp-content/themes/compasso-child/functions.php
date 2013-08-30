<?php
/**
 * override rating function from the parent theme,
 * to show a number instead of an image
 */
function g7_rating($rating, $size = '') {
	return sprintf(
		'<span class="custom-rating %s">%s</span>',
		$size,
		$rating
	);
}
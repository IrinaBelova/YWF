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

/**
 * Shows menu from a location
 */
function g7_menuFuckers($location, $class = '') {
	if (has_nav_menu($location)) {
		wp_nav_menu(array(
			'theme_location' => $location,
			'container' => '',
			'menu_id' => $location,
			'menu_class' => $class
		));
	} else {
		echo '<ul id="'.$location.'"><li><a href="'.home_url().'"><i class="icon-home icon-3x"></i></a>';
		wp_list_pages('title_li=');
		echo '</ul>';
	}
}
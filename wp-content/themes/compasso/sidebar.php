<?php
$sidebar_id = 'sidebar';
if (is_page()) {
	$sidebar_name = get_post_meta(get_the_ID(), '_g7_sidebar', true);
	if (!empty($sidebar_name)) {
		$sidebar_id = g7_sidebar_id($sidebar_name);
	}
}
?>
<div id="sidebar">
	<ul>
		<?php dynamic_sidebar($sidebar_id); ?>
	</ul>
</div>
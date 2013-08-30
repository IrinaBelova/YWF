<?php
$tag_desc = '';
$tag_description = tag_description();
if (!empty($tag_description)) {
	$tag_desc = apply_filters('tag_archive_meta', '<div class="archive-meta">' . $tag_description . '</div>');
}

get_header();
?>

<?php get_template_part('wrapper', 'start'); ?>

	<?php if (have_posts()) : ?>

		<header class="page-header box mb20">
			<?php g7_breadcrumbs(); ?>
			<h1 class="page-title"><?php echo single_tag_title('', false); ?></h1>
			<?php echo $tag_desc; ?>
		</header>

		<?php while (have_posts()) : the_post(); ?>
			<?php get_template_part('content'); ?>
		<?php endwhile; ?>

		<?php g7_pagination(); ?>

	<?php else : ?>

		<?php get_template_part('content', 'none'); ?>

	<?php endif; ?>

<?php get_template_part('wrapper', 'end'); ?>

<?php get_footer(); ?>
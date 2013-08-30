<?php get_header(); ?>

<?php get_template_part('wrapper', 'start'); ?>

	<?php if (have_posts()) : ?>

		<header class="page-header box mb20">
			<?php g7_breadcrumbs(); ?>
			<h1 class="page-title">
				<?php printf(__('Search Results for: %s', 'g7theme'), '<span>' . get_search_query() . '</span>' ); ?>
			</h1>
		</header>

		<div class="list-container mb20">
			<?php while (have_posts()) : the_post(); ?>
				<?php get_template_part('content'); ?>
			<?php endwhile; ?>
		</div>

		<?php g7_pagination(); ?>

	<?php else : ?>

		<?php get_template_part('content', 'none'); ?>

	<?php endif; ?>

<?php get_template_part('wrapper', 'end'); ?>

<?php get_footer(); ?>
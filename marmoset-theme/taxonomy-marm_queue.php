<?php get_header(); ?>

<div class="project-queue" data-queue="<?php echo get_query_var('term'); ?>">
	<?php dynamic_sidebar( 'Project List' ); ?>
</div>

<?php get_footer(); ?>

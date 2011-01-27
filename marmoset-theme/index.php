<?php get_header(); ?>
<?php if( get_query_var('marm_widget_area') ) : ?>
	<div class="project-queue" data-queue="<?php echo get_query_var('term'); ?>">
		<?php dynamic_sidebar( get_query_var('marm_widget_area') ); ?>
	</div>
<?php else: ?>
	<?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>

		<h2><?php the_title(); ?></h2>
		<div><?php the_content(); ?></div>

	<?php endwhile ; else : ?>

		<p>Nothing to display.</p>

	<?php endif; ?>
<?php endif; ?>

<?php get_footer(); ?>

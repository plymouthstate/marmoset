<?php get_header(); ?>

<?php if( have_posts() ) : while( have_posts() ) : the_loop(); ?>

	<h2><?php the_title(); ?></h2>
	<div><?php the_content(); ?></div>

<?php endwhile ; else : ?>

	<p>Nothing to display.</p>

<?php endif; ?>

<?php get_footer(); ?>

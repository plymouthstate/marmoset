<?php get_header(); ?>

<?php $active_term = get_query_var('term'); ?>

<div class="project-queue" data-queue="<?php echo $active_term; ?>">
	<?php
	
	if( is_active_sidebar( 'queue-' . $active_term ) ) {
		dynamic_sidebar( 'queue-' . get_query_var('term') );
	} else {
		dynamic_sidebar( 'default-project-list' );
	}

	?>
</div>

<?php get_footer(); ?>

<?php get_header(); ?>

<?php $terms = get_terms('marm_queue'); ?>

<ul class="queues">
<?php foreach( $terms as $term): ?>
	<li><a href="<?php bloginfo('url'); ?>/queue/<?php echo $term->slug; ?>/"><?php echo $term->name; ?></a></li>
<?php endforeach; ?>
</ul>

<?php get_footer(); ?>

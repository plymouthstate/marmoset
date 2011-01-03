<ol class="projects">
<?php if ( have_posts() ) : $i = 1; ?>
	<?php while ( have_posts() ) : the_post(); ?>
	<?php $taxonomies = get_the_taxonomies(); ?>
	<?php $post->queue = wp_get_object_terms( get_the_ID(), array('marm_queue') ); $post->queue = $post->queue[0]; ?>
	<?php $post->complexity = (int) get_post_meta( get_the_ID(), 'project_complexity', true ); ?>
	<li data-postid="<?php the_ID(); ?>" <?php post_class('project'); ?>> 
		<span class="item-number"><?php echo $i; ?>.</span>
		<div class="contents">
			<div class="project-title">
				<h2>
					<span><span class="type"><a href="<?php bloginfo('url'); ?>/queue/<?php echo $post->queue->slug; ?>/"><?php echo $post->queue->name; ?> Project</a> &raquo;</span> <?php the_title(); ?></span>
				</h2>
				<span class="date" title="Estimated Date of Completion"><span><?php Marmoset::the_due_date(); ?></span></span>
				<span class="permalink">[<a href="<?php the_permalink(); ?>" title="View Project Details">Details</a>]</span>
			</div>
			<div data-complexity="<?php echo $post->complexity; ?>" class="complexity complexity-<?php echo $post->complexity; ?>" title="Project Complexity (<?php echo $post->complexity; ?>)">
				<span class="readable"><?php echo $post->complexity; ?></span>
				<span class="indicator indicator-1"></span><span class="indicator indicator-2"></span><span class="indicator indicator-3"></span><span class="indicator indicator-4"></span><span class="indicator indicator-5"></span></span>
			</div>
			<div class="progress-container">
				<div class="progress progress-<?php echo get_post_meta( get_the_ID(), 'project_progress', true ); ?>"></div> 
			</div>
		</div> 
		<div class="details">
			<ul class="meta">
				<li class="members"><?php echo $taxonomies['marm_members']; ?></li>
				<?php if( $taxonomies['marm_stakehold'] ): ?><li class="stakehold"><?php echo $taxonomies['marm_stakehold']; ?></li><?php endif; ?>
			</ul>
			<?php if( get_the_content() ): ?>
				<div class="body">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>
		</div>
	</li> 
	<?php $i++; ?>
	<?php endwhile; ?>
<?php endif; ?>
</ol>
<p class="<?php if( have_posts() ) echo 'hidden'; ?>"><?php _e('There are currently no projects with this status.'); ?></p>

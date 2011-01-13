<ol class="projects">
<?php if ( have_posts() ) : $i = 1; ?>
	<?php while ( have_posts() ) : the_post(); ?>
	<?php Marmoset::get_the_queue(); ?>
	<?php $post->meta = get_post_meta( $post->ID, '' ); ?>
	<li data-postid="<?php the_ID(); ?>" <?php post_class('project'); ?>> 
		<span class="item-number"><?php echo $i; ?>.</span>
		<div class="contents">
			<div class="project-title">
				<h2>
					<span><span class="type"><a href="<?php bloginfo('url'); ?>/queue/<?php echo $post->queue->slug; ?>/"><?php echo $post->queue->name; ?> Project</a> &raquo;</span> <?php the_title(); ?></span>
				</h2>
				<span class="date" title="Estimated Date of Completion"><span><?php Marmoset::the_due_date('Y-m-d'); ?></span></span>
				<span class="permalink">[<a href="<?php the_permalink(); ?>" title="View Project Details">Details</a>]</span>
			</div>
			<div data-complexity="<?php the_project_complexity(); ?>" data-complexity-original="<?php the_project_complexity(); ?>" class="complexity complexity-<?php the_project_complexity(); ?>" title="Project Complexity (<?php the_project_complexity(); ?>)">
				<span class="readable"><?php the_project_complexity(); ?></span>
				<span class="indicator indicator-1"></span><span class="indicator indicator-2"></span><span class="indicator indicator-3"></span><span class="indicator indicator-4"></span><span class="indicator indicator-5"></span></span>
				<ul>
					<li class="complexity-clear">clear</li>
					<li class="complexity-reset">reset</li>
				</ul>
			</div>
			<div class="progress-container">
				<div class="progress progress-<?php echo get_post_meta( get_the_ID(), 'project_progress', true ); ?>"></div> 
			</div>
		</div> 
		<div class="details">
			<ul class="meta">
				<li class="queue"><?php Marmoset::the_queue(); ?></li>
				<li class="status"><?php Marmoset::the_status(); ?></li>
				<li class="proposed_date">Date Proposed: <?php Marmoset::the_proposed_date(); ?></li>
				<li class="due_date">Date Due: <?php Marmoset::the_due_date(); ?></li>
				<li class="members"><?php Marmoset::the_members(); ?></li>
				<?php if( Marmoset::get_the_stakeholders() ): ?><li class="stakehold"><?php Marmoset::the_stakeholders(); ?></li><?php endif; ?>
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

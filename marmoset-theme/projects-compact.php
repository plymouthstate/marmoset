<ol class="projects">
<?php global $marmoset_theme; ?>
<?php if ( have_posts() ) : $i = 1; ?>
	<?php while ( have_posts() ) : the_post(); ?>
	<?php 
		Marmoset::get_the_queue(); 
		global $post; 
		$post->meta = get_post_meta( $post->ID, '' );

		$class = 'project'.(Marmoset::is_overdue( $args ) ? ' past-due' : '');
	?>
	<li data-postid="<?php the_ID(); ?>" <?php post_class($class); ?>>
		<span class="item-number"><?php echo $i; ?>.</span>
		<div class="contents" title="<?php if( Marmoset::is_overdue( $args ) ) : echo Marmoset::get_the_overdue_date(); endif; ?>">
			<div class="project-title">
				<h2>
					<span><span class="type"><a href="<?php bloginfo('url'); ?>/queue/<?php echo $post->queue->slug; ?>/"><?php echo $post->queue->name; ?></a> &raquo;</span> <?php the_title(); ?></span>
				</h2>

				<span class="progress-percent">(<span>Progress:</span><?php echo Marmoset::get_the_progress(); ?>%)</span>
				<span class="date" title="<?php Marmoset::format_date_title( $args[ 'date_display' ] ) ; ?>"><span ><?php if( $args[ 'date_display' ] ) : Marmoset::the_date( $args[ 'date_display' ], 'F d, Y' ); endif; ?></span></span>
				<span class="permalink">
					[<a href="<?php the_permalink(); ?>" title="View Project Details">Details</a>]
					<span class="editors-only">[<a href="<?php echo get_edit_post_link(); ?>" title="Edit Project Details">Edit</a>]</span>
				</span>
			</div>
			<div data-complexity="<?php Marmoset::the_complexity_int(); ?>" data-complexity-original="<?php Marmoset::the_complexity_int(); ?>" class="complexity <?php Marmoset::the_complexity_slug(); ?>" title="<?php Marmoset::the_complexity_name(); ?>">
				<span class="readable"><?php Marmoset::the_complexity_name(); ?></span>
				<span class="indicator indicator-1">*</span><span class="indicator indicator-2">*</span><span class="indicator indicator-3">*</span><span class="indicator indicator-4">*</span><span class="indicator indicator-5">*</span></span>
				<ul>
					<li class="complexity-reset">reset</li>
				</ul>
			</div>
			<div class="progress-container">
				<div class="progress progress-<?php Marmoset::the_progress(); ?>"></div>
			</div>
		</div>
		<div class="details">
			<ul class="meta">
				<li class="queue"><?php Marmoset::the_queue(); ?></li>
				<li class="status"><?php Marmoset::the_status(); ?></li>
				<?php if( Marmoset::get_the_estimated_start_date() ): ?><li class="estimated_start_date"><span class="label">Estimated Start Date:</span><?php Marmoset::the_estimated_start_date(); ?></li><?php endif; ?>
				<?php if( Marmoset::get_the_start_date() ): ?><li class="start_date"><span class="label"><span class="label">Actual Start Date:</span> <?php Marmoset::the_start_date(); ?></li><?php endif; ?>
				<?php if( Marmoset::get_the_due_date() ): ?><li class="due_date"><span class="label">Due Date:</span> <?php Marmoset::the_due_date(); ?></li><?php endif; ?>
				<?php if( Marmoset::get_the_complete_date() ): ?><li class="complete_date"><span class="label">Date Completed:</span> <?php Marmoset::the_complete_date(); ?></li><?php endif; ?>
				<?php if( Marmoset::get_the_members() ): ?><li class="members"><?php Marmoset::the_members(); ?></li><?php endif; ?>
				<?php if( Marmoset::get_the_stakeholders() ): ?><li class="stakeholders"><?php Marmoset::the_stakeholders(); ?></li><?php endif; ?>
				<?php if( Marmoset::get_the_complexity_description() ): ?><li class="complexity">Project Complexity: <?php Marmoset::the_complexity_description();  ?></li><?php endif; ?>
			</ul>
			<?php if( get_the_content() ): ?>
				<div class="body">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>
		</div>
		<div id="comments">
			<?php comments_template(); ?>
		</div>
	</li>
	<?php $marmoset_theme->update_found_terms(); ?>
	<?php $i++; ?>
	<?php endwhile; ?>
<?php endif; ?>
</ol>
<p class="<?php if( have_posts() ) echo 'hidden'; ?>"><?php _e('There are currently no projects with this status.'); ?></p>

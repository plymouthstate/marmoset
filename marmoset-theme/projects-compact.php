<ol class="projects">
<?php if ( have_posts() ) : $i = 1; ?>
	<?php while ( have_posts() ) : the_post(); ?>
	<?php $taxonomies = get_the_taxonomies(); ?>
	<li data-postid="<?php the_ID(); ?>" <?php post_class('project'); ?>> 
		<span class="item-number"><?php echo $i; ?>.</span>
		<div class="contents">
			<div class="progress progress-<?php echo get_post_meta( get_the_ID(), 'project_progress', true ); ?>"></div> 
			<div class="project-title">
				<h2>
					<span><?php the_title(); ?></span>
					<sup><?php echo get_post_meta( get_the_ID(), 'project_order', true ); ?></sup>
					<span class="date"><span><?php Marmoset::the_due_date(); ?></span></span>
					<span class="permalink">[<a href="<?php the_permalink(); ?>">Details</a>]</span>
				</h2>
			</div>
			<div class="complexity complexity-<?php echo (int)get_post_meta( get_the_ID(), 'project_complexity', true ); ?>">
				<span class="readable"><?php echo (int)get_post_meta( get_the_ID(), 'project_complexity', true ); ?></span>
				<span class="circle-1"></span><span class="circle-2"></span><span class="circle-3"></span><span class="circle-4"></span><span class="circle-5"></span></span>
			</div>
		</div> 
		<div class="details">
			<?php if( get_the_content() ): ?>
				<div class="body">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>
			<ul class="meta">
				<li class="members"><?php echo $taxonomies['marm_members']; ?></li>
				<?php if( $taxonomies['marm_stakehold'] ): ?><li class="stakehold"><?php echo $taxonomies['marm_stakehold']; ?></li><?php endif; ?>
			</ul>
		</div>
	</li> 
	<?php $i++; ?>
	<?php endwhile; ?>
<?php endif; ?>
</ol>
<p class="<?php if( have_posts() ) echo 'hidden'; ?>"><?php _e('There are currently no projects with this status.'); ?></p>

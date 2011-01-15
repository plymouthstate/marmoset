<?php require_once ABSPATH . '/wp-admin/includes/template.php'; ?>
<div id="submit-container">
<h2>Submit Project</h2>
<form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" class="submit-project" method="post">
	<input type="hidden" name="action" value="submit_project">
	<?php Marmoset::wp_nonce_field(); ?>
	<ul>
		<li>
			<label>Name:</label>
			<input name="marm-title" type="text" size="30">
		</li>
		<li>
			<label>Description:</label>
			<textarea name="marm-content"></textarea>
		</li>
		<li>
			<label>Due date:</label>
			<input name="marm-duedate" type="text" size="30">
		</li>
		<li class="stakeholders">
			<label for="stakeholders">Stakeholders:</label>
			<?php
			// hide checkboxes on top-level terms if there are child terms
			$stakeholder_nochildren = wp_count_terms('marm_stakeholders') == wp_count_terms('marm_stakeholders', array( 'parent' => 0 ) );
			?>
			<?php if ( wp_count_terms('marm_stakeholders') > 0 ): ?>
				<div class="input-stakeholders stakeholder-levels-<?php echo $stakeholder_nochildren ? 1 : 2; ?>">
					<ul>
						<?php wp_terms_checklist( 0, 'taxonomy=marm_stakeholders' ); ?>
					</ul>
				</div>
			<?php else: ?>
				The list of stakeholders is currently empty.
			<?php endif; ?>
			<br>
			<div class="hidden">
				<input type="text" name="marm-stakeholder[]" size="20" placeholder="New stakeholder"><!-- No space, doesn't work with .siblings() anyway. --><a href="#">Delete</a><br>
				<a href="#">Add another&hellip;</a>
			</div>
			<div>Don't see your group? <a href="#">Create new stakeholders</a>.</div>
		</li>
		<li>
			<ul>
				<?php 
					$args = array(
						'fields' => 'all',
						'hide_empty' => 0,
						'orderby' => 'slug',
					);
					$complexities = get_terms( 'marm_complexity', $args );

					foreach( $complexities as $complexity )
					{
						?>

							<li>
								<input value="<?php echo $complexity->slug; ?>" type="radio" name="complexity" /> 
								<label><?php echo $complexity->name.' - '.$complexity->description; ?> </label>
							</li>

						<?php
					}//end foreach
				?>
			</ul>
		</li>
		<li>
			<label for="">&nbsp;</label>
			<input type="submit" value="Submit Project" class="button">
		</li>
	</ul>
</form>
</div>

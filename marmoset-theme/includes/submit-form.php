<?php require_once ABSPATH . '/wp-admin/includes/template.php'; ?>
<div id="submit-container">
<h2>Submit Project</h2>
<form class="submit-project" method="post">
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
			<!--<div class="help">Don't see your group? <a href="#">Create new stakeholders</a>.</div>-->
		</li>
		<li>
			<label for="complexity">Complexity:</label>
			<?php wp_dropdown_categories("hide_empty=0&taxonomy=marm_complexity&orderby=slug&name=marm-complexity"); ?>
		</li>
		<li class="actions">
			<label for="">&nbsp;</label>
			<button type="submit" class="button save">Submit Project</button>
			<a href="" class="cancel">Cancel Submission</a>
		</li>
	</ul>
</form>
</div>

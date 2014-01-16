<?php extend_template('default') ?>
	
	<h2><?=$institution_name?> Subscription Information</h2>
	<?php if($expired === TRUE):  ?>
		<p style='color: red'><strong><?= $institution_name ?> does not currently have an active subscription</strong>.  New students will not be able to register.</p>
	<?php endif; ?>	
	<?php if($expired === FALSE): ?>
	<p>Your subscription expires on: <b><?= $expiry_date ?></b></p>
		<ul class="bullets">
			
			<li>
				<h3>Student registration link</h3>
				<p>
				Place this link on the course page for students using <?=$institution_name?>'s LMS.
				<textarea>
					<?=$student_url?>
				</textarea>
				</p>
			</li>
			
			<li>
				<h3>Educator registration link</h3>
				<p>
				Paste this link into an email to the practice educators that will observe students.
				<textarea>
					<?=$educator_url?>
				</textarea>
			</p></li>
		</ul>
	<?php endif; ?>
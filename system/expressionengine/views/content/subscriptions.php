<?php extend_template('default') ?>
	
	<?php if($expired):  ?>
		<p style='color: red'><strong><?= $institution_name ?> does not currently have an active subscription</strong>.  Your subscription expired on <strong><?= $expiry_date ?></strong>.
			New students will not be able to register and existing users from <?=$institution_name?> will not have access to the subscribed areas of the site.</p>
			
	<?php endif; ?>	
	<?php if(!$expired): ?>
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
			
			<li>
				<h3>Lecturer registration link</h3>
				<p>
				Email this link to your Lecturers.
				<textarea>
					<?=$lecturer_url?>
				</textarea>
				</p>
			</li>
		</ul>
	<?php endif; ?>
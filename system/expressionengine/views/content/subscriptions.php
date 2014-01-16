<?php extend_template('default') ?>
	
	<h3><?=$institution_name?> Subscription Information</h3>
	<?php if($expired === TRUE):  ?>
		<p style='color: red'><strong><?= $institution_name ?> does not currently have an active subscription</strong>.  New students will not be able to register.</p>
	<?php endif; ?>	
	<?php if($expired === FALSE): ?>
	<p>Your subscription expires on: <b><?= $expiry_date ?></b></p>
		<ul class="bullets">
			
			<li><p>
				Student registration link:<br>
				Place this link on the course page for students using <?=$institution_name?>'s LMS.
				<textarea>
					<?=$student_url?>
				</textarea>
				</p>
			</li>
			
			<li><p>
				Educator registration link:<br>
				Paste this link into an Email to Practice Educators.
				<textarea>
					<?=$educator_url?>
				</textarea>
			</p></li>
		</ul>
	<?php endif; ?>
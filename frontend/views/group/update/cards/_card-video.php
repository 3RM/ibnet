
	<?= $update->title ? '<h3>' . $update->title . '</h3>' : NULL; ?>
	<?= $update->description ? '<p>' . $update->description . '</p>' : NULL ?>
			
	<div class="update-video-wrapper">
		<?= $update->getVideo(true) ?>
	</div>
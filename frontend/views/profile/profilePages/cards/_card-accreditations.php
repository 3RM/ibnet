<?php
use common\models\profile\Profile;	
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>Accreditations:</p>
			<div class="right">
				<ul>
					<?php foreach ($accreditations as $accreditation) {
						echo '<li>' . HTML::a($accreditation->association . '&nbsp' . Html::icon('new-window', ['class' => 'internal-link']), $accreditation->website, ['target' => '_blank', 'rel' => 'noopener noreferrer']) . '</li>';
					} ?>
				</ul>
			</div>
		</div>
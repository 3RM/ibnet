<?php
use common\models\profile\Profile;	
use common\models\profile\ServiceTime;
use yii\bootstrap\Html;
?>

		<div class="card title-list">
			<p>Service Times:</p>
			<div class="right">
				<?php $serviceTimes = $profile->serviceTime; ?>
				<ul>
					<li><?= empty($serviceTimes->day_1) ? NULL : '<b>' . ServiceTime::DAY[$serviceTimes->day_1] . ' ' . $serviceTimes->time_1 . '</b> - ' . $serviceTimes->description_1 ?></li>
					<li><?= empty($serviceTimes->day_2) ? NULL : '<b>' . ServiceTime::DAY[$serviceTimes->day_2] . ' ' . $serviceTimes->time_2 . '</b> - ' . $serviceTimes->description_2 ?></li>
					<li><?= empty($serviceTimes->day_3) ? NULL : '<b>' . ServiceTime::DAY[$serviceTimes->day_3] . ' ' . $serviceTimes->time_3 . '</b> - ' . $serviceTimes->description_3 ?></li>
					<li><?= empty($serviceTimes->day_4) ? NULL : '<b>' . ServiceTime::DAY[$serviceTimes->day_4] . ' ' . $serviceTimes->time_4 . '</b> - ' . $serviceTimes->description_4 ?></li>
					<li><?= empty($serviceTimes->day_5) ? NULL : '<b>' . ServiceTime::DAY[$serviceTimes->day_5] . ' ' . $serviceTimes->time_5 . '</b> - ' . $serviceTimes->description_5 ?></li>
					<li><?= empty($serviceTimes->day_6) ? NULL : '<b>' . ServiceTime::DAY[$serviceTimes->day_6] . ' ' . $serviceTimes->time_6 . '</b> - ' . $serviceTimes->description_6 ?></li>
				</ul>
			</div>
		</div>
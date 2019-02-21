<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

    A profile has been flagged as innappropriate:

    Profile: <?= $url ?>

    <?php if (isset($user)) { ?>
    	<?= 'Flagged by user: ' . $user; ?>
    <?php } else { ?>
    	<?= 'Flagged by User: Guest' ?>
    <?php } ?>

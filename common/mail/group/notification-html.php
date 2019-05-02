<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

<div>
    <h4><?= $title ?></h4>
    <p><?= $message ?></p>

	<?php if ($extMsg) { ?>
    	<h4>Message from Group Owner</h4>
    	<p><?= $extMsg ?></p>
    <?php } ?>
</div>

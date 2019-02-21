<?php

use common\models\Utility;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = $profile->coupleName;
?>

<div class="profile">
  <div class="profile-main">
  
    <div class="update-header">
      <?= empty($profile->image2) ? Html::img('@img.profile/content/profile-logo.png', ['class' => 'update-img2', 'alt' => 'Logo Image']) : Html::img($profile->image2, ['class' => 'update-img2', 'alt' => 'Logo image']) ?>
      <h2>Prayer Updates</h2>
      <h1><?= $this->title ?></h1>
      <?= Html::a('Visit our profile ' . Html::icon('link', ['class' => 'internal-link']), ['/profile/missionary', 'id' => $profile->id, 'urlLoc' => $profile->url_loc, 'urlName' => $profile->url_name]) ?>
    </div>
    <div class="update-flag">
      <?= html::img('@img.flag/' . str_replace(' ', '-', $missionary->field) . '.png', ['alt' => 'Country flag']) ?>
      <p>
        <?php if ($profile->sub_type == 'Furlough Replacement') { ?>
            <?= $missionary->field == 'Furlough Replacement' ? 'Various' : $missionary->field ?>
          <?php } else { ?>
            <?= $missionary->field ?>
        <?php } ?>
      </p>
    </div>
    <p class="update-notice"><?= Html::icon('info-sign') ?> This page is intended for our mailing list only.  Please do not post the link or contents on the internet unless otherwise noted.  Thank you!</p>

    <?php if ($updates) {
      foreach ($updates as $update) {
        if ($update->mailchimp_url) {
          echo $this->render('cards/_card-mailchimp', ['update' => $update]);
        } elseif ($update->pdf) {
          echo $this->render('cards/_card-pdf', ['update' => $update]);
        } elseif ($update->vimeo_url || $update->youtube_url) {
          echo $this->render('cards/_card-video', ['update' => $update]);
        }
      }
    } else {
      echo '<div class="updates-none"><h4>There are no updates at this time.</h4></div>';
    } ?>

  </div>
</div>
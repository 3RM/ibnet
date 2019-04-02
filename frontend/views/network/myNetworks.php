<?php

use common\models\network\Network;
use frontend\assets\NetworkAsset;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
NetworkAsset::register($this);
?>
<?= $this->render('../site/_userAreaHeader', ['active' => 'network']) ?>
<div class="container">
    <?= $this->render('../site/_userAreaLeftNav', ['active' => 'networks']) ?>

    <div class="right-content">
        <h2>Networks</h2>

        <?= Html::a(Html::icon('plus') . ' New Network', ['create'], ['id' => 'new-network', 'class' => 'btn btn-primary']) ?>

        <h3>Find a Network</h3>
        <?php $form = ActiveForm::begin(['id' => 'network-search-form']); ?>
        <div class="network-search">
            <?= $form->field($networkSearch, 'term')->textInput([
                'maxlength' => true, 
                'class' => 'form-control',
                'placeholder' => 'Search by location or keyword...',
                'autocomplete' => 'off',
            ])->label('') ?>
            <?= Html::submitButton('', [
                'method' => 'POST',
                'class' => 'btn btn-default network-search-icon',
                'name' => 'search',
            ]) ?>
        </div>
        <?php $form = ActiveForm::end(); ?>

        <?php if ($joinedNetworks) { ?>
        	<h3>Joined Networks</h3>
            <div class="network-list-container">
                <?php foreach ($joinedNetworks as $joinedNetwork) { ?>
                    <?= $this->render('_joinedNetwork', ['network' => $joinedNetwork]) ?>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($ownNetworks) { ?>
        	<h3>My Networks</h3>
            <div class="network-list-container">
                <?php foreach ($ownNetworks as $ownNetwork) { ?>
                    <?= $this->render('_ownNetwork', ['network' => $ownNetwork]) ?>
                <?php } ?>
            </div>
        <?php } ?>
            
    </div>
</div>

<?php Modal::begin([
    'header' => '<span class="glyphicons glyphicons-user-add"></span>',
    'id' => 'invite-modal',
    'size' => 'modal-md',
    'headerOptions' => ['class' => ''],
]);
    echo '<div id="invite-content"></div>';
Modal::end(); ?>
<?php

use common\models\group\Group;
use frontend\assets\GroupAsset;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
GroupAsset::register($this);
Url::Remember();
?>
<?= $this->render('../site/_userAreaHeader', ['active' => 'group']) ?>
<div class="container">
    <?= $this->render('../site/_userAreaLeftNav', ['active' => 'groups', 'joinedGroups' => $joinedGroups]) ?>

    <div class="right-content">
        <h2>Groups</h2>

        <?= Html::a(Html::icon('plus') . ' New Group', ['create'], ['id' => 'new-group', 'class' => 'btn btn-primary']) ?>

        <h3>Find a Group</h3>
        <?php $form = ActiveForm::begin(['id' => 'group-search-form']); ?>
        <div class="group-search">
            <?= $form->field($groupSearch, 'term')->textInput([
                'maxlength' => true, 
                'class' => 'form-control',
                'placeholder' => 'Search by location or keyword...',
                'autocomplete' => 'off',
            ])->label('') ?>
            <?= Html::submitButton('', [
                'method' => 'POST',
                'class' => 'btn btn-default group-search-icon',
                'name' => 'search',
            ]) ?>
        </div>
        <?php $form = ActiveForm::end(); ?>
        <?php if ($dataProvider) {
            echo '<div class="group-search-results">';
            echo yii\widgets\ListView::widget([
                    'dataProvider' => $dataProvider,
                    'showOnEmpty' => false,
                    'emptyText' => '<p>Your search did not return any results.</p>',
                    'itemView' => '_searchResults',
                    'viewParams' => ['aids' => $aids, 'pids' => $pids],
                    'itemOptions' => ['class' => 'item-bordered'],
                    'layout' => '<div class="summary-row hidden-print clearfix">{summary}</div>{items}{pager}',
                ]);
            echo '</div>';
        } ?>

        <?php if ($pendingGroups) { ?>
            <h3>Pending Groups</h3>
            <div class="group-list-container">
                <?php foreach ($pendingGroups as $pendingGroup) { ?>
                    <?= $this->render('_pendingGroup', ['group' => $pendingGroup]) ?>
                <?php } ?>
            </div>
        <?php } ?>
        <?php if ($allJoinedGroups) { ?>
        	<h3>Joined Groups</h3>
            <div class="group-list-container">
                <?php foreach ($allJoinedGroups as $joinedGroup) { ?>
                    <?= $this->render('_joinedGroup', ['group' => $joinedGroup]) ?>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($ownGroups) { ?>
        	<h3>My Groups</h3>
            <div class="group-list-container">
                <?php foreach ($ownGroups as $ownGroup) { ?>
                    <?= $this->render('_ownGroup', ['group' => $ownGroup]) ?>
                <?php } ?>
            </div>
        <?php } ?>
            
    </div>
</div>

<?php Modal::begin([
    'header' => '<h3><span class="glyphicons glyphicons-user-add"></span> Invite New Members</h3>',
    'id' => 'invite-modal',
    'size' => 'modal-md',
    'headerOptions' => ['class' => ''],
]);
    echo '<div id="invite-content"></div>';
Modal::end(); ?>
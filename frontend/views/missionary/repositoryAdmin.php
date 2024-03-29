<?php

use common\models\missionary\MissionaryUpdate;
use frontend\assets\AjaxAsset;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */
AjaxAsset::register($this);
\Eddmash\Clipboard\ClipboardAsset::register($this);
?>

<?= $this->render('../site/_userAreaHeader', ['active' => 'update']) ?>
<div class="container">
    <?= $this->render('../site/_userAreaLeftNav', ['active' => 'updates', 'gid' => NULL, 'role' => $role, 'joinedGroups' => $joinedGroups]) ?>

    <div class="right-content">
        <div id="video-container" class="feature-video-container" <?= $displayNone ?>>
            <?php Modal::begin([
                'header' => '',
                'id' => 'videoModal',
                'toggleButton' => [
                    'label' => 'Watch Video',
                    'alt' => 'Flag innapropriate content',
                    'class' => 'btn-primary'],
                'headerOptions' => ['class' => 'modal-header'],
                'bodyOptions' => ['class' => 'link-modal-body'],
                ]); ?>
                <div class="videoWrapper">
                    <iframe id="video" src="https://player.vimeo.com/video/261478010?byline=0&portrait=0" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>                    
                </div>
            <?php Modal::end() ?>
            <?= Html::a('<i class="fa fa-close"></i>', ['ajax/viewed', 'mid' => $missionary->id], [
                'id' => 'viewed-id', 
                'data-on-done' => 'viewedDone',
            ]); ?>
            <?php $this->registerJs("$('#viewed-id').click(handleAjaxSpanLink);", \yii\web\View::POS_READY); ?>
        </div>

        <h2>Missionary Updates</h2>

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="repo-url">
            <?= 'Private Url to share with your mailing list:<br>' . \Eddmash\Clipboard\Clipboard::input($this, 'text', 'url', $repo_url, ['id' => 'repository_link', 'readonly' => true]) ?>
        </div>
        <div class="repo-links">
            <?= Html::submitButton(Html::icon('refresh') . ' <span class="mb-hide">Generate new url</span>', [
                'class' => 'repo-url-refresh', 
                'name' => 'new_url', 
                'title' => 'Generate new url', 
                'onclick' => 'return confirm("Are you sure? This will lock out everyone who has bookmarked this link to access your updates.")'
            ]) ?>
            <?= Html::a(Html::icon('new-window') . ' <span class="mb-hide">Take me there</span>', $repo_url, [
                'target' => '_blank', 
                'rel' => 'noopener noreferrer', 
                'title' => 'Take me there'
            ]) ?>
        </div>

        <?php if ($profileActive) { ?>
            <div class="repo-form">
                <h2>New Update</h2>
                <div id="mc-link"><?= Html::a(($mcSynced ? 'Update Mailchimp syncing' : 'Sync with Mailchimp'), 'mailchimp-step1') ?></div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($newUpdate, 'title')->textInput(['maxlength' => true, 'placeholder' => 'Max 50 characters...',]) ?> 
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($newUpdate, 'active')->widget(Select2::classname(), [
                            'data' => ['3' => 'Three Months', '6' => 'Six Months', '12' => 'One Year', '24' => 'Two Years', '99' => 'Forever'],
                            'language' => 'en',
                            'theme' => 'krajee',
                            'hideSearch' => true,
                        ]); ?>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($newUpdate, 'description')->textarea(['maxlength' => true, 'rows' => 2, 'placeholder' => 'Max 1000 characters...',]) ?> 
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($newUpdate, 'youtube_url', ['enableAjaxValidation' => true])->textInput(['maxlength' => true, 'placeholder' => 'e.g. https://youtu.be/abC-dEFgHij']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">    
                        <?= $form->field($newUpdate, 'vimeo_url', ['enableAjaxValidation' => true])->textInput(['maxlength' => true, 'placeholder' => 'e.g. https://vimeo.com/123456789']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">     
                        <?= Html::button(Html::icon('info-sign'), ['class' => 'repo-tooltip', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 
                            'title' => 'Navigate to Google Drive. Double-click on your video to open a preview. Select the three-dot menu in the top right corner and click "Share."  Copy the link provided there.']); ?>
                        <?= $form->field($newUpdate, 'drive_url', ['enableAjaxValidation' => true])->textInput(['maxlength' => false, 'placeholder' => 'e.g. https://drive.google.com/file/d/1SHf.../view?usp=sharing']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($newUpdate, 'pdf')->fileInput() ?>
                    </div>
                    <div class="col-md-12">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'save']) ?>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="repo-form-warning">
                <h4><?= Html::icon('warning-sign') ?> Your missionary profile is inactive. Reactivate your profile to take full advantage of this feature.</h4>
            </div>
        <?php } ?>

 
        <?php if ($updates) { ?>

            <div class="repo-update-table">
                <div class="panel panel-default">
                    <div class="panel-heading">My Updates</div>
                    <table class="table">
                    <?php foreach ($updates as $update) { 
                        if ($update->edit) { ?>
                            <tr id=<?= '"' . $update->id . '"' ?>>
                                <td colspan="3" class="repo-edit-update">
                                    <div class="update-container">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <?php if ($update->mailchimp_url) {
                                                    echo Html::img('@img.user-area/freddie-small.png');
                                                } elseif ($update->pdf) {
                                                    echo '<span class="filetypes filetypes-pdf repo-table-icon"></span>';
                                                } elseif ($update->youtube_url) {
                                                    echo '<span class="social social-youtube repo-table-icon"></span>';
                                                } elseif ($update->vimeo_url) {
                                                    echo '<span class="social social-vimeo repo-table-icon"></span>';
                                                } ?>
                                            </div>
                                            <div class="col-md-10">
                                                <h3><?= $update->title ?></h3>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?= $form->field($update, 'title')->textInput(['maxlength' => true, 'placeholder' => 'Max 50 characters...',]) ?> 
                                            </div>
                                            <div class="col-md-6">
                                                <?= $form->field($update, 'editActive')->widget(Select2::classname(), [
                                                    'data' => ['3' => 'Three Months', '6' => 'Six Months', '12' => 'One Year', '24' => 'Two Years', '99' => 'Forever'],
                                                    'language' => 'en',
                                                    'theme' => 'krajee',
                                                    'hideSearch' => true,
                                                ]); ?>
                                            </div>
                                        </div>
                                         <div class="row">
                                            <div class="col-md-12">
                                                <?= $form->field($update, 'description')->textarea(['maxlength' => true, 'rows' => 2, 'placeholder' => 'Max 1000 characters...',]) ?> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?= ($update->pdf || $update->mailchimp_url) ? $form->field($update, 'pdf')->fileInput() : NULL ?>
                                                <?= $update->youtube_url ? $form->field($update, 'youtube_url')->textInput(['maxlength' => true, 'placeholder' => 'e.g. https://youtu.be/abC-dEFgHij']) : NULL ?>
                                                <?= $update->vimeo_url ? $form->field($update, 'vimeo_url')->textInput(['maxlength' => true, 'placeholder' => 'e.g. https://vimeo.com/123456789']) : NULL ?>
                                                <?= $update->drive_url ? Html::button(Html::icon('info-sign'), ['class' => 'repo-tooltip', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 
                                                    'title' => 'Navigate to Google Drive. Double-click on your video to open a preview. Select the three-dot menu in the top right corner and click "Share."  Copy the link provided there.']) : NULL; ?>
                                                <?= $update->drive_url ? $form->field($update, 'drive_url')->textInput(['maxlength' => true, 'placeholder' => 'e.g. https://drive.google.com/file/d/1SHf.../view?usp=sharing']) : NULL ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 edit-buttons">    
                                                <?= Html::submitButton('Save', ['method' => 'POST',
                                                    'class' => 'btn btn-primary',
                                                    'name' => 'edit-save',
                                                    'value' => $update->id,
                                                ]) ?>
                                                <?= Html::a('Cancel', ['/missionary/update-repository', 'a' => $update->id], ['class' => 'btn btn-primary']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>                        
                            </tr>   
                        <?php } else { ?>
                            <?php if (($update->alert_status == MissionaryUpdate::ALERT_ENABLED) || ($update->alert_status == MissionaryUpdate::ALERT_PAUSED)) { ?>
                            <tr>
                                <td colspan="3" id="alert-timer-container-<?= $update->id ?>">
                                    <?php if ($update->alert_status == MissionaryUpdate::ALERT_PAUSED) { ?>
                                        <div id="update-alert-timer-<?= $update->id ?>">
                                            <?= Html::icon('send') . Html::a(' Send alert', ['ajax/send-alert', 'id' => $update->id], ['id' => 'alert-send', 'data-on-done' => 'alertSendDone']) ?>
                                            <?= Html::a('<i class="fas fa-times"></i>', ['ajax/cancel-alert', 'id' => $update->id], ['id' => 'alert-cancel', 'data-on-done' => 'alertCancelDone']) ?>
                                            <?php $this->registerJs("$('#update-alert-timer-" . $update->id . "').on('click', '#alert-send', handleAjaxLink);", \yii\web\View::POS_READY); ?>
                                            <?php $this->registerJs("$('#update-alert-timer-" . $update->id . "').on('click', '#alert-cancel', handleAjaxSpanLink);", \yii\web\View::POS_READY); ?>
                                        </div>
                                    <?php } elseif ($update->alert_status == MissionaryUpdate::ALERT_ENABLED) { ?>
                                        <div id="update-alert-timer-<?= $update->id ?>">
                                            <?= Html::a('<i class="fas fa-times"></i>', ['ajax/cancel-alert', 'id' => $update->id], [
                                                'id' => 'alert-cancel', 
                                                'style' => 'visibility:hidden', 
                                                'data-on-done' => 'alertCancelDone'
                                            ]) ?>
                                            <div id="alert-timer-text">
                                                Sending group alert in <span id="timer"></span>
                                                <?= Html::a('<i class="far fa-stop-circle"></i>', ['ajax/pause-alert', 'id' => $update->id], ['id' => 'alert-pause', 'data-on-done' => 'alertPauseDone']) ?>
                                            </div>
                                            <?php $this->registerJs("$('#update-alert-timer-" . $update->id . "').on('click', '#alert-send', handleAjaxLink);", \yii\web\View::POS_READY); ?>
                                            <?php $this->registerJs("$('#update-alert-timer-" . $update->id . "').on('click', '#alert-pause', handleAjaxSpanLink);", \yii\web\View::POS_READY); ?>
                                            <?php $this->registerJs("$('#update-alert-timer-" . $update->id . "').on('click', '#alert-cancel', handleAjaxSpanLink);", \yii\web\View::POS_READY); ?>
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <tr id=<?= '"' . $update->id . '"' ?>>
                                <td>
                                    <?php if ($update->mailchimp_url) {
                                        echo Html::img('@img.user-area/freddie-small.png', ['class' => 'mc-icon']);
                                    } elseif ($update->pdf) {
                                        echo '<span class="filetypes filetypes-pdf repo-table-icon"></span>';
                                    } elseif ($update->youtube_url) {
                                        echo '<span class="social social-youtube repo-table-icon"></span>';
                                    } elseif ($update->vimeo_url) {
                                        echo '<span class="social social-vimeo repo-table-icon"></span>';
                                    } ?>
                                </td>
                                <td>
                                    <h3><?= $update->title ?></h3>
                                    <div class="repo-table-expires">
                                        <?= Yii::$app->formatter->asDate($update->to_date, 'php:Y') > 2100 ?
                                            'Expires Never' :
                                            'Expires ' . Yii::$app->formatter->asDate($update->to_date, 'php:F j, Y'); 
                                        ?>
                                    </div>
                                    <?= $update->description ? '<p>' . $update->description . '</p>' : NULL; ?>
                                    <?= empty($update->pdf) ? NULL : \Eddmash\Clipboard\Clipboard::input($this, 'text', 'url', Url::base(true) . $update->pdf, ['id' => 'update_link_' . $update->id, 'readonly' => true])?>
                                    <?= (1 == $update->vid_not_accessible) ? '<div class="alert alert-danger">' . Html::icon('warning-sign') . ' We could not retrieve this video. Ensure your video privacy settings allow embedding on this site.</div>' : NULL; ?>
                                    <?= empty($update->thumbnail) ? NULL : Html::img($update->thumbnail, ['class' => 'repo-thumb']); ?>
                                    <?= empty($update->drive_url) ? NULL : '<iframe src="' . $update->drive_url . '" width="100%" height="405"></iframe>'; ?>
                                </td>
                                <td>
                                    <div class="repo-table-buttons">
                                        <?= Html::submitButton(Html::icon('edit'), [
                                            'method' => 'POST',
                                            'class' => 'btn btn-form btn-sm',
                                            'name' => 'edit',
                                            'value' => $update->id,
                                        ]) ?>
                                        <?= Html::submitButton(Html::icon('remove'), [
                                            'method' => 'POST',
                                            'class' => 'btn btn-form btn-sm',
                                            'name' => 'remove',
                                            'value' => $update->id,
                                        ]) ?>
                                    </div>
                                    <div id=<?='"visible-result-' . $update->id . '"'?>>
                                        <?= $update->visible ? 
                                            Html::a(Html::icon('eye-open'), ['ajax/update-visible', 'id' => $update->id], [
                                                'id' => 'visible-' . $update->id, 
                                                'data-on-done' => 'visibleDone', 
                                                'class' => 'update-visible'
                                            ]) : 
                                            Html::a(Html::icon('eye-close'), ['ajax/update-visible', 'id' => $update->id], [
                                                'id' => 'visible-' . $update->id, 
                                                'data-on-done' => 'visibleDone', 
                                            ]) ?>
                                    </div>
                                    <?php $this->registerJs("$('#visible-result-" . $update->id . "').on('click', '#visible-" . $update->id . "', handleAjaxSpanLink);", \yii\web\View::POS_READY); ?>
                                </td>
                            </tr>
                        <?php }                           
                    } ?>
                    </table>
                </div>
                <p><span style="color:#00b100; font-size:0.8em"><?= Html::icon('eye-open') ?></span> = Visible on your public profile</p>
                <p style="margin-top: -12px"><span style="color:#337ab7; font-size:0.8em"><?= Html::icon('eye-close') ?></span> = Not visible on your public profile</p>
            </div>
        <?php } ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?= Html::hiddenInput('hash', $a, ['id' => 'hash']) ?>
<script type="text/javascript">
    $(document).ready(function(){
            console.log($('#hash').val());
            location.hash = "#"+$('#hash').val();           
    });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        /* Get iframe src attribute value i.e. YouTube video url and store it in a variable */
        var url = $("#video").attr('src');
        
        /* Assign empty url value to the iframe src attribute when modal hide, which stop the video playing */
        $("#videoModal").on('hide.bs.modal', function(){
            $("#video").attr('src', '');
        });
        
        /* Assign the initially stored url back to the iframe src attribute when modal is displayed again */
        $("#videoModal").on('show.bs.modal', function(){
            $("#video").attr('src', url);
        });
    }); 
</script>

<script type="text/javascript">
// Initialize counter
window.onload = timer;

function initialize() {
    if (parseInt(<?= isset($updates[0]) ? $updates[0]->alert_status : 0 ?>) == parseInt(<?= MissionaryUpdate::ALERT_ENABLED ?>)) {
        timer;
    }
}

// Intitialize variables
var createDate = parseInt(<?= isset($updates[0]) ? $updates[0]->created_at : 0 ?>) * 1000;
var delay = parseInt(<?= Yii::$app->params['delay.missionaryUpdate'] ?>) * 1000;
var countDownTime = createDate + delay;
var setTimer;
var setAlertSent;

// Timer function
function timer() {
    setTimer = setInterval(calcTime, 500);

    function calcTime() {
        var now = new Date().getTime();
        var delta = countDownTime - now;
        var min = Math.floor((delta % (1000 * 60 * 60)) / (1000 * 60));
        var sec = Math.floor((delta % (1000 * 60)) / 1000);
        
        document.getElementById("timer").innerHTML = min + ":" + ("0" + sec).slice(-2);
          
        if (delta < 0) {
            clearInterval(setTimer);
            document.getElementById("update-alert-timer-<?= isset($updates[0]) ? $updates[0]->id : NULL ?>").innerHTML = "Group alert in queue...";
        }
    }
}

</script>
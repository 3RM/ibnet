<?php

use frontend\assets\AjaxAsset;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */
AjaxAsset::register($this);
\Eddmash\Clipboard\ClipboardAsset::register($this);
?>
<?= $this->render('../site/_userAreaHeader', ['menuItems' => $menuItems, 'active' => 'update']) ?>
<div class="container">
    <?= $this->render('../site/_userAreaLeftNav', ['active' => 'updates']) ?>

    <div class="right-content">
        <h2>Missionary Updates</h2>

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="repo-url">
            <?= 'Private Url to share with your mailing list:<br>' . \Eddmash\Clipboard\Clipboard::input($this, 'text', 'url', $repo_url, ['id' => 'repository_link', 'readonly' => true]) ?>
        </div>
        <div class="repo-links">
            <?= Html::submitButton(Html::icon('refresh') . ' Generate new url', ['class' => 'repo-url-refresh', 'name' => 'new_url', 'onclick' => 'return confirm("Are you sure? This will lock out everyone who has bookmarked this link to access your updates.")']) ?>
            <?= Html::a(Html::icon('new-window') . ' Take me there', $repo_url, ['target' => '_blank']) ?>
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
                    <div class="col-md-6">
                        <?= $form->field($newUpdate, 'youtube_url')->textInput(['maxlength' => true, 'placeholder' => 'e.g. https://youtu.be/abC-dEFgHij']) ?>
                    </div>
                    <div class="col-md-6">    
                        <?= $form->field($newUpdate, 'vimeo_url')->textInput(['maxlength' => true, 'placeholder' => 'e.g. https://vimeo.com/123456789']) ?>
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
                                                    echo Html::img('@images/content/freddie-small.png');
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
                            <tr id=<?= '"' . $update->id . '"' ?>>
                                <td>
                                    <?php if ($update->mailchimp_url) {
                                        echo Html::img('@images/content/freddie-small.png', ['class' => 'mc-icon']);
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
                                    <?= empty($update->thumbnail) ? NULL : Html::img($update->thumbnail, ['class' => 'repo-thumb']); ?>
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
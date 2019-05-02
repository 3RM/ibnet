<?php

use common\models\group\Prayer;
use common\models\group\PrayerTag;
use common\models\Utility;
use Dompdf\Dompdf;
use frontend\assets\AjaxAsset;
use frontend\assets\GroupAsset;
use kartik\checkbox\CheckboxX;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\widgets\ListView;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

GroupAsset::register($this);
AjaxAsset::register($this);
Url::Remember();

if (isset($_SESSION['html'])) {
    $pdf = new Dompdf();
    $pdf->loadHtml($prayer->html);

    //Set options
    isset($_SESSION['size']) ? $pdf->setPaper($_SESSION['size']) : NULL;

    // Render and send to browser
    $pdf->render();
    $pdf->stream('prayer_list.pdf');

    unset($_SESSION['html']);
    unset($_SESSION['size']);
}

$this->title = 'Prayer List';
?>

<?= $this->render('../../site/_userAreaLeftNav', ['active' => 'prayer', 'joinedGroups' => $joinedGroups]) ?>

<div class="right-content">

    <!-- boxes -->
    <div class="col-md-3 boxes">

        <!-- sort box -->
        <div class="box box-solid">
            <div class="box-header with-border">
                <h4 class="box-title">Sort</h4>
            </div>
            <div class="box-body">
                <?= isset($l) ?
                    '<div class="sort">
                        <p>Answer Date </p>
                        <p>' . Html::a(Html::icon('arrow-up'), Url::current(['f' => $f, 'l' => $l, 'sort' => 'answer_date'])) . ' ' . Html::a(Html::icon('arrow-down'), Url::current(['sort' => '-answer_date'])) . '</p>
                    </div>' :
                    '<div class="sort">
                        <p>Most Recent </p>
                        <p>' . Html::a(Html::icon('arrow-up'), Url::current(['f' => $f, 'l' => $l, 'sort' => 'last_update'])) . ' ' . Html::a(Html::icon('arrow-down'), Url::current(['sort' => '-last_update'])) . '</p>
                    </div>'
                ?>
                <div class="sort">
                    <p>Requested By </p><p><?= Html::a(Html::icon('arrow-up'), Url::current(['f' => $f, 'l' => $l, 'sort' => 'name'])) . ' ' . Html::a(Html::icon('arrow-down'), Url::current(['sort' => '-name'])) ?></p>
                </div>
                <div class="sort">
                    <p>Duration </p><p><?= Html::a(Html::icon('arrow-up'), Url::current(['f' => $f, 'l' => $l, 'sort' => 'duration'])) . ' ' . Html::a(Html::icon('arrow-down'), Url::current(['sort' => '-duration'])) ?></p>
                </div>
            </div>
        <!-- /sort box -->
        </div>

        <!-- filter box -->
        <div class="box box-solid">
            <div class="box-header with-border">
                <h4 class="box-title">Filter</h4>
            </div>
            <div class="box-body">
                    
                <!-- Requested By -->
                <?= Select2::widget([
                    'id' => 'name-id',
                    'name' => 'nameSelect',
                    'data' => $prayerNameList,
                    'disabled' => $f,
                    'options' => ['class' => 'filter-select', 'placeholder' => 'Requested By'],
                ]); ?>
                <?php $this->registerJs(
                "$('#name-id').change(function() {
                    GET = location.search.substr(1).split('&').reduce((o,i)=>(u=decodeURIComponent,[k,v]=i.split('='),o[u(k)]=v&&u(v),o),{});
                    if (GET.f) {
                        nameVal = NULL;
                    } else {
                        nameVal = '&PrayerSearch%5Bname%5D\=' + $('#name-id option:selected').text();
                    }
                    window.location.replace('" . Url::to(['group/prayer', 'id' => $group->id, 'f' => $f, 'l' => $l]) . "' + nameVal);
                });", \yii\web\View::POS_READY); ?>
                    
                <!-- Duration -->
                <?= Select2::widget([
                    'id' => 'duration-id',
                    'name' => 'durationSelect',
                    'data' => Prayer::$duration,
                    'hideSearch' => true,
                    'disabled' => $f,
                    'options' => ['class' => 'filter-select', 'placeholder' => 'Duration'],
                ]); ?>
                <?php $this->registerJs(
                "$('#duration-id').change(function() {
                    GET = location.search.substr(1).split('&').reduce((o,i)=>(u=decodeURIComponent,[k,v]=i.split('='),o[u(k)]=v&&u(v),o),{});
                    if (GET.f) {
                        durVal = NULL;
                    } else {
                        durVal = '&PrayerSearch%5Bduration%5D\=' + $('#duration-id').val();
                    }
                    window.location.replace('" . Url::to(['group/prayer', 'id' => $group->id, 'f' => $f, 'l' => $l]) . "' + durVal);
                });", \yii\web\View::POS_READY); ?>
                    
                <!-- Tag -->
                <?= Select2::widget([
                    'id' => 'tag-id',
                    'name' => 'tagSelect',
                    'data' => ArrayHelper::map($tagList, 'id', 'tag'),
                    'disabled' => $f,
                    'options' => ['class' => 'filter-select', 'placeholder' => 'Tag'],
                ]); ?>
                <?php $this->registerJs(
                "$('#tag-id').change(function() {
                    GET = location.search.substr(1).split('&').reduce((o,i)=>(u=decodeURIComponent,[k,v]=i.split('='),o[u(k)]=v&&u(v),o),{});
                    if (GET.f) {
                        tagVal = NULL;
                    } else {
                        tagVal = '&PrayerSearch%5Btag%5D\=' + $('#tag-id option:selected').text();
                    }
                    window.location.replace('" . Url::to(['group/prayer', 'id' => $group->id, 'f' => $f, 'l' => $l]) . "' + tagVal);
                });", \yii\web\View::POS_READY); ?>
                <div class="reset">
                    <?= Html::a('<span class="glyphicons glyphicons-repeat"></span>', Url::to(['group/prayer', 'id' => $group->id, 'l' => $l]), ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Reset List']) ?>
                    <?= Html::a('<span class="glyphicons glyphicons-resize-full"></span>', Url::to(['group/prayer', 'id' => $group->id, 'f' => 1, 'l' => $l]), ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Show Entire List']) ?>
                </div>
            </div>
        <!-- /filter box -->
        </div>

        <!-- new request box -->
        <div class="box box-solid">
            <div class="box-header with-border">
                <h4 class="box-title">New Prayer Request</h4>
            </div>
            <div class="box-body">

                <?php Modal::begin([
                    'header' => '<h3>' . Html::icon('send') . ' Send Requests By Email</h3>',
                    'toggleButton' => [
                        'id' => 'email-request-id',
                        'label' => 'Send Requests by Email',
                        'class' => 'link-btn',
                    ],
                    'headerOptions' => ['class' => 'modal-header'],
                    'bodyOptions' => ['class' => 'email-request-modal-body'],
                ]); ?>
                    <p>
                        Send requests to: <?= $group->prayer_email ? '<b>' . $group->prayer_email . '</b>' : '<i>Pending</i>' ?><br>
                        Be sure to use the email you are registered with (<?= $member->email ?>).
                    </p>
                    <p><b>Subject: </b><span class="user-input">Request: Your Request</span></p>
                    <p>
                        <b>Email Body: </b><br>
                        <span class="user-input">
                            Your description (optional)<br>
                            Include optional <i>duration</i> and <i>tags</i> separated with ' ## '.<br>
                            Indicate duration with one of the following numbers: <br>
                            <ul>
                                <li>1 (urgent)</li>
                                <li>2 (short-term)</li>
                                <li>3 (long-term)</li>
                                <li>4 (permanent)</li>
                            </ul>
                            Separate multiple tags with ' , ' (comma).  Tag names must match existing tags.
                        </span>
                    </p>
                    <b>For example:</b>
                    <div class="ex-email">
                        <div class="row">
                            <div class="col-md-2">
                                <b>To:</b>
                            </div>
                            <div class="col-md-10">
                                <?= Html::textInput('to', 'net2.request@ibnet.org') ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <b>Subject:</b>
                            </div>
                            <div class="col-md-10">
                                <?= Html::textInput('subject', 'Request: Pray for my brother Jim to be saved') ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-area">
                                    <p>
                                        I've been witnessing to my brother Jim for the past two years.  His health is 
                                        failing and he's beginning to question his eternal destination.  I would like if 
                                        someone from the church could go with me to visit him in the next week or two.
                                    </p>
                                    <p>## 2 ## salvation, family, visitation ##</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?= Html::button('Send') ?>
                            </div>
                        </div>
                    </div>
                <?php Modal::end(); ?>

                <?php $form = ActiveForm::begin(['options' => ['onsubmit' => 'clearStorage()']]); ?>
                <?= $form->field($prayer, 'request')->textInput(['onkeyup' => 'saveValue(this)', 'maxlength' => true]) ?>
                <?= $form->field($prayer, 'description')->textArea(['onkeyup' => 'saveValue(this)', 'maxlength' => true]) ?>
                <?= $form->field($prayer, 'duration')->widget(Select2::classname(), [
                    'data' => Prayer::$duration,
                    'language' => 'en',
                    'theme' => 'krajee',
                    'options' => [ 
                        'placeholder' => '',
                        'onchange' => 'saveValue(this)',
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>
                <?= $form->field($prayer, 'select')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map($tagList, 'id', 'tag'),
                    'language' => 'en',
                    'theme' => 'krajee',
                    'options' => [
                        'placeholder' => 'Select...', 
                        'multiple' => true,
                        'onchange' => 'saveValue(this)', 
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
                <div id="add-tag">
                    <?php Modal::begin([
                        'header' => '<h3>New Tag <span class="glyphicons glyphicons-tag light drop"></span></h3>',
                        'toggleButton' => [
                            'label' => 'Manage Tags',
                            'alt' => 'Add a new prayer list tag',
                            'class' => 'link-btn'],
                        'headerOptions' => ['class' => 'modal-header'],
                        'bodyOptions' => ['class' => 'link-modal-body'],
                    ]); ?>
                    <?php $form = ActiveForm::begin(); ?>
                    <?= $form->field($tag, 'tag')->textInput(['maxlength' => true]) ?>
                    <?= Html::submitButton('Save', [
                        'id' => 'submit-tag',
                        'method' => 'POST',
                        'class' => 'btn btn-primary',
                        'name' => 'main',
                    ]) ?>
                    <?php $form = ActiveForm::end(); ?>
                    <div class="modal-footer">
                        <?php foreach ($tagList as $tagItem) {
                           echo '<div id="tag-' . $tagItem->id . '" class="tag-row">';
                                echo $tagItem->tag . ' ' . Html::a(Html::icon('remove'), ['ajax/delete-tag', 'tid' => $tagItem->id], [
                                   'id' => 'tagitem-' . $tagItem->id, 
                                   'data-on-done' => 'tagDone']) . '<br>';
                           echo '</div>';
                           $this->registerJs("$('#tagitem-" . $tagItem->id . "').click(handleAjaxSpanLink);", \yii\web\View::POS_READY);
                        } ?>
                    </div>
                    <?php Modal::end() ?>
                </div>
                <?= Html::submitButton('Save', [
                    'method' => 'POST',
                    'class' => 'btn btn-primary-nw',
                    'name' => 'request',
                ]) ?>
                <?php $form = ActiveForm::end(); ?>

            </div>
        <!-- /new request box -->
        </div>

        <!-- export box -->
        <div class="box box-solid">
            <div class="box-header with-border">
                <h4 class="box-title">Export</h4>
            </div>
            <div class="box-body center">
               <?php Modal::begin([
                    'header' => '<h3><span class="filetypes filetypes-pdf"></span> Export to PDF</h3>',
                    'toggleButton' => [
                        'id' => 'export-list',
                        'label' => 'PDF',
                        'class' => 'btn btn-primary-nw longer',
                    ],
                    'id' => 'export-modal',
                    'headerOptions' => ['class' => 'modal-header'],
                ]); ?>
                    <div class="export-container">
                        <div class="export-controls">
                            <label class="cbx-label" for="details">Details</label>
                            <?= CheckboxX::widget([
                                'name'=>'details',
                                'options'=>['id'=>'details-chkbx-id'],
                                'pluginOptions'=>['threeState'=>false, 'size'=>'lg'],
                                'value' => 1,
                            ]); ?>
                            <?= Select2::widget([
                                'id' => 'export-select-id',
                                'name' => 'export-list',
                                'data' => ['10' => 'Letter', '20' => 'A4'],
                                'hideSearch' => true,
                                'options' => ['placeholder' => 'Paper Size'],
                            ]); ?>
                            <?= Html::button('Export', ['id' => 'export-btn-id', 'class' => 'btn btn-primary-nw']); ?>
                        </div>
                    </div>
                    <div class="page">
                        <div id="loading-id" style="display:none">Working on it...</div>
                        <p style="margin:0; font-size:24px;"><?= $group->name ?> Prayer List</p>
                        <p style="margin:0;">ibnet.org, <?= date('F j, Y') ?></p><br>
                        <?= ListView::widget([
                            'dataProvider' => $dataProvider,
                            'showOnEmpty' => false,
                            'emptyText' => '...nothing to export',
                            'itemView' => function ($model, $key, $index, $widget) {
                                return $this->render('_exportPrayerItem',['index' => $index, 'model' => $model]);
                            },
                            'itemOptions' => ['class' => 'item-bordered'],
                            'viewParams'=>['nmid'=>$member->id],
                            'layout' => '{items}',
                        ]); ?>
                    </div>
                <?php Modal::end(); ?>
                <?php $this->registerJs(
                "$('#details-chkbx-id').change(function () {
                    $('.details').toggle();
                });", \yii\web\View::POS_READY); ?>
                <?php $this->registerJs(
                "$('#export-btn-id').click(function () {
                    var htmlContent = $('.page').html();
                    $('#loading-id').toggle();
                    setTimeout(function() {
                         $('#loading-id').fadeOut('slow');
                    }, 4000);
                    var paperSize = $('#export-select-id option:selected').text();
                    $.post('" . Yii::$app->urlManager->createAbsoluteUrl(['group/prayer', 'id' => $group->id]) . "', 
                        {html: htmlContent, size: paperSize}, function(result) {});
                });", \yii\web\View::POS_READY); ?>
                <?php $this->registerJs(
                "$('#export-modal').on('hidden.bs.modal', function () {
                    $('#loading-id').toggle();
                });", \yii\web\View::POS_READY); ?>
            </div>
        <!-- /export box -->
        </div>

        <!-- alerts box -->
        <div class="box box-solid">
            <div class="box-header with-border">
                <h4 class="box-title">Email Alerts</h4>
            </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin(); ?>
                <label class="control-sidebar-subheading chbx-alert">
                    Immediate Alert
                    <?= $form->field($member, 'email_prayer_alert', ['options' => ['id' => 'alert-id', 'class' => 'pull-right']])->checkbox() ?>
                </label>
                <?php $this->registerJs(
                "$('#alert-id').change(function () {
                    $.ajax({
                        type: 'POST',
                        url: '" . Url::toRoute(['ajax/prayer-alert']) . "',
                        dataType: 'json',
                        data: jQuery.param({ mid: '" . $member->id . "'}),
                        async: true,
                        success: function (msg) {
                        }
                    });
                });", \yii\web\View::POS_READY); ?>
                <label class="control-sidebar-subheading">
                    Weekly Summary
                    <?= $form->field($member, 'email_prayer_summary', ['options' => ['id' => 'summary-id', 'class' => 'pull-right']])->checkbox() ?>
                </label>
                <?php $this->registerJs(
                "$('#summary-id').change(function () {
                    $.ajax({
                        type: 'POST',
                        url: '" . Url::toRoute(['ajax/prayer-summary']) . "',
                        dataType: 'json',
                        data: jQuery.param({ mid: '" . $member->id . "'}),
                        async: true,
                        success: function (msg) {
                        }
                    });
                });", \yii\web\View::POS_READY); ?>
                <?php $form = ActiveForm::end(); ?>
            </div>
        <!-- /alerts box -->
        </div>

    <!-- /boxes -->
    </div>

    <!-- prayer list -->
    <div class="col-md-9 prayer-list">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'showOnEmpty' => false,
            'emptyText' => '
                <div class="list-empty">
                  <div class="switch-view">' . Html::a(empty($l) ? 'View Answer List' : 'View Prayer List', ['group/prayer', 'id' => $group->id, 'l' => !$l]) . '</div>
                  <h4><em>... no requests</em></h4>
                </div>',
            'itemView' => $l ? '_answerItem' : '_prayerItem',
            'itemOptions' => ['class' => 'item-bordered'],
            'viewParams'=>['nmid'=> $member->id],
            'layout' => '<div class="summary-row hidden-print clearfix">{summary}<div class="switch-view">' . Html::a(empty($l) ? 'View Answer List' : 'View Prayer List', ['group/prayer', 'id' => $group->id, 'l' => !$l]) . '</div></div>{items}{pager}',
        ]); ?>
    </div>
    <!-- /prayer list -->
</div>


<?php Modal::begin([
    'header' => '<h3>' . Html::icon('pencil') . ' Update</h3>',
    'id' => 'update-request-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'link-modal-body'],
]);
    echo '<div id="update-request-content"></div>';
Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h3><span class="glyphicons glyphicons-message-in"></span> Answered</h3>',
    'id' => 'answer-request-modal',
    'headerOptions' => ['class' => 'modal-header'],
    'bodyOptions' => ['class' => 'link-modal-body'],
]);
    echo '<div id="answer-request-content"></div>';  
Modal::end(); ?>

<script type="text/javascript">
    document.getElementById("prayer-request").value = getSavedValue("prayer-request");    // set the value to this input
    document.getElementById("prayer-description").value = getSavedValue("prayer-description");
    document.getElementById("prayer-select").value = getSavedValue("prayer-select");
    document.getElementById("prayer-duration").value = getSavedValue("prayer-duration");

    //Save the value function - save it to localStorage as (ID, VALUE)
    function saveValue(e) {
        var id = e.id;  // get the sender's id to save it . 
        var val = e.value; // get the value. 
        localStorage.setItem(id, val);// Every time user writes something, the localStorage's value will override . 
    }

    //Get the saved value function - return the value of "v" from localStorage. 
    function getSavedValue(v) {
        if (localStorage.getItem(v) === null) {
            return "";	// defualt value
        }
        return localStorage.getItem(v);
    }

    //Clear storage on submit
    function clearStorage() {
    	return localStorage.clear();
    }
</script>

<script type="text/javascript">
    /**
     * Get the URL parameters
     * source: https://css-tricks.com/snippets/javascript/get-url-variables/
     * @param  {String} url The URL
     * @return {Object}     The URL parameters
     */
    var getParams = function (url) {
        var params = {};
        var parser = document.createElement('a');
        parser.href = url;
        var query = parser.search.substring(1);
        var vars = query.split('&');
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split('=');
            params[pair[0]] = decodeURIComponent(pair[1]);
        }
        return params;
    };
</script>
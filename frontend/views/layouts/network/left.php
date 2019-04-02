<?php
use yii\helpers\Html;
use common\models\Utility;

$network = $this->context->network;
?>

<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel" style="display:flex;white-space:normal;">
            <div class="pull-left image">
                <?= empty($network->network_image) ? 
                    Html::img('@img.site/user.png', ['class' => 'img-circle shadow', 'alt' => 'User image']) :
                    Html::img($network->network_image, ['class' => 'img-circle shadow', 'alt' => 'User image']) ?>
            </div>
            <div class="pull-left info" style="display:table;width:150px;">
                <p style="display:table-cell;vertical-align:middle;height: 45px;"><?= $network->name ?></p>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <?php
        $items = [];
        array_push($items, ['label' => 'Dashboard', 'icon' => 'dashboard', 'url' => ['network/dashboard/' . $network->id], 'active' => Yii::$app->controller->getRoute() == 'network/dashboard']);
        $network->feature_prayer ? array_push($items, ['label' => 'Prayer List', 'icon' => 'hand-o-up', 'url' => ['network/prayer/' . $network->id], 'active' => Yii::$app->controller->getRoute() == 'network/prayer']) : NULL;
        // $this->context->network->feature_chat ? array_push($items, ['label' => 'Live Chat', 'icon' => 'quote-left', 'url' => ['network/chat/' . $network->id], 'active' => Yii::$app->controller->getRoute() == 'network/chat']) : NULL;
        $network->feature_forum ? array_push($items, ['label' => 'Discussions', 'icon' => 'comments', 'url' => ['network/discussion/' . $network->id], 'active' => Yii::$app->controller->getRoute() == 'network/discussion']) : NULL;
        $network->feature_calendar ? array_push($items, ['label' => 'Calendar', 'icon' => 'calendar', 'url' => ['network/calendar/' . $network->id], 'active' => Yii::$app->controller->getRoute() == 'network/calendar']) : NULL;
        $network->feature_notification ? array_push($items, ['label' => 'Notifications', 'icon' => 'bullhorn', 'url' => ['network/notification/' . $network->id], 'active' => Yii::$app->controller->getRoute() == 'network/notification']) : NULL;
        $network->feature_document ? array_push($items, ['label' => 'Document Library', 'icon' => 'archive', 'url' => ['network/document/' . $network->id], 'active' => Yii::$app->controller->getRoute() == 'network/document']) : NULL;
        $network->feature_update ? array_push($items, ['label' => 'Missionary Updates', 'icon' => 'file-text', 'url' => ['network/update/' . $network->id], 'active' => Yii::$app->controller->getRoute() == 'network/update']) : NULL;
        $network->feature_donation ? array_push($items, ['label' => 'Donations', 'icon' => 'paypal', 'url' => ['network/donate/' . $network->id], 'active' => Yii::$app->controller->getRoute() == 'network/donate']) : NULL; 
        ?>

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
                'items' => $items,
                'activeCssClass' => 'active-leftmenu-item',
            ]
        ) ?>

    </section>

</aside>

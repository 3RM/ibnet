<?php
/**
 * comment-list.php
 * @author Revin Roman
 * @link https://rmrevin.ru
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $CommentsDataProvider
 *
 * customized by Steve McKinley & ibnet.org
 */

use rmrevin\yii\fontawesome\FA;
use rmrevin\yii\module\Comments;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Markdown;
use yii\helpers\Url;

/** @var Comments\widgets\CommentListWidget $CommentListWidget */
$CommentListWidget = $this->context;

$comments = [];

/** @var Comments\models\Comment $CommentModel */
$CommentModel = \Yii::createObject(Comments\Module::instance()->model('comment'));

if ($CommentListWidget->showCreateForm && $CommentModel::canCreate()) {
    echo Comments\widgets\CommentFormWidget::widget([
        'theme' => $CommentListWidget->theme,
        'entity' => $CommentListWidget->entity,
        'Comment' => $CommentModel,
        'anchor' => $CommentListWidget->anchorAfterUpdate,
    ]);
}

echo yii\widgets\ListView::widget([
    'dataProvider' => $CommentsDataProvider,
    'options' => ['class' => 'comments-list'],
    'layout' => "{items}\n{pager}",
    'itemView' =>
        function (Comments\models\Comment $Comment, $key, $index, yii\widgets\ListView $Widget)
        use (&$comments, $CommentListWidget) {
            ob_start();

            $Formatter = Yii::$app->getFormatter();
            $Author = $Comment->author;
            $comments[$Comment->id] = $Comment->attributes;
            $options = [
                'data-comment' => $Comment->id,
                'class' => 'row comment',
            ];
            if ($index === 0) {
                Html::addCssClass($options, 'first');
            }
            if ($Comment->isDeleted()) {
                Html::addCssClass($options, 'deleted');
            }
            ?>

            <div <?= Html::renderTagAttributes($options) ?>>
                <?php
                $avatar = false;
                $name = Yii::t('app', 'Unknown author');
                $url = false;

                if (empty($Author)) {
                    $name = empty($Comment->from) ? $name : $Comment->from;
                } elseif ($Author instanceof Comments\interfaces\CommentatorInterface) {
                    $avatar = $Author->getCommentatorAvatar();
                    $name = $Author->getCommentatorName();
                    $name = empty($name) ? Yii::t('app', 'Unknown author') : $name;
                    $url = $Author->getCommentatorUrl();
                    $churchName = $Author->getCommentatorChurch();
                    $churchName = empty($churchName) ? 'Unknown church' : $churchName;
                }

                $name_html = Html::tag('strong', $name);
                $churchName_html = Html::tag('strong', $churchName);

                if (false === $avatar) {
                    $avatar_html = Html::tag('div', FA::icon('male'), [
                        'class' => 'img-circle',
                        'title' => Yii::t('app', 'Unknown author'),
                    ]);
                } else {
                    $avatar_html = Html::img($avatar, [
                        'class' => 'img-circle',
                        'alt' => Yii::t('app', 'Author avatar'),
                        'title' => $name,
                    ]);
                }
                ?>
           
                <?= false !== $url ? 
                    Html::a($avatar_html, $url, ['target' => '_blank']) :
                    $avatar_html; 
                ?>
             
                <div class="comment-container">
                    <div class="comment-author">
                        <?php
                        echo false !== $url ?
                            Html::a($name_html, $url, ['target' => '_blank']) :
                            $name_html;
                        echo Html::tag('bold', ' &middot ');
                        echo $churchName_html;
                        if ((time() - $Comment->created_at) > (86400 * 2)) {
                            echo '<br>' . Html::tag('span', $Formatter->asDatetime($Comment->created_at), ['class' => 'comment-date']);
                        } else {
                            echo '<br>' . Html::tag('span', $Formatter->asRelativeTime($Comment->created_at), ['class' => 'comment-date']);
                        }
                        ?>
                    </div>
                    <div class="comment-text">
                        <?php
                        if ($Comment->isDeleted()) {
                            echo Yii::t('app', 'Comment was deleted.');
                        } else {
                            echo Markdown::process($Comment->text, 'gfm-comment');

                            if ($Comment->isEdited()) {
                                echo Html::tag('small', Yii::t('app', 'Updated at {date-relative}', [
                                    'date' => $Formatter->asDate($Comment->updated_at),
                                    'date-time' => $Formatter->asDatetime($Comment->updated_at),
                                    'date-relative' => $Formatter->asRelativeTime($Comment->updated_at),
                                ]));
                            }
                        }
                        ?>
                    </div>
                    <?php
                    if ($Comment->canUpdate() && !$Comment->isDeleted()) {
                        ?>
                        <div class="edit">
                            <?php
                            echo Comments\widgets\CommentFormWidget::widget([
                                'entity' => $CommentListWidget->entity,
                                'Comment' => $Comment,
                                'anchor' => $CommentListWidget->anchorAfterUpdate,
                            ]);
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="comment-actions">
                        <?php
                        if (!$Comment->isDeleted()) {
                            if ($Comment->canCreate()) {
                                echo Html::a(FA::icon('reply') . ' ' . Yii::t('app', 'Reply'), '#', [
                                    'class' => '',
                                    'data-role' => 'reply',
                                ]);
                            }

                            if ($Comment->canUpdate()) {
                                echo Html::a(
                                    FA::icon('pencil') . ' ' . Yii::t('app', 'Edit'),
                                    '#',
                                    [
                                        'data-role' => 'edit',
                                        'class' => '',
                                    ]
                                );
                            }

                            if ($Comment->canDelete()) {
                                 echo Html::a(
                                    FA::icon('times') . ' ' . Yii::t('app', 'Delete'),
                                    Url::current(['delete-comment' => $Comment->id]),
                                    ['class' => '']
                                );
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php

            return ob_get_clean();
        }
]);

$CommentListWidget
    ->getView()
    ->registerJs('jQuery("#' . $CommentListWidget->options['id'] . '").yiiCommentsList(' . Json::encode($comments) . ');');
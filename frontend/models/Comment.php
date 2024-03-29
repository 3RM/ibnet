<?php
/**
 * Comment.php Extended
 * @author Revin Roman
 * @link https://rmrevin.ru
 */

namespace frontend\models;

use common\models\User;
use common\models\profile\Profile;
use frontend\components\CommentSend;
use rmrevin\yii\module\Comments;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * Class Comment
 * @package rmrevin\yii\module\Comments\models
 *
 * @property integer $id
 * @property string $entity
 * @property string $from
 * @property string $text
 * @property integer $deleted
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property \yii\db\ActiveRecord $author
 * @property \yii\db\ActiveRecord $lastUpdateAuthor
 *
 * @method queries\CommentQuery hasMany(string $class, array $link) see BaseActiveRecord::hasMany() for more info
 * @method queries\CommentQuery hasOne(string $class, array $link) see BaseActiveRecord::hasOne() for more info
 */
class Comment extends \rmrevin\yii\module\Comments\models\Comment
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'blameable' => BlameableBehavior::className(),
            'timestamp' => TimestampBehavior::className(),
            // 'commentSend' => CommentSend::classNames(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'required'],
            [['from', 'text'], 'string'],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['deleted'], 'boolean'],
            [['deleted'], 'default', 'value' => self::NOT_DELETED],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'entity' => \Yii::t('app', 'Entity'),
            'from' => \Yii::t('app', 'Comment author'),
            'text' => \Yii::t('app', 'Text'),
            'created_by' => \Yii::t('app', 'Created by'),
            'updated_by' => \Yii::t('app', 'Updated by'),
            'created_at' => \Yii::t('app', 'Created at'),
            'updated_at' => \Yii::t('app', 'Updated at'),
        ];
    }

    /**
     * @return bool
     */
    public function isEdited()
    {
        return $this->created_at !== $this->updated_at;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted === self::DELETED;
    }

    /**
     * @return bool
     */
    public static function canCreate()
    {
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id))[0];
        return (($role == User::ROLE_SAFEUSER) || ($role == User::ROLE_ADMIN));
    }

    /**
     * @return bool
     */
    public function canUpdate()
    {
        $user = \Yii::$app->getUser();
        return Comments\Module::instance()->useRbac === true ? 
            (\Yii::$app->getUser()->can(Comments\Permission::UPDATE) 
                || \Yii::$app->getUser()->can(Comments\Permission::UPDATE_OWN, ['Comment' => $this])) : 
            ($user->isGuest ? false : ($this->created_by === $user->id));
    }

    /**
     * @return bool
     */
    public function canDelete()
    {
        $user = \Yii::$app->getUser();
        $profile = Profile::findOne($_GET['id']);
        return Comments\Module::instance()->useRbac === true ? 
            (\Yii::$app->getUser()->can(Comments\Permission::DELETE) 
                || \Yii::$app->getUser()->can(Comments\Permission::DELETE_OWN, ['Comment' => $this]) 
                || \Yii::$app->getUser()->can('updateProfile', ['profile' => $profile])) : 
            ($user->isGuest ? false : ($this->created_by === $user->id));
    }

    /**
     * @return queries\CommentQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Comments\Module::instance()->userIdentityClass, ['id' => 'created_by']);
    }

    /**
     * @return queries\CommentQuery
     */
    public function getLastUpdateAuthor()
    {
        return $this->hasOne(Comments\Module::instance()->userIdentityClass, ['id' => 'updated_by']);
    }

    /**
     * @return queries\CommentQuery
     */
    public static function find()
    {
        return \Yii::createObject(
            Comments\Module::instance()->model('commentQuery'),
            [get_called_class()]
        );
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    const NOT_DELETED = 0;
    const DELETED = 1;
}
<?php
namespace console\controllers;

use common\models\User;
use common\models\Subscription;
use common\models\Utility;
use common\models\profile\Profile;
use yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
// use yii\db\ActiveRecord;

class DateController extends Controller
{
    
    public function actionInit() {

        // Convert profile created_at and updated_at timestamp to unix time
        // $profiles = Profile::find()->all();
        // foreach ($profiles as $profile) {
        //     $created = strtotime($profile->created_at);
        //     $updated = strtotime($profile->updated_at);
        //     $profile->updateAttributes(['created' => $created, 'updated' => $updated]);
        //     echo $profile->id . PHP_EOL;
        // }

        // Return last update dates for all active profiles
        // $profiles = Profile::find()
        //     ->where(['status' => Profile::STATUS_ACTIVE])
        //     // ->andWhere('last_update>=DATE_SUB(CURDATE(), INTERVAL 2 YEAR)')
        //     ->orderBy('last_update DESC')
        //     ->all();
        // foreach ($profiles as $profile) {
        //     echo $profile->last_update . PHP_EOL;
        // } 

        // Add an additional year onto profile renewal date
        // $profiles = Profile::find()->where(['status' => Profile::STATUS_ACTIVE])->all();
        // foreach ($profiles as $profile) {
        //     $renewal = new Expression('DATE_ADD("' . $profile->renewal_date . '", INTERVAL 1 YEAR)');
        //     $profile->updateAttributes(['renewal_date' => $renewal]);
        //     echo $profile->id . PHP_EOL;
        // }
         
        // Move all user subscriptions from user table to subcriptions table
        $users = User::find()->all();         
        $subs = Subscription::find()->all();
        $subEmails = ArrayHelper::getColumn($subs, 'email');
        foreach ($users as $user) {
            if (!in_array($user->email, $subEmails)) {
                $sub = new Subscription();
                $sub->scenario = 'add';
                $sub->email = $user->email;
                $sub->token = Yii::$app->security->generateRandomString(32);
                $sub->save();
                $sub->updateAttributes([
                    'profile' => $user->emailPrefProfile,
                    'links' => $user->emailPrefLinks,
                    'comments' => $user->emailPrefComments,
                    'features' => $user->emailPrefFeatures,
                    'blog' => $user->emailPrefBlog,
                ]);
            }

        }
    }
}

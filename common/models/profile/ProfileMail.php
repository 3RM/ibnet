<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */
 
namespace common\models\profile;

use common\models\profile\Profile;
use common\models\User;
use common\models\Utility;
use frontend\controllers\ProfileController;
use frontend\controllers\ProfileFormController;
use yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "mail".
 *
 * @property int $id
 * @property int $linking_profile
 * @property int $profile
 * @property int $profile_owner
 * @property string $l_type
 * @property string $dir
 */
class ProfileMail extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {   
        return 'mail';
    }

   /**
     * User: Processing profile transfer
     * 
     * @return boolean
     */
    public static function sendProfileTransfer($subject, $title, $msg, $profile, $email, $link)
    {       
        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'profile/profile-transfer-html'], 
                ['title' => $title, 'msg' => $msg, 'profile' => $profile, 'link' => $link]
            )
            ->setFrom([\yii::$app->params['email.admin']])
            ->setTo([$email])
            ->setSubject($subject)
            ->send();

        return true;
    }

    /**
     * User: A new Mailchimp update has been added to the missionary update page
     * 
     * @return boolean
     */
    public static function sendMailchimp($email, $repoKey, $id)
    {       
        $repoUrl = Url::toRoute(['//site/login', 'url' => Url::toRoute(['/missionary/update-repository'], 'https')], 'https');
        $updateUrl = Url::toRoute(['missionary/update', 'repository_key' => $repoKey, 'id' => $id], 'https');
        $msg = 'Your recent Mailchimp campaign has been synced to your IBNet Missionary Updates page.  Visit your Updates admin page ' . 
            Html::a('here', $repoUrl) . ' or your Updates page ' . Html::a('here', $updateUrl) . 
            '.<br><br>Administrator<br><b>IBNet</b> | https://ibnet.org';
        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'site/notification-html'], 
                ['title' => 'New Missionary Update', 'message' => $msg]
            )
            ->setFrom([\yii::$app->params['email.admin']])
            ->setTo($email)
            ->setSubject('IBNet Mailchimp Sync')
            ->send();

        return true;
    }

    /**
     * Send user notification of forwarding email activation
     * 
     * @return boolean
     */
    public static function sendForwardingEmailNotif($email)
    {
        $msg = 'Your forwarding email ' . $email . ' has been set up.<br><br>Administrator<br><b>IBNet</b> | https://ibnet.org';
        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'site/notification-html'], 
                ['message' => $msg]
            )
            ->setFrom([\yii::$app->params['email.admin']])
            ->setTo($email)
            ->setSubject('IBNet Forwarding Email')
            ->send();

        return true;
    }

    /**
     * System: Forwarding email has been requested by user
     * 
     * @return boolean
     */
    public static function sendForwardingEmailRqst($id, $email, $email_pvt)
    {
        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'system/forwarding-email-html'], 
                ['id' => $id, 'email' => $email, 'email_pvt' => $email_pvt]
            )
            ->setFrom([\yii::$app->params['email.noReply']])
            ->setTo([\yii::$app->params['email.admin']])
            ->setSubject('Forwarding Address Request')
            ->send();

        return true;
    }

    /**
     * User: Profile link or unlink has occured.
     * 
     * @return boolean
     */
    public static function sendLink($linkingProfile, $profile, $profileOwner, $lType, $dir)
    {
        switch ($lType) { 
            case 'HC':                                                                              // Home Church
                $subject = 'IBNet Church Profile: Updated Link';
                $dir == 'UL' ?
                    $title = 'A Link to your Church Profile has Changed' :
                    $title = 'New Link to your Church Profile';
                $msg = Html::a($linkingProfile->fullName, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                        'urlLoc' => $linkingProfile->url_loc, 
                        'name' => $linkingProfile->url_name, 
                        'id' => $linkingProfile->id], 'https'));
                $dir == 'UL'? 
                    $msg .= ' has just unlinked from ' :
                    $msg .= ' has just linked to ';
                $msg .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https')) . '.';
                $dir == 'UL'?
                    NULL :
                    $msg .= ' Be sure to visit the ' . Html::a('church staff page', 
                        Url::toRoute(['//site/login', 'url' => Url::toRoute(['/profile-form/form-route', 
                            'type' => $profile->type, 
                            'fmNum' => ProfileFormController::$form['sf']-1, 
                            'id' => $profile->id], 'https')], 'https')) 
                        . ' where you can manage all church staff.';
                break;

            case 'PSHC':                                                                              // Personal Settings Home Church
                $subject = 'IBNet Church Profile: Church Member Link';
                $dir == 'UL' ?
                    $title = 'A Link to your Church Profile has Changed' :
                    $title = 'New Link to your Church Profile';
                $msg = $linkingProfile->fullName;
                $dir == 'UL'? 
                    $msg .= ' has just unlinked from ' :
                    $msg .= ' has just linked to ';
                $msg .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https')) . ' as a church member.';
                break;

            case 'PM':                                                                                  // Parent Ministry
                $profile->type == Profile::TYPE_CHURCH ?
                    $subject = 'IBNet Church Profile: Updated Link' :
                    $subject = 'IBNet Ministry Profile: Updated Link';
                if ($profile->type == Profile::TYPE_CHURCH) {
                    $dir == 'UL' ?
                        $title = 'A Link to your Church Profile has Changed' :
                        $title = 'New Link to your Church Profile';
                } else {
                    $dir == 'UL' ?
                        $title = 'A Link to your Ministry Profile has Changed' :
                        $title = 'New Link to your Ministry Profile';
                }
                if ($linkingProfile->category == Profile::CATEGORY_IND) {
                    $msg = Html::a($linkingProfile->fullName, 
                        Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                            'urlLoc' => $linkingProfile->url_loc, 
                            'name' => $linkingProfile->url_name, 
                            'id' => $linkingProfile->id], 'https'));
                } else {
                    $msg = Html::a($linkingProfile->org_name, 
                        Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                            'urlLoc' => $linkingProfile->url_loc, 
                            'name' => $linkingProfile->url_name, 
                            'id' => $linkingProfile->id], 'https'));
                }
                $dir == 'UL'? 
                    $msg .= ' has just unlinked from ' :
                    $msg .= ' has just linked to ';
                $msg .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https')) . '.';
                if ($linkingProfile->category == Profile::CATEGORY_IND && $profile->type == Profile::TYPE_CHURCH) {
                    $dir == 'UL'?
                        NULL :
                        $msg .= ' Be sure to visit the ' . Html::a('church staff page', 
                            Url::toRoute(['//site/login', 'url' => Url::toRoute(['/profile-form/form-route', 
                                'type' => $profile->type, 
                                'fmNum' => ProfileFormController::$form['sf']-1, 
                                'id' => $profile->id], 'https')], 'https')) 
                            . ' where you can manage all church staff.';
                } elseif ($linkingProfile->category == Profile::CATEGORY_IND) {
                    $dir == 'UL'?
                        NULL :
                        $msg .= ' Be sure to visit the ' . Html::a('staff page', 
                            Url::toRoute(['//site/login', 'url' => Url::toRoute(['/profile-form/form-route', 
                                'type' => $profile->type, 
                                'fmNum' => ProfileFormController::$form['sf']-1, 
                                'id' => $profile->id], 'https')], 'https')) 
                            . ' where you can manage all ' . $profile->type . ' staff.';
                }
                break;

            case 'SF':                                                                              // Staff
                $subject = 'IBNet Profile Status Update';
                $dir == 'UL' ?
                    $title = 'Change to Your Status' :
                    $title = 'Staff Status Confirmed';
                $msg = Html::a($linkingProfile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                        'urlLoc' => $linkingProfile->url_loc, 
                        'name' => $linkingProfile->url_name, 
                        'id' => $linkingProfile->id], 'https'));
                $dir == 'UL'? 
                    $msg .= ' has just removed your status as staff for your profile "' :
                    $msg .= ' has just confirmed your status as staff for your profile "';
                $msg .= Html::a($profile->profile_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https')) . '".';
                break;

            case 'SFSA':                                                                            // Staff Senior Pastor
                $subject = 'IBNet Profile Status Update';
                $dir == 'UL' ?
                    $title = 'Change to your Status' :
                    $title = 'Staff Status Confirmed';
                $msg = Html::a($linkingProfile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                        'urlLoc' => $linkingProfile->url_loc, 
                        'name' => $linkingProfile->url_name, 
                        'id' => $linkingProfile->id], 'https'));
                $dir == 'UL'? 
                    $msg .= ' has just removed your status as Senior Pastor for your profile "' :
                    $msg .= ' has just confirmed your status as Senior Pastor for your profile "';
                $msg .= Html::a($profile->profile_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https')) . '".';
                break;

            case 'PG':                                                                              // Program
                $subject = 'IBNet Profile: Updated Link';
                $dir == 'UL' ?
                    $title = 'Change to a Linked Minsitry' :
                    $title = 'New Linked Minsitry';
                $msg = Html::a($linkingProfile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                        'urlLoc' => $linkingProfile->url_loc, 
                        'name' => $linkingProfile->url_name, 
                        'id' => $linkingProfile->id], 'https'));
                $dir == 'UL'? 
                    $msg .= ' has just removed ' :
                    $msg .= ' has just added ';
                $msg .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https'));
                $msg .= ' as a church program.';
                break;

            case 'AS':                                                                              // Association/Fellowship
                $subject = 'IBNet Profile: Updated Link';
                $dir == 'UL' ?
                    $title = 'Change to a Linked Minsitry' :
                    $title = 'New Linked Minsitry';
                $linkingProfile->category == Profile::CATEGORY_IND ?
                    $msg = Html::a($linkingProfile->fullName, 
                        Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                            'urlLoc' => $linkingProfile->url_loc, 
                            'name' => $linkingProfile->url_name, 
                            'id' => $linkingProfile->id], 'https')) :
                    $msg = Html::a($linkingProfile->org_name, 
                        Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                            'urlLoc' => $linkingProfile->url_loc, 
                            'name' => $linkingProfile->url_name, 
                            'id' => $linkingProfile->id], 'https'));
                if ($linkingProfile->category == Profile::CATEGORY_IND) {
                    $dir == 'UL'? 
                        $msg .= ' has just removed his affiliated status with the ' :
                        $msg .= ' has just indicated his affiliation with the ';
                } else {
                    $dir == 'UL'? 
                        $msg .= ' has just removed their affiliated status with the ' :
                        $msg .= ' has just indicated their affiliation with the ';
                }                
                $msg .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https'));
                $linkingProfile->category == Profile::CATEGORY_IND ?
                    $msg .= ' on his personal profile.' :
                    $msg .= ' on their church profile.';
                break;

            case 'MA':                                                                              // Mission Agency
                $subject = 'IBNet Profile Update';
                $dir == 'UL' ?
                    $title = 'Change to a Linked Missionary' :
                    $title = 'New Linked Missionary';
                $msg = 'Missionary ';
                $msg .= Html::a($linkingProfile->fullName, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                        'urlLoc' => $linkingProfile->url_loc, 
                        'name' => $linkingProfile->url_name, 
                        'id' => $linkingProfile->id], 'https'));
                $dir == 'UL'? 
                    $msg .= ' has just unlinked from the ' :
                    $msg .= ' has just linked to the ';
                $msg .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https')) . ' profile.';
                break;

            case 'SA':                                                                              // School Attended
                $subject = 'IBNet Profile Update';
                $dir == 'UL' ?
                    $title = 'Change to a Linked Profile' :
                    $title = 'New Linked Profile';
                $msg = Html::a($linkingProfile->fullName, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                        'urlLoc' => $linkingProfile->url_loc, 
                        'name' => $linkingProfile->url_name, 
                        'id' => $linkingProfile->id], 'https'));
                $dir == 'UL'? 
                    $msg .= ' has just unlinked from ' :
                    $msg .= ' has just identified ';
                $msg .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https'));
                $dir == 'UL' ?
                    NULL :
                    $msg .= ' as an attended school.';
                break;
            
            default:
                //
                break;
        }

        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'profile/profile-link-html'],
                ['title' => $title, 'msg' => $msg, 'profile' => $profile]
            )
            ->setFrom([\yii::$app->params['email.admin']])
            ->setTo([$profileOwner->email])
            ->setSubject($subject)
            ->send();

        return true;
    }

    /**
     * Notify a profile owner of new comment
     * 
     * @return boolean
     */
    public static function sendComment($id, $createdBy)
    {
        $user = User::findOne($profile->user_id);
        if ($user->emailPrefComments != 1) {
            return true;
        }
        $commenter = User::findOne($createdBy);
        $profile = Profile::findOne($id);
        $title = '<b>New Comment</b>';
        $msg = $commenter->display_name . ' just left a comment on your profile "' . $profile->profile_name . '". Click ' . 
            Html::a('here', 
                Url::toRoute([
                    'profile/' . ProfileController::$profilePageArray[$profile->type], 
                    'urlLoc' => $profile->url_loc, 
                    'name' => $profile->url_name,
                    'id' => $profile->id,
                    'p' => 'comments', 
                    '#' => 'p'
                ], ['target' => '_blank', 'rel' => 'noopener noreferrer'])
            ) . ' to see it.';
        
        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'site/notification-html'], 
                ['title' => $title, 'message' => $msg]
            )
            ->setFrom([\yii::$app->params['email.admin']])
            ->setTo($user->email)
            ->setSubject('IBNet | New Comment')
            ->send();

        return true;
    }

    /**
     * Notify a profile owner of new like
     * 
     * @return boolean
     */
    public static function sendLike($profile, $likedBy)
    {
        if ($likedBy->emailPrefLinks != 1) {
            return true;
        }
        $profileOwner = User::findOne($profile->user_id);
        if ($likedBy->id == $profileOwner->id) {                                            // Don't send message to self
            return true;
        }
        if ($likedByProfile =  Profile::find()
                ->where(['user_id' => $likedBy->id])
                ->andWhere(['status' => Profile::STATUS_ACTIVE])
                ->andWhere(['category' => Profile::CATEGORY_IND])
                ->one()) {
            if (empty($likedByProfile->spouse_first_name)) {
                $name = $likedByProfile->coupleName;
                $pronoun = ' themself ';
                $verb = ' has ';
                $noun = ' a friend ';
            } else {
                $name = $likedByProfile->coupleName;
                $pronoun = ' themselves ';
                $verb = ' have ';
                $noun = ' friends ';
            }
        } else {
            $name = $likedBy->display_name;
            $pronoun = 'themself';
            $verb = ' has ';
            $noun = ' a friend';
        }
        $title = '<b>New Connection</b>';
        $msg = $name . ' ' . $verb . ' identified ' . $pronoun . ' as ' . $noun . ' of your ministry on profile "'
           . $profile->profile_name . '". Click ' . 
            Html::a('here', 
                Url::toRoute([
                    'profile/' . ProfileController::$profilePageArray[$profile->type], 
                    'urlLoc' => $profile->url_loc, 
                    'name' => $profile->url_name,
                    'id' => $profile->id,
                    'p' => 'connections', 
                    '#' => 'p'
                ], ['target' => '_blank', 'rel' => 'noopener noreferrer'])
            ) . ' to see it.';
        
        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'site/notification-html'], 
                ['title' => $title, 'message' => $msg]
            )
            ->setFrom([\yii::$app->params['email.admin']])
            ->setTo($profileOwner->email)
            ->setSubject('IBNet | New Connection')
            ->send();

        return true;
    }

    /**
     * Admin: send admin notice of new profile creation
     * 
     * @return boolean
     */
    public static function sendAdminNewProfile($id)
    {
        $profile = Profile::findOne($id);
        $user = User::findOne($profile->user_id);
        $userName = $user->fullName;
        $title = 'Newly Created Profile';
        $msg = 'A profile was just created by ' . $userName . ': ' . $profile->profile_name;

        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'site/notification-html'], 
                ['title' => $title, 'message' => $msg]
            )
            ->setFrom([\yii::$app->params['email.admin']])
            ->setTo([\yii::$app->params['email.admin']])
            ->setSubject(Yii::$app->params['email.systemSubject'])
            ->send();

        return true;
    }

    /**
     * Admin: send admin notice of newly activated profile
     * 
     * @return boolean
     */
    public static function sendAdminActiveProfile($id)
    {
        $profile = Profile::findOne($id);
        $user = User::findOne($profile->user_id);
        $userName = $user->fullName;
        $title = 'Newly Activated Profile';
        $name = $profile->category == Profile::CATEGORY_IND ?
            $profile->fullName :
            $profile->org_name;
        $msg = 'A profile was just activated by ' . $userName . ' : ' . $name;

        Yii::$app
            ->mailer
            ->compose(
                ['html' => 'site/notification-html'], 
                ['title' => $title, 'message' => $msg]
            )
            ->setFrom([\yii::$app->params['email.admin']])
            ->setTo([\yii::$app->params['email.admin']])
            ->setSubject(Yii::$app->params['email.systemSubject'])
            ->send();

        return true;
    }

}
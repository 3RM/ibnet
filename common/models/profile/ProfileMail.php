<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */
 
namespace common\models\profile;

use common\models\profile\Profile;
use common\models\Subscription;
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
     * Send new user notice of profile transfer request
     * @return boolean
     */
    public static function sendProfileTransfer($profile, $newUser, $oldUser, $complete=false)
    {   
        $mail = Subscription::getSubscriptionByEmail($newUser->email) ?? new Subscription();
        $mail->headerColor = Subscription::COLOR_PROFILE;
        $mail->headerImage = Subscription::IMAGE_PROFILE;
        $mail->headerText = 'Profile Transfer';
        $mail->to = $complete ? $oldUser->email : $newUser->email;
        $mail->subject = $complete ? 'IBNet Profile Transfer Complete' : 'IBNet Profile Transfer Request';
        $mail->title = $complete ? 'IBNet Profile Transfer Complete' : 'IBNet Profile Transfer Request';
        $mail->message = $complete ? 
            'Your IBNet profile "' . $profile->profile_name . '" has been successfully transferred 
                to ' . $newUser->fullName :
            $oldUser->fullName . ' requests that you assume ownership of IBNet profile "' . 
                $profile->profile_name . '".  Click the link below to complete the transfer and take ownership 
                of this profile.  This link will remain valid for one week.';
        $resetLink = $complete ? NULL : Yii::$app->urlManager->createAbsoluteUrl(['profile-mgmt/transfer-complete', 'id' => $profile->id, 'token' => $profile->transfer_token]);
        $mail->link = $complete ? NULL : Html::a(Html::encode($resetLink), $resetLink);
        
        return $mail->sendNotification();
    }

    /**
     * Send user notice that Mailchimp update has been added to the missionary update page
     * @return boolean
     */
    public static function sendMailchimp($email, $repoKey, $id)
    {   
        $mail = Subscription::getSubscriptionByEmail($email) ?? new Subscription();
        $mail->headerColor = Subscription::COLOR_MISSIONARY;
        $mail->headerImage = Subscription::IMAGE_MAILCHIMP;
        $mail->headerText = 'Mailchimp Sync';
        $mail->to = $email;
        $mail->subject = 'Mailchimp Sync';
        $mail->title = 'New Missionary Update';
        $updateUrl = Url::toRoute(['missionary/update', 'repository_key' => $repoKey, 'id' => $id], 'https');
        $mail->message = 'Your recent Mailchimp campaign has been synced to your IBNet Missionary Updates page.  Visit your Updates admin page ' . 
            Html::a('here', Yii::$app->params['url.loginFirst'] . 'missionary/update-repository') . ' or your Updates page ' . Html::a('here', $updateUrl);
        $mail->sendNotification();

        return true;
    }

    /**
     * Send user notice of forwarding email activation
     * @return boolean
     */
    public static function sendForwardingEmailNotif($email)
    {
        $mail = Subscription::getSubscriptionByEmail($email) ?? new Subscription();
        $mail->headerColor = Subscription::COLOR_PROFILE;
        $mail->headerImage = Subscription::IMAGE_PROFILE;
        $mail->headerText = 'Forwarding Email';
        $mail->to = $email;
        $mail->subject = 'IBNet Forwarding Email';
        $mail->message = 'Your forwarding email ' . $email . ' has been set up.';
        $mail->sendNotification();

        return true;
    }

    /**
     * Send admin notice of new forwarding email request
     * @return boolean
     */
    public static function sendForwardingEmailRqst($id, $email, $email_pvt)
    {
        $mail = Subscription::getSubscriptionByEmail(Yii::$app->params['email.admin']);
        $mail->to = Yii::$app->params['email.admin'];
        $mail->subject = 'Forwarding Email Request';
        $mail->title = 'Forward Email Request';
        $mail->message = 'ID: ' . $id . '; IBNet Email: ' . $email . '; Private Email: ' . $email_pvt;
        $mail->sendNotification();

        return true;
    }

    /**
     * Send linking notification if linking profile is active
     * If linking profile is inactive, save parameters to db and run when profile goes active
     * @param $linkingProfile object Profile doing the linking
     * @param $profile object Profile being linked to
     * @param $profileOwner object Owner of profile being linked to
     * @param $lType string Type of link
     * @param $dir string Direction of link (link/unlink)
     * @return boolean
     */
    public static function initSendLink($linkingProfile, $profile, $profileOwner, $lType, $dir)
    {   
        // linking profile is new or inactive
        if ($linkingProfile->status != Profile::STATUS_ACTIVE) {
            
            // link exists in db
            if ($mail = self::find()
                ->where(['linking_profile' => $linkingProfile->id])
                ->andWhere(['profile' => $profile->id])
                ->andWhere(['profile_owner' => $profileOwner->id])
                ->andWhere(['l_type' => $lType])
                ->one()) {
                // Update direction in case it is opposite
                $mail->dir = $dir;
                $mail->save();

            // Link doens't exist in db, add it
            } else {  
                $mail = new ProfileMail();
                $mail->linking_profile = $linkingProfile->id;
                $mail->profile = $profile->id;
                $mail->profile_owner = $profileOwner->id;
                $mail->l_type = $lType;
                $mail->dir = $dir;
                // Set original direction to be opposite of current selection
                if ($mail->orig_dir == NULL) {
                    // This will be used at time of send to avoid sending out notification for current selection
                    $mail->orig_dir = $dir == 'L' ? 'UL' : 'L';
                }
                $mail->save();
            }

        // profile is active, send notificaiton
        } else {
            self::sendLink($linkingProfile, $profile, $profileOwner, $lType, $dir);
        }

        return true;
    }

    /**
     * Send linking notifications that are stored in db when profile goes active
     * @param $id integer id of ProfileMail model
     * @return boolean
     */
    public static function dbSendLink($id)
    {
        $mailArray = ProfileMail::find()->where(['linking_profile' => $id])->all();
        
        foreach ($mailArray as $mail) {
            
            // Don't send if link direction is the same as original direction
            if ($mail->dir == $mail->orig_dir) {
                $mail->delete();
                break;
            }
                        
            $linkingProfile = Profile::findProfile($mail->linking_profile);
            $profile = Profile::findActiveProfile($mail->profile);
            $profileOwner = $profile->user;

            // Don't send if link direction is unlink and profile status is new
            if ($mail->dir == 'UL' && $linkingProfile->status == Profile::STATUS_NEW) {
                $mail->delete();
                break;
            }

            if ($linkingProfile && $profile && $profileOwner) {
                $mail->sendLink($linkingProfile, $profile, $profileOwner, $mail->l_type, $mail->dir);
            }
            $mail->delete();
        }
    }

    /**
     * Send user notice of profile link change
     * 
     * @return boolean
     */
    public static function sendLink($linkingProfile, $profile, $profileOwner, $lType, $dir)
    {
        $mail = $profileOwner->subscription ?? new Subscription();
        $mail->to = $profileOwner->email;
        $mail->headerColor = Subscription::COLOR_PROFILE;
        $mail->headerImage = Subscription::IMAGE_PROFILE;
        $mail->headerText = Subscription::TEXT_LINK;
        switch ($lType) { 
            case 'HC':                                                                              // Home Church
                $mail->subject = 'Church Profile Updated Link';
                $mail->title = $dir == 'UL' ? 'A Link to your Church Profile has Changed' : 'New Link to your Church Profile';
                $mail->message = Html::a($linkingProfile->fullName, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                        'urlLoc' => $linkingProfile->url_loc, 
                        'name' => $linkingProfile->url_name, 
                        'id' => $linkingProfile->id], 'https'));
                $mail->message .= $dir == 'UL'? ' has just unlinked from ' : ' has just linked to ';
                $mail->message .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https')) . '.';
                $mail->message .= $dir == 'UL'? NULL :
                    ' Be sure to visit the ' . Html::a('church staff page', 
                    Url::toRoute(['//site/login', 'url' => Url::toRoute(['/profile-form/form-route', 
                        'type' => $profile->type, 
                        'fmNum' => ProfileFormController::$form['sf']-1, 
                        'id' => $profile->id], 'https')], 'https')) 
                    . ' where you can manage all church staff.';
                break;

            case 'PSHC':                                                                              // Personal Settings Home Church
                $mail->subject = 'Church Profile Member Link';
                $mail->title = $dir == 'UL' ? 'A Link to your Church Profile has Changed' : 'New Link to your Church Profile';
                $mail->message = $linkingProfile->fullName;
                $mail->message .= $dir == 'UL'? ' has just unlinked from ' : ' has just linked to ';
                $mail->message .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https')) . ' as a church member.';
                break;

            case 'PM':                                                                                  // Parent Ministry
                $mail->subject = $profile->type == Profile::TYPE_CHURCH ? 
                    'Church Profile Updated Link' : 
                    'Ministry Profile Updated Link';
                if ($profile->type == Profile::TYPE_CHURCH) {
                    $mail->title = $dir == 'UL' ?
                        'A Link to your Church Profile has Changed' :
                        'New Link to your Church Profile';
                } else {
                    $mail->title = $dir == 'UL' ?
                        'A Link to your Ministry Profile has Changed' :
                        'New Link to your Ministry Profile';
                }
                if ($linkingProfile->category == Profile::CATEGORY_IND) {
                    $mail->message = Html::a($linkingProfile->fullName, 
                        Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                            'urlLoc' => $linkingProfile->url_loc, 
                            'name' => $linkingProfile->url_name, 
                            'id' => $linkingProfile->id], 'https'));
                } else {
                    $mail->message = Html::a($linkingProfile->org_name, 
                        Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                            'urlLoc' => $linkingProfile->url_loc, 
                            'name' => $linkingProfile->url_name, 
                            'id' => $linkingProfile->id], 'https'));
                }
                $mail->message .= $dir == 'UL'? ' has just unlinked from ' : ' has just linked to ';
                $mail->message .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https')) . '.';
                if ($linkingProfile->category == Profile::CATEGORY_IND && $profile->type == Profile::TYPE_CHURCH) {
                    $mail->message .= $dir == 'UL'? NULL :
                        ' Be sure to visit the ' . Html::a('church staff page', 
                        Url::toRoute(['//site/login', 'url' => Url::toRoute(['/profile-form/form-route', 
                            'type' => $profile->type, 
                            'fmNum' => ProfileFormController::$form['sf']-1, 
                            'id' => $profile->id], 'https')], 'https')) 
                        . ' where you can manage all church staff.';
                } elseif ($linkingProfile->category == Profile::CATEGORY_IND) {
                    $mail->message .= $dir == 'UL'? NULL :
                        ' Be sure to visit the ' . Html::a('staff page', 
                        Url::toRoute(['//site/login', 'url' => Url::toRoute(['/profile-form/form-route', 
                            'type' => $profile->type, 
                            'fmNum' => ProfileFormController::$form['sf']-1, 
                            'id' => $profile->id], 'https')], 'https')) 
                        . ' where you can manage all ' . $profile->type . ' staff.';
                }
                break;

            case 'SF':                                                                              // Staff
                $mail->subject = 'Profile Status Update';
                $mail->title = $dir == 'UL' ? 'Change to Your Status' : 'Staff Status Confirmed';
                $mail->message = Html::a($linkingProfile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                        'urlLoc' => $linkingProfile->url_loc, 
                        'name' => $linkingProfile->url_name, 
                        'id' => $linkingProfile->id], 'https'));
                $mail->message .= $dir == 'UL'? 
                    ' has just removed your status as staff for your profile "' :
                    ' has just confirmed your status as staff for your profile "';
                $mail->message .= Html::a($profile->profile_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https')) . '".';
                break;

            case 'SFSA':                                                                            // Staff Senior Pastor
                $mail->subject = 'Profile Status Update';
                $mail->title = $dir == 'UL' ? 'Change to your Status' : 'Staff Status Confirmed';
                $mail->message = Html::a($linkingProfile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                        'urlLoc' => $linkingProfile->url_loc, 
                        'name' => $linkingProfile->url_name, 
                        'id' => $linkingProfile->id], 'https'));
                $mail->message .= $dir == 'UL'? 
                    ' has just removed your status as Senior Pastor for your profile "' :
                    ' has just confirmed your status as Senior Pastor for your profile "';
                $mail->message .= Html::a($profile->profile_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https')) . '".';
                break;

            case 'PG':                                                                              // Program
                $mail->subject = 'Profile Updated Link';
                $mail->title = $dir == 'UL' ? 'Change to a Linked Minsitry' : 'New Linked Minsitry';
                $mail->message = Html::a($linkingProfile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                        'urlLoc' => $linkingProfile->url_loc, 
                        'name' => $linkingProfile->url_name, 
                        'id' => $linkingProfile->id], 'https'));
                $mail->message .= $dir == 'UL'? ' has just removed ' : ' has just added ';
                $mail->message .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https'));
                $mail->message .= ' as a church program.';
                break;

            case 'AS':                                                                              // Association/Fellowship
                $mail->subject = 'Profile Updated Link';
                $mail->title = $dir == 'UL' ? 'Change to a Linked Minsitry' : 'New Linked Minsitry';
                $mail->message = $linkingProfile->category == Profile::CATEGORY_IND ?
                    Html::a($linkingProfile->fullName, 
                        Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                            'urlLoc' => $linkingProfile->url_loc, 
                            'name' => $linkingProfile->url_name, 
                            'id' => $linkingProfile->id], 'https')) :
                    Html::a($linkingProfile->org_name, 
                        Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                            'urlLoc' => $linkingProfile->url_loc, 
                            'name' => $linkingProfile->url_name, 
                            'id' => $linkingProfile->id], 'https'));
                if ($linkingProfile->category == Profile::CATEGORY_IND) {
                    $mail->message .= $dir == 'UL'? 
                        ' has just removed his affiliated status with the ' :
                        ' has just indicated his affiliation with the ';
                } else {
                    $mail->message .= $dir == 'UL'? 
                        ' has just removed their affiliated status with the ' :
                        ' has just indicated their affiliation with the ';
                }                
                $mail->message .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https'));
                $mail->message .= $linkingProfile->category == Profile::CATEGORY_IND ?
                    ' on his personal profile.' :
                    ' on their church profile.';
                break;

            case 'MA':                                                                              // Mission Agency
                $mail->subject = 'Profile Update';
                $mail->title = $dir == 'UL' ? 'Change to a Linked Missionary' : 'New Linked Missionary';
                $mail->message = 'Missionary ' . Html::a($linkingProfile->fullName, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                        'urlLoc' => $linkingProfile->url_loc, 
                        'name' => $linkingProfile->url_name, 
                        'id' => $linkingProfile->id], 'https'));
                $mail->message .= $dir == 'UL'? ' has just unlinked from the ' : ' has just linked to the ';
                $mail->message .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https')) . ' profile.';
                break;

            case 'SA':                                                                              // School Attended
                $mail->subject = 'Profile Update';
                $mail->title = $dir == 'UL' ? 'Change to a Linked Profile' : 'New Linked Profile';
                $mail->message = Html::a($linkingProfile->fullName, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$linkingProfile->type], 
                        'urlLoc' => $linkingProfile->url_loc, 
                        'name' => $linkingProfile->url_name, 
                        'id' => $linkingProfile->id], 'https'));
                $mail->message .= $dir == 'UL'? ' has just unlinked from ' : ' has just identified ';
                $mail->message .= Html::a($profile->org_name, 
                    Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                        'urlLoc' => $profile->url_loc, 
                        'name' => $profile->url_name, 
                        'id' => $profile->id], 'https'));
                $mail->message .= $dir == 'UL' ? NULL : ' as an attended school.';
                break;
            
            default:
                //
                break;
        }
        $mail->sendNotification();

        return true;
    }

    /**
     * Send notice to profile owner of new comment
     * 
     * @return boolean
     */
    public static function sendComment($id, $createdBy)
    {
        $user = $profile->user;
        if ($user->subscription->blog == 0) {
            return true;
        }
        $commenter = User::findOne($createdBy);
        $profile = Profile::findOne($id);
        $mail = $user->subscription ?? new Subscription();
        $mail->headerColor = Subscription::COLOR_PROFILE;
        $mail->headerImage = Subscription::IMAGE_PROFILE_COMMENT;
        $mail->headerText = 'Profile Comment';
        $mail->to = $user->email;
        $mail->subject = 'New Comment';
        $mail->title = 'New Comment';
        $mail->message = $commenter->fullName . ' just left a comment on your profile "' . $profile->profile_name . '". Click ' . 
            Html::a('here', 
                Url::toRoute(['profile/' . ProfileController::$profilePageArray[$profile->type], 
                    'urlLoc' => $profile->url_loc, 
                    'name' => $profile->url_name,
                    'id' => $profile->id,
                    'p' => 'comments', 
                    '#' => 'p'
                ], ['target' => '_blank', 'rel' => 'noopener noreferrer'])
            ) . ' to see it.';
        $mail->sendNotification();

        return true;
    }

    /**
     * Send notice to profile owner of new like
     * 
     * @return boolean
     */
    public static function sendLike($profile, $likedBy)
    {
        $profileOwner = $profile->user;
        if (($profileOwner->subscription->links == 0) || ($likedBy->id == $profileOwner->id)) {
            return true;
        }

        $mail = $profileOwner->subscription ?? new Subscription();
        $mail->headerColor = Subscription::COLOR_PROFILE;
        $mail->headerImage = Subscription::IMAGE_PROFILE;
        $mail->headerText = 'Profile Like';
        $mail->to = $profileOwner->email;
        $mail->subject = 'New Connection';
        $mail->title = 'New Connection';
        $mail->message = $likedBy->fullName . ' likes your profile "'. $profile->profile_name . '". ' .  
            Html::a('Click here', 
                Url::toRoute([
                    'profile/' . ProfileController::$profilePageArray[$profile->type], 
                    'urlLoc' => $profile->url_loc, 
                    'urlName' => $profile->url_name,
                    'id' => $profile->id,
                    'p' => 'connections', 
                    '#' => 'p'
                ], ['target' => '_blank', 'rel' => 'noopener noreferrer'])
            ) . ' to see.';
        $mail->sendNotification();

        return true;
    }

    /**
     * Send admin notice of new profile creation
     * 
     * @return boolean
     */
    public static function sendAdminNewProfile($id)
    {
        $profile = Profile::findOne($id);
        $user = User::findOne($profile->user_id);
        $userName = $user->fullName;
        $mail = Subscription::getSubscriptionByEmail(Yii::$app->params['email.admin']);
        $mail->to = Yii::$app->params['email.admin'];
        $mail->subject = Yii::$app->params['email.systemSubject'];
        $mail->title = 'Newly Created Profile';
        $mail->message = 'A profile was just created by ' . $userName . ': ' . $profile->profile_name;
        $mail->sendNotification();

        return true;
    }

    /**
     * Send admin notice of newly activated profile
     * 
     * @return boolean
     */
    public static function sendAdminActiveProfile($id)
    {
        $profile = Profile::findOne($id);
        $user = User::findOne($profile->user_id);
        $mail = Subscription::getSubscriptionByEmail(Yii::$app->params['email.admin']);
        $mail->to = Yii::$app->params['email.admin'];
        $mail->subject = Yii::$app->params['email.systemSubject'];
        $mail->title = 'Newly Activated Profile';
        $mail->message = 'A profile was just activated by ' . $user->fullName . ': ' . 
            ($profile->category == Profile::CATEGORY_IND ? $profile->fullName : $profile->org_name);
        $mail->sendNotification();

        return true;
    }

}
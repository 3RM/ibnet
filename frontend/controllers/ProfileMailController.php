<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */
 
namespace frontend\controllers;

use common\models\Utility;
use common\models\profile\Profile;
use common\models\profile\ProfileMail;
use frontend\controllers\ProfileController;
use Yii;
use yii\web\Controller;

/**
 * Mail controller
 */
class ProfileMailController extends Controller
{
    /**
     * Send linking notification if linking profile is active
     * If linking profile is inactive, save parameters to db and run when profile goes active
     * 
     * @param object $linkingProfile    Profile doing the linking
     * @param object $profile           Profile being linked to
     * @param object $profileOwner      Owner of profile being linked to
     * @param string $lType             Type of link
     * @param string $dir               Direction of link (link/unlink)
     * @return boolean
     */
    public function initSendLink($linkingProfile, $profile, $profileOwner, $lType, $dir)
    {   
        // check if profile owner has email preferences set to receive link notifications
        if ($profileOwner->emailPrefLinks != 1) {
            return true;
        }

        // linking profile is new or inactive
        if ($linkingProfile->status != Profile::STATUS_ACTIVE) {
            
            // link exists in db
            if ($mail = ProfileMail::find()
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
                $mail = new ProfileMail;
                $mail->linking_profile = $linkingProfile->id;
                $mail->profile = $profile->id;
                $mail->profile_owner = $profileOwner->id;
                $mail->l_type = $lType;
                $mail->dir = $dir;
                // Set original direction to be opposite of current selection
                if ($mail->orig_dir == NULL) {
                    // This will be used at time of send to avoid sending out notification for current selection
                    $dir == 'L' ?
                        $mail->orig_dir = 'UL' :
                        $mail->orig_dir = 'L';
                }
                $mail->save();
            }

        // profile is active, send notificaiton
        } else {
            ProfileMail::sendLink($linkingProfile, $profile, $profileOwner, $lType, $dir);
        }

        return true;
    }

    /**
     * Send linking notifications that are stored in db when profile goes active
     * 
     * @param int $id
     * @return boolean
     */
    public function dbSendLink($id)
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
}

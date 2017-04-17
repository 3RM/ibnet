<?php
namespace frontend\controllers;

use common\models\Utility;
use common\models\profile\Mail;
use common\models\profile\Profile;
use frontend\controllers\ProfileController;
use Yii;
use yii\web\Controller;

/**
 * Mail controller
 */
class MailController extends Controller
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
        if ($profileOwner->emailPrefLinks != 1) {                                                   // check if profile owner has email preferences set to receive link notifications
            return true;
        }

        if ($linkingProfile->status != Profile::STATUS_ACTIVE) {                                    // linking profile is new or inactive (don't save for active profiless, emails are sent in real time)
            
            if ($mail = Mail::find()
                ->where(['linking_profile' => $linkingProfile->id])
                ->andWhere(['profile' => $profile->id])
                ->andWhere(['profile_owner' => $profileOwner->id])
                ->andWhere(['l_type' => $lType])
                ->one()) {                                                                          // link exists
                $mail->dir = $dir;                                                                  // update direction in case it is opposite
                $mail->save();
            } else {                                                                                // Link doens't exist in db, add it  
                $mail = new Mail;
                $mail->linking_profile = $linkingProfile->id;
                $mail->profile = $profile->id;
                $mail->profile_owner = $profileOwner->id;
                $mail->l_type = $lType;
                $mail->dir = $dir;
                if ($mail->orig_dir == NULL) {                                                      // Set original direction to be opposite of current selection
                    $dir == 'L' ?                                                                       // This will be used at time of send to avoid sending out  
                        $mail->orig_dir = 'UL' :                                                        // notification for current selection
                        $mail->orig_dir = 'L';
                }
                $mail->save();
            }

        } else {
            $mail->sendLink($linkingProfile, $profile, $profileOwner, $lType, $dir);                // profile is active, send notificaiton
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
        $mailArray = Mail::find()->where(['linking_profile' => $id])->all();
        
        foreach ($mailArray as $mail) {
        
            if ($mail->dir == $mail->orig_dir) {                                                    // Don't send if link direction is the same as original direction
                $mail->delete();
                break;
            }
                        
            $linkingProfile = ProfileController::findProfile($mail->linking_profile);
            $profile = ProfileController::findActiveProfile($mail->profile);
            $profileOwner = $profile->user;

            if ($mail->dir == 'UL' && $linkingProfile->status == Profile::STATUS_NEW) {             // Don't send if link direction is unlink and profile status is new
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

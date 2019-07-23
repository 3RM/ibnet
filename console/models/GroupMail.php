<?php

namespace console\models;

use common\models\Subscription;
use common\models\group\Group;
use common\models\group\GroupMember;
use common\models\group\GroupNotification;
use common\models\group\GroupNotificationMessageID;
use common\models\group\Prayer;
use common\models\group\GroupAlertQueue;
use common\models\group\PrayerTag;
use common\models\group\PrayerUpdate;
use common\models\missionary\MissionaryUpdate;
use SSilence\ImapClient\ImapClientException;
use SSilence\ImapClient\ImapConnect;
use SSilence\ImapClient\ImapClient as Imap;
use EmailReplyParser\Parser\EmailParser;
use Yii;
use Yii\base\Model;
use yii\bootstrap\Html;

class GroupMail extends Model
{
    const SEPARATOR = '##';
    const SIG_REGEX = '/(?:^\s*--|^\s*__|^-\w|^-- $)|(?:^Sent (from|via) (my|the )?(?:\s*\w+){1,4}$)|(?:^={30,}$)$/s';

    /**
     * Retrieve mail and process new prayer requests, updates, and answers; send alerts
     * @param object $group
     * @return Boolean
     */
    public function processPrayer(Group $group) 
    {
    	
        if ($group->prayer_email == NULL || $group->prayer_email_pwd == NULL) {
            return false;
        }

        //Get subscribed group members
        $members = $group->prayerAlertMembers;

        // Process alert queue (requests generated from webform)
        if ($alerts = $group->prayerImmediateAlertQueue) {
            foreach($alerts as $alert) {
                if ($prayer = $alert->prayer) {
                    foreach ($members as $member) {
                        $prayer->toEmail = $member->user->email;
                        $prayer->toName = $member->user->fullName;
                        Yii::$app->formatter->timeZone = $member->user->timezone;
                        // Check for unsubscribed members (returns false if unsubscribed)
                        if ($return = $prayer->prepareAlert($alert->status)) {
                            $messages[] = $return;
                        }
                    }
                    Yii::$app->mailer->sendMultiple($messages);
                    unset($messages);
                }
                $alert->updateAttributes(['alerted' => 1]);
            }
        }
      
        // Process email requests and send alerts
    	$id = $group->id;
    	$imap = new Imap('imap.ionos.com', $group->prayer_email, $group->prayer_email_pwd, Imap::ENCRYPT_SSL);
        $imap->selectFolder('INBOX');
        try {
        	$unreadEmails = $imap->getUnreadMessages();  // false to keep messages unread
        } catch (\Exception $e) {
            echo $e->getMessage() . ' (' . $group->name . ' prayer requests)' . PHP_EOL;
            return false;
        }

        foreach ($unreadEmails as $email) {

            // check if email is from a group member
            $from = $email->header->details->from[0];
            $fromAddress = $from->mailbox . '@' . $from->host;
            if (!$member = GroupMember::find()
                ->joinWith('user')
                ->where(['group_member.group_id' => $id, 'user.email' => $fromAddress])
                ->one()) {
                continue;
            }
            
            // Fetch email subject and body
            if (!$subject = $email->header->subject) {
                continue;
            }
            $types = $email->message->types;
            if (in_array('text', $types) || in_array('plain', $types)) {
                $body = $email->message->plain->__get('body');  
            } elseif (in_array('html', $types)) {
                $body = $email->message->html->__get('body');
            }

            // New Request
            if (is_string($subject) && !isset($email->header->in_reply_to)) {
                
                // Check if duration and tags
                $description = NULL;
                $duration = NULL;
                $tags = NULL;
                $durTags = NULL;
                if (is_string($body) && (strpos(strtolower($body), self::SEPARATOR) !== false)) {
                    // Parse duration and tags
                    list ($description, $durTags) = explode(self::SEPARATOR, $body, 2);
                    if (is_string($durTags)) {
                        list ($duration, $tags,) = explode(self::SEPARATOR, $durTags, 3);
                    }
                } else {
                    $description = $body;
                }
        
                // Create new prayer request
                $prayer = new Prayer;
                $prayer->scenario = 'prayer';
                $prayer->group_id = $id;
                $prayer->group_member_id = $member->id;
                $prayer->request = $subject;
                $prayer->description = $description;
                if (in_array($duration, [1, 2, 3, 4])) {
                    $prayer->duration = (intVal($duration) * 10);
                }
                $prayer->save();
                    
                // Save tags 
                if (is_string($tags) && ($tags = explode(',', $tags))) {
                    // Convert tags to ids
                    $tagList = [];
                    foreach ($tags as $tag) {
                        if ($t = PrayerTag::find()
                            ->where(['group_id' => $id, 'tag' => trim(strtolower($tag)), 'deleted' => 0])
                            ->one()) {
                            $tagList[] = $t->id;
                        }
                    }
                    $prayer->select = $tagList;
                    // Save tags and link to prayer
                    $prayer->handleTags();
                }

                // Send admin email to requester
                $prayer->sendAdmin();

                // Send alerts
                foreach ($members as $member) {
                    // Don't send alerts to requestor
                    if ($prayer->group_member_id == $member->id) {
                        continue;
                    }
                    $prayer->toEmail = $member->user->email;
                    $prayer->toName = $member->user->fullName;
                    // Check for unsubscribed members (returns false if unsubscribed)
                    if ($return = $prayer->prepareAlert(GroupAlertQueue::PRAYER_STATUS_NEW)) {
                        $messages[] = $return;
                    }
                    Yii::$app->mailer->sendMultiple($messages);
                }  

            // Mark Request Answered
            } elseif (is_string($subject) && is_string($body) && isset($email->header->in_reply_to) && strpos(strtolower($subject), 'answer') !== false) {

                // Find prayer with matching message_id
                $mid = rtrim(ltrim($email->header->in_reply_to, '<'), '>');
                if ($prayer = Prayer::find()->where(['message_id' => $mid, 'answered' => 0, 'deleted' => 0])->one()) {

                    // Parse email reply
                    $parsed = (new EmailParser());
                    $parsed->setSignatureRegex(self::SIG_REGEX);
                    if ($description = current($parsed->parse($body)->getFragments())->getContent()) {
                    
                        // Mark prayer as answered
                        $prayer->scenario = 'answer';
                        $prayer->answer_description = trim(strip_tags($description));
                        $prayer->answer_date = time();
                        $prayer->answered = 1;
                        $prayer->save();

                        // Send confirmation email
                        $prayer->sendConfirmation('answer');

                        // Send alerts
                        foreach ($members as $member) {
                            // Don't send alerts to requestor
                            if ($prayer->user_id == $member->user_id) {
                                continue;
                            }
                            $prayer->toEmail = $member->user->email;
                            $prayer->toName = $member->user->fullName;
                            // Check for unsubscribed members (returns false if unsubscribed)
                            if ($return = $prayer->prepareAlert(GroupAlertQueue::PRAYER_STATUS_ANSWER)) {
                                $messages[] = $return;
                            }
                            Yii::$app->mailer->sendMultiple($messages);
                        }
                    }
                }

            // Update Request
            } elseif (is_string($subject) && is_string($body) && isset($email->header->in_reply_to)) {

                // Find prayer with matching message-id
                $mid = rtrim(ltrim($email->header->in_reply_to, '<'), '>');
                if ($prayer = Prayer::find()->where(['message_id' => $mid, 'answered' => 0, 'deleted' => 0])->one()) {

                    // Parse email reply and quoted reply text
                    $parsed = (new EmailParser());
                    $parsed->setSignatureRegex(self::SIG_REGEX);
                    if ($description = current($parsed->parse($body)->getFragments())->getContent()) {
                    
                        // Create new update
                        $update = New PrayerUpdate();
                        $update->prayer_id = $prayer->id;
                        $update->update = $description;
                        $update->save();
                                        
                        // Mark prayer as updated
                        $prayer->scenario = 'update';
                        $prayer->save();

                        // Send confirmation email
                        $prayer->sendConfirmation('update');

                        // Send alerts
                        foreach ($members as $member) {
                            // Don't send alerts to requestor
                            if ($prayer->user_id == $member->user_id) {
                                continue;
                            }
                            $prayer->toEmail = $member->user->email;
                            $prayer->toName = $member->user->fullName;
                            Yii::$app->formatter->timeZone = $member->user->timezone;
                            // Check for unsubscribed members (returns false if unsubscribed)
                            if ($return = $prayer->prepareAlert(GroupAlertQueue::PRAYER_STATUS_UPDATE)) {
                                $messages[] = $return;
                            }
                            Yii::$app->mailer->sendMultiple($messages);
                        }                       
                    }
                }
            }

        // End foreach $unreadEmails
        }

        return true;
    }

    /**
     * Send out a summary of the last week's prayer requests
     * @param object $group
     * @return Boolean
     */
    public function sendWeeklyPrayerSummary(Group $group) 
    {
        
        if ($group->prayer_email == NULL || $group->prayer_email_pwd == NULL) {
            return false;
        }

        $members = $group->prayerAlertMembers;
        if ($prayers = $group->prayerWeeklyAlertQueue) {
            foreach ($members as $member) {
          
                // Check subscriptions
                $sub = $member->user->subscription;
                if ($sub->token && $sub->unsubscribe) {
                    continue;
                }

                // Set member timezone
                Yii::$app->formatter->timeZone = $member->user->timezone;

                // Assemble message;
                $messages[] = Yii::$app->mailer->compose(
                        ['html' => 'group/prayerWeeklySummary-html', 'text' => 'group/prayerWeeklySummary-text'], 
                        [
                            'prayers' => $prayers, 
                            'gid' => $group->id, 
                            'to' => $member->user->email, 
                            'token' => $sub->token
                        ]
                    )
                    ->setFrom([Yii::$app->params['email.noReply'] => $group->name])
                    ->setTo([$member->user->email => $member->user->fullName])
                    ->setSubject('Prayer Weekly Summary');
            }
            // Send messages
            Yii::$app->mailer->sendMultiple($messages);
        }
    }

    /**
     * Send new missionary update alerts
     * @param object $group
     * @return Boolean
     */
    public function sendUpdateAlerts() 
    {
        if ($alerts = GroupAlertQueue::getUpdateQueue()) {
            foreach($alerts as $alert) { 

                $update = $alert->missionaryUpdate;

                // Get missionary group memberships where showing updates
                $memberships = $update->user->groupMembersWithUpdates;
                foreach ($memberships as $membership) {
                    $alertMembers[] = $membership->group->updateAlertMembers;
                }

                // Get unique members (don't send multiple alerts to same user)
                $uniqueMembers = [];
                $uids = []; 
                $i = 0;
                foreach($alertMembers as $val1) {
                    foreach ($val1 as $val2) {
                        if (!in_array($val2['user_id'], $uids)) {
                            $uids[$i] = $val2['user_id'];
                            $uniqueMembers[$i] = $val2;
                        }
                        $i++;
                    }
                } 

                foreach ($uniqueMembers as $member) {

                    // Check subscriptions
                    $sub = $member->user->subscription;
                    if ($sub->token && $sub->unsubscribe) {
                        continue;
                    }

                    // Set member timezone
                    Yii::$app->formatter->timeZone = $member->user->timezone;

                    // Assemble message;
                    $group = $member->group;
                    $messages[] = Yii::$app->mailer->compose(
                            ['html' => 'group/updateAlert-html', 'text' => 'group/updateAlert-text'], 
                            [
                                'update' => $update, 
                                'gid' => $group->id, 
                                'to' => $member->user->email, 
                                'token' => $sub->token
                            ]
                        )
                        ->setFrom([Yii::$app->params['email.noReply'] => $group->name])
                        ->setTo([$member->user->email => $member->user->fullName])
                        ->setSubject('Missionary Update Alert');
                }
                Yii::$app->mailer->sendMultiple($messages);
                unset($messages);
                $alert->updateAttributes(['alerted' => 1]);
                $update->updateAttributes(['alert_status' => MissionaryUpdate::ALERT_SENT]);
            }

            // Clear the queue (prayer and update alerts)
            GroupAlertQueue::clearQueue();
        }
    }

    /**
     * Retrieve mail and process new and replied notifications
     * @param object $group
     * @return Boolean
     */
    public function processNotice(Group $group) 
    {
        if ($group->notice_email == NULL || $group->notice_email_pwd == NULL) {
            return false;
        }

        // Get emails
        $imap = new Imap('imap.ionos.com', $group->notice_email, $group->notice_email_pwd, Imap::ENCRYPT_SSL);
        $imap->selectFolder('INBOX');
        try {
            $unreadEmails = $imap->getUnreadMessages();  // false to keep messages unread
        } catch (\Exception $e) {
            echo $e->getMessage() . ' (' . $group->name . ' notices)' . PHP_EOL;
            return false;
        }
        
        foreach ($unreadEmails as $email) {

            // check if email is from a group member
            $from = $email->header->details->from[0];
            $fromAddress = $from->mailbox . '@' . $from->host;
            if (!$user = GroupMember::find()
                ->joinWith('user')
                ->where(['group_member.group_id' => $group->id, 'user.email' => $fromAddress])
                ->one()) {
                continue;
            }
            
            // Get email subject and body
            if (!$subject = $email->header->subject) {
                continue;
            }
            $types = $email->message->types;
            if (in_array('text', $types) || in_array('plain', $types)) {
                $body = $email->message->plain->__get('body');  
            } elseif (in_array('html', $types)) {
                $body = $email->message->html->__get('body');
            }

            $members = $group->memberUsers;
            $messages = [];
            // New notice
            if (isset($body) && is_string($subject) && !isset($email->header->in_reply_to)) {

                // Parse email reply
                $parsed = (new EmailParser());
                $parsed->setSignatureRegex(self::SIG_REGEX);
                $message = current($parsed->parse($body)->getFragments())->getContent();

                // Save Notification
                $notification = new GroupNotification();
                $notification->group_id = $group->id;
                $notification->user_id = $user->user->id;
                $notification->subject = $subject;
                $notification->message = $message;
                $notification->save();               

                // Assemble messages
                foreach ($members as $member) {
                    // Don't send notification to self
                    // if ($notification->user_id == $member->id) {
                    //     continue;
                    // }
                    $notification->toEmail = $member->email;
                    $notification->toName = $member->fullName;
                    // Check for unsubscribed members (returns false if unsubscribed)
                    if ($return = $notification->prepareNotification()) {
                        $messages[] = $return;
                    }
                }
                

            // Notice reply
            } elseif (isset($body) && is_string($subject) && isset($email->header->in_reply_to)) {

                // Find replied to notification
                $mid = rtrim(ltrim($email->header->in_reply_to, '<'), '>');                  
                if ($gnmid = GroupNotificationMessageID::findOne($mid)) {
                    $parent = $gnmid->notification;
                    $parent = $parent->topParent;

                    // Parse email reply
                    $parsed = (new EmailParser());
                    $parsed->setSignatureRegex(self::SIG_REGEX);
                    $message = current($parsed->parse($body)->getFragments())->getContent();

                    // Save reply
                    $reply = new GroupNotification();
                    $reply->group_id = $group->id;
                    $reply->user_id = $user->user->id;
                    $reply->reply_to = $parent->id;
                    $reply->subject = 'Re: ' . $parent->subject;
                    $reply->message = $message;
                    $reply->save();

                    // Assemble messages
                    foreach ($members as $member) {
                        // Don't send message back to replier
                        if ($user->user->email == $member->email) {
                            continue;
                        }
                        $parent->toEmail = $member->email;
                        $parent->toName = $member->fullName;
                        Yii::$app->formatter->timeZone = $member->timezone;
                        // Check for unsubscribed members (returns false if unsubscribed)
                        if ($return = $parent->prepareNotification(true)) {
                            $messages[] = $return;
                        }
                    }
                }
            }

            Yii::$app->mailer->sendMultiple($messages);

        // End foreach
        }
        return true;
    }
}

<?php

namespace backend\models;

use common\models\User;
use common\models\Utility;
use Yii;

/**
 * This is a generic model for processing Mailchimp api requests.
 *
 */
class Mailchimp extends \yii\base\Model
{
    
    /**
     * Build a new Mailchimp mailing list of all users who have email 
     * preferences set to receive notification of new features.
     *
     */
    public function buildFeatureList()
    {
        $client = new \sammaye\mailchimp\Chimp();
        $client->apikey = Yii::$app->params['mc_token'];

        // Delete old list
        $lists = $client->get('/lists');
        foreach ($lists->lists as $list) {
            if ($list->name == Yii::$app->params['mcFeatureListName']) {
                $deleteRes = $client->delete('/lists/' . $list->id);
            }
        }

        // Create new list
        $create = $client->post('/lists', [
            'name' => Yii::$app->params['mcFeatureListName'], 
            'permission_reminder' => 'You are receiving this email because you are registered with IBNet and have elected to recieve new feature updates.  Log into your IBNet account to change your email preferences.', 
            'email_type_option' => true, 
            'contact' => [
                "company" => "Independent Baptist Network",
                "address1" => "Dressogue",
                "city" => "Athboy",
                "state" => "Co. Meath",
                "zip" => "",
                "country" => "Ireland",
                "phone" => "",
            ], 
            'campaign_defaults' => [
                "from_name" => "IBNet",
                "from_email" => "admin@ibnet.org",
                "subject" => "See what's new at IBNet",
                "language" => "en",
            ]
        ]);
        $lists = $client->get('/lists');
        foreach ($lists->lists as $list) {
            if ($list->name == Yii::$app->params['mcFeatureListName']) {
                $listId = $list->id;
            }
        }

        // Populate list
        $users = User::find()->where('email != ""')->andWhere(['status' => User::STATUS_ACTIVE, 'emailPrefFeatures' => 1])->all();
        foreach ($users as $user) {
            $res = $client->post('/lists/' . $listId . '/members', [
                'email_address' => $user->email,
                'merge_fields' => ['FNAME' => $user->first_name, 'LNAME' => $user->last_name],
                'status' => 'subscribed',
            ]);
        }

        Yii::$app->session->setFlash('success', 'A new Mailchimp feature mailing list has been created.');

        return true;
    }

}

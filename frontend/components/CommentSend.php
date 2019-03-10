<?php
namespace frontend\components;

use common\models\Utility;
use common\models\profile\ProfileMail;
use yii\db\ActiveRecord;

class CommentSend extends \yii\base\Behavior
{
	public function events()
	{
		return [
			\yii\db\ActiveRecord::EVENT_AFTER_INSERT => 'send',
		];
	}

	public function send($event)
	{	
		if ($event->sender->created_by != $_GET['id']) {					// Only send notification if user is not commenting on own profile
			ProfileMail::sendComment($_GET['id'], $event->sender->created_by);
		}
	}
}
?>
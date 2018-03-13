<?php

namespace common\models\missionary;

use Yii;

/**
 * This is a generic model for capturing the select2 input on Mailchimp setup
 *
 * @property array $select
 */
class MailchimpList extends \yii\base\Model
{
    public $select;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['select'], 'required'],
            [['select'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'select' => 'Mailing Lists',
        ];
    }
}

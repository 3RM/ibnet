<?php

use yii\db\Migration;

/**
 * Class m190327_234414_create_group_tables
 */
class m190327_234414_create_group_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        /**
         * Table group
         **/
        $this->createTable('{{%group}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNULL(),
            'transfer_token' => $this->integer(),
            'reviewed' => $this->tinyInteger(1),
            'url_name' => $this->string(60)->notNULL(),
            'created_at' => $this->integer()->notNULL(),
            'status' => $this->smallInteger(6),
            'last_visit' => $this->integer(),
            'name' => $this->string(60),
            'description' => $this->text(),
            'image' => $this->string(),
            'permit_user' => $this->tinyInteger(1),
            'private' => $this->tinyInteger(1),
            'hide_on_profiles' => $this->tinyInteger(1),
            'not_searchable' => $this->smallInteger(6),
            'group_level' => $this->smallInteger(6),
            'ministry_id' => $this->integer(),
            'discourse_group_name' => $this->string(),
            'discourse_group_id' => $this->integer(),
            'discourse_category_id' => $this->integer(),
            'discourse_category_header_color' => $this->string(12),
            'discourse_category_title_color' => $this->string(12),
            'feature_prayer' => $this->tinyInteger(1),
            'feature_calendar' => $this->tinyInteger(1),
            'feature_notification' => $this->tinyInteger(1),
            'feature_document' => $this->tinyInteger(1),
            'feature_forum' => $this->tinyInteger(1),
            'feature_update' => $this->tinyInteger(1),
            'feature_donation' => $this->tinyInteger(1),
            'prayer_email' => $this->string(),
            'prayer_email_pwd' => $this->string(20),
            'notice_email' => $this->string(),
            'notice_email_pwd' => $this->string(20),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'group',
            'ministry_id',
            'FOREIGN KEY (ministry_id) REFERENCES  profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-group-ministry_id',
            'group',
            'ministry_id',
            'profile',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table group_place
         **/
        $this->createTable('{{%group_place}}', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer()->notNULL(),
            'city' => $this->string()->notNULL(),
            'state' => $this->string()->notNULL(),
            'country' => $this->string()->notNULL(),
            'deleted' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'group_place',
            'group_id',
            'FOREIGN KEY (group_id) REFERENCES group (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_place-group_id',
            'group_place',
            'group_id',
            'group',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table group_keyword
         **/
        $this->createTable('{{%group_keyword}}', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer()->notNULL(),
            'keyword' => $this->string(12)->notNULL(),
            'deleted' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'group_keyword',
            'group_id',
            'FOREIGN KEY (group_id) REFERENCES group (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_keyword-group_id',
            'group_keyword',
            'group_id',
            'group',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table group_member
         **/
        $this->createTable('{{%group_member}}', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer()->notNULL(),
            'user_id' => $this->integer(),
            'profile_id' => $this->integer(),
            'missionary_id' => $this->integer(),
            'group_owner' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer(),
            'status' => $this->smallInteger(6)->defaultValue(0),
            'approval_date' => $this->integer(),
            'inactivate_date' => $this->integer(),
            'show_updates' => $this->tinyInteger(1)->defaultValue(0),
            'email_prayer_alert' => $this->tinyInteger(1),
            'email_prayer_summary' => $this->tinyInteger(1),
            'email_update_alert' => $this->tinyInteger(1),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'group_member',
            'group_id',
            'FOREIGN KEY (group_id) REFERENCES group (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'group_member',
            'user_id',
            'FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'group_member',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'group_member',
            'missionary_id',
            'FOREIGN KEY (missionary_id) REFERENCES missionary (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_member-group_id',
            'group_member',
            'group_id',
            'group',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_member-user_id',
            'group_member',
            'user_id',
            'user',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_member-profile_id',
            'group_member',
            'profile_id',
            'profile',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_member-missionary_id',
            'group_member',
            'missionary_id',
            'missionary',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->createIndex(
            'idx-group_member-group_id',
            'group_member',
            'group_id'
        );

        $this->createIndex(
            'idx-group_member-user_id',
            'group_member',
            'user_id'
        );

        /**
         * Table group_invite
         **/
        $this->createTable('{{%group_invite}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string(60)->notNULL(),
            'created_at' => $this->integer()->notNULL(),
            'group_id' => $this->integer()->notNULL(),
            'token' => $this->string()->notNULL(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'group_invite',
            'group_id',
            'FOREIGN KEY (group_id) REFERENCES group (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_invite-group_id',
            'group_invite',
            'group_id',
            'group',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table prayer
         **/
        $this->createTable('{{%prayer}}', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer()->notNULL(),
            'group_member_id' => $this->integer()->notNULL(),
            'created_at' => $this->integer(),
            'last_update' => $this->integer(),
            'request' => $this->string()->notNULL(),
            'description' => $this->text(),
            'duration' => $this->smallInteger(6),
            'answered' => $this->tinyInteger(1)->defaultValue(0),
            'answer_description' => $this->text(),
            'answer_date' => $this->integer(),
            'message_id' => $this->string(),
            'deleted' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'prayer',
            'group_id',
            'FOREIGN KEY (group_id) REFERENCES group (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'prayer',
            'group_member_id',
            'FOREIGN KEY (group_member_id) REFERENCES group_member (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-prayer-group_id',
            'prayer',
            'group_id',
            'group',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-prayer-group_member_id',
            'prayer',
            'group_member_id',
            'group_member',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table prayer_update
         **/
        $this->createTable('{{%prayer_update}}', [
            'id' => $this->primaryKey(),
            'prayer_id' => $this->integer()->notNULL(),
            'created_at' => $this->integer(),
            'update' => $this->text(),
            'deleted' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'prayer_update',
            'prayer_id',
            'FOREIGN KEY (prayer_id) REFERENCES prayer (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-prayer_update-prayer_id',
            'prayer_update',
            'prayer_id',
            'prayer',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table prayer_tag
         **/
        $this->createTable('{{%prayer_tag}}', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer()->notNULL(),
            'tag' => $this->string()->notNULL(),
            'deleted' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'prayer_tag',
            'group_id',
            'FOREIGN KEY (group_id) REFERENCES group (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-prayer_tag-group_id',
            'prayer_tag',
            'group_id',
            'group',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table prayer_has_prayer_tag
         **/
        $this->createTable('{{%prayer_has_prayer_tag}}', [
            'prayer_id' => $this->integer()->notNull(),
            'prayer_tag_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'prayer_has_prayer_tag',
            'prayer_id',
            'FOREIGN KEY (prayer_id) REFERENCES prayer (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'prayer_has_prayer_tag',
            'prayer_tag_id',
            'FOREIGN KEY (prayer_tag_id) REFERENCES prayer_tag (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addPrimaryKey(
            'pk-prayer_has_prayer_tag',
            'prayer_has_prayer_tag',
            [
                'prayer_id',
                'prayer_tag_id',
            ]
        );


        /**
         * Table group_calendar_event
         **/
        $this->createTable('{{%group_calendar_event}}', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer()->notNULL(),
            'group_member_id' => $this->integer()->notNULL(),
            'created_at' => $this->integer(),
            'title' => $this->string(60),
            'color' => $this->string(20)->defaultValue('#3d85c6'),
            'description' => $this->text(),
            'start' => $this->timeStamp(),
            'end' => $this->timeStamp(),
            'all_day' => $this->boolean(),
            'deleted' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'group_calendar_event',
            'group_id',
            'FOREIGN KEY (group_id) REFERENCES group (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'group_calendar_event',
            'group_member_id',
            'FOREIGN KEY (group_member_id) REFERENCES group_member (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_calendar_event-group_id',
            'group_calendar_event',
            'group_id',
            'group',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_calendar_event-group_member_id',
            'group_calendar_event',
            'group_member_id',
            'group_member',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table group_icalendar_url
         **/
        $this->createTable('{{%group_icalendar_url}}', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer()->notNULL(),
            'group_member_id' => $this->integer()->notNULL(),
            'ical_id' => $this->integer(),
            'url' => $this->string()->notNULL(),
            'color' => $this->string(20)->defaultValue('#ff4f00'),
            'error_on_import' => $this->tinyInteger(1)->defaultValue(0),
            'deleted' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'group_icalendar_url',
            'group_id',
            'FOREIGN KEY (group_id) REFERENCES group (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'group_icalendar_url',
            'group_member_id',
            'FOREIGN KEY (group_member_id) REFERENCES group_member (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'group_icalendar_url',
            'ical_id',
            'FOREIGN KEY (ical_id) REFERENCES icalender_main (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_icalendar_url-group_id',
            'group_icalendar_url',
            'group_id',
            'group',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_icalendar_url-group_member_id',
            'group_icalendar_url',
            'group_member_id',
            'group_member',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_icalendar_url-ical_id',
            'group_icalendar_url',
            'ical_id',
            'icalender_main',
            'id',
            'NO ACTION',
            'NO ACTION'
        );



        /**
         * Table group_notification
         **/
        $this->createTable('{{%group_notification}}', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer(),
            'user_id' => $this->integer(),
            'created_at' => $this->integer(),
            'reply_to' => $this->integer(),
            'subject' => $this->string(),
            'message' => $this->text(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'group_notification',
            'group_id',
            'FOREIGN KEY (group_id) REFERENCES group (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'group_notification',
            'user_id',
            'FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'group_notification',
            'reply_to',
            'FOREIGN KEY (reply_to) REFERENCES group_notification (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_notification-group_id',
            'group_notification',
            'group_id',
            'group',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_notification-user_id',
            'group_notification',
            'user_id',
            'user',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_notification-reply_to',
            'group_notification',
            'reply_to',
            'group_notification',
            'id',
            'NO ACTION',
            'NO ACTION'
        );



        /**
         * Table group_notification_message_id
         **/
        $this->createTable('{{%group_notification_message_id}}', [
            'notification_id' => $this->integer(),
            'message_id' => $this->string()->notNULL(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'group_notification_message_id',
            'notification_id',
            'FOREIGN KEY (notification_id) REFERENCES group_notification (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_notification_message_id-notification_id',
            'group_notification_message_id',
            'notification_id',
            'group_notification',
            'id',
            'NO ACTION',
            'NO ACTION'
        );



        /**
         * Table group_alert_queue
         **/
        $this->createTable('{{%group_alert_queue}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer(),
            'group_id' => $this->integer(),
            'prayer_id' => $this->integer(),
            'update_id' => $this->integer(),
            'prayer_status' => $this->smallInteger(6),
            'alerted' => $this->tinyInteger(1),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'group_alert_queue',
            'group_id',
            'FOREIGN KEY (group_id) REFERENCES group (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'group_alert_queue',
            'prayer_id',
            'FOREIGN KEY (prayer_id) REFERENCES prayer (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'group_alert_queue',
            'update_id',
            'FOREIGN KEY (update_id) REFERENCES missionary_update (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_alert_queue-group_id',
            'group_alert_queue',
            'group_id',
            'group',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_alert_queue-prayer_id',
            'group_alert_queue',
            'prayer_id',
            'prayer',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-group_alert_queue-update_id',
            'group_alert_queue',
            'update_id',
            'missionary_update',
            'id',
            'NO ACTION',
            'NO ACTION'
        );
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_234414_create_group_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_234414_create_group_tables cannot be reverted.\n";

        return false;
    }
    */
}

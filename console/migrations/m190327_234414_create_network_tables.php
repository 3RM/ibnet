<?php

use yii\db\Migration;

/**
 * Class m190327_234414_create_network_tables
 */
class m190327_234414_create_network_tables extends Migration
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
         * Table network
         **/
        $this->createTable('{{%network}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNULL(),
            'transfer_token' => $this->integer(),
            'reviewed' => $this->tinyInteger(1),
            'url_name' => $this->string(60)->notNULL(),
            'created_at' => $this->integer()->notNULL(),
            'status' => $this->smallInteger(6),
            'name' => $this->string(60),
            'description' => $this->text(),
            'network_image' => $this->string(),
            'permit_user' => $this->tinyInteger(1),
            'private' => $this->tinyInteger(1),
            'hide_on_profiles' => $this->tinyInteger(1),
            'not_searchable' => $this->smallInteger(6),
            'network_level' => $this->smallInteger(6),
            // 'network_level' => $this->ENUM ('Local', 'Regional', 'State/\Province', 'National', 'International'),
            'ministry_id' => $this->integer(),
            'feature_prayer' => $this->tinyInteger(1),
            'feature_calendar' => $this->tinyInteger(1),
            'feature_notification' => $this->tinyInteger(1),
            'feature_document' => $this->tinyInteger(1),
            'feature_chat' => $this->tinyInteger(1),
            'feature_forum' => $this->tinyInteger(1),
            'feature_update' => $this->tinyInteger(1),
            'feature_donation' => $this->tinyInteger(1),
            'prayer_email' => $this->string(),
            'prayer_email_pwd' => $this->string(20),
            'notice_email' => $this->string(),
            'notice_email_pwd' => $this->string(20),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'network',
            'ministry_id',
            'FOREIGN KEY (ministry_id) REFERENCES  profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-network-ministry_id',
            'network',
            'ministry_id',
            'profile',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table network_place
         **/
        $this->createTable('{{%network_place}}', [
            'id' => $this->primaryKey(),
            'network_id' => $this->integer()->notNULL(),
            'city' => $this->string()->notNULL(),
            'state' => $this->string()->notNULL(),
            'country' => $this->string()->notNULL(),
            'deleted' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'network_place',
            'network_id',
            'FOREIGN KEY (network_id) REFERENCES network (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-network_place-network_id',
            'network_place',
            'network_id',
            'network',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table network_keyword
         **/
        $this->createTable('{{%network_keyword}}', [
            'id' => $this->primaryKey(),
            'network_id' => $this->integer()->notNULL(),
            'keyword' => $this->string(12)->notNULL(),
            'deleted' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'network_keyword',
            'network_id',
            'FOREIGN KEY (network_id) REFERENCES network (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-network_keyword-network_id',
            'network_keyword',
            'network_id',
            'network',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table network_member
         **/
        $this->createTable('{{%network_member}}', [
            'id' => $this->primaryKey(),
            'network_id' => $this->integer()->notNULL(),
            'user_id' => $this->integer(),
            'profile_id' => $this->integer(),
            'missionary_id' => $this->integer(),
            'created_at' => $this->integer(),
            'left_network' => $this->tinyInteger(1),
            'email_prayer_alert' => $this->tinyInteger(1),
            'email_prayer_summary' => $this->tinyInteger(1),
            'email_update_alert' => $this->tinyInteger(1),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'network_member',
            'network_id',
            'FOREIGN KEY (network_id) REFERENCES network (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'network_member',
            'user_id',
            'FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'network_member',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'network_member',
            'missionary_id',
            'FOREIGN KEY (missionary_id) REFERENCES missionary (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-network_member-network_id',
            'network_member',
            'network_id',
            'network',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-network_member-user_id',
            'network_member',
            'user_id',
            'user',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-network_member-profile_id',
            'network_member',
            'profile_id',
            'profile',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-network_member-missionary_id',
            'network_member',
            'missionary_id',
            'missionary',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table prayer
         **/
        $this->createTable('{{%prayer}}', [
            'id' => $this->primaryKey(),
            'network_id' => $this->integer()->notNULL(),
            'network_member_id' => $this->integer()->notNULL(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
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
            'network_id',
            'FOREIGN KEY (network_id) REFERENCES network (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'prayer',
            'network_member_id',
            'FOREIGN KEY (network_member_id) REFERENCES network_member (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-prayer-network_id',
            'prayer',
            'network_id',
            'network',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-prayer-network_member_id',
            'prayer',
            'network_member_id',
            'network_member',
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
            'network_id' => $this->integer()->notNULL(),
            'tag' => $this->string()->notNULL(),
            'deleted' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'prayer_tag',
            'network_id',
            'FOREIGN KEY (network_id) REFERENCES network (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-prayer_tag-network_id',
            'prayer_tag',
            'network_id',
            'network',
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
         * Table network_calendar_event
         **/
        $this->createTable('{{%network_calendar_event}}', [
            'id' => $this->primaryKey(),
            'network_id' => $this->integer()->notNULL(),
            'network_member_id' => $this->integer()->notNULL(),
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
            'network_calendar_event',
            'network_id',
            'FOREIGN KEY (network_id) REFERENCES network (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'network_calendar_event',
            'network_member_id',
            'FOREIGN KEY (network_member_id) REFERENCES network_member (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-network_calendar_event-network_id',
            'network_calendar_event',
            'network_id',
            'network',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-network_calendar_event-network_member_id',
            'network_calendar_event',
            'network_member_id',
            'network_member',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table network_icalendar_url
         **/
        $this->createTable('{{%network_icalendar_url}}', [
            'id' => $this->primaryKey(),
            'network_id' => $this->integer()->notNULL(),
            'network_member_id' => $this->integer()->notNULL(),
            'ical_id' => $this->integer(),
            'url' => $this->string()->notNULL(),
            'color' => $this->string(20)->defaultValue('#ff4f00'),
            'error_on_import' => $this->tinyInteger(1)->defaultValue(0),
            'deleted' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'network_icalendar_url',
            'network_id',
            'FOREIGN KEY (network_id) REFERENCES network (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'network_icalendar_url',
            'network_member_id',
            'FOREIGN KEY (network_member_id) REFERENCES network_member (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'network_icalendar_url',
            'ical_id',
            'FOREIGN KEY (ical_id) REFERENCES icalender_main (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-network_icalendar_url-network_id',
            'network_icalendar_url',
            'network_id',
            'network',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-network_icalendar_url-network_member_id',
            'network_icalendar_url',
            'network_member_id',
            'network_member',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-network_icalendar_url-ical_id',
            'network_icalendar_url',
            'ical_id',
            'icalender_main',
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
        echo "m190327_234414_create_network_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_234414_create_network_tables cannot be reverted.\n";

        return false;
    }
    */
}

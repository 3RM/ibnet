<?php

use yii\db\Migration;

/**
 * Class m190311_181235_create_profile_tables
 */
class m190311_132910_create_profile_tables extends Migration
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
         * Table profile
         **/
        $this->createTable('{{%profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNULL(),
            'transfer_token' => $this->integer(),
            'reviewed' => $this->tinyInteger(1),
            'type' => $this->string(20)->notNull(),
            'sub_type' => $this->string(20),
            'category' => $this->smallInteger(6),
            'profile_name' => $this->string(60)->notNull(),
            'url_name' => $this->string(60),
            'url_loc' => $this->string(60),
            'created_at' => $this->dateTime(),
            'created' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'updated' => $this->integer(),
            'last_update' => $this->date(),
            'renewal_date' => $this->date(),
            'inactivation_date' => $this->date(),
            'has_been_inactivated' => $this->tinyInteger(1),
            'status' => $this->smallInteger(6),
            'edit' => $this->smallInteger(6),
            'tagline' => $this->string(60),
            'title' => $this->string(60),
            'description' => $this->text(),
            'ministry_of' => $this->integer(),
            'home_church' => $this->integer(),
            'image1' => $this->string(),
            'image2' => $this->string(),
            'flwsp_ass_level' => $this->smallInteger(6),
            'org_name' => $this->string(60),
            'org_address1' => $this->string(60),
            'org_address2' => $this->string(60),
            'org_po_box' => $this->string(4),
            'org_city' => $this->string(60),
            'org_st_prov_reg' => $this->string(50),
            'org_state_long' => $this->string(50),
            'org_zip' => $this->string(20),
            'org_country' => $this->string(60),
            'org_loc' => $this->string(40),
            'org_po_address1' => $this->string(60),
            'org_po_address2' => $this->string(60),
            'org_po_city' => $this->string(60),
            'org_po_st_prov_reg' => $this->string(50),
            'org_po_state_long' => $this->string(50),
            'org_po_zip' => $this->string(20),
            'org_po_country' => $this->string(60),
            'ind_first_name' => $this->string(20),
            'ind_last_name' => $this->string(40),
            'spouse_first_name' => $this->string(20),
            'ind_address1' => $this->string(60),
            'ind_address2' => $this->string(60),
            'ind_po_box' => $this->string(4),
            'ind_city' => $this->string(60),
            'ind_st_prov_reg' => $this->string(50),
            'ind_state_long' => $this->string(50),
            'ind_zip' => $this->string(20),
            'ind_country' => $this->string(60),
            'ind_loc' => $this->string(40),
            'ind_po_address1' => $this->string(60),
            'ind_po_address2' => $this->string(60),
            'ind_po_city' => $this->string(60),
            'ind_po_st_prov_reg' => $this->string(50),
            'ind_po_state_long' => $this->string(50),
            'ind_po_zip' => $this->string(20),
            'ind_po_country' => $this->string(60),
            'show_map' => $this->tinyInteger(1),
            'phone' => $this->string(20),
            'email' => $this->string(60),
            'email_pvt' => $this->string(60),
            'email_pvt_status' => $this->smallInteger(6),
            'website' => $this->string(),
            'pastor_interim' => $this->tinyInteger(1),
            'cp_pastor' => $this->tinyInteger(1),
            'bible' => $this->string(60),
            'worship_style' => $this->string(60),
            'polity' => $this->string(60),
            'packet' => $this->string(),
            'inappropriate' => $this->tinyInteger(1),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'profile',
            'user_id',
            'FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'profile',
            'last_update',
            'User update that qualifies the profile for renewal'
        );


        /**
         * Table forms_completed
         **/
        $this->createTable('{{%forms_completed}}', [
            'id' => $this->integer(),
            'form_array' => $this->string(),
        ], $tableOptions);

        $this->addPrimaryKey(
            'pk-forms_completed',
            'forms_completed',
            'id'
        );


        /**
         * Table type
         **/
        $this->createTable('{{%type}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(40)->notNull(),
            'group' => $this->string(20)->notNull(),
            'staff' => $this->tinyInteger(1)->notNull(),
            'active' => $this->tinyInteger(1)->notNull(),
        ], $tableOptions);


        /**
         * Table sub_type
         **/
        $this->createTable('{{%sub_type}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(40)->notNull(),
            'sub_type' => $this->string(40)->notNull(),
        ], $tableOptions);


        /**
         * Table profile_tracking
         **/
        $this->createTable('{{%profile_tracking}}', [
            'id' => $this->primaryKey(),
            'date' => $this->timeStamp()->notNull(),
            'users' => $this->integer(),
            'type_array' => $this->string(),
            'sub_type_array' => $this->string(),
            'expired' => $this->integer(),
        ], $tableOptions);


        /**
         * Table state
         **/
        $this->createTable('{{%state}}', [
            'state' => $this->string(15)->notNull(),
            'abbreviation' => $this->string(2),
            'long' => $this->string(15)->notNull(),
        ], $tableOptions);


        /**
         * Table country
         **/
        $this->createTable('{{%country}}', [
            'id' => $this->primaryKey(),
            'iso' => $this->string(2)->notNull(),
            'name' => $this->string(80)->notNull(),
            'printable_name' => $this->string(80)->notNull(),
            'iso3' => $this->string(3),
            'numcode' => $this->smallInteger(),
            'ran' => $this->tinyInteger(1),
        ], $tableOptions);

        $this->createIndex(
            'idx-country-printable_name',
            'country',
            'printable_name'
        );


        /**
         * Table staff
         **/
        $this->createTable('{{%staff}}', [
            'id' => $this->primaryKey(),
            'staff_id' => $this->integer(),
            'staff_type' => $this->string(40)->notNull(),
            'staff_title' => $this->string(60),
            'ministry_id' => $this->integer(),
            'home_church' => $this->tinyInteger(1),
            'church_pastor' => $this->tinyInteger(1),
            'ministry_of' => $this->tinyInteger(1),
            'ministry_other' => $this->tinyInteger(1),
            'sr_pastor' => $this->tinyInteger(1),  
            'confirmed' => $this->tinyInteger(1),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'staff',
            'staff_id',
            'FOREIGN KEY (staff_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'staff',
            'ministry_id',
            'FOREIGN KEY (ministry_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->createIndex(
            'idx-staff-staff_id',
            'staff',
            'staff_id'
        );
        
        $this->createIndex(
            'idx-ministry_id',
            'staff',
            'ministry_id'
        );


        /**
         * Table service_time
         **/
        $this->createTable('{{%service_time}}', [
            'id' => $this->primaryKey(),
            'profile_id' => $this->integer()->notNull(),
            'day_1' => $this->smallInteger()->notNull(),
            'time_1' => $this->string(8)->notNull(),
            'description_1' => $this->string(30)->notNull(),
            'day_2' => $this->smallInteger(),
            'time_2' => $this->string(8),
            'description_2' => $this->string(30),
            'day_3' => $this->smallInteger(),
            'time_3' => $this->string(8),
            'description_3' => $this->string(30),
            'day_4' =>$this->smallInteger(),
            'time_4' => $this->string(8),
            'description_4' => $this->string(30),
            'day_5' => $this->smallInteger(),
            'time_5' => $this->string(8),
            'description_5' => $this->string(30),
            'day_6' => $this->smallInteger(),
            'time_6' => $this->string(8),
            'description_6' => $this->string(30),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'service_time',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        /**
         * Table social
         **/
        $this->createTable('{{%social}}', [
            'id' => $this->primaryKey(),
            'profile_id' => $this->integer()->notNull(),
            'facebook' => $this->string(),
            'instagram' => $this->string(),
            'flickr' => $this->string(),
            'linkedin' => $this->string(),
            'pinterest' => $this->string(),
            'rss' => $this->string(),
            'sermonaudio' => $this->string(),
            'soundcloud' => $this->string(),
            'tumblr' => $this->string(),
            'twitter' => $this->string(),
            'vimeo' => $this->string(),
            'youtube' => $this->string(),
            'reviewed' => $this->tinyInteger(1),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'social',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        /**
         * Table tag
         **/
        $this->createTable('{{%tag}}', [
            'id' => $this->primaryKey(),
            'tag' => $this->string(),
            'syn' => $this->string(),
        ], $tableOptions);

        $this->createIndex(
            'idx-tag-tag',
            'tag',
            'tag'
        );


        /**
         * Table profile_has_tag
         **/
        $this->createTable('{{%profile_has_tag}}', [
            'profile_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'profile_has_tag',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'profile_has_tag',
            'tag_id',
            'FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addPrimaryKey(
            'pk-profile_has_tag',
            'profile_has_tag',
            [
                'profile_id',
                'tag_id',
            ]
        );


        /**
         * Table profile_has_program
         **/
        $this->createTable('{{%profile_has_program}}', [
            'profile_id' => $this->integer()->notNull(),
            'program_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'profile_has_program',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'profile_has_program',
            'program_id',
            'FOREIGN KEY (program_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addPrimaryKey(
            'pk-profile_has_program',
            'profile_has_program',
            [
                'profile_id',
                'program_id',
            ]
        );


        /**
         * Table profile_has_like
         **/
        $this->createTable('{{%profile_has_like}}', [
            'profile_id' => $this->integer()->notNull(),
            'liked_by_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'profile_has_like',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'profile_has_like',
            'liked_by_id',
            'FOREIGN KEY (liked_by_id) REFERENCES user (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addPrimaryKey(
            'pk-profile_has_like',
            'profile_has_like',
            [
                'profile_id',
                'liked_by_id',
            ]
        );


        /**
         * Table mission_agcy
         **/
        $this->createTable('{{mission_agcy}}', [
            'id' => $this->primaryKey(),
            'mission' => $this->string(60)->notNull(),
            'mission_acronym' => $this->string(20),
            'profile_id' => $this->integer(),
            'status' =>$this->smallInteger()->defaultValue(10),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'mission_agcy',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-mission_agcy-profile_id',
            'mission_agcy',
            'profile_id',
            'profile',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table profile_has_mission_agcy
         **/
        $this->createTable('{{%profile_has_mission_agcy}}', [
            'profile_id' => $this->integer()->notNull(),
            'mission_agcy_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'profile_has_mission_agcy',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'profile_has_mission_agcy',
            'mission_agcy_id',
            'FOREIGN KEY (mission_agcy_id) REFERENCES mission_agcy (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addPrimaryKey(
            'pk-profile_has_mission_agcy',
            'profile_has_mission_agcy',
            [
                'profile_id',
                'mission_agcy_id',
            ]
        );


        /**
         * Table missionary
         **/
        $this->createTable('{{%missionary}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'profile_id' => $this->integer()->notNULL(),
            'field' => $this->string(80),
            'status' => $this->string(60),
            'mission_agcy_id' => $this->integer(),
            'packet' => $this->string(),
            'cp_pastor_at' => $this->integer(),
            'repository_key' => $this->integer(60),
            'mc_token' => $this->string(),
            'mc_key' => $this->string(12),
            'viewed_update' => $this->tinyInteger(1),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'missionary',
            'user_id',
            'FOREIGN KEY (user_id) REFERENCES  user (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'missionary',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'missionary',
            'mission_agcy_id',
            'FOREIGN KEY (mission_agcy_id) REFERENCES mission_agcy (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'missionary',
            'cp_pastor_at',
            'FOREIGN KEY (cp_pastor_at) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->createIndex(
            'idx-missionary-mission_agcy_id',
            'missionary',
            'mission_agcy_id'
        );

        $this->addForeignKey(
            'fk-missionary-user_id',
            'missionary',
            'user_id',
            'user',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-missionary-profile_id',
            'missionary',
            'profile_id',
            'profile',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-missionary-mission_agcy_id',
            'missionary',
            'mission_agcy_id',
            'mission_agcy',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-missionary-cp_pastor_at',
            'missionary',
            'cp_pastor_at',
            'profile',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table missionary_update
         **/
        $this->createTable('{{%missionary_update}}', [
            'id' => $this->primaryKey(),
            'missionary_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'title' => $this->string(60)->notNull(),
            'mailchimp_url' => $this->string(),
            'pdf' => $this->string(),
            'youtube_url' => $this->string(),
            'vimeo_url' => $this->string(),
            'drive_url' => $this->string(),
            'thumbnail' => $this->string(),
            'description' => $this->text(),
            'from_date' => $this->date()->notNull(),
            'to_date' => $this->date()->notNull(),
            'visible' => $this->tinyInteger(1)->defaultValue(0),
            'deleted' => $this->tinyInteger(1)->defaultValue(0),
            'vid_not_accessible' => $this->tinyInteger(1)->defaultValue(0),
            'profile_inactive' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'missionary_update',
            'missionary_id',
            'FOREIGN KEY (missionary_id) REFERENCES  missionary (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-missionary_update-missionary_id',
            'missionary_update',
            'missionary_id',
            'missionary',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table miss_housing
         **/
        $this->createTable('{{miss_housing}}', [
            'id' => $this->primaryKey(),
            'profile_id' => $this->integer()->notNull(),
            'description' => $this->text()->notNull(),
            'contact' => $this->string(60),
            'trailer' => $this->tinyInteger(1),
            'water' => $this->tinyInteger(1),
            'electric' => $this->tinyInteger(1),
            'sewage' => $this->tinyInteger(1),
            'status' => $this->smallInteger(6)->defaultValue(10),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'miss_housing',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-miss_housing-profile_id',
            'miss_housing',
            'profile_id',
            'profile',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table association
         **/
        $this->createTable('{{association}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(60),
            'acronym' => $this->string(20),
            'profile_id' => $this->integer(),
            'status' =>$this->smallInteger()->defaultValue(10),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'association',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-association-profile_id',
            'association',
            'profile_id',
            'profile',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table profile_has_association
         **/
        $this->createTable('{{%profile_has_association}}', [
            'profile_id' => $this->integer()->notNull(),
            'ass_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'profile_has_association',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'profile_has_association',
            'ass_id',
            'FOREIGN KEY (ass_id) REFERENCES association (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addPrimaryKey(
            'pk-profile_has_association',
            'profile_has_association',
            [
                'profile_id',
                'ass_id',
            ]
        );


        /**
         * Table fellowship
         **/
        $this->createTable('{{fellowship}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(60),
            'acronym' => $this->string(20),
            'profile_id' => $this->integer(),
            'status' =>$this->smallInteger()->defaultValue(10),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'fellowship',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-fellowship-profile_id',
            'fellowship',
            'profile_id',
            'profile',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table profile_has_fellowship
         **/
        $this->createTable('{{%profile_has_fellowship}}', [
            'profile_id' => $this->integer()->notNull(),
            'flwship_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'profile_has_fellowship',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'profile_has_fellowship',
            'flwship_id',
            'FOREIGN KEY (flwship_id) REFERENCES fellowship (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addPrimaryKey(
            'pk-profile_has_fellowship',
            'profile_has_fellowship',
            [
                'profile_id',
                'flwship_id',
            ]
        );


        /**
         * Table school
         **/
        $this->createTable('{{school}}', [
            'id' => $this->primaryKey(),
            'school' => $this->string(60),
            'school_acronym' => $this->string(20),
            'city' => $this->string(60),
            'st_prov_reg' => $this->string(20),
            'country' => $this->string(80)->notNull(),
            'ib' => $this->tinyInteger(1),
            'closed' => $this->tinyInteger(1),
            'profile_id' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'school',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-school-profile_id',
            'school',
            'profile_id',
            'profile',
            'id',
            'NO ACTION',
            'NO ACTION'
        );


        /**
         * Table profile_has_school
         **/
        $this->createTable('{{%profile_has_school}}', [
            'profile_id' => $this->integer()->notNull(),
            'school_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'profile_has_school',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'profile_has_school',
            'school_id',
            'FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addPrimaryKey(
            'pk-profile_has_school',
            'profile_has_school',
            [
                'profile_id',
                'school_id',
            ]
        );


        /**
         * Table school_level
         **/
        $this->createTable('{{school_level}}', [
            'id' => $this->primaryKey(),
            'school_level' => $this->string(40)->notNull(),
            'level_group' => $this->string(40)->notNull(),
        ], $tableOptions);


        /**
         * Table profile_has_school_level
         **/
        $this->createTable('{{%profile_has_school_level}}', [
            'profile_id' => $this->integer()->notNull(),
            'school_level_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'profile_has_school_level',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'profile_has_school_level',
            'school_level_id',
            'FOREIGN KEY (school_level_id) REFERENCES school_level (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addPrimaryKey(
            'pk-profile_has_school_level',
            'profile_has_school_level',
            [
                'profile_id',
                'school_level_id',
            ]
        );


        /**
         * Table accreditation
         **/
        $this->createTable('{{accreditation}}', [
            'id' => $this->primaryKey(),
            'association' => $this->string(),
            'acronym' => $this->string(20),
            'website' => $this->string(),
            'level' => $this->string(60),
            'classification' => $this->string(60),
        ], $tableOptions);


        /**
         * Table profile_has_accreditation
         **/
        $this->createTable('{{%profile_has_accreditation}}', [
            'profile_id' => $this->integer()->notNull(),
            'accreditation_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'profile_has_accreditation',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addCommentOnColumn(
            'profile_has_accreditation',
            'accreditation_id',
            'FOREIGN KEY (accreditation_id) REFERENCES accreditation (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addPrimaryKey(
            'pk-profile_has_accreditation',
            'profile_has_accreditation',
            [
                'profile_id',
                'accreditation_id',
            ]
        );


        /**
         * Table bible
         **/
        $this->createTable('{{bible}}', [
            'id' => $this->primaryKey(),
            'bible' => $this->string(60)->notNull(),
        ], $tableOptions);


        /**
         * Table worship_style
         **/
        $this->createTable('{{worship_style}}', [
            'id' => $this->primaryKey(),
            'style' => $this->string(60)->notNull(),
        ], $tableOptions);


        /**
         * Table polity
         **/
        $this->createTable('{{polity}}', [
            'id' => $this->primaryKey(),
            'polity' => $this->string(60)->notNull(),
        ], $tableOptions);


        /**
         * Table mail
         **/
        $this->createTable('{{mail}}', [
            'id' => $this->primaryKey(),
            'linking_profile' => $this->integer(),
            'profile' => $this->integer(),
            'profile_owner' => $this->integer(),
            'l_type' => $this->string(4),
            'dir' => $this->string(4),
            'orig_dir' => $this->string(4),
        ], $tableOptions);


        /**
         * Table history
         **/
        $this->createTable('{{history}}', [
            'id' => $this->primaryKey(),
            'profile_id' => $this->integer(),
            'date' => $this->integer()->notNull(),
            'title' => $this->string(50)->notNull(),
            'description' => $this->text(),
            'event_image' => $this->string(),
            'deleted' => $this->tinyInteger(1)->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnColumn(
            'history',
            'profile_id',
            'FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE NO ACTION ON UPDATE NO ACTION'
        );

        $this->addForeignKey(
            'fk-history-profile_id',
            'history',
            'profile_id',
            'profile',
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
        echo "m190311_181235_create_profile_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190311_181235_create_profile_tables cannot be reverted.\n";

        return false;
    }
    */
}

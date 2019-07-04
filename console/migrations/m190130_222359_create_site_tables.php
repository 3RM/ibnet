<?php

use yii\db\Migration;

/**
 * Class m190311_132910_create_site_tables
 */
class m190130_222359_create_site_tables extends Migration
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
         * Table user
         **/
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(20)->notNULL(),
            'last_name' => $this->string(40)->notNULL(),
            'email' => $this->string()->notNULL(),
            'new_email' => $this->string(),
            'new_email_token' => $this->string(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'last_login' => $this->integer()->notNull(),
            'ip' => $this->string(),
            'timezone' => $this->string()->defaultValue('UTC'),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'usr_image' => $this->string(),
            'display_name' => $this->string(60),
            'home_church' => $this->integer(),
            'primary_role' => $this->string(20),
            'reviewed' => $this->tinyInteger(1),   
        ], $tableOptions);

        $this->createIndex(
            'idx-user-username-password_hash',
            'user',
            [
                'username',
                'password_hash',
            ]
        );


        /**
         * Table primary_role
         **/
        $this->createTable('{{%primary_role}}', [
            'id' => $this->primaryKey(),
            'role' => $this->string(60)->notNull(),
            'type' => $this->string(40)->notNull(),
        ], $tableOptions);


        /**
         * Table mail_preferences
         **/
        $this->createTable('{{%subscription}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string(60)->notNull(),
            'token' => $this->string(32),
            'unsubscribe' => $this->tinyInteger(1)->defaultValue(0),
            'profile' => $this->tinyInteger(1)->defaultValue(1),
            'links' => $this->tinyInteger(1)->defaultValue(1),
            'comments' => $this->tinyInteger(1)->defaultValue(1),
            'features' => $this->tinyInteger(1)->defaultValue(1),
            'blog' => $this->tinyInteger(1)->defaultValue(1),
        ], $tableOptions);  
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
        echo "m190311_132910_create_site_tables cannot be reverted.\n";

        return false;
    }
    */
}

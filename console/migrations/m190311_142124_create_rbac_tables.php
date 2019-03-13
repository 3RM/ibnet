<?php

use yii\db\Migration;

/**
 * Class m190311_142124_create_rbac_tables
 */
class m190311_142124_create_rbac_tables extends Migration
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
         * Table auth_assignment
         **/
        $this->createTable('{{%auth_assignment}}', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer()->defaultValue(NULL),
        ], $tableOptions);

        $this->addPrimaryKey(
            'pk-auth_assignment',
            'auth_assignment',
            [
                'item_name',
                'user_id',
            ]
        );


        /**
         * Table auth_rule
         **/
        $this->createTable('{{%auth_rule}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->text(),
            'created_at' => $this->integer()->defaultValue(NULL),
            'updated_at' => $this->integer()->defaultValue(NULL),
        ], $tableOptions);

        $this->addPrimaryKey(
            'pk-auth_rule-name',
            'auth_rule',
            'name'
        );


        /**
         * Table auth_item
         **/
        $this->createTable('{{%auth_item}}', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->integer()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->text(),
            'created_at' => $this->integer()->defaultValue(NULL),
            'updated_at' => $this->integer()->defaultValue(NULL),
        ], $tableOptions);

        $this->addPrimaryKey(
            'pk-auth_item',
            'auth_item',
            'name'
        );

        $this->createIndex(
            'idx-auth_item-rule_name-type',
            'auth_item',
            [
                'rule_name',
                'type',
            ]
        );

        $this->addForeignKey(
            'fk-auth_item-rule_name',
            'auth_item',
            'rule_name',
            'auth_rule',
            'name',
            'SET NULL',
            'CASCADE'
        );


        /**
         * Table auth_item_child
         **/
        $this->createTable('{{%auth_item_child}}', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey(
            'pk-auth_item_child',
            'auth_item_child',
            [
                'parent',
                'child',
            ]
        );

        $this->createIndex(
            'idx-auth_item_child-child',
            'auth_item_child',
            'child'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // $this->dropTable('{{%auth_assignment}}');
        // $this->dropTable('{{%auth_item}}');
        // $this->dropTable('{{%auth_item_child}}');
        // $this->dropTable('{{%auth_rule}}');

        // return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190311_142124_create_rbac_tables cannot be reverted.\n";

        return false;
    }
    */
}

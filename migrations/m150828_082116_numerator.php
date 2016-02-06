<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_082116_numerator extends Migration
{
    public function up()
    {
  		$this->createTable('numerator_template', [
  				'id' => 'pk',
  				'name' => 'string NOT NULL',
  				'model_class' => 'string NOT NULL',
  				'field' => 'string NOT NULL',
  				'type_field' => 'string',
  				'type_value' => 'string',
  				'mask' => $this->string(64)->unique(),
  				'FK_type' => $this->integer(2),
          'join_table' => $this->string(),
          'join_on_condition' => $this->string(),
  				'init_val' => 'integer'
  		], "DEFAULT CHARSET = utf8 COLLATE utf8_general_ci");

  		$this->createTable('numerator_type', [
  				'id' => 'pk',
  				'name' => 'string NOT NULL',
  				'alias' => 'string',
  		], "DEFAULT CHARSET = utf8");

  		$this->insert('numerator_type', [
  				'id' => 1,
  				'name' => 'Порядковая нумерация',
  				'alias' => 'Ordered numeration',
  		]);

  		$this->insert('numerator_type', [
  				'name' => 'GUID',
  				'alias' => 'GUID',
  		]);
      }

      public function down()
      {
          $this->dropTable('numerator_template');
          $this->dropTable('numerator_type');
      }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}

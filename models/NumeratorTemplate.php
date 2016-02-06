<?php

namespace novikas\numerator\models;

use yii\db\ActiveRecord;
use yii\base\Model;

/**
 * @property string $model_class
 * @property string $field
 * @property string $type_field must be the field that defines type of entinity
 * @property string $type_value must be a value that defines type of entinity
 * @property string $name must be unique template name
 * @property string $mask
 * @property integer $FK_type
 * @property integer $init_val
 *
 * @property NumeratorType $type
 * @property boolean $isMasked
 * @property boolean $isTimestamp
 *
 * @author Novikov A.S.
 *
 */
class NumeratorTemplate extends ActiveRecord
{
	const TYPE_MASKED = 1;
	const TYPE_TIMESTAMP = 2;

	public static function tableName()
	{
		return 'numerator_template';
	}

	public function rules()
	{
		return [
			['FK_type', 'default', 'value' => self::TYPE_MASKED],
			[['FK_type', 'model_class', 'field' ], 'required'],
			[['name', 'model_class', 'field', 'mask', 'type_field', 'type_value'], 'string'],
			[['name', 'model_class', 'field', 'mask', 'type_field', 'type_value', 'FK_type', 'init_val', 'join_table', "join_on_condition"], 'safe'],
			[['model_class'], 'validateModelClassExist'],
			[['field', 'type_field'], 'validateFieldExist'],
			['name', 'unique'],
		];
	}

	public function validateModelClassExist( $attribute )
	{
		$class = $this->$attribute;
		if( !class_exists($class) ){
			$this->addError($attribute, "Attribute model_class contains a non-existing classname.");
			return;
		}
		$model = new $class();
		if( !($model instanceof Model) ){
			$this->addError($attribute, "Attribute model_class must be a valid model class. Make sure that it's a descedant of \"yii\\base\\Model\" class.");
		}
	}

	public function validateFieldExist( $attribute )
	{
		if( $this->hasErrors('model_class') || !$this->isMasked ){
			return;
		}
		$class = $this->model_class;
		$model = new $class;
		$attrs = explode("&", $this->$attribute);
		foreach ($attrs as $key => $attr) {
				if( $model->hasAttribute( $attr ) ){
						return;
				}
				$rel = explode(".", $attr);
				if( $model->hasRelation($rel[0]) ){
						return;
				}
				$this->addError($attribute, "Model class \"$class\" has not a property \"{$attr}\".");
		}
	}

	public function getType()
	{
		return $this->hasOne( NumeratorType::className(), ['id' => 'FK_type']);
	}


	public static function findByName( $name )
	{
		return self::find()->where([
				'name' => $name
		])->one();
	}

	public function getIsMasked()
	{
		return ( $this->FK_type === self::TYPE_MASKED );
	}

	public function getIsTimestamp()
	{
		return ( $this->FK_type === self::TYPE_TIMESTAMP );
	}
}

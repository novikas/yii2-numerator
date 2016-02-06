<?php

namespace novikas\numerator\models;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $name
 * @property string $alias
 *
 * @property NumeratorTemplate[] $numerators
 * @author Novikov A.S.
 *
 */

class NumeratorType extends ActiveRecord
{
	public static function tableName()
	{
		return 'numerator_type';
	}

	public function getNumerators()
	{
		return $this->hasMany( NumeratorTemplate::className(), ['FK_type' => 'id']);
	}
}

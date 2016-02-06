<?php

namespace app\models;

use novikas\numerator\Numerator;

class Doc extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return 'doc';
	}

	public function rules()
	{
		return [
			[['number'], 'default', 'value' => function($value){

				if(isset($this->type, $this->store->type)){
						$templateName = "doc{$this->type}{$this->store->type}";
						echo $templateName;
				}
				$numerator = Numerator::getNumerator($templateName);
				return $numerator->getNextNumber();
			}],
			[['type', 'FK_store'], 'safe']
		];
	}

	public function getStore()
	{
		return $this->hasOne(Store::className(), ['id' => 'FK_store']);
	}
}

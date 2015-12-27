<?php

namespace app\models;

class Store extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return 'store';
	}
	
	public function rules()
	{
		return [
			[['type', 'name'], 'safe']
		];
	}
}
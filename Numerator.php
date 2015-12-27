<?php

namespace novik\numerator;

use yii\base\Object;
use novik\numerator\models\NumeratorTemplate;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use novik\numerator\NumeratorMasker;
use yii\base\yii\base;
/**
 * @property yii\base\Model $lastModel
 * @author Novikov A.S
 *
 */

class Numerator extends Object
{

	/**
	 * @var NumeratorTemplate
	 */
	public $template;

	public $masker;

	public function init()
	{
		parent::init();

		if( !( $this->masker instanceof NumeratorMasker ) ||
			!( $this->template instanceof NumeratorTemplate ) ||
			!( $this->template->mask === $this->masker->mask ) ) {
			throw new InvalidConfigException("Properties masker and template need to be set and their properties mask must be equal.");
		}
	}

	public static function createNumerator( $config )
	{
		$model = new NumeratorTemplate();
		$model->attributes = $config;

		if( !$model->save() ){
			$errorString = '';
			foreach ( $model->getErrors() as $attribute ){
				$errorString .= implode('. ', $attribute);
			}
			throw new InvalidConfigException( $errorString );
		}

		return ( new self([
				'template' => $model,
				'masker' => (new NumeratorMasker(['mask' => $model->mask])),
		]) );
	}

	public static function getNumerator( $name )
	{
		$template = NumeratorTemplate::findByName($name);
		if( !isset($template) ) {
			throw new InvalidArgumentException("Invalid numerator name - \"{$name}\"");

		}
		$masker = new NumeratorMasker(['mask' => $template->mask]);
		return ( new self(compact('template', 'masker')) );
	}

	public function setMask($mask)
	{
		$this->masker = new NumeratorMasker(['mask' => $mask]);
	}

	public function getMask()
	{
		return isset($this->masker)?$this->masker->mask:"Masker is not set";
	}

	public function getNextNumber()
	{
		$number = 0;
		if( $this->template->isMasked ) {
			$number = $this->getNextMaskNumber();
		} else {
			$number = date('Ymdhis');
		}
		return $number;
	}

	public function getNextMaskNumber()
	{
		$this->masker->maskedNumber = $this->lastNumber;
		$number = $this->masker->getNumberFromMasked();

		if(is_null($number)){
			$number = $this->initNumber();
		} else {
			$number++;
		}

		return $this->masker->putMaskOn($number);
	}

	public function initNumber()
	{
		return $this->template->init_val;
	}

	public function getLastNumber()
	{
		$field = $this->template->field;

		try {
				$lastModel = $this->lastModel;
		} catch (\Exception $e ) {
				return "";
		}

		return $this->lastModel->$field;
	}

	public function getLastModel()
	{
		$modelClass = $this->template->model_class;
		$field = $this->template->field;

		$type_field = $this->template->type_field;

		$query = $modelClass::find()->where($this->condition)
												->andWhere("$field LIKE '".$this->masker->searchMask."'")
												->orderBy([$field => SORT_DESC ]);

		if( isset($this->template->join_table, $this->template->join_table) ) {
				$query->leftJoin($this->template->join_table, $this->template->join_on_condition);
		}

		$model = $query->one();
		if( is_null($model) ) {
			throw new \Exception("Last model not found", 404);
		}

		return $model;
	}

	public function getCondition()
	{
			$fields = explode("&", $this->template->type_field);
			$values = explode("&", $this->template->type_value);
			if( count($fields) !== count($values) ) {
					throw new InvalidConfigException("Invalid amount of fields and values in condition string");
			}
			$condition = [];

			for ($i=0; $i < count($fields); $i++) {
					$condition[$fields[$i]] = $values[$i];
			}
			return $condition;
	}

}

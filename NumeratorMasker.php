<?php

namespace novik\numerator;

use yii\base\Object;
use yii\base\InvalidConfigException;
use novik\numerator\classes\MbString;

/**
 * @property string $maskWithoutChars
 * @property string $maskWithoutSpecChars
 * @property string $unknownChars
 * @author Novikov A.S
 *
 */
class NumeratorMasker extends Object
{
	const YEAR_MARKER = 'y';
	const MONTH_MARKER = 'm';
	const QUARTER_MARKER = 'q';

	const NUMBER_MARKER = '9';

	const CHARS_START_MARKER = '{';
	const CHARS_END_MARKER = '}';

	public $specialChars = [
		self::YEAR_MARKER => '([0-9]{2})',
		'Y' => '(2[0-9]{3})',
		self::MONTH_MARKER => '(0[0-9]|1[0-2])',
		self::QUARTER_MARKER => '([1-4])',
		self::CHARS_START_MARKER => '()',
		self::CHARS_END_MARKER => '()',
		self::NUMBER_MARKER => '([0-9])',
	];

	public $mb_mask;

	/**
	 */
	public $mask;

	/**
	 */
	public $maskedNumber;

	public $number;

	public function init()
	{
		parent::init();
		$this->validateMask();

		if( isset($this->number) ){
			$this->putMaskOn($this->number);
		} else if( isset( $this->maskedNumber ) ){
			$this->getNumberFromMasked();
		}
	}

	public function getNumberAsInt()
	{
		$number = $this->numberFromMasked;
		if( isset($number) ){
			return (integer)$number;
		}
		return $number;
	}

	public function getNumberFromMasked()
	{
		$matches = [];

		preg_match($this->maskRegExp, $this->maskedNumber, $matches);

		$this->number = isset($matches[$this->numberMaskOrder + 1])?$matches[$this->numberMaskOrder + 1]:null;
		return $this->number;
	}

	public function getNumberMaskOrder()
	{
		$count = count($this->getCharsMasks());
		$mask = new MbString(['mb_string' => $this->maskWithoutChars]);
		$pos = $mask->posOf( $this->getMaskof(self::NUMBER_MARKER) );
		$pos += $count;

		return $pos;
	}

	public function getMaskRegExp()
	{
		$regexpArray = array_replace( $this->yearRegExp,
							  $this->monthRegExp,
							  $this->quarterRegExp,
							  $this->numberRegExp,
							  $this->charsRegExp );
		ksort($regexpArray);

		return "/".implode('', $regexpArray)."/";
	}

	protected function getYearRegExp()
	{
		$yearMask = $this->getMaskOf(self::YEAR_MARKER);
		if( empty($yearMask) ) {
			return [];
		}

		$regexp = $this->specialChars[$yearMask];
		$place = $this->getPlaceOf($yearMask);

		return [
			 $place => $regexp,
		];
	}

	protected function getMonthRegExp()
	{
		$monthMask = $this->getMaskOf(self::MONTH_MARKER);
		if( empty($monthMask) ) {
			return [];
		}

		$regexp = $this->specialChars[$monthMask];
		$place = $this->getPlaceOf($monthMask);

		return [
			 $place => $regexp,
		];
	}

	protected function getQuarterRegExp()
	{
		$quarterMask = $this->getMaskOf(self::QUARTER_MARKER);
		if( empty($quarterMask) ) {
			return [];
		}

		$regexp = $this->specialChars[$quarterMask];
		$place = $this->getPlaceOf($quarterMask);

		return [
			$place => $regexp,
		];
	}

	protected function getNumberRegExp()
	{
		$mask = $this->getMaskOf(self::NUMBER_MARKER);
		if( empty($mask) ) {
			return [];
		}
		$count = strlen( $mask );
		$regexp = "([0-9]{{$count},})";
		$place = $this->getPlaceOf($mask);

		return [
			$place => $regexp,
		];
	}

	protected function getCharsRegExp()
	{
		$charMasks = $this->charsMasks;
		if( empty($charMasks) ){
			return [];
		}
		$charRegExp = [];
		foreach($charMasks as $mask){
			$unbordered = $this->removeCharsBorders($this->mask, $mask );
			$pos = (new MbString(['mb_string' => $unbordered]))->posOf($mask);
			$charRegExp[$pos] = "($mask)";
		}
		return $charRegExp;
	}

	protected function getPlaceOf( $specChar )
	{
		if(empty($specChar)){
			return false;
		}
		$mask = $this->removeCharsBorders($this->mask);
		$mbString = new MbString(['mb_string' => $mask]);
		return $mbString->posOf($specChar);
	}

	public function putMaskOn($number)
	{
		$this->number = $number;
		$maskedNumber = $this->putOnNumber($number, $this->mask);
		$maskedYearMonth = $this->putOnYearMonth($maskedNumber);
		$maskedQuarter = $this->putOnQuarter($maskedYearMonth);
		$this->maskedNumber = $this->removeCharsBorders($maskedQuarter);
		return $this->maskedNumber;
	}

	public function putOnYearMonth( $mask )
	{
		$maskWithYear = date($this->quote($mask));

		return $maskWithYear;
	}

	public function putOnNumber($number, $mask)
	{
		$zeroCount = strlen( $this->getMaskOf(self::NUMBER_MARKER) ) - strlen($number);
		$number = str_repeat('0', ( $zeroCount <= 0 )?0:$zeroCount ).$number;
		return str_replace($this->getMaskOf(self::NUMBER_MARKER), $number, $mask);
	}

	public function putOnQuarter($mask)
	{
		$mask = str_split($mask);
		$quarter = (string)ceil(date('m')/3);
		$ignore = false;
		for($i = 0; $i < count($mask); $i++){
			if( !$ignore && ($mask[$i] == self::QUARTER_MARKER) ) {
				$mask[$i] = $quarter;
			}
		}
		return implode('', $mask);
	}

	public function removeCharsBorders( $mask )
	{
		return str_replace([
				self::CHARS_END_MARKER,
				self::CHARS_START_MARKER,
		], '', $mask);
	}

	public function validateMask()
	{
		$this->mb_mask = new MbString(['mb_string' => $this->mask]);
		if( !empty( $this->unknownChars ) ){
			throw new InvalidConfigException("Mask must contain only characters from list below: Y, y, m, q, {, }, 9" );
		}
	}

	protected function getCharsMasks()
	{
		return $this->mb_mask->getBetween([
				self::CHARS_START_MARKER,
				self::CHARS_END_MARKER
		]);
	}

	public function getMaskOf( $part )
	{
		$matches = [];
		preg_match("/{$part}+/i", $this->maskWithoutChars, $matches);
		return isset($matches[0])?$matches[0]:[];
	}

	public function getMaskWithoutChars()
	{
		return $this->mb_mask->replaceBetween([
				self::CHARS_START_MARKER,
				self::CHARS_END_MARKER
		]);
	}

	public function getMaskWithoutSpecChars()
	{
		return str_replace( array_keys( $this->specialChars ), '', $this->mask);
	}

	public function getUnknownChars()
	{
		return str_replace( array_keys( $this->specialChars ), '', $this->maskWithoutChars);
	}

	protected function getMaskWithHiddenChars()
	{
		return $this->mb_mask->replaceEachBetween([
				self::CHARS_START_MARKER,
				self::CHARS_END_MARKER
		], "?");
	}

	protected function getSearchMask()
	{
		$masks = $this->charsMasks;
		$searchMask = "%";
		foreach ($masks as $m){
			$searchMask .= $m."%";
		}
		return $searchMask;
	}

	public function removeFromMask( $part )
	{
		return $this->mb_mask->remove($part);
	}

	public function quote($mask)
	{
		$quoted = '';
		$mb_mask = new MbString(['mb_string' => $mask]);
		$chars = $mb_mask->toArray;
		foreach ( $chars as $char){
			if( !in_array($char, [self::YEAR_MARKER, strtoupper(self::YEAR_MARKER), self::MONTH_MARKER]) ) {
				$quoted .= '\\'.$char;
			} else {
				$quoted .= $char;
			}
		}
		return $quoted;
	}

	public function setMask($mask)
	{
		$this->mask = $mask;
		$this->validateMask();
	}
}

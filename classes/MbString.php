<?php

namespace novik\numerator\classes;

use yii\base\Object;

class MbString extends Object{
	
	public $mb_string;
	public $encoding = 'UTF-8';
	
	public function getToArray()
	{
		return preg_split('/(?<!^)(?!$)/u', $this->mb_string );
	}

	/**
	 * 
	 * @param string|array $markers
	 * @param string $replacement
	 * @param boolean $includeBrackets
	 * @return mixed
	 */
	public function replaceBetween( $markers, $replacement = '' )
	{
		$startMarker = "\\".(is_array($markers)?$markers[0]:$markers);
		$endMarker = "\\".(is_array($markers)?$markers[1]:$markers);
		
		return preg_replace("/{$startMarker}[^{$endMarker}]*{$endMarker}/", $replacement, $this->mb_string);
	}
	
	public function replaceEachBetween($markers, $replacement = '?')
	{
		$matches = [];
		$startMarker = "\\".(is_array($markers)?$markers[0]:$markers);
		$endMarker = "\\".(is_array($markers)?$markers[1]:$markers);
		
		preg_match_all("/{$startMarker}[^{$endMarker}]*{$endMarker}/", $this->mb_string, $matches);
		$result = $this->mb_string;
		foreach ($matches[0] as $match){
			$len = mb_strlen($match, $this->encoding) - 2;
			$rep = str_repeat($replacement, $len);
			$result = str_replace($match, $rep, $result);
		}
		return $result;
	}
	
	public function getBetween( $markers )
	{
		$startMarker = "\\".(is_array($markers)?$markers[0]:$markers);
		$endMarker = "\\".(is_array($markers)?$markers[1]:$markers);
		
		$matches = [];
		$result = [];
		preg_match_all("/{$startMarker}[^{$endMarker}]*{$endMarker}/", $this->mb_string, $matches); 
		foreach ( $matches[0] as $match ) {
			$result[] = str_replace($markers, '', $match); 
		}
		return $result;
	}
	
	public function posOf($char, $offset = 0)
	{
		return mb_strpos( $this->mb_string, $char, $offset, $this->encoding);
	}
	
	public function remove($char)
	{
		return preg_replace("/$char+/i", '', $this->mb_string);
	}
	
	public function countOf($char)
	{
		return mb_substr_count( $this->mb_string, $char, $this->encoding );
	}
	
	public function replace($search, $replacement)
	{
		return str_replace($search, $replacement, $this->mb_string);
	}
	
}
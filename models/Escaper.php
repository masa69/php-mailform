<?php

class Escaper {

	/**
	 * 文字列のエスケープ
	 *
	 * @param string $string
	 * @return string
	 */
	public static function string($string = null) {
		return (string) self::convert($string);
	}


	/**
	 * 数値のエスケープ
	 *
	 * @param string|int $integer
	 * @return int
	 */
	public static function int($integer = null) {
		return (integer) self::convert($integer);
	}


	/**
	 * 理論値のエスケープ
	 *
	 * @param boolean|int|string $boolean
	 * @return boolean
	 */
	public static function boolean($boolean = null) {
		return (boolean) self::convert($boolean);
	}


	/**
	 * エスケープする
	 *
	 * @param boolean|int|string $value
	 * @return $value
	 */
	private static function convert($value) {
		return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
	}

}
<?php

class ClassAutoLoader
{
	private static $dirs = [];

	public function __construct($path = null)
	{
		if (is_array($path)) {
			self::$dirs = $path;
		}
		spl_autoload_register([$this, 'loader']);
	}

	public static function loader($classname)
	{
		foreach (self::$dirs as $dir) {
			$file = "{$dir}{$classname}.php";
			if (is_readable($file)) {
				require_once $file;
				return;
			}
		}
	}
}

/**
 * 対象パスの設定
 */
$ClassAutoLoader = new ClassAutoLoader([
	'models' => APP_PATH . '/models/',
]);
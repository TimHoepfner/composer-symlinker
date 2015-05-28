<?php


namespace ComposerSymlinker;

use \Dotenv;

class Env
{
	public function __construct($path = null, $file = '.env')
	{
		if (is_null($path)) {
			$path = getcwd();
		}
		try {
			Dotenv::load($path, $file);
		} catch (\Exception $e) {}
	}

	public function get($key, $default = null)
	{
		$value = getenv($key);

		return $value === false ? $default : $value;
	}

	/**
	 * @param $str
	 * @return array
	 */
	public function splitStr($str)
	{
		return preg_split("/(\||,)/", $str);
	}

	/**
	 * @param $key
	 * @param null $default
	 * @return array
	 */
	public function getKey($key, $default = null)
	{
		return $this->splitStr(
			$this->get($key, $default)
		);
	}

	/**
	 * @param $key
	 * @param null $default
	 * @return array
	 */
	public function getKeyAsKeyValuePair($key, $default = null)
	{
		$parts = $this->getKey($key, $default);
		$keyValue = [];
		foreach ($parts as $k => &$part) {
			$part = explode(':', $part);
			$keyValue[$part[0]] = $part[1];
		}
		return $keyValue;
	}
}

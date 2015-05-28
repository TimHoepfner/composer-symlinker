<?php

namespace ComposerSymlinker\Tests;

use ComposerSymlinker\Env;
use ComposerSymlinker\Config;


class EnvTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Env
	 */
	protected $env;

	public function setUp()
	{
		$pathToEnv = __DIR__ . '/fixtures';
		$this->env = new Env($pathToEnv);
	}

	public function testDotenvNestingVariablesWorking()
	{
		$localDirs = $this->env->get(Config::ENV_KEY_DIRS);
		$this->assertEquals('/path/to/DOCUMENT_ROOT/projects', $localDirs);
	}

	public function testRegularEnvVar()
	{
		// configured in phpunit xml
		$regularEnvVar = $this->env->get('REGULAR_ENV_VAR');
		$this->assertEquals('regular-env-var', $regularEnvVar);
	}

	public function testDefaultValue()
	{
		$defaultValue = $this->env->get('_UNDEFINED_KEY_', 'defaultValue');
		$this->assertEquals('defaultValue', $defaultValue);
	}

	public function testPathDelimiter()
	{
		$expected = [
			'/path/1',
			'/path/2',
			'/path/3',
		];

		$this->assertEquals($expected, $this->env->splitStr('/path/1,/path/2,/path/3'));
		$this->assertEquals($expected, $this->env->splitStr('/path/1|/path/2|/path/3'));
		$this->assertEquals($expected, $this->env->splitStr('/path/1,/path/2|/path/3'));
		$this->assertEquals($expected, $this->env->splitStr('/path/1|/path/2,/path/3'));
	}

	public function testWindowsPathDelimiters()
	{
		$expected = [
			'C:\path\1',
			'C:\path\2',
			'C:\path\3',
		];
		$this->assertEquals($expected, $this->env->splitStr('C:\path\1,C:\path\2,C:\path\3'));
		$this->assertEquals($expected, $this->env->splitStr('C:\path\1|C:\path\2|C:\path\3'));
		$this->assertEquals($expected, $this->env->splitStr('C:\path\1,C:\path\2|C:\path\3'));
		$this->assertEquals($expected, $this->env->splitStr('C:\path\1|C:\path\2,C:\path\3'));
	}

	public function testGetKey()
	{
		$this->assertEquals(['vendor1', 'vendor2'], $this->env->getKey(Config::ENV_KEY_VENDORS));
	}

	public function testGetKeyAsKeyValuePair()
	{
		$this->assertEquals([
			'vendor/package1' => '/my/absolute/path/to/vendor/package1',
			'vendor/package2' => '/my/absolute/path/to/vendor/package2'
		], $this->env->getKeyAsKeyValuePair(Config::ENV_KEY_PACKAGES));
	}

}

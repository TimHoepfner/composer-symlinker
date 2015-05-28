<?php

namespace ComposerSymlinker\Tests;

use ComposerSymlinker\Env;
use ComposerSymlinker\Config;


class ConfigTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var string
	 */
	private $fixturesPath;

	public function setUp()
	{
		$this->fixturesPath = __DIR__ . '/fixtures';
	}

	private function createComposerMock(array $extra)
	{
		$package = $this->getMock('\Composer\Package\PackageInterface');
		$package->expects($this->any())
			->method('getExtra')
			->will($this->returnValue($extra));

		$composer = $this->getMock('\Composer\Composer');
		$composer->expects($this->any())
			->method('getPackage')
			->will($this->returnValue($package));
		return $composer;
	}

	private function createComposerMockWithReturnValue()
	{
		$composer = $this->createComposerMock([
			'local-dirs' => '/path/to/DOCUMENT_ROOT/projects',
			'local-packages' => [
				'vendor/package1' => '/my/absolute/path/to/vendor/package1',
				'vendor/package2' => '/my/absolute/path/to/vendor/package2',
			],
			'local-vendors' => [
				'vendor1',
				'vendor2',
			]
		]);

		return $composer;
	}

	private function createComposerMockEmptyValue()
	{
		$composer = $this->createComposerMock([]);
		return $composer;
	}

	private function createEnv()
	{
		return new Env($this->fixturesPath);
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	private function createEnvMock()
	{
		$env = $this->getMockBuilder('\ComposerSymlinker\Env')->disableOriginalConstructor()->getMock();
		$env->expects($this->any())
			->method('get')
			->will($this->returnValue(false));
		$env->expects($this->any())
			->method('getKey')
			->will($this->returnValue(false));
		$env->expects($this->any())
			->method('getKeyAsKeyValuePair')
			->will($this->returnValue(false));
		return $env;
	}

	public function testComposerExtraConfig()
	{
		$composer = $this->createComposerMockWithReturnValue();
		$env = $this->createEnv();
		$config = new Config(getcwd(), $composer, $env);
		$this->assertTrue($config->hasComposerConfig());
	}

	public function testComposerWithoutExtraConfig()
	{
		$composer = $this->createComposerMockEmptyValue();
		$env = $this->createEnv();
		$config = new Config(getcwd(), $composer, $env);
		$this->assertFalse($config->hasComposerConfig());
	}

	public function testEnvConfig()
	{
		$composer = $this->createComposerMockEmptyValue();
		$env = $this->createEnv();
		$config = new Config(getcwd(), $composer, $env);
		$this->assertTrue($config->hasEnvConfig());
	}

	public function testEnvWithoutConfig()
	{
		$composer = $this->createComposerMockEmptyValue();
		$env = $this->createEnvMock();
		$config = new Config(getcwd(), $composer, $env);
		$this->assertFalse($config->hasEnvConfig());
	}

	public function testHasAnyConfigComposer()
	{
		$composer = $this->createComposerMockWithReturnValue();
		$env = $this->createEnvMock();
		$config = new Config(getcwd(), $composer, $env);
		$this->assertTrue($config->hasAnyConfig());
	}

	public function testHasAnyConfigEnv()
	{
		$composer = $this->createComposerMock([]);
		$env = $this->createEnv();
		$config = new Config(getcwd(), $composer, $env);
		$this->assertTrue($config->hasAnyConfig());
	}

	public function testGetLocalDirsVendorsAndPackages()
	{
		$localDir = '/path/to/DOCUMENT_ROOT/projects';
		for ($i = 0; $i < 2; $i++) {
			if ($i < 1) {
				$composer = $this->createComposerMockWithReturnValue();
				$env = $this->createEnvMock();
				$expectedMessage = "Using concrete composer and env mock";
			} else {
				$composer = $this->createComposerMock([]);
				$env = $this->createEnv();
				$expectedMessage = "Using composer mock and concrete env";
				$localDir = [$localDir];
			}
			$config = new Config(getcwd(), $composer, $env);
			$this->assertEquals($localDir, $config->getLocalDirs(), $expectedMessage);
			$this->assertEquals([
				'vendor/package1' => '/my/absolute/path/to/vendor/package1',
				'vendor/package2' => '/my/absolute/path/to/vendor/package2'
			], $config->getLocalPackages(), $expectedMessage);
			$this->assertEquals(['vendor1', 'vendor2'], $config->getLocalVendors(), $expectedMessage);
		}
	}
}

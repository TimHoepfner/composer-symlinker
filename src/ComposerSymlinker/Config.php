<?php


namespace ComposerSymlinker;

use Composer\Composer;


class Config
{

	const KEY_DIRS = 'local-dirs';
	const KEY_VENDORS = 'local-vendors';
	const KEY_PACKAGES = 'local-packages';

	const ENV_KEY_DIRS = 'COMPOSER_LOCAL_DIRS';
	const ENV_KEY_VENDORS = 'COMPOSER_LOCAL_VENDORS';
	const ENV_KEY_PACKAGES = 'COMPOSER_LOCAL_PACKAGES';
	const ENV_KEY_DISABLED = 'COMPOSER_LOCAL_DISABLED';

	protected $composer;

	protected $env;

	public function __construct(Composer $composer, Env $env)
	{
		$this->composer = $composer;
		$this->env = $env;
		$this->extra = $this->composer->getPackage()->getExtra();
	}

	/**
	 * Check if any config available
	 * @return bool
	 */
	public function hasAnyConfig()
	{
		return $this->hasComposerConfig() || $this->hasEnvConfig();
	}

	/**
	 * Check if there is any config in composers "extra" block
	 * @return bool
	 */
	public function hasComposerConfig()
	{
		$extra = $this->composer->getPackage()->getExtra();
		if (empty($extra)) {
			return false;
		}

		return isset($extra[static::KEY_DIRS]);
	}

	/**
	 * Check if there is a dot env config and feature is not disabled
	 * @return bool
	 */
	public function hasEnvConfig()
	{
		return $this->env->get(static::ENV_KEY_DISABLED) != true
			&& $this->env->get(static::ENV_KEY_DIRS) !== false;
	}

	public function getLocalDirs()
	{
		return isset($this->extra[Config::KEY_DIRS])
			? $this->extra[Config::KEY_DIRS] : $this->env->getKey(Config::ENV_KEY_DIRS, dirname(getcwd()));
	}

	public function hasLocalVendors()
	{
		return is_array($this->getLocalVendors());
	}

	public function getLocalVendors()
	{
		return isset($this->extra[Config::KEY_VENDORS])
			? $this->extra[Config::KEY_VENDORS] : $this->env->getKey(Config::ENV_KEY_VENDORS);
	}

	public function hasLocalPackages()
	{
		return is_array($this->getLocalPackages());
	}

	public function getLocalPackages()
	{
		return isset($this->extra[Config::KEY_PACKAGES])
			? $this->extra[Config::KEY_PACKAGES] : $this->env->getKeyAsKeyValuePair(Config::ENV_KEY_PACKAGES);
	}

}

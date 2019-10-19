<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Utils;

use Composer\Composer;
use Composer\Package\PackageInterface;

final class PathResolver
{

	/** @var Composer */
	private $composer;

	public function __construct(Composer $composer)
	{
		$this->composer = $composer;
	}

	public function getAbsolutePath(PackageInterface $package): string
	{
		if ($package === $this->composer->getPackage()) {
			return $this->getRootDir();
		}

		return $this->composer->getInstallationManager()->getInstallPath($package);
	}

	public function getRelativePath(PackageInterface $package): string
	{
		return substr($this->getAbsolutePath($package), strlen($this->getRootDir()));
	}

	public function getConfigFileFqn(PackageInterface $package, string $fileName): string
	{
		// File name is absolute, use it
		if (realpath($fileName) === $fileName && file_exists($fileName)) {
			return $fileName;
		}

		return $this->getAbsolutePath($package) . '/' . $fileName;
	}

	public function getRootDir(): string
	{
		// Composer supports ProjectInstaller only during create-project command so let's hope no-one change vendor-dir
		return dirname($this->composer->getConfig()->get('vendor-dir'));
	}

}

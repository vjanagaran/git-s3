<?php namespace GitS3\Wrapper;

use Symfony\Component\Yaml\Yaml;
use BadMethodCallException;
use Exception;

class Config
{
	private $file;
	private $data;

	public function __construct($file) {
		$this->file = $file;
		$this->data = Yaml::parse($file);
	}

	public function getPath()
	{
		$pathValue = $this->data['path'];

		// check if path is absolute
		if (substr($pathValue, 0, 1) == '/')
		{
			$path = $pathValue;
		}
		else
		{
			$path = __DIR__ . '/../../../' . $pathValue;
		}

		$path = realpath($path);

		if ( ! $path)
		{
			throw new Exception('Invalid path');
		}

		return $path;
	}

	public function setPath($pathValue)
	{
		// check if path is absolute
		if (substr($pathValue, 0, 1) == '/')
		{
			$path = $pathValue;
		}
		else
		{
			$path = __DIR__ . '/../../../' . $pathValue;
		}

		if ( ! realpath($path))
		{
			var_dump($path);
			throw new Exception('Invalid path');
		}

		$this->data['path'] = $pathValue;
	}

	public function __call($method, $parameters)
	{
		$prefix = substr($method, 0, 3);
		$attribute = lcfirst(substr($method, 3));
		$availabeAttributes = array(
			'key',
			'secret',
			'region',
			'bucket',
			);

		if ( ! in_array($attribute, $availabeAttributes))
		{
			throw new BadMethodCallException();	
		}

		if ($prefix == 'get')
		{
			return $this->data[$attribute];
		}
		elseif ($prefix == 'set')
		{
			$this->data[$attribute] = $parameters[0];
		}
		else
		{
			throw new BadMethodCallException();
		}
	}

	public function save()
	{
		$yaml = Yaml::dump($this->data);

		return file_put_contents($this->file, $yaml);
	}
	
}
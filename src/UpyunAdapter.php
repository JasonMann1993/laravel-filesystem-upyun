<?php

namespace Jasonmann\LaravelFilesystem\Upyun;

use Upyun\Upyun;
use League\Flysystem\Config;
use League\Flysystem\Adapter\AbstractAdapter;

class UpyunAdapter extends AbstractAdapter
{
    /**
     * @var
     */
    protected $bucket;
    /**
     * @var
     */
    protected $operator;
    /**
     * @var
     */
    protected $password;

    /**
     * @var
     */
    protected $domain;

    /**
     * @var
     */
    protected $protocol;

    /**
     * UpyunAdapter constructor.
     * @param $bucket
     * @param $operator
     * @param $password
     * @param mixed $domain
     * @param mixed $protocol
     */
    public function __construct($bucket, $operator, $password, $domain, $protocol = 'http')
    {
        $this->bucket = $bucket;
        $this->operator = $operator;
        $this->password = $password;
        $this->domain = $domain;
        $this->protocol = $protocol;
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     * @return array|bool|false
     * @throws \Exception
     */
    public function write($path, $contents, Config $config)
    {
        return $this->client()->write($path, $contents);
    }

    /**
     * @param string   $path
     * @param resource $resource
     * @param Config   $config
     * @return array|bool|false
     * @throws \Exception
     */
    public function writeStream($path, $resource, Config $config)
    {
        return $this->client()->write($path, $resource);
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     * @return array|bool|false
     * @throws \Exception
     */
    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    /**
     * @param string   $path
     * @param resource $resource
     * @param Config   $config
     * @return array|bool|false
     * @throws \Exception
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * @param string $path
     * @param string $newpath
     * @return bool
     * @throws \Exception
     */
    public function rename($path, $newpath)
    {
        $this->copy($path,$newpath);
        return $this->delete($path);
    }

    /**
     * @param string $path
     * @param string $newpath
     * @return bool
     * @throws \Exception
     */
    public function copy($path, $newpath)
    {
        $this->writeStream($newpath, fopen($this->getUrl($path), 'r'), new Config());
        return true;
    }

    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function delete($path)
    {
        return $this->client()->delete($path);
    }

    /**
     * @param string $dirname
     * @return bool
     * @throws \Exception
     */
    public function deleteDir($dirname)
    {
        return $this->client()->deleteDir($dirname);
    }

    /**
     * @param string $dirname
     * @param Config $config
     * @return array|bool|false
     * @throws \Exception
     */
    public function createDir($dirname, Config $config)
    {
        return $this->client()->createDir($dirname);
    }

    /**
     * @param string $path
     * @param string $visibility
     * @return array|bool|false
     */
    public function setVisibility($path, $visibility)
    {
        return true;
    }

    /**
     * @param string $path
     * @return array|bool|null
     * @throws \Exception
     */
    public function has($path)
    {
        return $this->client()->has($path);
    }

    /**
     * @param string $path
     * @return array|false
     */
    public function read($path)
    {
        $contents = file_get_contents($this->getUrl($path));
        return compact('contents', 'path');
    }

    /**
     * @param string $path
     * @return array|false
     */
    public function readStream($path)
    {
        $stream = fopen($this->getUrl($path), 'r');
        return compact('stream', 'path');
    }

    /**
     * @param string $directory
     * @param bool   $recursive
     * @return array
     * @throws \Exception
     */
    public function listContents($directory = '', $recursive = false)
    {
        $list = [];

        $result = $this->client()->read($directory, null, [ 'X-List-Limit' => 100, 'X-List-Iter' => null]);

        foreach ($result['files'] as $files) {
            $list[] = $this->normalizeFileInfo($files, $directory);
        }

        return $list;
    }

    /**
     * @param string $path
     * @return array|false
     */
    public function getMetadata($path)
    {
        return $this->client()->info($path);
    }

    /**
     * @param $path
     * @return array
     */
    public function getType($path)
    {
        $response = $this->getMetadata($path);

        return ['type' => $response['x-upyun-file-type']];
    }

    /**
     * @param string $path
     * @return array|false
     */
    public function getSize($path)
    {
        $response = $this->getMetadata($path);

        return ['size' => $response['x-upyun-file-size']];
    }

    /**
     * @param string $path
     * @return array|false
     */
    public function getMimetype($path)
    {
        $headers = get_headers($this->getUrl($path), 1);
        $mimetype = $headers['Content-Type'];
        return compact('mimetype');
    }

    /**
     * @param string $path
     * @return array|false
     */
    public function getTimestamp($path)
    {
        $response = $this->getMetadata($path);

        return ['timestamp' => $response['x-upyun-file-date']];
    }

    /**
     * @param string $path
     * @return array|bool|false
     */
    public function getVisibility($path)
    {
        return true;
    }

    /**
     * @param $path
     * @return string
     */
    public function getUrl($path)
    {
        return $this->normalizeHost($this->domain).$path;
    }

    /**
     * @return Upyun
     */
    public function client()
    {
        $config = new \Upyun\Config($this->bucket, $this->operator, $this->password);
        $config->useSsl = config('filesystems.disks.upyun.protocol') === 'https' ? true : false;
        return new Upyun($config);
    }

    /**
     * @param array  $stats
     * @param string $directory
     * @return array
     */
    protected function normalizeFileInfo(array $stats, string $directory)
    {
        $filePath = ltrim($directory . '/' . $stats['name'], '/');

        return [
            'type' => $this->getType($filePath)['type'],
            'path' => $filePath,
            'timestamp' => $stats['time'],
            'size' => $stats['size'],
        ];
    }

    /**
     * @param $domain
     * @return string
     */
    protected function normalizeHost($domain)
    {
        if (0 !== stripos($domain, 'https://') && 0 !== stripos($domain, 'http://')) {
            $domain = $this->protocol."://{$domain}";
        }

        return rtrim($domain, '/').'/';
    }
}
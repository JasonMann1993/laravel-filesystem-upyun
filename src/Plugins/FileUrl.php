<?php

namespace Jasonmann\LaravelFilesystem\Upyun\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

class FileUrl extends AbstractPlugin
{
    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'getUrl';
    }

    public function handle($path = null)
    {
        return $this->filesystem->getAdapter()->getUrl($path);
    }
}
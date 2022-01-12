<?php

namespace Jasonmann\LaravelFilesystem\Upyun\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

class Kernel extends AbstractPlugin
{
    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'kernel';
    }

    public function handle($path = null)
    {
        return $this->filesystem->getAdapter()->client();
    }
}
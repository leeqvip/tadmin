<?php

namespace tadmin\command;

use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Init extends Command
{
    protected function configure()
    {
        $this->setName('tadmin:init')->setDescription('init tadmin');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->publishAssets();
    }

    protected function publishAssets()
    {
        $source = new Filesystem(
            new LocalFilesystemAdapter(__DIR__ . '/../../resource/assets')
        );
        $target = new Filesystem(
            new LocalFilesystemAdapter($this->app->getRootPath() . 'public/tmp/assets')
        );

        $manager = new MountManager([
            'source' => $source,
            'target' => $target,
        ]);

        $contents = $manager->listContents('source://', true);

        foreach ($contents as $entry) {
            $path = $entry->path();
            if ($entry->isDir()) {
                continue;
            }

            $update = false;

            if (!$manager->fileExists('target://' . $path)) {
                $update = true;
            } elseif ($manager->lastModified('source://' . $path) > $manager->lastModified('target://' . $path)) {
                $update = true;
            }

            if ($update) {
                $manager->write('target://' . $path, $manager->read('source://' . $path));
            }
        }
    }
}


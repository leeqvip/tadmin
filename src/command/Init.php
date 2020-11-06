<?php

namespace tadmin\command;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Loader;

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
            new Local(__DIR__ . '/../../resource/assets')
        );
        $traget = new Filesystem(
            new Local($this->app->getRootPath() . 'public/tmp/assets')
        );

        $manager = new MountManager([
            'source' => $source,
            'traget' => $traget,
        ]);

        $contents = $manager->listContents('source://', true);

        foreach ($contents as $entry) {
            $update = false;

            if (!$manager->has('traget://' . $entry['path'])) {
                $update = true;
            } elseif ($manager->getTimestamp('source://' . $entry['path']) > $manager->getTimestamp('traget://' . $entry['path'])) {
                $update = true;
            }

            if ('file' === $entry['type'] && $update) {

                $manager->put('traget://' . $entry['path'], $manager->read('source://' . $entry['path']));
            }
        }
    }
}

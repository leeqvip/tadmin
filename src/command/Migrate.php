<?php

namespace tadmin\command;

use think\migration\command\migrate\Run as MigrateRun;

class Migrate extends MigrateRun
{
    protected $adapter;

    protected function configure()
    {
        parent::configure();
        $this->setName('tadmin:migrate:run')->setDescription('Migrate the database for tadmin');
    }

    protected function getPath()
    {
        return __DIR__.'/../../database/migrations';
    }
}

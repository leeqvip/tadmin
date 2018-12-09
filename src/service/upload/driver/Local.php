<?php

namespace tadmin\service\upload\driver;

class Local
{
    protected $config;

    protected $file;

    protected $fileType = 'images';

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function multiple(...$names)
    {
        $files = [];
        foreach ($names as $key => $name) {
            if (!$this->has($name)) {
                continue;
            }
            $files[$name] = request()->file($name);
        }
        $multiple = [];
        foreach ($files as $key => $file) {
            $f = $file->move($this->config['root'].'files');
            $multiple[$key] = [
                'save_name' => $this->config['url'].'files'.'/'.$f->getSaveName(),
            ];
        }

        return $multiple;
    }

    public function image($name)
    {
        if (!$this->has($name)) {
            return false;
        }

        $file = request()->file($name);

        $this->file = $file->move($this->config['root'].$this->fileType);

        if (!$this->file) {
            throw new \Exception($file->getError());
        }

        return $this;
    }

    public function video($name)
    {
        $this->fileType = 'videos';
        if (!$this->has($name)) {
            return false;
        }
        $file = request()->file($name);

        $this->file = $file->move($this->config['root'].$this->fileType);

        if (!$this->file) {
            throw new \Exception($file->getError());
        }

        return $this;
    }

    public function has($name)
    {
        return isset($_FILES[$name]) && 0 == $_FILES[$name]['error'];

        try {
            return request()->has($name, 'file');
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getUrlPath()
    {
        if (!$this->file) {
            return '';
        }

        return $this->config['url'].$this->fileType.'/'.$this->file->getSaveName();
    }
}

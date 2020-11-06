<?php

namespace tadmin\model;

class Nav extends Model
{
    use traits\Tree;

    protected $name = 'navs';

    protected $append = ['target_text'];

    public function getTargetTextAttr()
    {
        return self::mapTarget($this->getAttr('target'));
    }

    public static function mapTarget($target = null)
    {
        $map = [
            '_self' => '默认',
            '_blank' => '新标签页',
            '_parent' => '父级框架',
            '_top' => '整个窗口',
        ];
        if (null === $target) {
            return $map;
        }

        return isset($map[$target]) ? $map[$target] : '';
    }
}

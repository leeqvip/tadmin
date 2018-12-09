<?php

namespace tadmin\model;

class OperationLog extends Model
{
    protected $table = 'operation_logs';

    public function getBrowserAttr()
    {
        return $this->getClientBrowser($this->useragent);
    }

    public function adminer()
    {
        return $this->hasOne(Adminer::class, 'id', 'adminer_id');
    }

    public function getClientBrowser($agent, $glue = ' ')
    {
        $browser = [];
        /* 定义浏览器特性正则表达式 */
        $regex = [
            'ie' => '/(MSIE) (\d+\.\d)/',
            'chrome' => '/(Chrome)\/(\d+\.\d+)/',
            'firefox' => '/(Firefox)\/(\d+\.\d+)/',
            'opera' => '/(Opera)\/(\d+\.\d+)/',
            'safari' => '/Version\/(\d+\.\d+\.\d) (Safari)/',
        ];
        foreach ($regex as $type => $reg) {
            preg_match($reg, $agent, $data);

            if (!empty($data) && \is_array($data)) {
                $browser = 'safari' === $type ? [$data[2], $data[1]] : [$data[1], $data[2]];

                break;
            }
        }

        return empty($browser) ? false : (null === $glue ? $browser : implode($glue, $browser));
    }
}

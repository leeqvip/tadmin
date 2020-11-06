<?php

namespace tadmin\model;

class OperationLog extends Model
{
    protected $name = 'operation_logs';

    public function getBrowserAttr()
    {
        $browser = $this->browserInfo($this->useragent);

        return  $browser['name'].' '.$browser['version'];
    }

    public function adminer()
    {
        return $this->hasOne(Adminer::class, 'id', 'adminer_id');
    }

    public function browserInfo($user_agent = null)
    {
        if (empty($user_agent)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = '';

        //First get the platform?
        if (preg_match('/linux/i', $user_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $user_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
            $bname = 'Internet Explorer';
            $ub = 'MSIE';
        } elseif (preg_match('/Trident/i', $user_agent)) { // this condition is for IE11
            $bname = 'Internet Explorer';
            $ub = 'rv';
        } elseif (preg_match('/Firefox/i', $user_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = 'Firefox';
        } elseif (preg_match('/Chrome/i', $user_agent)) {
            $bname = 'Google Chrome';
            $ub = 'Chrome';
        } elseif (preg_match('/Safari/i', $user_agent)) {
            $bname = 'Apple Safari';
            $ub = 'Safari';
        } elseif (preg_match('/Opera/i', $user_agent)) {
            $bname = 'Opera';
            $ub = 'Opera';
        } elseif (preg_match('/Netscape/i', $user_agent)) {
            $bname = 'Netscape';
            $ub = 'Netscape';
        }

        // finally get the correct version number
        // Added "|:"
        $known = ['Version', $ub, 'other'];
        $pattern = '#(?<browser>'.implode('|', $known).')[/|: ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $user_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if (1 != $i) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($user_agent, 'Version') < strripos($user_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if (null == $version || '' == $version) {
            $version = '?';
        }

        return [
            'userAgent' => $user_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern,
        ];
    }
}

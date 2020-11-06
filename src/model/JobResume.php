<?php

namespace tadmin\model;

class JobResume extends Model
{
    protected $name = 'job_resumes';

    protected $append = ['summary_text'];

    public function getSummaryTextAttr()
    {
        $summary = str_limit(strip_tags($this->getAttr('content')), 50);

        return $summary;
    }
}

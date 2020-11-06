<?php

namespace tadmin\model;

class Job extends Model
{
    protected $name = 'jobs';

    protected $append = ['summary_text'];

    public function getSummaryTextAttr()
    {
        $summary = strip_tags($this->getAttr('content'));

        return $summary;
    }

    public function resumes()
    {
        return $this->hasMany(JobResume::class, 'job_id', 'id');
    }
}

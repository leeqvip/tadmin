<?php

namespace tadmin\service\auth\guard\traits;

trait GuardHelper
{
    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return null !== $this->user();
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        if ($this->user()) {
            return $this->user()->getAuthIdentifier();
        }
    }
}

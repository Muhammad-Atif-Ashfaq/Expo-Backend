<?php

namespace App\Interfaces\Admin;

interface ContestStartInterface
{
    public function index($contestId);
    public function recordWithScoreFields($contestId);

}

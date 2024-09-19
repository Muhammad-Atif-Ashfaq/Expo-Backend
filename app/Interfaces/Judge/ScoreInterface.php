<?php

namespace App\Interfaces\Judge;

interface ScoreInterface
{
    public function save_score(array $data);

    public function check_score();
}

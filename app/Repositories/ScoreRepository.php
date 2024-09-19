<?php

namespace App\Repositories;
use App\Models\Score;
use App\Enums\UserRolesEnum;
use App\Helpers\UploadFiles;
use Hash;
use App\Interfaces\Judge\ScoreInterface;

class ScoreRepository implements ScoreInterface
{
    private $model;

    public function __construct(Score $model)
    {
        $this->model = $model;
    }

    public function save_score(array $data)
    {
        $score = $this->model::create([
            'contest_id' => $data['contest_id'],
            'judge_id'   => $data['judge_id'],
            'participant_id'  =>  $data['participant_id'],
            'score'      => $data['score'],
        ]);
        return $score;
    }

    public function check_score()
    {
        return $this->model::where('judge_id', auth()->user()->id)->get();
    }

}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Admin\ScoreRequest;
use App\Helpers\ExceptionHandlerHelper;
use App\Repositories\ScoreRepository;

class ScoreController extends Controller
{
    private $scoreRepository;

    public function __construct(ScoreRepository $scoreRepository)
    {
        $this->scoreRepository = $scoreRepository;
    }

    public function save_score(ScoreRequest $request)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($request) {
            $score = $this->scoreRepository->save_score($request->validated());
            return $this->sendResponse($score, 'Score Added Successfully');
        });
    }

    public function check_score()
    {
        return ExceptionHandlerHelper::tryCatch(function () {
            $score = $this->scoreRepository->check_score();
            return $this->sendResponse($score, 'All Score');
        });
    }
}

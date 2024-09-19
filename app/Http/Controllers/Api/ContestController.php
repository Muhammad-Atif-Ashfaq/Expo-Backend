<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Admin\{ContestRequest};
use App\Helpers\ExceptionHandlerHelper;
use App\Repositories\ContestRepository;

class ContestController extends Controller
{
    private $contestRepository;

    public function __construct(ContestRepository $contestRepository)
    {
        $this->contestRepository = $contestRepository;
    }

    public function index(Request $request)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($request) {
            $contest = $this->contestRepository->index($request);
            return $this->sendResponse($contest, 'All Contest');
        });
    }

    public function store(ContestRequest $request)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($request) {
        $contest = $this->contestRepository->store($request->validated());
            return $this->sendResponse($contest, 'Contest Store Successfully');
        });
    }

    public function show(string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($id) {
            $contest = $this->contestRepository->show($id);
            return $this->sendResponse($contest, 'Single Contest');
        });
    }

    public function update(ContestRequest $request, string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($request, $id) {
            $contest = $this->contestRepository->update($request->validated(), $id);
                return $this->sendResponse($contest, 'Contest Updated Successfully');
            });
    }

    public function destroy(string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($id) {
            $contest = $this->contestRepository->destroy($id);
            return $this->sendResponse($contest, 'Single Contest Deleted');
        });
    }


   public function userScreenContestInfo(Request $request,$id){
    return ExceptionHandlerHelper::tryCatch(function () use($id) {
        $contest = $this->contestRepository->show($id);
        return $this->sendResponse($contest, 'Single Contest');
    });
   }


   public function allExpoContests($id)
   {
       try {
           $expo = $this->contestRepository->getAllContest($id);
           if ($expo->isEmpty()) {
               return response()->json(['error' => 'No contests found'], 404);
           }
           return response()->json($expo, 200);

       } catch (\Exception $e) {
           return response()->json(['error' => 'Failed to fetch contests'], 500);
       }
   }
}

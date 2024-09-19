<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Repositories\JudgeRepository;
use App\Helpers\ExceptionHandlerHelper;
use App\Http\Requests\Api\Admin\ContestRequest;
use App\Http\Requests\Api\Admin\{JudgeRequest};

class JudgeController extends Controller
{
    private $judgeRepository;

    public function __construct(JudgeRepository $judgeRepository)
    {
        $this->judgeRepository = $judgeRepository;
    }

    public function index(Request $request)
    {
        $judges = $this->judgeRepository->index($request->contest_id);

        if ($judges->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No judges found',
                'payload' => []
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'All Judges',
            'payload' => $judges
        ], 200);
    }

    public function store(JudgeRequest $request)
    {
        try {
            // Validate the incoming request
            $validatedData = $request->validated();

            // Store the judge data
            $judge = $this->judgeRepository->store($validatedData);

            // Return a successful response
            return $this->sendResponse($judge, 'Judges Store Successfully');
        } catch (\Exception $e) {
            // Log the error details
            Log::error('Failed to store judge:', [
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);

            // Return an error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to store judge',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show(string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($id) {
            $judge = $this->judgeRepository->show($id);
            return $this->sendResponse($judge, 'Single Judges');
        });
    }

    public function update(ContestRequest $request, string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($request, $id) {
            $judge = $this->judgeRepository->update($request->validated(), $id);
                return $this->sendResponse($judge, 'Judges Updated Successfully');
            });
    }

    public function destroy(string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($id) {
            $judge = $this->judgeRepository->destroy($id);
            return $this->sendResponse($judge, 'Single Judges Deleted');
        });
    }

}

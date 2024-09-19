<?php

namespace App\Http\Controllers\Api;

use App\Models\Score;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Repositories\ContestStartRepository;
use App\Http\Requests\Api\Admin\ScoreRequest;
use App\Http\Requests\Api\Admin\ApproveJudgeScoreRequest;

class ContestStartController extends Controller
{
    protected $contestStartRepository;

    public function __construct(ContestStartRepository $contestStartRepository)
    {
        $this->contestStartRepository = $contestStartRepository;
    }

    public function index($contestId)
    {
        try {
            $data = $this->contestStartRepository->index($contestId);

            if (empty($data['participants']) && empty($data['judges'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'No participants or judges found for this contest',
                    'data' => $data,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Participants and Judges retrieved successfully',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving contest data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving contest data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function judgeParticipant(Request $request, $contestId)
    {
        try {
            $request->validate([
                'participant_id' => 'required|exists:participients,id',
            ]);

            $result = $this->contestStartRepository->judgeParticipant($request, $contestId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Participant assigned to judges successfully',
                    'participant' => $result['participant'],
                    'scorecard' => $result['scorecard'],
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], $result['code']);
            }
        } catch (\Exception $e) {
            Log::error('Error assigning participant to judges: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while assigning the participant to judges',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function submitScore(ScoreRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $result = $this->contestStartRepository->submitScore($validatedData);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Score submitted successfully',
                    'totalScore' => $result['data']['total_score'],
                    'judge_id' => $result['data']['judge_id'],
                    'participant_id' => $result['data']['participant_id']
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'error' => $result['error'],
                ], $result['code']);
            }
        } catch (\Exception $e) {
            Log::error('Error submitting score: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit score',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function publishRecord($contestId)
    {
        $result = $this->contestStartRepository->publishRecord($contestId);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }
    }

    // ContestStartController.php

    public function getContestRecordsWithPositions($contestId)
    {
        try {
            $result = $this->contestStartRepository->getContestRecordsWithPositions($contestId);

            // Check if the participants data is empty
            if (empty($result['participants'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'No contest records found',
                    'data' => [],
                    'tied' => false,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contest records retrieved successfully',
                'data' => $result['participants'],
                'tied' => $result['tied'],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving contest records: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contest records',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function initiateRematch(Request $request, $contestId)
    {
        try {
            $tiedParticipants = $this->contestStartRepository->getTiedParticipants($contestId);

            if (empty($tiedParticipants)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tied participants found for rematch.',
                ], 200);
            }

            $this->contestStartRepository->initiateRematch($contestId, $tiedParticipants);

            return response()->json([
                'success' => true,
                'message' => 'Rematch initiated successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error initiating rematch: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate rematch',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    public function generateIframeLink(Request $request, $contestId)
    {
        try {
            $iframeLink = $this->contestStartRepository->generateIframeLink($contestId);

            return response()->json([
                'success' => true,
                'iframe_link' => $iframeLink
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error generating iframe link: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate iframe link',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function behindScreenResult($contestId){
        try {

            $totalScores = Score::where('contest_id', $contestId)->count();
            $publishedScores = Score::where('contest_id', $contestId)
                ->where('is_published', 1)
                ->count();

                $status = ($totalScores > 0 && $totalScores === $publishedScores);
            $data = $this->contestStartRepository->recordWithScoreFields($contestId);

            if (empty($data['participants']) && empty($data['judges'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'No participants or judges found for this contest',
                    'data' => $data,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'status'=>$status,
                'publish'=>$status,
                'message' => 'Participants and Judges retrieved successfully',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving contest data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving contest data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function approveJudgeScores(ApproveJudgeScoreRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $data = $this->contestStartRepository->approveJudgeScore($validatedData);

            if ($data['statusUpdated']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Score for Participant Approved and Status Updated',
                    'data' => $data,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'all judges have not given scores yet',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving contest data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving contest data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function behindScreenResultAfterApprove(Request $request)
{
    try {
        $contestId = $request->input('contest_id');
        $participantId = $request->input('participant_id');

        // Update or create the status record
        Status::updateOrCreate(
            ['contest_id' => $contestId, 'participant_id' => $participantId],
            ['status' => 1]
        );

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error updating status: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while updating the status.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

}

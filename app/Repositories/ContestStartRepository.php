<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Score;
use App\Models\ScoreCard;
use App\Models\FileUpload;
use App\Models\IframeLink;
use App\Enums\UserRolesEnum;
use App\Events\ScoreUpdated;
use App\Helpers\UploadFiles;
use App\Models\Participient;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\Admin\ContestStartInterface;
use App\Models\Status;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContestStartRepository implements ContestStartInterface
{
    public function index($contestId)
    {
        $participants = Participient::where('contest_id', $contestId)
            ->where('is_judged', 0)
            ->get();

        $judges = User::where('contest_id', $contestId)
            ->where('role', UserRolesEnum::JUDGE)
            ->get();

        $scoreCard = ScoreCard::where('contest_id', $contestId)->first();

        $participantScores = Score::where('contest_id', $contestId)
            ->select('participant_id', 'judge_id', DB::raw('SUM(score) as total_score'))
            ->groupBy('participant_id', 'judge_id')
            ->with(['participant', 'judge'])
            ->get();

        $scoresByJudge = [];
        foreach ($participantScores as $score) {
            $scoresByJudge[$score->judge_id][$score->participant_id] = $score->total_score;
        }

        // Count total scores and published scores

        return [
            'participants' => $participants,
            'judges' => $judges,
            'now_in_progress' => $scoreCard->current_participant_name ?? '',
            'total_scores' => $participantScores,
            'scores_by_judge' => $scoresByJudge
        ];
    }



    public function recordWithScoreFields($contestId)
    {
        $participants = Participient::where('contest_id', $contestId)
            ->where('is_judged', 0)
            ->get();
        $judges = User::where('contest_id', $contestId)
            ->where('role', UserRolesEnum::JUDGE)
            ->get();

        $scoreCard = ScoreCard::where('contest_id', $contestId)->first();

        $status = Status::where('contest_id', $contestId)
        ->where('status', 1)
        ->where('participant_id', $scoreCard->current_participant_id)
        ->first();

    $Status = false;

    if ($status) {
        $Status = true;
    }

        $participantScores = Score::where('contest_id', $contestId)
            ->select('participant_id', 'judge_id', 'field_name', DB::raw('SUM(score) as total_score'))
            ->groupBy('participant_id', 'judge_id', 'field_name')
            ->with(['participant', 'judge'])
            ->get();
        $scoresByJudge = [];
        $totalScoresByParticipant = [];
        foreach ($participantScores as $score) {
            $scoresByJudge[$score->judge_id][$score->participant_id][$score->field_name] = $score->total_score;
            if (!isset($totalScoresByParticipant[$score->participant_id])) {
                $totalScoresByParticipant[$score->participant_id] = 0;
            }
            $totalScoresByParticipant[$score->participant_id] += $score->total_score;
        }
        $files = FileUpload::where('contest_id', $contestId)->get();

        // Map files to include URLs
        $filesWithUrls = $files->map(function ($file) {
            $file->file_url = url('storage/uploads/' . $file->file);
            return $file;
        });


        return [
            'status' => $Status,
            'participants' => $participants,
            'judges' => $judges,
            'scoreCard' => $scoreCard,
            'now_in_progress' => $scoreCard->current_participant_name ?? '',
            'total_scores' => $participantScores,
            'scores_by_judge' => $scoresByJudge,
            'total_scores_by_participant' => $totalScoresByParticipant,
            'files' => $filesWithUrls,
        ];
    }



    public function judgeParticipant($request, $contestId)
    {
        $participantId = $request->participant_id;

        $participant = Participient::where('contest_id', $contestId)
            ->where('id', $participantId)
            ->firstOrFail();

        $fieldsValues = json_decode($participant->fields_values, true);
        $participantName = $fieldsValues['name'] ?? 'Unknown';


        $scoreCard = ScoreCard::where('contest_id', $contestId)->firstOrFail();
        $scoreCard->update([
            'current_participant_name' => $participantName,
            'current_participant_id' => $participant->id,
        ]);

        return [
            'success' => true,
            'participant' => $participant,
            'scorecard' => $scoreCard,
        ];
    }

    public function submitScore(array $validatedData)
    {

        $participant = Participient::findOrFail($validatedData['participant_id']);
        $judge = User::findOrFail($validatedData['judge_id']);


        foreach ($validatedData['scores'] as $scoreData) {
            $score = Score::create([
                'judge_id' => $validatedData['judge_id'],
                'participant_id' => $validatedData['participant_id'],
                'contest_id' =>  $validatedData['contest_id'],
                'field_name' => $scoreData['field_name'],
                'score' => $scoreData['score'],
            ]);
        }

        $totalScore = Score::where('participant_id', $validatedData['participant_id'])
            ->sum('score');
        broadcast(new ScoreUpdated($participant->id, $judge->id, $totalScore));
        return [
            'success' => true,
            'message' => 'Score submitted successfully',
            'data' => [
                'judge_id' => $validatedData['judge_id'],
                'participant_id' => $validatedData['participant_id'],
                'total_score' => $totalScore,
            ],
        ];
    }



    public function publishRecord($contestId)
    {
        // Count the total number of judges in the contest
        $totalJudges = User::where('contest_id', $contestId)
            ->where('role', UserRolesEnum::JUDGE)
            ->count();

        // Get all participants
        $participants = Participient::where('contest_id', $contestId)->get();

        // Check if each participant has been scored by all judges
        foreach ($participants as $participant) {
            $scoresByJudges = Score::where('contest_id', $contestId)
                ->where('participant_id', $participant->id)
                ->distinct('judge_id')
                ->count('judge_id');

            // Debugging information
            logger("Participant ID: {$participant->id}, Scores by Judges: $scoresByJudges");

            if ($scoresByJudges < $totalJudges) {
                return [
                    'success' => false,
                    'message' => 'Not all judges have given scores to all participants yet.',
                ];
            }
        }
        Score::where('contest_id', $contestId)
            ->update(['is_published' => 1]);

        $iframeLink = $this->IframeLink($contestId);

        IframeLink::create([
            'contest_id' => $contestId,
            'iframe_link' => $iframeLink,
        ]);
        return [
            'success' => true,
            'message' => 'All judges have given scores to all participants. Record published.',
        ];
    }

    public function calculateParticipantScores($contestId)
    {
        $participantScores = Score::where('contest_id', $contestId)
            ->select('participant_id', DB::raw('SUM(score) as total_score'))
            ->groupBy('participant_id')
            ->with('participant')
            ->get();

        return $participantScores;
    }

    public function getContestRecords($contestId)
    {
        $participantScores = $this->calculateParticipantScores($contestId);

        $sortedParticipants = $participantScores->sortByDesc('total_score')->values();

        return $sortedParticipants;
    }

    public function getContestRecordsWithPositions($contestId)
    {
        $sortedParticipants = $this->getContestRecords($contestId);

        $rank = 1;
        $previousScore = null;
        $tiedParticipants = [];
        $results = [];

        foreach ($sortedParticipants as $participant) {
            if ($previousScore !== null && $participant->total_score === $previousScore) {
                $tiedParticipants[] = $participant;
            } else {
                $participant->position = $rank++;
            }

            $previousScore = $participant->total_score;
            $results[] = $participant;
        }

        return [
            'participants' => $results,
            'tied' => !empty($tiedParticipants)
        ];
    }

    public function getTiedParticipants($contestId)
    {
        $participantScores = $this->calculateParticipantScores($contestId);

        $tiedScores = $participantScores->groupBy('total_score')
            ->filter(function ($group) {
                return $group->count() > 1;
            })
            ->collapse();
        return $tiedScores;
    }

    public function initiateRematch($contestId, $tiedParticipants)
    {
        foreach ($tiedParticipants as $participant) {
            Score::where('participant_id', $participant->participant_id)->delete();
            $participantRecord = Participient::where('id', $participant->participant_id)->first();
            $participantRecord->update(['is_judged' => 0]);
        }
    }


    public function generateIframeLink($contestId)
    {
        $link = IframeLink::where('contest_id', $contestId)->first();

        if (!$link) {
            throw new \Exception('Link not found for this contest ID');
        }

        return '<iframe src="' . $link->iframe_link . '" width="100%" height="500px"></iframe>';
    }

    public function approveJudgeScore(array $validatedData)
    {
        $participant = Participient::findOrFail($validatedData['participant_id']);
        $totalJudges = User::where('contest_id', $participant->contest_id)
            ->where('role', UserRolesEnum::JUDGE)
            ->count();

        $submittedJudges = Score::where('participant_id', $validatedData['participant_id'])
            ->distinct('judge_id')
            ->count('judge_id');

        $statusUpdated = false;

        if ($submittedJudges >= $totalJudges) {
            $participant->update([
                'is_judged' => 1
            ]);
            $statusUpdated = true;
        }

        return [
            'statusUpdated' => $statusUpdated,
            'participants' => Participient::where('contest_id', $participant->contest_id)->get(),
            'judges' => User::where('contest_id', $participant->contest_id)
                ->where('role', UserRolesEnum::JUDGE)
                ->get(),
        ];
    }

    // Method to generate iframe link
    protected function IframeLink($contestId)
    {
        return "https://frontend.saeedantechpvt.com/public_all-records/{$contestId}";
    }



    // public function recordWithScoreFieldsUpdated($contestId)
    // {
    //     $participants = Participient::where('contest_id', $contestId)
    //         ->where('is_judged', 1)
    //         ->get();
    //     $judges = User::where('contest_id', $contestId)
    //         ->where('role', UserRolesEnum::JUDGE)
    //         ->get();

    //     $scoreCard = ScoreCard::where('contest_id', $contestId)->first();


    //     $participantScores = Score::where('contest_id', $contestId)
    //         ->select('participant_id', 'judge_id', 'field_name', DB::raw('SUM(score) as total_score'))
    //         ->groupBy('participant_id', 'judge_id', 'field_name')
    //         ->with(['participant', 'judge'])
    //         ->get();
    //     $scoresByJudge = [];
    //     $totalScoresByParticipant = [];
    //     foreach ($participantScores as $score) {
    //         $scoresByJudge[$score->judge_id][$score->participant_id][$score->field_name] = $score->total_score;
    //         if (!isset($totalScoresByParticipant[$score->participant_id])) {
    //             $totalScoresByParticipant[$score->participant_id] = 0;
    //         }
    //         $totalScoresByParticipant[$score->participant_id] += $score->total_score;
    //     }
    //     $files = FileUpload::where('contest_id', $contestId)->get();

    //     // Map files to include URLs
    //     $filesWithUrls = $files->map(function ($file) {
    //         $file->file_url = url('storage/uploads/' . $file->file);
    //         return $file;
    //     });


    //     return [
    //         'participants' => $participants,
    //         'judges' => $judges,
    //         'now_in_progress' => $scoreCard->current_participant_name ?? '',
    //         'total_scores' => $participantScores,
    //         'scores_by_judge' => $scoresByJudge,
    //         'total_scores_by_participant' => $totalScoresByParticipant,
    //         'files' => $filesWithUrls,
    //     ];
    // }
}

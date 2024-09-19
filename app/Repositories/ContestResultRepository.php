<?php

namespace App\Repositories;
use App\Models\Expo;
use App\Models\Score;
use App\Models\Status;
use App\Enums\UserRolesEnum;
use App\Models\Participient;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Admin\ExpoInterface;
use App\Interfaces\Admin\ContestResultInterface;

class ContestResultRepository implements ContestResultInterface
{

    public function manuallyRematch(array $data)
    {
        foreach ($data['participant_id'] as $participantId) {
            Score::where('participant_id', $participantId)->delete();
            Status::where('participant_id', $participantId)->delete();
            $participantRecord = Participient::find($participantId);
            if ($participantRecord) {
                $participantRecord->update(['is_judged' => 0]);
            }
        }
    }



public function calculateParticipantScores($contestId)
{
    $participantScores = Score::where('contest_id', $contestId)
    ->where('is_published',1)
        ->select('participant_id', DB::raw('SUM(score) as total_score'),'is_published')
        ->groupBy('participant_id','is_published')
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

}

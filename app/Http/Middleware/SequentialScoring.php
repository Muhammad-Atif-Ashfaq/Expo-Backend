<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Enums\UserRolesEnum;
use Illuminate\Http\Request;
use App\Models\Score;
use Symfony\Component\HttpFoundation\Response;

class SequentialScoring
{
    public function handle($request, Closure $next)
    {
        $participantId = $request->participant_id;
        $contestId = $request->contest_id;
        $currentJudgeId = auth()->user()->id;
        $judgeSequence = User::where('contest_id', $contestId)
            ->where('role', UserRolesEnum::JUDGE)
            ->orderBy('created_at', 'asc')
            ->pluck('id')
            ->toArray();
        $currentJudgeIndex = array_search($currentJudgeId, $judgeSequence);
        if ($currentJudgeIndex === false) {
            return response()->json(['error' => 'You are not authorized to score this participant.'], 403);
        }
        $scores = Score::where('participant_id', $participantId)
            ->whereIn('judge_id', $judgeSequence)
            ->orderByRaw('FIELD(judge_id, ' . implode(',', $judgeSequence) . ')')
            ->get();
        if ($currentJudgeIndex > 0) {
            $previousJudgeId = $judgeSequence[$currentJudgeIndex - 1];
            $previousJudgeScore = $scores->where('judge_id', $previousJudgeId)->first();
            if (!$previousJudgeScore) {
                return response()->json(['error' => 'You cannot score this participant yet.'], 403);
            }
        }
        return $next($request);
    }
}

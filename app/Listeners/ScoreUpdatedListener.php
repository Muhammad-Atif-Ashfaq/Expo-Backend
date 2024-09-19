<?php

namespace App\Listeners;

use App\Events\ScoreUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Pusher\Pusher;

class ScoreUpdatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\ScoreUpdated  $event
     * @return void
     */
    public function handle(ScoreUpdated $event)
    {
        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => true,
            ]
        );


        $data = [
            'participant_id' => $event->participantId,
            'judge_id' => $event->judgeId,
            'total_score' => $event->totalScore,
        ];
        $pusher->trigger('participant.' . $event->participantId, 'score-updated', $data);
    }
}

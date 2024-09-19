<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScoreUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $participantId;
    public $judgeId;
    public $totalScore;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $participantId, int $judgeId, float $totalScore)
    {
        $this->participantId = $participantId;
        $this->judgeId = $judgeId;
        $this->totalScore = $totalScore;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('participant.' . $this->participantId);
    }

    public function broadcastWith()
    {
        return [
            'judge_id' => $this->judgeId,
            'total_score' => $this->totalScore,
        ];
    }
}

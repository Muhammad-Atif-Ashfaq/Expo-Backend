<?php

namespace App\Repositories;
use App\Models\{Participient, Contest};
use App\Enums\UserRolesEnum;
use App\Interfaces\Admin\ParticipientInterface;
use Carbon\Carbon;

class ParticipientRepository implements ParticipientInterface
{
    private $model;
    private $contest;

    public function __construct(Participient $model, Contest $contest)
    {
        $this->model = $model;
        $this->contest = $contest;
    }

    public function index($request)
    {
        $participient = $this->model::where('contest_id', $request->contest_id)
                             ->get();
        return $participient;
    }

    public function show(string $id)
    {
        $participient = $this->model::findOrFail($id);
        return $participient;
    }

    public function store(array $data)
    {
        $contest = $this->contest::findOrFail($data['contest_id']);
        $participantCount = $this->model::where('contest_id', $data['contest_id'])->count();
        
        if ($contest->max_contestent > $participantCount) {
            if ($contest->end_date_time > Carbon::now()) {
                $participant = $this->model::create([
                    'contest_id' => $data['contest_id'],
                    'fields_values' => json_encode($data['fields_values']),
                ]);
                return [
                    'success' => true,
                    'participant' => $participant,
                    'message' => 'Participant registered successfully',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Registration Date is Expired',
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Maximum registration for this contest is completed',
            ];
        }
    }

    public function update(array $data, $id)
    {
        $participient = $this->model::findOrFail($id);
        $update = $participient->update([
            'fields_values'  => $data['fields_values']
        ]);
        return $participient;
    }

    public function destroy(string $id)
    {
        $participient = $this->model::findOrFail($id)->delete();
        return true;
    }

}
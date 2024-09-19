<?php

namespace App\Repositories;
use Carbon\Carbon;
use App\Models\Contest;
use App\Enums\UserRolesEnum;
use App\Helpers\UploadFiles;
use App\Interfaces\Admin\ContestInterface;

class ContestRepository implements ContestInterface
{
    private $model;

    public function __construct(Contest $model)
    {
        $this->model = $model;
    }

    public function index($request)
    {
        $expo = $this->model::where('expo_id', $request->expo_id)->get();
        return $expo;
    }

    public function show(string $id)
    {
        $expo = $this->model::findOrFail($id);
        return $expo;
    }

    public function store(array $data)
    {
        $expo = $this->model::create([
            'expo_id' => $data['expo_id'],
            'name' => $data['name'],
            'start_date_time' => $data['start_date_time'],
            'end_date_time'   => $data['end_date_time'],
            'max_contestent' => $data['max_contestent']
        ]);
        return $expo;
    }

    public function update(array $data, $id)
    {
        $expo = $this->model::findOrFail($id);
        $update = $expo->update([
            'name' => $data['name'] ?? $expo->name,
            'start_date_time' => $data['start_date_time'] ?? $expo->start_date_time,
            'end_date_time'   => $data['end_date_time'] ?? $expo->end_date_time,
            'max_contestent' => $data['max_contestent'] ?? $expo->max_contestent
        ]);
        return $expo;
    }

    public function destroy(string $id)
    {
        $expo = $this->model::findOrFail($id)->delete();
        return true;
    }
    public function getAllContest($id)
    {
        $contests = Contest::where('expo_id', $id)
            ->select('id', 'name', 'start_date_time', 'end_date_time')
            ->orderBy('created_at', 'desc')
            ->setEagerLoads([])
            ->get();

        $contests->transform(function ($contest) {
            $contest->start_date_time = Carbon::parse($contest->start_date_time)->format('M d, Y h:i:s A');
            $contest->end_date_time = Carbon::parse($contest->end_date_time)->format('M d, Y h:i:s A');
            return $contest;
        });
        return $contests;
    }

}

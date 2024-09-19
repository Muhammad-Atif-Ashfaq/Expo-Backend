<?php

namespace App\Repositories;
use Carbon\Carbon;
use App\Models\Expo;
use App\Enums\UserRolesEnum;
use App\Interfaces\Admin\ExpoInterface;

class ExpoRepository implements ExpoInterface
{
    private $model;

    public function __construct(Expo $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        $expo = $this->model::where('user_id', auth()->user()->id)->get();
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
            'user_id' => auth()->user()->id,
            'name' => $data['name']
        ]);
        return $expo;
    }

    public function update(array $data, $id)
    {
        $expo = $this->model::findOrFail($id);
        $update = $expo->update([
            'name' => $data['name'] ?? $expo->name
        ]);
        return $expo;
    }

    public function destroy(string $id)
    {
        $expo = $this->model::findOrFail($id)->delete();
        return true;
    }


    public function getAllEvents($perPage = 10)
    {
        $events = $this->model::select('id', 'name', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $events->getCollection()->transform(function ($event) {
            $event->created_at = Carbon::parse($event->created_at)->format('M d, Y h:i:s A');
            return $event;
        });

        return $events;
    }
}

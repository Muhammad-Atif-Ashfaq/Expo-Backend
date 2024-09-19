<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Admin\{ExpoRequest};
use App\Helpers\ExceptionHandlerHelper;
use App\Repositories\ExpoRepository;

class ExpoController extends Controller
{
    private $expoRepository;

    public function __construct(ExpoRepository $expoRepository)
    {
        $this->expoRepository = $expoRepository;
    }

    public function index()
    {
        return ExceptionHandlerHelper::tryCatch(function () {
            $expo = $this->expoRepository->index();
            return $this->sendResponse($expo, 'All Expo');
        });
    }

    public function store(ExpoRequest $request)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($request) {
        $expo = $this->expoRepository->store($request->validated());
            return $this->sendResponse($expo, 'Expo Store Successfully');
        });
    }

    public function show(string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($id) {
            $expo = $this->expoRepository->show($id);
            return $this->sendResponse($expo, 'single Expo');
        });
    }

    public function update(ExpoRequest $request, string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($request, $id) {
            $expo = $this->expoRepository->update($request->validated(), $id);
                return $this->sendResponse($expo, 'Expo Updated Successfully');
            });
    }

    public function destroy(string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($id) {
            $expo = $this->expoRepository->destroy($id);
            return $this->sendResponse($expo, 'Single Expo Deleted');
        });
    }


    public function allEvents()
    {
        try {
            $expo = $this->expoRepository->getAllEvents();
            if ($expo->isEmpty()) {
                return response()->json(['error' => 'No events found'], 404);
            }
            return response()->json($expo, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch events'], 500);
        }
    }

}

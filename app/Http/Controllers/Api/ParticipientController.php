<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Admin\{ParticipientRequest};
use App\Helpers\ExceptionHandlerHelper;
use App\Repositories\ParticipientRepository;

class ParticipientController extends Controller
{
    private $participientRepository;

    public function __construct(ParticipientRepository $participientRepository)
    {
        $this->participientRepository = $participientRepository;
    }

    public function index(Request $request)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($request) {
            $participient = $this->participientRepository->index($request);
            return $this->sendResponse($participient, 'All participients');
        });
    }

    public function store(ParticipientRequest $request)
    {
        return ExceptionHandlerHelper::tryCatch(function () use ($request) {
            $response = $this->participientRepository->store($request->validated());
            
            if ($response['success']) {
                return $this->sendResponse($response['participant'], $response['message']);
            } else {
                return $this->sendError($response['message'], 400);
            }
        });
    }

    public function show(string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($id) {
            $participient = $this->participientRepository->show($id);
            return $this->sendResponse($participient, 'Single participients');
        });
    }

    public function update(ParticipientRequest $request, string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($request, $id) {
                $participient = $this->participientRepository->update($request->validated(), $id);
                return $this->sendResponse($participient, 'participients Updated Successfully');
            });
    }

    public function destroy(string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($id) {
            $participient = $this->participientRepository->destroy($id);
            return $this->sendResponse($participient, 'Single participients Deleted');
        });
    }
}
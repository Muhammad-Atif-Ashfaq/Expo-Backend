<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Admin\{FormFieldRequest, FormFieldIndex};
use App\Helpers\ExceptionHandlerHelper;
use App\Repositories\FormFieldRepository;

class FormFieldController extends Controller
{
    private $formFieldRepository;

    public function __construct(FormFieldRepository $formFieldRepository)
    {
        $this->formFieldRepository = $formFieldRepository;
    }

    public function index(FormFieldIndex $request)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($request) {
            $formField = $this->formFieldRepository->index($request->validated());
            return $this->sendResponse($formField, 'All Form Field');
        });
    }

    public function store(FormFieldRequest $request)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($request) {
            $formField = $this->formFieldRepository->store($request->validated());
            return $this->sendResponse($formField, 'Form Field Store Successfully');
        });
    }

    public function show(string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($id) {
            $formField = $this->formFieldRepository->show($id);
            return $this->sendResponse($formField, 'Single Form Field');
        });
    }

    public function update(FormFieldRequest $request, string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($request, $id) {
            $formField = $this->formFieldRepository->update($request->validated(), $id);
                return $this->sendResponse($formField, 'Form Field Updated Successfully');
            });
    }

    public function destroy(string $id)
    {
        return ExceptionHandlerHelper::tryCatch(function () use($id) {
            $formField = $this->formFieldRepository->destroy($id);
            return $this->sendResponse($formField, 'Single Form Field Deleted');
        });
    }
}

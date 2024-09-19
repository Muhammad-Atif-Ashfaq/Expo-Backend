<?php

namespace App\Repositories;
use App\Models\FormField;
use App\Enums\UserRolesEnum;
use App\Helpers\UploadFiles;
use Hash;
use App\Interfaces\Admin\FormFieldInterface;

class FormFieldRepository implements FormFieldInterface
{
    private $model;

    public function __construct(FormField $model)
    {
        $this->model = $model;
    }

    public function index($data)
    {
        $formField = $this->model::where('contest_id', $data['contest_id'])
                             ->get();
        return $formField;
    }

    public function show(string $id)
    {
        $formField = $this->model::findOrFail($id);
        return $formField;
    }

    public function store(array $data)
    {
        foreach($data['formData'] as $data)
        {
            $formField = $this->model::create([
                'admin_id' => auth()->user()->id,
                'contest_id' => $data['contest_id'],
                'name' => $data['name'],
                'type' => $data['type'],
                'label'=> $data['label'],
                'required' => ($data['required'] == 'yes' ? true:false),
                'is_important' => $data['is_important'] == 'yes' ? true : false
            ]);
        }
        
        return true;
    }

    public function update(array $data, $id)
    {
        $formField = $this->model::findOrFail($id);
        $update = $formField->update([
            'contest_id' => $data['contest_id'] ?? $formField->contest_id,
            'name' => $data['name'] ?? $formField->name,
            'type' => $data['type'] ?? $formField->type,
            'label'=> $data['label'] ?? $formField->label,
            'required' => ($data['required'] == 'yes' ? true:false) ?? $formField->required,
            'is_important' => ($data['is_important'] == 'yes' ? true : false) ?? $formField->is_important
        ]);
        return $formField;
    }

    public function destroy(string $id)
    {
        $formField = $this->model::findOrFail($id)->delete();
        return true;
    }

}
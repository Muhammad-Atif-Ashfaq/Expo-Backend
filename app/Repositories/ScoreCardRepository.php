<?php
namespace App\Repositories;
use App\Models\FormField;
use App\Models\ScoreCard;
use App\Models\Participient;
use Illuminate\Support\Facades\Log;
use App\Interfaces\Admin\ScoreCardInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\ScoreCardRepositoryInterface;

class ScoreCardRepository implements ScoreCardInterface
{
    public function getAll($contestId)
    {
        $userId = auth()->user()->id;

        $scoreCard = ScoreCard::where('contest_id', $contestId)
            ->with(['judges' => function ($query) use ($userId) {
                $query->where('users.id', $userId);
            }])
            ->first();

        if (!$scoreCard) {
            return response()->json(['error' => 'Score card not found'], 404);
        }
        $formFields = FormField::where('contest_id', $contestId)
            ->where('is_important', 0)
            ->get();

        if ($formFields->isEmpty()) {
            return response()->json(['error' => 'No form fields found'], 404);
        }
        $participant = Participient::find($scoreCard->current_participant_id);

        if (!$participant) {
            return response()->json(['error' => 'Participant not found'], 404);
        }
        $fieldsValues = json_decode($participant->fields_values, true);

        if ($fieldsValues === null) {
            return response()->json(['error' => 'Failed to decode participant data'], 500);
        }


        $participantData = [];
        foreach ($formFields as $field) {
            $fieldName = $field->name;
            if (isset($fieldsValues[$fieldName])) {
                $participantData[$fieldName] = $fieldsValues[$fieldName];
            } else {
                $participantData[$fieldName] = null;
            }
        }

        return response()->json([
            'scoreCard' => $scoreCard,
            'formFields' => $formFields,
            'participantData' => $participantData,
        ]);
    }

    public function judgeScoreCard($contestId){
        $userId = auth()->user()->id;

        $scoreCard = ScoreCard::where('contest_id', $contestId)
            ->with(['judges' => function ($query) use ($userId) {
                $query->where('users.id', $userId);
            }])
            ->first();

        if (!$scoreCard) {
            return response()->json(['error' => 'Score card not found'], 404);
        }
        $formFields = FormField::where('contest_id', $contestId)
            ->where('is_important', 0)
            ->get();

        if ($formFields->isEmpty()) {
            return response()->json(['error' => 'No form fields found'], 404);
        }
        $participant = Participient::find($scoreCard->current_participant_id);

        if (!$participant) {
            return response()->json(['error' => 'Participant not found'], 404);
        }
        $fieldsValues = json_decode($participant->fields_values, true);

        if ($fieldsValues === null) {
            return response()->json(['error' => 'Failed to decode participant data'], 500);
        }


        $participantData = [];
        foreach ($formFields as $field) {
            $fieldName = $field->name;
            if (isset($fieldsValues[$fieldName])) {
                $participantData[$fieldName] = $fieldsValues[$fieldName];
            } else {
                $participantData[$fieldName] = null;
            }
        }

        return response()->json([
            'scoreCard' => $scoreCard,
            'formFields' => $formFields,
            'participantData' => $participantData,
        ]);
    }


 public function findById(int $id): ? ScoreCard
 {
     return ScoreCard::findOrFail($id);
 }

 public function create(array $data): ScoreCard
 {
     return ScoreCard::create($data);
 }

 public function update(int $id, array $data): bool
 {
     $scoreCard = $this->findById($id);
     if ($scoreCard) {
         return $scoreCard->update($data);
     }
     return false;
 }

 public function delete(int $id): bool
 {
     $scoreCard = $this->findById($id);
     if ($scoreCard) {
         return $scoreCard->delete();
     }
     return false;
 }
}

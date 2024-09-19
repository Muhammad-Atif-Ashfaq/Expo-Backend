<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Interfaces\Admin\ScoreCardInterface;
use Illuminate\Validation\ValidationException;
use App\Repositories\ScoreCardRepositoryInterface;
use App\Http\Requests\Api\Admin\ScoreCardStoreRequest;
use App\Http\Requests\Api\Admin\ScoreCardUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ScoreCardController extends Controller
{
    protected $scoreCardRepository;

    public function __construct(ScoreCardInterface $scoreCardRepository)
    {
        $this->scoreCardRepository = $scoreCardRepository;
    }

    public function index($id)
    {
        try {
            $data = $this->scoreCardRepository->getAll($id);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch score cards', 'error' => $e->getMessage()], 500);
        }
    }


    public function judgeScoreCard($id){
        try {
            $data = $this->scoreCardRepository->judgeScoreCard($id);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch score cards', 'error' => $e->getMessage()], 500);
        }
    }



    public function store(ScoreCardStoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $names = is_array($validated['name']) ? $validated['name'] : [$validated['name']];

            $scoreCards = [];
            foreach ($names as $name) {
                $scoreCards[] = $this->scoreCardRepository->create([
                    'admin_id' => $validated['admin_id'],
                    'name' => $name,
                ]);
            }

            return response()->json($scoreCards, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create score cards', 'error' => $e->getMessage()], 500);
        }
    }


    public function show($id)
    {
        try {
            $scoreCard = $this->scoreCardRepository->findById($id);

            if (!$scoreCard) {
                return response()->json(['message' => 'ScoreCard not found'], 404);
            }

            return response()->json($scoreCard);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'ScoreCard not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch score card', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(ScoreCardUpdateRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            $updated = $this->scoreCardRepository->update($id, $validated);

            if (!$updated) {
                return response()->json(['message' => 'ScoreCard not found'], 404);
            }

            return response()->json(['message' => 'ScoreCard updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update score card', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->scoreCardRepository->delete($id);

            if (!$deleted) {
                return response()->json(['message' => 'ScoreCard not found'], 404);
            }

            return response()->json(['message' => 'ScoreCard deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'ScoreCard not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete score card', 'error' => $e->getMessage()], 500);
        }
    }
}

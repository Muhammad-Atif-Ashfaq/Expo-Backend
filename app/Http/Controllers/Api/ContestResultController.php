<?php

namespace App\Http\Controllers\Api;

use App\Mail\PDFMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Repositories\ContestResultRepository;
use App\Http\Requests\Api\Admin\ContestManualRematchRequest;

class ContestResultController extends Controller
{
    protected $contestResultRepository;

    public function __construct(ContestResultRepository $contestResultRepository)
    {
        $this->contestResultRepository = $contestResultRepository;
    }


    public function getPublicContestResult($contestId)
    {
        try {
            $result = $this->contestResultRepository->getContestRecordsWithPositions($contestId);
            if (empty($result['participants'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'No contest records found',
                    'data' => [],
                    'tied' => false,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contest records retrieved successfully',
                'data' => $result['participants'],
                'tied' => $result['tied'],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving contest records: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contest records',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function manuallyRematch(ContestManualRematchRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $this->contestResultRepository->manuallyRematch($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Participants rematched successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error rematching participants: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to rematch participants.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function sendPDF(Request $request)
    {

        $user=Auth::user();
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('uploads', 'public');
            Mail::to($user->email)->send(new PDFMail($filePath));

            return response()->json(['message' => 'PDF Sent To Organizer Successfully.']);
        }

        return response()->json(['error' => 'File not uploaded.'], 400);
    }
}

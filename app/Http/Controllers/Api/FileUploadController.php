<?php

namespace App\Http\Controllers\Api;

use App\Models\FileUpload;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240',
                'contest_id' => 'required|exists:contests,id'
            ]);

            // Check if there's already an uploaded file for this contest ID
            $existingFile = FileUpload::where('contest_id', $request->contest_id)->first();

            if ($existingFile) {
                // Delete the existing file from storage
                Storage::disk('public')->delete('uploads/' . $existingFile->file);
                // Delete the existing file record from database
                $existingFile->delete();
            }

            // Upload the new file
            $uploadedFile = $request->file('file');
            $fileName = time() . '_' . $uploadedFile->getClientOriginalName();
            $filePath = $uploadedFile->storeAs('uploads', $fileName, 'public');

            $file = new FileUpload();
            $file->admin_id = auth()->user()->id;
            $file->contest_id = $request->contest_id;
            $file->file = $fileName; // Store the file name

            $file->save();

            // Construct the URL for the uploaded file
            $fileUrl = url('storage/uploads/'.$fileName);

            return response()->json([
                'message' => 'File uploaded successfully',
                'file_url' => $fileUrl
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to upload file. Please try again later.'], 500);
        }
    }

}

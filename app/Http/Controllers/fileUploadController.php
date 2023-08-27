<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class fileUploadController extends Controller
{
    public function fileupload(Request $request)
    {
        $input = $request->all();
        $validation = Validator::make($input, [
            'file' => 'required|mimes:jpeg,jpg,png,mp4,avi,mov,mp3,wav,pdf,doc,docx,xls,xlsx|max:1048576',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors(), 'status_code' => 400], 400);
        } else {
            $fileName = $request->file->getClientOriginalName();
            $filePath = $fileName;
            $path = Storage::disk('s3')->put($filePath, file_get_contents($request->file));
            return response()->json(['status_code' => 200, 'message' => 'file successfully uploaded.']);
        }
    }

    public function getfiletemurl(Request $request)
    {
        $input = $request->all();
        $validation = Validator::make($input, [
            'filename' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors(), 'status_code' => 400], 400);
        } else {
            if (Storage::disk('s3')->exists($request->filename)) {
                $file = Storage::disk('s3')->temporaryUrl($request->filename, now()->addHour());
                return response()->json(['message' => 'success', 'data' => $file]);
            } else {
                return response()->json(['error' => 'file Not Found', 'status_code' => 400], 400);
            }
        }
    }

    public function multiplefileupload(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'files.*' => 'required|mimes:jpeg,jpg,png,mp4,avi,mov,mp3,wav,pdf,doc,docx,xls,xlsx|max:1048576',
            ]);

            if ($validation->fails()) {
                return response()->json(['error' => $validation->errors(), 'status_code' => 400], 400);
            } else {
                $files = $request->file('files');

                if (empty($files)) {
                    return response()->json(['message' => 'No files were uploaded.'], 400);
                }

                foreach ($files as $file) {
                    $fileName = $file->getClientOriginalName();
                    $uploaded = Storage::disk('s3')->put($fileName, file_get_contents($file));

                    if (!$uploaded) {
                        return response()->json(['message' => 'Failed to upload files.'], 500);
                    }

                    $path = Storage::disk('s3')->url($fileName);
                }

                return response()->json(['message' => 'Files have been successfully uploaded.']);
            }
        } catch (\Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            return response()->json(['message' => $message, 'status_code' => 500], 500);
        }
    }

    public function deletefile(Request $request)
    {
        $input = $request->all();
        $validation = Validator::make($input, [
            'filename' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors(), 'status_code' => 400], 400);
        } else {
            if (Storage::disk('s3')->exists($request->filename)) {
                Storage::disk('s3')->delete($request->filename);
                return response()->json(['message' => 'File deleted successfully']);
            } else {
                return response()->json(['error' => 'file Not Found', 'status_code' => 400], 400);
            }
        }
    }


    public function deletemultiplefiles(Request $request)
    {
        $input = $request->all();
        $validation = Validator::make($input, [
            'filenames' => 'required|array',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors(), 'status_code' => 400], 400);
        } else {
            $filenames = $request->input('filenames');

            if (empty($filenames)) {
                return response()->json(['message' => 'No filenames were provided.'], 400);
            }

            foreach ($filenames as $filename) {
                if (Storage::disk('s3')->exists($filename)) {
                    Storage::disk('s3')->delete($filename);
                }
            }

            return response()->json(['message' => 'Files have been successfully deleted.']);
        }
    }


    public function getfileurl(Request $request)
    {
        $input = $request->all();
        $validation = Validator::make($input, [
            'filename' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors(), 'status_code' => 400], 400);
        } else {
            if (Storage::disk('s3')->exists($request->filename)) {
                $file = Storage::disk('s3')->url($request->filename);
                return response()->json(['message' => 'success', 'data' => $file]);
            } else {
                return response()->json(['error' => 'file Not Found', 'status_code' => 400], 400);
            }
        }
    }

}

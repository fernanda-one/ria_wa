<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileManager extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        if ($request->subfolder) {
            if (!Storage::exists($request->subfolder)) {
                Storage::makeDirectory($request->subfolder);
            }
        }

        $files = Storage::files($request->subfolder ? $request->subfolder : null);

        $ignore = [
            '.gitignore',
            '.gitkeep',
        ];

        // get detail all files
        foreach ($files as $key => $file) {
            if (in_array($file, $ignore)) {
                unset($files[$key]);
                continue;
            }
            $files[$key] = [
                'path' => $file,
                'filename' => explode('/', $file)[count(explode('/', $file)) - 1],
                'mime' => explode('/', Storage::mimeType($file))[0],
                'ext' => explode('.', $file)[count(explode('.', $file)) - 1],
            ];
        }

        $data['files'] = $files;
        $data['subfolder'] = $request->subfolder ? $request->subfolder : null;
        $data['ismain'] = $request->ismain ? $request->ismain : false;
        return view('ilsya.files.index', $data);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|max:18384',
        ]);

        $originalName = trim($request->file('file')->getClientOriginalName());

        if (Storage::exists($request->subfolder ? $request->subfolder . '/' . $originalName : $originalName)) {
            $originalName = explode('.', $originalName)[0] . '-' . time() . '.' . explode('.', $originalName)[count(explode('.', $originalName)) - 1];
        }
        $originalName = str_replace(' ', '-', $originalName);
        $originalName = preg_replace('/[^A-Za-z0-9\-._+]/', '', $originalName);

        // hindari duplikasi minus minus --
        $originalName = preg_replace('/--+/', '-', $originalName);

        $file = $request->file('file')->storeAs($request->subfolder ? $request->subfolder : null, $originalName);

        return response()->json([
            'message' => 'File uploaded successfully',
            'data' => [
                'path' => $file,
                'filename' => $originalName,
                'mime' => explode('/', Storage::mimeType($file))[0],
                'ext' => explode('.', $file)[count(explode('.', $file)) - 1],
            ],
        ]);
    }

    public function delete(Request $request)
    {
        if ($request->file) {
            if (Storage::exists($request->file)) {
                Storage::delete($request->file);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'File deleted successfully',
        ]);
    }
}

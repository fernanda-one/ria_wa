<?php

namespace App\Http\Controllers;

use App\Helpers\Lyn;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PluginsController extends Controller
{
    public function __construct()
    {
        $this->url = config('app.base_node');
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->isMethod('POST')) {
            if (!session()->get('main_device')) return response()->json(['message' => 'No device selected.'], 400);

            $getsession = Session::where(['id' => session()->get('main_device'), 'user_id' => auth()->user()->id])->first();
            if (!$getsession) return response()->json(['message' => 'No device selected.'], 400);

            $response = Http::post($this->url . '/api/get-plugins', [
                'api_key' => $getsession->api_key,
            ])->json();

            if (!$response['status']) return response()->json(['message' => $response['message']], 400);

            return datatables()->of($response['data']['commands'])->addIndexColumn()
                ->addColumn('responsive_id', function () {
                    return '';
                })
                ->editColumn('cmd', function ($row) {
                    $cmd = '';
                    foreach ($row['cmd'] as $key => $value) {
                        $cmd .= '<span class="badge me-1 mb-1 small bg-label-primary">' . $value . '</span>';
                    }
                    return $cmd;
                })
                ->addColumn('status', function ($row) use ($response) {
                    $name = strtolower(str_replace(' ', '', $row['name']));
                    $owned = $response['data']['session_commands'];
                    if (in_array($name, $owned)) {
                        return '<a href="javascript:void(0)" class="badge bg-label-success is-change-status" data-status="inactive" data-name="' . $name . '">Active</a>';
                    } else {
                        return '<a href="javascript:void(0)" class="badge bg-label-dark is-change-status" data-status="active" data-name="' . $name . '">InActive</a>';
                    }
                })
                ->addColumn('view', function ($row) {
                    return '<a href="' . $row['docs'] . '" target="_blank" class="btn btn-icon btn-label-primary me-1"><span class="ti ti-list-details"></span></a>';
                })
                ->rawColumns(['status', 'cmd', 'view'])
                ->make(true);
        }

        if (!session()->get('main_device')) {
            return Lyn::view('nodevice');
        }
        return Lyn::view('plugins.index');
    }

    public function change(Request $request)
    {
        if ($request->ajax() || $request->isMethod('POST')) {
            $request->validate([
                'commands_name' => 'required',
                'status' => 'required|in:active,inactive',
            ]);
            if (!session()->get('main_device')) return response()->json(['message' => 'No device selected.'], 400);

            $getsession = Session::where(['id' => session()->get('main_device'), 'user_id' => auth()->user()->id])->first();
            if (!$getsession) return response()->json(['message' => 'No device selected.'], 400);

            $response = Http::post($this->url . '/api/act-plugins', [
                'api_key' => $getsession->api_key,
                'commands_name' => strtolower(str_replace(' ', '', $request->commands_name)),
                'status' => $request->status,
            ])->json();

            if (!$response['status']) return response()->json([
                'message' => $response['message'] ?? 'Something went wrong.',
            ], 400);

            if ($request->status == 'active') {
                return response()->json([
                    'message' => 'Plugin activated successfully.',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Plugin deactivated successfully.',
                ], 200);
            }
        }
    }
}

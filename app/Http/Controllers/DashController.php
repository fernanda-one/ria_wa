<?php

namespace App\Http\Controllers;

use App\Helpers\Lyn;
use App\Models\AutoResponder;
use App\Models\Bulk;
use App\Models\Campaigns;
use App\Models\Contact;
use App\Models\ContactLabel;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class DashController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->isMethod('post')) {
            $table = Session::where(['user_id' => auth()->user()->id]);
            return datatables()->of($table->get())->addIndexColumn()
                ->addColumn('responsive_id', function () {
                    return;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'CONNECTED') {
                        return '<div class="status-connection"><span class="badge rounded-pill bg-label-success"><span style="font-size: 1.05rem;" class="ti ti-plug-connected"></span> CONNECTED</span></div>';
                    } else {
                        return '<div class="status-connection"><span class="badge rounded-pill bg-label-danger"><span style="font-size: 1.05rem;" class="ti ti-plug-connected-x"></span> DISCONNECTED</span></div>';
                    }
                })
                ->editColumn('whatsapp_number', function ($row) {
                    return $row->whatsapp_number ? $row->whatsapp_number : '<span class="badge rounded-pill bg-label-danger">Not Connected</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('device.detail', $row->id) . '" class="btn btn-icon btn-label-primary me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="QR Code"><span class="ti ti-qrcode"></span></a>';
                    $btn .= '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Settings" class="btn btn-icon btn-label-dark me-1 is-show-settings" data-id="' . $row->id . '"><span class="ti ti-webhook"></span></a>';
                    $btn .= '<a href="javascript:void(0)" class="btn btn-icon btn-label-danger is-delete-device" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Device" data-id="' . $row->id . '"><span class="ti ti-trash-x"></span></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status', 'whatsapp_number'])
                ->make(true);
        }
        $data['count_device_online'] = Session::where(['user_id' => auth()->user()->id, 'status' => 'CONNECTED'])->count();
        $data['count_device'] = Session::where(['user_id' => auth()->user()->id])->count();
        return Lyn::view('dash.dash', $data);
    }

    public function device_store(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid Request.'
        ], 500);

        $request->validate([
            'session_name' => 'required'
        ]);

        // limit
        $limit = auth()->user()->limit_device;
        if ($limit != null) {
            if (Session::where(['user_id' => auth()->user()->id])->count() >= $limit) {
                return response()->json([
                    'message' => 'Device limit reached.'
                ], 500);
            }
        }

        $session = new Session();
        $session->session_name = $request->session_name;
        $session->user_id = auth()->user()->id;
        $session->whatsapp_number = null;
        $session->webhook = null;
        $session->api_key = Lyn::unique_apikey(20);

        if ($session->save()) {
            return response()->json([
                'message' => 'Device created.'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Device not created.'
            ], 500);
        }
    }

    public function device_delete(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid Request.'
        ], 500);

        $device = Session::where(['id' => $request->id, 'user_id' => auth()->user()->id])->first();

        if (!$device) return response()->json([
            'message' => 'Device not found.'
        ], 404);

        // delete auto responder
        AutoResponder::where(['session_id' => $device->id])->delete();
        // contacts label
        ContactLabel::where(['session_id' => $device->id])->delete();
        // contacts
        Contact::where(['session_id' => $device->id])->delete();

        Bulk::where(['session_id' => $device->id])->delete();

        Campaigns::where(['session_id' => $device->id])->delete();

        $device->delete();

        return response()->json([
            'message' => 'Device deleted.'
        ], 200);
    }

    public function device_settings_update(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid Request.'
        ], 500);

        $request->validate(
            [
                'id' => 'required',
                'api_key' => ['required', 'unique:sessions,api_key,' . $request->id],
            ],
            [
                'api_key.unique' => 'Please use another API Key.'
            ]
        );
        $device = Session::where(['id' => $request->id, 'user_id' => auth()->user()->id])->first();
        if (!$device) return response()->json([
            'message' => 'Device not found.'
        ], 404);

        $device->webhook = $request->webhook;
        $device->api_key = $request->api_key;
        $device->save();

        return response()->json([
            'message' => 'Device settings updated.'
        ], 200);
    }

    public function ajax_change_device(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid Request.'
        ], 500);

        $device = $request->device;
        if ($device != 'forgot') {
            $request->session()->put('main_device', $device);
        } else {
            $request->session()->forget('main_device');
        }

        return response()->json([
            'message' => 'Main Device changed.'
        ], 200);
    }

    public function ajax_generate_key(Request $request)
    {
        return response()->json([
            'apikey' => Lyn::unique_apikey(20)
        ], 200);
    }

    public function ajax_main_device(Request $request)
    {
        $main_device = $request->main_device_id;
        $header_device = Session::where(['user_id' => auth()->user()->id]);
        echo "<option value='forgot'>-- Select Device --</option>";
        foreach ($header_device->get() as $hdevice) {
            if ($main_device) {
                echo "<option " . ($hdevice->id == $main_device ? 'selected' : '') . " value='" . $hdevice->id . "'>" . $hdevice->session_name . "</option>";
            } else {
                echo "<option value='" . $hdevice->id . "'>" . $hdevice->session_name . "</option>";
            }
        }
    }

    public function ajax_device_settings(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid Request.'
        ], 500);

        $request->validate([
            'id' => 'required'
        ]);

        $device = Session::where(['id' => $request->id, 'user_id' => auth()->user()->id])->first();

        if (!$device) return response()->json([
            'message' => 'Device not found.'
        ], 404);

        return response()->json([
            'message' => 'Webhook loaded.',
            'data' => [
                'id' => $device->id,
                'webhook' => $device->webhook ? $device->webhook : '',
                'api_key' => $device->api_key
            ]
        ], 200);
    }

    public function Files()
    {
        // if (!session()->has('main_device')) {
        //     return Lyn::view('nodevice');
        // }
        return Lyn::view('dash.files');
    }


    public function storage(Request $request)
    {
        $path = $request->url;
        if ($path) {
            $storage = Storage::disk();
            if ($storage->exists(urldecode($path))) {
                return response()->file($storage->path(urldecode($path)));
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }
}

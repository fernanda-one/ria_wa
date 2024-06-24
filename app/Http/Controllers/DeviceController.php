<?php

namespace App\Http\Controllers;

use App\Helpers\Lyn;
use App\Models\Session;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request, $id)
    {
        $device = Session::where(['id' => $id, 'user_id' => $request->user()->id])->first();
        if (!$device) return redirect()->back()->withErrors(['msg' => 'Device not found']);
        $data['device'] = $device;
        return Lyn::view('dash.device_detail', $data);
    }
}

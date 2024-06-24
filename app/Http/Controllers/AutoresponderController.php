<?php

namespace App\Http\Controllers;

use App\Models\AutoResponder;
use Illuminate\Http\Request;
use App\Helpers\Lyn;

class AutoresponderController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        if (!session()->has('main_device')) {
            return Lyn::view('nodevice');
        }
        if ($request->ajax() || $request->isMethod('post')) {
            $table = AutoResponder::where(['user_id' => auth()->user()->id, 'session_id' => session()->get('main_device')]);
            return datatables()->of($table->get())->addIndexColumn()
                ->addColumn('responsive_id', function () {
                    return;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'active') {
                        return '<a href="javascript:void(0)" class="badge bg-label-primary is-change-status" data-id="' . $row->id . '">Active</a>';
                    } else {
                        return '<a href="javascript:void(0)" class="badge bg-label-dark is-change-status" data-id="' . $row->id . '">InActive</a>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('responder.detail', $row->id) . '" class="btn btn-icon btn-label-dark me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail"><span class="ti ti-list-details"></span></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return Lyn::view('responder.index');
    }

    public function store(Request $request)
    {
        if (!session()->get('main_device')) return response()->json(['message' => 'No device selected.'], 400);
        $request->validate([
            'keyword' => 'required',
            'message_type' => 'required',
            'reply_when' => 'required',
            'status' => 'required',
        ]);
        $table = new AutoResponder;

        $table->user_id = auth()->user()->id;
        $table->session_id = session()->get('main_device');
        $table->keyword = $request->keyword;
        $table->message_type = $request->message_type;
        $table->reply_when = $request->reply_when;
        $table->status = $request->status;
        $table->type_keyword = $request->type_keyword;
        Lyn::genereate_message($table, $request, 'save');
        return response()->json(['message' => 'Autoresponder added.']);
    }

    public function detail(Request $request, $id)
    {
        if (!session()->get('main_device')) return Lyn::view('nodevice');
        $table = AutoResponder::where([
            'id' => $id,
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device')
        ])->first();
        if (!$table) return redirect()->route('responder')->withErrors('Responder not found.');
        $data['row'] = $table;
        $data['data'] = json_decode($table->message);
        return Lyn::view('responder.detail', $data);
    }

    public function update(Request $request, $id)
    {
        if (!session()->get('main_device')) return response()->json(['message' => 'No device selected.'], 400);
        $request->validate([
            'keyword' => 'required',
            'reply_when' => 'required',
            'status' => 'required',
        ]);

        $table = AutoResponder::where([
            'id' => $id,
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device')
        ])->first();

        if (!$table) return response()->json(['message' => 'Autoresponder not found.']);

        $table->keyword = $request->keyword;
        $table->reply_when = $request->reply_when;
        $table->status = $request->status;
        $table->type_keyword = $request->type_keyword;
        Lyn::genereate_message($table, $request, 'update');

        return response()->json(['message' => 'Autoresponder updated.']);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $table = AutoResponder::find($request->id);
        if ($table->status == 'active') {
            $table->status = 'inactive';
        } else {
            $table->status = 'active';
        }

        $table->save();

        return response()->json([
            'status' => $table->status,
            'message' => 'Autoresponder status changed.'
        ]);
    }

    public function delete(Request  $request)
    {
        $table = AutoResponder::whereIn('id', $request->id)->delete();
        return response()->json(['message' => 'Autoresponder deleted.']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\Lyn;
use App\Models\Bulk;
use App\Models\Campaigns;
use App\Models\Contact;
use App\Models\ContactLabel;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CampaignsController extends Controller
{
    private $url;

    public function __construct()
    {
        $this->url = config('app.base_node', 'http://localhost:4000');
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->isMethod('post')) {
            if (!session()->get('main_device')) return response()->json(['message' => 'No main device selected'], 400);
            $auth = auth()->user();
            $table = Campaigns::where([
                'user_id' => $auth->id,
                'session_id' => session()->get('main_device'),
            ])->orderBy('created_at', 'desc')->get();

            return datatables()->of($table)
                ->addColumn('responsive_id', function () {
                    return;
                })
                ->editColumn('scheduled_at', function ($row) {
                    return Carbon::parse($row->scheduled_at)->format('d M Y H:i');
                })
                ->editColumn('delay', function ($row) {
                    return $row->delay . 's';
                })
                ->addColumn('of', function ($row) {
                    $row->total = $row->bulk()
                        ->whereNot('status', 'invalid')
                        ->count();
                    $row->sent = $row->bulk()
                        ->whereNot('status', 'invalid')
                        ->where('status', 'sent')
                        ->count();
                    return Lyn::thousandsCurrencyFormat($row->sent) . '/' . Lyn::thousandsCurrencyFormat($row->total);
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'waiting') {
                        return '<span class="badge bg-label-warning">Waiting</span>';
                    } else if ($row->status == 'processing') {
                        return '<span class="badge bg-label-info">Processing</span>';
                    } else if ($row->status == 'completed') {
                        return '<span class="badge bg-label-success">Completed</span>';
                    } else if ($row->status == 'paused') {
                        return '<span class="badge bg-label-secondary">Paused</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('campaigns.detail', $row->id) . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Show Detail" class="btn btn-icon btn-label-primary me-1" data-id="' . $row->id . '"><span class="ti ti-list-details"></span></a>';
                    if ($row->status == 'paused') {
                        $btn .= '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Resume" class="btn btn-icon btn-label-success me-1 is-change-status" data-id="' . $row->id . '" data-status="resume"><span class="ti ti-player-play"></span></a>';
                    } else if ($row->status == 'waiting' || $row->status == 'processing') {
                        $btn .= '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" title="Pause" class="btn btn-icon btn-label-warning me-1 is-change-status" data-id="' . $row->id . '" data-status="pause"><span class="ti ti-player-pause"></span></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        if (!session()->get('main_device')) return Lyn::view('nodevice');
        $data['phonebook'] = ContactLabel::where([
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device'),
        ])->withCount('contacts')->get();

        return Lyn::view('campaigns.index', $data);
    }

    public function ajax_change_status(Request $request)
    {
        if ($request->ajax()) {
            if (!session()->get('main_device')) return response()->json(['message' => 'No main device selected'], 400);
            $campaign = Campaigns::where([
                'id' => $request->id,
                'user_id' => auth()->user()->id,
                'session_id' => session()->get('main_device'),
            ])->first();

            if (!$campaign) return response()->json(['message' => 'Campaign not found'], 404);

            if ($campaign->status == 'completed') return response()->json(['message' => 'Campaign already completed'], 400);

            if ($request->status == 'pause') {
                $campaign->status = 'paused';
                $campaign->save();
                Lyn::trigerCampaigns();
                return response()->json(['message' => 'Campaign paused'], 200);
            } else if ($request->status == 'resume') {
                $check = Campaigns::where([
                    'user_id' => auth()->user()->id,
                    'session_id' => session()->get('main_device'),
                ])->whereIn('status', ['processing', 'waiting'])->count();
                if ($check > 0) return response()->json(['message' => 'There is another campaign in progress'], 400);
                $campaign->status = 'processing';
                $campaign->save();
                Lyn::trigerCampaigns();
                return response()->json(['message' => 'Campaign resumed'], 200);
            }
        }
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return response()->json(['message' => 'Bad request'], 400);
        if (!session()->get('main_device')) return response()->json(['message' => 'No main device selected'], 400);

        $request->validate([
            'name' => 'required',
            'phonebook_id' => 'required',
            'message_type' => 'required',
            'delay' => ['required', 'numeric', 'min:0'],
            'scheduled_at' => ['required'],
        ]);

        $contacts = Contact::where([
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device'),
            'label_id' => $request->phonebook_id,
        ])->get();

        if ($contacts->count() == 0) return response()->json(['message' => 'No contacts found in selected phonebook'], 400);

        $campaign = new Campaigns;

        $campaign->user_id = auth()->user()->id;
        $campaign->session_id = session()->get('main_device');
        $campaign->name = $request->name;
        $campaign->phonebook_id = $request->phonebook_id;
        $campaign->message_type = $request->message_type;
        Lyn::genereate_message($campaign, $request, 'save');
        $campaign->delay = $request->delay;
        $campaign->scheduled_at = $request->scheduled_at;
        if (Campaigns::where([
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device'),
        ])->whereIn('status', ['processing', 'waiting'])->count() > 0) {
            $campaign->status = 'paused';
            $msg = 'Campaign created and paused. because another campaign is still running';
        } else {
            $msg = 'Campaign created.';
            $campaign->status = 'waiting';
        }
        $campaign->save();

        // insert bulk
        try {
            $bulk = [];
            foreach ($contacts as $row) {
                $bulk[] = [
                    'id' => Str::uuid(),
                    'user_id' => auth()->user()->id,
                    'session_id' => session()->get('main_device'),
                    'campaign_id' => $campaign->id,
                    'receiver' => $row->phone_number,
                    'message_type' => $request->message_type,
                    'message' => $campaign->message,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Bulk::insert($bulk);
        } catch (\Exception $e) {
            $campaign->delete();
            return response()->json(['message' => $e->getMessage()], 400);
        }
        Lyn::trigerCampaigns();
        return response()->json(['message' => $msg], 200);
    }

    public function detail(Request $request, $id)
    {
        if ($request->ajax() || $request->isMethod('post')) {
            if (!session()->get('main_device')) return response()->json(['message' => 'No main device selected'], 400);
            $table = Bulk::where([
                'user_id' => auth()->user()->id,
                'session_id' => session()->get('main_device'),
                'campaign_id' => $id,
            ]);

            return datatables()->of($table)
                ->addColumn('responsive_id', function () {
                    return;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'pending') return '<span class="badge bg-label-warning">Pending</span>';
                    if ($row->status == 'sent') return '<span class="badge bg-label-success">Sent</span>';
                    if ($row->status == 'failed') return '<span class="badge bg-label-danger">Failed</span>';
                    if ($row->status == 'invalid') return '<span class="badge bg-label-secondary">Invalid</span>';
                })
                ->addColumn('type', function ($row) {
                    if (strpos($row->receiver, '@g.us') !== false) {
                        return 'Group';
                    } else {
                        return 'Personal';
                    }
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('d, M Y (H : i)') : '-';
                })
                ->rawColumns(['status'])
                ->make(true);
        }
        if (!session()->get('main_device')) return Lyn::view('nodevice');
        $campaign = Campaigns::where([
            'id' => $id,
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device'),
        ])->first();

        if (!$campaign) return redirect()->route('campaigns.index')->withErrors(['message' => 'Campaign not found']);

        $data['row'] = $campaign;
        $data['data'] = json_decode($campaign->message);
        return Lyn::view('campaigns.detail', $data);
    }

    public function delete(Request $request)
    {
        if (!$request->ajax()) return response()->json(['message' => 'Bad request'], 400);
        if (!session()->get('main_device')) return response()->json(['message' => 'No main device selected'], 400);

        $campaign = Campaigns::whereIn('id', $request->id)->where([
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device'),
        ]);

        foreach ($campaign->get() as $row) {
            $row->bulk()->delete();
            $row->delete();
        }

        return response()->json(['message' => 'Campaign deleted'], 200);
    }
}

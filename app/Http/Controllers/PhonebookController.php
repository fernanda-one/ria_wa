<?php

namespace App\Http\Controllers;

use App\Helpers\ContactsExport;
use App\Helpers\Lyn;
use App\Helpers\ContactImport;
use App\Models\Contact;
use App\Models\ContactLabel;
use App\Models\MstArea;
use App\Models\MstCity;
use App\Models\MstRegion;
use App\Models\MstShop;
use App\Models\Session;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class PhonebookController extends Controller
{
    public function __construct()
    {
        $this->url = config('app.base_node');
    }

    public function index()
    {
        if (!session()->get('main_device')) {
            return Lyn::view('nodevice');
        }

        $data['phonebook'] = ContactLabel::where([
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device')
        ]);

        return Lyn::view('phonebook.index', $data);
    }

    public function ajax_label_store(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid request'
        ], 400);

        if (!session()->get('main_device')) {
            return response()->json([
                'message' => 'No device selected'
            ], 400);
        }

        $request->validate([
            'title' => 'required'
        ]);

        $table = new ContactLabel();
        $table->user_id = auth()->user()->id;
        $table->session_id = session()->get('main_device');
        $table->title = $request->title;
        $table->save();

        return response()->json([
            'message' => 'Label created.',
            'data' => array(
                'title' => $table->title,
                'url' => route('phonebook.contacts.index', $table->id),
            )
        ]);
    }

    public function label_delete($id)
    {
        if (!session()->get('main_device')) return response()->json([
            'message' => 'No device selected'
        ], 400);

        $table = ContactLabel::where([
            'id' => $id,
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device')
        ])->first();

        if ($table) {
            Contact::where('label_id', $table->id)->delete();
            $table->delete();
        } else {
            return response()->json([
                'message' => 'Label not found.'
            ], 400);
        }

        return response()->json([
            'message' => 'Labels removed. with all contacts',
        ]);
    }

    public function contacts(Request $request, $id)
    {
        if ($request->ajax() || $request->isMethod('post')) {
            if (!session()->get('main_device')) return response()->json([
                'message' => 'No device selected'
            ], 400);

            $table = Contact::where([
                'user_id' => auth()->user()->id,
                'session_id' => session()->get('main_device'),
                'label_id' => $id
            ]);
            return datatables()->of($table->get())->addIndexColumn()
                ->addColumn('responsive_id', function () {
                    return;
                })
                ->addColumn('type', function ($row) {
                    if (strpos($row->phone_number, '@g.us') !== false) {
                        return 'Group';
                    } else {
                        return 'Personal';
                    }
                })
                ->rawColumns(['type'])
                ->make(true);
        }

        if (!session()->get('main_device')) return Lyn::view('nodevice');

        $label = ContactLabel::where([
            'id' => $id,
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device')
        ])->first();

        $getlabel = ContactLabel::where([
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device')
        ])->get();

        if (!$label) return redirect()->route('phonebook')->withErrors('Label not found.');
        $data['label'] = $label;
        $data['getlabel'] = $getlabel;
        $data['regions'] = MstRegion::where([])->get();
        $data['areas'] = MstArea::where([])->get();
        $data['cities'] = MstCity::where([])->get();
        $data['shops'] = MstShop::where([])->get();

        return Lyn::view('phonebook.contacts', $data);
    }

    public function contacts_store(Request $request, $id)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid request'
        ], 400);

        $request->validate([
            'phone_number' => 'required',
            'name' => 'required'
        ]);

        if (!session()->get('main_device')) return response()->json([
            'message' => 'No device selected'
        ], 400);

        if (Contact::where([
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device'),
            'label_id' => $id,
            'phone_number' => $request->phone_number
        ])->first()) return response()->json([
            'message' => 'Number already exists.'
        ], 400);
        $nickname = 'Kak';

        if ($request->marital_status !== null) {
            $nickname = $request->gender === 'male' ? 'Bapak' : 'Bunda';
        }

        $table = new Contact();
        $table->user_id = auth()->user()->id;
        $table->session_id = session()->get('main_device');
        $table->label_id = $id;
        $table->name = $request->name;
        $table->nickname = $nickname . ' ' . $request->name;
        $table->phone_number = $request->phone_number;
        $table->birth_date = $request->birth_date;
        $table->gender = $request->gender;
        $table->marital_status = $request->marital_status;
        $table->religion = $request->religion;
        $table->region_id = $request->region;
        $table->area_id = $request->area;
        $table->city_id = $request->city;
        $table->shop_id = $request->shop;
        $table->registration_date = new DateTime();
        $table->save();

        return response()->json([
            'message' => 'Contact created.',
        ]);
    }

    public function contacts_delete(Request $request)
    {
        if (!$request->ajax()) return response()->json([
            'message' => 'Invalid request'
        ], 400);

        if (!session()->get('main_device')) return response()->json([
            'message' => 'No device selected'
        ], 400);

        foreach ($request->id as $id) {
            $table = Contact::where([
                'id' => $id,
                'user_id' => auth()->user()->id,
                'session_id' => session()->get('main_device')
            ])->first();
            if ($table) $table->delete();
        }

        return response()->json([
            'message' => 'Contact deleted.',
        ]);
    }

    public function contacts_export(Request $request, $id)
    {
        if (!session()->get('main_device')) redirect()->route('phonebook.contacts.index', $id)->withErrors('No device selected.');

        try {
            $label = ContactLabel::where([
                'id' => $id,
                'user_id' => auth()->user()->id,
                'session_id' => session()->get('main_device')
            ])->first();

            if (!$label) return redirect()->route('phonebook')->withErrors('Label not found.');

            $table = Contact::where([
                'user_id' => auth()->user()->id,
                'session_id' => session()->get('main_device'),
                'label_id' => $id
            ])->get();

            if ($table->count() == 0) return redirect()->route('phonebook.contacts.index', $id)->withErrors('No contacts found.');

            $filename = $label->title . ' - ' . date('d-m-Y') . '.xlsx';
            return Excel::download(new ContactsExport($label->id, auth()->user()->id, session()->get('main_device')), $filename);
        } catch (\Exception $e) {
            return redirect()->route('phonebook.contacts.index', $id)->withErrors($e->getMessage());
        }
    }

    public function contacts_import(Request $request, $id)
    {
        if (!session()->get('main_device')) return response()->json(['message' => 'No device selected'], 400);
        if (!$request->hasFile('file')) return response()->json(['message' => 'File not found.'], 400);
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        try {
            $label = ContactLabel::where([
                'id' => $id,
                'user_id' => auth()->user()->id,
                'session_id' => session()->get('main_device')
            ])->first();
            if (!$label) return response()->json(['message' => 'Label not found.'], 400);

            Excel::import(new ContactImport($label->id, auth()->user()->id, session()->get('main_device')), $request->file('file')->store('temp'));

            return response()->json([
                'message' => 'Contacts imported.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong'], 400);
        }
    }

    public function fetch_group(Request $request, $id)
    {
        // if (!$request->ajax()) return response()->json([
        //     'message' => 'Invalid request'
        // ], 400);

        if (!session()->get('main_device')) return response()->json([
            'message' => 'No device selected'
        ], 400);

        $getsession = Session::where([
            'id' => session()->get('main_device'),
            'user_id' => auth()->user()->id
        ])->first();

        if (!$getsession) return response()->json([
            'message' => 'Session not found.'
        ], 400);

        try {
            $response = Http::post($this->url . '/api/fetch-group', [
                'api_key' => $getsession->api_key,
            ])->json();

            if ($response['status'] == 'success') {

                foreach ($response['data'] as $key => $value) {
                    if (Contact::where([
                        'user_id' => auth()->user()->id,
                        'session_id' => session()->get('main_device'),
                        'number' => $value['id'],
                        'label_id' => $id
                    ])->first()) continue;

                    $table = new Contact();
                    $table->user_id = auth()->user()->id;
                    $table->session_id = session()->get('main_device');
                    $table->label_id = $id;
                    $table->name = $value['name'];
                    $table->phone_number = $value['id'];
                    $table->save();
                }

                return response()->json(['message' => ($response['message'] ?? 'Success Fetch Group.')]);
            } else {
                return response()->json(['message' => ($response['message'] ?? 'Something went wrong')], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}

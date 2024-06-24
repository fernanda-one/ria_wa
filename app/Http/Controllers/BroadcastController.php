<?php

namespace App\Http\Controllers;

use App\Helpers\Lyn;
use App\Models\Contact;
use App\Models\MstArea;
use App\Models\MstCity;
use App\Models\MstRegion;
use App\Models\MstShop;
use App\Models\Scheduler;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Dotenv\Util\Str;
use Illuminate\Support\Facades\Date;

class BroadcastController extends Controller
{
    private $url;

    public function __construct()
    {
        $this->url = config('app.base_node');
    }

    public function index()
    {
        $data['regions'] = MstRegion::where([])->get();
        $data['areas'] = MstArea::where([])->get();
        $data['cities'] = MstCity::where([])->get();
        $data['shops'] = MstShop::where([])->get();

        return Lyn::view('broadcast.index', $data);
    }
    public function birthday()
    {
        return Lyn::view('broadcast.birthday');
    }

    public function location(Request $request)
    {
        if (!session()->get('main_device')) {
            return response()->json(['message' => 'No device selected.'], 400);
        }

        $filters = [];
        $hasFilter = false;
        if ($request->region) {
            $filters['region_id'] = $request->region;
            $hasFilter = true;
        }
        if ($request->area) {
            $filters['area_id'] = $request->area;
            $hasFilter = true;
        }
        if ($request->shop) {
            $filters['shop_id'] = $request->shop;
            $hasFilter = true;
        }
        if ($request->city) {
            $filters['city_id'] = $request->city;
            $hasFilter = true;
        }

        if (!$hasFilter) {
            return response()->json(['message' => 'At least select one location is required.'], 400);
        }

        $sender = new SingleSender();
        $contacts = Contact::where([
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device'),
        ] + $filters)->get();

        if ($contacts->count() == 0) return response()->json(['message' => 'No contacts found.'], 400);

        foreach ($contacts as $contact) {
            $request['receiver'] = $contact->phone_number;
            $sender->store($request);
            sleep(1);
        }
    }

    public function birthdayStore(Request $request)
    {
        if (!session()->get('main_device')) return response()->json(['message' => 'No device selected.'], 400);

        $request->validate([
            'birth_date' => 'required',
            'schedule_date' => 'required',
        ]);
        $schedule = new Scheduler();
        $schedule->user_id = auth()->user()->id;
        $schedule->session_id = session()->get('main_device');
        $schedule->birth_date = $request->birth_date;
        $schedule->scheduler_at = $request->schedule_date;
        $schedule->save();

        return response()->json(['message' => 'Scheduler added.']);
    }

    public function checkScheduler()
    {
        logger()->info('Scheduler checked. ' . date('Y-m-d H:i:s'));
        $now = date('Y-m-d');
        $schedulerArray = Scheduler::where(['scheduler_at' => $now])->get();
        foreach ($schedulerArray as $scheduler) {
            session()->put('main_device', $scheduler->session_id);
            $this->shedulerBirthday($scheduler);
            sleep(2);
        }

        return 'success';
    }

    public function shedulerBirthday($request)
    {
        try {
            $sender = new SingleSender();
            $contacts = Contact::where(['user_id' => $request->user_id, 'session_id' => session()->get('main_device'), 'voucher_sent' => null])
                ->whereRaw('MONTH(birth_date) = ? AND DAY(birth_date) = ?', [date('m', strtotime($request->birth_date)), date('d', strtotime($request->birth_date))])
                ->get();

            foreach ($contacts as $contact) {
                $request['receiver'] = $contact->phone_number;
                $id = $contact->id;
                $nickname = trim(strtoupper($contact->nickname));
                $voucher = trim(strtoupper($contact->voucher));
                $gender = $contact->gender;
                $voucher_valid = date("j ", strtotime($contact->voucher_valid)) . $this->getMonth(date("m", strtotime($contact->voucher_valid))) . date(" Y", strtotime($contact->voucher_valid));
                $birth = date("j ", strtotime($contact->tanggal_lahir)) . strtoupper($this->getMonth(date("m", strtotime($contact->birth_date)))) . date(" Y");
                $this->generateImage($id, $nickname, $voucher, $voucher_valid, $gender, $birth);
                $request['message_type'] = 'media';
                $request['media_type'] = 'image';
                $request['media'] = trim(url('/') . '/storage?url=voucher/' . $id . '.jpg');

                $sender->storeBirtday($request);
                $contact->update(['voucher_sent' => Date::now()->format('Y-m-d')]);
                sleep(10);
            }
        } catch (\Exception $e) {
            logger()->error('shedulerBirthday failed. ' . $e->getMessage());
        }
    }

    public function getMonth($month)
    {
        switch ($month) {
            case 1:
                return "Januari";
                break;
            case 2:
                return "Februari";
                break;
            case 3:
                return "Maret";
                break;
            case 4:
                return "April";
                break;
            case 5:
                return "Mei";
                break;
            case 6:
                return "Juni";
                break;
            case 7:
                return "Juli";
                break;
            case 8:
                return "Agustus";
                break;
            case 9:
                return "September";
                break;
            case 10:
                return "Oktober";
                break;
            case 11:
                return "November";
                break;
            case 12:
                return "Desember";
                break;
        }
    }

    public function generateImage($id, $name, $voucher, $voucherValidDate, $gender, $birthDate)
    {
        try {
            $baseImagePath = storage_path('app/public/voucher/' . ($gender == 'male' ? 'male.jpg' : 'female.jpg'));
            $image = Image::make($baseImagePath);

            $nameTagPosition = $this->calculateTextPosition($name, 100);
            $voucherTagPosition = $this->calculateTextPosition($voucher, 150);
            $voucherValidDateTagPosition = $this->calculateTextPosition($voucherValidDate, 20);
            $birthDateTagPosition = $this->calculateTextPosition($birthDate, 80);

            $image
                ->text($name, $nameTagPosition + 150, 789, function ($font) {
                    $font->file(storage_path('app/public/font/montserrat.regular.ttf'));
                    $font->size(100);
                    $font->color('#034a80');
                })
                ->text($voucher, 1831, 1980 - strlen($voucher), function ($font) use ($voucher) {
                    $font->file(storage_path('app/public/font/librebarcode.ttf'));
                    $font->size(150);
                    $font->color('#000000');
                    $font->angle(-270);
                })
                ->text($voucherValidDate, $voucherValidDateTagPosition, 1980, function ($font) {
                    $font->file(storage_path('app/public/font/Roboto-Bold.ttf'));
                    $font->size(20);
                    $font->color('#FFFFFF');
                })
                ->text($birthDate, $birthDateTagPosition + 225, 900, function ($font) {
                    $font->file(storage_path('app/public/font/montserrat.regular.ttf'));
                    $font->size(80);
                    $font->color('#034a80');
                });

            $savePath = storage_path('app/public/voucher/' . $id . '.jpg');
            $image->save($savePath);

            logger()->info('Image generated. ' . storage_path('app/public/voucher/' . $id . '.jpg') . date('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
        }
    }

    private function calculateTextPosition($text, $fontSize)
    {
        return 1181 - (strlen(trim($text)) * $fontSize) / 2;
    }
}

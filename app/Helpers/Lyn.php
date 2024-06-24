<?php

namespace App\Helpers;

use App\Models\Session;
use Illuminate\Support\Facades\Http;

class Lyn
{

    public static function view($view, $data = [], $mergeData = [])
    {
        $data['auth'] = auth()->user();
        $main_device = Session::where(['id' => session()->get('main_device'), 'user_id' => $data['auth']->id]);
        if (!$main_device->exists()) session()->forget('main_device');
        $data['main_device'] = $main_device->first();
        return view($view, $data, $mergeData);
    }

    public static function unique_apikey($length = 32)
    {
        do {
            $token = bin2hex(random_bytes($length));
        } while (Session::where(['api_key' => $token])->exists());
        return $token;
    }

    public static function thousandsCurrencyFormat($num)
    {

        if ($num > 1000) {

            $x = round($num);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = array('k', 'm', 'b', 't');
            $x_count_parts = count($x_array) - 1;
            $x_display = $x;
            $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
            $x_display .= $x_parts[$x_count_parts - 1];

            return $x_display;
        }

        return $num;
    }

    public static function genereate_message($table, $request, $type)
    {
        if ($type == 'save') {
            $msg_type = $request->message_type;
        } else {
            $msg_type = $table->message_type;
        }
        if ($msg_type == 'text') {
            $request->validate([
                'message' => 'required',
            ]);
            $data = array(
                'message' => $request->message,
            );

            if ($request->quoted == 'yes') {
                $data['quoted'] = $request->quoted;
            }

            $table->message = json_encode($data);
            $table->save();
        } else if ($msg_type == 'media') {
            $request->validate([
                'media' => 'required',
            ]);
            $data = array(
                'url' => $request->media,
                'media_type' => $request->media_type,
                'caption' => $request->message ?? '',
            );

            if ($request->quoted == 'yes') {
                $data['quoted'] = $request->quoted;
            }

            $table->message = json_encode($data);
            $table->save();
        } else if ($msg_type == 'button') {
            $request->validate([
                'message' => 'required',
                'footer' => 'required',
            ]);

            $buttons = [];

            foreach ($request->btn_display as $key => $val) {
                $buttons[] = array(
                    "display" => $request->btn_display[$key],
                    "id" => $request->btn_id[$key],
                );
            }

            $table->message = json_encode(array(
                'message' => $request->message,
                'footer' => $request->footer,
                'buttons' => $buttons,
            ));
            $table->save();
        } else if ($msg_type == 'list') {
            $request->validate([
                'message' => 'required',
                'footer' => 'required',
            ]);

            $sections = [];
            $first = true;
            foreach ($request->btn_display as $key => $val) {
                if ($request->type[$key] == 'section') {
                    if ($first) {
                        $first = false;
                    }
                    $sections[] = array(
                        "title" => $request->btn_display[$key],
                        "rows" => [],
                    );
                } else if ($request->type[$key] == 'option') {
                    if ($first) {
                        $sections[] = array(
                            "rows" => [],
                        );
                        $first = false;
                    }
                    $sections[count($sections) - 1]['rows'][] = array(
                        "title" => $request->btn_display[$key],
                        "rowId" => $request->btn_id[$key] ?? '',
                    );
                }
            }

            $table->message = json_encode(array(
                'title' => $request->title ?? '',
                'message' => $request->message,
                'footer' => $request->footer,
                'buttonText' => $request->button_text ?? 'Click Here',
                'sections' => $sections,
            ));
            $table->save();
        }
    }

    public static function trigerCampaigns()
    {
        try {
            Http::post(config('app.base_node', 'http://localhost:4000') . '/api/triger-campaigns', array(
                "api_key" => Session::where('id', session()->get('main_device'))->first()->api_key,
            ));
        } catch (\Exception $e) {

            return $e->getMessage();
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\Lyn;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index()
    {
        if (!session()->get('main_device')) return Lyn::view('nodevice');

        return Lyn::view('api.apidocs', [
            'webhook' => $this->hight_webhook(),
        ]);
    }


    public function hight_webhook()
    {
        return '&lt;?php
header(&#39;content-type: application/json&#39;);
$data = json_decode(file_get_contents(&#39;php://input&#39;), true);
file_put_contents(&#39;logwebhook.txt&#39;, &#39;[&#39; . date(&#39;Y-m-d H:i:s&#39;) . &quot;]\n&quot; . json_encode($data) . &quot;\n\n&quot;, FILE_APPEND);

$message = $data[&#39;message&#39;] ?? null;
$from = $data[&#39;from&#39;] ?? null;
$isGroup = $data[&#39;isGroup&#39;] ?? null;
$isMe = $data[&#39;isMe&#39;] ?? null;

switch ($message) {
    case &quot;ping&quot;:
        $data = [
            &#39;message_type&#39; =&gt; &#39;text&#39;,
            &#39;message&#39; =&gt; array(
                &#39;message&#39; =&gt; &#39;pong&#39;
            )
        ];
        break;
    case &quot;media&quot;:
        $data = [
            &#39;message_type&#39; =&gt; &#39;media&#39;,
            &#39;message&#39; =&gt; array(
                &quot;media_type&quot; =&gt; &quot;image&quot;, // image, video, audio, file
                &quot;url&quot; =&gt; &quot;https://i.ibb.co/QmPKL4Q/sad.jpg&quot;,
                &quot;caption&quot; =&gt; &quot;This is caption&quot; // optional
            )
        ];
        break;
    default:
        // if message is not match
        $data = false;
        break;
}

print json_encode([
    &#39;status&#39; =&gt; &#39;success&#39;,
    &#39;data&#39; =&gt; json_encode($data)
]);
';
    }
}

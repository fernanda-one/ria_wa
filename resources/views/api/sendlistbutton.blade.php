<div class="d-flex mb-3 gap-3">
    <div>
        <span class="badge bg-label-primary rounded-2 p-2">
            <i class="ti ti-brand-whatsapp ti-lg"></i>
        </span>
    </div>
    <div>
        <h4 class="mb-0">
            <span class="align-middle">Send List Button</span>
        </h4>
        <small>Api Docs Sending List Button Messages</small>
    </div>
</div>
<div id="accordionPayment" class="accordion">
    <div class="card">
        <div class="card-body">
            <p>SendText is an API that allows you to send list button messages to WhatsApp numbers.</p>
            <span class="text-info">Endpoint :</span> {!! config('app.base_node') !!}/api/send-listmsg <br>
            <span class="text-info">Method :</span> POST <br>
            <span class="text-info">Download Example PHP :</span> <a class="fw-bold" target="_blank" href="https://github.com/ilsyaa/example-lazygateway">Download</a><br>

            <div class="card accordion-item mt-3">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#sendtext-array" aria-controls="accordionPayment-1">
                        Array Body
                    </button>
                </h2>

                <div id="sendtext-array" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <pre style="border-radius: 6px;"><code class="hljs language-php">{
  "api_key" => "{!! $main_device->api_key !!}",
  "receiver" => "628xxxxxxxx",
  "data": {
    "title": "Title Message",
    "message": "Message Content",
    "footer": "WALazy - Ilsya",
    "buttonText": "Click Me",
    "sections": [
        {
            "title": "Section 1",
            "rows": [
                {
                    "title": "Option 1",
                    "rowId": "option1"
                },
                {
                    "title": "Option 2",
                    "rowId": "option2",
                    "description": "This is a description"
                }
            ]
        },
        {
            "title": "Section 2",
            "rows": [
                {
                    "title": "test",
                    "rowId": "test"
                }
            ]
        }
    ]
  }
}</code></pre>
                    </div>
                </div>
            </div>

            <div class="card accordion-item mt-3">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#sendtext-curl" aria-controls="accordionPayment-1">
                        Code Example Curl
                    </button>
                </h2>

                <div id="sendtext-curl" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <pre style="border-radius: 6px;"><code class="hljs language-php">curl -X POST \
'{!! config('app.base_node') !!}/api/send-listmsg' \
--header 'Accept: */*' \
--header 'Content-Type: application/json' \
--data-raw '{
  "api_key": "{!! $main_device->api_key !!}",
  "receiver": "628xxxxxxxx",
  "data": {
    "title": "Title Message",
    "message": "Message Content",
    "footer": "WALazy - Ilsya",
    "buttonText": "Click Me",
    "sections": [
        {
            // "title": "Section 1",
            "rows": [
                {
                    "title": "Option 1",
                    "rowId": "option1"
                },
                {
                    "title": "Option 2",
                    "rowId": "option2",
                    "description": "This is a description"
                }
            ]
        },
        {
            "title": "Section 2",
            "rows": [
                {
                    "title": "test",
                    "rowId": "test"
                }
            ]
        }
    ]
  }
}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

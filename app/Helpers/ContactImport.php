<?php

namespace App\Helpers;

use App\Models\Contact;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ContactImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    protected $tag;
    protected $user;
    protected $session_id;
    public function __construct($label_id, $user_id, $session_id)
    {
        $this->tag = $label_id;
        $this->user = $user_id;
        $this->session_id = $session_id;
    }
    public function collection(Collection $collection)
    {

        // Log::info($collection);
        foreach ($collection as $row) {
            if (Contact::where([
                'user_id' => $this->user,
                'session_id' => $this->session_id,
                'label_id' => $this->tag,
                'number' => (string)$row[1]
            ])->count() > 0) continue;

            Contact::create([
                'user_id' => $this->user,
                'session_id' => $this->session_id,
                'label_id' => $this->tag,
                'name' => $row[0],
                'number' => (string)$row[1]
            ]);
        }
    }
}

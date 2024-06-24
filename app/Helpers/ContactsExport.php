<?php

namespace App\Helpers;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromCollection;

class ContactsExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
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
    public function collection()
    {
        return Contact::where([
            'user_id' => $this->user,
            'label_id' => $this->tag,
            'session_id' => $this->session_id
        ])->get(['name', 'number']);
    }
}

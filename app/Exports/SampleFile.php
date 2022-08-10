<?php

namespace App\Exports;

use App\Models\Option;

use Maatwebsite\Excel\Concerns\FromArray;

class SampleFile implements FromArray
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        return [[
            'name', 
            'email', 
            'email_verified_at', 
            'password',
            'remember_token',
            'created_at',
            'updated_at',
            'surname',
            'phone',
            'state',
            'rol_id',
            'type_id',
            'subcompanies_id',
            'link_facebook',
            'link_google',
            'link_linkedin',
            'link_instagram'
        ]];
    }

}

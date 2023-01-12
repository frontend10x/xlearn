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
            'nombre',
            'apellido', 
            'telefono',
            'email', 
            'password',
        ]];
    }

}

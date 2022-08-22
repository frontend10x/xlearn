<?php

namespace App\Imports;
use Illuminate\Support\Facades\Hash;
use Mail;
use App\Mail\EmailNotification;
use Illuminate\Support\Facades\Crypt;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{

    public function __construct(string $subcompanies_id)
    {
        $this->subcompanies_id = $subcompanies_id;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $createUser =  new User([
            'name' => $row['nombre'],
            'email' => $row['email'],
            'password' => Hash::make($row['password']),
            'surname' => $row['apellido'],
            'phone' => $row['telefono'],
            'state' => 0,
            'rol_id' => 4,
            'subcompanies_id' => $this->subcompanies_id
        ]);

        $encryptedId = Crypt::encryptString($createUser->id);
        Mail::to($row['email'])->send(new EmailNotification($encryptedId, 'confirmation_register'));

        return $create;
    }
}
 
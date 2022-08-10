<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'name' => $row['name'], 
            'email' => $row['email'], 
            'email_verified_at' => $row['email_verified_at'], 
            'password' => $row['password'],
            'remember_token' => $row['remember_token'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'surname' => $row['surname'],
            'phone' => $row['phone'],
            'state' => $row['state'],
            'rol_id' => 1,
            'type_id' => 1,
            'subcompanies_id' => 7,
            'link_facebook' => $row['link_facebook'],
            'link_google' => $row['link_google'],
            'link_linkedin' => $row['link_linkedin'],
            'link_instagram' => $row['link_instagram']
        ]);
    }
}

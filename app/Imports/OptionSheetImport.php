<?php

namespace App\Imports;

use App\Models\Option;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class OptionSheetImport implements ToModel, WithHeadingRow
{

    public $options = [];

    public function __construct()
    {
        HeadingRowFormatter::default('none');
    }
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        /*$$this->questions = [
            "id" => $row['id'],
            "response" => $row['response'],
            "numeration" => $row['numeration'],
            "question_id" => $row['question_id'],
        ];*/

        // return $this->answers;
        $this->options[] = new Option([
            "id" => $row['id'],
            "response" => $row['response'],
            "numeration" => $row['numeration'],
            "question_id" => $row['question_id'],
        ]);


        return $this->options;
    }
}
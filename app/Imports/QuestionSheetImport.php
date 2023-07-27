<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class QuestionSheetImport implements ToModel, WithHeadingRow
{

    public $questions = [];

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

        // $filteredRow = preg_replace('/\s+/', '', $row);

        // $this->questions = [
            // 'id' => $filteredRow['id'],
            // "type" => $filteredRow['type'],
            // "question" => $filteredRow['question'],
            // "response_types" => $filteredRow['response_types'],
            // "required" => $filteredRow['required'],
            // "answer" => $filteredRow['answer'],
        // ];

        // return $this->questions;

        $this->questions[] = new Question([
            'id' => $row['id'],
            "type" => $row['type'] ?? 'evaluación',
            "question" => $row['question'],
            "response_types" => $row['response_types'] ?? 'options',
            "required" => $row['required'] ?? 1,
            "answer" => $row['answer'],
        ]);

        return $this->questions;
    }
}
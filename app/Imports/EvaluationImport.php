<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class EvaluationImport implements WithMultipleSheets
{
    private $importedRows;
    
    public function __construct($course_id)
    {
        HeadingRowFormatter::default('none');
        $this->course_id = $course_id;
    }
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    /*public function model(array $row)
    {
        
        $this->importedRows[] = $row;
        
    }*/

    public function sheets(): array
    {
        $this->importedRows =  [
            'Preguntas' => new QuestionSheetImport(),
            'Respuestas' => new OptionSheetImport(),
        ];

        return $this->importedRows;
    }

    public function getImportedRows()
    {
        return $this->importedRows;
    }
}
<?php


abstract class mainTableClass
{

    protected $spreadSheet = '12CEN3JzXWYKBwE';
    protected $sheet;

    public function __construct($sheet){
        $this->sheet = $sheet;

    }

    public function fillTable($arr){

        $lastRow = GoogleDocs::getLastRowId();
        GoogleDocs::writeRows(
            [
                "start_row" => $lastRow + 1,
                "start_cell" => 1,
                "write_data" => [
                    $arr
                ]

            ]);


    }
}
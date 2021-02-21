<?php



class basicTableClass extends mainTableClass
{
    public function fillTable($arr){
        GoogleDocs::$spreadSheet = $this->spreadSheet;
        GoogleDocs::$sheet = $this->sheet;
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
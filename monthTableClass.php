<?php


class monthTable extends mainClass
{

    public function fillMonth($users, $statuses, $account, $lead, $leadPrice, $respUser, $time)
    {
        echo $this->sheet;
        GoogleDocs::$sheet = $this->sheet;
        for ($i = 2; $i < count($users) + 2; $i++) {
            foreach ($account['groups'] as $group) {
                if ($group['id'] == $users[$lead['responsible_user_id']]['group_id']) {
                    $userGroup = $group['name'];
                }
            }
            $tmpCell = GoogleDocs::readCell('A' . $i);
            if ($tmpCell == '') {
                $amountMonth = 1;
                if ($lead['status_id'] == 142) {
                    $priceMonth = $leadPrice;
                } else $billMonth = $leadPrice;
                $averageBillMonth = $leadPrice;
                break;
            }

            if ($tmpCell == $respUser) {

                $tmpMonth = GoogleDocs::readCells(['A' . $i, 'B' . $i, 'C' . $i, 'D' . $i, 'E' . $i, 'F' . $i, 'G' . $i]);

                if ($statuses[$lead['pipeline_id']]['name'] == "Счет отправлен" || $statuses[$lead['pipeline_id']]['name'] == "Дожим 2") {
                    $billMonth = $tmpMonth['С' . $i] + $leadPrice;
                } else $billMonth = $tmpMonth['С' . $i];

                if ($lead['status_id'] == 142) {
                    $priceMonth = $tmpMonth['D' . $i] + $leadPrice;
                } else $priceMonth = $tmpMonth['D' . $i];


                $amountMonth = $tmpMonth['F' . $i] + 1;

                $averageBillMonth = $priceMonth / $amountMonth;

                $respMonth = $tmpMonth['A' . $i];

                break;
            }

        }

        $currentMonthArr = [$respUser, $userGroup, $billMonth, $priceMonth, $averageBillMonth ,$amountMonth];
        print_r($currentMonthArr);
   

        GoogleDocs::writeRows(
            [
                "start_row" => $i,
                "start_cell" => 1,
                "write_data" => [
                  $currentMonthArr
                ]

            ]);



    }


}

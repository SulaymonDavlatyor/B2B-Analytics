<?php


class clientsTableClass extends mainTableClass
{


    public function fillClients($users,$pdo){


        foreach($users as $user){
            $userId = $user['id'];
            $query = "SELECT * FROM `b2bAnalytics` WHERE `managerId` = '$userId'";
            $result = $pdo->query($query);
            $resultBd = $result->fetchAll();
            foreach($resultBd as $bdElem){
                for($i = 2; $i<count($resultBd)+2;$i++){
                    $respCell =  GoogleDocs::readCell('B'.$i);
                    if($respCell == $users[$userId]['name']){
                        $pipeCell = GoogleDocs::readCell('C'.$i);
                        if($pipeCell == $bdElem['pipeline_id']){
                            $amountCell = GoogleDocs::readCell('D'.$i);
                            $amountCell += 1;
                            GoogleDocs::writeCell('D'.$i,$amountCell);
                        }else{
                            $this->fillTable([[time(),$users[$userId]['name'],$bdElem['pipeline_id'],1]]);

                        }
                    }
                }


            }


        }


    }

}
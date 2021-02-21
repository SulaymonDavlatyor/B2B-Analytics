<?php

require_once('lib/init.php');
require_once('vendor/autoload.php');
require_once('mainClass.php');
require_once('basicTable.php');
require_once('monthTable.php');
require_once('db_connect.php');

HU::initGoogleDocsApi();


Introvert\Configuration::getDefaultConfiguration()->setApiKey('key', 'e54864f9482');
$api = new Intr\ApiClient();

if (!file_exists(__DIR__ . '/cache.json')) {
    $time = 0;
} else {
    $time = json_decode(file_get_contents(__DIR__ . '/cache.json'), 1)['time'];
}

if ($time + 24 * 60 * 60 < time()) {

    $users = $api->account->users()['result'];
    $statuses = $api->account->allStatuses()['result'];
    $pipelines = $api->account->pipelines()['result'];
    $account = $api->account->info()['result'];
    $cache['users'] = $users;
    $cache['time'] = time();
    $cache['statuses'] = $statuses;
    $cache['pipelines'] = $pipelines;
    $cache['account'] = $account;
    file_put_contents('cache.json', json_encode($cache));
}


$users = json_decode(file_get_contents(__DIR__ . '/cache.json'), 1)['users'];
$statuses = json_decode(file_get_contents(__DIR__ . '/cache.json'), 1)['statuses'];
$pipelines = json_decode(file_get_contents(__DIR__ . '/cache.json'), 1)['pipelines'];
$account = json_decode(file_get_contents(__DIR__ . '/cache.json'), 1)['account'];

$data = json_decode($_POST, 1);

$statement = $pdo->query("CREATE TABLE IF NOT EXISTS b2bAnalytics (
                                       
                                        id	INTEGER,
                                        status_id	INTEGER,
                                         pipeline_id INTEGER,   
                                         old_status_id INTEGER,   
                                         old_pipeline_id INTEGER,
                                         timeL DATE ,   
                                         managerId INTEGER,
                                            PRIMARY KEY(id)
                                        )");

$id = $data['id'];
$status_id = $data['status_id'];
$pipeline_id = $data['pipeline_id'];
$old_status_id = $data['old_status_id'];
$old_pipeline_id = $data['old_pipeline_id'];
$timeL = time();
$pdo->query("INSERT INTO b2bAnalytics (id, status_id,pipeline_id,old_status_id,old_pipeline_id,timeL) VALUES ('$id', '$status_id', '$pipeline_id', '$old_status_id', '$old_pipeline_id','$timeL')");

$query = 'SELECT COUNT(*) FROM `b2bAnalytics`';
$result = $pdo->query($query);
$resultBd = $result->fetchAll();

if (preg_match('/00$/', $result)) {
$offset = $result - 100;
$query = "SELECT * FROM `b2bAnalytics` LIMIT '$offset',100";
$result = $pdo->query($query);
$resultBd = $result->fetchAll();

$idsArr = [];
foreach ($resultBd as $res) {
    $idsArr[] = $res['id'];
}


$leads = $api->lead->getAll(null, null, $idsArr)['result'];
foreach ($leads as $lead) {

    $contactsIds[] = $lead['main_contact_id'];
}
$contacts = $api->contact->getAll(null, $contactsIds)['result'];
foreach ($contacts as $contact) {
    $companyIds[] = $lead['linked_company_id'];
}

$companies = $api->company->getAll($companyIds)['result'];

foreach ($leads as $key => $lead) {
    foreach ($contacts as $contact) {
        if ($lead['main_contact_id'] == $contact['id']) $leads[$key]['contact'] = $contact;
    }
    foreach ($companies as $company) {
        if ($lead['linked_company_id'] == $company['id']) $leads[$key]['company'] = $company;
    }
}


foreach ($leads

as $key => $lead) {
    $tmpTags = [];
    $tmpLeadTags = [];


    $leadId              = $lead['id'];
    $date                = date('d-m-Y');
    $leadName            = $lead['name'];
    $leadPrice           = $lead['price'];
    $respUser            = $users[$lead['responsible_user_id']]['name'];
    $dateCreate          = $lead['date_create'];
    $createdUser         = $users[$lead['created_user_id']];
    foreach ($contacts[$key]['tags'] as $tag) {
        $tmpLeadTags[] = $tag['name'];
    }
    $leadTags            = implode(',', $tmpLeadTags);
    $leadStatus          = $statuses[$lead['pipeline_id'][$lead['status_id']]];
    $pipeline            = $pipelines[$lead['pipeline_id']];
    $dateClose           = date('d-m-Y', $lead['date_close']);
    $leadLink            = "skillbox.amocrm.ru/leads/detail/" . $lead['id'];
    $payType             = HAmo::getCFValue($lead, 1276339);
    $payMethod           = HAmo::getCFValue($lead, 1279198);
    $expectedPayDate     = HAmo::getCFValue($lead, 1279512);
    $payDate             = HAmo::getCFValue($lead, 1280552);
    $b2bQuery            = HAmo::getCFValue($lead, 1284480);
    $rsPeriod            = HAmo::getCFValue($lead, 1279380);
    $innerRsEgeBox       = HAmo::getCFValue($lead, 1284186);
    $lastPayment         = HAmo::getCFValue($lead, 1279582);
    $direction           = HAmo::getCFValue($lead, 1279192);
    $courseType          = HAmo::getCFValue($lead, 1279194);
    $courses             = implode(',', HAmo::getCFValues($lead, 1279228));
    $courseEgeBox        = HAmo::getCFValue($lead, 1284278);
    $b2bSource           = HAmo::getCFValue($lead, 1284478);
    $accessAmount        = HAmo::getCFValue($lead, 1284496);
    $source              = HAmo::getCFValue($lead, 1279190);
    $solutionResp        = HAmo::getCFValue($lead, 1284168);
    $examSubjects        = implode(',', HAmo::getCFValues($lead, 1284170));
    $stydyStartTime      = HAmo::getCFValue($lead, 1284172);
    $city                = HAmo::getCFValue($lead, 736612);
    $sex                 = HAmo::getCFValue($lead, 1272765);
    $bankContractSigner  = HAmo::getCFValue($lead, 1277444);
    $testAccess          = HAmo::getCFValue($lead, 1279204);
    $promo               = HAmo::getCFValue($lead, 1284494);
    $lossReason          = HAmo::getCFValue($lead, 1279528);
    $lossReasonB2B       = HAmo::getCFValue($lead, 1283600);
    $lossComment         = HAmo::getCFValue($lead, 1283920);
    $opCall              = HAmo::getCFValue($lead, 1280440);
    $opCallTime          = HAmo::getCFValue($lead, 1280442);
    $leadComment         = HAmo::getCFValue($lead, 1283920);
    $managerKc           = HAmo::getCFValue($lead, 1279396);
    $responsOp           = HAmo::getCFValue($lead, 1282110);
    $closeStatus         = HAmo::getCFValue($lead, 1282090);
    $salesDepartment     = HAmo::getCFValue($lead, 1282124);
    $salesGroup          = HAmo::getCFValue($lead, 1282372);
    $groupHeads          = HAmo::getCFValue($lead, 1284182);
    $country             = HAmo::getCFValue($lead, 1284302);
    $departmentAssist    = HAmo::getCFValue($lead, 1284314);
    $employment          = HAmo::getCFValue($lead, 1278884);
    $gmt                 = HAmo::getCFValue($lead, 1284466);
    $b2bLinks            = HAmo::getCFValue($lead, 1284570);
    $b2bTmGroup          = HAmo::getCFValue($lead, 1284572);
    $successTransfer     = HAmo::getCFValue($lead, 1284574);
    $failTransfer        = HAmo::getCFValue($lead, 1284576);
    $contactId           = $lead['main_contact_id'];
    $contactName         = $lead['contact']['name'];
    $contactResp         = $users[$lead['contact']['responsible_user_id']]['name'];
    foreach ($lead['contact']['tags'] as $tag) {
        $tmpTags[] = $tag['name'];
    }
    $contactTags         = implode(',', $tmpTags);
    $contactCreatedUser  = $users[$lead['contact']['created_user_id']];
    $contactDateCreate   = date('d-m-Y', $lead['contact']['date_create']);
    $position            = HAmo::getCFValue($lead['contact'], 736562);
    $phone               = implode(',', HAmo::getCFValues($contacts['key'], 'PHONE'));
    $add                 = HAmo::getCFValue($lead['contact'], 1283714);
    $birthday            = HAmo::getCFValue($lead['contact'], 1283896);
    $contactStatus       = HAmo::getCFValue($lead['contact'], 1284174);
    $positionNew         = HAmo::getCFValue($lead['contact'], 1284198);
    $closedB2BLead       = HAmo::getCFValue($lead['contact'], 1284200);//flag
    $lpr                 = HAmo::getCFValue($lead['contact'], 1284268);//flag
    $contactCity         = HAmo::getCFValue($lead['contact'], 1284468);
    $contactGmt          = HAmo::getCFValue($lead['contact'], 1284470);
    $contactCompany      = $lead['contact']['linked_company_id'];

    $companyName         = $lead['company']['name'];
    $companyResp         = $users[$lead['company']['reponsible_user_id']];
    $companyCreateDate   = $lead['company']['date_create'];
    $companyCreatedUser  = $lead['company']['created_user_id'];
    foreach ($lead['company']['tags'] as $tag) {
        $tmpCompanyTags[] = $tag['name'];
    }
    $companyTags         = implode(',', $tmpCompanyTags);
    $companyLink         = "skillbox.amocrm.ru/companies/detail/" . $lead['company']['id'];
    $companyPhone        = HAmo::getCFValue($lead['company'], 'PHONE');
    $companyEmail        = HAmo::getCFValue($lead['company'], 'EMAIL');
    $web                 = HAmo::getCFValue($lead['company'], 736568);
    $adress              = HAmo::getCFValue($lead['company'], 736572);
    $business            = HAmo::getCFValue($lead['company'], 736572);
    $product             = HAmo::getCFValue($lead['company'], 736572);
    $companyEmployees    = HAmo::getCFValue($lead['company'], 1283664);
    $companyCorporate    = HAmo::getCFValue($lead['company'], 1284204);
    $companyCorporateAdr = HAmo::getCFValue($lead['company'], 1284206);
    $companyORGN         = HAmo::getCFValue($lead['company'], 1284208);
    $companyINN          = HAmo::getCFValue($lead['company'], 1284210);
    $companyKPP          = HAmo::getCFValue($lead['company'], 1284212);
    $companyFullName     = HAmo::getCFValue($lead['company'], 1284214);
    $companyBik          = HAmo::getCFValue($lead['company'], 1284216);
    $companyBank         = HAmo::getCFValue($lead['company'], 1284218);
    $coreAccount         = HAmo::getCFValue($lead['company'], 1284220);
    $signer              = HAmo::getCFValue($lead['company'], 1284658);
    $companyCity         = HAmo::getCFValue($lead['company'], 1284498);
    $brand               = HAmo::getCFValue($lead['company'], 1284602);

    $mainTableArr = [
        $leadId, $date, $leadName, $leadPrice, $respUser, $dateCreate, $createdUser, $leadTags, $leadStatus, $pipeline, $dateClose, $leadLink, $payType, $payMethod, $expectedPayDate, $payDate, $b2bQuery, $rsPeriod, $innerRsEgeBox, $lastPayment, $direction, $courseType, $courses, $courseEgeBox, $b2bSource, $accessAmount, $source, $solutionResp, $examSubjects, $stydyStartTime, $city, $sex, $bankContractSigner, $testAccess, $promo, $lossReason, $lossReasonB2B, $lossComment, $opCall, $opCallTime, $leadComment, $managerKc, $responsOp, $closeStatus, $salesDepartment, $salesGroup, $groupHeads, $country, $departmentAssist, $employment, $gmt, $b2bLinks, $b2bTmGroup, $successTransfer, $failTransfer, $contactId, $contactName, $contactResp, $contactTags, $contactCreatedUser, $contactCreatedUser, $contactDateCreate, $position, $phone, $add, $birthday, $contactStatus, $positionNew, $closedB2BLead, $lpr, $contactCity, $contactGmt, $contactCompany, $companyName, $companyResp, $companyCreateDate, $companyCreatedUser, $companyTags, $companyLink, $companyPhone, $companyEmail, $web, $adress, $business, $product, $companyEmployees, $companyCorporate, $companyCorporateAdr, $companyORGN, $companyINN, $companyKPP, $companyFullName, $companyBik, $companyBank, $coreAccount, $signer, $companyCity, $brand];

    print_r($mainTableArr);
    $mainTable = new basicTableClass('Лист1');
    $mainTable->fillTable($mainTableArr);


    $query = "SELECT * FROM `b2bAnalytics` WHERE `id` = '$leadId'";
    $result = $pdo->query($query);
    $resultBd = $result->fetchAll();
    foreach ($resultBd as $bdElem) {
        if ($bdElem['status_id'] == 29379399) {
            $opTime = $bdElem['timeL'];
        }
        if ($bdElem['status_id'] == 142) {
            $succesTime = $bdElem['timeL'];
        }

        if ($bdElem['status_id'] == 24966657) {
            $billTime = $bdElem['timeL'];
        }
        if ($bdElem['status_id'] == 32438883) {
            $kpTime = $bdElem['timeL'];
        }

    }


    $opTime = 1111111;
    $successTime = 11111111;
    $billTime = 11111111;
    $kpTime = 444444444;
    $diffTime = date('d', ($succesTime - $opTime));
    $opTime = date('d-m-Y', $opTime);
    $succesTime = date('d-m-Y', $succesTime);
    $billTime = date('d-m-Y', $billTime);

    if ($lead['status_id'] == 142) {
        $usersSalesArr = [$date, $leadId, $leadPrice, $respUser];
        $usersSales = new basicTableClass('Лист2');
        $usersSales->fillTable($usersSalesArr);

        $productSalesArr = [$date, $leadId, $courses, $direction, $leadPrice];
        $productSales = new basicTableClass('Лист3');
        $productSales->fillTable($productSalesArr);


        $clientSalesArr = [$date, $leadId, $companyName, $companyLink, $b2bSource, $courses, $direction, $respUser, $opTime, $succesTime, $diffTime, $leadPrice];
        $clientSales = new basicTableClass('Лист4');
        $clientSales->fillTable($clientSalesArr);

    }


    if ($lead['status_id'] != 142) {
        //TODO ДАТА ОТПРАВКИ КП УЗНАЙ
        $monthPotentialArr = [$date, $leadId, $companyName, $leadLink, $b2bSource, $courses, $direction, $respUser, $opTime, $kpTime, $leadPrice, $billTime];
        $productSales = new basicTableClass('Лист4');
        $productSales->fillTable($productSalesArr);
    }

    $currentMonth = new monthTableClass('Лист5');
    $currentMonth->fillMonth($users, $statuses, $account, $lead, $leadPrice, $respUser, $time);


    $resultBd[0]['timeL'];

    $response1 = $api->note->getAll($leadId, 1, null, 10, null)['result'];
    $response2 = $api->note->getAll($leadId, 1, null, 11, null)['result'];
    $allCalls = array_merge($response1, $response2);
    $callsArr = [];

    foreach ($allCalls as $call) {
        $callText = json_decode($call['text'], 1);
        $callsArr[] = [time(), $respUser, $call['date_create'], $callText['DURATION']];

    }
    $calls = new basicTableClass('Лист 6');
    $calls->fillTable($callsArr);


    $responseMail = $api->note->getAll($leadId, 1, $resultBd[0]['timeL'], 15, null)['result'];
    foreach ($responseMail as $mail) {
        $mailArr[] = [time(), $respUser];
    }
    $mailArr = [];
    $mail = new basicTableClass('Лист 7');
    $mail->fillTable($mailArr);

    if ($lead['pipeline_id'] == 3777729) {


        $marketArr = [time(), $respUser];
        if ($lead['status_id'] == 142) $marketArr[] = 'true';
        $market = new basicTableClass('Лист 8');
        $market->fillTable($marketArr);


    }


    $respUserId = $lead['responsible_user_id'];
    $pdo->query("INSERT INTO b2bAnalytics (`managerId`) VALUES ('$respUserId') WHERE `id` = '$leadId'");


    $timeWork = json_decode(file_get_contents(__DIR__ . '/cache.json'), 1)['time'];
    if ($timeWork + 24 * 60 * 60 < time()) {
        $workClients = new clientsTableClass('Лист9');
        $workClients->fillClients($users, $pdo);
    }

}

?>


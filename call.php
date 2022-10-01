<?php
if ($_GET || $_POST){
    require_once 'db.php';
    $json = file_get_contents('settings.json');
    $settings = json_decode($json);
    $bob = new Db($settings->servername, $settings->username, $settings->password, $settings->db);
}   else    {
    die('');
}
if ($_GET){
    switch ($_GET['type']){
        case 'gas':
            $price = getPrice();
            $gas = $bob->getLog($_GET['person'], $price);
            print json_encode(['data' => $gas], JSON_PRETTY_PRINT);
            break;
        case 'price':
            print getPrice();
            break;
    }

}   elseif ($_POST){
    switch($_POST['type']){
        case 'new':
            $price = getPrice();
            $bob->newLog($_POST['when'], $_POST['who'], $_POST['much'], $price);
            break;
    }
}

function getPrice(){
    $curl = curl_init('https://www.gasbuddy.com/gaspricemap/county?lat=34.400669&lng=-118.913757&usa=true');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, []);
    $resp = curl_exec($curl);
    return json_decode($resp, true)[0]['Price'];
}

?>
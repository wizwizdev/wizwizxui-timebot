<?php
session_start();
ob_start();
//include '../functions/city.php';
//include '../functions/car.php';
//include '../functions/user.php';
include '../functions/estekhdam.php';
include '../functions/orders.php';
include '../functions/functions.php';

require_once("zarinpal_function.php");

//$res = payestekhdam();

$MerchantID 	= "e89b483e-4a97-11e9-8be6-000c29344811";
$Amount 		= '1000';
$ZarinGate 		= false;
$SandBox 		= true;

$zp 	= new zarinpal();
$result = $zp->verify($MerchantID, $Amount, $SandBox, $ZarinGate);

if (isset($result["Status"]) && $result["Status"] == 100) {
    // Success
    orders($result);

    echo "تراکنش با موفقیت انجام شد";
    echo "<br />مبلغ : ". $result["Amount"];
    echo "<br />کد پیگیری : ". $result["RefID"];
    echo "<br />Authority : ". $result["Authority"];
} else {
    // error
    echo "پرداخت ناموفق";
    echo "<br />کد خطا : ". $result["Status"];
    echo "<br />تفسیر و علت خطا : ". $result["Message"];
}

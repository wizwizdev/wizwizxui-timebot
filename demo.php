<?php

session_start();
ob_start();
$res = payestekhdam();

$dfd = '1000';
$MerchantID 	= "";
$Amount 		= '1000';
$Description 	= "تراکنش زرین پال";
$Email 			= "a@a.com";
$Mobile 		= '';
$CallbackURL 	= "http://127.0.0.1/wizwiznpal/verify.php?add=$res[id]";
$ZarinGate 		= false;
$SandBox 		= true;

$zp 	= new zarinpal();
$result = $zp->request($MerchantID, $Amount, $Description, $Email, $Mobile, $CallbackURL, $SandBox, $ZarinGate);

if (isset($result["Status"]) && $result["Status"] == 100)
{
    // Success and redirect to pay
    $zp->redirect($result["StartPay"]);
} else {
    // error
    echo "خطا در ایجاد تراکنش";
    echo "<br />کد خطا : ". $result["Status"];
    echo "<br />تفسیر و علت خطا : ". $result["Message"];
}


//$res = payestekhdam();

$MerchantID 	= "";
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

private function curl_check()
    {
        return (function_exists('curl_version')) ? true : false;
    }

    private function soap_check()
    {
        return (extension_loaded('soapx')) ? true : false;
    }

    private function error_message($code, $desc, $cb, $request=false)
    {
        if (empty($cb) && $request === true)
        {
            return "لینک بازگشت ( CallbackURL ) نباید خالی باشد";
        }

        if (empty($desc) && $request === true)
        {
            return "توضیحات تراکنش ( Description ) نباید خالی باشد";
        }


        $error = array(
            "-1" 	=> "اطلاعات ارسال شده ناقص است.",
            "-2" 	=> "IP و يا مرچنت كد پذيرنده صحيح نيست",
            "-3" 	=> "با توجه به محدوديت هاي شاپرك امكان پرداخت با رقم درخواست شده ميسر نمي باشد",
            "-4" 	=> "سطح تاييد پذيرنده پايين تر از سطح نقره اي است.",
            "-11" 	=> "درخواست مورد نظر يافت نشد.",
            "-12" 	=> "امكان ويرايش درخواست ميسر نمي باشد.",
            "-21" 	=> "هيچ نوع عمليات مالي براي اين تراكنش يافت نشد",
            "-22" 	=> "تراكنش نا موفق ميباشد",
            "-33" 	=> "رقم تراكنش با رقم پرداخت شده مطابقت ندارد",
            "-34" 	=> "سقف تقسيم تراكنش از لحاظ تعداد يا رقم عبور نموده است",
            "-40" 	=> "اجازه دسترسي به متد مربوطه وجود ندارد.",
            "-41" 	=> "اطلاعات ارسال شده مربوط به AdditionalData غيرمعتبر ميباشد.",
            "-42" 	=> "مدت زمان معتبر طول عمر شناسه پرداخت بايد بين 30 دقيه تا 45 روز مي باشد.",
            "-54" 	=> "درخواست مورد نظر آرشيو شده است",
            "100" 	=> "عمليات با موفقيت انجام گرديده است.",
            "101" 	=> "عمليات پرداخت موفق بوده و قبلا PaymentVerification تراكنش انجام شده است.",
        );

        if (array_key_exists("{$code}", $error))
        {
            return $error["{$code}"];
        } else {
            return "خطای نامشخص هنگام اتصال به درگاه زرین پال";
        }
    }


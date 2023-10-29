<?php
if(!file_exists("baseInfo.php") || !file_exists("config.php")){
    form("فایل های مورد نیاز یافت نشد");
    exit();
}
// ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require "baseInfo.php";
require "config.php";
include "jdf.php";


if(isset($_REQUEST['id'])){
    $config_link = $_REQUEST['id'];

        if(preg_match('/^vmess:\/\/(.*)/',$config_link,$match)){
            $jsonDecode = json_decode(base64_decode($match[1]),true);
            $connectionLink = $config_link;
            $config_link = $jsonDecode['id'];
        }elseif(preg_match('/^vless:\/\/(.*?)\@/',$config_link,$match)){
            $connectionLink = $config_link;
            $config_link = $match[1];
        }elseif(preg_match('/^trojan:\/\/(.*?)\@/',$config_link,$match)){
            $connectionLink = $config_link;
            $config_link = $match[1];
        }elseif(!preg_match('/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $config_link)
            && !(preg_match('/^[a-zA-Z0-9]{5,15}/',$config_link))){
            form("متن وارد شده معتبر نمی باشد");
            exit();
        }

    $stmt = $connection->prepare("SELECT * FROM `server_config`");
    $stmt->execute();
    $serversList = $stmt->get_result();
    $stmt->close();
    $found = false;
    while($row = $serversList->fetch_assoc()){
        $serverId = $row['id'];
        $response = getJson($serverId);
        if($response->success){
            $list = json_encode($response->obj);

            if(strpos($list, $config_link)){
                $found = true;
                $list = $response->obj;
                if(!isset($list[0]->clientStats)){
                    foreach($list as $keys=>$packageInfo){
                        if(strpos($packageInfo->settings, $config_link)!=false){
                            $remark = $packageInfo->remark;
                            $upload = sumerize2($packageInfo->up);
                            $download = sumerize2($packageInfo->down);
                            $state = $packageInfo->enable == true?"فعال 🟢":"غیر فعال 🔴";
                            $totalUsed = sumerize2($packageInfo->up + $packageInfo->down);
                            $total = $packageInfo->total!=0?sumerize2($packageInfo->total):"نامحدود";
                            $expiryTime = $packageInfo->expiryTime != 0?jdate("Y-m-d H:i:s",substr($packageInfo->expiryTime,0,-3)):"نامحدود";
                            $leftMb = $packageInfo->total!=0?sumerize2($packageInfo->total - $packageInfo->up - $packageInfo->down):"نامحدود";
                            $expiryDay = $packageInfo->expiryTime != 0?
                                floor(
                                    (substr($packageInfo->expiryTime,0,-3)-time())/(60 * 60 * 24))
                                :
                                "نامحدود";
                            if(is_numeric($expiryDay)){
                                if($expiryDay<0) $expiryDay = 0;
                            }
                            break;
                        }
                    }
                }
                else{
                    $keys = -1;
                    $settings = array_column($list,'settings');
                    foreach($settings as $key => $value){
                        if(strpos($value, $config_link)!= false){
                            $keys = $key;
                            break;
                        }
                    }
                    if($keys == -1){
                        $found = false;
                        break;
                    }
                    $clientsSettings = json_decode($list[$keys]->settings,true)['clients'];
                    if(!is_array($clientsSettings)){
                        form("با عرض پوزش، متأسفانه مشکلی رخ داده است، لطفا مجدد اقدام کنید");
                        exit();
                    }
                    $settingsId = array_column($clientsSettings,'id');
                    $settingKey = array_search($config_link,$settingsId);

                    if(!isset($clientsSettings[$settingKey]['email'])){
                        $packageInfo = $list[$keys];
                        $remark = $packageInfo->remark;
                        $upload = sumerize2($packageInfo->up);
                        $download = sumerize2($packageInfo->down);
                        $state = $packageInfo->enable == true?"فعال 🟢":"غیر فعال 🔴";
                        $totalUsed = sumerize2($packageInfo->up + $packageInfo->down);
                        $total = $packageInfo->total!=0?sumerize2($packageInfo->total):"نامحدود";
                        $expiryTime = $packageInfo->expiryTime != 0?jdate("Y-m-d H:i:s",substr($packageInfo->expiryTime,0,-3)):"نامحدود";
                        $leftMb = $packageInfo->total!=0?sumerize2($packageInfo->total - $packageInfo->up - $packageInfo->down):"نامحدود";
                        if(is_numeric($leftMb)){
                            if($leftMb<0){
                                $leftMb = 0;
                            }else{
                                $leftMb = sumerize2($packageInfo->total - $packageInfo->up - $packageInfo->down);
                            }
                        }


                        $expiryDay = $packageInfo->expiryTime != 0?
                            floor(
                                (substr($packageInfo->expiryTime,0,-3)-time())/(60 * 60 * 24)
                            ):
                            "نامحدود";
                        if(is_numeric($expiryDay)){
                            if($expiryDay<0) $expiryDay = 0;
                        }
                    }else{
                        $email = $clientsSettings[$settingKey]['email'];
                        $clientState = $list[$keys]->clientStats;
                        $emails = array_column($clientState,'email');
                        $emailKey = array_search($email,$emails);
                        if($clientState[$emailKey]->total != 0 || $clientState[$emailKey]->up != 0  ||  $clientState[$emailKey]->down != 0 || $clientState[$emailKey]->expiryTime != 0){
                            $upload = sumerize2($clientState[$emailKey]->up);
                            $download = sumerize2($clientState[$emailKey]->down);
                            $total = $clientState[$emailKey]->total==0 && $list[$keys]->total !=0?$list[$keys]->total:$clientState[$emailKey]->total;
                            $leftMb = $total!=0?($total - $clientState[$emailKey]->up - $clientState[$emailKey]->down):"نامحدود";
                            if(is_numeric($leftMb)){
                                if($leftMb<0){
                                    $leftMb = 0;
                                }else{
                                    $leftMb = sumerize2($total - $clientState[$emailKey]->up - $clientState[$emailKey]->down);
                                }
                            }
                            $totalUsed = sumerize2($clientState[$emailKey]->up + $clientState[$emailKey]->down);
                            $total = $total!=0?sumerize2($total):"نامحدود";
                            $expTime = $clientState[$emailKey]->expiryTime == 0 && $list[$keys]->expiryTime?$list[$keys]->expiryTime:$clientState[$emailKey]->expiryTime;
                            $expiryTime = $expTime != 0?jdate("Y-m-d H:i:s",substr($expTime,0,-3)):"نامحدود";
                            $expiryDay = $expTime != 0?
                                floor(
                                    ((substr($expTime,0,-3)-time())/(60 * 60 * 24))
                                ):
                                "نامحدود";
                            if(is_numeric($expiryDay)){
                                if($expiryDay<0) $expiryDay = 0;
                            }
                            $state = $clientState[$emailKey]->enable == true?"فعال 🟢":"غیر فعال 🔴";
                            $remark = $email;
                        }
                        elseif($list[$keys]->total != 0 || $list[$keys]->up != 0  ||  $list[$keys]->down != 0 || $list[$keys]->expiryTime != 0){
                            $upload = sumerize2($list[$keys]->up);
                            $download = sumerize2($list[$keys]->down);
                            $leftMb = $list[$keys]->total!=0?($list[$keys]->total - $list[$keys]->up - $list[$keys]->down):"نامحدود";
                            if(is_numeric($leftMb)){
                                if($leftMb<0){
                                    $leftMb = 0;
                                }else{
                                    $leftMb = sumerize2($list[$keys]->total - $list[$keys]->up - $list[$keys]->down);
                                }
                            }
                            $totalUsed = sumerize2($list[$keys]->up + $list[$keys]->down);
                            $total = $list[$keys]->total!=0?sumerize2($list[$keys]->total):"نامحدود";
                            $expiryTime = $list[$keys]->expiryTime != 0?jdate("Y-m-d H:i:s",substr($list[$keys]->expiryTime,0,-3)):"نامحدود";
                            $expiryDay = $list[$keys]->expiryTime != 0?
                                floor(
                                    ((substr($list[$keys]->expiryTime,0,-3)-time())/(60 * 60 * 24))
                                ):
                                "نامحدود";
                            if(is_numeric($expiryDay)){
                                if($expiryDay<0) $expiryDay = 0;
                            }
                            $state = $list[$keys]->enable == true?"فعال 🟢":"غیر فعال 🔴";
                            $remark = $list[$keys]->remark;
                        }
                    }
                }
                break;
            }
        }
    }
    if(!$found){
        form("اطلاعات وارد شده اشتباه می باشد",$cancelKey);
    }else{
        showForm("configInfo");
    }
}
else{
    showForm("unknown");
}
?>
<?php
function showForm($type){
    global $remark, $state, $upload, $download, $total, $leftMb, $expiryTime, $expiryDay;
    ?>
    <html lang="en">
    <head>
        <meta charset="utf-8"><meta name="viewport" content="width=device-width">
        <title><?php if($type=="unknown") echo "جستجوی اطلاعات کانفیگ";
            elseif ($type=="id") echo "نتیجه اطلاعات کانفیگ";
            ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link type="text/css" href="assets/webconf.css" rel="stylesheet" />
    </head>
    <body style="background: <?php if(!isset($state)) echo "#f7f0f5"; elseif($state) echo "#f7f0f5"; elseif(!$state) echo "#FF5733";?>;">
    <?php if ($type=="configInfo"){
        $download = $download != 0 && $total != "نامحدود"? round(100 * $download / $total,2):0;
        $upload = $upload != 0 && $total != "نامحدود"? round(100 * $upload / $total,2):0;
        $leftMb = $leftMb != "نامحدود" && $total != "نامحدود"?round(100 * $leftMb / $total,2):"100";
        ?>
        <div class="container" style="">
            <form id="contact" class="contactw">
                <br>
                <p style="font-size:22px;font-weight: bold;color:#1d3557;font-family:iransans !important;"> ( اطلاعات کانفیگ <?php echo $remark;?> ) </p>
                <p style="font-size:18px;font-weight: bold;color:#1d3557;margin-top:15px;"> وضعیت: <?php echo $state;?> </p>

                <br>
                
                
                <div class="mainform" >
    
                    <div>
                    <svg xmlns="http://www.w3.org/2000/svg" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="margin-left: 6px;enable-background:new 0 0 512 512;" xml:space="preserve" width="20" height="20">
                        <g>
                            <path d="M210.731,386.603c24.986,25.002,65.508,25.015,90.51,0.029c0.01-0.01,0.019-0.019,0.029-0.029l68.501-68.501   c7.902-8.739,7.223-22.23-1.516-30.132c-8.137-7.357-20.527-7.344-28.649,0.03l-62.421,62.443l0.149-329.109   C277.333,9.551,267.782,0,256,0l0,0c-11.782,0-21.333,9.551-21.333,21.333l-0.192,328.704L172.395,288   c-8.336-8.33-21.846-8.325-30.176,0.011c-8.33,8.336-8.325,21.846,0.011,30.176L210.731,386.603z"/>
                            <path d="M490.667,341.333L490.667,341.333c-11.782,0-21.333,9.551-21.333,21.333V448c0,11.782-9.551,21.333-21.333,21.333H64   c-11.782,0-21.333-9.551-21.333-21.333v-85.333c0-11.782-9.551-21.333-21.333-21.333l0,0C9.551,341.333,0,350.885,0,362.667V448   c0,35.346,28.654,64,64,64h384c35.346,0,64-28.654,64-64v-85.333C512,350.885,502.449,341.333,490.667,341.333z"/>
                        </g>
                    </svg>
                        <p style="font-size:16px">حجم دانلود</p>
                        <div class="progress-bar" style="display:flex; background: radial-gradient(closest-side, #F9F9F9 79%, transparent 80% 100%),conic-gradient(<?php if($download <= 50) echo "#04a777 "; elseif($download <= 70 && $download > 50) echo "yellow "; elseif($download > 70) echo "red "; echo $download . "%";?>, #e2eafc 0);">
                        <?php echo $download . "%";?></div>
                    </div>
                    
                    <div style="margin-right:50px;">
                        <svg style="margin-left: 6px" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="20" height="20"><path d="M23.9,11.437A12,12,0,0,0,0,13a11.878,11.878,0,0,0,3.759,8.712A4.84,4.84,0,0,0,7.113,23H16.88a4.994,4.994,0,0,0,3.509-1.429A11.944,11.944,0,0,0,23.9,11.437Zm-4.909,8.7A3,3,0,0,1,16.88,21H7.113a2.862,2.862,0,0,1-1.981-.741A9.9,9.9,0,0,1,2,13,10.014,10.014,0,0,1,5.338,5.543,9.881,9.881,0,0,1,11.986,3a10.553,10.553,0,0,1,1.174.066,9.994,9.994,0,0,1,5.831,17.076ZM7.807,17.285a1,1,0,0,1-1.4,1.43A8,8,0,0,1,12,5a8.072,8.072,0,0,1,1.143.081,1,1,0,0,1,.847,1.133.989.989,0,0,1-1.133.848,6,6,0,0,0-5.05,10.223Zm12.112-5.428A8.072,8.072,0,0,1,20,13a7.931,7.931,0,0,1-2.408,5.716,1,1,0,0,1-1.4-1.432,5.98,5.98,0,0,0,1.744-5.141,1,1,0,0,1,1.981-.286Zm-5.993.631a2.033,2.033,0,1,1-1.414-1.414l3.781-3.781a1,1,0,1,1,1.414,1.414Z"/></svg>
                        <p style="font-size:16px; font-family:iransans !important;">حجم آپلود</p>
                        <div class="progress-bar" style="display:flex; background: radial-gradient(closest-side, #F9F9F9 79%, transparent 80% 100%),conic-gradient(<?php if($upload <= 30) echo "#f48c06 "; elseif($upload < 50 && $upload > 30) echo "yellow "; elseif($upload >= 50) echo "#ed254e ";  echo $upload . "%";?>, #e2eafc 0);">
                        <?php echo $upload . "%";?></div>
                    </div>
                </div>
                
                
                
                <div class="mainform" style="margin-top:50px;">
                    
                    <div style="margin-left: 6px">
                        <svg style="margin-left: 6px" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="20" height="20"><path d="M23.9,11.437A12,12,0,0,0,0,13a11.878,11.878,0,0,0,3.759,8.712A4.84,4.84,0,0,0,7.113,23H16.88a4.994,4.994,0,0,0,3.509-1.429A11.944,11.944,0,0,0,23.9,11.437Zm-4.909,8.7A3,3,0,0,1,16.88,21H7.113a2.862,2.862,0,0,1-1.981-.741A9.9,9.9,0,0,1,2,13,10.014,10.014,0,0,1,5.338,5.543,9.881,9.881,0,0,1,11.986,3a10.553,10.553,0,0,1,1.174.066,9.994,9.994,0,0,1,5.831,17.076ZM7.807,17.285a1,1,0,0,1-1.4,1.43A8,8,0,0,1,12,5a8.072,8.072,0,0,1,1.143.081,1,1,0,0,1,.847,1.133.989.989,0,0,1-1.133.848,6,6,0,0,0-5.05,10.223Zm12.112-5.428A8.072,8.072,0,0,1,20,13a7.931,7.931,0,0,1-2.408,5.716,1,1,0,0,1-1.4-1.432,5.98,5.98,0,0,0,1.744-5.141,1,1,0,0,1,1.981-.286Zm-5.993.631a2.033,2.033,0,1,1-1.414-1.414l3.781-3.781a1,1,0,1,1,1.414,1.414Z"/></svg>
                        <p style="font-size:16px; font-family:iransans !important;">حجم باقیمانده</p>
                        <div class="progress-bar" style="display:flex; background: radial-gradient(closest-side, #F9F9F9 79%, transparent 80% 100%),conic-gradient(<?php if($leftMb <= 30) echo "red "; elseif($leftMb < 50 && $leftMb > 30) echo "yellow "; elseif($leftMb >= 50) echo "#ed254e ";  echo $leftMb . "%";?>, #e2eafc 0);">
                        <?php echo $leftMb . "%";?></div>
                    </div>
                    
                    <div style="margin-right:50px;">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Bold" viewBox="0 0 24 24" width="20" height="20"><path d="M22.5,18a1.5,1.5,0,0,1-1.061-.44L13.768,9.889a2.5,2.5,0,0,0-3.536,0L2.57,17.551A1.5,1.5,0,0,1,.449,15.43L8.111,7.768a5.505,5.505,0,0,1,7.778,0l7.672,7.672A1.5,1.5,0,0,1,22.5,18Z"/></svg>
                        <p style="font-size:16px">حجم کلی</p>
                        <div class="progress-bar" style="display:flex; background: radial-gradient(closest-side, #F9F9F9 79%, transparent 80% 100%),conic-gradient(<?php if($upload <= 50) echo "#467599 "; elseif($upload <= 70 && $upload > 50) echo "#467599 "; elseif($upload > 70) echo "#467599 "; echo $upload . "%";?>, #467599 0);">
                        <?php echo (is_numeric($total) ? $total . "GB": $total);?></div>
                    </div>
    
                    <!--<div style="margin-right:50px;">-->
                    <!--    <svg style="margin-left: 6px" id="Layer_1" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m23 13a11.01 11.01 0 0 0 -10-10.949v-2.051h-2v2.051a10.977 10.977 0 0 0 -7.062 18.408l-1.928 2.118 1.48 1.346 1.934-2.123a10.916 10.916 0 0 0 13.152 0l1.934 2.126 1.48-1.346-1.928-2.118a10.948 10.948 0 0 0 2.938-7.462zm-11 9a9 9 0 1 1 9-9 9.011 9.011 0 0 1 -9 9z"/><path d="m5.523 1.745-1.067-1.689a15.17 15.17 0 0 0 -4.439 3.955l1.663 1.109a13.144 13.144 0 0 1 3.843-3.375z"/><path d="m22.32 5.12 1.663-1.109a15.17 15.17 0 0 0 -4.439-3.955l-1.067 1.689a13.144 13.144 0 0 1 3.843 3.375z"/><path d="m11 7v5.414l3.293 3.293 1.414-1.414-2.707-2.707v-4.586z"/></svg>-->
                    <!--    <p style="font-size:16px">تعداد روز باقیمانده</p>-->
                    <!--    <div class="progress-bar" style="display:flex; background: radial-gradient(closest-side, #F9F9F9 79%, transparent 80% 100%),conic-gradient(#a06cd5 100%, #13293d 0);">-->
                    <!--    <?php echo $expiryDay . " روز";?></div>-->
                    <!--</div>-->
                </div>
        <div class="container">
                    <p class="tarikh" style="font-size:14px;margin-top:10px">
                       expireTime: <span><?php echo $expiryTime;?></span>
                    </p>
                </div>
                <p style="font-size:10px">Made with 🖤 in <a target="_blank" href="https://github.com/wizwizdev/wizwizxui-timebot">wizwiz</a></p>
            </form>
        </div>

    <?php }
    elseif($type=="unknown"){ ?>

        <div class="container">
            <form id="contact" action="search.php" method="get">
                <h3 style="margin:20px">لطفا اطلاعات خواسته شده را وارد کنید</h3>
                <fieldset>
                    <input placeholder="لینک اتصال و یا هم uuid کانفیگ را وارد کنید" type="text"  id="id" name="id" autocomplete="off" required >
                </fieldset>
                <fieldset>
                    <button class="search" type="submit">جستجو</button>
                </fieldset>
                <p style="font-size:13px">Made with 🖤 in <a target="_blank" href="https://github.com/wizwizdev/wizwizxui-timebot">wizwiz</a></p>
            </form>
        </div>
        <br>
        <br>
    <?php } ?>
    </body>
    </html>
    <?php
}
function form($msg, $error = true){
    ?>

    <html dir="rtl">
    <head>
        <meta charset="utf-8"><meta name="viewport" content="width=device-width">
        <title>error</title>
        <link type="text/css" href="assets/webconf.css" rel="stylesheet" />
        <meta name="next-head-count" content="4">
    </head>
    <body>
    <div id="__next">
        <section class="ant-layout1 PayPing-layout1">
            <main>
                <div class="justify-center align-center w-100">
                    <div class="div1">
                        <div class="div2">
                            <?php if ($error == true){ ?> <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" class="PayPing-icon" stroke-width="1" width="100">
                                <circle cx="12" cy="12" r="11"></circle>
                                <path d="M15.3 8.7l-6.6 6.6M8.7 8.7l6.6 6.6"></path>
                            </svg>
                            <?php }?>
                            <div style="padding: 40px 30px" > <?php echo $msg ?></div>
                        </div>
                    </div>
                </div>
            </main>
        </section>
    </div>
    </body>
    </html>
    <?php
}
?>

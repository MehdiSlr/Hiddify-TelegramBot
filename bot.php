<?php
require 'secrets.php';
define ('API_URL','https://api.telegram.org/bot'.BOT_TOKEN.'/');

function HiddifyCreate($name, $comment, $package_size, $telegram_id, $uuid){
    $domain = DOMAIN;
    $proxyPath = PROXY_PATH;
    $adminSecret = ADMIN_SECRET;
    $url = $domain.'/'.$proxyPath.'/'.$adminSecret.'/api/v1/user/';

    $headers = array(
        'Accept: application/json',
        'Content-Type: application/json',
    );

    $finalUuid = $uuid;

    $data = array(
        'added_by_uuid' => $adminSecret,
        'comment' => $comment,
        'current_usage_GB' => 0,
        'last_online' => null,
        'last_reset_time' => null,
        'mode' => 'no_reset',
        'name' => $name,
        'package_days' => 30,
        'start_date' => date('Y-m-d'),
        'telegram_id' => $telegram_id,
        'usage_limit_GB' => $package_size,
        'uuid' => $finalUuid
    );

    $data_string = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if ($result != null) {
        return $finalUuid;
    } else {
        return false;
    }
}

function delete($uuid){
    $domain = DOMAIN;
    $proxyPath = PROXY_PATH;
    $adminSecret = ADMIN_SECRET;
    $url = $domain.'/'.$proxyPath.'/'.$adminSecret.'/admin/user/delete/';

    $data = http_build_query([
        'id' => $uuid,
        'url' =>  "0aNLSeaFkFihkJeciaGGYmDwRLPS5H/05988fcb-1fcf-48b7-9dbe-222b260b30d7/admin/user/"
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);

    $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
}

function generateRandomUUID(){
    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function check($text){
    if(ADMIN == 'true'){
        $parts = explode(' ', $text); // Split by space
        if (count($parts) > 1) {
            if($parts[0] == '/start'){
                $subparts = explode('_', $parts[1]); // Split the second part by underscore
                $command = $subparts[0]; // "check"
                $uid = $subparts[1]; // "uid"
                if($command == 'check'){
                    return $uid;
                }
                else{
                    return $text;
                }
            }
            else{
                return $text;
            }
        }
        else{
            return $text;
        }
    }
    else{
        return $text;
    }
}

function cCheck($cdata){
    include 'serv_conf.php';
    $parts = explode('-', $cdata); // Split by dash
    if(count($parts) > 4){ //if its uuid
        $cCheck = explode('_',$cdata); //Split by underscore
        if(count($cCheck) > 1){
            $uuid = $cCheck[1];
            if($cCheck[0] == 'delete'){
                $sql = "SELECT uid FROM configs WHERE uuid = '$uuid'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                $uid = $row['uid'];
                $sql = "DELETE FROM configs WHERE uuid = '$uuid'";
                mysqli_query($conn, $sql);
                $msg = 'کانفیگ با موفقیت حذف شد 🙂
👉🏻 UUID: ' . $uuid;
                $keyboard = array(
                    'resize_keyboard' => true,
                    'inline_keyboard' => array(
                        array(
                            array('text' => '⤵️ | بازگشت', 'callback_data' => $uid)
                        )
                    )
                );
                msg(editMessageText ,array('chat_id'=>UID, 'message_id'=>CMSGID, 'text'=>$msg, 'reply_markup' => $keyboard));
            }
            elseif($cCheck[0] == 'renew'){
                $sql = "SELECT cmd FROM configs WHERE uuid = '$uuid'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                $cmd = $row['cmd'];
                $msg = text($cmd);
                
                //Normal Plans
                $normal_unlimit1u = array( //Normal Unlimit 1 User
                    array(
                        array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
                    array(
                        array('text' => '⤵️ | بازگشت', 'callback_data' => $uuid)
                    )
                );
                $normal_unlimit2u = array( //Normal Unlimit 2 User
                    array(
                        array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
                    array(
                        array('text' => '⤵️ | بازگشت', 'callback_data' => $uuid)
                    )
                );
                $normal_limit40g = array( //Normal Limited 40GB
                    array(
                        array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
                    array(
                        array('text' => '⤵️ | بازگشت', 'callback_data' => $uuid)
                    )
                );$normal_limit60g = array( //Normal Limited 60GB
                    array(
                        array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
                    array(
                        array('text' => '⤵️ | بازگشت', 'callback_data' => $uuid)
                    )
                );
                $normal_limit80g = array( //Normal Limited 80GB
                    array(
                        array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
                    array(
                        array('text' => '⤵️ | بازگشت', 'callback_data' => $uuid)
                    )
                );
                $normal_limit120g = array( //Normal Limited 120GB
                    array(
                        array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
                    array(
                        array('text' => '⤵️ | بازگشت', 'callback_data' => $uuid)
                    )
                );
                //VIP Plans
                $vip_unlimit1u = array( //VIP Unlimit 1 User
                    array(
                        array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
                    array(
                        array('text' => '⤵️ | بازگشت', 'callback_data' => $uuid)
                    )
                );
                $vip_unlimit2u = array( //VIP Unlimit 2 User
                    array(
                        array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
                    array(
                        array('text' => '⤵️ | بازگشت', 'callback_data' => $uuid)
                    )
                );
                $vip_limit40g = array( //VIP Limited 40GB
                    array(
                        array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
                    array(
                        array('text' => '⤵️ | بازگشت', 'callback_data' => $uuid)
                    )
                );$vip_limit60g = array( //VIP Limited 60GB
                    array(
                        array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
                    array(
                        array('text' => '⤵️ | بازگشت', 'callback_data' => $uuid)
                    )
                );
                $vip_limit80g = array( //VIP Limited 80GB
                    array(
                        array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
                    array(
                        array('text' => '⤵️ | بازگشت', 'callback_data' => $uuid)
                    )
                );
                $vip_limit120g = array( //VIP Limited 120GB
                    array(
                        array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
                    array(
                        array('text' => '⤵️ | بازگشت', 'callback_data' => $uuid)
                    )
                );

                if( $cmd == 'normal_unlimit1u' ){ $btns = $normal_unlimit1u; }
                if( $cmd == 'normal_unlimit2u' ){ $btns = $normal_unlimit2u; }
                if( $cmd == 'normal_limit40g' ) { $btns = $normal_limit40g; }
                if( $cmd == 'normal_limit60g' ) { $btns = $normal_limit60g; }
                if( $cmd == 'normal_limit80g' ) { $btns = $normal_limit80g; }
                if( $cmd == 'normal_limit120g' ){ $btns = $normal_limit120g; }

                if( $cmd == 'vip_unlimit1u' )   { $btns = $vip_unlimit1u; }
                if( $cmd == 'vip_unlimit2u' )   { $btns = $vip_unlimit2u; }
                if( $cmd == 'vip_limit40g' )    { $btns = $vip_limit40g; }
                if( $cmd == 'vip_limit60g' )    { $btns = $vip_limit60g; }
                if( $cmd == 'vip_limit80g' )    { $btns = $vip_limit80g; }
                if( $cmd == 'vip_limit120g' )   { $btns = $vip_limit120g; }

                $keyboard = array(
                    'resize_keyboard' => true,
                    'inline_keyboard' => $btns
                );
                msg(editMessageText ,array('chat_id'=>UID, 'text'=>$msg, 'message_id'=>CMSGID, 'reply_markup' => $keyboard, 'parse_mode' => 'HTML'));
            }
        }
        else{
            if(ADMIN == 'true' && STATUS == 'users'){
                $sql = "SELECT uid FROM configs WHERE uuid = '$cdata'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                $uid = $row['uid'];
                $msg = 'میخوای با این UUID چیکار کنی؟
👉🏻 <code>' . $cdata . '</code> 👇🏻';
                $keyboard = array(
                    'resize_keyboard' => true,
                    'inline_keyboard' => array(
                        array(
                            array('text' => '❌ | حذف کانفیگ', 'callback_data' => 'delete_' . $cdata)
                        ),
                        array(
                            array('text' => '⤵️ | بازگشت', 'callback_data' => $uid)
                        )
                    )
                );
                msg(editMessageText ,array('text'=>$msg, 'chat_id'=>UID, 'message_id'=>CMSGID, 'reply_markup' => $keyboard, 'parse_mode' => 'HTML'));
            }
            else{
                $config = getConfig(UID, $cdata);
                $msg = 
                    '😍 کانفیگ شما
📡 پروتکل: vless
💝 config : 
<code>' . $config . '</code>';
                $keyboard = array(
                    'resize_keyboard' => true,
                    'inline_keyboard' => array(
                        array(
                            array('text' => '🗒️ | مشخصات کانفیگ', 'url' => 'https://admin-4.innozoneshop.com/xauAJSHwlndetDCtGvKx08Zu/'.$cdata)
                        ),
                        array(
                            array('text' => '♻️ | تمدید اشتراک', 'callback_data' => 'renew_' . $cdata)
                        ),
                        array(
                            array('text' => '⤵️ | بازگشت', 'callback_data' => 'configs')
                        )
                    )
                );
                msg(editMessageText ,array('chat_id'=>UID, 'message_id'=>CMSGID, 'text'=>$msg, 'reply_markup' => $keyboard , 'parse_mode' => 'HTML'));
            }
        }
    }
    else{
        return $cdata;
    }
}

function getConfig($uid, $uuid){
    include 'serv_conf.php';
    $domain = DOMAIN;
    $configPath = CONFIG_PATH;
    $url = $domain."/".$configPath."/".$uuid."/api/v2/user/all-configs/";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json'
    ));
    $response = curl_exec($curl);

    if ($response === false) {
        $error = curl_error($curl);
        echo "cURL Error: $error";
    } else {
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http_code == 200) {
            // Request was successful, handle response
            $data = json_decode($response, true);
            $link = $data['7']['link'];
            // Do something with $data
            $sql = "SELECT type FROM users WHERE uid = '$uid'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $type = $row['type'];

            if($type == 'normal'){
                return $link;
            }elseif($type == 'vip'){
                $vlink = str_replace("www-4", "site", $link);
                return $vlink;
            }
        } else {
            // Request failed
            echo "HTTP Code: $http_code";
            // Handle other HTTP response codes if needed
        }
    }

    curl_close($curl);


}

function getUID($callback_message)
{
    $start = strpos($callback_message, "شناسه :   ") + strlen("شناسه :   ");
    $end = strpos($callback_message, " \n    🏖 نام کاربری");
    $uid = substr($callback_message, $start, $end - $start);
    return $uid;
}

function getPlan($uid)
{
    include 'serv_conf.php';
    $sql = "SELECT plan FROM users WHERE uid = '$uid'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if (empty($row['plan']))
    {
        return 'نامشخص';
    }
    $plan = $row['plan'];
    return $plan;
}
function getPrice($uid)
{
    include 'serv_conf.php';
    $sql = "SELECT price FROM users WHERE uid = '$uid'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if (empty($row['price']))
    {
        return 'نامشخص';
    }
    $price = $row['price'];
    return $price;
}

function getConfigs($uid){
    include 'serv_conf.php';
    $ssql = "SELECT status FROM users WHERE uid = UID";
    $sresult = mysqli_query($conn, $ssql);
    $srow = mysqli_fetch_assoc($sresult);
    if($srow['status'] == 'active'){
        $back = 'back';
    }
    else{
        $back = 'users';
    }
    $sql = "SELECT uuid FROM configs WHERE uid = '$uid'";
    $result = mysqli_query($conn, $sql);
    $configs = array();
    if (mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_assoc($result)) {
            $configs[] = array(array('text' => $row['uuid'], 'callback_data' => $row['uuid']));
        }
        $configs[] = array(array('text' => '⤵️ | بازگشت', 'callback_data' => $back));
    }
    else{
        $configs = array(
            array(array('text' => '🙄 | هیچ سفارشی وجود ندارد', 'callback_data' => $back)),
            array(array('text' => '⤵️ | بازگشت', 'callback_data' => $back))
        );
    }
    $btns = $configs;
    $keyboard = array(
        'resize_keyboard' => true,
        'inline_keyboard' => $btns
    );
    return $keyboard;
}

function orderList()
{
    include 'serv_conf.php';
    $sql = "SELECT uid FROM users WHERE pay = 'paid'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_error($conn))
    {
        $order = array(array(array('text' => mysqli_error($conn), 'callback_data' => 'back')));
    }
    else
    {
        if (mysqli_num_rows($result) > 0)
        {
            $orders = array(); // Array to store order buttons
            while ($row = mysqli_fetch_assoc($result))
            {
                // For each order, create a button and add it to the main array
                $orders[] = array(array('text' => '👤 | ' . $row['uid'] , 'callback_data' => $row['uid']));
            }
            // Add a button for back navigation
            $orders[] = array(array('text' => '⤵️ | Back', 'callback_data' => 'back'));
        }
        else
        {
            $order = array(
                            array(array('text' => '🙄 | هیچ سفارشی وجود ندارد', 'callback_data' => 'back')),
                            array(array('text' => '⤵️ | بازگشت', 'callback_data' => 'back'))
                        );
        }
    }
    return $order;
}

function userList(){
    include 'serv_conf.php';
    $sql = "SELECT uid,name FROM users";
    $result = mysqli_query($conn, $sql);
    if (mysqli_error($conn)){
        $order = array(array(array('text' => mysqli_error($conn), 'callback_data' => 'back')));
    }else{
        if (mysqli_num_rows($result) > 0){
            $users = array(); // آرایه‌ای برای ذخیره دکمه‌های کاربران
            while ($row = mysqli_fetch_assoc($result)){
                // برای هر کاربر یک دکمه ایجاد می‌کنیم و به آرایه اصلی اضافه می‌کنیم
                $users[] = array(array('text' => '👤 | ' . $row['name'] , 'callback_data' => $row['uid']));
            }
            // دکمه‌ای برای بازگشت نیز اضافه می‌کنیم
            $users[] = array(array('text' => '⤵️ | بازگشت', 'callback_data' => 'back'));
        }else{
            $users = array(
                            array(array('text' => '🙄 | هیچ کاربری وجود ندارد', 'callback_data' => 'back')),
                            array(array('text' => '⤵️ | بازگشت', 'callback_data' => 'back'))
                        );
        }
    }
    return $users;
}

function userdata($uid)
{
    include 'serv_conf.php';
    $sql = "SELECT * FROM users WHERE uid = '$uid'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);


    $data = '🎙مشخصات کاربری:
    
    🛡 شناسه :   <code>'.$row['uid'].'</code> 
    🏖 نام کاربری :   @'.$row['username'].' 
    🧑‍💼 نام :   <code>'.$row['name'].'</code> 
    💰 قیمت : '.getPrice($uid).'
    📃 توضیحات : '.getPlan($uid).'
    ⁮⁮ ⁮⁮ ⁮⁮ ⁮⁮';
    return $data;
}


function msg($method,$parm){
    if(!$parm){
        $parm = array();
    }
    $parm["method"] = $method;
    $handle = curl_init(API_URL);
    curl_setopt($handle,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($handle,CURLOPT_CONNECTTIMEOUT,10);
    curl_setopt($handle,CURLOPT_TIMEOUT,60);
    curl_setopt($handle,CURLOPT_POSTFIELDS,json_encode($parm));
    curl_setopt($handle,CURLOPT_HTTPHEADER,array("Content-Type:application/json"));
    $result = curl_exec($handle);
    return $result;
}

function text($msg){
    $welcome = '⚡️به ربات AHAtech خوش اومدی⚡️

    🌐 سازگار با تمام اپراتورها
    📅 پشتیبانی تا آخرین روز اشتراک
    ✅ مناسب برای اندروید، آیفون، ویندوز و مک
    
    ♻️ /start';

    
    $buy = '🤔 توضیحات: 
    
    📶 | تعرفه عادی:
         ⚠️ فقط برای اپراتور های تلفن همراه
    
    💎 | تعرفه VIP:
         ⚡️ سرعت بالاتر
         ✅ برای اپراتور های تلفن همراه، مخابرات و وایفای
    
    تعرفه مدنظرت رو انتخاب کن 👇🏼';
    
    $configs = '📦 | کانفیگ های من';

    $apps = '📱 | اپلیکیشن های مورد نیاز :
    
    نرم افزار مناسب با سیستم عامل دستگاه خود را انتخاب کنید 👇🏻';

    $supp = '💁🏻‍♂️ پشتیبانی مورد نظر را جهت ارتباط انتخاب کنید 👇🏻';
    
    $about = '📅پشتیبانی تا آخرین روز اشتراک
    ✅ مناسب برای اندروید، آیفون، ویندوز و مک
    🌐 سازگار با تمام اپراتورها
    ';

    $volume = '🤔 توضیحات: 

    📗 | اشتراک نامحدود:
       📅     ✅ مدت زمان اشتراک 30 روز
       💾     ✅ بدون محدودیت حجم مصرفی
       👤     🚫 با محدودیت تعداد کاربر
    
    📕 | اشتراک حجمی:
       📅     ✅ مدت زمان اشتراک 30 روز
       💾     🚫 با محدودیت حجم مصرفی
       👤     ✅ بدون محدودیت تعداد کاربر
    
    اشتراک مورد نظرت رو انتخاب کن 👇🏼';

    $unlimit_plans = '📗 پلن های اشتراک نامحدود 

    یکی از پلن های زیر رو انتخاب کن 🤳🏼';

    $limit_plans = '📕 پلن های اشتراک حجمی 

    یکی از پلن های زیر رو انتخاب کن 🤳🏼';

    $card = CARD;
    $cardName = NAME;
    $pay = '♻️ یه تصویر از فیش واریزی که شامل (شماره پیگیری -  ساعت پرداخت - نام پرداخت کننده ) هست رو برام ارسال کن :

    💳 <code>' . $card . '</code> - ' . $cardName . '
    
    ✅ بعد از اینکه پرداختت تایید شد ( لینک سرور ) به صورت خودکار از طریق همین ربات برات ارسال میشه!';

    $received = '🛍 سفارشت با موفقیت ثبت شد.
    بعد از تایید برات ارسال میکنم ... 🥳';

    $wait = 'لطفا تا انجام سفارش قبلی صبر کنید 🙏🏼';

    $plans = ['normal_unlimit1u', 'normal_unlimit2u', 'normal_limit40g', 'normal_limit60g', 'normal_limit80g', 'normal_limit120g',
              'vip_unlimit1u', 'vip_unlimit2u', 'vip_limit40g', 'vip_limit60g', 'vip_limit80g', 'vip_limit120g'];
    
    // Admins messages
    $order = 'لیست کاربرانی که پرداخت کرده اند:';

    $confirm = 'لطفا UUID رو اینجا ارسال کن 👇🏻';

    $reject = '❌ پرداخت سفارشت انجام نشد 🥲

    برای پیگیری به پشتیبانی پیام بده 💬 ';

    $message = '📣 پیامی که میخوای برای همه کاربران ارسال کنی رو بنویس 👇🏻';

    $users = '👥 لیست کاربران ربات:';

    if( $msg == 'welcome' )             { return $welcome; }
    if( $msg == 'configs' )             { return $configs; }
    if( $msg == 'buy' )                 { return $buy; }
    if( $msg == 'apps' )                { return $apps; }
    if( $msg == 'supp' )                { return $supp; }
    if( $msg == 'normal' or 
        $msg == 'vip' )                 { return $volume; }
    if( $msg == 'normal_unlimit' or  
        $msg == 'vip_unlimit' )         { return $unlimit_plans; }
    if( $msg == 'normal_limit' or 
        $msg == 'vip_limit' )           { return $limit_plans; }
    if( in_array($msg, $plans))         { return proforma_invoice($msg);}
    if( $msg == 'pay' )                 { return $pay; }
    if( $msg == 'received' )            { return $received; }
    if( $msg == 'wait' )                { return $wait; }
    
    // Admins messages
    if( $msg == 'order' )               { return $order; }
    if( $msg == 'confirm' )             { return $confirm; }
    if( $msg == 'reject' )              { return $reject; }
    if( $msg == 'message' )             { return $message; }
    if( $msg == 'users' )               { return $users; }
}

function keyboard($keyboard){

    // Admins Buttons
    $order = orderList();
    $users = userList();
    $confirm = array(
        array(
            array('text' => '✅ | تایید پرداخت', 'callback_data' => 'accept'), 
            array('text' => '❌ | رد پرداخت', 'callback_data' => 'reject')
        ),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'order')
        )
    );
    if ( ADMIN == 'true' ) {
        $admin_btn1 = array(
            array('text' => '📣 | ارسال پیام', 'callback_data' => 'message'),
            array('text' => '📑 | سفارش ها', 'callback_data' => 'order')
        );
        $admin_btn2 = array(
            array('text' => '👥 | کاربران', 'callback_data' => 'users'),
        );
    }
    else
    {
        $admin_btn1 = array();
        $admin_btn2 = array();
    }
    
    $welcome = array(
        array(
            array('text' => '📦 | کانفیگ های من', 'callback_data' => 'configs'), 
            array('text' => '🛒 | خرید کانفیگ جدید', 'callback_data' => 'buy')
        ),
        array(
            array('text' => '📱 | نرم افزارها', 'callback_data' => 'apps'),
            array('text' => '💁🏻‍♂️ | پشتیبانی', 'callback_data' => 'supp')
        ),
        $admin_btn1,$admin_btn2
    );
    $buy = array(
        array(
            array('text' => '💎 | تعرفه VIP', 'callback_data' => 'vip'), 
            array('text' => '📶 | تعرفه عادی', 'callback_data' => 'normal')
        ),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'back')
        )
    );
    $apps = array(
        array(
            array('text' => '📶 | Hiddify Next (Android, Windows, MacOS, Linux)', 'url' => 'https://github.com/hiddify/hiddify-next/releases')
        ),
        array(
            array('text' => '🍏 | Fair VPN (iOS & MacOS)', 'url' => 'https://apps.apple.com/us/app/fair-vpn/id1533873488'),
            array('text' => '🍎 | Npv Tunnel (iOS)', 'url' => 'https://apps.apple.com/us/app/npv-tunnel/id1629465476'),
        ),
        array(
            array('text' => '☑️ | v2rayNG (Android)', 'url' => 'https://play.google.com/store/apps/details?id=com.v2ray.ang&hl=en&gl=US'),
            array('text' => '😻 | NekoBox (Android)', 'url' => 'https://play.google.com/store/apps/details?id=moe.nb4a&hl=en&gl=US')
        ),
        array(
            array('text' => '💻 | v2rayN (Windows)', 'url' => 'https://github.com/2dust/v2rayN/releases')
        ),
        array(
            array('text' => '😼 | NekoRay (Windows, MacOS, Linux)', 'url' => 'https://github.com/MatsuriDayo/nekoray/releases')
        ),
        array(
            array('text' => '✔️ | V2Box (iOS & MacOS)', 'url' => 'https://apps.apple.com/us/app/v2box-v2ray-client/id6446814690')
        ),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'back')
        )
    );
    $supp = array(
        array(
            array('text' => '🧑🏻‍💻 | پشتیبانی فنی', 'url' => 'https://t.me/AHAtech'),
            array('text' => '🛒 | پشتیبانی فروش', 'url' => 'https://t.me/AHAtechSell')
        ),
        array(
            array('text' => '📢 | کانال اخبار', 'url' => 'https://t.me/ahatech_gram')
        ),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'back')
        )
    );
    $normal = array(
        array(
            array('text' => '📕 | اشتراک حجمی', 'callback_data' => 'normal_limit'), 
            array('text' => '📗 | اشتراک نامحدود', 'callback_data' => 'normal_unlimit')
        ),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'buy')
        )
    );
    $vip = array(
        array(
            array('text' => '📕 | اشتراک حجمی', 'callback_data' => 'vip_limit'), 
            array('text' => '📗 | اشتراک نامحدود', 'callback_data' => 'vip_unlimit')
        ),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'buy')
        )
    );
    $normal_unlimit = array(
        array(
            array('text' => 'نامحدود تک کاربره - 160,000 تومان', 'callback_data' => 'normal_unlimit1u')
        ),
        array(
            array('text' => 'نامحدود دو کاربره - 260,000 تومان', 'callback_data' => 'normal_unlimit2u')
        ),

        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'normal')
        )
    );
    $normal_limit = array(
        array(
            array('text' => '40 گیگابایت چند کاربره - 50,000 تومان', 'callback_data' => 'normal_limit40g')
        ),
        array(
            array('text' => '60 گیگابایت چند کاربره - 70,000 تومان', 'callback_data' => 'normal_limit60g')
        ),
        array(
            array('text' => '80 گیگابایت چند کاربره - 90,000 تومان', 'callback_data' => 'normal_limit80g')
        ),
        array(
            array('text' => '120 گیگابایت چند کاربره - 130,000 تومان', 'callback_data' => 'normal_limit120g')
        ),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'normal')
        )
    );
    $vip_unlimit = array(
        array(
            array('text' => 'نامحدود تک کاربره - 200,000 تومان', 'callback_data' => 'vip_unlimit1u')
        ),
        array(
            array('text' => 'نامحدود دو کاربره - 300,000 تومان', 'callback_data' => 'vip_unlimit2u')
        ),

        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'vip')
        )
    );
    $vip_limit = array(
        array(
            array('text' => '40 گیگابایت چند کاربره - 90,000 تومان', 'callback_data' => 'vip_limit40g')
        ),
        array(
            array('text' => '60 گیگابایت چند کاربره - 110,000 تومان', 'callback_data' => 'vip_limit60g')
        ),
        array(
            array('text' => '80 گیگابایت چند کاربره - 130,000 تومان', 'callback_data' => 'vip_limit80g')
        ),
        array(
            array('text' => '120 گیگابایت چند کاربره - 170,000 تومان', 'callback_data' => 'vip_limit120g')
        ),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'vip')
        )
    );
    //Normal Plans
    $normal_unlimit1u = array( //Normal Unlimit 1 User
        array(
            array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'normal_unlimit')
        )
    );
    $normal_unlimit2u = array( //Normal Unlimit 2 User
        array(
            array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'normal_unlimit')
        )
    );
    $normal_limit40g = array( //Normal Limited 40GB
        array(
            array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'normal_limit')
        )
    );$normal_limit60g = array( //Normal Limited 60GB
        array(
            array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'normal_limit')
        )
    );
    $normal_limit80g = array( //Normal Limited 80GB
        array(
            array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'normal_limit')
        )
    );
    $normal_limit120g = array( //Normal Limited 120GB
        array(
            array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'normal_limit')
        )
    );
    //VIP Plans
    $vip_unlimit1u = array( //VIP Unlimit 1 User
        array(
            array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'vip_unlimit')
        )
    );
    $vip_unlimit2u = array( //VIP Unlimit 2 User
        array(
            array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'vip_unlimit')
        )
    );
    $vip_limit40g = array( //VIP Limited 40GB
        array(
            array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'vip_limit')
        )
    );$vip_limit60g = array( //VIP Limited 60GB
        array(
            array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'vip_limit')
        )
    );
    $vip_limit80g = array( //VIP Limited 80GB
        array(
            array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'vip_limit')
        )
    );
    $vip_limit120g = array( //VIP Limited 120GB
        array(
            array('text' => '💳 کارت به کارت' , 'callback_data' => 'pay')),
        array(
            array('text' => '⤵️ | بازگشت', 'callback_data' => 'vip_limit')
        )
    );
    $cansel = array(
        array('😪 منصرف شدم بیخیال')
    );
    $wait = array(
        array(
            array('text' => '🏠 | بازگشت به صفحه اصلی', 'callback_data' => 'back')
        ),
        array(
            array('text' => '💁🏻‍♂️ | ارتباط با پشتیبانی', 'callback_data' => 'supp')
        )
    );

    if( $keyboard == 'home' )            { $btns = $welcome; }
    if( $keyboard == 'buy' )             { $btns = $buy; }
    if( $keyboard == 'apps' )            { $btns = $apps; }
    if( $keyboard == 'supp' )            { $btns = $supp; }
    if( $keyboard == 'normal' )          { $btns = $normal; }
    if( $keyboard == 'vip' )             { $btns = $vip; }
    if( $keyboard == 'wait' )            { $btns = $wait; }

    if( $keyboard == 'normal_unlimit' )  { $btns = $normal_unlimit; }
    if( $keyboard == 'normal_limit' )    { $btns = $normal_limit; }
    if( $keyboard == 'vip_unlimit' )     { $btns = $vip_unlimit; }
    if( $keyboard == 'vip_limit' )       { $btns = $vip_limit; }

    if( $keyboard == 'normal_unlimit1u' ){ $btns = $normal_unlimit1u; }
    if( $keyboard == 'normal_unlimit2u' ){ $btns = $normal_unlimit2u; }
    if( $keyboard == 'normal_limit40g' ) { $btns = $normal_limit40g; }
    if( $keyboard == 'normal_limit60g' ) { $btns = $normal_limit60g; }
    if( $keyboard == 'normal_limit80g' ) { $btns = $normal_limit80g; }
    if( $keyboard == 'normal_limit120g' ){ $btns = $normal_limit120g; }

    if( $keyboard == 'vip_unlimit1u' )   { $btns = $vip_unlimit1u; }
    if( $keyboard == 'vip_unlimit2u' )   { $btns = $vip_unlimit2u; }
    if( $keyboard == 'vip_limit40g' )    { $btns = $vip_limit40g; }
    if( $keyboard == 'vip_limit60g' )    { $btns = $vip_limit60g; }
    if( $keyboard == 'vip_limit80g' )    { $btns = $vip_limit80g; }
    if( $keyboard == 'vip_limit120g' )   { $btns = $vip_limit120g; }

    // Admins Buttons
    if( $keyboard == 'order' )            { $btns = $order; }
    if( $keyboard == 'confirm' )          { $btns = $confirm; }
    if( $keyboard == 'users' )            { $btns = $users; }


    ////////////////////////

    if( $keyboard == 'cancel' )
    {
        $keyboard = array(
            'resize_keyboard' => true,
            'keyboard' => $cansel,
        );
    }
    elseif( $keyboard == 'remove' )
    {
        $keyboard = array(
            'remove_keyboard' => true
        );
    }
    elseif( $keyboard == 'configs' )
    {
        $keyboard = getConfigs(UID);
    }
    else
    {
        $keyboard = array(
            'resize_keyboard' => true,
            'inline_keyboard' => $btns
        );
    }
    
    return $keyboard;
}

function proforma_invoice($data){   
    //Normal Plans
        //Normal Unlimit Plans
    if( $data == 'normal_unlimit1u' )
    {
        $name = 'تعرفه یک ماهه نامحدود تک کاربره';
        $price = '160,000';
        $decription = 'تعرفه عادی یک ماهه، تک کاربره بدون محدودیت حجمی';
        $type = 'normal';
        $usage = '1000';
    }
    if( $data == 'normal_unlimit2u' )
    {
        $name = 'تعرفه یک ماهه نامحدود دو کاربره';
        $price = '260,000';
        $decription = 'تعرفه عادی یک ماهه، دو کاربره بدون محدودیت حجمی';
        $type = 'normal';
        $usage = '1000';
    }

        //Normal Limited Plans
    if( $data == 'normal_limit40g' )
    {
        $name = 'تعرفه یک ماهه حجمی 40 گیگابایت ';
        $price = '50,000';
        $decription = 'تعرفه عادی یک ماهه، 
        40 گیگابایت بدون محدودیت کاربر';
        $type = 'normal';
        $usage = '40';
    }
    if( $data == 'normal_limit60g' )
    {
        $name = 'تعرفه یک ماهه حجمی 60 گیگابایت ';
        $price = '70,000';
        $decription = 'تعرفه عادی یک ماهه، 
        60 گیگابایت بدون محدودیت کاربر';
        $type = 'normal';
        $usage = '60';
    }
    if( $data == 'normal_limit80g' )
    {
        $name = 'تعرفه یک ماهه حجمی 80 گیگابایت ';
        $price = '90,000';
        $decription = 'تعرفه عادی یک ماهه، 
        80 گیگابایت بدون محدودیت کاربر';
        $type = 'normal';
        $usage = '80';
    }
    if( $data == 'normal_limit120g' )
    {
        $name = 'تعرفه یک ماهه حجمی 120 گیگابایت ';
        $price = '130,000';
        $decription = 'تعرفه عادی یک ماهه، 
        120 گیگابایت بدون محدودیت کاربر';
        $type = 'normal';
        $usage = '120';
    }

    //VIP Plans
        //VIP Unlimit Plans
    if( $data == 'vip_unlimit1u' )
    {
        $name = 'تعرفه یک ماهه نامحدود تک کاربره';
        $price = '200,000';
        $decription = 'تعرفه VIP یک ماهه، تک کاربره بدون محدودیت حجمی';
        $type = 'vip';
        $usage = '1000';
    }
    if( $data == 'vip_unlimit2u' )
    {
        $name = 'تعرفه یک ماهه نامحدود دو کاربره';
        $price = '300,000';
        $decription = 'تعرفه VIP یک ماهه، دو کاربره بدون محدودیت حجمی';
        $type = 'vip';
        $usage = '1000';
    }

        //VIP Limited Plans
    if( $data == 'vip_limit40g' )
    {
        $name = 'تعرفه یک ماهه حجمی 40 گیگابایت ';
        $price = '90,000';
        $decription = 'تعرفه VIP یک ماهه، 
        40 گیگابایت بدون محدودیت کاربر';
        $type = 'vip';
        $usage = '40';
    }
    if( $data == 'vip_limit60g' )
    {
        $name = 'تعرفه یک ماهه حجمی 60 گیگابایت ';
        $price = '110,000';
        $decription = 'تعرفه VIP یک ماهه، 
        60 گیگابایت بدون محدودیت کاربر';
        $type = 'vip';
        $usage = '60';
    }
    if( $data == 'vip_limit80g' )
    {
        $name = 'تعرفه یک ماهه حجمی 80 گیگابایت ';
        $price = '130,000';
        $decription = 'تعرفه VIP یک ماهه، 
        80 گیگابایت بدون محدودیت کاربر';
        $type = 'vip';
        $usage = '80';
    }
    if( $data == 'vip_limit120g' )
    {
        $name = 'تعرفه یک ماهه حجمی 120 گیگابایت ';
        $price = '170,000';
        $decription = 'تعرفه VIP یک ماهه، 
        120 گیگابایت بدون محدودیت کاربر';
        $type = 'vip';
        $usage = '120';
    }


    include 'serv_conf.php';
    $uid = UID;
    $sql = "UPDATE temp SET plan = '$decription', price = '$price', type = '$type', pu = '$usage', cmd = '$data' WHERE uid = $uid";
    mysqli_query($conn, $sql);
    return proforma_invoice_maker($name, $price, $decription);
}

function proforma_invoice_maker($name, $price, $decription)
{
    $invoice = '📄 پیش فاکتور

    〽️ نام پلن:  '.$name.'
                ➖➖➖➖➖➖➖➖➖➖
    💎 قیمت پنل : '.$price.' تومان 
                ➖➖➖➖➖➖➖➖➖➖
    📃 توضیحات :
    '.$decription.'
                ➖➖➖➖➖➖➖➖➖➖';

    return $invoice;
}
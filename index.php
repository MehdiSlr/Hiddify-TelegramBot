<?php

$content = file_get_contents('php://input');
$update = json_decode($content, true);

$chat_id = $update['message']['chat']['id'];
$text = $update['message']['text'];
$user_id = $update['message']['chat']['username'];
$user_name = $update['message']['chat']['first_name'];

$callback_chat_id = $update['callback_query']['message']['chat']['id'];
$callback_data = $update['callback_query']['data'];
$callback_message_id = $update['callback_query']['message']['message_id'];
$callback_message = $update['callback_query']['message']['text'];
$callback_user_id = $update['callback_query']['from']['username'];
$callback_user_name = $update['callback_query']['from']['first_name'];
$bot_id = $update['callback_query']['from']['id'];

require 'bot.php';
require 'serv_conf.php';


if($chat_id == '1278109787' || $chat_id == '85023428') {
    define('ADMIN', 'true');
}elseif($callback_chat_id == '1278109787' || $callback_chat_id == '85023428') {
    define('ADMIN', 'true');
}

// Query to retrieve the user's status
$sql = "SELECT status FROM users WHERE uid = '$callback_chat_id'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $status = $row['status'];
}
else {
    $sql = "SELECT status FROM users WHERE uid = '$chat_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $status = $row['status'];
}
define('STATUS', $status);
// SQL query to retrieve user information
if ($callback_data != null) {
    define ('UID', $callback_chat_id);
    define ('CDATA', $callback_data);
    define ('CMSGID', $callback_message_id);
    $callback_data = cCheck($callback_data);
    $sql = "SELECT * FROM users WHERE uid = '$callback_data'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (ADMIN == 'true') {
            $sql = "SELECT status FROM users WHERE uid = '$callback_chat_id'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $status = $row['status'];
            if($status == 'order'){
                $keyboard = keyboard('confirm');
            }
            elseif($status == 'users'){
                $keyboard = getConfigs($callback_data);
            }
            msg(editMessageText ,array('text'=>userdata($callback_data), 'chat_id'=>$callback_chat_id, 'message_id'=>$callback_message_id, 'reply_markup' => $keyboard, 'parse_mode' => 'HTML'));
        }
    }
}

// Start command
if ($text == '/start') {
    msg(sendMessage ,array('chat_id'=>$chat_id, 'text'=>'⏳ در حال بررسی ...', 'reply_markup' => keyboard('remove')));
    $sql = "SELECT * FROM users WHERE uid = '$chat_id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $sql = "UPDATE users SET uid = '$chat_id', username = '$user_id', name = '$user_name' WHERE uid = '$chat_id'";
        mysqli_query($conn, $sql);
        $sql = "UPDATE temp SET uid = '$chat_id' WHERE uid = '$chat_id'";
    }
    else {
        $sql = "INSERT INTO users (uid, name, username, status) VALUES ('$chat_id', '$user_name', '$user_id', 'active')";
        mysqli_query($conn, $sql);
        $sql = "INSERT INTO temp (uid) VALUES ('$chat_id')";
    }
    mysqli_query($conn, $sql);
    if(mysqli_error($conn)){
        msg(sendMessage ,array('chat_id'=>$chat_id, 'text'=>"خطا در اتصال به پایگاه داده: " . mysqli_error($conn)));
    }
    $text = text('welcome');
    $keyboard = keyboard('home');
    msg(sendMessage, ['chat_id' => $chat_id, 'text' => $text, 'reply_markup' => $keyboard]);
    mysqli_close($conn);
}

// Cancel operation
elseif ($text == '😪 منصرف شدم بیخیال') {
    msg(sendMessage ,array('chat_id'=>$chat_id, 'text'=>'⏳ در حال بررسی ...', 'reply_markup' => keyboard('remove')));
    
    $sql = "UPDATE users SET uid = '$chat_id', username = '$user_id', name = '$user_name', pay = 'cancelled', status = 'active' WHERE uid = '$chat_id'";
    mysqli_query($conn, $sql);
    $sql = "UPDATE temp SET plan = null, price = null WHERE uid = '$chat_id'";
    mysqli_query($conn, $sql);
    mysqli_close($conn);
    
    $text = text('welcome');
    $keyboard = keyboard('home');
    msg(sendMessage ,array('chat_id'=>$chat_id, 'text'=>$text, 'reply_markup' => $keyboard));
}

// Pay operation
elseif ($callback_data == 'pay') {
    msg(deleteMessage ,array('chat_id'=>$callback_chat_id, 'message_id'=>$callback_message_id));
    $sql = "SELECT pay FROM users WHERE uid = '$callback_chat_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $pay = $row['pay'];
    if($pay == 'paid'){
        msg(sendMessage ,array('chat_id'=>$callback_chat_id, 'text'=>text('wait'), 'reply_markup' => keyboard('wait')));
    }
    else{
        $sql = "UPDATE users SET pay = 'clicked' WHERE uid = '$callback_chat_id'";
        mysqli_query($conn, $sql);
        mysqli_close($conn);
        msg(sendMessage ,array('chat_id'=>$callback_chat_id, 'text'=>text($callback_data), 'reply_markup' => keyboard('cancel'), 'parse_mode' => 'HTML'));
    }
}

// Processing the sent photo
elseif (isset($update['message'])) {
    if(isset($text)){
        if(check($text) != $text){
            $uid = check($text);
            $sql = "SELECT pay FROM users WHERE uid = '$uid'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if ($row['pay'] == 'paid') {
                    msg(sendMessage ,array('chat_id'=>$chat_id, 'text'=>userdata($uid), 'reply_markup' => keyboard('confirm'), 'parse_mode' => 'HTML'));
                }
                else{
                    $msg = userdata($uid) . '
<strong>سفارش مورد بررسی ای برای این کاربر وجود ندارد 😕</strong>';
                    msg(sendMessage ,array('chat_id'=>$chat_id, 'text'=>$msg, 'reply_markup' => keyboard('order'), 'parse_mode' => 'HTML'));
                }
            }
        }
        elseif(check($text) == $text){
            $sql = "SELECT status FROM users WHERE uid = '$chat_id'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $status = $row['status'];
            if($status == 'message'){
                $sql = "SELECT uid FROM users";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                // $uid = $row['uid'];
                while($row = mysqli_fetch_assoc($result)){
                    // if($uid = '5331641129'){
                        msg(sendMessage, ['chat_id' => $row['uid'], 'text' => $text]);
                    // }
                }
                msg(sendMessage ,array('chat_id'=>$chat_id, 'text'=>'پیام ارسال شد ✅', 'reply_markup' => keyboard('remove')));
                $sql = "UPDATE users SET status = 'active' WHERE uid = '$chat_id'";
                mysqli_query($conn, $sql);
                mysqli_close($conn);
                msg(sendMessage ,array('chat_id'=>$chat_id, 'text'=>text('welcome'), 'reply_markup' => keyboard('home')));
            }
            elseif($status == 'confirm'){
                $sql = "SELECT confirm FROM users WHERE uid = '$chat_id'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                $uid = $row['confirm'];
                $uuid = $text;

                // Save config to data base
                $sql = "INSERT INTO configs (uid, uuid) VALUES ('$uid', '$uuid')"; 
                mysqli_query($conn, $sql);

                //admin 
                msg(sendMessage ,array('chat_id'=>$chat_id, 'text'=>'سفارش با موفقیت انجام شد ✅', 'reply_markup' => keyboard('remove')));
                $sql = "UPDATE users SET status = 'active', confirm = null WHERE uid = '$chat_id'";
                mysqli_query($conn, $sql);
                msg(sendMessage ,array('chat_id'=>$chat_id, 'text'=>text('welcome'), 'reply_markup' => keyboard('home')));

                //user
                $sql = "UPDATE users SET pay = 'confirmed', plan = null, price = null WHERE uid = '$uid'";
                mysqli_query($conn, $sql);
                $config = getConfig($uid, $uuid);
                $msg = 
                '😍 کانفیگ شما
📡 پروتکل: vless
💝 config : 
<code>' . $config . '</code>';
                $keyboard = array(
                    'resize_keyboard' => true,
                    'inline_keyboard' => array(
                        array(
                            array('text' => '🗒️ | مشخصات کانفیگ', 'url' => 'https://admin-4.innozoneshop.com/xauAJSHwlndetDCtGvKx08Zu/'.$uuid)
                        ),
                        array(
                            array('text' => '🏠 | بازگشت به صفحه اصلی', 'callback_data' => 'start')
                        )
                    )
                );
                $config_qr = urlencode($config);
                $image = 'https://quickchart.io/qr?text='.$config_qr.'&light=d6ffe8&dark=122544&size=300&centerImageUrl=https%3A%2F%2Fi.postimg.cc%2F8zjRH5n2%2Fahatech-rbg.png';
                msg(sendPhoto ,array('chat_id'=>$uid, 'photo'=>$image, 'caption'=>$msg, 'reply_markup' => $keyboard , 'parse_mode' => 'HTML'));
            }
        }
    }

    $sql = "SELECT pay FROM users WHERE uid = '$chat_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $pay = $row['pay'];
    if($pay == 'clicked'){
        if(isset($update['message']['photo'])) {
            $photoId = $update['message']['photo'][0]['file_id'];
            $channel_id = '-1002101418751';
            $caption = '🎙مشخصات پرداخت:
    
            🛡 شناسه :   <code>'.$chat_id.'</code> 
            💰 قیمت :  '.getPrice($chat_id).'
            📃 توضیحات : '.getPlan($chat_id).'
            🔍 بررسی : https://t.me/ahatechvpnbot?start=check_'.$chat_id.'
            ⁮⁮ ⁮⁮ ⁮⁮ ⁮';
            // $keyboard = keyboard('check');
            msg(sendPhoto ,array('chat_id'=>$channel_id, 'photo'=>$photoId, 'caption'=>$caption, 'parse_mode' => 'HTML'));
            $sql = "SELECT * FROM temp WHERE uid = '$chat_id'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $plan = $row['plan'];
            $price = $row['price'];
            $pu = $row['pu'];
            $type = $row['type'];
            $cmd = $row['cmd'];

            $sql = "UPDATE users SET pay = 'paid', plan = '$plan', price = '$price',pu = '$pu', type = '$type', cmd = '$cmd' WHERE uid = '$chat_id'";
            mysqli_query($conn, $sql);
            mysqli_close($conn);
            msg(sendMessage ,array('chat_id'=>$chat_id, 'text'=>text('received'), 'reply_markup' => keyboard('remove')));
            msg(sendMessage ,array('chat_id'=>$chat_id, 'text'=>text('welcome'), 'reply_markup' => keyboard('home')));
        }
        else {
            $keyboard = keyboard('cancel');
            msg(sendMessage ,array('chat_id'=>$chat_id, 'text'=>'لطفا یک تصویر ارسال کنید', 'reply_markup' => $keyboard));
        }
    }
}

// admin commands
elseif($callback_data == 'accept'){
    // msg(deleteMessage ,array('chat_id'=>$callback_chat_id, 'message_id'=>$callback_message_id));
    $uid = getUID($callback_message);
    $sql = "SELECT * FROM users WHERE uid = '$uid'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $name = $row['name'];
    $comment = $row['type'];
    $package_size = intval($row['pu']);
    $telegram_id = $uid;
    $cmd = $row['cmd'];
    $sql = "SELECT * FROM configs WHERE cmd = '$cmd' AND uid = '$uid'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        $uuid = $row['uuid'];
        $config = getConfig($uid, $uuid);
        $msg = 'اشتراک کانفیگ شما با موفقیت تمدید شد 🎉
📡 پروتکل: vless
💝 config : 
<code>' . $config . '</code>';
    }
    else{
        $uuid =  generateRandomUUID();
        
        // Save config to data base
        $sql = "INSERT INTO configs (uid, uuid, cmd) VALUES ('$uid', '$uuid', '$cmd')";
        mysqli_query($conn, $sql);
        $config = getConfig($uid, $uuid);
        $msg = 
    '😍 کانفیگ شما
📡 پروتکل: vless
💝 config : 
<code>' . $config . '</code>';
    }
    $uuid =  HiddifyCreate($name, $comment, $package_size, $telegram_id, $uuid); // update or create user
    // admin
    msg(editMessageText ,array('text'=>'سفارش با موفقیت انجام شد ✅', 'chat_id'=>$callback_chat_id, 'message_id'=>$callback_message_id));
    msg(sendMessage ,array('chat_id'=>$callback_chat_id, 'text'=>text('welcome'), 'reply_markup' => keyboard('home')));

    // user
    $sql = "UPDATE users SET pay = 'confirmed', plan = null, pu = null, price = null WHERE uid = '$uid'";
    mysqli_query($conn, $sql);
    $config = getConfig($uid, $uuid);
    
    $keyboard = array(
        'resize_keyboard' => true,
        'inline_keyboard' => array(
            array(
                array('text' => '🗒️ | مشخصات کانفیگ', 'url' => 'https://admin-4.innozoneshop.com/xauAJSHwlndetDCtGvKx08Zu/'.$uuid)
            ),
            array(
                array('text' => '🏠 | بازگشت به صفحه اصلی', 'callback_data' => 'start')
            )
        )
    );
    $config_qr = urlencode($config);
    $image = 'https://quickchart.io/qr?text='.$config_qr.'&light=d6ffe8&dark=122544&size=300&centerImageUrl=https%3A%2F%2Fi.postimg.cc%2F8zjRH5n2%2Fahatech-rbg.png';
    msg(sendPhoto ,array('chat_id'=>$uid, 'photo'=>$image, 'caption'=>$msg, 'reply_markup' => $keyboard , 'parse_mode' => 'HTML'));
}
elseif ($callback_data == 'reject') { //if admin reject a payment
    $uid = getUID($callback_message);
    $sql = "UPDATE users SET pay = 'rejected', plan = null, pu = null, price = null WHERE uid = '$uid'";
    mysqli_query($conn, $sql);
    msg(sendMessage ,array('chat_id'=>$uid, 'text'=>text('reject'), 'reply_markup' => keyboard('wait')));
    msg(editMessageText ,array('text'=>text('order'), 'chat_id'=>$callback_chat_id, 'message_id'=>$callback_message_id, 'reply_markup' => keyboard('order')));
    mysqli_close($conn);
}
elseif ($callback_data == 'message') {
    msg(deleteMessage ,array('chat_id'=>$callback_chat_id, 'message_id'=>$callback_message_id));
    $sql = "UPDATE users SET status = 'message' WHERE uid = '$callback_chat_id'";
    mysqli_query($conn, $sql);
    msg(sendMessage ,array('chat_id'=>$callback_chat_id, 'text'=>text('message'), 'reply_markup' => keyboard('cancel')));
}
elseif ($callback_data == 'start') {
    msg(sendMessage ,array('chat_id'=>$callback_chat_id, 'text'=>'⏳ در حال بررسی ...', 'reply_markup' => keyboard('remove')));
    $sql = "SELECT * FROM users WHERE uid = '$callback_chat_id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $sql = "UPDATE users SET uid = '$callback_chat_id', username = '$callback_user_id', name = '$callback_user_name' WHERE uid = '$callback_chat_id'";
        mysqli_query($conn, $sql);
        $sql = "UPDATE temp SET uid = '$callback_chat_id' WHERE uid = '$callback_chat_id'";
    }
    else {
        $sql = "INSERT INTO users (uid, name, username, status) VALUES ('$callback_chat_id', '$callback_user_name', '$callback_user_id', 'active')";
        mysqli_query($conn, $sql);
        $sql = "INSERT INTO temp (uid) VALUES ('$callback_chat_id')";
    }
    mysqli_query($conn, $sql);
    if(mysqli_error($conn)){
        msg(sendMessage ,array('chat_id'=>$callback_chat_id, 'text'=>"خطا در اتصال به پایگاه داده: " . mysqli_error($conn)));
    }
    $text = text('welcome');
    $keyboard = keyboard('home');
    msg(sendMessage, ['chat_id' => $callback_chat_id, 'text' => $text, 'reply_markup' => $keyboard]);
    mysqli_close($conn);
}
else {
    if ($callback_data == 'back') { // if user clicked on back button
        $sql = "UPDATE users SET status = 'active' WHERE uid = '$callback_chat_id'";
        mysqli_query($conn, $sql);
        $text = text('welcome');
        $keyboard = keyboard('home');
    }
    elseif ($callback_data == 'order') { // if admin clicked on order button
        $sql = "UPDATE users SET status = 'order' WHERE uid = '$callback_chat_id'";
        mysqli_query($conn, $sql);
        $text = text('order');
        $keyboard = keyboard('order');
    }
    elseif ($callback_data == 'users') { // if admin clicked on users button
        $sql = "UPDATE users SET status = 'users' WHERE uid = '$callback_chat_id'";
        mysqli_query($conn, $sql);
        $text = text('users');
        $keyboard = keyboard('users');
    }
    else {
        $text = text($callback_data);
        $keyboard = keyboard($callback_data);
    }
    msg(editMessageText ,array('text'=>$text, 'chat_id'=>$callback_chat_id, 'message_id'=>$callback_message_id, 'reply_markup' => $keyboard));
}
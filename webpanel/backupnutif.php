<?php
include 'includ/db.php';
include 'includ/jdf.php';
include '../wizwizxui-timebot-main/baseInfo.php';

$sql_servers = "SELECT * FROM servers where status=1";
$result_servers = $conn->query($sql_servers);

while ($row_servers = $result_servers->fetch_assoc()) {
    $name_db = $row_servers["name"];
    $port_db = $row_servers["port"];
    $ip_db = $row_servers["ip"];
    $username_db = $row_servers["username"];
    $password_db = $row_servers["password"];
    $remote_file = $row_servers["panel"];

    $connection = ssh2_connect($ip_db, $port_db);
    ssh2_auth_password($connection, $username_db, $password_db);

    if($connection){
// $remote_file = '/etc/x-ui-english/x-ui-english.db';
        $sftp = ssh2_sftp($connection);
        $stream = fopen("ssh2.sftp://{$sftp}{$remote_file}", 'r');
        $file_contents = '';
        while (!feof($stream)) {
            $file_contents .= fread($stream, 8192); // read in chunks of 8KB
        }
        fclose($stream);

        $current_time = jdate('Y-m-d H:i:s');
        $message = "{$name_db} -  {$current_time}";
        // $chat_id = '-1001770683676';
        $sql_servers1 = "SELECT * FROM admins";
        $result_servers1 = $conn->query($sql_servers1);
        $row_servers1 = $result_servers1->fetch_assoc();
        $chat_id = $row_servers1["backupchannel"];
        $url = "https://api.telegram.org/bot{$botToken}/sendDocument";
        $post_fields = array(
            'chat_id' => $chat_id,
            'document' => new CURLFile("ssh2.sftp://{$sftp}{$remote_file}", 'text/plain', basename($remote_file)),
            'caption' => $message
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);


        // Check if the file was sent successfully
        if (json_decode($result)->ok) {
            echo "File sent successfully to the Telegram bot.";
        } else {
            echo "Error sending the file to the Telegram bot.";
        }
    }

}
?>



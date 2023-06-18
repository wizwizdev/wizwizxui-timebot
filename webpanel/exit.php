<?php
include './login.php';
session_start();
session_destroy();
//setcookie('cookie_username',$encryptedValue ,time() -  (604800));
header("location: login.php");

?>
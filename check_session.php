<?php

function checkSession() {
    // LOGINチェック → funcs.phpへ関数化しましょう！
    if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"]!=session_id()){
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Storybook Memory Maker</title>
</head>
<!-- Server的Write.php → Client的Write.html → Server的view.php -->
<body>
    <h3>Login Error. The page will be redirect to login page shortly. </h3> 
    <h3>If the page doesn't redirect automatically, please click <a href="javascript: gotoLoginPage();">here</a>.</h3>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="js/pc_or_mobile.js"></script>
<script>

    $(document).ready(function() {
        setTimeout(() => {
            gotoLoginPage();
        }, 4500);
    });

    function gotoLoginPage() {
        document.location.href = getDevicePage("index.php");
    }

</script>
</html>
<?php
        exit();
    }else{
        session_regenerate_id(true);
        $_SESSION["chk_ssid"] = session_id();
    }
}

?>
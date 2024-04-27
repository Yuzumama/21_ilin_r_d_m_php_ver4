<?php

session_start();
include 'check_session.php';
checkSession();

// 把post收到的書籍id跟名稱存到session裡去
$_SESSION["book_id"] = $_POST["book_id"];
$_SESSION["book_name"] = $_POST["book_name"];

// 把現在看到的頁數重設回 0
$_SESSION["current_view_page"] = 0;

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Storybook Memory Maker</title>
</head>
<body>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="js/pc_or_mobile.js"></script>
<script>

    $(document).ready(function() {
        document.location.href = getDevicePage("view.php");
//        $("#back_to_view_form").submit();
    });

</script>
</html>
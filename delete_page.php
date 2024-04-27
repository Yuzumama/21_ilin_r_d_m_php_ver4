<?php
session_start();
include 'check_session.php';
checkSession();

include 'connect_to_db.php';

// Connect to db
$pdo = connect_to_db();
if($pdo == null) {
    exit();
}

$page_id = $_POST["page_id"];

// 檢查該頁面是否存在
$sql = "SELECT * FROM `book_page_table` WHERE id=:page_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':page_id', $page_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();

if ($status === false) {
    //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit("SQL_ERROR: " . $error[2]);
}

$values = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 若該頁面存在則要把該頁面從DB刪除, 並且把該頁面上的影像檔案刪除
if (count($values) > 0) {

    $image_filename = $values[0]["image_filename"];

//    echo $image_filename;

    // 先確認該影像檔案還在, 若該檔案還在就刪除
    if (file_exists($image_filename)) {
        unlink($image_filename);
    }

    // 從DB裡把該頁面刪除
    $sql = "DELETE FROM `book_page_table` WHERE id=:page_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':page_id', $page_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
    $status = $stmt->execute();

    if ($status === false) {
        //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
        $error = $stmt->errorInfo();
        exit("SQL_ERROR: " . $error[2]);
    }
}

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

    $(document).ready(function () {
        document.location.href = getDevicePage("view.php");
//        $("#back_to_view_form").submit();
    });

</script>
</html>
<?php
session_start();
include 'check_session.php';
checkSession();

// Put Client's content from write.html to db

include 'connect_to_db.php';

// Connect to db
$pdo = connect_to_db();
if($pdo == null) {
    exit();
}

$page_id = $_POST["page_id"];
$image_file_updated = $_POST["image_file_updated"];

// 如果有上傳新的照片那就要把舊的照片從server上刪掉, 換成新上傳的照片
if($image_file_updated == "1") {
    
    // 先去db裡面找出舊有的照片名稱
    $sql = "SELECT * FROM `book_page_table` WHERE id=:page_id;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":page_id", $page_id, PDO::PARAM_INT);
    $status = $stmt->execute();

    if ($status === false) {
        //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
        $error = $stmt->errorInfo();
        exit("SQL_ERROR: " . $error[2]);
    }

    $values = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 從db搜尋出來的那一筆資料裡取出影片檔案名稱
    $image_filename = $values[0]["image_filename"];

    // 檢查該檔案是否仍存在, 如果仍存在就刪掉
    if(file_exists($image_filename)) {
        unlink($image_filename);
    }

    // 取得上傳檔案的暫存檔檔名
    $tempname = $_FILES["image_file_chooser"]["tmp_name"];

    // 把新上傳的影像檔案重新命名為同一個檔案
    if(!move_uploaded_file($tempname, $image_filename)) { 

        // 如果重新命名失敗就顯示錯誤訊息
        exit("Failed to upload image file");
    }
}

// 接著要來更新上傳的文字

// 取得form所上傳的內容
$book_id = $_SESSION["book_id"];
$book_name = $_SESSION["book_name"];

$author = $_POST["author"];
$storybook = $_POST["storybook"];
$child_name = $_POST["child_name"];
$progress = $_POST["progress"];
$child_feedback = $_POST["child_feedback"];
$comments = $_POST["comments"];
$book_score = $_POST["book_score"];
$book_tag_1 = $_POST["book_tag_1"];
$book_tag_2 = $_POST["book_tag_2"];
$book_tag_3 = $_POST["book_tag_3"];

// 把Text和image的資料存到SQL/DB
//    $sql = "INSERT INTO `book_page_table`(`book_group`, `image_filename`, `storyteller`, `input_date`, `input_comment`, 'storybook_name', 'child_name', 'progress', 'child_feedback') VALUES (:book_group,:image_filename,:storyteller,sysdate(),:input_comment, :storybook_name, :child_name, :progress, :child_feedback);";
$sql = "UPDATE `book_page_table` SET book_id=:book_id," . 
                                    "storyteller=:storyteller," . 
                                    "input_comment=:input_comment," .
                                    "storybook_name=:storybook_name,".
                                    "child_name=:child_name," .
                                    "progress=:progress,".
                                    "child_feedback=:child_feedback," .
                                    "book_score=:book_score," .
                                    "`book_tag_1`=:book_tag_1," .
                                    "`book_tag_2`=:book_tag_2," . 
                                    "`book_tag_3`=:book_tag_3 " .
                                    "WHERE id=:page_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':book_id', $book_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':storyteller', $author, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':input_comment', $comments, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':storybook_name', $storybook, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':child_name', $child_name, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':progress', $progress, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':child_feedback', $child_feedback, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':book_score', $book_score, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':book_tag_1', $book_tag_1, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':book_tag_2', $book_tag_2, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':book_tag_3', $book_tag_3, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':page_id', $page_id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status === false) {
    //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit("SQL_ERROR: " . $error[2]);
}

include 'check_book_tag.php';

// 檢查上傳的三個 book tag 是否存在, 如果不存在就追加到 db 裡去
checkBookTagAndAddToDbIfNotExist($book_tag_1, $pdo);
checkBookTagAndAddToDbIfNotExist($book_tag_2, $pdo);
checkBookTagAndAddToDbIfNotExist($book_tag_3, $pdo);

// 設定現在看到第幾頁了
if (isset($_POST["current_view_page"])) {
    $_SESSION["current_view_page"] = $_POST["current_view_page"];
}

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
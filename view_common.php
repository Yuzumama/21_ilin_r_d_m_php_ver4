<!-- Database Imformation Input-->
<?php
session_start();
include 'check_session.php';
checkSession();

include 'connect_to_db.php';
include 'common_helpers.php';

// Connect to db
$pdo = connect_to_db();
if ($pdo == null) {
    exit();
}


// Set the name of book, edit the book, and store the content to write.php
// user_info.php 會查詢好該使用者所創作的書籍id及書籍名稱
if (isset($_SESSION["book_id"])) {

    $book_id = $_SESSION["book_id"];
    $book_name = $_SESSION["book_name"];
} else {
    $book_id = "";
    $book_name = "";
}

$user_id = $_SESSION["user_id"];
$author = $_SESSION["nickname"];

// 只要使用者之前有登錄過書名的話，語法如下：
// book_group =: xxxx (自行喜好填寫)
if ($book_id != "") {
    $values = getAllPagesOfBookFromDb($book_id, $pdo);

    // 取得這本書的封面資料
    $sql = "SELECT * FROM `book_table` WHERE id=:book_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":book_id", $book_id, PDO::PARAM_STR);
    $status = $stmt->execute();

    if ($status === false) {
        //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
        $error = $stmt->errorInfo();
        exit("SQL_ERROR: " . $error[2]);
    }
    // 封面資料取得成功的話，從資料庫裡面讀到$cover_values的變數裡
    $cover_values = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 取得封面的版面設計json
    $cover_layout = json_decode($cover_values[0]["cover_layout"], true);
} else {
    // 書籍id是空白的話，Values=Page的資料會顯示空白
    $values = [];
    $cover_values = null;
    $cover_layout = null;
}

include "svg_icons.php";

$icons_in_db = getSvgIcons($pdo);

//$is_editting = isset($_POST["is_writing_icon"]);

$is_editting = false;
if(isset($_SESSION["is_writing_icon"])) {
    if($_SESSION["is_writing_icon"] == 1) {
        $is_editting = true;
        $_SESSION["is_writing_icon"] = 0;
    }
}

// 取得資料庫裡所有出現過的tag
$sql = "SELECT * FROM book_tag_table";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

if ($status === false) {
    //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit("SQL_ERROR: " . $error[2]);
}

// tag資料取得成功的話，從資料庫裡面讀到$tag_values的變數裡
$tag_values = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
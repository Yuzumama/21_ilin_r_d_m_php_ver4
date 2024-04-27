<?php
// 檢查上傳的 book tag 有沒有存在資料庫裏面, 如果沒有就要新增進去
function checkBookTagAndAddToDbIfNotExist($book_tag, $pdo) {

    // 如果 $book_tag 不存在或者為空值就不必檢查
    if(!isset($book_tag)) {
        return;
    }
    if($book_tag == null) {
        return;
    }

    // 從 db 裡面取出該 book_tag
    $sql = "SELECT * FROM book_tag_table WHERE tag_name=:book_tag";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":book_tag", $book_tag, PDO::PARAM_STR);
    $status = $stmt->execute();

    if ($status === false) {
        //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
        $error = $stmt->errorInfo();
        exit("SQL_ERROR: " . $error[2]);
    }

    // 如果查詢結果的 row 為 0 就代表沒有查到這個 tag
    // 就必須新增到 db 裡去
    if($stmt->rowCount() == 0) {
        $sql = "INSERT INTO book_tag_table (tag_name) VALUES (:book_tag)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":book_tag", $book_tag, PDO::PARAM_STR);
        $status = $stmt->execute();

        if ($status === false) {
            //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
            $error = $stmt->errorInfo();
            exit("SQL_ERROR: " . $error[2]);
        }
    }
}
?>
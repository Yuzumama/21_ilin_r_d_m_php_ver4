<?php

function getAllBooksOfUserFromDb($user_id, $pdo)
{
    $sql = "SELECT * FROM book_table where author_id=:user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $status = $stmt->execute();

    if ($status === false) {
        //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
        $error = $stmt->errorInfo();
        exit("SQL_ERROR when checking id and pw: " . $error[2]);
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllPagesOfBookFromDb($book_id, $pdo)
{
    $sql = "SELECT * FROM `book_page_table` WHERE book_id=:target_book_id;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':target_book_id', $book_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
    $status = $stmt->execute();

    if ($status === false) {
        //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
        $error = $stmt->errorInfo();
        exit("SQL_ERROR: " . $error[2]);
    }

    //之前登錄過的資料(表單的)都會從資料庫裡面讀到$Values的變數裡面 
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserId($target_user_id, $pdo)
{

    // Get id of test user for comparison
    $sql = "SELECT * FROM user_table WHERE user_id=:user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":user_id", $target_user_id, PDO::PARAM_STR);
    $status = $stmt->execute();

    if ($status === false) {
        //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
        $error = $stmt->errorInfo();
        exit("SQL_ERROR: " . $error[2]);
    }

    // 成功從DB取得資料的話，從資料庫裡面讀到$all_tags的變數裡
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["id"];
}

?>
<?php

// 開始利用session, 把登入資料放在server端
session_start();

// 檢查 session 裡面是否已經有登入的資訊了
// if (isset($_SESSION["user_id"]) {}

include 'connect_to_db.php';
include 'common_helpers.php';

// Connect to db
$pdo = connect_to_db();
if($pdo == null) {
    exit();
}

$user_id = $_POST["author"];
$user_pw = $_POST["password"];

// 不可以把密碼直接就照樣存到資料庫裏面去, 必須要用hash暗號化
$user_pw = password_hash($user_pw, PASSWORD_DEFAULT);

$user_email = $_POST["email"];
$is_new_user = $_POST["is_new_user"];

//echo $user_id;
//echo $user_pw;
//echo $user_email;
//echo $is_new_user;

// Create new user
if($is_new_user == "1"){
    // 檢查該id是否已經存在, 如果已經存在就回傳錯誤到index.php
    $sql = "SELECT * FROM `user_table` WHERE user_id=:user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
    $status = $stmt->execute();

    if ($status === false) {
        //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
        $error = $stmt->errorInfo();
        exit("SQL_ERROR when checking new user id: " . $error[2]);
    }

    $values = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If the user id is used, then send an error message to index.php
    if(count($values)>0){
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
    <form id="back_to_index_form" method="post" action="index.php">
        <input type="text" name="user_id" value="<?= $user_id ?>" hidden />
        <input type="text" name="user_email" value="<?=$user_email?>" hidden />
        <input type="text" name="error_message" value="<?= $user_id ?> is already used" hidden />
        <input type="text" name="is_new_user" value="<?=$is_new_user?>" hidden />
    </form>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="js/pc_or_mobile.js"></script>
<script>

    $(document).ready(function() {
        $("#back_to_index_form").attr("action", getDevicePage("index.php"));
        $("#back_to_index_form").submit();
    });

</script>
</html>

<?php
    exit(); // Stop php
    }

    // If the user id is not used, then create a new one in database
    else {
        $sql = "INSERT INTO `user_table`(`user_id`,`user_pw`,`user_email`) VALUES " .
                                       "(:user_id ,:user_pw ,:user_email)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->bindParam(":user_pw", $user_pw, PDO::PARAM_STR);
        $stmt->bindParam(":user_email", $user_email, PDO::PARAM_STR);
        $status = $stmt->execute();

        if ($status === false) {
            //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
            $error = $stmt->errorInfo();
            exit("SQL_ERROR when create new user: " . $error[2]);
        }

        $user_id_num = $pdo->lastInsertId();

        // 取得該筆新增的 user 資料
        $sql = "SELECT * FROM user_table WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $user_id_num, PDO::PARAM_INT);
        $status = $stmt->execute();
        $status = $stmt->execute();
        if ($status === false) {
            //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
            $error = $stmt->errorInfo();
            exit("SQL_ERROR when inserting user info: " . $error[2]);
        }

        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Log in
else {
    // 取得user_id的資料
    $sql = "SELECT * FROM `user_table` WHERE user_id=:user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
    $status = $stmt->execute();
    if ($status === false) {
        //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
        $error = $stmt->errorInfo();
        exit("SQL_ERROR when checking id and pw: " . $error[2]);
    }

    $values = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 預設登入成功
    $login_successed = true;

    // 如果在 db 裡找不到輸入的 user_id 就變成登入失敗
    if(count($values) == 0) {
        $login_successed = false;
    }

    // 如果 db 裡有找到 user_id 那就繼續檢查 pw 是不是符合
    else {
        // 檢查密碼是否正確
        $pw = password_verify($user_pw, $values[0]["user_pw"]);
        if(!$pw) {
            $login_successed = false;
        }

        $user_id_num = $values[0]["id"];
    }

    // 如果登入失敗, 那就要回到 login 畫面並顯示登入失敗訊息
    if ($login_successed) {
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
    <form id="back_to_index_form" method="post" action="index.php">
        <input type="text" name="user_id" value="<?= $user_id ?>" hidden />
        <input type="text" name="error_message" value="Wrong id and password" hidden />
        <input type="text" name="is_new_user" value="<?= $is_new_user ?>" hidden />
    </form>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="js/pc_or_mobile.js"></script>
<script>

    $(document).ready(function() {
        $("#back_to_index_form").attr("action", getDevicePage("index.php"));
        $("#back_to_index_form").submit();
    });

</script>
</html>
<?php
        exit(); // Stop php
    }
}

// Login 成功或 create 帳號成功就把使用者的資料存進session裡去
$_SESSION["chk_ssid"] = session_id();   // session id
$_SESSION["user_id"] = $user_id_num;

// 如果使用者有設定nickname就使用nickname
if (isset($values[0]["nickname"])) {
    $_SESSION["nickname"] = $values[0]["nickname"];
}
// 如果使用者有設定nickname就先使用user_id當nickname
else {
    $_SESSION["nickname"] = $values[0]["user_id"];
}

// 如果使用者順利建立帳號或者是成功登入了, 那就去book_page_table裡找該使用者的最新一本創作的書
$values = getAllBooksOfUserFromDb($user_id_num, $pdo);

// 如果該使用者尚未創作任何一本書就把書的id跟名稱留白
if(count($values)==0){
    $latest_book_id = "";
    $latest_book_name = "";
}

// 如果該使用者有創作過書就設定為搜尋結果書籍的名稱
else {
    $latest_book_id = $values[0]["id"];
    $latest_book_name = $values[0]["book_name"];
}

// 把書本的資料也存到 session 去
$_SESSION["book_id"] = $latest_book_id;
$_SESSION["book_name"] = $latest_book_name;
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
        window.location.href = getDevicePage("view.php");
    });

</script>
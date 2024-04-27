<?php
session_start();
include 'check_session.php';
checkSession();

include 'connect_to_db.php';

include 'ai_test.php';

// Connect to db
$pdo = connect_to_db();
if ($pdo == null) {
    exit();
}

$book_id = $_SESSION["book_id"];
$book_name = $_SESSION["book_name"];
$user_id = $_SESSION["user_id"];
$previously_recommended_books = "";
if(isset($_POST["recommended_books"])) {
    $previously_recommended_books = $_POST["recommended_books"];
}

// 搜尋所有該使用者所製作的書
$sql = "SELECT * FROM `book_table` WHERE author_id=:author";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":author", $user_id, PDO::PARAM_STR);
$status = $stmt->execute();

if ($status === false) {
    //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit("SQL_ERROR: " . $error[2]);
}

// 成功從DB取得資料的話，從資料庫裡面讀到$books的變數裡
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 紀錄該使用者所記錄過的所有書
$all_read_books = [];

foreach ($books as $book) {
    $sql = "SELECT storybook_name FROM book_page_table WHERE book_id=:book_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":book_id", $book["id"], PDO::PARAM_INT);
    $status = $stmt->execute();

    if ($status === false) {
        //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
        $error = $stmt->errorInfo();
        exit("SQL_ERROR: " . $error[2]);
    }

    $storybook_names = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 把每本書不重複的話就加入 all_read_books
    foreach ($storybook_names as $storybook_name) {
        // 如果不存在於 $read_book_name 裡就追加進去
        if(!in_array($storybook_name["storybook_name"], $all_read_books)){
            array_push($all_read_books, $storybook_name["storybook_name"]);
        }
    }
}

function getReplyFromChatGPT($content)
{
    $ch = curl_init();
    $url = 'https://api.openai.com/v1/chat/completions';
    $api_key = getChatGpuApiKey();

    $messages = [
        [
            'role' => 'user',
            'content' => $content
        ]
    ];

    $post_fields = array(
        "model" => "gpt-3.5-turbo",
        "messages" => $messages,
        "max_tokens" => 100,
        "temperature" => 0.7,
    );

    $header = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ];

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    }
    curl_close($ch);

    $response = json_decode($result);

    return $response->choices[0]->message->content;
}

// For test
function copyChatGpu($question) {
    $response = json_decode("{\n  \"id\": \"chatcmpl-9Fk61N4IvJTlWBhYlI2A0FX21LR6Q\",\n  \"object\": \"chat.completion\",\n  \"created\": 1713539141,\n  \"model\": \"gpt-3.5-turbo-0125\",\n  \"choices\": [\n    {\n      \"index\": 0,\n      \"message\": {\n        \"role\": \"assistant\",\n        \"content\": \"1. \\\"Where the Wild Things Are\\\" by Maurice Sendak\\n2. \\\"Goodnight Moon\\\" by Margaret Wise Brown\\n3. \\\"The Giving Tree\\\" by Shel Silverstein\\n4. \\\"The Rainbow Fish\\\" by Marcus Pfister\\n5. \\\"Chicka Chicka Boom Boom\\\" by Bill Martin Jr. and John Archambault\"\n      },\n      \"logprobs\": null,\n      \"finish_reason\": \"stop\"\n    }\n  ],\n  \"usage\": {\n    \"prompt_tokens\": 45,\n    \"completion_tokens\": 71,\n    \"total_tokens\": 116\n  },\n  \"system_fingerprint\": \"fp_d9767fc5b9\"\n}\n");
    return $response->choices[0]->message->content;
}

// Set up the question to ask chat gpt
$question_to_ai = "I have read the following books" . $previously_recommended_books; 
foreach ($all_read_books as $read_book_name) {
    $question_to_ai .= ",\"". $read_book_name . "\"";
}
$question_to_ai .= ". Recommend me 6 other picture books.";

//echo $question_to_ai;

// Ask chat gpt
//$reply_from_ai = copyChatGpu($question_to_ai);
$reply_from_ai = getReplyFromChatGPT($question_to_ai);

// Process with gpt's answer
$recommended_books_rough = preg_split("/[0-9]+\./", $reply_from_ai);

$recommended_books = [];
foreach ($recommended_books_rough as $recommended_book) {
    if($recommended_book != "") {
        $recommended_book_and_author = preg_split("/ by /", $recommended_book);

        $recommended_book_author = "Unknown Author";
        if(count($recommended_book_and_author) > 1) {
            $recommended_book_author = trim(str_replace('"', '', preg_replace('/\s+/', ' ', $recommended_book_and_author[1])));
        }

        $recommended_book_name = trim(str_replace('"', '', preg_replace('/\s+/', ' ', $recommended_book_and_author[0])));

        // APIの基本になるURL
        $base_url = 'https://www.googleapis.com/books/v1/volumes?q=' . 
            str_replace(' ', '+', $recommended_book_name . "+by+" .
            str_replace(' ', '+', $recommended_book_author) . 
            '&maxResults=1');

        $book_json = file_get_contents($base_url);
        $book_data = json_decode($book_json);
        $book = $book_data->items[0];

        $thumbnail = "";
        if(isset($book->volumeInfo)) {
            if(isset($book->volumeInfo->imageLinks)) {
                if(isset($book->volumeInfo->imageLinks->thumbnail)) {
                    $thumbnail = $book->volumeInfo->imageLinks->thumbnail;
                }
            }
        }

        array_push($recommended_books, array(
            "book" => $recommended_book_name,
            "author" => $recommended_book_author,
            "thumbnail" => $thumbnail,
        ));
    }
}

// Save current recommended books 
foreach($recommended_books as $book) {
    $previously_recommended_books = $previously_recommended_books . 
        ", \"" . $book["book"] . " by " . $book["author"] . "\"";
}

$previously_recommended_books = str_replace("\'", "\\\'", $previously_recommended_books);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Storybook Memory Maker</title>
    <link rel="stylesheet" href="./css/view_and_bookshelf.css">
    <link rel="stylesheet" href="./css/al_recommendation.css">
</head>
<body style="background-image: none;">
<!-- 一開始網頁還沒讀取完之前看起來版面會很亂所以先把畫面遮起來等到一切整理好之後再秀出來 -->
<div id="blank_curtain" class="blank_curtain_style"></div>
<!-- 左邊的選單 -->
<div class="left_bar_back_bottom"></div>
<div class="left_bar_back_top"></div>
    <div class="left_bar_back_style">
        <div id="left_bar_main" class="left_bar_layer_1_style">
            <button id="btn_about_us" class="edit_btn_style"> About Us</button>    
            <button id="btn_service" class="edit_btn_style"> Service </button>
            <button id="btn_news" class="edit_btn_style"> News </button>
            <button id="btn_contact" class="edit_btn_style"> Contact </button>
            <button id="btn_logout" class="edit_btn_style"> Log Out </button>
        </div>
        <div id="left_bar_service" class="left_bar_layer_2_style">
            <button id="btn_buy_this_book" class="edit_btn_style"> Buy Book </button>
            <button id="btn_bookshelf" class="edit_btn_style"> Bookshelf </button>
            <button id="btn_ai_recommend" class="edit_btn_style"> AI Recommend </button>
            <button id="btn_see_my_trend" class="edit_btn_style"> See My Trend </button>
            <button onclick="javascript: backtoMain('left_bar_service');" class="edit_btn_style"> Back </button>
        </div>
        <div class="left_bar_left_side_fadeout_curtain_style"></div>
        <div class="left_bar_right_side_fadeout_curtain_style"></div>
    </div>
    <!-- 右半邊選擇書本的畫面 -->
    <div class="view_main_back_style">
        <div class="view_book_title_style"></div>
        <div class="bookshelf_book_list_style">
            <div class="bookshelf_prev_book_style">
                <div class="prev_book_btn_style" onclick="javascript: movePrev('my_book');">◀</div>
            </div>
            <div class="bookshelf_all_books_style">
                <?php
                $i = 0;
                foreach ($recommended_books as $book) {
                    ?>

                <div id="my_book_<?= $i ?>" class="one_book_cover_style">
                    <div id="book_<?= $i ?>" onclick="javascript: viewBook('<?=$book["book"]?>', '<?=$book["author"]?>');" class="view_right_page_back_style">
                        <div id="cover_page_<?= $i ?>" class="first_page_all_style first_page_for_recommendation_style">
                        <div class="book_cover_thumbnail_back_style">
                            <?php if($book["thumbnail"] != "") { ?>
                                <img src="<?= $book["thumbnail"] ?>" alt="<?=$book["book"] ?>" style="book_cover_thumbnail_style">
                            <?php } ?>
                            </div>    
                        
                        <div class="first_page_title_for_recommendation_style">
                                <?=$book["book"]?>
                            </div>
                            
                            <div class="book_cover_author_back_style">
                            <div class="first_page_author_for_recommendation_style">
                                by <?=$book["author"]?>
                            </div>
                            </div>
                        </div>                        
                    </div>
                </div>

                <?php
                $i++;
                }
                ?>

                <!--<div id="my_book_new" class="new_book_cover_style" onclick="javascript: viewBook('', '');">
                    <div id="new_book" class="view_new_right_page_back_style">
                        <div class="new_book_icon_back_style">
                            <div class="new_book_icon_circle_style"></div>
                            <span class="new_book_icon_horizontal_line_style"></span>
                            <span class="new_book_icon_verticle_line_style"></span>
                        </div>
                    </div>
                </div>-->
            </div>
            <div class="bookshelf_next_book_style">
                <div class="next_book_btn_style" onclick="moveNext('my_book');">▶</div>
            </div>
        </div>

        <div class="book_buttons_back_style">
            <button id="refresh_ai_list" class="book_btn_style refresh_book_btn_style"> </button>
            <button id="add_to_my_favorite" class="book_btn_style add_to_favorite_book_btn_style"> </button>
            <button id="check_my_favorite" class="book_btn_style check_favorite_book_btn_style"> </button>
            <button id="share_book" class="book_btn_style share_book_btn_style"> </button>
            <button id="buy_book" class="book_btn_style buy_book_btn_style"> </button>
        </div>

        <div>
            <form id="refresh_ai_list_form" method="post" action="try_recommendation.php">
                <input id="recommended_books" type="text" name="recommended_books" value='<?=$previously_recommended_books?>' hidden>
            </form>
        </div>
    </div>
    
</body>

<!-- JS Library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script> 
<script src="./js/pc_or_mobile.js"></script>
<script src="./js/left_bar_functions.js"></script>


<script>

    function viewBook(book_name, book_author) {
        //        alert(book_name + " by " + book_author);

        keyword = book_name.replace(" ", "+") + "+by+" + 
                  book_author.replace(" ", "+") + "+image";

        window.open("https://www.google.com/search?q=" + keyword, '_blank').focus();
    }

    // 停止變更版面的功能
    $(document).ready(function () {
        updateBookButtons("my_book");

        // 等到一切都就緒了再來把白色簾幕掀開
        $("#blank_curtain").css({ opacity: 0.0 });
        setTimeout(() => {
            $("#blank_curtain").attr("class", "empty");
        }, 2000);
    });

    // 從鋼琴作業裡copy過來的
    function updateBookButtons(book_prefix) {
/*        $("#" + book_prefix + "_new").css({
            left: (10 + (bookListLength[book_prefix] - currBookIndex) * 245) + "px",
        });*/
        for (let i = 0; ; ++i) {
            let book_id = book_prefix + "_" + i;
            if ($("#" + book_id).length) {
                let book_btn = $("#" + book_id);
                book_btn.css({
                    left: (10 + (i - currBookIndex) * 390) + "px",
                });
            }
            else {
                break;
            }
        }
    }

    let currBookIndex = 0;
    function movePrev(book_prefix) {
        if (currBookIndex > 0) {
            currBookIndex--;
            updateBookButtons(book_prefix);
        }
    }

    let bookListLength = [];
    bookListLength['my_book'] = <?= count($recommended_books) ?>;
    function moveNext(book_prefix) {
        
        if (currBookIndex < bookListLength[book_prefix] - 2) {
            currBookIndex++;
            updateBookButtons(book_prefix);
        }
    }

    // 生成指定的svg icon html語法
    function createSvgContent(svg_item_id, svg_ref_id) {
        return '<svg class="svg_icon_style" viewBox="' + $("#" + svg_ref_id).attr("bounding-box") + '">' +
               '<use id="' + svg_item_id + '" xlink:href="#' + svg_ref_id + '" ref_target="' + svg_ref_id + '" x="0" y="0"></use>' +
               '</svg>';
    }

    $("#refresh_ai_list").on("click", function(){
        $("#refresh_ai_list_form").submit();
    });

</script>

</html>
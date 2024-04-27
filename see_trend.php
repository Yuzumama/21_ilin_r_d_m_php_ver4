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

// 從 book_tag_table 裡面取所有的東西出來
$sql = "SELECT * FROM book_tag_table";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

if ($status === false) {
    //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit("SQL_ERROR: " . $error[2]);
}

// 成功從DB取得資料的話，從資料庫裡面讀到$all_tags的變數裡
$all_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user_id = $_SESSION["user_id"];

// Get id of 2 test account
$test_user_1_id = getUserId('test_user', $pdo);
$test_user_2_id = getUserId('test_user2', $pdo);

// 從 db 裡取得該使用者所有念過的繪本的 tag 資料
function getAllTagRecordsOfUserFromDb($target_user_id, $pdo)
{
    $tag_summary = array();

    // 取得該使用者所有的書本
    $all_books = getAllBooksOfUserFromDb($target_user_id, $pdo);
    foreach ($all_books as $book) {

        // 取得各本書的所有內頁
        $all_pages = getAllPagesOfBookFromDb($book["id"], $pdo);
        foreach ($all_pages as $page) {

            // 取得頁面上的 tag
            for ($i = 0; $i < 3; $i++) {
                if (isset($page["book_tag_" . $i])) {

                    $curr_tag = $page["book_tag_" . $i];

                    // 這個 tag 不可以是空字串
                    if($curr_tag != "") {

                        // 如果這個 tag 不在 $tag_summary 裡面, 那就追加進去
                        if (!isset($tag_summary[$curr_tag])) {
                            $tag_summary[$curr_tag] = array(
                                "num" => 0,
                                "books" => [],
                            );
                        }

                        $tag_summary[$curr_tag]["num"]++;
                        if (!in_array($page["storybook_name"], $tag_summary[$curr_tag]["books"])) {
                            array_push($tag_summary[$curr_tag]["books"], $page["storybook_name"]);
                        }
                    }
                }
            }
        }
    }
    return $tag_summary;
}

$tag_summary = [array(
    "user_id" => "you",
    "tag_summary" => getAllTagRecordsOfUserFromDb($user_id, $pdo),
),
array(
    "user_id" => "other_user_1",
    "tag_summary" => getAllTagRecordsOfUserFromDb($test_user_1_id, $pdo),
),
array(
    "user_id" => "other_user_2",
    "tag_summary" => getAllTagRecordsOfUserFromDb($test_user_2_id, $pdo),
)];

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Storybook Memory Maker</title>
    <link rel="stylesheet" href="./css/see_trend.css">
    <link rel="stylesheet" href="./css/view_and_bookshelf.css">
</head>
<body>
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
    <!-- 右半邊秀出數據的畫面 -->
    <div class="view_main_back_style">
        <!-- Radar chart -->
        <div class="one_figure_back_style">
            <div id="figure_title" class="figure_title_style">
                    
            </div>
            <div id="figure_content" class="figure_content_style">
                <canvas id="figure_chart" class="figure_chart_style"></canvas>
                <div class="change_comparison_target_button_back_style">
                    <button id="change_comparison_target" class="book_btn_style share_book_btn_style"> </button>
                </div>
                <div class="change_figure_button_back_style">
                    <button id="change_figure_style" class="book_btn_style refresh_book_btn_style"> </button>
                </div>
            </div>
        </div>

        <!-- More analyze data -->
        <div id="analyze_summary" class="analyze_summary_back_style">
            <div id="field_i_win" class="analyze_region_style">
                <div class="analyze_region_title_style">
                    The fields you read more:
                </div>
                <div class="one_field_style">
                    <div class="win_field_crown_style" style="left: 120px;"></div>
                    <div id="win_field_1" class="field_name_style"> AAA </div>
                    <div id="win_field_score_1" class="field_score_style"> AAA </div>
                    <button class="show_more_book_btn_style" onclick="showMoreBooks(0)"> Show more books </button>
                </div>
                <div class="one_field_style">
                    <div id="win_field_2" class="field_name_style"> BBB </div>
                    <div id="win_field_score_2" class="field_score_style"> AAA </div>
                    <button class="show_more_book_btn_style" onclick="showMoreBooks(1)"> Show more books </button>
                </div>
                <div class="one_field_style">
                    <div id="win_field_3" class="field_name_style"> CCC </div>
                    <div id="win_field_score_3" class="field_score_style"> AAA </div>
                    <button class="show_more_book_btn_style" onclick="showMoreBooks(2)"> Show more books </button>
                </div>
            </div>
            <div id="field_i_lose" class="analyze_region_style" style="margin-top: 50px">
                <div class="analyze_region_title_style">
                    The fields you read less:
                </div>
                <div class="one_field_style">
                    <div id="lose_field_3" class="field_name_style"> AAA </div>
                    <div id="lose_field_score_3" class="field_score_style"> AAA </div>
                    <button class="show_more_book_btn_style" onclick="recommendSomeBooks(0)"> Suggest some books </button>
                </div>
                <div class="one_field_style">
                    <div id="lose_field_2" class="field_name_style"> BBB </div>
                    <div id="lose_field_score_2" class="field_score_style"> AAA </div>
                    <button class="show_more_book_btn_style" onclick="recommendSomeBooks(1)"> Suggest some books </button>
                </div>
                <div class="one_field_style">
                    <div class="win_field_crown_style" style="left: 190px;"></div>
                    <div id="lose_field_1" class="field_name_style"> CCC </div>
                    <div id="lose_field_score_1" class="field_score_style"> AAA </div>
                    <button class="show_more_book_btn_style" onclick="recommendSomeBooks(2)"> Suggest some books </button>
                </div>
            </div>
        </div>
    </div>
</body>

<!-- JS Library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script> 
<script src="./js/pc_or_mobile.js"></script>
<script src="./js/left_bar_functions.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

    let chart = null;
    let curr_chart_style = 'bar';
    let curr_comparison_user = 1;

    function createChart() {

        // 刪除原本的chart
        if (chart) {
            chart.destroy();
            chart = null;
        }

        // 取得canvas物件
        const chart_container = $("#figure_chart");

        // 取得畫chart的資料, 就是各個項目的名稱以及各項目閱讀的書本數量
        let label_and_data = createChartData();

        // 根據現在的style畫不同的圖
        if (curr_chart_style === 'bar') {

            let max_num = 0;

            for (j = 0; j < label_and_data["datasets"].length; ++j) {
                for (i = 0; i < label_and_data["datasets"][j]["data"].length; ++i) {
                    max_num = Math.max(max_num, label_and_data["datasets"][j]["data"][i]);
                    if (j == 1) {
                        label_and_data["datasets"][j]["data"][i] *= -1;
                    }
                }
            }


            // 畫成長條圖
            chart = new Chart(chart_container, {
                type: curr_chart_style,
                data: {
                    labels: label_and_data["labels"],
                    datasets: [{
                        label: label_and_data["datasets"][0]["label"],
                        data: label_and_data["datasets"][0]["data"],
                        borderWidth: 3,
                        xAxisID: "x1",
                        yAxisID: "y1",
                    },
                    {
                        label: label_and_data["datasets"][1]["label"],
                        data: label_and_data["datasets"][1]["data"],
                        borderWidth: 3,
                        xAxisID: "x2",
                        yAxisID: "y2",
                    },
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {

                        x1: {
                            min: -max_num,
                            max: max_num,
                            offset: true,
                            ticks: {
                                callback: function (val, index) {
                                    return Math.abs(val);
                                }
                            }
                        },
                        y1: {
                            position: 'right',
                            grid: {
                                display: false,
                            }
                        },
                        x2: {
                            min: -max_num,
                            max: max_num,
                            offset: true,
                            display: false,
                        },
                        y2: {
                            position: 'left',
                            grid: {
                                display: false,
                            }
                        }                        
                    }
                }
            });

        } else if (curr_chart_style === 'radar') {

            // 畫雷達圖
            chart = new Chart(chart_container, {
                type: curr_chart_style,
                data: {
                    labels: label_and_data["labels"],
                    datasets: [{
                        label: label_and_data["datasets"][0]["label"],
                        data: label_and_data["datasets"][0]["data"],
                        borderWidth: 3,
                    },
                    {
                        label: label_and_data["datasets"][1]["label"],
                        data: label_and_data["datasets"][1]["data"],
                        borderWidth: 3,
                    },
                    ]
                },
                options: {
                    scales: {
                        r: {
                            suggestedMin: 0,
                        }
                    }
                }
            });
        }
    }

    let win_results = [];
    let lose_results = [];

    function updateComparisonTarget() {

        // 比較兩者所看的書的種類
        let label_and_data = createChartData();

        // 計算各種類別裡兩者念過的書的數量的差異
        let diff_between_two_users = [];
        for (i = 0; i < label_and_data["labels"].length; ++i) {
            var diff = label_and_data["datasets"][1]["data"][i] - label_and_data["datasets"][0]["data"][i];
            diff_between_two_users.push({
                diff: diff,
                tag: label_and_data["labels"][i],
                my_number: label_and_data["datasets"][0]["data"][i],
                comparison_number: label_and_data["datasets"][1]["data"][i],
            });
        }

        // 照差異排序
        diff_between_two_users = (diff_between_two_users.sort(function (a, b) {
            return (b.diff - a.diff);
        }));

        console.log(diff_between_two_users);

        // 把原本的比較結果清空
        win_results = [];
        lose_results = [];

        // 重新把最多跟最少的前三名塞進比較結果
        for (i = 0; i < 3; ++i) {
            lose_results.push(diff_between_two_users[i]);  // 輸最多的前三個類別

            // 把結果秀出來
            $("#lose_field_" + (i + 1)).text(lose_results[i].tag);
            $("#lose_field_score_" + (i + 1)).text(lose_results[i].my_number + " / " + lose_results[i].comparison_number);
        }

        for (i = 0; i < 3; ++i) {
            win_results.push(diff_between_two_users[diff_between_two_users.length - 1 - i]);  // 贏最多的前三個類別

            // 把結果秀出來
            $("#win_field_" + (i+1)).text(win_results[i].tag);
            $("#win_field_score_" + (i+1)).text(win_results[i].my_number + " / " + win_results[i].comparison_number);
        }

        console.log(win_results);
        console.log(lose_results);
    };

    // 參考同期的翻頁效果
    $(document).ready(function () {

        // 比較使用者的閱讀數量
        updateComparisonTarget();

        // 畫圖
        createChart();

        // 等到一切都就緒了再來把白色簾幕掀開
        $("#blank_curtain").css({ opacity: 0.0 });
        setTimeout(() => {
            $("#blank_curtain").attr("class", "empty");
        }, 2000);
    });

    // 更換chart的類型
    $("#change_figure_style").on("click", function () {

        // 切換圖的種類
        if (curr_chart_style === 'radar') {

            // 變成長條圖
            curr_chart_style = 'bar';

        } else if (curr_chart_style === 'bar') {

            // 變成雷達圖
            curr_chart_style = 'radar';

        }

        // 重新畫圖
        createChart();
    });

    // 更換比較對象
    $("#change_comparison_target").on("click", function () {

        // 切換比較對象
        ++curr_comparison_user;
        if (curr_comparison_user >= all_users_tag_summary.length) {
            curr_comparison_user = 1;
        }

        // 重新比較兩個使用者的閱讀數量
        updateComparisonTarget();

        // 重新畫圖
        createChart();
    });

    const all_users_tag_summary = (JSON.parse('<?= json_encode($tag_summary); ?>'));

    function createChartData() {

        let labels = [];
        let datasets = [];
        let comparison_target = [
            all_users_tag_summary[0],
            all_users_tag_summary[curr_comparison_user]
        ]

        // Put all user's tags into labels
        for (i = 0; i < comparison_target.length; ++i) {
            let user_tag_summary = comparison_target[i]["tag_summary"];
            for (var key in user_tag_summary) {
                if (!labels.includes(key)) {
                    labels.push(key);
                }
            }
        }

        // Summarize each user's number of books
        for (i = 0; i < comparison_target.length; ++i) {
            let user_tag_summary = comparison_target[i]["tag_summary"];
            let data = [];

            // 把所有 tag 檢查一遍
            for (j = 0; j < labels.length; ++j) {
                let tag_name = labels[j];

                // 如果使用者的書裡面有這個 tag 就把它的數量加進去
                if (user_tag_summary[tag_name] != undefined) {
                    data.push(user_tag_summary[tag_name]["num"]);
                }

                // 如果使用者的書裡面有這個 tag 就填0
                else {
                    data.push(0);
                }
            }

            datasets.push({
                label: comparison_target[i]["user_id"],
                data: data,
            });
        }

        return {
            labels: labels,
            datasets: datasets,
        };
    }

</script>
</html>
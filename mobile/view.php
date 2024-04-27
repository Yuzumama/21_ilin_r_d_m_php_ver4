<?php
include '../view_common.php';

// HTML
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Storybook Memory Maker</title>
    <link rel="stylesheet" href="./css/view_style.css" />
    <link rel="stylesheet" href="./css/view_and_bookshelf.css" />
</head>
<body>
    
    <!-- 主畫面 -->
    <div class="view_item_empty_back_style">
        <svg xmlns="http://www.w3.org/2000/svg">
            <?php
            printSvgIconDefInHtml($icons_in_db);
            ?>
        </svg>
    </div>
    <div class="main_frame">
        <div class="top_bar_back_style">
            <div id="top_bar_start_btn" class="top_bar_start_btn_style">
                <div class="top_bar_start_btn_line_style" style="top: 30px;"></div>
                <div class="top_bar_start_btn_line_style" style="top: 74px;"></div>
                <div class="top_bar_start_btn_line_style" style="top: 120px;"></div>
            </div>
        </div>
        <div class="view_book_and_buttons_style">
            <div id="book" class="view_one_pages_back_style">
                <!-- 封面的左邊插入空白空間 -->
                <div id="page_0" class="view_right_page_back_style">
                
                    <div id="page_bottom_bar_0" class="empty">
                        <button onclick="javascript: showIconMenu(0);" class="page_btn_style page_layout_btn_style">Add Icon</button>
                        <button onclick="javascript: resetOnePageLayout(0);" class="page_btn_style page_layout_rightmost_btn_style">Reset</button>
                    </div>
                </div>

                <!-- 用for迴圈跑每一頁的資料設定 -->
                <?php

                // 計算這本書的總頁數, 封面已經先算進去了
                $total_num_pages = 1;

                // i = 頁數
                $i = 1;
                foreach ($values as $value) {
                    ?>
                <!-- 每一頁的資料庫都有設定ID, 可以根據ID更新 -->
                <!-- 不想給PHP處理的部分 -->
                <div id="page_<?= $i ?>" class="<?php
                // 單數頁設在左邊，偶數頁的右邊，利用php的$i去找
                if (($i % 2) == 1)
                    echo "view_left_page_back_style";
                else
                    echo "view_right_page_back_style";
                ?>">
                    <div id="record_id_<?= $i ?>" hidden><?= $value['id'] ?></div>
                    <div id="page_bottom_bar_<?= $i ?>" class="empty">
                        <button onclick="javascript: showIconMenu(<?= $i ?>);" class="page_btn_style page_layout_btn_style">Add Icon</button>
                        <button onclick="javascript: resetOnePageLayout(<?= $i ?>);" class="page_btn_style page_layout_btn_style">Reset</button>
                        <button onclick="javascript: editText(<?= $i ?>, true);" class="page_btn_style page_layout_btn_style">Edit</button>
                        <button onclick="javascript: deleteOnePage(<?= $value["id"] ?>)" class="page_btn_style page_layout_rightmost_btn_style">Delete</button>
                    </div>
                    <div id="update_form_back_<?= $i ?>" class="empty">
                        <!--表單的語法-->
                        <form id="upload_form_<?= $i ?>" action="update_page_content.php" method="post" enctype="multipart/form-data">
                    
                            <div id="image_preview_<?= $i ?>" class="image_preview_style">
                                <img id="record_image_content_<?= $i ?>" src="../<?= $value["image_filename"] ?>" class="view_record_image_style" />
                            </div>
                            <div class="form_one_item_back_style">
                                <input type="file" id="image_file_chooser_<?= $i ?>" name="image_file_chooser" onchange="changeImage(<?= $i ?>);" hidden />
                                <input type="text" id="image_file_updated_<?= $i ?>" name="image_file_updated" value="0" hidden />
                            </div>
                            <!-- 隱藏 -->
                            <div class="empty">
                                <input type="text" name="page_id" hidden value="<?= $value["id"] ?>" />
                                <div class="form_label_style">
                                    Book: 
                                </div>
                                <input id="book_name_<?= $i ?>" type="text" name="book_name" class="form_text_style" value="<?= $book_name ?>" />
                                <input id="book_id_<?= $i ?>" type="text" name="book_id" value="<?= $book_id ?>" />
                            </div>
                        
                            <!-- 顯示 -->
                            <div class="form_one_item_back_style">
                                <div class="form_label_style">
                                    Storybook:
                                </div>
                                <input type="text" name="storybook" class="form_text_style" value="<?= $value["storybook_name"] ?>" />
                            </div>
                            <div class="form_one_item_back_style">
                                <div class="form_label_style">
                                    Progress:
                                </div>
                                <input type="text" name="progress" class="form_text_style" value="<?= $value["progress"] ?>" />
                            </div>
                            <div class="form_one_item_back_style">
                                <div class="form_label_style">
                                    Storyteller: 
                                </div>
                                <input type="text" name="author" class="form_text_style" value="<?= $author ?>" />
                            </div>
                            <div class="form_one_item_back_style">
                                <div class="form_label_style">
                                    Listener:
                                </div>
                                <input type="text" name="child_name" class="form_text_style" value="<?= $value["child_name"] ?>"/>
                            </div>
                            <div class="form_one_item_back_style">
                                <div class="form_label_style">
                                    Feedback:
                                </div>
                                <input type="text" name="child_feedback" class="form_text_style" value="<?= $value["child_feedback"] ?>" />
                            </div>
                            <div class="form_one_item_back_style">
                                <div class="form_label_style">
                                    Comment: 
                                </div>
                                <textarea name="comments" rows="5" cols="40" class="input_comments_style"><?= $value["input_comment"] ?></textarea>
                            </div>
                        </form>
                        <div class="form_button_back_style">
                            <button class="page_btn_style select_image_btn_style" onclick="selectImage(<?= $i ?>);"> Select Image </button>
                            <button class="page_btn_style send_btn_style" onclick="sendForm(<?= $i ?>);"> Update </button>
                            <button class="page_btn_style cancel_btn_style" onclick="editText(<?= $i ?>, false)"> Cancel </button>
                        </div>
                    </div>
                </div>
            
                <?php
                // 每處理完1頁，頁數+1
                $i++;
                $total_num_pages++;
                }
                // 因為中間有穿插不想被PHP處理的HTML語法，所以用好幾個PHP引號
                ?>

                <!-- 最後一頁的輸入表單 -->
                <!-- 表單有可能在頁數的左右兩邊，所以要再寫一次if語法 -->
                <div id="page_<?= $i ?>" class="<?php
                if (($i % 2) == 1)
                    echo "view_left_page_back_style";
                else
                    echo "view_right_page_back_style";
                ?>">
                    <!--表單的語法-->
                    <form id="upload_form" action="../write.php" method="post" enctype="multipart/form-data">
                    
                        <div id="image_preview" class="image_preview_style"></div>
                        <div class="form_one_item_back_style">
                            <input type="file" id="image_file_chooser" name="image_file_chooser" onchange="changeImage();" hidden>
                            <input type="text" id="image_file_updated" name="image_file_updated" value="0" hidden />
                        </div>
                        <!-- 隱藏 -->
                        <div class="empty">
                            <div class="form_label_style">
                                Book: 
                            </div>
                            <input id="book_name" type="text" name="book_name" class="form_text_style" value="<?= $book_name ?>" />
                            <input id="book_id" type="text" name="book_id" value="<?= $book_id ?>" />
                        </div>
                    
                        <!-- 顯示 -->
                        <div class="form_one_item_back_style">
                            <div class="form_label_style">
                                Storybook:
                            </div>
                            <input type="text" name="storybook" class="form_text_style" />
                        </div>
                        <div class="form_one_item_back_style">
                            <div class="form_label_style">
                                Progress:
                            </div>
                            <input type="text" name="progress" class="form_text_style" />
                        </div>
                        <div class="form_one_item_back_style">
                            <div class="form_label_style">
                                Storyteller: 
                            </div>
                            <input type="text" name="author" class="form_text_style" value="<?= $author ?>" />
                        </div>
                        <div class="form_one_item_back_style">
                            <div class="form_label_style">
                                Listener:
                            </div>
                            <input type="text" name="child_name" class="form_text_style" />
                        </div>
                        <div class="form_one_item_back_style">
                            <div class="form_label_style">
                                Feedback:
                            </div>
                            <input type="text" name="child_feedback" class="form_text_style" />
                        </div>
                        <div class="form_one_item_back_style">
                            <div class="form_label_style">
                                Comment: 
                            </div>
                            <textarea name="comments" rows="5" cols="40" class="input_comments_style"></textarea>
                        </div>
                    </form>
                    <div class="form_button_back_style">
                        <button id="select_image" class="page_btn_style select_image_btn_style" onclick="selectImage();"> Image </button>
                        <button id="send_btn" class="page_btn_style send_btn_style" onclick="sendForm();"> Send </button>
                    </div>
                </div>
                <?php
                // 把輸入表單的這一頁也算進去
                $total_num_pages++
                    ?>

                <!-- 為了不讓表單放到最後一頁, 插入空白頁，讓它變成一本書可以合起來 -->
                <?php
                if (($i % 2) == 1) {
                    ?>
                <div id="page_<?= ($i + 1) ?>" class="view_right_page_back_style">
                </div>
                <?php
                $i++;

                // 把空白頁算進去
                $total_num_pages++;
                }
                ?>

                <!-- 封底 -->
                <div id="page_<?= ($i + 1) ?>" class="view_left_page_back_style">
                    <div class="first_page_all_style">
                        <div class="first_page_title_style">
                        </div>
                    </div>
                </div>
                <?php
                // 封底也算一頁
                $total_num_pages++
                    ?>
            </div>
            <div class="book_flip_page_btn_bar_style">
                <div class="prev_page_back_style">
                    <div id="prev_page_btn" class="prev_page_btn_circle_style">
                        ◀
                    </div>
                
                </div>
                <div style="width: 300px;"></div>
                <div class="prev_page_back_style">
                    <div id="next_page_btn" class="prev_page_btn_circle_style">
                    ▶
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/jquery-ui-touch-punch@0.2.3/jquery.ui.touch-punch.min.js"></script>
<script src="../js/set_item_draggable.js"></script>

<script>

    // Chat App的照片上傳Code再利用
    let selected_image = new Image();
    let selected_image_file = new File([""], "");

    function changeImage(page) {

        // 如果page為空白就代表是新的一頁
        if (!page) {
            item_name_postfix = "";
        }
        // 如果page非空白就代表是要更新已經存在的某一頁
        else {
            item_name_postfix = "_" + page;
        }

        $("#image_file_updated" + item_name_postfix).val("1");

        // The image is stored in memory
        selected_image_file = $("#image_file_chooser" + item_name_postfix)[0].files[0];

        let reader = new FileReader();
        reader.onloadend = function () {

            selected_image.src = reader.result;
            selected_image.onload = function () {
                $("#record_image_content" + item_name_postfix).remove();
                $("#image_preview" + item_name_postfix).css("background-image", "url('" + reader.result + "')");
            }
        };
        reader.readAsDataURL(selected_image_file);
    }

    function selectImage(page) {
        // 如果page為空白就代表是新的一頁
        if (!page) {
            item_name_postfix = "";
        }
        // 如果page非空白就代表是要更新已經存在的某一頁
        else {
            item_name_postfix = "_" + page;
        }
        $("#image_file_chooser" + item_name_postfix).click();
    }

    // 各頁面上的icon的數量
    let num_icons_on_page = Array(<?= (count($values) + 1) ?>);
    <?php
    // 設定封面上的icon數量
    $num_icons = 0;
    ?>
    num_icons_on_page[0] = <?= $num_icons ?>;
    <?php
    $i = 1;
    foreach ($values as $value) {
        $num_icons = 0;
        ?>
    num_icons_on_page[<?= $i ?>] = <?= $num_icons ?>;
    <?php
    $i++;
    }
    ?>

   // 生成指定的svg icon html語法
    function createSvgContent(svg_item_id, svg_ref_id) {
        return '<svg class="svg_icon_style" viewBox="' + $("#" + svg_ref_id).attr("bounding-box") + '">' +
               '<use id="' + svg_item_id + '" xlink:href="#' + svg_ref_id + '" ref_target="' + svg_ref_id + '" x="0" y="0"></use>' +
               '</svg>';
    }

    // 用javascript去生成每一頁的照片、日期、繪本名字等的html語法
    $(function () {  
        <?php

        // 取得封面照片檔名
        $first_page_image = null;
        if ($cover_values) {
            if (count($cover_values) > 0) {
                $first_page_image = $cover_values[0]["cover_filename"];
            }
        }

        // 取得封面格式json
        // 書名的放大、縮小、位置移動(編輯中)
        if ($cover_layout) {
            $cover_title_layout = $cover_layout["first_page_layout"];
        } else {
            $cover_title_layout = "{}";
        }

        ?>
        // 用javascript去生成封面的照片的html語法
        addOneDraggableItemWithoutPage(
            "page_0",
            '<?= json_encode($cover_title_layout) ?>',
            "cover_image",
            '<img id="cover_image_item" src="../<?= $first_page_image ?>" class="view_record_cover_image_style" />'
        );
        <?php

        // 如果使用者沒有上傳過封面照片那就把封面照片的物件隱藏起來
        if (!$first_page_image) {
            ?>
        $("#record_cover_image").attr("class", "empty");
        <?php
        }

        // 生成封面的書名
        $cover_title_html_content = "<div class='first_page_title_style'>";
        if ($book_name == "") {
            $cover_title_html_content = $cover_title_html_content .
                "<input id='input_book_title' type='text' class='input_book_title_style' />";
        } else {
            // 如果書名存在就直接秀出書名
            $cover_title_html_content = $cover_title_html_content .
                $book_name;
        }
        $cover_title_html_content = $cover_title_html_content .
            "</div>";
        ?>
        // 用javascript去生成封面的書名的html語法
        addOneDraggableItemWithoutPage(
            "page_0",
            '<?= json_encode($cover_title_layout) ?>',
            "cover_title",
            "<?= $cover_title_html_content ?>"
        );

        // 透過設定z-index 強制 title 秀在圖片跟icon前面
        $("#record_cover_title").css({"z-index": 2,});

        // 生成封面的icon
        <?php
        if (isset($cover_title_layout["icons"])) {

            $icon_id = 0;
            foreach ($cover_title_layout["icons"]["icon_layout"] as $icon_layout) {
                $icon_layout_str = json_encode($icon_layout);
                $icon_name = $icon_layout["icon_name"];
                ?>
        // 用javascript去生成封面的icon
        var svgContent = createSvgContent("use_icon_0_<?= $icon_id ?>", "<?= $icon_name ?>");
        addOneDraggableItemWithPageAndIndex("page_0", '<?= $icon_layout_str ?>', "icon", 0, <?= $icon_id ?>, svgContent);

        <?php
            ++$icon_id;
        }
        ?>
        num_icons_on_page[0] = <?= $cover_title_layout["icons"]["num_icons"] ?>;
        <?php
        } else {
            ?>
        num_icons_on_page[0] = 0;
        <?php
        }
        ?>

        // 用javascript去啟動封面的照片跟書名等的html語法
        setDraggableWithoutPage('cover_image');
        setDraggableWithoutPage('cover_title');

        // 用javascript去啟動封面的icon的html語法
        for (icon_id = 0; icon_id < num_icons_on_page[0]; ++icon_id) {
            setDraggableWithPageAndIndex("icon", 0, icon_id);
        }
        <?php
        $i = 1;
        foreach ($values as $value) {
            // $page_layout讀目前頁面的layout
            if (isset($value["page_layout"])) {
                $page_layout_str = $value["page_layout"];
            } else {
                $page_layout_str = "{}";
            }
            ?>

            // 生成第i頁上的元件
            addOneDraggableItemWithPage("page_<?= $i ?>", '<?= $page_layout_str ?>', "image",          <?= $i ?>, '<img id="record_image_content_' + (<?= $i ?>) + '" src="../<?= $value["image_filename"] ?>" class="view_record_image_style" />');
            addOneDraggableItemWithPage("page_<?= $i ?>", '<?= $page_layout_str ?>', "datetime",       <?= $i ?>, '<?= DateTime::createFromFormat('Y-m-d H:i:s', $value["input_date"])->format('Y/m/d H:i') ?>');
            addOneDraggableItemWithPage("page_<?= $i ?>", '<?= $page_layout_str ?>', "author",         <?= $i ?>, '<?= $value["storyteller"] ?>');
            <?php if (isset($value["storybook_name"])) { ?>
            addOneDraggableItemWithPage("page_<?= $i ?>", '<?= $page_layout_str ?>', "storybook",      <?= $i ?>, '<?= $value["storybook_name"] ?>');
            <?php }
            if (isset($value["child_name"])) { ?>
            addOneDraggableItemWithPage("page_<?= $i ?>", '<?= $page_layout_str ?>', "child_name",     <?= $i ?>, '<?= $value["child_name"] ?>');
            <?php }
            if (isset($value["progress"])) { ?>
            addOneDraggableItemWithPage("page_<?= $i ?>", '<?= $page_layout_str ?>', "progress",       <?= $i ?>, '<?= $value["progress"] ?>');
            <?php }
            if (isset($value["child_feedback"])) { ?>
            addOneDraggableItemWithPage("page_<?= $i ?>", '<?= $page_layout_str ?>', "child_feedback", <?= $i ?>, '<?= $value["child_feedback"] ?>');
            <?php }
            if (isset($value["input_comment"])) { ?>
            addOneDraggableItemWithPage("page_<?= $i ?>", '<?= $page_layout_str ?>', "comment",        <?= $i ?>, "<?= $value["input_comment"] ?>");
            <?php } ?>

            // 把文字設在最上層
            $("#record_datetime_<?= $i ?>").css({ "z-index": 2 });
            $("#record_author_<?= $i ?>").css({ "z-index": 2 });
            $("#record_storybook_<?= $i ?>").css({ "z-index": 2 });
            $("#record_child_name_<?= $i ?>").css({ "z-index": 2 });
            $("#record_progress_<?= $i ?>").css({ "z-index": 2 });
            $("#record_child_feedback_<?= $i ?>").css({ "z-index": 2 });
            $("#record_comment_<?= $i ?>").css({ "z-index": 2 });

            // 生成第i頁上的icon
            <?php
            $page_layout = json_decode($page_layout_str, true);
            if (isset($page_layout["icons"])) {
                $icon_id = 0;
                $icon_layout = $page_layout["icons"]["icon_layout"];
                foreach ($icon_layout as $one_icon) {
                    $icon_item_id = "icon_" . $i . "_" . $icon_id;
                    $icon_layout_str = json_encode($icon_layout[$icon_item_id]);
                    $icon_name = $one_icon["icon_name"];
                    ?>

                var svgContent = createSvgContent("use_icon_<?= $i ?>_<?= $icon_id ?>", "<?= $icon_name ?>");
                addOneDraggableItemWithPageAndIndex("page_<?= $i ?>", '<?= $icon_layout_str ?>', "icon", <?= $i ?>, <?= $icon_id ?>, svgContent, true);

                <?php
                ++$icon_id;
                }
                ?>
                num_icons_on_page[<?= $i ?>] = <?= $page_layout["icons"]["num_icons"] ?>;
        <?php
            } else {
                ?>
                num_icons_on_page[<?= $i ?>] = 0;
        <?php
            }
            $i++;
        }
        ?>

        for (page = 1; page <= <?= count($values) ?> ; ++page) {
            // 將第i頁的元件追加拖拉的功能
            setDraggableWithPage("image", page);
            setDraggableWithPage("datetime", page);
            setDraggableWithPage("author", page);
            setDraggableWithPage("storybook", page);
            setDraggableWithPage("child_name", page);
            setDraggableWithPage("progress", page);
            setDraggableWithPage("child_feedback", page);
            setDraggableWithPage("comment", page);

            // 將每一頁的icon追加拖拉的功能
            for (icon_id = 0; icon_id < num_icons_on_page[page]; ++icon_id) {
                setDraggableWithPageAndIndex("icon", page, icon_id);
            }
        }

        // 封面輸入書名時一併跟表單連動
        $("#input_book_title").on("change", function () {
            $("#book_name").val($("#input_book_title").val());
            $("#layout_book_name").val($("#input_book_title").val());
            $("#create_icon_book_name").val($("#input_book_title").val());
        });
    });

    // 編輯Layout
    let edit_layout_enabled = false;

    // 設定版面上的元件是否可以編輯
    function setLayoutEdit(edit_enabled) {

        // 封面上的元件
        setEditOneItemWithoutPage("cover_title", edit_enabled);
        setEditOneItemWithoutPage("cover_image", edit_enabled);
        setPageBottomButtonEnabled(0, edit_enabled);

        // 封面上所有icon
        for (let icon_id = 0; icon_id < num_icons_on_page[0]; ++icon_id) {
            setEditOneItemWithPageAndIndex("icon", 0, icon_id, edit_enabled);
        }

        for (page = 1; page <= <?= count($values) ?>; ++page) {

            // 各頁上的元件
            setEditOneItemWithPage("image", page, edit_enabled);
            setEditOneItemWithPage("storybook", page, edit_enabled);
            setEditOneItemWithPage("datetime", page, edit_enabled);
            setEditOneItemWithPage("author", page, edit_enabled);
            setEditOneItemWithPage("child_name", page, edit_enabled);
            setEditOneItemWithPage("progress", page, edit_enabled);
            setEditOneItemWithPage("child_feedback", page, edit_enabled);
            setEditOneItemWithPage("comment", page, edit_enabled);
            setPageBottomButtonEnabled(page, edit_enabled);

            // 各頁上所有icon
            for (let icon_id = 0; icon_id < num_icons_on_page[page]; ++icon_id) {
                setEditOneItemWithPageAndIndex("icon", page, icon_id, edit_enabled);
            }
        }
    }

    // 參考同期的翻頁效果
    $(document).ready(function () {

        updatePages();

        // 設定所有元件的大小
        let body_width = $("body").width();


        // 把所有元件的拖曳功能都先關掉
        setLayoutEdit(edit_layout_enabled);

        // 把icon選單上的所有icon排列好
//        updateIconSamples("sample_icons");

        // 等到一切都就緒了再來把白色簾幕掀開
//        $("#blank_curtain").css({ opacity: 0.0 });
//        setTimeout(() => {
//            $("#blank_curtain").attr("class", "empty");
//        }, 2000);
    });

    function sendForm(page) {
        // 如果page為空白就代表是新的一頁
        if (!page) {
            item_name_postfix = "";
        }
        // 如果page非空白就代表是要更新已經存在的某一頁
        else {
            item_name_postfix = "_" + page;
        }

        // Submit之前會確認Title是否空白
        if ($("#book_name" + item_name_postfix).val() == "") {
            alert("Must input the book's name on cover page!");
            return;
        }
        // Submit之前會確認reader是否空白
        if ($("#author" + item_name_postfix).val() == "") {
            alert("Must input reader!");
            return;
        }
        // 如果是新的一頁, submit之前檢查有沒有選好影像檔案
        if (!page) {
            if (selected_image_file.name == "") {
                alert("Must select an image!");
                return;
            }
        }
        
        // 沒問題的話就允許submit
        $("#upload_form" + item_name_postfix).submit();
    };


    // 目前的總頁數
    let max_num_pages = <?= $total_num_pages ?>;

    // 目前右邊顯示的頁數
    let current_page_num = <?= count($values) + 1 ?>;
    function updatePages() {

        // 先把所有頁面都先隱藏起來
        for (i = 0; i < max_num_pages; ++i) {
            $("#page_" + i).attr("class", "empty");
        }

        let page_width = $("body").width();
        let page_height = page_width * 60 / 42;

        // 把目前頁數那頁秀出來
        if ((current_page_num % 2) == 0) {
            $("#page_" + (current_page_num)).attr("class", "view_left_page_back_style");
        }
        else {
            $("#page_" + (current_page_num)).attr("class", "view_right_page_back_style");
        }

        $("#page_" + (current_page_num)).css({
            transform: "scale(" + page_width / 420 + "," + page_height / 600 + ")",
        });
    }

    $("#prev_page_btn").on("click", function () {
        if (current_page_num > 0) {
            current_page_num -= 1;
            updatePages();
        }
    });

    $("#next_page_btn").on("click", function () {
        if (current_page_num < max_num_pages) {
            current_page_num += 1;
            updatePages();
        }
    });
</script>
</html>
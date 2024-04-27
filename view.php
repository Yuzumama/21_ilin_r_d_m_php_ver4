<!-- Database Imformation Input-->
<?php
include 'view_common.php';

// 取得現在瀏覽的頁數
if(isset($_SESSION["current_view_page"])) {
    $current_view_page = floor(((int)$_SESSION["current_view_page"] + 1) / 2) * 2;    
}
else {
    $current_view_page = 0;
}

// HTML
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Storybook Memory Maker</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./css/view_style.css">
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
    <div class="view_item_empty_back_style">
        <svg xmlns="http://www.w3.org/2000/svg">
            <?php 
            printSvgIconDefInHtml($icons_in_db);
            ?>
        </svg>
    </div>
    <!-- 右半邊的書本編輯 -->
    <div class="view_main_back_style">
        <div class="view_book_and_buttons_style">
            <div id="book" class="view_two_pages_back_style">
                <!-- 封面的左邊插入空白空間 -->
                <div id="page_-1" class="view_empty_page_style"></div>
                <div id="page_0" class="view_right_page_back_style">
                
                    <div id="page_bottom_bar_0" class="page_bottom_bar">
                        <button id="change_cover" class="page_btn_style page_layout_btn_style"> Cover Image </button>
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
                    $book_score = 0;
                    if(isset($value["book_score"])) {
                        $book_score = (int)$value["book_score"];
                    }
                    $book_tag_1 = "";
                    $book_tag_2 = "";
                    $book_tag_3 = "";
                    if (isset($value["book_tag_1"]))    $book_tag_1 = $value["book_tag_1"];
                    if (isset($value["book_tag_2"]))    $book_tag_2 = $value["book_tag_2"];
                    if (isset($value["book_tag_3"]))    $book_tag_3 = $value["book_tag_3"];
                ?>
                <!-- 每一頁的資料庫都有設定ID, 可以根據ID更新 -->
                <!-- 不想給PHP處理的部分 -->
                <div id="page_<?=$i?>" class="<?php
                // 單數頁設在左邊，偶數頁的右邊，利用php的$i去找
                                                if(($i % 2) == 1)
                                                    echo "view_left_page_back_style";
                                                else 
                                                    echo "view_right_page_back_style";
                                                ?>">
                    <div id="record_id_<?=$i?>" hidden><?= $value['id'] ?></div>
                    <div id="page_bottom_bar_<?=$i?>" class="page_bottom_bar">
                        <button onclick="javascript: showIconMenu(<?=$i?>);" class="page_btn_style page_layout_btn_style">Add Icon</button>
                        <button onclick="javascript: resetOnePageLayout(<?= $i ?>);" class="page_btn_style page_layout_btn_style">Reset</button>
                        <button onclick="javascript: editText(<?= $i ?>, true);" class="page_btn_style page_layout_btn_style">Edit</button>
                        <button onclick="javascript: deleteOnePage(<?=$value["id"]?>)" class="page_btn_style page_layout_rightmost_btn_style">Delete</button>
                    </div>
                    <div id="update_form_back_<?=$i?>" class="empty">
                        <!--表單的語法-->
                        <form id="upload_form_<?= $i ?>" action="update_page_content.php" method="post" enctype="multipart/form-data">
                    
                            <div id="image_preview_<?= $i ?>" class="image_preview_style">
                                <img id="record_image_content_<?=$i?>" src="<?=$value["image_filename"]?>" class="view_record_image_style" />
                            </div>
                            <div class="form_one_item_back_style">
                                <input type="file" id="image_file_chooser_<?= $i ?>" name="image_file_chooser" onchange="changeImage(<?=$i?>);" hidden />
                                <input type="text" id="image_file_updated_<?= $i ?>" name="image_file_updated" value="0" hidden />
                            </div>
                            <!-- 隱藏 -->
                            <div class="empty">
                                <input type="text" name="page_id" hidden value="<?=$value["id"]?>" />
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
                                <input type="text" name="storybook" class="form_text_style" value="<?=$value["storybook_name"]?>" />
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
                            <div class="form_label_style" style="width: 75px;">
                                Book Score:
                            </div>
                            <div class="form_score_selector_style">
                                <div class="rating left" page="_<?= $i ?>">
                                    <div class="stars right">
                                        <a class="star rated"></a>
                                        <a class="star<?php if($book_score > 0) echo " rated"; ?>"></a>
                                        <a class="star<?php if($book_score > 1) echo " rated"; ?>"></a>
                                        <a class="star<?php if($book_score > 2) echo " rated"; ?>"></a>
                                        <a class="star<?php if($book_score > 3) echo " rated"; ?>"></a>
                                    </div>
                                </div>
                            </div>
                            <input id="book_score_<?= $i ?>" type="text" name="book_score" value="<?=$book_score?>" hidden/>
                        </div>
                        <div class="form_one_item_back_style">
                            <div class="form_label_style" style="width: 63px;">
                                Book Tag:
                            </div>
                            <input name="book_tag_1" type="text" list="book_tag_list" class="form_book_tag_list_style" value="<?= $book_tag_1 ?>" style="margin-right:10px;" />
                            <input name="book_tag_2" type="text" list="book_tag_list" class="form_book_tag_list_style" value="<?= $book_tag_2 ?>" style="margin-right:10px;" />
                            <input name="book_tag_3" type="text" list="book_tag_list" class="form_book_tag_list_style" value="<?= $book_tag_3 ?>" />
                            
                            <datalist name="book_tags" id="book_tag_list" >
                                <?php
                                foreach ($tag_values as $tag_value) {
                                ?>
                                <option><?= $tag_value["tag_name"] ?></option>
                                <?php
                                }
                                ?>
                            </datalist>
                        </div>         
                            <div class="form_one_item_back_style">
                                <div class="form_label_style">
                                    Comment: 
                                </div>
                                <textarea name="comments" rows="5" cols="40" class="input_comments_style"><?= $value["input_comment"]  ?></textarea>
                            </div>
                            <input type="text" name="current_view_page" value="<?= $i ?>" hidden />
                        </form>
                        <div class="form_button_back_style">
                            <button class="page_btn_style select_image_btn_style" onclick="selectImage(<?=$i?>);"> Select Image </button>
                            <button class="page_btn_style send_btn_style" onclick="sendForm(<?=$i?>);"> Update </button>
                            <button class="page_btn_style cancel_btn_style" onclick="editText(<?=$i?>, false)"> Cancel </button>
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
                <div id="page_<?=$i?>" class="<?php
                                              if(($i % 2) == 1)
                                                    echo "view_left_page_back_style";
                                                else 
                                                    echo "view_right_page_back_style";
                                              ?>">
                    <!--表單的語法-->
                    <form id="upload_form" action="write.php" method="post" enctype="multipart/form-data">
                    
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
                            <input id="book_name" type="text" name="book_name" class="form_text_style" value="<?=$book_name?>" />
                            <input id="book_id" type="text" name="book_id" value="<?=$book_id?>" />
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
                            <input type="text" name="author" class="form_text_style" value="<?=$author?>" />
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
                            <div class="form_label_style" style="width: 75px;">
                                Book Score:
                            </div>
                            <div id="form_score_selector" class="form_score_selector_style">
                                <div class="rating left" page="">
                                    <div class="stars right">
                                        <a class="star rated"></a>
                                        <a class="star"></a>
                                        <a class="star"></a>
                                        <a class="star"></a>
                                        <a class="star"></a>
                                    </div>
                                </div>
                            </div>
                            <input id="book_score" type="text" name="book_score" hidden/>
                        </div>
                        <div class="form_one_item_back_style">
                            <div class="form_label_style" style="position: relative; left: -2px;">
                                Book Tag:
                            </div>
                            <div class="form_tag_list_back_style">
                                <input name="book_tag_1" type="text" list="book_tag_list" class="form_book_tag_list_style" style="margin-right:10px;" />
                                <input name="book_tag_2" type="text" list="book_tag_list" class="form_book_tag_list_style" style="margin-right:10px;"/>
                                <input name="book_tag_3" type="text" list="book_tag_list" class="form_book_tag_list_style" />
                            </div>
                            
                            <datalist name="book_tags" id="book_tag_list" >
                                <?php 
                                foreach ($tag_values as $tag_value) {
                                ?>
                                <option><?= $tag_value["tag_name"] ?></option>
                                <?php
                                }
                                ?>
                            </datalist>
                        </div>                        
                        <div class="form_one_item_back_style">
                            <div class="form_label_style">
                                Comment: 
                            </div>
                            <textarea name="comments" rows="5" cols="40" class="input_comments_style"></textarea>
                        </div>
                        <input type="text" name="current_view_page" value="<?=$i?>" hidden />
                    </form>
                    <div class="form_button_back_style">
                        <button id="select_image" class="page_btn_style select_image_btn_style" onclick="selectImage();"> Select Image </button>
                        <button id="send_btn" class="page_btn_style send_btn_style" onclick="sendForm();"> Send </button>
                    </div>
                </div>
                <?php 
                // 把輸入表單的這一頁也算進去
                $total_num_pages++ 
                ?>

                <!-- 為了不讓表單放到最後一頁, 插入空白頁，讓它變成一本書可以合起來 -->
                <?php
                if(($i % 2) == 1){
                ?>
                <div id="page_<?=($i+1)?>" class="view_right_page_back_style">
                </div>
                <?php
                    $i++;
                
                    // 把空白頁算進去
                    $total_num_pages++;
                }
                ?>

                <!-- 封底 -->
                <div id="page_<?=($i+1)?>" class="view_left_page_back_style">
                    <div class="first_page_all_style">
                        <div class="first_page_title_style">
                        </div>
                    </div>
                </div>
                <?php 
                // 封底也算一頁
                $total_num_pages++ 
                ?>
                <!-- 封底的右邊插入空白空間 -->
                <div id="page_<?=($i+2)?>" class="view_empty_page_style"></div>
            </div>
            <div class="book_flip_page_btn_bar_style">
                <div class="prev_page_back_style">
                    <div id="prev_page_btn" class="prev_page_btn_circle_style">
                        ◀
                    </div>                
                </div>
                <div style="width: 100px;"></div>
                <input id="jump_to_page" type="text" value="<?=$current_view_page?>" class="jump_to_page_style" />
                <div style="width: 100px;"></div>
                <div class="prev_page_back_style">
                    <div id="next_page_btn" class="prev_page_btn_circle_style">
                    ▶
                    </div>
                </div>
            </div>
        </div>

        <!-- 選擇icon的表單 -->
        <div id="icon_list" target_page="" class="empty">
            <div class="icon_list_prev_icon_style">
                <div class="prev_icon_btn_style" onclick="movePrev('sample_icons');">◀</div>
            </div>
            <div class="icon_list_all_icons_style">
                <?php
                // 把db裡的icon都秀出來
                $i = 0;
                foreach ($icons_in_db as $one_icon) {
                ?>
                <div id="sample_icons_<?=$i?>" class="svg_sample_back_style" onclick="addIconToPage(current_icon_page, '<?=$one_icon["icon_name"]?>');">
                    <svg class="svg_icon_style" viewBox="<?= $one_icon["icon_x"] ?> <?= $one_icon["icon_y"] ?> <?= $one_icon["icon_width"] ?> <?=$one_icon["icon_height"]?>">
                        <use xlink:href="#<?=$one_icon["icon_name"]?>" ref_target="<?= $one_icon["icon_name"] ?>" x="0" y="0"></use>
                    </svg>
                </div>
                <?php
                $i++;
                }
                ?>
                <div id="sample_icons_new" class="svg_sample_new_button_back_style">
                    <div class="svg_sample_new_button_circle_style"></div>
                    <span class="new_svg_sample_horizontal_line_style"></span>
                    <span class="new_svg_sample_verticle_line_style"></span>
                </div>
            </div>
            <div class="icon_list_next_icon_style">
                <div class="next_icon_btn_style" onclick="moveNext('sample_icons');">▶</div>
            </div>
            <div id="icon_list_exit_btn" class="icon_list_exit_btn_style">
                <div class="icon_list_exit_btn_circle"></div>
                <div class="icon_list_exit_btn_cross_line_1"></div>
                <div class="icon_list_exit_btn_cross_line_2"></div>
            </div>
        </div>

        <!-- 4/14 新增icon的表單 -->
        <div id="create_icon_form_back" class="<?php 
                                                if($is_editting) { 
                                                    echo "create_icon_back_style";
                                                } else {
                                                    echo "empty";
                                                }?>">
            <div class="create_icon_form_back_style">
                <form id="create_icon_form" method="post" action="write_icon.php">
                    <input type="text" id="create_icon_book_id" name="book_id" value="<?= $book_id ?>" hidden />
                    <input type="text" id="create_icon_book_name" name="book_name" value="<?= $book_name ?>" hidden />
                    <input type="text" id="create_icon_author" name="author" value="<?= $author ?>" hidden />
                    <div class="create_icon_form_one_row_style">
                        <div class="create_icon_form_title_style">
                            Add your own icon!
                        </div>
                    </div>
                    <div class="create_icon_form_one_row_style">
                        <div class="create_icon_form_label_style">
                            Name
                        </div>
                        <input id="create_icon_name" type="text" name="icon_name" class="create_icon_form_text_style" />
                    </div>
                    <div class="create_icon_form_one_row_style">
                        <div class="create_icon_form_label_style">
                            Viewbox
                        </div>
                        <input id="create_icon_x" type="text" name="icon_x" class="create_icon_form_viewbox_style" value="0" />
                        <input id="create_icon_y" type="text" name="icon_y" class="create_icon_form_viewbox_style" value="0"/>
                        <input id="create_icon_width" type="text" name="icon_width" class="create_icon_form_viewbox_style" value="1024"/>
                        <input id="create_icon_height" type="text" name="icon_height" class="create_icon_form_viewbox_style" value="1024" />
                    </div>
                    <div class="create_icon_form_one_row_style">
                        <div class="create_icon_form_label_style">
                            Svg code
                        </div>
                        <textarea id="create_icon_html" name="icon_html" class="create_icon_form_textarea_style"></textarea>
                    </div>
                    <input type="text" id="create_icon_current_view_page" name="current_view_page" hidden />
                </form>
                <div class="create_icon_btn_back_style">
                    <input type="file" id="create_icon_svg_chooser" hidden />
                    <button id="create_icon_load_btn" class="create_icon_btn_style" style="margin-right: 20px;">Load Svg</button>
                    <button id="create_icon_submit_btn" class="create_icon_btn_style">Save</button>
                </div>
            </div>
            <div id="create_icon_preview" class="create_icon_preview_back_style">
                
            </div>
            <div id="create_icon_exit_btn" class="icon_list_exit_btn_style">
                <div class="icon_list_exit_btn_circle"></div>
                <div class="icon_list_exit_btn_cross_line_1"></div>
                <div class="icon_list_exit_btn_cross_line_2"></div>
            </div>
        </div>
        
        <!-- 編輯書籍的按鈕 -->
        <div class="book_buttons_back_style">
        <button id="edit_layout" class="book_btn_style"> Edit Pages </button>
        <button id="save_layout" class="book_btn_style"> Save Design </button>        
        </div>
        
        <!-- 上傳每一頁和封面封底影像的表單到Write_layout_PHP -->
        <form id="layout_form" method="post" action="write_layout.php"  enctype="multipart/form-data">
            <input type="text" id="cover_image_is_chosen" name="cover_image_is_chosen" value="0" hidden>
            <input type="file" id="cover_image_file_chooser" name="cover_image_file_chooser" hidden>
            <input id="layout_json" type="text" name="layout_json" hidden/>
            <input id="cover_layout_json" type="text" name="cover_layout_json" hidden/>
            <input type="text" id="cover_layout_current_view_page" name="current_view_page" hidden />
        </form>

        <!-- 刪除某一頁 -->
        <form id="delete_page_form" method="post" action="delete_page.php">
            <input type="text" id="delete_page_id" name="page_id" hidden />
        </form>
    </div>    
</body>

<!-- JS Library -->
<script src='//cdnjs.cloudflare.com/ajax/libs/gsap/1.18.0/TweenMax.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/gsap/1.18.2/utils/Draggable.min.js'></script>
<script src='//s3-us-west-2.amazonaws.com/s.cdpn.io/16327/MorphSVGPlugin.min.js?r=185'></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script> 
<script src="./js/set_item_draggable.js"></script>
<script src="./js/pc_or_mobile.js"></script>
<script src="./js/left_bar_functions.js"></script>

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

    // reader的名稱有更改的話，表單上面的內容也會跟著變更
    $("#author").on("change", function () {
        $("#book_author").val($("#author").val());
    });

    // 各頁面上的icon的數量
    let num_icons_on_page = Array(<?=(count($values) + 1)?>);
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
    num_icons_on_page[<?=$i?>] = <?= $num_icons ?>;
    <?php
    $i++;
    }
    ?>

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
            '<?=json_encode($cover_title_layout)?>',
            "cover_image",
            '<img id="cover_image_item" src="<?=$first_page_image?>" class="view_record_cover_image_style" />'
        );
        <?php

        // 如果使用者沒有上傳過封面照片那就把封面照片的物件隱藏起來
        if(!$first_page_image) {
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
            "<?=$cover_title_html_content?>"
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
        var svgContent = createSvgContent("use_icon_0_<?= $icon_id ?>", "<?=$icon_name?>");
        addOneDraggableItemWithPageAndIndex("page_0", '<?= $icon_layout_str ?>', "icon", 0, <?= $icon_id ?>, svgContent);

        <?php
                ++$icon_id;
            }
        ?>
        num_icons_on_page[0] = <?=$cover_title_layout["icons"]["num_icons"]?>;
        <?php
        }
        else {
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
            }
            else {
                $page_layout_str = "{}";
            }
            ?>

            // 生成第i頁上的元件
            addOneDraggableItemWithPage("page_<?=$i?>", '<?=$page_layout_str?>', "image",          <?=$i?>, '<img id="record_image_content_' + (<?=$i?>) + '" src="<?=$value["image_filename"]?>" class="view_record_image_style" />');
            addOneDraggableItemWithPage("page_<?=$i?>", '<?=$page_layout_str?>', "datetime",       <?=$i?>, '<?= DateTime::createFromFormat('Y-m-d H:i:s', $value["input_date"])->format('Y/m/d H:i') ?>');
            addOneDraggableItemWithPage("page_<?=$i?>", '<?=$page_layout_str?>', "author",         <?=$i?>, '<?= $value["storyteller"] ?>');
            <?php if(isset($value["storybook_name"])) { ?>
            addOneDraggableItemWithPage("page_<?=$i?>", '<?=$page_layout_str?>', "storybook",      <?=$i?>, '<?= $value["storybook_name"] ?>');
            <?php } if(isset($value["child_name"])) { ?>
            addOneDraggableItemWithPage("page_<?=$i?>", '<?=$page_layout_str?>', "child_name",     <?=$i?>, '<?= $value["child_name"] ?>');
            <?php } if(isset($value["progress"])) { ?>
            addOneDraggableItemWithPage("page_<?=$i?>", '<?=$page_layout_str?>', "progress",       <?=$i?>, '<?= $value["progress"] ?>');
            <?php } if(isset($value["child_feedback"])) { ?>
            addOneDraggableItemWithPage("page_<?=$i?>", '<?=$page_layout_str?>', "child_feedback", <?=$i?>, '<?= $value["child_feedback"] ?>');
            <?php } if(isset($value["input_comment"])) { ?>
            addOneDraggableItemWithPage("page_<?=$i?>", '<?=$page_layout_str?>', "comment",        <?=$i?>, "<?= $value["input_comment"] ?>");
            <?php } ?>

            // 把文字設在最上層
            $("#record_datetime_<?=$i?>").css({ "z-index": 2 });
            $("#record_author_<?=$i?>").css({ "z-index": 2 });
            $("#record_storybook_<?=$i?>").css({ "z-index": 2 });
            $("#record_child_name_<?=$i?>").css({ "z-index": 2 });
            $("#record_progress_<?=$i?>").css({ "z-index": 2 });
            $("#record_child_feedback_<?=$i?>").css({ "z-index": 2 });
            $("#record_comment_<?=$i?>").css({ "z-index": 2 });

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

                var svgContent = createSvgContent("use_icon_<?= $i ?>_<?= $icon_id ?>", "<?=$icon_name?>");
                addOneDraggableItemWithPageAndIndex("page_<?= $i ?>", '<?= $icon_layout_str ?>', "icon", <?= $i ?>, <?= $icon_id ?>, svgContent, true);

                <?php
                ++$icon_id;
                }
                ?>
                num_icons_on_page[<?= $i ?>] = <?= $page_layout["icons"]["num_icons"] ?>;
        <?php
            }
            else {
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

    // 參考同期的翻頁效果
    $(document).ready(function () {
//        $('#book').turn({
//            width: 840,
//            height: 600,
//            autoCenter: true
        //        });

        // 設定 score 的星星
        $('.rating .star').hover(function() {
            $(this).addClass('to_rate');
            $(this).parent().find('.star:lt(' + $(this).index() + ')').addClass('to_rate');
            $(this).parent().find('.star:gt(' + $(this).index() + ')').addClass('no_to_rate');
        }).mouseout(function() {
            $(this).parent().find('.star').removeClass('to_rate');
            $(this).parent().find('.star:gt(' + $(this).index() + ')').removeClass('no_to_rate');
        }).click(function() {
            $(this).removeClass('to_rate').addClass('rated');
            $(this).parent().find('.star:lt(' + $(this).index() + ')').removeClass('to_rate').addClass('rated');
            $(this).parent().find('.star:gt(' + $(this).index() + ')').removeClass('no_to_rate').removeClass('rated');
            /*Save your rate*/

            $("#book_score" + $(this).parent().parent().attr("page")).val($(this).index());
        });

        updatePages();

        // 把所有元件的拖曳功能都先關掉
        setLayoutEdit(edit_layout_enabled);

        // 把icon選單上的所有icon排列好
        updateIconSamples("sample_icons");

        // 等到一切都就緒了再來把白色簾幕掀開
        $("#blank_curtain").css({ opacity: 0.0 });
        setTimeout(() => {
            $("#blank_curtain").attr("class", "empty");
        }, 2000);
    });

    // 把每一個元件的X, Y座標的旋轉角度和縮放比例，轉成JSON格式，才能透過PHP存到DB去
    function createOneItemLayoutJson(pos_item_name, rot_item_name) {
        let pos_item = $("#"+pos_item_name);
        let rot_item = $("#"+rot_item_name);

        let item_left = pos_item.css("left");
        if (!item_left) item_left = "0px";

        let item_top = pos_item.css("top");
        if (!item_top) item_top = "0px";

        // 角度
        let item_degree = rot_item.attr("degree");
        if (!item_degree) item_degree = 0;

        // 縮放比例
        let item_scale = rot_item.attr("scale");
        if (!item_scale) item_scale = 1.0;

        // 存成json格式
        let item_json = {
            left: item_left,
            top: item_top,
            degree: item_degree,
            scale: item_scale,
        };

        return item_json;
    }

    function createOneItemJsonWithPage(item_name, page) {
        let pos_item_name = "record_" + item_name + '_' + page;
        let rot_item_name = "record_" + item_name + "_rotateable_" + page;

        return createOneItemLayoutJson(pos_item_name, rot_item_name);
    }

    function createOneItemJsonWithPageAndIndex(item_name, page, index) {
        let pos_item_name = "record_" + item_name + '_' + page + '_' + index;
        let rot_item_name = "record_" + item_name + "_rotateable_" + page + '_' + index;

        return createOneItemLayoutJson(pos_item_name, rot_item_name);
    }

    function createOneItemJsonWithoutPage(item_name) {
        let pos_item_name = "record_" + item_name;
        let rot_item_name = "record_" + item_name + "_rotateable";

        return createOneItemLayoutJson(pos_item_name, rot_item_name);
    }

    // 把每一頁的縮放、角度等變數存成json格式，僅存在Client端
    $("#save_layout").on("click", function () {

        // Submit之前會確認Title是否空白
        if ($("#layout_book_name").val() == "") {
            alert("Must input the book's name on cover page!");
            return;
        }
        // Submit之前會確認reader是否空白
        if ($("#layout_author").val() == "") {
            alert("Must input author!");
            return;
        }

        let all_pages_json = [];
        let one_page_json = null;

        for (page = 1; page <= <?=count($values)?> ; ++page) {

            one_page_json = {
                id: parseInt($("#record_id_" + page).text()),
                layout: {
                    image: createOneItemJsonWithPage("image", page),
                    datetime: createOneItemJsonWithPage("datetime", page),
                    author: createOneItemJsonWithPage("author", page),
                    storybook: createOneItemJsonWithPage("storybook", page),
                    child_name: createOneItemJsonWithPage("child_name", page),
                    progress: createOneItemJsonWithPage("progress", page),
                    child_feedback: createOneItemJsonWithPage("child_feedback", page),
                    comment: createOneItemJsonWithPage("comment", page),
                    icons: {
                        num_icons: num_icons_on_page[page],
                        icon_layout: {},
                    },
                },
            };
            // 追加每一頁裡的icon到json
            for (icon_id = 0; icon_id < num_icons_on_page[page]; ++icon_id) {
                let icon_name = "icon_" + page + "_" + icon_id;
                let icon_json = {};
                icon_json["icon_name"] = $("#use_" + icon_name).attr("ref_target");
                icon_json["icon"] = createOneItemJsonWithPageAndIndex("icon", page, icon_id);
                one_page_json["layout"]["icons"]["icon_layout"][icon_name] = icon_json;
            }


            // 每一頁處理完的layout的資料，統整成整本書的內容(Client端)
            all_pages_json.push(one_page_json);
        }

        // 把封面的版面設計轉成json
        let cover_layout_json = {
            first_page_layout: {
                cover_title: createOneItemJsonWithoutPage("cover_title"),
                cover_image: createOneItemJsonWithoutPage("cover_image"),
                icons: {
                    num_icons: num_icons_on_page[0],
                    icon_layout: {},
                },
            },
        };
        // 把封面的icon追加到json裡去
        for (icon_id = 0; icon_id < num_icons_on_page[0]; ++icon_id) {
            let icon_name = "icon_0_" + icon_id;
            let icon_json = {};
            icon_json["icon_name"] = $("#use_" + icon_name).attr("ref_target");
            icon_json["icon"] = createOneItemJsonWithPageAndIndex("icon", 0, icon_id);
            cover_layout_json["first_page_layout"]["icons"]["icon_layout"][icon_name] = icon_json;
        }
//        for (icon_id = 0; icon_id < num_icons_on_page[0]; ++icon_id) {
//            cover_layout_json["first_page_layout"]["icons"]["icon_layout"]["icon_0_" + icon_id] = createOneItemJsonWithPageAndIndex("icon", 0, icon_id);
//        }

        // 把json轉成文字，透過form將文字資料傳到DB去，因為DB上面只能儲存文字
        all_pages_json_str = JSON.stringify(all_pages_json);
        cover_pages_json_str = JSON.stringify(cover_layout_json);

        // 把文字填入表單
        $("#layout_json").val(all_pages_json_str);
        $("#cover_layout_json").val(cover_pages_json_str);

        // 把現在看的頁數設定好等下回來才能夠回到原本看的這一頁
        $("#cover_layout_current_view_page").val(
            $("#jump_to_page").val());

        // Line 346 表單Submit出去
        $("#layout_form").submit();
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

        for (page = 1; page <= <?=count($values)?>; ++page) {

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

    // 編輯Layout的按鈕功能設定
    $("#edit_layout").on("click", function () {
        edit_layout_enabled = !edit_layout_enabled;
        if (edit_layout_enabled) {
            $("#edit_layout").text("Stop Edit");
        }
        else {
            $("#edit_layout").text("Edit Page");
        }
        setLayoutEdit(edit_layout_enabled);
    });

    // Chat風 等同Select Image功能，只適用在封面
    let selected_cover_file = new File([""], "");

    $("#cover_image_file_chooser").on("change", function () {
        selected_cover_file = this.files[0];

        let reader = new FileReader();
        reader.onloadend = function () {

            // 如果因為之前沒有上傳過封面影像而把封面影像物件隱藏起來的話就把封面影像再秀出來
            let no_empty_class = $("#record_cover_image").attr("class").replace("empty ", "");
            $("#record_cover_image").attr("class", no_empty_class);

            // 把img物件的影像網址設為新的
            $("#cover_image_item").attr("src", reader.result);
            $("#cover_image_is_chosen").val("1");
        };
        reader.readAsDataURL(selected_cover_file);
    })

    $("#change_cover").on("click", function () {
        $("#cover_image_file_chooser").click();
    });

    // 把每一個元件的X, Y座標的旋轉角度和縮放比例都還原成預設值
    function resetOneItemLayout(pos_item_name, rot_item_name, item_name) {
        let pos_item = $("#"+pos_item_name);
        let rot_item = $("#"+rot_item_name);

        pos_item.css("left", item_default_layout_json[item_name]["left"]);
        pos_item.css("top" , item_default_layout_json[item_name]["top"]);

        // 角度
        $("#"+rot_item_name).attr('degree', 0);

        // 縮放比例
        $("#"+rot_item_name).attr('scale', 1.0);

        // 用CSS控制大小和旋轉方向
        var rotateCSS = 'rotate(0deg) scale(1.0)';
        $("#"+rot_item_name).css({
            '-moz-transform': rotateCSS,
            '-webkit-transform': rotateCSS
        });
    }

    function resetOneItemLayoutWithPage(item_name, page) {
        let pos_item_name = "record_" + item_name + '_' + page;
        let rot_item_name = "record_" + item_name + "_rotateable_" + page;

        resetOneItemLayout(pos_item_name, rot_item_name, item_name);
    }

    function resetOneItemLayoutWithoutPage(item_name) {
        let pos_item_name = "record_" + item_name;
        let rot_item_name = "record_" + item_name + "_rotateable";

        resetOneItemLayout(pos_item_name, rot_item_name, item_name);
    }

    // 每一頁是否在編輯狀態
    let page_on_edit = new Array(<?=count($values) + 1?>).fill(false);

    // 秀出指定頁面的修改表單
    function editText(page, is_editting) {
        if (page) {

            // 紀錄該頁面的編輯狀態
            page_on_edit[page] = is_editting;

            // 如果是在編輯狀態則秀出編輯畫面
            if (is_editting) {
                if ((page % 2) == 1) {
                    $("#update_form_back_" + page).attr("class", "view_left_page_update_form_back_style");
                }
                else {
                    $("#update_form_back_" + page).attr("class", "view_right_page_update_form_back_style");
                }
            }

            // 如果是非編輯狀態則隱藏編輯畫面
            else {
                $("#update_form_back_" + page).attr("class", "empty");
            }
        }
    }

    // 把指定頁的版面設定還原為預設值
    function resetOnePageLayout(page) {

        // 再次確認是否要把設定還原為預設值
        if (!confirm("Are you sure to reset the design of page " + page + "?")) {
            return;
        }

        // 確認是否要移除所有icon
        let remove_icons = confirm("Do you want to remove all icons?");

        // 封面的版面設定
        if (page == 0) {
            resetOneItemLayoutWithoutPage("cover_title");
            resetOneItemLayoutWithoutPage("cover_image");
        }

        // 內頁的版面設定
        else {
            resetOneItemLayoutWithPage("image", page);
            resetOneItemLayoutWithPage("datetime", page);
            resetOneItemLayoutWithPage("author", page);
            resetOneItemLayoutWithPage("storybook", page);
            resetOneItemLayoutWithPage("child_name", page);
            resetOneItemLayoutWithPage("progress", page);
            resetOneItemLayoutWithPage("child_feedback", page);
            resetOneItemLayoutWithPage("comment", page);
        }

        if (remove_icons) {
            // 移除掉所有的icon
            for (icon_id = 0; icon_id < num_icons_on_page[page]; icon_id++) {
                let icon_item_name = "record_icon_" + page + "_" + icon_id;
                $("#" + icon_item_name).remove();
            }
            num_icons_on_page[page] = 0;
        }
    }

    // 把指定的頁面刪除
    function deleteOnePage(page_id) {
        if (confirm("Are you sure to delete this page?")) {
            $("#delete_page_id").val(page_id);
            $("#delete_page_form").submit();
        }
    }

    

    // 生成指定的svg icon html語法
    function createSvgContent(svg_item_id, svg_ref_id) {
        return '<svg class="svg_icon_style" viewBox="' + $("#" + svg_ref_id).attr("bounding-box") + '">' +
               '<use id="' + svg_item_id + '" xlink:href="#' + svg_ref_id + '" ref_target="' + svg_ref_id + '" x="0" y="0"></use>' +
               '</svg>';
    }

    // 追加指定的icon到指定頁面
    function addIconToPage(page, icon_name) {
        const icon_id = num_icons_on_page[page];
        ++num_icons_on_page[page];

        let svgContent = createSvgContent('use_icon_' + page + '_' + icon_id, icon_name);        

        // 生成icon
        addOneDraggableItemWithPageAndIndex("page_" + page, '{}', "icon", page, icon_id, svgContent, true);

        // Icon生成後將其擺在預設位置的最左上角
        $("#record_icon_" + page + "_" + icon_id).css({
            left: "100px",
            top: "100px",
        });

        // 設定icon
        setDraggableWithPageAndIndex("icon", page, icon_id);

        // 啟動icon的拖拉功能
        setEditOneItemWithPageAndIndex("icon", page, icon_id, edit_layout_enabled);

        // 把icon列表隱藏起來
        showIconMenu(-1);
    }

    // 把新增icon的視窗秀出來
    let show_icon_list = false;
    let current_icon_page = 0;
    function showIconMenu(page) {
        current_icon_page = page;

        if (page == -1) {
            show_icon_list = false;
        } else {
            show_icon_list = !show_icon_list;
        }

        if (show_icon_list) {
            $("#icon_list").attr("taget_page", page);
            $("#icon_list").attr("class", "icon_list_back_style");
            $("#create_icon_form_back").attr("class", "empty");
        }
        else {
            $("#icon_list").attr("class", "empty");
        }
    }

    // 從鋼琴作業裡copy過來的
    let num_items_per_column = [];
    num_items_per_column['sample_icons'] = 3;

    function updateIconSamples(icon_prefix) {
        {
            let x_pos = Math.floor((0 - currIconSampleIndex) / num_items_per_column[icon_prefix]);
            let y_pos = 0;
            $("#" + icon_prefix + "_new").css({
                left: (20 + x_pos * 130) + "px",
                top: (60 + y_pos * 120) + "px",
            });
        }
        for (let i = 0; ; ++i) {
            let icon_id = icon_prefix + "_" + i;
            if ($("#" + icon_id).length) {
                let icon_btn = $("#" + icon_id);

                let x_pos = Math.floor(((i + 1) - currIconSampleIndex) / num_items_per_column[icon_prefix]);
                let y_pos = (i + 1) % num_items_per_column[icon_prefix];

                icon_btn.css({
                    left: (20 + x_pos * 130) + "px",
                    top: (60 + y_pos * 120) + "px",
                });
            }
            else {
                break;
            }
        }
    }

    let currIconSampleIndex = 0;
    function movePrev(icon_prefix) {
        if (currIconSampleIndex > 0) {
            currIconSampleIndex-= num_items_per_column[icon_prefix];
            updateIconSamples(icon_prefix);
        }
    }

    let iconSampleListLength = [];
    iconSampleListLength['sample_icons'] = <?= count($icons_in_db) ?>;
    function moveNext(icon_prefix) {
        
        if (currIconSampleIndex < iconSampleListLength[icon_prefix] - 11) {
            currIconSampleIndex += num_items_per_column[icon_prefix];
            updateIconSamples(icon_prefix);
        }
    }

    $("#icon_list_exit_btn").on("click", function(){
        // 把icon列表隱藏起來
        showIconMenu(-1);
    });

    $("#sample_icons_new").on("click", function () {

        // 把icon列表隱藏起來
        showIconMenu(-1);

        // 把生成icon的表單秀出來
        $("#create_icon_form_back").attr("class", "create_icon_back_style");
    });

    function showPreviewSvgIcon() {

        // 檢查svg html code已填寫, 如果svg html code是空白就不動作
        if ($("#create_icon_html").val() == "") {
            return;
        }

        // 檢查viewBox是否已經填寫, 如果是空白就用預設值(0 0 1024 1024)
        let icon_x = 0;
        if ($("#create_icon_x").val() != "") {
            icon_x = parseInt($("#create_icon_x").val());
        }

        let icon_y = 0;
        if ($("#create_icon_y").val() != "") {
            icon_y = parseInt($("#create_icon_y").val());
        }

        let icon_w = 1024;
        if ($("#create_icon_width").val() != "") {
            icon_w = parseInt($("#create_icon_width").val());
        }

        let icon_h = 1024;
        if ($("#create_icon_height").val() != "") {
            icon_h = parseInt($("#create_icon_height").val());
        }
        

        // 刪掉之前preview的結果
        $("#create_icon_preview").empty();

        // 按照使用者輸入的資訊生成新的svg
        let svg_viewbox = icon_x + " " + icon_y + " " + icon_w + " " + icon_h;

        // 追加到create_icon_preview底下
        let svg_content = 
            '<svg class="create_icon_preview_svg_style" viewBox="' + svg_viewbox + '">' +
                $("#create_icon_html").val() +
            '</svg>';

        $("#create_icon_preview").append(svg_content);
    };

    // 追加icon表單上的各個欄位如果有變動就會去重新調整icon preview的結果
    $("#create_icon_x").on("change", function () {
        if (isNaN(parseInt($("#create_icon_x").val())) && $("#create_icon_x").val() != "") {
            alert("Icon viewbox must be integer!");
            $("#create_icon_x").val("");
            return;
        }
        showPreviewSvgIcon();
    })

    // 追加icon表單上的各個欄位如果有變動就會去重新調整icon preview的結果
    $("#create_icon_y").on("change", function () {
        if (isNaN(parseInt($("#create_icon_y").val())) && $("#create_icon_y").val() != "") {
            alert("Icon viewbox must be integer!");
            $("#create_icon_y").val("");
            return;
        }
        showPreviewSvgIcon();
    })

    // 追加icon表單上的各個欄位如果有變動就會去重新調整icon preview的結果
    $("#create_icon_width").on("change", function () {
        if (isNaN(parseInt($("#create_icon_width").val())) && $("#create_icon_width").val() != "") {
            alert("Icon viewbox must be integer!");
            $("#create_icon_width").val("");
            return;
        }
        showPreviewSvgIcon();
    })

    // 追加icon表單上的各個欄位如果有變動就會去重新調整icon preview的結果
    $("#create_icon_height").on("change", function () {
        if (isNaN(parseInt($("#create_icon_height").val())) && $("#create_icon_height").val() != "") {
            alert("Icon viewbox must be integer!");
            $("#create_icon_height").val("");
            return;
        }
        showPreviewSvgIcon();
    })

    // 追加icon表單上的各個欄位如果有變動就會去重新調整icon preview的結果
    $("#create_icon_html").on("change", function () {
        showPreviewSvgIcon();
    })

    // Save icon的按鈕
    $("#create_icon_submit_btn").on("click", function () {

        // 先檢查是不是有空白的欄位, 如果有就跳錯誤訊息
        if ($("#create_icon_name").val() == "") {
            alert("Must input icon's name!")
            return;
        }

        if ($("#create_icon_x").val() == "") {
            alert("Must input icon's x!")
            return;
        }
        if ($("#create_icon_y").val() == "") {
            alert("Must input icon's y!")
            return;
        }
        if ($("#create_icon_width").val() == "") {
            alert("Must input icon's width!")
            return;
        }
        if ($("#create_icon_height").val() == "") {
            alert("Must input icon's height!")
            return;
        }

        if ($("#create_icon_html").val() == "") {
            alert("Must input icon's svg code!")
            return;
        }

        // 把現在看的頁數設定好等下回來才能夠回到原本看的這一頁
        $("#create_icon_current_view_page").val(
            $("#jump_to_page").val());

        // 如果所有欄位都已經填完了就把表單送出
        $("#create_icon_form").submit();
    });

    // 離開製作icon的表單
    $("#create_icon_exit_btn").on("click", function () {
        $("#create_icon_form_back").attr("class", "empty");
    });

    // 點選load svg按鈕
    $("#create_icon_load_btn").on("click", function() {
        $("#create_icon_svg_chooser").click();
    });

    // 當選取好svg icon檔案時進行下面動作
    $("#create_icon_svg_chooser").on("change", function(){
        var fr = new FileReader();

        // 檔案讀完後的動作
        fr.onload = function() {
            var svg_text = fr.result;

            // 從讀進來的文字檔的內容裡抓取viewbox
            var viewbox_str = svg_text.split(/[vV][iI][eE][wW][bB][oO][xX]=\"/)[1].split("\"")[0];
            
            // 把viewbox的參數用空白分開
            var viewbox_values = viewbox_str.split(" ");

            // 從讀進來的文字檔的內容裡抓取svg內容
            var svg_str = svg_text.split(/<svg[^>]*>/)[1].split(/<\/svg>/)[0];

            // 移除不需要的code
            svg_str=svg_str.replace("<title\/>", "");

            // 把取得的viewbox參數放到對應的input裡
            $("#create_icon_x").val(viewbox_values[0]);
            $("#create_icon_y").val(viewbox_values[1]);
            $("#create_icon_width").val(viewbox_values[2]);
            $("#create_icon_height").val(viewbox_values[3]);

            // 把取得的svg內容放到textarea裡
            $("#create_icon_html").val(svg_str);

            // 秀出svg的preview
            showPreviewSvgIcon();
        }

        // 用文字格式把檔案讀進來
        fr.readAsText(this.files[0]);
    });

    // 目前的總頁數
    let max_num_pages = <?= $total_num_pages ?>;

    // 目前右邊顯示的頁數
    let current_page_num = <?= $current_view_page ?>;
    function updatePages() {

        // 先把所有頁面都先隱藏起來
        for (i = -1; i <= max_num_pages; ++i) {
            $("#page_" + i).attr("class", "empty");
        }

        // 把目前頁數的左邊那頁秀出來
        if (current_page_num - 1 < 0) {
            // -1 頁的話就是秀出封面左邊的空白空間
            $("#page_" + (current_page_num - 1)).attr("class", "view_empty_page_style");
        }
        else {
            $("#page_" + (current_page_num - 1)).attr("class", "view_left_page_back_style");
        }

        // 把目前頁數的右邊那頁秀出來
        if (current_page_num >= max_num_pages) {
            $("#page_" + (current_page_num)).attr("class", "view_empty_page_style");
        } else {
            $("#page_" + (current_page_num)).attr("class", "view_right_page_back_style");
        }
    }

    function changePage(target_page, is_directly_input_by_user) {
        if (target_page < 0) {
            target_page = 0;
        }
        else if (target_page >= max_num_pages) {
            target_page = max_num_pages - 1;
        }

        // 記得 current_page_num 代表的是右邊那一頁, 每次都是秀 2 頁出來
        // 封面的情況下
        current_page_num = Math.floor((target_page + 1) / 2) * 2;

        // 更新書頁
        updatePages();

        // 如果不是使用者自己指定要跳到這一頁的話就記得要更新目前頁數
        if (!is_directly_input_by_user) {
            $("#jump_to_page").val(current_page_num);
        }
    }

    $("#jump_to_page").on("change", function () {
        let target_page = parseInt($("#jump_to_page").val());

        // 檢查使用者是否輸入了正確的數字
        if (target_page === Infinity || String(target_page) !== $("#jump_to_page").val()) {
            alert("The page must be a number!");
            return;
        }

        if (target_page < 0) {
            target_page = 0;
            $("#jump_to_page").val(target_page);
        }
        else if (target_page >= max_num_pages) {
            target_page = max_num_pages - 1;
            $("#jump_to_page").val(target_page);
        }

        changePage(target_page, true);
    });

    $("#prev_page_btn").on("click", function () {
        changePage(current_page_num - 2);
    });

    $("#next_page_btn").on("click", function () {
        changePage(current_page_num + 2);
    });
</script>

</html>
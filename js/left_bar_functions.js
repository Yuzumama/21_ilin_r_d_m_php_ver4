// 登出帳號, 回到登入畫面的index.php
$("#btn_logout").on("click", function () {
    if (confirm("Are you sure to log-out?")) {
        window.location.href = "logout.php";
    }
});

$("#btn_ai_recommend").on("click", function() {
    getDevicePage("try_recommendation.php", true);
});

$("#btn_bookshelf").on("click", function(){
    getDevicePage("bookshelf.php", true);
});

$("#btn_service").on("click", function() {
    $("#left_bar_main").css({
        left: "-230px",
    });
    $("#left_bar_service").css({
        left: "20px",
    });
});

$("#btn_see_my_trend").on("click", function () {
    getDevicePage("see_trend.php", true);
})

function backtoMain(originalBarId) {
    $("#" + originalBarId).css({
        left: "270px",
    });
    $("#left_bar_main").css({
        left: "20px",
    });
}
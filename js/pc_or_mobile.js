// Detect whether the user is from pc or mobile
function isMobile() {
    const regex = /Mobi|Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i;
    return regex.test(navigator.userAgent);
//    return true;
}

function getDevicePage(page_file_name, move_to_page) {

    target_page = page_file_name;

    // If mobile, then asking whether to move to mobile version
    if (isMobile()) {
//        if (confirm("Do you want to move to mobile page?")) 
        {
            target_page = "./mobile/" + page_file_name;
        }
    }

    if(move_to_page) {
        // Check whether current page is the same page
        if(!window.location.href.includes(target_page)) {
            window.location.href = target_page;
        }
    }
    else {
        return target_page;
    }
}
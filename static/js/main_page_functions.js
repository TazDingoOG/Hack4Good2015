function request_for_qr_and_url(refugee_camp_name) {
    //TODO: request qr
}

function check_for_refugee_camp(refugee_camp_name) {
    var submitbutton = document.getElementById("search_button");

    if (refugee_camp_name == "NotMoabit") {
        no_refugee_camps_found(submitbutton);

    } else {
        send_read_only_file_from(refugee_camp_name);
    }
}

function no_refugee_camps_found(submitbutton) {
    // if not found delete input
    submitbutton.value = '';
    var div = document.createElement("div");
    div.className = "not_found_div";
    not_found_div.textContent = "nothing was found";
    document.body.tfheader.appendChild(not_found_div);
}

function send_refugee_camps() {

}

function send_read_only_file_from(refugee_camp) {
// add read only file with needed items
    var div = document.createElement("div");
    div.className = "read_only_div";
    div.id = "read_only_div";

    var elem = document.createElement("img");
    elem.setAttribute("src", "get_picture_from_server.png");
    elem.setAttribute("height", "768");
    elem.setAttribute("width", "1024");
    elem.setAttribute("alt", "Flower");
    document.getElementById("read_only_div").appendChild(elem);

    document.body.tfheader.appendChild(read_only_div);
}

// donutplanet.de
// user name tazdingo pw hack4good!

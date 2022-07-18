var conn = new WebSocket(wsCurEnv);
conn.onopen = function (e) {
    conn.send(JSON.stringify({
        command: "attachAccount",
        id_group: id_company,
        role: 2
    }));
};
conn.onmessage = function (e) {
    var dt = jQuery.parseJSON(e.data);
    if (dt.status == 200) {
        if (dt.action == "notification") {
            if (dt.type == 1) {
                $('#notifications').prepend(`<a href="javascript:void(0)"> <div class="btn btn-danger btn-circle"><i class="fas fa-exclamation"></i></div><div class="mail-contnet"><h5>${complaint}</h5> <span class="mail-desc">${dt.receiver_name} ${reportedby} ${dt.sender_name}</span> <span class="time">${dt.date}</span></div></a>`);
            } else if (dt.type == 2) {
                $('#notifications').prepend(`<a href="javascript:void(0)"> <div class="btn btn-danger btn-circle"><i class="fas fa-exclamation"></i></div><div class="mail-contnet"><h5>${late}</h5> <span class="mail-desc">${dt.sender_name} ${waitingfor} ${dt.receiver_name}</span> <span class="time">${dt.date}</span></div></a>`);
                $(".notify_" + dt.sender).css("display", "block");
                $(".point_" + dt.sender).css("display", "block");
            }
            $(".notify").css("display", "block");
            $(".heartbit").css("display", "block");
            $(".point").css("display", "block");
        }
    }
}
function deleteNotif() {
    $.ajax({
        url: "functions_ajax.php",
        type: "POST",
        data: {
            type: "seen",
            id: id_account
        },
        success: function(data) {
            if (data == 1) {
                $("#notify").css("display", "none");
                $("#heartbit").css("display", "none");
                $("#point").css("display", "none");
                $('.notSeen').removeClass('notSeen');
            }
        }
    });
};
if (seen != '') {
    $(".notify").css("display", "block");
    $(".heartbit").css("display", "block");
    $(".point").css("display", "block");
}
$(document).click(function (e) {
    if (!navigator.onLine) {
        Swal.fire({
            type: 'error',
            title: 'Please verify your internet connection !'
        })
        e.preventDefault();
    }

    /*             if (conn.readyState === WebSocket.CLOSED) {
                    swalWithBootstrapButtons.fire({
                        title: 'connection closed !',
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonText: 'Reconnect',
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#overlay').show();
                            var conn = new WebSocket(wsCurEnv);
                            conn.onopen = function(e) {
                                conn.send(JSON.stringify({
                                    command: "attachAccount",
                                    account: <?= $_SESSION['id_user'] ?>,
                                    id_group: <?= $_SESSION['id_company'] ?>,
                                    role: 2
                                }));
                            };
                            setTimeout(function() {
                                if (conn.readyState === WebSocket.OPEN) {
                                    Swal.fire({
                                        type: 'success',
                                        title: 'Connected',
                                        showCancelButton: true
                                    })
                                } else {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Failed to connect !',
                                        showCancelButton: true
                                    })
                                }
                            }, 2000);
                        }
                    })
                } */
});
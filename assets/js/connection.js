var conn = new WebSocket(wsCurEnv);
conn.onopen = function(e) {
    conn.send(JSON.stringify({
        command: "attachAccount",
        id_group: id_company,
        role: 2
    }));
};
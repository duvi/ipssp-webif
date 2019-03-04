function show_area(command, station, area_id) {
    $.ajax({
        url: "res/compare.php",
        type: "POST",
        data: ({command: command, sta: station}),
        dataType: "json",
        async: false,
        success: function(data) {
            if (data.area_id != area_id) {
                area_id = data.area_id;
                $("#nav-area_" + area_id + "-tab").tab("show");
            }
        }
    });

    setTimeout(show_area, 1000, command, station, area_id);
}

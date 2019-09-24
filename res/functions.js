function point_it(event) {
    var pos_x = event.offsetX?(event.offsetX):event.layerX-document.getElementById("record_map_div").offsetLeft;
    var pos_y = event.offsetY?(event.offsetY):event.layerY-document.getElementById("record_map_div").offsetTop;
    document.getElementById("cross").style.left = (pos_x-5);
    document.getElementById("cross").style.top = (pos_y-5);
    document.getElementById("cross").style.visibility = "visible";
    document.getElementById("rec_button").style.visibility = "visible";
    document.getElementById("rec_text").style.visibility = "hidden";
    document.pointform.form_x.value = pos_x;
    document.pointform.form_y.value = pos_y;
}

function blink() {
    var e = document.getElementById("blink_img");
    e.style.visibility = ( e.style.visibility == 'visible' )? 'hidden' : 'visible';
    setTimeout(blink, 500);
 }

function seconds() {
    var e = document.getElementById('seconds');
    var value = parseInt(e.textContent) + 1;
    e.innerHTML = value;
    setTimeout(seconds, 1000);
}

function clearCanvas(name) {
    var canvas = document.getElementById(name);
    var ctx = canvas.getContext("2d");
    ctx.clearRect (0, 0, canvas.width, canvas.height);
}

function clearAreas(name) {
    $(document.getElementById(name).getElementsByClassName("area")).attr("fill-opacity","0");
}

function locate() {
    var canvas = document.getElementById("locate_canvas");
    var ctx = canvas.getContext("2d");
    var station_select = document.locate_form.station.value;
    var stations = document.locate_form.getElementsByClassName('item');
    var station;

    document.getElementById("locate_punkt").innerHTML = "";
    document.getElementById("locate_message").innerHTML = "";
    ctx.clearRect (0, 0, canvas.width, canvas.height);
    clearAreas("locate_svg");

    if (!document.locate_form.command || !stations.length) {
        document.getElementById("locate_message").innerHTML = "Station or command list empty";
        locate_timer = setTimeout(locate, 1000);
        return;
    }

    for (var i=0; i < document.locate_form.command.length; i++) {
        if (document.locate_form.command[i].checked) {
            var command = document.locate_form.command[i].value;
        }
    }

    for (var j=0; j < stations.length; j++) {
        if (stations[j].checked || stations[j].value == station_select) {
            station = stations[j].value;
            $.ajax({
                url: "res/compare.php",
                type: "POST",
                data: ({command: command, sta: station}),
                dataType: "json",
                async: false,
                success: function(data) {
                    if (stations[j].checked && data.x) {
                        ctx.beginPath();
                        ctx.arc(data.x, data.y, 6, 0, Math.PI*2, true);
                        ctx.stroke();
                        ctx.fillStyle = "rgba("+data.r+","+data.g+","+data.b+", 0.6)";
                        ctx.fill();
                    }
                    if (station == station_select && data.area_id) {
                        var area = document.getElementById("area-" + data.area_id);
                        area.setAttribute("fill-opacity","0.6");
                        area.setAttribute("fill","rgb("+data.r+","+data.g+","+data.b+")");
                    }
                    document.getElementById("locate_message").innerHTML += command + " " + station + "<br>" + data.message;
                }
            });
        }
    }

    locate_timer = setTimeout(locate, 1000);
}

function locate2(command, station, area_id) {
    $.ajax({
        url: "res/compare.php",
        type: "POST",
        data: ({command: command, sta: station}),
        dataType: "json",
        async: false,
        success: function(data) {
            document.getElementById("compare_punkt").style.left = data.x-5 + "px";
            document.getElementById("compare_punkt").style.top = data.y-5 + "px";
            document.getElementById("compare_punkt").style.visibility = "visible";
            document.getElementById("compare_message").innerHTML = command + " " + station + ": " + data.message;
            if (data.area_id != area_id) {
                clearAreas("locate_svg");
                area_id = data.area_id;
                var area = document.getElementById("area-" + data.area_id);
                area.setAttribute("fill-opacity","0.6");
                area.setAttribute("fill","rgb("+data.r+","+data.g+","+data.b+")");
            }
        }
    });

    setTimeout(locate2, 1000, command, station, area_id);
}

function show_stations() {
    $.ajax({
        url: "models/stations.php",
        type: "POST",
        data: ({command: "show_station"}),
        dataType: "json",
        success: function(data) {
            document.getElementById("info_message").innerHTML = data.message;
        }
    });
}

function show_station(station) {
    $.ajax({
        url: "models/stations.php",
        type: "POST",
        data: ({command: "show_station", station: station}),
        dataType: "json",
        success: function(data) {
            document.getElementById("info_message").innerHTML = data.message;
        }
    });
}

function show_positions() {
    $.ajax({
        url: "models/positions.php",
        type: "POST",
        data: ({command: "show_position"}),
        dataType: "json",
        success: function(data) {
            document.getElementById("pos_message").innerHTML = data.message;
        }
    });
}

function show_position(position) {
    $.ajax({
        url: "models/positions.php",
        type: "POST",
        data: ({command: "show_position", position: position}),
        dataType: "json",
        success: function(data) {
            document.pos_form.pos.value = position;
            document.getElementById("pos_message").innerHTML = data.message;
        }
    });
}

function show_monitor(monitor) {
    $.ajax({
        url: "models/monitors.php",
        type: "POST",
        data: ({command: "show_monitor", monitor: monitor}),
        dataType: "json",
        success: function(data) {
            document.getElementById("mon_message").innerHTML = data.message;
            document.getElementById("mon_punkt").innerHTML = "";

            var images = document.getElementById("monitors").getElementsByTagName("img");
            for (var i=0; i<images.length; i++) {
                images[i].style.opacity = 0.4;
            }
            document.getElementById(monitor).style.opacity = 1;
            $.each(data.result, function(i, item) {
                document.getElementById("mon_punkt").innerHTML += '<div style="left:' + (item.x-7) + 'px;top:' + (item.y-10) + 'px;">' +  item.signal + '</div>';
            });
        }
    });
}

function show_area(area) {
    $.ajax({
        url: "models/areas.php",
        type: "POST",
        data: ({command: "show_area", area: area}),
        dataType: "json",
        success: function(data) {
            document.getElementById("area_message").innerHTML = data.message;
        }
    });
}

function get_folders() {
    var session = document.getElementById('map_select').value;
    $.ajax({
        url: "get_folders.php",
        type: "POST",
        data: ({session: session}),
        dataType: "json",
        success: function(data) {
            document.getElementById("map_select2").innerHTML = data.message;
            document.getElementById("load_pos_ok").style.visibility = 'visible';
        }
    });
}

function get_stations() {
    var station_select = document.locate_form.station.value;
    var stations = document.locate_form.getElementsByClassName('item');

    var locate_stations = {};
    for (var j=0; j < stations.length; j++) {
        if (stations[j].checked) {
            locate_stations[stations[j].value] = true;
        }
    }

    $.ajax({
        url: "models/stations.php",
        type: "POST",
        data: ({command: "get_stations"}),
        dataType: "json",
        success: function(data) {
            document.getElementById("sidebar_message").innerHTML = data.message;
            document.info_form.querySelector('.stations').innerHTML = '';
            document.locate_form.querySelector('.stations').innerHTML = '';
            document.locate_form.station.innerHTML = '<option>Select station</option>';
            document.record_form.querySelector('.stations').innerHTML = '';
            $.each(data.result, function(i, item) {
                document.info_form.querySelector('.stations').innerHTML += '<label><input type="radio" name="sta" value="' + item.sta_id + '">' + item.sta_id + '</label>';
                document.locate_form.querySelector('.stations').innerHTML += '<label><input type="checkbox" name="sta" value="' + item.sta_id + '"' + (locate_stations[item.sta_id] ? ' checked' : '') + ' class="item"><span class="user_punkt" style="background-color:rgb(' + item.r + ',' + item.g + ',' + item.b + ');"></span>' + item.sta_id + '</label>';
                document.locate_form.station.innerHTML += '<option value="' + item.sta_id + '"' + (station_select == item.sta_id ? ' selected' : '') + '>' + item.sta_id + '</option>';
                document.record_form.querySelector('.stations').innerHTML += '<label><input type="radio" name="sta" value="' + item.sta_id + ' ' + item.record + '">' + item.sta_id + ' ' + item.record + '</label>';
            });
        }
    });
}

$(document).ready(function(){
    $(document.info_form).on('change', 'input', function(){
        document.getElementById('info_message').innerHTML = 'Loading...';
        show_station($(this).val());
    });
});

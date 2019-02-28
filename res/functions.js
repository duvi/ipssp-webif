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
    setTimeout("blink();", 500);
 }

function seconds() {
    var e = document.getElementById('seconds');
    var value = parseInt(e.textContent) + 1;
    e.innerHTML = value;
    setTimeout("seconds();", 1000);
}

function navigate(pos_x, pos_y) {
    document.getElementById("navigate_punkt").style.left = (pos_x-5);
    document.getElementById("navigate_punkt").style.top = (pos_y-5);
    document.getElementById("navigate_punkt").style.visibility = "visible";
    if (document.getElementById("navigate_text")) {
        document.getElementById("navigate_text").style.visibility = "hidden";
    }

    dest_x = pos_x;
    dest_y = pos_y;

    if (src_x && src_y) {
        drawpath();
    }
}

function drawpath() {
    var canvas = document.getElementById("navigate_canvas");

    if (canvas.getContext) {
        var ctx = canvas.getContext("2d");

        ctx.clearRect (0, 0, 623, 379);
        ctx.lineWidth = 8;
        ctx.strokeStyle = "#0000ff";
        ctx.beginPath();

        $.ajax({
            url: "res/path2.php",
            type: "POST",
            data: ({sx: src_x, sy: src_y, tx: dest_x, ty: dest_y}),
            dataType: "json",
            success: function(data) {
                ctx.moveTo(src_x,src_y);
                $.each(data, function(i, item) {
                    ctx.lineTo(item.x,item.y);
                });
                ctx.lineTo(dest_x,dest_y);
                ctx.stroke();
            }
        });
    }
}

function change_park(park_id,status) {
    $.ajax({
        url: "res/set_park.php",
        type: "POST",
        data: ({park_id: park_id, free: status}),
        dataType: "json",
        success: function(data) {
            document.getElementById("park_message").innerHTML = data.message;
        }
    });
}

function drawpark() {
    $.ajax({
        url: "res/get_park.php",
        dataType: "json",
        success: function(data) {
            document.getElementById("nav_punkt").innerHTML = "";
            document.getElementById("park_map").innerHTML = "";
            document.getElementById("park_message").innerHTML = "";
            $.each(data.result, function(i, item) {
                if (item.free == 0) {
                    document.getElementById("nav_punkt").innerHTML += '<img src="img/rotpunkt.png" title="' +  item.park_id + '" style="position:absolute;float:none;z-index:1;left:' + (item.x-5) + 'px;top:' + (item.y-5) + 'px;">';
                    document.getElementById("park_map").innerHTML += '<img src="img/rotpunkt.png" onclick="change_park(' + item.park_id + ',1)" title="' +  item.park_id + '" style="position:absolute;float:none;z-index:1;cursor:pointer;left:' + (item.x-5) + 'px;top:' + (item.y-5) + 'px;">';
                }
                else if (item.free == 1) {
                    document.getElementById("nav_punkt").innerHTML += '<img src="img/gruenerpunkt.png" onclick="navigate(' + item.x + ',' + item.y +')" title="' +  item.park_id + '" style="position:absolute;float:none;z-index:1;cursor:pointer;left:' + (item.x-5) + 'px;top:' + (item.y-5) + 'px;">';
                    document.getElementById("park_map").innerHTML += '<img src="img/gruenerpunkt.png" onclick="change_park(' + item.park_id + ',2)" title="' +  item.park_id + '" style="position:absolute;float:none;z-index:1;cursor:pointer;left:' + (item.x-5) + 'px;top:' + (item.y-5) + 'px;">';
                }
                else {
                    document.getElementById("nav_punkt").innerHTML += '<img src="img/blaupunkt.png" title="' +  item.park_id + '" style="position:absolute;float:none;z-index:1;left:' + (item.x-5) + 'px;top:' + (item.y-5) + 'px;">';
                    document.getElementById("park_map").innerHTML += '<img src="img/blaupunkt.png" onclick="change_park(' + item.park_id + ',0)" title="' +  item.park_id + '" style="position:absolute;float:none;z-index:1;cursor:pointer;left:' + (item.x-5) + 'px;top:' + (item.y-5) + 'px;">';
                }
            });
        }
    });

    park_timer = setTimeout("drawpark()", 3000);
}

function clearCanvas(name) {
    var canvas = document.getElementById(name);
    var ctx = canvas.getContext("2d");
    ctx.clearRect (0, 0, canvas.width, canvas.height);
}

function locate() {
    var canvas = document.getElementById("locate_canvas");
    var ctx = canvas.getContext("2d");
    var station;

    document.getElementById("locate_punkt").innerHTML = "";
    document.getElementById("locate_message").innerHTML = "";
    ctx.clearRect (0, 0, canvas.width, canvas.height);

    if (!document.locate_form.command || !document.locate_form.sta) {
        document.getElementById("locate_message").innerHTML = "Station or command list empty";
        locate_timer = setTimeout("locate()", 1000);
        return;
    }

    for (var i=0; i < document.locate_form.command.length; i++) {
        if (document.locate_form.command[i].checked) {
            var command = document.locate_form.command[i].value;
        }
    }

    for (var j=0; j < document.locate_form.sta.length; j++) {
        if (document.locate_form.sta[j].checked) {
            station = document.locate_form.sta[j].value;
            $.ajax({
                url: "res/compare.php",
                type: "POST",
                data: ({command: command, sta: station}),
                dataType: "json",
                async: false,
                success: function(data) {
                    if (data.x) {
                        ctx.beginPath();
                        ctx.arc(data.x, data.y, 6, 0, Math.PI*2, true);
                        ctx.stroke();
                        ctx.fillStyle = "rgba("+data.r+","+data.g+","+data.b+", 0.6)";
                        ctx.fill();
                    }
                    document.getElementById("locate_message").innerHTML += command + " " + station + "<br>" + data.message;
                }
            });
        }
    }

    locate_timer = setTimeout("locate()", 1000);
}

function compare(){
    for (var i=0; i < document.compare_form.command.length; i++) {
        if (document.compare_form.command[i].checked) {
            var command = document.compare_form.command[i].value;
        }
    }

    for (var j=0; j < document.compare_form.sta.length; j++) {
        if (document.compare_form.sta[j].checked) {
            var station = document.compare_form.sta[j].value;
        }
    }

    $.ajax({
        url: "res/compare.php",
        type: "POST",
        data: ({command: command, sta: station}),
        dataType: "json",
        success: function(data) {
            if (data.x) {
                document.getElementById("compare_punkt").style.left = data.x-5 + "px";
                document.getElementById("compare_punkt").style.top = data.y-5 + "px";
                document.getElementById("compare_punkt").style.visibility = "visible";
                src_x = data.x;
                src_y = data.y;
            }
            document.getElementById("compare_message").innerHTML = command + " " + station + "<br>" + data.message;
        }
    });

    if (src_x && src_y && dest_x && dest_y) {
        drawpath();
    }

    compare_timer = setTimeout("compare()", 1000);
}

function compare2(command, station) {
    $.ajax({
        url: "res/compare.php",
        type: "POST",
        data: ({command: command, sta: station}),
        dataType: "json",
        success: function(data) {
            document.getElementById("compare_punkt").style.left = data.x-5 + "px";
                document.getElementById("compare_punkt").style.top = data.y-5 + "px";
                document.getElementById("compare_punkt").style.visibility = "visible";
                document.getElementById("compare_message").innerHTML = command + " " + station + ": " + data.message;
                src_x = data.x;
                src_y = data.y;
        }
    });

    if (dest_x && dest_y) {
        drawpath();
    }

    command2 = command;
    station2 = station;

    setTimeout("compare2(command2, station2);", 1000);
}

function list_pos() {
    $.ajax({
        url: "res/list_pos.php",
        type: "POST",
        dataType: "json",
        success: function(data) {
            document.getElementById("info_message").innerHTML = data.message;
        }
    });
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

function show_pos(position) {
    $.ajax({
        url: "res/show_pos.php",
        type: "POST",
        data: ({pos: position}),
        dataType: "json",
        success: function(data) {
            document.pos_form.pos.value = position;
            document.getElementById("pos_message").innerHTML = data.message;
        }
    });
}

function show_mon(monitor) {
    $.ajax({
        url: "res/show_mon.php",
        type: "POST",
        data: ({mon: monitor}),
        dataType: "json",
        success: function(data) {
//            document.getElementById("mon_message").innerHTML = data.message;
            document.getElementById("mon_punkt").innerHTML = "";

            var images = document.getElementById("monitors").getElementsByTagName("img");
            for (var i=0; i<images.length; i++) {
                images[i].style.opacity = 0.4;
            }
            document.getElementById(monitor).style.opacity = 1;
            $.each(data.result, function(i, item) {
//                document.getElementById("mon_punkt").innerHTML += '<img src="img/rotpunkt.png" title="' +  item.signal + '" style="position:absolute;float:none;z-index:1;left:' + (item.x-5) + 'px;top:' + (item.y-5) + 'px;"><br>';
                document.getElementById("mon_punkt").innerHTML += '<div style="left:' + (item.x-7) + 'px;top:' + (item.y-10) + 'px;">' +  item.signal + '</div>';
            });
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
    var locate_stations = {};
    if (document.locate_form.sta) {
        for (var j=0; j < document.locate_form.sta.length; j++) {
            if (document.locate_form.sta[j].checked) {
                locate_stations[document.locate_form.sta[j].value] = true;
            }
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
            document.record_form.querySelector('.stations').innerHTML = '';
            document.compare_form.querySelector('.stations').innerHTML = '';
            $.each(data.result, function(i, item) {
                document.info_form.querySelector('.stations').innerHTML += '<label><input type="radio" name="sta" value="' + item.sta_id + '">' + item.sta_id + '</label>';
                document.locate_form.querySelector('.stations').innerHTML += '<label><input type="checkbox" name="sta" value="' + item.sta_id + '"' + (locate_stations[item.sta_id] ? ' checked' : '') + '><span class="user_punkt" style="background-color:rgb(' + item.r + ',' + item.g + ',' + item.b + ');"></span>' + item.sta_id + '</label>';
                document.record_form.querySelector('.stations').innerHTML += '<label><input type="radio" name="sta" value="' + item.sta_id + ' ' + item.record + '">' + item.sta_id + ' ' + item.record + '</label>';
                document.compare_form.querySelector('.stations').innerHTML += '<label><input type="radio" name="sta" value="' + item.sta_id + '">' + item.sta_id + '</label>';
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

#!/usr/bin/php
<?php
$server_ip   = '127.0.0.1';
$out_port    = 5950;
$in_port     = rand(10000, 65530);
$message     = $argv[1];
$logfile     = "logs/position.log";

if ($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
    socket_bind($socket, $server_ip, $in_port);
    socket_sendto($socket, $message, strlen($message), 0, $server_ip, $out_port);
    print "Sending message: '$message' to IP $server_ip:$out_port\n";
    socket_set_block($socket);
    socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>5,"usec"=>0));
    socket_recvfrom($socket, $reply, 65535, 0, $clientIP, $clientPort);
    socket_set_nonblock($socket);
    socket_close($socket);
    if ($reply) {
        list($command_in, $param_in) = sscanf($reply, "%s %s");
        switch ($command_in) {
            case "done":
                $message_file = fopen($logfile, "r");
                echo "Reply type: $command_in\n";
                echo "Message:\n";
                echo fread($message_file, filesize($logfile));
                fclose($message_file);
                break;
            case "message":
                echo "Reply type: $command_in\n";
                echo "Message:\n";
                echo substr($reply, strlen($command_in));
                break;
            default:
                echo "Reply: $reply\n";
                break;
        }
    }
}
else {
    print("can't create socket\n");
}
?>

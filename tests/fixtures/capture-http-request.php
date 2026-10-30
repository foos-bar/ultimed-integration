<?php

$server = stream_socket_server('tcp://127.0.0.1:0', $errorCode, $errorMessage);
if ($server === false) {
    throw new RuntimeException($errorMessage);
}

fwrite(STDOUT, stream_socket_get_name($server, false)."\n");
$connection = stream_socket_accept($server, 5);
if ($connection === false) {
    throw new RuntimeException('No HTTP request received within five seconds.');
}

try {
    stream_set_timeout($connection, 5);
    $request = '';
    do {
        $line = fgets($connection);
        if ($line === false) {
            throw new RuntimeException('Incomplete HTTP request headers.');
        }
        $request .= $line;
    } while ($line !== "\r\n");

    if (preg_match('/^Content-Length:\\s*(\\d+)\\r$/mi', $request, $matches) && (int) $matches[1] > 0) {
        $body = stream_get_contents($connection, (int) $matches[1]);
        if ($body === false || strlen($body) !== (int) $matches[1]) {
            throw new RuntimeException('Incomplete HTTP request body.');
        }
        $request .= $body;
    }

    fwrite($connection, "HTTP/1.1 200 OK\r\nContent-Type: text/plain\r\nContent-Length: ".strlen($request)."\r\nConnection: close\r\n\r\n".$request);
} finally {
    fclose($connection);
    fclose($server);
}

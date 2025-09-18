<?php
session_start();

function read_json($file) {
    $data = file_get_contents($file);
    return json_decode($data, true);
}

function write_json($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}
?>
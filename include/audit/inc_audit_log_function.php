<?php

function audit($user, string $action, $data)
{
    require_once __DIR__."/../../config.php";
    $mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    $stmt = $mysqli->prepare("INSERT INTO audit_log(id, time, user, action, data) VALUES (null, NOW(), ?, ?, ?)");
    $stmt->bind_param("iss", $user, $action, $data);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();
}

<?php
error_reporting(E_ALL & ~E_NOTICE);

date_default_timezone_set("Europe/Berlin");
header("Cache-Control: no-cache");
header("Content-Type: text/event-stream");

require_once dirname(__FILE__)."/config.php";

/** @noinspection PhpUndefinedVariableInspection */
$mysqli = new mysqli($DB_SERVER, $DB_USER, $DB_PASS, $DB_NAME);

if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}
exit();
while (true) {
    // Every second, send a "ping" event.

    $res = $mysqli->query("SELECT count(*) as event_count, event FROM check_events GROUP BY event");
    $numRows = $res->num_rows;
    $num = 0;
    if ($numRows > 2) {
        die("Invalid Number of rows");
    } else {
        $data = $res->fetch_all(MYSQLI_ASSOC);
        $num = abs($data[0]['event_count'] - ($numRows == 1 ? 0 : $data[1]['event_count']));
    }

    echo "event: ping\n";
    $curDate = date(DATE_ISO8601);
    /** @noinspection PhpUndefinedVariableInspection */
    echo 'data: {"time": "' . $curDate . '", "guests": '.$num.', "limit": '.$PERSON_LIMIT.'}';
    echo "\n\n";

    ob_end_flush();
    flush();

    // Break the loop if the client aborted the connection (closed the page)

    if ( connection_aborted() ) break;

    sleep(1);
}


<?php
define("INCLUDED", true);

if(php_sapi_name() != "cli")
    die();

require_once dirname(__FILE__)."/config.php";

function getPassword($stars = false)
{
    // Get current style
    $oldStyle = shell_exec('stty -g');

    if ($stars === false) {
        shell_exec('stty -echo');
        $password = rtrim(fgets(STDIN), "\n");
    } else {
        shell_exec('stty -icanon -echo min 1 time 0');

        $password = '';
        while (true) {
            $char = fgetc(STDIN);

            if ($char === "\n") {
                break;
            } else if (ord($char) === 127) {
                if (strlen($password) > 0) {
                    fwrite(STDOUT, "\x08 \x08");
                    $password = substr($password, 0, -1);
                }
            } else {
                fwrite(STDOUT, "*");
                $password .= $char;
            }
        }
    }

    // Reset old style
    shell_exec('stty ' . $oldStyle);

    // Return the password
    return $password;
}

/** @noinspection PhpUndefinedVariableInspection */
$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$user = readline("User [".$argv[1]."]: ");
if($user == null || $user == ""){
    $user = $argv[1];
}
$g = readline("Given name         : ");
$sn = readline("Surname            : ");
fwrite(STDOUT, "Password           : ");
$password = getPassword(false);

$hash = password_hash($password, PASSWORD_ARGON2I);

$s = $mysqli->prepare("INSERT INTO users(id, username, password, given_name, surname) VALUES (null, ?, ?, ?, ?)");
$s->bind_param("ssss", $user, $hash, $g, $sn);
$s->execute();
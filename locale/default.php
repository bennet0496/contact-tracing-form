<?php
const LANG = [];
function LANG($msg)
{
    if (key_exists($msg, LANG)) {
        return LANG[$msg];
    } else {
        return $msg;
    }
}

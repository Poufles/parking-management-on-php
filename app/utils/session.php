<?php

session_start();

$_SESSION['uid'] = $_COOKIE['parcheggiamo-uid'] ?? null;
$_SESSION['username'] = $_COOKIE['parcheggiamo-username'] ?? null;
$_SESSION['account-type'] = $_COOKIE['parcheggiamo-account-type'] ?? null;
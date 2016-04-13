<?php
require "bootstrap.php";

$oauth->finishAuthorization();

// For convenience
if($tokenStorage->getAccessToken() !== null) {
    die("Authorization succeeded. <a href='client.php'>Go to client</a>");
} else {
    die("Authorization failed");
}
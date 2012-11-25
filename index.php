<?php
include("router.php");

$router->add("home","home",array());
$router->setDefaultRoute("home");
$router->run();

function home() {
    echo "<h1>HOME</h1>";
}
?>
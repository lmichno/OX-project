<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
if (isset($_POST['player'])) {
    $player = $_POST['player'];
    if ($player === 'X') {
        $nextPlayer = 'O';
    } else {
        $nextPlayer = 'X';
    }
    echo json_encode(array('player' => $player, 'nextPlayer' => $nextPlayer));
}

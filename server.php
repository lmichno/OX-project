<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
$memcached = new Memcache();
$memcached->addServer('localhost', 11211);

if (isset($_POST['player'])) {
    $player = $_POST['player'];
    $nextPlayer = $player == 'X' ? 'O' : 'X';

    $memcached->set('player', $player);
    $memcached->set('nextPlayer', $nextPlayer);
    $memcached->set('move', $player);

    echo json_encode(array('player' => $player, 'nextPlayer' => $nextPlayer));
}
else if (isset($_POST['update'])) {
    echo json_encode(array('player' => $memcached->get('player'), 'nextPlayer' => $memcached->get('nextPlayer'), 'move' => $memcached->get('move')));
}
else if(isset($_POST['reset'])) {
    $memcached->delete('player');
    $memcached->delete('nextPlayer');
    echo json_encode(array('player' => null, 'nextPlayer' => null));
}

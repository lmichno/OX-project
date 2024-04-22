<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
$memcached = new Memcache();
$memcached->addServer('localhost', 11211);

$field = array(
    array(null, null, null),
    array(null, null, null),
    array(null, null, null)
);

if (isset($_POST['player'])) {
    $player = $_POST['player'];
    $nextPlayer = $player == 'X' ? 'O' : 'X';

    $field = array(
        array(null, null, null),
        array(null, null, null),
        array(null, null, null)
    );
    $memcached->set('field', $field);


    $memcached->set('player', $player);
    $memcached->set('nextPlayer', $nextPlayer);
    $memcached->set('move', $player);

    echo json_encode(array('player' => $player, 'nextPlayer' => $nextPlayer));
}
else if (isset($_POST['update'])) {
    $winner = checkWinner($memcached->get('field'));
    echo json_encode(array('player' => $memcached->get('player'), 'nextPlayer' => $memcached->get('nextPlayer'), 'move' => $memcached->get('move'), 'field' => $memcached->get('field'), 'winner' => $winner));
}
else if(isset($_POST['reset'])) {
    $memcached->delete('player');
    $memcached->delete('nextPlayer');
    $field = array(
        array(null, null, null),
        array(null, null, null),
        array(null, null, null)
    );
    $memcached->set('field', $field);
    echo json_encode(array('player' => null, 'nextPlayer' => null));
}
else if(isset($_POST['field'])) {
    if ($_POST['playerChoice'] == $memcached->get('move')) {
        $field = $memcached->get('field');
        $indexes = str_split($_POST['field']);
        $row = intval($indexes[0]) - 1;
        $col = intval($indexes[1]) - 1;
        if ($memcached->get('field')[$row][$col] == null) {
            $field[$row][$col] = $memcached->get('move');
        }
        $memcached->set('field', $field);
        $memcached->set('move', $memcached->get('move') == 'X' ? 'O' : 'X');

        echo json_encode(array('field' => $field, 'move' => $memcached->get('move')));
    }
}

function checkWinner($field) {
    for ($i = 0; $i < 3; $i++) {
        if ($field[$i][0] && $field[$i][0] == $field[$i][1] && $field[$i][0] == $field[$i][2]) {
            return $field[$i][0];
        }
    }

    for ($i = 0; $i < 3; $i++) {
        if ($field[0][$i] && $field[0][$i] == $field[1][$i] && $field[0][$i] == $field[2][$i]) {
            return $field[0][$i];
        }
    }

    if ($field[0][0] && $field[0][0] == $field[1][1] && $field[0][0] == $field[2][2]) {
        return $field[0][0];
    }
    if ($field[0][2] && $field[0][2] == $field[1][1] && $field[0][2] == $field[2][0]) {
        return $field[0][2];
    }

    for ($i=0; $i < 3; $i++) { 
        for ($j=0; $j < 3; $j++) { 
            if ($field[$i][$j] == null) {
                return null;
            }
        }
    }

    return 'draw';
}

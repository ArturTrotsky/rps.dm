<?php

define('SHAPES', ['R', 'P', 'S']);
define('PLAYER_INDEX_FILE', 'player-index.txt');
define('GAMES_FILE', 'games.txt');

require_once 'functions.php';

session_start();

if (empty($_SESSION)) {
    $playerIndex = file_exists(PLAYER_INDEX_FILE)
    ? file_get_contents(PLAYER_INDEX_FILE)
    : 0;
    $playerIndex++;
    $_SESSION['username'] = 'Player' . $playerIndex;
    $_SESSION['game'] = false;
    file_put_contents(PLAYER_INDEX_FILE, $playerIndex);
}
if (isset($_GET['new'])) {
    $_SESSION['game'] = false;
}

$username = $_SESSION['username'];
$shape = $_REQUEST['shape'] ?? false;
if ($_SESSION['game'] !== false) {
    $gameData = getAllGamesData()[$_SESSION['game']];
} else {
    $gameData = getGameData();
}

if ($shape) {
    if (isset($gameData[$username])) {
        # it is him game
    } elseif ($_SESSION['game'] === false) {
        $gameData[$username] = $shape;
        $_SESSION['game'] = saveGameData($gameData);
    }
}

$vars = [
    '{USERNAME}' => $_SESSION['username']
];
if (count($gameData) === 0) {
    outputHTML($vars, 'forms');
} elseif (count($gameData) === 1 && isset($gameData[$username])) {
    outputHTML($vars, 'waiting');
} elseif (count($gameData) === 1 && !isset($gameData[$username])) {
    outputHTML($vars, 'forms');
} else {
    $shapes = [];
    $players = [];
    foreach ($gameData as $player => $shape) {
        $shapes[] = $shape;
        $players[] = $player;
    }
    $results = playRockPaperScissors($shapes[0], $shapes[1]);
    if ($results === 'first') {
        $results = $players[0] . ' win';
    } elseif ($results === 'second') {
        $results = $players[1] . ' win';
    } else {
        $results = 'Draw';
    }
    $vars['{RESULTS}'] = $results;
    outputHTML($vars, 'results');
}

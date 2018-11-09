<?php

define('SHAPES', ['R', 'P', 'S']);
define('PLAYER_INDEX_FILE', 'player-index.txt');
define('GAMES_FILE', 'games.txt');

require_once 'functions.php';

session_start();

$sortType = $_GET['sort'] ?? 'default';
$username = $_SESSION['username'];

switch ($sortType) {
    case 'default':
        $statsData = getStatsData();
        break;
    case 'player1':
        $statsData = getStatsData();
        $statsData = sortStatsData($statsData, 1);
        break;
    case 'player2':
        $statsData = getStatsData();
        $statsData = sortStatsData($statsData, 2);
        break;
    case 'result':
        $statsData = getStatsData();
        $statsData = sortStatsData($statsData, 3);
        break;
}

$vars['{USERNAME}'] = $username;
$vars['{STATS}'] = makeHtmlStats($username, $statsData);

outputHTML($vars, 'stats');

<?php

function getAllGamesData()
{
    if (!file_exists(GAMES_FILE)) {
        file_put_contents(GAMES_FILE, serialize([]));
    }
    return unserialize(file_get_contents(GAMES_FILE));
}

function saveGameData($data)
{
    $gamesData = getAllGamesData();
    $lastIndex = count($gamesData) - 1;
    if (count($data) === 1) {
        $gamesData[] = $data;
    } else {
        $gamesData[$lastIndex] = $data;
    }
    file_put_contents(
        GAMES_FILE,
        serialize($gamesData)
    );
    return count($gamesData) - 1;
}

function getGameData()
{
    $gamesData = getAllGamesData();
    $gameData = [];
    $lastIndex = count($gamesData) - 1;
    if ($lastIndex > -1) {
        $lastGameData = $gamesData[$lastIndex];
        $lastCount = count($lastGameData);
        if ($lastCount === 1) {
            $gameData = $lastGameData;
        }
    }
    return $gameData;
}

function playRockPaperScissors($firstShape, $secondShape)
{
    if (!in_array($firstShape, SHAPES)) {
        if (!in_array($secondShape, SHAPES)) {
            return 'draw';
        }
        return 'second';
    }
    if (!in_array($secondShape, SHAPES)) {
        return 'first';
    }
    $firstIndex = array_search($firstShape, SHAPES);
    $secondIndex = array_search($secondShape, SHAPES);
    switch ($firstIndex - $secondIndex) {
        case -2:
            return 'first';
        case -1:
            return 'second';
        case 0:
            return 'draw';
        case 1:
            return 'first';
        case 2:
            return 'second';
    }
}

function outputHTML($vars, $template)
{
    $html = file_get_contents($template . '.html');
    echo str_replace(
        array_keys($vars),
        array_values($vars),
        $html
    );
}

function makeHtmlStats($username, $statsData)
{
    $htmlStats = null;
    
    foreach ($statsData as $data) {
        $htmlStats .= '<tr><td>' . $data['gameCount'] . '</td><td>' . $data['namePlayer1'] . ' (' . $data['shapePlayer1'] . ')</td>';
        $htmlStats .= '<td>' . $data['namePlayer2'] . ' (' . $data['shapePlayer2'] . ')</td><td>';
        $htmlStats .= addHtmlWinTag($data['result'], $username) . '</td></tr>';
    }

    return $htmlStats;
}

function addHtmlWinTag($str_result, $username)
{
    if (substr_count($str_result, $username) > 0) {
        return '<b><font color=red>' . $str_result . '</font></b>';
    } else {
        return $str_result;
    }
}

function getStatsData()
{
    $arrGames = getAllGamesData();
    $arrGamesWithResult = [];
    $gameCount = 0;

    foreach ($arrGames as $game) {
        if (count($game) === 2) {
            $players = [];
            $shapes = [];
            $gameCount++;

            foreach ($game as $key => $value) {
                $players[] = $key;
                $shapes[] = $value;
            }

            $results = playRockPaperScissors($shapes[0], $shapes[1]);

            if ($results === 'first') {
                $results = $players[0] . ' win';
            } elseif ($results === 'second') {
                $results = $players[1] . ' win';
            } else {
                $results = 'Draw';
            }

            $arrGamesWithResult[] = [
                'gameCount' => $gameCount,
                'namePlayer1' => $players[0],
                'shapePlayer1' => $shapes[0],
                'namePlayer2' => $players[1],
                'shapePlayer2' => $shapes[1],
                'result' => $results
            ];
        }
    }
    return $arrGamesWithResult;
}

function sortStatsData($statsData, $sortType)
{
    if ($sortType === 1) {
        $sortKey = 'namePlayer1';
    } elseif ($sortType === 2) {
        $sortKey = 'namePlayer2';
    } else {
        $sortKey = 'result';
    }

    $data_key = [];

    foreach ($statsData as $key => $arr) {
        $data_key[$key] = $arr[$sortKey];
    }

    array_multisort($data_key, SORT_STRING, $statsData);

    return $statsData;
}

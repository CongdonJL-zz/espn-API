<?php
    include('simple_html_dom.php');
    header("Content-Type: application/json; charset=UTF-8");

    $week = $_GET["week"];
    $leagueID = $_GET["leagueid"];

    if ((empty($week) || (empty($leagueID)))){
        echo "Vars Missing!";
        exit;
    }

    $html = new simple_html_dom();
    $html->load_file('http://games.espn.go.com/ffl/scoreboard?leagueId=' . $leagueID . '&matchupPeriodId=' . $week);

    $matchups = $html->find('.matchup');
    $json_matchups = [];

    foreach($matchups as $matchup) {
        $teams = $matchup->find('tr');
        $team_containers = [];
        $count = 0;
        foreach ($teams as $team) {
            if ($count < 2) {

                $team_data = $team->parent()->last_child()->find('.playersPlayed');
                $team_containers[] = [
                    'Team' => $team->find('a')[0]->innertext(),
                    'score' => $team->find('.score')[0]->innertext(),
                    'record' => $team->find('.record')[0]->innertext(),
                    'yet-to-play' => $team_data[$count]->first_child()->innertext(),
                    'in-play' => $team_data[$count]->nth_child(1)->innertext(),
                    'min-left' => $team_data[$count]->nth_child(2)->innertext(),
                    'proj-total' => $team_data[$count]->nth_child(3)->innertext(),
                    'line' => $team_data[$count]->nth_child(4)->innertext(),
                    'top-scorer' => $team_data[$count]->last_child()->innertext
                ];
            }
            $count++;
        }

        array_push($json_matchups, $team_containers);
    }

    echo json_encode($json_matchups);


function debugging($message){
    echo "<pre>";
    print_r($message);
    exit;
}
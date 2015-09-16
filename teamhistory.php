<?
	//MySQLi connection
	$mysqli = new mysqli('localhost', 'rclsirzj_lolmeta', 'LoLMeta42', 'rclsirzj_lolmeta');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}

	
	//get db teamMatchHistory
	$history = array();
	if($result = $mysqli->query("SELECT matchID FROM teamMatchHistory")){
		while ($row = $result->fetch_assoc()) {
			array_push($history, $row['matchID']);
		}
		unset($row);
    	$result->close();
	}

	//get JSON matchhistory
	$obj = array();
	$json = file_get_contents("https://euw.api.pvp.net/api/lol/euw/v2.4/team/TEAM-1cc4ac30-3bfe-11e3-b84d-782bcb4ce61a?api_key=92d4e772-44cf-4bc8-9782-236d2c18fcc0");
	$obj = json_decode($json, true);

	//teamMatchHistory
	foreach ($obj['TEAM-1cc4ac30-3bfe-11e3-b84d-782bcb4ce61a']['matchHistory'] as $key => $value) {
		if (!(in_array($value['gameId'], $history))) {
			$query = '	INSERT INTO teamMatchHistory (
							matchID,
							mapID,
							creation,
							win,
							kills,
							deaths,
							assists,
							opponent)
						VALUES ( 
							'.$value['gameId'].',
							'.$value['mapId'].',
							'.$value['date'].',
							'.($value['win'] == '1' ? "1" : "0").',
							'.$value['kills'].', 
							'.$value['deaths'].', 
							'.$value['assists'].',
							"'.$value['opposingTeamName'].'"
							)';
			$mysqli->query($query);
			unset($query);
		}
	}
	unset($history, $obj);


	//MySQLi disconnection
	$mysqli->close();
?>

<?
	//--- MySQLi connection ---
	$mysqli = new mysqli('localhost', 'rclsirzj_lolmeta', 'LoLMeta42', 'rclsirzj_matchHistory');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}


	//--- Variables ---
	$apikey = 'a964ed63-f501-4f5e-9d16-d7e90b0048e2';
	$teamIds = array(
	    'FourGoodoneBadGuy'	=> "TEAM-1cc4ac30-3bfe-11e3-b84d-782bcb4ce61a",
	    'Allerbesten'		=> "TEAM-ccee0980-5e43-11e5-8042-c81f66dd32cd"
	);
	
	//get JSON team-v2.4
	$urlTeam =
		'https://euw.api.pvp.net/api/lol/euw/v2.4/team/'
			.$teamIds['FourGoodoneBadGuy'].','
			.$teamIds['Allerbesten'].'?api_key='
			.$apikey;
	$jsonTeam = file_get_contents($urlTeam);
	$objTeam = json_decode($jsonTeam,true);


	//--- Methodes ---
	foreach ($objTeam as $key0 => $team) {
		foreach ($team['matchHistory'] as $key1 => $match) {
			$query = '
				INSERT INTO teamHistory (
					matchId,
					teamId,
					win,
					kills,
					deaths,
					assists,
					opposingTeamName,
					opposingTeamKills,
					invalid
				)
				VALUES (
					'.$match['gameId'].',
					"'.$team['fullId'].'",
					'.($match['win']>0?1:0).',
					'.$match['kills'].',
					'.$match['deaths'].',
					'.$match['assists'].',
					"'.$match['opposingTeamName'].'",
					'.$match['opposingTeamKills'].',
					'.($match['invalid']>0?1:0).'
				)
			';
			$mysqli->query($query);
			unset($query);
		}
	}
	unset($urlTeam,$jsonTeam,$objTeam);


	//--- MySQLi disconnection ---
	$mysqli->close();
?>

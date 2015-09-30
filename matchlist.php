<?
	//MySQLi connection
	$mysqli = new mysqli('localhost', 'rclsirzj_lolmeta', 'LoLMeta42', 'rclsirzj_matchHistory');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}

	$summonerIds = array(
		'Biberman'	=> "40023404",
	    'Lupusius'	=> "38527580",
		'Lurchi09'	=> "40105796",
		'Oglie'		=> "39999855",
		'TomotX'	=> "41739529",
	    'Viper6'	=> "38598275");

	foreach ($summonerIds as $key0 => $value0) {
		//get JSON matchlist-v2.2
		$url =
			'https://euw.api.pvp.net/api/lol/euw/v2.2/matchlist/by-summoner/'
				.$value0.
			'?seasons=SEASON2015&api_key=92d4e772-44cf-4bc8-9782-236d2c18fcc0';
		$json = file_get_contents($url);
		$obj = json_decode($json, true);

		foreach ($obj['matches'] as $key => $value) {
			$query = '
				INSERT INTO matchlist (
					matchId,
					summonerId,
					queue,
					champion,
					lane,
					role,
					timestamp,
					season
				)
				VALUES (
					'.$value['matchId'].',
					'.$value0.',
					"'.$value['queue'].'",
					'.$value['champion'].',
					"'.$value['lane'].'",
					"'.$value['role'].'",
					'.$value['timestamp'].',
					"'.$value['season'].'"
				)
				ON DUPLICATE KEY UPDATE
					queue 		=	VALUES(queue),
					champion 	= 	VALUES(champion),
					lane 		= 	VALUES(lane),
					role 		= 	VALUES(role),
					timestamp	= 	VALUES(timestamp),
					season 		= 	VALUES(season)
			';
			$mysqli->query($query);
			unset($query);
		}
		unset($url,$json,$obj);
	}

	//MySQLi disconnection
	$mysqli->close();
?>

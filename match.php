
<?
	//MySQLi connection
	$mysqli = new mysqli('localhost', 'rclsirzj_lolmeta', 'LoLMeta42', 'rclsirzj_matchHistory');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}

	//gibt die matchIds aus, die noch nicht im table match sind
	$query = '
		SELECT DISTINCT ml.matchId
		FROM matchlist ml
		WHERE NOT EXISTS (
		    SELECT m.matchId
		    FROM `match` m
		    WHERE m.matchId = ml.matchId
		)
		ORDER BY ml.matchId DESC
	';
	$matchIds = array();
	if($result = $mysqli->query($query)){
		while ($row = $result->fetch_assoc()) {
			$matchIds[] = $row;
		}
		unset($row);
    	$result->close();
    }
    unset($query);

	for ($i=0; $i<5 ; $i++) { //wegen Abfragelimit auf 5 API-Requests per Aufruf beschraenkt
		//get JSON match-v2.2
		$url =
			'https://euw.api.pvp.net/api/lol/euw/v2.2/match/'
				.$matchIds[$i]['matchId'].
			'?api_key=92d4e772-44cf-4bc8-9782-236d2c18fcc0';
		$json = file_get_contents($url);
		$obj = json_decode($json, true);

		$query = '
			INSERT INTO `match` (
				matchId,
				queueType,
				matchCreation,
				matchDuration,
				mapId,
				matchMode,
				matchType,
				matchVersion,
				season
			)
			VALUES (
				'.$matchIds[$i]['matchId'].',
				"'.$obj['queueType'].'",
				'.$obj['matchCreation'].',
				'.$obj['matchDuration'].',
				'.$obj['mapId'].',
				"'.$obj['matchMode'].'",
				"'.$obj['matchType'].'",
				"'.$obj['matchVersion'].'",
				"'.$obj['season'].'"
			)
			ON DUPLICATE KEY UPDATE
				queueType 		=	VALUES(queueType),
				matchCreation	= 	VALUES(matchCreation),
				matchDuration	= 	VALUES(matchDuration),
				mapId 			= 	VALUES(mapId),
				matchMode 		= 	VALUES(matchMode),
				matchType 		= 	VALUES(matchType),
				matchVersion	= 	VALUES(matchVersion),
				season 			= 	VALUES(season)
		';
		$mysqli->query($query);
		unset($query);

	}
	unset($matchIds);

	//MySQLi disconnection
	$mysqli->close();
?>

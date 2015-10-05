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
		if (array_key_exists('matchId', $matchIds[$i])) {
			//get JSON match-v2.2
			$url =
				'https://euw.api.pvp.net/api/lol/euw/v2.2/match/'
					.$matchIds[$i]['matchId'].
				'?api_key=92d4e772-44cf-4bc8-9782-236d2c18fcc0';
			$json = file_get_contents($url);
			$obj = json_decode($json, true);

			//fügt generelle Matchdaten in table match ein
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

			//fügt Summoner spezifische Daten in table matchParticipants ein
			$participants = array();
			foreach ($obj['participantIdentities'] as $key => $value) {
				if (in_array($value['player']['summonerId'], $summonerIds)) {
					$participants[$key]['summonerId'] = $value['player']['summonerId'];
					$participants[$key]['participantId'] = $value['participantId'];
				}
			}
			foreach ($participants as $key0 => $participant) {
				foreach ($obj['participants'] as $key => $value) {
					if($value['participantId'] == $participant['participantId']){
						$query = '
							INSERT INTO matchParticipants (
								matchId,
								summonerId,
								winner,
								championId,
								kills,
								deaths,
								assists,
								minionsKilled,
								champLevel,
								goldEarned,
								item0,
								item1,
								item2,
								item3,
								item4,
								item5,
								item6,
								totalDamageDealtToChampions,
								totalDamageTaken,
								totalHeal,
								totalTimeCrowdControlDealt
							)
							VALUES (
								'.$matchIds[$i]['matchId'].',
								'.$participant['summonerId'].',
								'.($value['stats']['winner']>0?1:0).',
								'.$value['championId'].',
								'.$value['stats']['kills'].',
								'.$value['stats']['deaths'].',
								'.$value['stats']['assists'].',
								'.$value['stats']['minionsKilled'].',
								'.$value['stats']['champLevel'].',
								'.$value['stats']['goldEarned'].',
								'.$value['stats']['item0'].',
								'.$value['stats']['item1'].',
								'.$value['stats']['item2'].',
								'.$value['stats']['item3'].',
								'.$value['stats']['item4'].',
								'.$value['stats']['item5'].',
								'.$value['stats']['item6'].',
								'.$value['stats']['totalDamageDealtToChampions'].',
								'.$value['stats']['totalDamageTaken'].',
								'.$value['stats']['totalHeal'].',
								'.$value['stats']['totalTimeCrowdControlDealt'].'
							)
							ON DUPLICATE KEY UPDATE
								winner 						=	VALUES(winner),
								championId					= 	VALUES(championId),
								kills						= 	VALUES(kills),
								deaths 						= 	VALUES(deaths),
								assists 					= 	VALUES(assists),
								minionsKilled 				= 	VALUES(minionsKilled),
								champLevel					= 	VALUES(champLevel),
								goldEarned					=	VALUES(goldEarned),
								item0 						= 	VALUES(item0),
								item1 						= 	VALUES(item1),
								item2 						= 	VALUES(item2),
								item3 						= 	VALUES(item3),
								item4 						= 	VALUES(item4),
								item5 						= 	VALUES(item5),
								item6 						= 	VALUES(item6),
								totalDamageDealtToChampions	= 	VALUES(totalDamageDealtToChampions),
								totalDamageTaken 			= 	VALUES(totalDamageTaken),
								totalHeal 					=	VALUES(totalHeal),
								totalTimeCrowdControlDealt	= 	VALUES(totalTimeCrowdControlDealt)
						';
						$mysqli->query($query);
						unset($query);
					}
				}
			}
			unset($participants);
			unset($url,$json,$obj);
		}
	}
	unset($matchIds);

	//MySQLi disconnection
	$mysqli->close();
?>

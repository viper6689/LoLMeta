<?
	//--- MySQLi connection ---
	$mysqli = new mysqli('localhost', 'rclsirzj_lolmeta', 'LoLMeta42', 'rclsirzj_matchHistory');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}


	//--- Variables ---
	$apikey = 'a964ed63-f501-4f5e-9d16-d7e90b0048e2';
	$summonerIds = array(
		'Biberman'	=> "40023404",
	    'Lupusius'	=> "38527580",
		'Lurchi09'	=> "40105796",
		'Oglie'		=> "39999855",
		'TomotX'	=> "41739529",
	    'Viper6'	=> "38598275"
	);

	//$matchIds die noch nicht im table matches sind
	$query = '
		SELECT DISTINCT sm.matchId
		FROM summonerMatchstats sm
		WHERE NOT EXISTS (
		    SELECT m.matchId
		    FROM matches m
		    WHERE m.matchId = sm.matchId
		)
		ORDER BY sm.matchId DESC
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
	

	//--- Methodes ---
	for ($i=0; $i<5 ; $i++) { //wegen Abfragelimit auf 5 API-Requests per Aufruf beschraenkt
		if (!is_null($matchIds[$i])) {
			//get JSON match-v2.2
			$urlMatch =
				'https://euw.api.pvp.net/api/lol/euw/v2.2/match/'
				.$matchIds[$i]['matchId'].'?api_key='
				.$apikey
			;
			$jsonMatch = file_get_contents($urlMatch);
			$objMatch = json_decode($jsonMatch,true);

			//fügt generelle Matchdaten in table "matches" ein
			$query = '
				INSERT INTO matches (
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
					"'.$objMatch['queueType'].'",
					'.$objMatch['matchCreation'].',
					'.$objMatch['matchDuration'].',
					'.$objMatch['mapId'].',
					"'.$objMatch['matchMode'].'",
					"'.$objMatch['matchType'].'",
					"'.$objMatch['matchVersion'].'",
					"'.$objMatch['season'].'"
				)
			';
			$mysqli->query($query);
			unset($query);

			//fügt Summoner spezifische Daten in table summonerMatchstats ein
			$participants = array();
			foreach ($objMatch['participantIdentities'] as $key => $participantIdentity) {
				if (in_array($participantIdentity['player']['summonerId'], $summonerIds)) {
					$participants[$key]['summonerId'] = $participantIdentity['player']['summonerId'];
					$participants[$key]['participantId'] = $participantIdentity['participantId'];
				}
			}
			foreach ($participants as $key0 => $participant0) {
				foreach ($objMatch['participants'] as $key1 => $participant) {
					if($participant['participantId'] == $participant0['participantId']){
						$query = '
							UPDATE summonerMatchstats
							SET 
								win							= '.($participant['stats']['winner']>0?1:0).',
								kills						= '.$participant['stats']['kills'].',
								deaths 						= '.$participant['stats']['deaths'].',
								assists 					= '.$participant['stats']['assists'].',
								minionsKilled 				= '.$participant['stats']['minionsKilled'].',
								champLevel 					= '.$participant['stats']['champLevel'].',
								goldEarned					= '.$participant['stats']['goldEarned'].',
								item0						= '.$participant['stats']['item0'].',
								item1						= '.$participant['stats']['item1'].',
								item2						= '.$participant['stats']['item2'].',
								item3						= '.$participant['stats']['item3'].',
								item4						= '.$participant['stats']['item4'].',
								item5						= '.$participant['stats']['item5'].',
								item6						= '.$participant['stats']['item6'].',
								totalDamageDealtToChampions = '.$participant['stats']['totalDamageDealtToChampions'].',
								totalDamageTaken 			= '.$participant['stats']['totalDamageTaken'].',
								totalHeal 					= '.$participant['stats']['totalHeal'].',
								totalTimeCrowdControlDealt 	= '.$participant['stats']['totalTimeCrowdControlDealt'].',
								complete 					= 1
							WHERE 
								matchId		= '.$matchIds[$i]['matchId'].' AND
								summonerId 	= '.$participant0['summonerId'].'
						';
						$mysqli->query($query);
						unset($query);
					}
				}
			}
			unset($participants);
			unset($urlMatch,$jsonMatch,$objMatch);
		}
	}
	unset($matchIds);

	//--- MySQLi disconnection ---
	$mysqli->close();
?>

<?
	//--- MySQLi connection ---
	$mysqli = new mysqli('localhost', 'rclsirzj_lolmeta', 'LoLMeta42', 'rclsirzj_matchHistory');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}


	//--- Variables ---
	$apikey = 'a964ed63-f501-4f5e-9d16-d7e90b0048e2';
	$playerOrTeamIds = array(
		'Biberman'	=> "40023404",
	    'Lupusius'	=> "38527580",
		'Lurchi09'	=> "40105796",
		'Oglie'		=> "39999855",
		'TomotX'	=> "41739529",
	    'Viper6'	=> "38598275",
	    'FourGoodoneBadGuy'	=> "TEAM-1cc4ac30-3bfe-11e3-b84d-782bcb4ce61a",
	    'Allerbesten'		=> "TEAM-ccee0980-5e43-11e5-8042-c81f66dd32cd");
	
	//get JSON league-v2.5
	$urlLeague =
		'https://euw.api.pvp.net/api/lol/euw/v2.5/league/by-summoner/'
			.$playerOrTeamIds['Biberman'].','
			.$playerOrTeamIds['Lupusius'].','
			.$playerOrTeamIds['Lurchi09'].','
			.$playerOrTeamIds['Oglie'].','
			.$playerOrTeamIds['TomotX'].','
			.$playerOrTeamIds['Viper6'].'/entry?api_key='
			.$apikey;
	$jsonLeague = file_get_contents($urlLeague);
	$objLeague = json_decode($jsonLeague,true);


	//--- Methodes ---
	foreach ($objLeague as $key0 => $leagues) {
		foreach ($leagues as $key1 => $league) {		
			$query = '
				INSERT INTO league (
					playerOrTeamId,
					playerOrTeamName,
					queue,
					tier,
					name,
					division,
					leaguePoints,
					wins,
					losses,
					isHotStreak,
					isFreshBlood,
					isVeteran,
					isInactive,
					seriesWins,
					seriesLosses,
					seriesTarget,
					seriesProgress
				)
				VALUES (
					"'.$league['entries'][0]['playerOrTeamId'].'",
					"'.$league['entries'][0]['playerOrTeamName'].'",
					"'.$league['queue'].'",
					"'.$league['tier'].'",
					"'.$league['name'].'",
					"'.$league['entries'][0]['division'].'",
					'.$league['entries'][0]['leaguePoints'].',
					'.$league['entries'][0]['wins'].',
					'.$league['entries'][0]['losses'].',
					'.($league['entries'][0]['isHotStreak']>0?1:0).',
					'.($league['entries'][0]['isFreshBlood']>0?1:0).',
					'.($league['entries'][0]['isVeteran']>0?1:0).',
					'.($league['entries'][0]['isInactive']>0?1:0).',
			';
			//Fallunterscheidung: in Promoserie?
			if(array_key_exists('miniSeries', $league['entries'][0])) {
				$query .= '
						'.$league['entries'][0]['miniSeries']['wins'].',
						'.$league['entries'][0]['miniSeries']['losses'].',
						'.$league['entries'][0]['miniSeries']['target'].',
						"'.$league['entries'][0]['miniSeries']['progress'].'"
				';
			} else {
				$query .= '
						NULL,
						NULL,
						NULL,
						NULL
				';
			}
			$query .= '
				)
				ON DUPLICATE KEY UPDATE
					playerOrTeamId 		= VALUES(playerOrTeamId),
					playerOrTeamName	= VALUES(playerOrTeamName),
					queue 				= VALUES(queue),
					tier 				= VALUES(tier),
					name 				= VALUES(name),
					division 			= VALUES(division),
					leaguePoints 		= VALUES(leaguePoints),
					wins 				= VALUES(wins),
					losses 				= VALUES(losses),
					isHotStreak 		= VALUES(isHotStreak),
					isFreshBlood 		= VALUES(isFreshBlood),
					isVeteran 			= VALUES(isVeteran),
					isInactive 			= VALUES(isInactive),
					seriesWins 			= VALUES(seriesWins),
					seriesLosses 		= VALUES(seriesLosses),
					seriesTarget 		= VALUES(seriesTarget),
					seriesProgress 		= VALUES(seriesProgress)
			';
			print $query;
			$mysqli->query($query);
			unset($query);
		}
	}
	unset($urlLeague,$jsonLeague,$objLeague);


	//--- MySQLi disconnection ---
	$mysqli->close();
?>

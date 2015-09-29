<?
	//MySQLi connection
	$mysqli = new mysqli('localhost', 'rclsirzj_lolmeta', 'LoLMeta42', 'rclsirzj_matchHistory');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}

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
	$url =
		'https://euw.api.pvp.net/api/lol/euw/v2.5/league/by-summoner/'
			.$playerOrTeamIds['Biberman'].','
			.$playerOrTeamIds['Lupusius'].','
			.$playerOrTeamIds['Lurchi09'].','
			.$playerOrTeamIds['Oglie'].','
			.$playerOrTeamIds['TomotX'].','
			.$playerOrTeamIds['Viper6'].
		'/entry?api_key=92d4e772-44cf-4bc8-9782-236d2c18fcc0';
	$json = file_get_contents($url);
	$obj = json_decode($json, true);

	//insert into DB matchHistory
	foreach ($obj as $key0 => $value0) {
		foreach ($value0 as $key => $value) {		
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
					"'.$value['entries'][0]['playerOrTeamId'].'",
					"'.$value['entries'][0]['playerOrTeamName'].'",
					"'.$value['queue'].'",
					"'.$value['tier'].'",
					"'.$value['name'].'",
					"'.$value['entries'][0]['division'].'",
					'.$value['entries'][0]['leaguePoints'].',
					'.$value['entries'][0]['wins'].',
					'.$value['entries'][0]['losses'].',
					'.($value['entries'][0]['isHotStreak']>0?1:0).',
					'.($value['entries'][0]['isFreshBlood']>0?1:0).',
					'.($value['entries'][0]['isVeteran']>0?1:0).',
					'.($value['entries'][0]['isInactive']>0?1:0).',
					NULL,
					NULL,
					NULL,
					NULL
				)
				ON DUPLICATE KEY UPDATE
					playerOrTeamName	=	VALUES(playerOrTeamName),
					queue 				=	VALUES(queue),
					tier 				= 	VALUES(tier),
					name 				= 	VALUES(name),
					division 			= 	VALUES(division),
					leaguePoints 		= 	VALUES(leaguePoints),
					wins 				= 	VALUES(wins),
					losses 				= 	VALUES(losses),
					isHotStreak 		= 	VALUES(isHotStreak),
					isFreshBlood 		= 	VALUES(isFreshBlood),
					isVeteran 			= 	VALUES(isVeteran),
					isInactive 			= 	VALUES(isInactive),
					seriesWins 			= 	VALUES(seriesWins),
					seriesLosses 		= 	VALUES(seriesLosses),
					seriesTarget 		= 	VALUES(seriesTarget),
					seriesProgress 		= 	VALUES(seriesProgress)
			';
			$mysqli->query($query);
			unset($query);

			//falls in Promoserie
			if(isset($value[0]['entries'][0]['miniSeries'])){
				$query = '
					INSERT INTO league (
						playerOrTeamId,
						seriesWins,
						seriesLosses,
						seriesTarget,
						seriesProgress
					)
					VALUES (
						"'.$value['entries'][0]['playerOrTeamId'].'",
						'.$value['entries'][0]['miniSeries']['wins'].',
						'.$value['entries'][0]['miniSeries']['losses'].',
						'.$value['entries'][0]['miniSeries']['target'].',
						"'.$value['entries'][0]['miniSeries']['progress'].'"
					)
					ON DUPLICATE KEY UPDATE
						seriesWins 		= 	VALUES(seriesWins),
						seriesLosses 	= 	VALUES(seriesLosses),
						seriesTarget 	= 	VALUES(seriesTarget),
						seriesProgress 	= 	VALUES(seriesProgress)
				';
				$mysqli->query($query);
				unset($query);
			}
		}
	}
	unset($url,$json,$obj);

	//MySQLi disconnection
	$mysqli->close();
?>

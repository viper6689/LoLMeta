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
	$seasons = 'SEASON2015';


	//--- Methodes ---
	foreach ($summonerIds as $key0 => $summonerId) {
		//get JSON matchlist-v2.2
		$urlMatchList =
			'https://euw.api.pvp.net/api/lol/euw/v2.2/matchlist/by-summoner/'
			.$summonerId.'?seasons='
			.$seasons.'&api_key='
			.$apikey
		;
		$jsonMatchlist = file_get_contents($urlMatchList);
		$objMatchlist = json_decode($jsonMatchlist,true);

		//wenn JSON erhalten, dann jedes Match in DB einfügen
		if(is_array($objMatchlist)) {
			foreach ($objMatchlist['matches'] as $key1 => $matchlistMatch) {
				$query = '
					INSERT INTO summonerMatchstats (
						matchId,
						summonerId,
						championId,
						lane,
						role,
						complete
					)
					VALUES (
						'.$matchlistMatch['matchId'].',
						'.$summonerId.',
						'.$matchlistMatch['champion'].',
						"'.$matchlistMatch['lane'].'",
						"'.$matchlistMatch['role'].'",
						0
					)
				';
				$mysqli->query($query);
				unset($query);
			}
		}
		unset($urlMatchList,$jsonMatchlist,$objMatchlist);
	}


	//--- MySQLi disconnection ---
	$mysqli->close();
?>

<?
	//MySQLi connection
	$mysqli = new mysqli('mysql7.000webhost.com', 'a7326768_lolmeta', 'lolmeta42', 'a7326768_lolmeta');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}

	//summonerIDs
	$summonerId = array(
	    "biberman"   => "40023404",
	    "lupusius" => "38527580",
		"lurchi09" => "40105796",
		"oglie"  => "39999855",
		"tomotx"  => "41739529",
	    "viper6" => "38598275"
	    );
/*
	//table summonerInfo reset
	$query = "TRUNCATE TABLE summonerInfo";
	$mysqli->query($query);
	unset($query);
*/
	//summonerInfo
	foreach ($summonerId as $key => $value) {
		//get JSON summonerinfo
		$json = file_get_contents("https://euw.api.pvp.net/api/lol/euw/v2.5/league/by-summoner/$value/entry?api_key=92d4e772-44cf-4bc8-9782-236d2c18fcc0");
		$obj = json_decode($json, true);
/*
		$query = '	INSERT INTO summonerInfo (
							summonerID, 
							name,
							league,
							division,
							points)
						VALUES (
							'.$obj[$value][0][entries][0][playerOrTeamId].',  
							"'.$obj[$value][0][entries][0][playerOrTeamName].'", 
							"'.$obj[$value][0][tier].'",
							"'.$obj[$value][0][entries][0][division].'",
							'.$obj[$value][0][entries][0][leaguePoints].')';
*/
		$query = '	UPDATE summonerInfo
					SET name 		= "'.$obj[$value][0][entries][0][playerOrTeamName].'",
						league 		= "'.$obj[$value][0][tier].'",
						division	= "'.$obj[$value][0][entries][0][division].'",
						points		= '.$obj[$value][0][entries][0][leaguePoints].'
					WHERE summonerID = '.$obj[$value][0][entries][0][playerOrTeamId];

		$mysqli->query($query);
		unset($query);
	}

	//MySQLi disconnection
	$mysqli->close();
?>

<?
	//MySQLi connection
	$mysqli = new mysqli('mysql7.000webhost.com', 'a7326768_lolmeta', 'lolmeta42', 'a7326768_lolmeta');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}

	//summonerIDs
	$summonerId = array(
	    "biberman"	=> "40023404",
	    "lupusius"	=> "38527580",
		"lurchi09"	=> "40105796",
		"oglie"		=> "39999855",
		"tomotx"	=> "41739529",
	    "viper6"	=> "38598275"
	    );
	
	//matchHistories
	foreach ($summonerId as $key => $value) {
		//get db matchHistory
		$history = array();
		if($result = $mysqli->query("SELECT matchID FROM matchHistory WHERE summonerID=$value")){
			while ($row = $result->fetch_assoc()) {
				array_push($history, $row[matchID]);
			}
			unset($row);
	    	$result->close();
		}

		//get JSON matchhistory
		$obj = array();
		$json = file_get_contents("https://euw.api.pvp.net/api/lol/euw/v2.2/matchhistory/$value?api_key=92d4e772-44cf-4bc8-9782-236d2c18fcc0");
		$obj = json_decode($json, true);

		//matchHistory
		foreach ($obj[matches] as $key2 => $value2) {
			if (!(in_array($value2[matchId], $history))) {
				$query = '	INSERT INTO matchHistory (
								summonerID,
								matchID,
								queueType,
								creation,
								champ,
								lane,
								duration,
								kills,
								deaths,
								assists,
								win)
							VALUES (
								'.$value.', 
								'.$value2[matchId].',
								"'.$value2[queueType].'",
								'.$value2[matchCreation].', 
								'.$value2[participants][0][championId].', 
								"'.$value2[participants][0][timeline][lane].'", 
								'.$value2[matchDuration].', 
								'.$value2[participants][0][stats][kills].', 
								'.$value2[participants][0][stats][deaths].', 
								'.$value2[participants][0][stats][assists].', 
								'.($value2[participants][0][stats][winner] == '1' ? "1" : "0").')';
				$mysqli->query($query);
				unset($query);
			}
		}
		unset($history, $obj);
	}

	//MySQLi disconnection
	$mysqli->close();
?>

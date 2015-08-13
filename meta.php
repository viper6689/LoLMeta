<?
	//MySQLi connection
	$mysqli = new mysqli('localhost', 'rclsirzj_lolmeta', 'LoLMeta42', 'rclsirzj_lolmeta');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}

	// loeschen der Tabelleninhalte
	$query = "TRUNCATE TABLE champs";
	$mysqli->query($query);
	$query = "TRUNCATE TABLE counters";
	$mysqli->query($query);
	unset($query);


	//import table data "champs"
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "champion.gg/statistics");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($ch);
	curl_close($ch);

	preg_match_all('/"key"."(?P<name>\w+)","role"."(?P<role>\w+)".*?"overallPosition".(?P<rank>\d+),/', $output, $matches, PREG_SET_ORDER);
	foreach ($matches as $key => $value){
		if($value['rank']<11){
			$query = "	INSERT INTO champs
						(name, role, rank)
						VALUES 
						('$value[name]',  '$value[role]', $value[rank])";
			$mysqli->query($query);
		}
	}
	unset($output, $matches, $value, $key);

	//import table data "counters"
	if ($result = $mysqli->query("SELECT * FROM champs WHERE role='Top' OR role='Middle'")) {
	    while($row = $result->fetch_assoc()){
        	$ch = curl_init();
        	curl_setopt($ch, CURLOPT_URL, "champion.gg/champion/$row[name]/$row[role]");
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        	$output = curl_exec($ch);
        	curl_close($ch);

        	preg_match_all('/"games".\d{3,},"statScore".(?P<rating1>\d+).(?P<rating2>\d+).*?"key"."(?P<counter>\w+)"/', $output, $matches, PREG_SET_ORDER);
	        foreach ($matches as $key => $value){
	        	if($result2 = $mysqli->query("SELECT * FROM champs WHERE role='$row[role]' AND name='$value[counter]'")){
	        		$row2 = $result2->fetch_assoc();
	        		if($row2['index']>0){
						$query = "	INSERT INTO counters
									(champ, counter, rating)
									VALUES 
									($row[index],  $row2[index], $value[rating1].$value[rating2])";
						$mysqli->query($query);
	        		}
	        		$result2->close();
	        	}
			}
			unset($output, $matches, $value, $key);
    	}
	    $result->close();
	}

	//MySQLi disconnection
	$mysqli->close();
?>

<?php
	/* --- CRON JOB (every 1 minute) --- */

	//MySQLi connection
	$mysqli = new mysqli('localhost', 'rclsirzj_lolmeta', 'LoLMeta42', 'rclsirzj_matchHistory');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}

	//globaleVariable
    $result = $mysqli->query('SELECT globeVar FROM dbVar LIMIT 1');
	$globaleVar = $result->fetch_assoc();
   	$result->close();

    switch ($globaleVar['globeVar']) {
    	case '1':
    		$cronUrl = "http://lolmeta.serverlux.me/matchlist.php";
    		break;
    	case '2':
    		$cronUrl = "http://lolmeta.serverlux.me/match.php";
    		break;

    	default:
    		$cronUrl = NULL;
    		break;
    }

    function curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    echo curl($cronUrl);

    if ($globaleVar['globeVar'] > 1) {
    	$mysqli->query('UPDATE dbVar SET globeVar = 1');
    } else {
    	$mysqli->query('UPDATE dbVar SET globeVar = globeVar+1');	
    }
    
    //MySQLi disconnection
	$mysqli->close();
?>

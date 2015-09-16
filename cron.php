<?
	/* --- CRON JOB (every 1 min) ---- */

	//MySQLi connection
	$mysqli = new mysqli('localhost', 'rclsirzj_lolmeta', 'LoLMeta42', 'rclsirzj_lolmeta');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}

	//globaleVariable
    $result = $mysqli->query('SELECT globeVar FROM dbVar LIMIT 1');
	$globaleVar = $result->fetch_assoc();
   	$result->close();

    switch ($globaleVar['globeVar']) {
    	case '1':
    		$cronUrl="http://lolmeta.serverlux.me/matchHistory.php?summonerID=40023404";
    		break;
    	case '2':
    		$cronUrl="http://lolmeta.serverlux.me/matchHistory.php?summonerID=38527580";
    		break;
		case '3':
    		$cronUrl="http://lolmeta.serverlux.me/matchHistory.php?summonerID=40105796";
    		break;
    	case '4':
    		$cronUrl="http://lolmeta.serverlux.me/matchHistory.php?summonerID=39999855";
    		break;
    	case '5':
    		$cronUrl="http://lolmeta.serverlux.me/matchHistory.php?summonerID=41739529";
    		break;
    	case '6':
            $cronUrl="http://lolmeta.serverlux.me/matchHistory.php?summonerID=38598275";
            break;
        case '7':
            $cronUrl="http://lolmeta.serverlux.me/teamHistory.php";
            break;

    	default:
    		$cronUrl=NULL;
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

    if ($globaleVar['globeVar']>6) {
    	$mysqli->query('UPDATE dbVar SET globeVar = 1');
    }else{
    	$mysqli->query('UPDATE dbVar SET globeVar = globeVar+1');	
    }
    
    //MySQLi disconnection
	$mysqli->close();
?>

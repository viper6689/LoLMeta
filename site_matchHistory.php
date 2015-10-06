<?
	//MySQLi connection
	$mysqli = new mysqli('localhost', 'rclsirzj_lolmeta', 'LoLMeta42', 'rclsirzj_matchHistory');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}

	$maxHistoryEntries = 10;

	$ddragonChampionsUrl = "http://ddragon.leagueoflegends.com/cdn/5.19.1/data/en_US/champion.json";
	$ddragonChampionsJson = file_get_contents($ddragonChampionsUrl);
	$ddragonChampions = json_decode($ddragonChampionsJson, true);

	$summonerIds = array(
		'Biberman'	=> "40023404",
	    'Lupusius'	=> "38527580",
		'Lurchi09'	=> "40105796",
		'Oglie'		=> "39999855",
		'TomotX'	=> "41739529",
	    'Viper6'	=> "38598275"
	);

	//Ligainfos der SoloQue Spieler
	$query = '
		SELECT
			playerOrTeamId,
			playerOrTeamName,
			queue,
			tier,
			name,
			division,
			leaguePoints
		FROM league
		WHERE queue = "RANKED_SOLO_5x5"
		ORDER BY playerOrTeamName
	';
	$summonerLeagues = array();
	if($result = $mysqli->query($query)){
		while ($row = $result->fetch_assoc()) {
			$summonerLeagues[] = $row;
		}
		unset($row);
    	$result->close();
    }
    unset($query);

	//MatchHistory der SoloQue Spieler
	$query = '
		SELECT
			mp.matchId,
			mp.summonerId,
			mp.winner,
			mp.championId,
			mp.kills,
			mp.deaths,
			mp.assists,
			m.queueType,
			m.matchCreation,
			m.matchDuration,
			m.season,
			ml.lane
		FROM matchParticipants mp
			LEFT JOIN `match` m
				ON m.matchId = mp.matchId
			LEFT JOIN matchlist ml
				ON ml.matchId = mp.matchId AND ml.summonerId = mp.summonerId
		WHERE m.queueType="RANKED_SOLO_5x5"
		ORDER BY m.matchCreation DESC
	';
    $historys = array();
	if($result = $mysqli->query($query)){
		while ($row = $result->fetch_assoc()) {
			$historys[] = $row;
		}
		unset($row);
    	$result->close();
    }
    unset($query);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ForGoodoneBadGuy</title>
	<link rel="stylesheet" type="text/css" href="styles.css" />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<link rel="icon" href="/favicon.ico" type="image/x-icon" />
</head>
<body>

<div id="summonerOverview">
	<?
		echo '
			<table>
				<tr>
		';

        foreach ($summonerLeagues as $key1 => $summonerLeague) {
        	echo '
        			<td>	
						<table>
							<tr>
								<td colspan="4">
									<center><b>'.$summonerLeague['playerOrTeamName'].'</b></center>
								</td>
							</tr>
							<tr>
								<td>
									<img src="/icons/'.$summonerLeague['playerOrTeamName'].'Icon.png" width="20" hight="45">
								</td>
								<td>
									<img src="/icons/'.$summonerLeague['tier'].'.png" width="80" hight="80">
								</td>
								<td>
									<img src="/icons/'.$summonerLeague['division'].'.png" width="40" hight="40">
								</td>
								<td>
									<font size="6"><b>'.$summonerLeague['leaguePoints'].'</b></font>
								</td>
							</tr>
							<tr>
								<td colspan="4">
			';
			$historyEntries = 0;
			foreach ($historys as $key2 => $history) {
				if ($historyEntries < $maxHistoryEntries && $history['summonerId'] == $summonerLeague['playerOrTeamId']) {
					echo '		
									<table bgcolor="'.($history['winner'] == '1' ? "green" : "FireBrick").'" width="200px">
										<tr>
											<td colspan="3">
												'.time_elapsed_string(unixTimeToSeconds($history['matchCreation'])+$history['matchDuration']).'
											</td>
										</tr>
										<tr>
											<td rowspan=2>
												<img src="http://ddragon.leagueoflegends.com/cdn/5.15.1/img/champion/'.champIdToChampName($history['championId'], $ddragonChampions).'.png" width="45" hight="45">
											</td>
											<td rowspan=2>
												<img src="/icons/lane_'.$history['lane'].'.png" width="45" hight="45">
											</td>
											<td>
												<center>'.gmdate("H:i:s", $history['matchDuration']).'</center>
											</td>
										</tr>
										<tr>
											<td>
												<center><b><font color="white">'.$history['kills'].' </font>|<font color="white"> '.$history['deaths'].' </font>|<font color="white"> '.$history['assists'].'</font></b></center>
											</td>
										</tr>
									</table>
					';
					$historyEntries++;
				}
			}
			echo '				</td>
							</tr>
						</table>
					</td>
			';
        }
		echo '
				<tr>
			</table>
		';
	?>
</div>

</body>
</html>

<?
	function time_elapsed_string($ptime)
	{
	    $etime = time() - $ptime;

	    if ($etime < 1)
	    {
	        return '0 seconds';
	    }

	    $a = array( 365 * 24 * 60 * 60  =>  'year',
	                 30 * 24 * 60 * 60  =>  'month',
	                      24 * 60 * 60  =>  'day',
	                           60 * 60  =>  'hour',
	                                60  =>  'minute',
	                                 1  =>  'second'
	  	);
	    $a_plural = array( 'year'   => 'years',
	                       'month'  => 'months',
	                       'day'    => 'days',
	                       'hour'   => 'hours',
	                       'minute' => 'minutes',
	                       'second' => 'seconds'
		);

	    foreach ($a as $secs => $str)
	    {
	        $d = $etime / $secs;
	        if ($d >= 1)
	        {
	            $r = round($d);
	            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
	        }
	    }
	}

	function unixTimeToSeconds($in)
	{
		return round($in/1000);
	}
	
	function champIdToChampName($in, $ddragonChampions)
	{
		foreach ($ddragonChampions['data'] as $key => $value) {
			if($value['key']==$in){
				return $value['id'];
			}
		}
	}

	//MySQLi disconnection
	$mysqli->close();
?>

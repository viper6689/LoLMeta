<?
	//--- MySQLi connection ---
	$mysqli = new mysqli('localhost', 'rclsirzj_lolmeta', 'LoLMeta42', 'rclsirzj_matchHistory');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}


	//--- Variables ---
	$maxHistoryEntries = 10;

	$urlDdragonChampions = 'http://ddragon.leagueoflegends.com/cdn/5.19.1/data/en_US/champion.json';
	$jsonDdragonChampionsJson = file_get_contents($urlDdragonChampions);
	$objDdragonChampions = json_decode($jsonDdragonChampionsJson,true);

	//Ligainfos der SoloQue Spieler
	$query = '
		SELECT
			playerOrTeamId,
			playerOrTeamName,
			queue,
			tier,
			name,
			division,
			leaguePoints,
			seriesProgress
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

	//Ligainfos der 3er Teams
	$query = '
		SELECT
			playerOrTeamId,
			playerOrTeamName,
			queue,
			tier,
			name,
			division,
			leaguePoints,
			seriesProgress
		FROM league
		WHERE queue = "RANKED_TEAM_3x3"
		ORDER BY playerOrTeamName
	';
	$team3Leagues = array();
	if($result = $mysqli->query($query)){
		while ($row = $result->fetch_assoc()) {
			$team3Leagues[] = $row;
		}
		unset($row);
    	$result->close();
    }
    unset($query);

	//MatchHistory der SoloQue Spieler
	$query = '
		SELECT
			sm.matchId,
			sm.summonerId,
			sm.championId,
			sm.lane,
			sm.role,
			sm.win,
			sm.kills,
			sm.deaths,
			sm.assists,
			m.queueType,
			m.matchCreation,
			m.matchDuration,
			m.season
		FROM summonerMatchstats sm
			LEFT JOIN matches m
				ON m.matchId = sm.matchId
		ORDER BY m.matchCreation DESC, sm.summonerId
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

    //MatchHistory der Teams
	$query = '
		SELECT
			th.matchId,
			th.teamId,
			th.win,
			th.kills,
			th.deaths,
			th.assists,
			m.queueType,
			m.matchCreation,
			m.matchDuration,
			m.season
		FROM teamHistory th
			LEFT JOIN matches m
				ON m.matchId = th.matchId
		ORDER BY m.matchCreation DESC
	';
    $teamHistories = array();
	if($result = $mysqli->query($query)){
		while ($row = $result->fetch_assoc()) {
			$teamHistories[] = $row;
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

        foreach ($summonerLeagues as $key0 => $summonerLeague) {
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
									<font size="6"><b>'.leaguePointsOrSeries($summonerLeague).'</b></font>
								</td>
							</tr>
							<tr>
								<td colspan="4">
			';
			$historyEntries = 0;
			foreach ($historys as $key1 => $history) {
				if ($history['summonerId'] == $summonerLeague['playerOrTeamId'] && $history['queueType'] == 'RANKED_SOLO_5x5') {
					echo '		
									<table bgcolor="'.($history['win']==1?'green':'firebrick').'" width="200px">
										<tr>
											<td colspan="3">
												'.time_elapsed_string(unixTimeToSeconds($history['matchCreation'])+$history['matchDuration']).'
											</td>
										</tr>
										<tr>
											<td rowspan=2>
												<img src="http://ddragon.leagueoflegends.com/cdn/5.15.1/img/champion/'.champIdToChampName($history['championId'],$objDdragonChampions).'.png" width="45" hight="45">
											</td>
											<td rowspan=2>
												<img src="/icons/lane_'.laneWithRole($history).'.png" width="45" hight="45">
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
					if (++$historyEntries >= $maxHistoryEntries) break;
				}
			}
			unset($historyEntries);
			echo '				</td>
							</tr>
						</table>
					</td>
			';
        }
		echo '
				</tr>
			</table>
		';
	?>
</div>

<div id="teamOverview">
	<?
		echo '
			<table>
				<tr>
		';

		foreach ($team3Leagues as $key0 => $team3League) {
			echo '
        			<td>	
						<table>
							<tr>
								<td colspan="4">
									<center><b>'.$team3League['playerOrTeamName'].'</b></center>
								</td>
							</tr>
							<tr>
								<td>
									<img src="/icons/'.($team3League['queue']=='RANKED_TEAM_3x3'?'3v3':'5v5').'Icon.png" width="20" hight="45">
								</td>
								<td>
									<img src="/icons/'.$team3League['tier'].'.png" width="80" hight="80">
								</td>
								<td>
									<img src="/icons/'.$team3League['division'].'.png" width="40" hight="40">
								</td>
								<td>
									<font size="6"><b>'.leaguePointsOrSeries($team3League).'</b></font>
								</td>
							</tr>
							<tr>
								<td colspan="4">
			';
			$historyEntries = 0;
			foreach ($teamHistories as $key1 => $teamHistory) {
				if ($teamHistory['teamId'] == $team3League['playerOrTeamId'] && $teamHistory['queueType'] == 'RANKED_TEAM_3x3') {
					//suche die Teilnehmer zusammen
					$summonerWithChamp = array();
					foreach ($historys as $key2 => $history) {
						if ($history['matchId'] == $teamHistory['matchId']) {
							$summonerWithChamp[]['summonerId'] = $history['summonerId'];
							end($summonerWithChamp);
							$summonerWithChamp[key($summonerWithChamp)]['championId'] = $history['championId'];
						}
					}

					echo '		
									<table bgcolor="'.($teamHistory['win']==1?'green':'firebrick').'" width="220px">
										<tr>
											<td>
												'.time_elapsed_string(unixTimeToSeconds($teamHistory['matchCreation'])+$teamHistory['matchDuration']).'
											</td>
											<td>
												<center><b><font color="white">'.$teamHistory['kills'].' </font>|<font color="white"> '.$teamHistory['deaths'].' </font>|<font color="white"> '.$teamHistory['assists'].'</font></b></center>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<table>
													<tr>
														<td width="65px">
															<img src="icons/'.summonerIdToSummonerName($summonerWithChamp[0]['summonerId']).'Icon.png" width="20" hight="45"><img src="http://ddragon.leagueoflegends.com/cdn/5.15.1/img/champion/'.champIdToChampName($summonerWithChamp[0]['championId'],$objDdragonChampions).'.png" width="45" hight="45">
														</td>
														<td width="65px">
															<img src="icons/'.summonerIdToSummonerName($summonerWithChamp[1]['summonerId']).'Icon.png" width="20" hight="45"><img src="http://ddragon.leagueoflegends.com/cdn/5.15.1/img/champion/'.champIdToChampName($summonerWithChamp[1]['championId'],$objDdragonChampions).'.png" width="45" hight="45">
														</td>
														<td width="65px">
															<img src="icons/'.summonerIdToSummonerName($summonerWithChamp[2]['summonerId']).'Icon.png" width="20" hight="45"><img src="http://ddragon.leagueoflegends.com/cdn/5.15.1/img/champion/'.champIdToChampName($summonerWithChamp[2]['championId'],$objDdragonChampions).'.png" width="45" hight="45">
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
					';
					if (++$historyEntries >= $maxHistoryEntries) break;
				}
			}
			unset($historyEntries);
			echo '				</td>
							</tr>
						</table>
					</td>
			';
        }
		echo '
				</tr>
			</table>
		';
	?>
</div>

</body>
</html>

<?
	//--- Functions ---
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
			if($value['key']==$in) {
				return $value['id'];
			}
		}
		return 'unknown';
	}

	function laneWithRole($history)
	{
		if($history['lane'] == 'BOTTOM') {
			return $history['lane'].'_'.$history['role'];
		} else {
			return $history['lane'];
		}
	}

	function leaguePointsOrSeries($summonerLeague)
	{
		if (is_null($summonerLeague['seriesProgress'])) {
			return $summonerLeague['leaguePoints'];
		}
		else {
			return '<img src="/icons/series_'.$summonerLeague['seriesProgress'].'.png" width="40" hight="40">';
		}
	}

	function summonerIdToSummonerName($in)
	{
		switch ($in) {
			case '40023404':
				$out="Biberman";
				break;
			case '38527580':
				$out="Lupusius";
				break;
			case '40105796':
				$out="Lurchi09";
				break;
			case '39999855':
				$out="Oglie";
				break;
			case '41739529':
				$out="TomotX";
				break;
			case '38598275':
				$out="Viper6";
				break;
			
			default:
				$out="Unknown";
				break;
		}
		return $out;
	}


	//--- MySQLi disconnection ---
	$mysqli->close();
?>

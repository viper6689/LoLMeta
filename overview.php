<?
	//MySQLi connection
	$mysqli = new mysqli('localhost', 'rclsirzj_lolmeta', 'LoLMeta42', 'rclsirzj_lolmeta');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}

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

	function champIdToChampName($in)
	{
		$json = file_get_contents('http://ddragon.leagueoflegends.com/cdn/5.15.1/data/en_US/champion.json');
		$obj = json_decode($json, true);
		foreach ($obj['data'] as $key => $value) {
			if($value['key']==$in){
				return $value['id'];
			}
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
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>FourGoodOneBadGuy</title>
	<link rel="stylesheet" type="text/css" href="styles.css" />

	<link rel="shortcut icon" href="/lurchi/icons/favicon.ico" type="image/x-icon">
	<link rel="icon" href="/lurchi/icons/favicon.ico" type="image/x-icon">

</head>
<body>
	<div id="misc">
		<a href="/index.php">Meta</a>
	</div>
	<div id="summonerOverview">
		<?
			$summonerId = array(
			    "biberman"   => "40023404",
			    "lupusius" => "38527580",
				"lurchi09" => "40105796",
				"oglie"  => "39999855",
				"tomotx"  => "41739529",
			    "viper6" => "38598275"
			);

			echo '	<table><tr>';

			foreach ($summonerId as $key2 => $value2) {
				$summoner = array();
				if($result = $mysqli->query("SELECT * FROM summonerInfo WHERE summonerID=$value2")){
					while ($row = $result->fetch_assoc()) {
						$summoner = $row;
					}
					unset($row);
		        	$result->close();
		        }
				$history = array();
				if($result = $mysqli->query("SELECT * FROM matchHistory WHERE summonerID=$value2 AND queueType='RANKED_SOLO_5x5' ORDER BY creation DESC LIMIT 10")){
					while ($row = $result->fetch_assoc()) {
						$history[] = $row;
					}
					unset($row);
		        	$result->close();
		        }
		        $mainRole['lane'] = "UNKNOWN";
		        if($result = $mysqli->query("SELECT lane, COUNT(lane) AS LaneCount FROM matchHistory WHERE summonerID=$value2 AND queueType='RANKED_SOLO_5x5' GROUP BY lane ORDER BY LaneCount DESC LIMIT 1")){
		        	if (mysqli_num_rows($result)>0) {
		        		$mainRole = $result->fetch_assoc();
		        	}
					unset($row);
		        	$result->close();
		        }

				echo '<td>	
						<table>
							<tr>
								<td colspan="3">
									<center><b>'.$summoner['name'].'</b><img src="/icons/main_'.$mainRole['lane'].'.png"></center>
								</td>
							</tr>
							<tr>
								<td>
									<img src="/icons/'.$summoner['league'].'.png" width="80" hight="80">
								</td>
								<td>
									<img src="/icons/'.$summoner['division'].'.png" width="40" hight="40">
								</td>
								<td>
									'.$summoner['points'].'
								</td>
							</tr>
							<tr>
								<td colspan="3">
				';
				foreach ($history as $key => $value) {
					echo '		<table bgcolor="'.($value['win'] == '1' ? "green" : "FireBrick").'" width="200px">
									<tr>
										<td colspan="3">
											'.time_elapsed_string(unixTimeToSeconds($value['creation'])+$value['duration']).'
										</td>
									</tr>
									<tr>
										<td rowspan=2>
											<img src="http://ddragon.leagueoflegends.com/cdn/5.15.1/img/champion/'.champIdToChampName($value['champ']).'.png" width="45" hight="45">
										</td>
										<td rowspan=2>
											<img src="/icons/lane_'.$value['lane'].'.png" width="45" hight="45">
										</td>
										<td>
											<center>'.gmdate("H:i:s", $value['duration']).'</center>
										</td>
									</tr>
									<tr>
										<td>
											<center><b><font color="white">'.$value['kills'].' </font>|<font color="white"> '.$value['deaths'].' </font>|<font color="white"> '.$value['assists'].'</font></b></center>
										</td>
									</tr>
								</table>
					';
				}
				echo '			</td>
							</tr>
						</table>
					</td>
				';
			}
			echo '</tr></table>';
		?>
	</div>
	<div id="teamOverview">
		<?
			$team = array();
			if($result = $mysqli->query("SELECT * FROM teamInfo")){
				while ($row = $result->fetch_assoc()) {
					$team = $row;
				}
				unset($row);
	        	$result->close();
	        }
			$history = array();
			if($result = $mysqli->query("SELECT * FROM teamMatchHistory WHERE mapId=10 ORDER BY creation DESC LIMIT 10")){
				while ($row = $result->fetch_assoc()) {
					$history[] = $row;
				}
				unset($row);
	        	$result->close();
	        }
	        
			echo '<table>
					<tr>
						<td colspan="3">
							<center><b>3vs3</center>
						</td>
					</tr>
					<tr>
						<td>
							<img src="/icons/'.$team['league'].'.png" width="80" hight="80">
						</td>
						<td>
							<img src="/icons/'.$team['division'].'.png" width="40" hight="40">
						</td>
						<td>
							'.$team['points'].'
						</td>
					</tr>
					<tr>
						<td colspan="3">
			';
			foreach ($history as $key => $value) {
				
				$summonerWithChamp = array();
				if($result = $mysqli->query('SELECT summonerID,champ FROM matchHistory WHERE matchID='.$value['matchID'].' AND queueType="RANKED_TEAM_3x3"')){
					while ($row = $result->fetch_assoc()) {
						$summonerWithChamp[] = $row;
					}
					unset($row);
		        	$result->close();
		        }
		        //print_r($summonerWithChamp);

				echo '		<table bgcolor="'.($value['win'] == '1' ? "green" : "FireBrick").'" width="220px">
								<tr>
									<td>
										'.time_elapsed_string(unixTimeToSeconds($value['creation'])).'
									</td>
									<td>
										<center><b><font color="white">'.$value['kills'].' </font>|<font color="white"> '.$value['deaths'].' </font>|<font color="white"> '.$value['assists'].'</font></b></center>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<table>
											<tr>
												<td width="65px">
													<img src="icons/'.summonerIdToSummonerName($summonerWithChamp[0]['summonerID']).'Icon.png" width="20" hight="45"><img src="http://ddragon.leagueoflegends.com/cdn/5.15.1/img/champion/'.champIdToChampName($summonerWithChamp[0]['champ']).'.png" width="45" hight="45">
												</td>
												<td width="65px">
													<img src="icons/'.summonerIdToSummonerName($summonerWithChamp[1]['summonerID']).'Icon.png" width="20" hight="45"><img src="http://ddragon.leagueoflegends.com/cdn/5.15.1/img/champion/'.champIdToChampName($summonerWithChamp[1]['champ']).'.png" width="45" hight="45">
												</td>
												<td width="65px">
													<img src="icons/'.summonerIdToSummonerName($summonerWithChamp[2]['summonerID']).'Icon.png" width="20" hight="45"><img src="http://ddragon.leagueoflegends.com/cdn/5.15.1/img/champion/'.champIdToChampName($summonerWithChamp[2]['champ']).'.png" width="45" hight="45">
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
				';
			}
			echo '		</td>
					</tr>
				</table>
			';
		?>
	</div>
</body>
</html>

<?
	//MySQLi disconnection
	$mysqli->close();
?>

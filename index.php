<?	
	//MySQLi connection
	$mysqli = new mysqli('mysql7.000webhost.com', 'a7326768_lolmeta', 'lolmeta42', 'a7326768_lolmeta');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>LoLMeta</title>
	<link rel="stylesheet" type="text/css" href="styles.css" />
	<link rel="shortcut icon" href="/icons/favicon.ico" type="image/x-icon" />
</head>
<body>
	<div id="misc">
		<a href="http://www.mobafire.com/profile/vercetty-627507/content/builds" target="_blank">Builds</a>
	</div>

	<div id="champs">
		<table>
			<tr>
				<?
					if($result = $mysqli->query("SELECT DISTINCT role FROM champs")){
						while ($row = $result->fetch_assoc()) {
							$roles[] = $row;
						}
						unset($row);
			        	$result->close();
			        }

					foreach ($roles as $key => $value) {
						switch ($value[role]) {
										    case Top:
										        $color = "sienna";
										        break;
										    case Jungle:
										        $color = "green";
										        break;
										    case Middle:
										        $color = "royalblue";
										        break;
										    case ADC:
										        $color = "darkred";
										        break;
										    case Support:
										        $color = "goldenrod";
										        break;
										};
						echo "	<td>
									<table bgcolor='$color'>
										<tr>
											<th colspan='2'><div style='font-size: 150%'>$value[role]</div></th>
										</tr>
										<tr>
											<th>Rank</th>
											<th>Champ</th>
										</tr>";
					
			
						if($result = $mysqli->query("SELECT * FROM champs WHERE role='$value[role]' ORDER BY rank")){
							while ($row = $result->fetch_assoc()) {
								$champs[] = $row;
							}
							unset($row);
				        	$result->close();
				        }

						foreach ($champs as $key2 => $value2) {
							echo "		<tr>
											<td><div align='right' style='color:white; font-weight:bold'>$value2[rank]</div></td>
											<td><a href='http://champion.gg/champion/$value2[name]/$value2[role]'><img src='http://ddragon.leagueoflegends.com/cdn/5.15.1/img/champion/$value2[name].png' alt='$value2[name]' height='45' width='45'></a></td>
										</tr>";
						}
						unset($champs, $key2, $value2);
						echo "		</table>
								</td>";
					}
					unset($roles, $key, $value);
				?>
			</tr>
		</table>
	</div>

	<div id="counters">
		<table>
			<tr>
				<?
					$roles = array(array("role" => "Top"), array("role" =>  "Middle"));
					foreach ($roles as $key => $value) {
						switch ($value[role]) {
						    case Top:
						        $color = "sienna";
						        break;
						    case Jungle:
						        $color = "green";
						        break;
						    case Middle:
						        $color = "royalblue";
						        break;
						    case ADC:
						        $color = "darkred";
						        break;
						    case Support:
						        $color = "goldenrod";
						        break;
						};
						echo "	<td>
									<table bgcolor='$color'>
										<tr>
											<th colspan='2'><div style='font-size: 150%'>$value[role]</div></th>
										</tr>
										<tr>
											<th>Champ</th>
											<th>Counter</th>
										</tr>";

						$SQLquery = "	SELECT champname, name AS countername, rating, champrank, role
										FROM (	SELECT counter, name AS champname, rating, rank AS champrank
												FROM counters LEFT JOIN champs ON (counters.champ=champs.index)) AS counterschamp								
										LEFT JOIN champs ON (counterschamp.counter=champs.index)
										WHERE role='$value[role]' AND rating<3.5
										ORDER BY champrank, rating
									";
						if($result = $mysqli->query($SQLquery)){
							while ($row = $result->fetch_assoc()) {
								$counters[] = $row;
							}
							unset($row);
				        	$result->close();
				        }

				        foreach ($counters as $key2 => $value2) {
				        	$champs[$value2[champname]] = $value2[champname];
				        }
				        unset($key2, $value2);
				        array_unique($champs);
						foreach ($champs as $key2 => $value2){
							echo "		<tr>
											<td><img src='http://ddragon.leagueoflegends.com/cdn/5.15.1/img/champion/"."$value2".".png' alt='$value2' height='45' width='45'></td>
											<td>
												<table border='1'>";
							foreach ($counters as $key3 => $value3) {
								if(strcmp($value3[champname], $value2)==0){
									echo "			<tr>
														<td><div style='color:white'>$value3[countername]</div></td>
													</tr>";
								}
							}
							echo "				</table>
											</td>
										</tr>";
						}
						unset($counters, $champs, $key2, $value2);
						echo "		</table>
								</td>";
					}
					unset($roles, $key, $value);
				?>
			</tr>
		</table>
	</div>
</body>
</html>

<? $mysqli->close(); ?>

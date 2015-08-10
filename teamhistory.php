<?
	//MySQLi connection
	$mysqli = new mysqli('mysql7.000webhost.com', 'a7326768_lolmeta', 'lolmeta42', 'a7326768_lolmeta');
	if($mysqli->connect_errno > 0){
	   	die('Unable to connect to database [' . $mysqli->connect_error . ']');
	}

	$teamId = "TEAM-1cc4ac30-3bfe-11e3-b84d-782bcb4ce61a";

	//get JSON teaminfo
	$json = file_get_contents("https://euw.api.pvp.net/api/lol/euw/v2.5/league/by-team/$teamId/entry?api_key=92d4e772-44cf-4bc8-9782-236d2c18fcc0");
	$obj = json_decode($json, true);
/*
	$query = '	INSERT INTO teamInfo (
						teamID, 
						name,
						league,
						division,
						points)
					VALUES (
						"'.$obj[$teamId][0][entries][0][playerOrTeamId].'",  
						"'.$obj[$teamId][0][entries][0][playerOrTeamName].'", 
						"'.$obj[$teamId][0][tier].'",
						"'.$obj[$teamId][0][entries][0][division].'",
						'.$obj[$teamId][0][entries][0][leaguePoints].')';
*/
	$query = '	UPDATE teamInfo
				SET name 		= "'.$obj[$teamId][0][entries][0][playerOrTeamName].'",
					league 		= "'.$obj[$teamId][0][tier].'",
					division	= "'.$obj[$teamId][0][entries][0][division].'",
					points		= '.$obj[$teamId][0][entries][0][leaguePoints].'
				WHERE teamID = "'.$obj[$teamId][0][entries][0][playerOrTeamId].'"';

	$mysqli->query($query);
	unset($query);


	//MySQLi disconnection
	$mysqli->close();



//https://euw.api.pvp.net/api/lol/euw/v2.4/team/TEAM-1cc4ac30-3bfe-11e3-b84d-782bcb4ce61a?api_key=92d4e772-44cf-4bc8-9782-236d2c18fcc0
//https://euw.api.pvp.net/api/lol/euw/v2.5/league/by-team/TEAM-1cc4ac30-3bfe-11e3-b84d-782bcb4ce61a/entry?api_key=92d4e772-44cf-4bc8-9782-236d2c18fcc0
?>

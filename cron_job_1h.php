<?php
	/* --- CRON JOB (every 1 hour) --- */

	file_get_contents('http://lolmeta.serverlux.me/meta.php');
	file_get_contents('http://lolmeta.serverlux.me/league.php');
?>

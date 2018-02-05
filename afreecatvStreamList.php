<?php

require_once("vendor/autoload.php");

try {
	$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
	$opt = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	$pdo = new PDO($dsn, DB_USER, DB_PASS, $opt);
	if ($pdo->connect_error){
		LogError("DB connection error: " . $conn->connect_error . "\n");
		die;
	} 
	$sql = "SELECT * FROM afreecatv_streams WHERE 1";
	$result = $pdo->query($sql);
	
	if ($result->rowCount() > 0) {
		echo $result->rowCount() . " streams were found in database: <br />";
	} else{
		LogError("AfreecaTV streams were not found in database\n");
		die;
	}

	foreach($result as $rows)
	{
		echo $rows['name'] . " ";
	}
	
} catch (PDOException $e) {
	LogError("Database PDO exception" . $e->getMessage() . " \n");
	die;
}
$pdo = null;
?>
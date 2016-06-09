<?php
require("../includes/init.php");
require("../includes/pdo.php");
$nome = $_GET['q'];

$q= "SELECT id,nome as name FROM alumni where nome LIKE '%$nome%' LIMIT 0,10";
$result = $cn->query($q);
$rows = $result->fetchAll( PDO::FETCH_ASSOC );
echo json_encode($rows);

<?php 
include('_header.php'); 


echo "<h1>Pag Inicial</h1><br><hr><br>";

$q = "select count(id) as tot from alumni;";
$result = $cn->query($q);
$tot = $result->fetchColumn();
echo '<h1 class="count">Total : '. $tot .'</h1>'; 

$q = "select count(id) as tot from alumni where captura=1 and parse=0;";
$result = $cn->query($q);
$tot = $result->fetchColumn();
echo '<h1 class="count">Nao parseados : '. $tot .'</h1>'; 


$q = "select count(id) as tot from alumni where parse=1;";
$result = $cn->query($q);
$tot = $result->fetchColumn();
echo '<h1 class="count">Alumnis : '. $tot .'</h1>'; 


echo "<pre>";
$q = "SELECT count(id) as tot , alumni_id FROM jobs where alumni_id<>0 group by alumni_id order by tot desc limit 20;";
$result = $cn->query($q);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
echo '<h3>Ranking Jobs - Top 20</h3>'; 
print_r($rows);
echo "</pre>";


echo "<pre>";
$q = "SELECT count(id) as tot , alumni_id FROM edus where alumni_id<>0 group by alumni_id order by tot desc limit 20;";
$result = $cn->query($q);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
echo '<h3>Ranking Edus - Top 20</h3>'; 
print_r($rows);
echo "</pre>";



include('_footer.php');
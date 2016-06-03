<?php
echo "<pre>";
$cn = new PDO("mysql:host=localhost;dbname=mestrado","root","");
$q = "select * from perfil;";

$result = $cn->query($q);
$rows = $result->fetchAll( PDO::FETCH_ASSOC );

$top = $rows[2]['top'];
$html = $rows[2]['html'];
$alumni_id = $rows[2]['alumni_id'];
#print_r($top);die;

echo "<hr>";
$q = "select * from jobs where alumni_id=".$alumni_id.";";
$result = $cn->query($q);
$jobs = $result->fetchAll( PDO::FETCH_ASSOC );

print_r($jobs);

echo "<hr>";
$q = "select * from edus where alumni_id=".$alumni_id.";";
$result = $cn->query($q);
$edus = $result->fetchAll( PDO::FETCH_ASSOC );

print_r($edus);
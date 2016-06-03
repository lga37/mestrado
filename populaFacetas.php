<?php
echo "<pre>";
$cn = new PDO("mysql:host=localhost;dbname=mestrado","root","");


/*
#testando as rotas
$results = $cn->query("select rota from rotas where feita=0 order by time;");
$rotas = $results->fetchAll(PDO::FETCH_ASSOC);
$rota=$rotas[3];
print_r($rota['rota']);

foreach($rotas as $rota){
    $u = "https://www.linkedin.com/edu/alumni?id=10582&facets=".$rota['rota'];
    echo $u,'<br>';
}
*/
######################################################

$q = "select * from facetas;";

$result = $cn->query($q);
$rows = $result->fetchAll( PDO::FETCH_ASSOC );
$ul = $rows[0]['ul'];

$dom = new DOMDocument('1.0', 'utf-8');
libxml_use_internal_errors(true);
$dom->validateOnParse = true; 
$dom->loadHTML($ul);        
$dom->preserveWhiteSpace = false;
libxml_use_internal_errors(false);

$uls = $dom->getElementsByTagName('ul');
# neste caso vem 1 ul + 5, ou 1 ul mais 3
# 0 - onde vivem         - G.br:6368
# 1 - onde trabalham     - CC.163306
# 2 - oq fazem           - CN.24
# 3 - oq estudaram       - FS.100912
# 4 - quais competencias - KE.260
#ele pega 3 pois o 1 e este aqui : alumni-facets-list col-item-container
foreach($uls as $k=>$ul){
	$classe = $ul->getAttribute('class'); 
	if(substr($classe,0,17)=='buckets-container'){
		$lis = $ul->getElementsByTagName('li');
		#var_dump($lis->length);
		foreach($lis as $li_interno){
			$ordem = $li_interno->getAttribute('data-fb-index');
			preg_match('/-(\d+)/', $ordem, $r);
			$ordem = $r[1];

			$total = $li_interno->getAttribute('data-count');

			$a = $li_interno->getElementsByTagName('a');
			$el = $a->item(0);
			$href = $el->getAttribute('href');
			#if(preg_match('/&facets=(.+)&/',$href ,$res)){
			#	$faceta = $res[1];
			#}
			$name = $el->getAttribute('data-name');
			$faceta_id = $el->getAttribute('data-fb-id');
			$cod = substr($faceta_id,0,2);
			#var_dump($total);
			#var_dump($ordem);
			#var_dump($faceta);
			#var_dump($name);
			#var_dump($faceta_id);
			switch($cod){
				case 'G.':
					$faceta="onde";break;
				case 'CC':
					$faceta="trabalho";break;
				case 'CN':
					$faceta="fazem";break;
				case 'FS':
					$faceta="estudou";break;
				case 'KE':
					$faceta="competencia";break;
				default:
					$faceta="erro";

			}
			#if(!saveFaceta($faceta,$faceta_id,$name,$ordem,$total)){
			#	echo "erro na faceta : ",$faceta,$faceta_id,$name,$ordem,$total,'<br>';
			#}
		}
	}
}


function saveFaceta($faceta,$faceta_id,$name,$ordem,$total){
	global $cn;
	$sql = "INSERT INTO facetas (faceta,faceta_id,name,ordem,total) VALUES (:faceta,:faceta_id,:name,:ordem,:total);";
	echo $sql;
	echo "<br>",$faceta,' , ',$faceta_id,' , ',$name,' , ',$ordem,' , ',$total;
	$stmt = $cn->prepare( $sql );
	$stmt->bindParam('faceta',$faceta);
	$stmt->bindParam('faceta_id',$faceta_id);
	$stmt->bindParam('name',$name);
	$stmt->bindParam('ordem',$ordem);
	$stmt->bindParam('total',$total);

	if($stmt->execute()){
		return $stmt->rowCount();
	}
	return false;
}

$facetas = ['onde','trabalho','fazem'];
list($onde,$trabalho,$fazem)=$facetas;
foreach($facetas as $f){
	$q = "select faceta_id from facetas where faceta='$f' order by ordem;";
	#$q = "select faceta_id from facetas where ordem=$i and faceta=$f;";
	$result = $cn->query($q);
	$$f = $result->fetchAll( PDO::FETCH_ASSOC );
}

#print_r($onde);
#print_r($trabalho);
#print_r($fazem);

for($i=0;$i<=24;$i++)
	for($j=0;$j<=24;$j++)
		for($k=0;$k<=24;$k++)
			if(!saveRota($onde[$i]['faceta_id'].','.$trabalho[$j]['faceta_id'].','.$fazem[$k]['faceta_id']))
				echo "erro na rota : ". $onde[$i]['faceta_id'].','.$trabalho[$j]['faceta_id'].','.$fazem[$k]['faceta_id'];
				#echo $onde[$i]['faceta_id'].','.$trabalho[$j]['faceta_id'].','.$fazem[$k]['faceta_id'].'<br>';



function saveRota($rota){
	global $cn;
	$sql = "INSERT INTO rotas (rota) VALUES (:rota);";
	echo $sql;
	$stmt = $cn->prepare( $sql );
	$stmt->bindParam('rota',$rota);

	if($stmt->execute()){
		return $stmt->rowCount();
	}
	return false;
}
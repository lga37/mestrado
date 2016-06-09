<?php
echo "<pre>";
/*
update alumni set parse=0;
update alumni set parse=1 where (edus<>'' and jobs<> '');
SELECT * FROM `alumni` WHERE alumni_id=427961673;
*/

$cn = new PDO("mysql:host=localhost;dbname=mestrado","root","");
$cn->exec("SET CHARACTER SET utf8;");
$cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

#$q = "select alumni_id,top,jobs,edus,hash from alumni limit 1000;";
#$q = "select alumni_id,top,jobs,edus,hash from alumni where alumni_id=0 limit 1000;";
$q = "select alumni_id,top,jobs,edus,hash from alumni where top <> '' and edus <> '' and jobs <> '' and parse=0 limit 1000;";
#$q = "select alumni_id,top,jobs,edus,hash from alumni where id in (1073);";
$result = $cn->query($q);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
echo "<h1>$q</h1>";
if($rows) echo "<h3>".count($rows)."</h3>";
#print_r($rows);
#$top = $rows[2]['top'];
#$html = $rows[2]['html'];
#$hash = $rows[2]['hash'];

#$row=$rows[0];
foreach ($rows as $row) {
	echo "<hr>";
	$top = $row['top'];
	$jobs = $row['jobs'];
	$edus = $row['edus'];
	$hash = $row['hash'];
	if($top=="" || $jobs=="" || $edus==""){
		continue;
	}

	################################# TOP ######################################
	$doc = new DOMDocument('1.0', 'utf-8');
	libxml_use_internal_errors(true);
	$doc->validateOnParse = true; 
	$doc->preserveWhiteSpace = false;
	$doc->loadHTML($top);        
	libxml_use_internal_errors(false);
	/*
	$img='';
	$control_gen_3 = $doc->getElementById('control_gen_3');
	if($control_gen_3){
		var_dump($control_gen_3->getElementsByTagName('img'));
		$img = $control_gen_3->getElementsByTagName('img')->item(0)->getAttribute('src');
	}
	*/
	$nome = $doc->getElementById('name')->nodeValue;

	$divs = $doc->getElementsByTagName('div');
	$conexoes='';
	$img='';
	foreach ($divs as $key => $div) {
		if($div->getAttribute('class')=="profile-picture"){
			$img=$div->getElementsByTagName('img')->item(0)->getAttribute('src');
		}
		
		if($div->getAttribute('class')=="masthead"){
			$member_id = $div->getAttribute('id');
			if(preg_match("/^member-(\d+)$/",$member_id,$res)){
				$alumni_id = (int) $res[1];
			}
		}

		if($div->getAttribute('class')=="member-connections"){
			$num = $div->nodeValue;
			if(preg_match('/(\d+)/',$num,$res)){
				$conexoes = $res[1];
			}
		}
	}

	if(!isset($alumni_id)) die("Erro ao processar o numero de alumni_id em top - hash:".$hash);

	$array['alumni_id']=$alumni_id;

	$q = sprintf("UPDATE alumni SET alumni_id=%d,nome=:nome,img='%s',conexoes=%d WHERE hash='%s' LIMIT 1;",$alumni_id,$img,$conexoes,$hash);
	$stm = $cn->prepare($q);

	echo $q,'<br>';
	$stm = $cn->prepare($q);
    $tipo = getTipo($nome);
    $stm->bindValue(':nome', $nome, $tipo);
	$stm->execute();

	unset($doc);
	################################# JOBS ######################################
	############################################################################


	$doc = new DOMDocument('1.0', 'utf-8');
	libxml_use_internal_errors(true);
	$doc->validateOnParse = true; 
	$doc->preserveWhiteSpace = false;
	$doc->loadHTML($jobs);        
	libxml_use_internal_errors(false);


	$divs_job = $doc->getElementById('background-experience');
	if($divs_job){
		$divs_job = $divs_job->getElementsByTagName('div');
		$regiao=$pais='';
		foreach($divs_job as $k=>$div){
			$id = $div->getAttribute('id');
			if(preg_match('/^experience-(\d+)-view$/',$id,$res)){
				$job_id = $res[1];
				echo "<p>job_id : $job_id</p>";
				$header = $div->getElementsByTagName('header');
				$a_h4 = $header->item(0)->getElementsByTagName('h4');
				$titulo = $a_h4->item(0)->nodeValue; 

				$titulo_link = $a_h4->item(0)->getElementsByTagName('a')->item(0)->getAttribute('href'); 
				$titulo_label = '';
				if(preg_match('/^https:\/\/www\.linkedin\.com\/title\/([\w-]+)\\?/', $titulo_link, $titulo_link_label)){
					$titulo_label = $titulo_link_label[1];
				}			

				$a_h5 = $header->item(0)->getElementsByTagName('h5');
				#caso de termos 2 h5
				if($a_h5->item(0)->getAttribute('class')=='experience-logo'){
					if(!$a_h5->item(1)->getElementsByTagName('a')->item(0)){
						#die('erro ao processar Job, hash:'.$hash);
						saveFalha($hash);
						continue;
					}
					$company_link = $a_h5->item(1)->getElementsByTagName('a')->item(0)->getAttribute('href'); 
					$company = $a_h5->item(1)->getElementsByTagName('a')->item(0)->nodeValue; 
				}else{
					$company_link = $a_h5->item(0)->getElementsByTagName('a')->item(0)->getAttribute('href'); 
					$company = $a_h5->item(0)->getElementsByTagName('a')->item(0)->nodeValue; 
				}
				if(preg_match('/^\/company\/(\d+)\?/', $company_link, $company_link_id)){
					$company_id = $company_link_id[1];
				}

				$span_times = $div->getElementsByTagName('span');
				
				foreach ($span_times as $key => $span_time) {
					if($span_time->getAttribute('class')=='experience-date-locale'){
						$time = $span_time->nodeValue;
						if(preg_match('/^(.*) de (\d{4}+).* ((.*) de (\d{4}+)|o momento)/',$time,$timeline)){
							$mes1=$timeline[1];
							$ano1=$timeline[2];
							$mes1=mes($mes1);
							$start = sprintf("%d-%d-01",$ano1,$mes1);
							if($timeline[3]=='o momento'){
								$end = date('Y-m-d');

							} else {
								$mes2=$timeline[4];
								$mes2=mes($mes2);
								$ano2=$timeline[5];
								$end = sprintf("%d-%d-01",$ano2,$mes2);
							}


						}
					}elseif($span_time->getAttribute('class')=='locality'){
						$local = $span_time->nodeValue;
						$pos_virgula = strpos($local,',');
						$len = strlen($local);
						if($pos_virgula){
							$regiao = substr($local, 0, $pos_virgula);
							$pais = substr($local, -($len-$pos_virgula-2));
						}else{
							$regiao = trim($local);
							$pais = '';
						}
							
					}
				}
				$resumo = compact('job_id','titulo','titulo_label','company_id','company','start','end','regiao','pais');
				var_dump($resumo);
				$array['jobs'][]= $resumo;

			} 
			unset($resumo);
		}#foreach
	} else {
		echo "<h1>nao tem jobs</h1>";	
	}
	#print_r($jobs);
	################################################

	unset($ano1,$ano2);
	#echo $hash,'<br>';

	unset($doc);
	################################# EDUS ######################################
	############################################################################

	$doc = new DOMDocument('1.0', 'utf-8');
	libxml_use_internal_errors(true);
	$doc->validateOnParse = true; 
	$doc->preserveWhiteSpace = false;
	$doc->loadHTML($edus);        
	libxml_use_internal_errors(false);

	$divs_edu = $doc->getElementById('background-education');
	if($divs_edu){
		#echo 'alumni_id:',$alumni_id;
		$divs_edu = $divs_edu->getElementsByTagName('div');
		#$edus['alumni_id']=$alumni_id;

		foreach($divs_edu as $k=>$div){
			$id = $div->getAttribute('id');
			if(preg_match('/^education-(\d+)-view$/',$id,$res)){
				$edu_id = $res[1];
				#echo $edu_id,'<br>';
				$header = $div->getElementsByTagName('header');
				$a_h4 = $header->item(0)->getElementsByTagName('h4');
				$edu_nome = $a_h4->item(0)->nodeValue;
				$edu_link = $a_h4->item(0)->getElementsByTagName('a')->item(0)->getAttribute('href'); 
				if(preg_match('/^\/edu\/school\?id=(\d+)&/',$edu_link,$edu_link_id)){
					$school_id = $edu_link_id[1];
				}

				$span_times = $div->getElementsByTagName('span');
				foreach ($span_times as $key => $span_time) {
					if($span_time->getAttribute('class')=='degree'){
						$degree = preg_replace('/^[, ]$/', '', $span_time->nodeValue);
					}elseif($span_time->getAttribute('class')=='major'){
						$major = $span_time->nodeValue;
						$major_link = $span_time->getElementsByTagName('a')->item(0)->getAttribute('href'); 			
						if(preg_match('/^\/edu\/fos\?id=(\d+)&/', $major_link, $major_link_id)){
							$major_id = $major_link_id[1];
						}	
					}elseif($span_time->getAttribute('class')=='education-date'){
						$time = $span_time->nodeValue;
						if(preg_match('/^(\d{4}+).*(\d{4})$/',$time,$timeline)){
							$ano1=$timeline[1];
							$ano2=$timeline[2];
						}
					}
				}
				$resumo = compact('edu_id','edu_nome','school_id','degree','major','major_id','ano1','ano2');
				var_dump($resumo);
				$array['edus'][]=$resumo;
			} 
			unset($resumo);
		}
	} else {
		echo "<h1>nao tem edus</h1>";	
	}

	try{
		#$cn->beginTransaction();
		extract($array);
		#var_dump($alumni_id);
		#var_dump($jobs);
		foreach ($jobs as $k => $job) {
			if(!saveJob($job,$alumni_id)){
				throw new Exception('<h2>erro - job id:'.$job['job_id'].'</h2>');
			}
		}
		#die('dieeeeeeeeeeeeeeeeeeeeeeeeeee');
		foreach ($edus as $k => $edu) {
			if(!saveEdu($edu,$alumni_id)){
				throw new Exception('<h2>erro - edu id:'.$edu['edu_id'].'</h2>');
			}
		}

		unset($array);	
		#$cn->commit();
	}catch(Exception $e){
		#$cn->rollback();
		echo $e->getMessage();
	}


	$q = sprintf("UPDATE alumni SET parse=1 WHERE hash='%s' LIMIT 1;",$hash);
	$stm = $cn->prepare($q);
	$stm->execute();

	echo "<br><br>",$q;
	#die('dieeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee');
}#foreach $rows




function saveJob(array $job,$alumni_id){
	global $cn;
	#print_r($job);
	extract($job);
	$tabela = "jobs";
	$pk="job_id";
	$job['alumni_id']=$alumni_id;
	$set="";
	foreach ($job as $campo => $valor) {
		$set .= $campo .'=:'.$campo.",";
	}
	$set = trim($set,",");

	if(existe($tabela,"job_id",$job_id)){
		$q = sprintf("UPDATE %s SET %s WHERE %s=%d;",$tabela,$set,$pk,$job_id);
	} else {
		$q = sprintf("INSERT %s SET %s;",$tabela,$set);
	}
	echo '<br>',$q;

	$stm = $cn->prepare($q);
    foreach($job as $campo => $valor){
    	#echo $campo,' = ',$valor,'<br>';
        $tipo = getTipo($valor);
        #$stm->bindParam(':'.$campo, $valor, $tipo);
        $stm->bindValue(':'.$campo, $valor, $tipo);
    }

	return $stm->execute();
}

function saveEdu(array $edu,$alumni_id){
	global $cn;
	#print_r($edu);
	extract($edu);
	#echo '<br>',$edu_id;
	$tabela = "edus";
	$pk="edu_id";
	$edu['alumni_id']=$alumni_id;
	$set="";
	foreach ($edu as $campo => $valor) {
		$set .= $campo .'=:'.$campo.",";
	}
	$set = trim($set,",");

	if(existe($tabela,"edu_id",$edu_id)){
		$q = sprintf("UPDATE %s SET %s WHERE %s=%d;",$tabela,$set,$pk,$edu_id);
	} else {
		$q =  sprintf("INSERT %s SET %s;",$tabela,$set);
	}
	echo '<br>',$q;

	$stm = $cn->prepare($q);
    foreach($edu as $campo => $valor){
    	#echo $campo,' = ',$valor,'<br>';
        $tipo = getTipo($valor);
        #$stm->bindParam(':'.$campo, $valor, $tipo);
        $stm->bindValue(':'.$campo, $valor, $tipo);
    }

    #if(!$stm->execute()){
    #	echo $cn->errorInfo;
    #}
	return $stm->execute();
	
}

function saveFalha($hash){
	global $cn;
	$q = sprintf("UPDATE alumni SET falha=1 WHERE hash='%s' LIMIT 1;",$hash);
	echo $q,'<br>';
	$stm = $cn->prepare($q);
	$stm->execute();

}


function existe($tabela,$pk,$valor_pk){
	global $cn;
	$q = "SELECT id from ".$tabela." WHERE ".$pk.'='.$valor_pk." LIMIT 1;";
    $stm = $cn->prepare($q);
    echo "<br>$q<br>";
    $stm->execute();
    return $stm->fetchColumn();
}

function getTipo($var){
    if(is_numeric($var) && (intval($var) == $var)){
        return PDO::PARAM_INT; #1
    }
    if(is_bool($var)){
        return PDO::PARAM_BOOL;
    }
    if(is_null($var)){
        return PDO::PARAM_NULL;
    }
    return PDO::PARAM_STR;
}


function mes($mes){
	$meses = [
		'janeiro',
		'fevereiro',
		'marco',
		'abril',
		'maio',
		'junho',
		'julho',
		'agosto',
		'setembro',
		'outubro',
		'novembro',
		'dezembro',
	];
	foreach ($meses as $key => $m) {
		if($m==$mes){
			return $key+1;
		}
	}
	return 1;
}


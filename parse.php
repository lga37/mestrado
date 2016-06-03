<?php
echo "<pre>";
$cn = new PDO("mysql:host=localhost;dbname=mestrado","root","");
$q = "select top,jobs,edus,hash from alumni where top <> '' and jobs <> '' and edus <> '';";
echo "<h1>$q</h1>";
$result = $cn->query($q);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
#print_r($rows);
#$top = $rows[2]['top'];
#$html = $rows[2]['html'];
#$hash = $rows[2]['hash'];

#$row=$rows[0];
foreach ($rows as $row) {
	$top = $row['top'];
	#$html = $row['html'];
	$jobs = $row['jobs'];
	$edus = $row['edus'];
	#var_dump($top);
	$hash = $row['hash'];
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

	$q = sprintf("UPDATE alumni SET alumni_id=%d,nome='%s',img='%s',conexoes=%d WHERE hash='%s' LIMIT 1;",$alumni_id,$nome,$img,$conexoes,$hash);
	echo $q,'<br>';
	$stm = $cn->prepare($q);
	$stm->execute();

	unset($doc);
	################################# TOP ######################################
	############################################################################

	$array['alumni_id']=$alumni_id;

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
							$start = sprintf("01-%d-%d",$mes1,$ano1);
							if($timeline[3]=='o momento'){
								$end = date('d-m-Y');

							} else {
								$mes2=$timeline[4];
								$mes2=mes($mes2);
								$ano2=$timeline[5];
								$end = sprintf("01-%d-%d",$mes2,$ano2);
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
				$array['jobs'][]=compact('job_id','titulo','titulo_label','company_id','company_name','start','end','regiao','pais');
			}
		}#foreach
	} else {
		echo "nao tem job";	
	}
	#print_r($jobs);
	################################################
	echo "<hr>";
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
		echo 'alumni_id:',$alumni_id;
		$divs_edu = $divs_edu->getElementsByTagName('div');
		#$edus['alumni_id']=$alumni_id;

		foreach($divs_edu as $k=>$div){
			$id = $div->getAttribute('id');
			if(preg_match('/^education-(\d+)-view$/',$id,$res)){
				$edu_id = $res[1];
				echo $edu_id,'<br>';
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

				$array['edus'][]=compact('edu_id','edu_nome','school_id','degree','major','major_id','ano1','ano2');
			}

		}
	} else {
		echo "nao tem edu";	
	}

	#print_r($array);

	try{
		#$cn->beginTransaction();
		extract($array);
		#var_dump($alumni_id);
		#var_dump($jobs);
		foreach ($jobs as $k => $job) {
			if(!saveJob($job,$alumni_id)){
				throw new Exception('<hr>erro - job id:'.$job['job_id']);
			}
		}
		#if(isset($edus)){
			foreach ($edus as $k => $edu) {
				if(!saveEdu($edu,$alumni_id)){
					throw new Exception('<hr>erro - edu id:'.$edu['edu_id']);
				}
			}
			
		#}

		#$cn->commit();
	}catch(Exception $e){
		#$cn->rollback();
		echo $e->getMessage();
	}


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
	echo '<hr>',$q;

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
	print_r($edu);
	extract($edu);
	echo '<br>',$edu_id;
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
	echo '<hr>',$q;

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

function existe($tabela,$pk,$valor_pk){
	global $cn;
	$q = "SELECT 1 from ".$tabela." WHERE ".$pk.'='.$valor_pk." LIMIT 1;";
    $stm = $cn->prepare($q);
    return $stm->execute()? (bool) $stm->fetchColumn() : false;
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
	return false;
}



function getElementsByClass(&$parentNode, $tagName, $className) {
    $nodes=array();

    $childNodeList = $parentNode->getElementsByTagName($tagName);
    if($childNodeList){
	    for ($i = 0; $i < $childNodeList->length; $i++) {
	        $temp = $childNodeList->item($i);
	        if (stripos($temp->getAttribute('class'), $className) !== false) {
	            $nodes[]=$temp;
	        }
	    }
    	
    }

    return $nodes;
}
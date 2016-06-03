<?php

$xml=simplexml_load_file("curriculo.xml") or die("Error: Cannot create object");

#echo "<pre>"; print_r($xml->children()['DADOS-GERAIS']); echo "<hr></pre>";

echo $xml->children()->attributes()['NOME-COMPLETO'];
echo '<hr>';
#var_dump( $xml->children()->children());die;
foreach($xml->children() as $k=>$f){
	#echo '.......'.$k,'<br>';

	#if($k=='DADOS-GERAIS'){
		foreach($f as $key=>$v){
			#echo $key,'<br>';
			

			#var_dump($v->children());
			#if($key=='FORMACAO-ACADEMICA-TITULACAO'){

				$netos = (array) $v->children()->GRADUACAO;
				#var_dump($netos);
				foreach ($netos as $i => $arrayNetos) {
					#var_dump($arrayNetos);
					echo $arrayNetos['NOME-INSTITUICAO'],'<br>';
					echo $arrayNetos['NOME-CURSO'],'<br>';
					echo $arrayNetos['ANO-DE-INICIO'],'<br>';
					echo $arrayNetos['ANO-DE-CONCLUSAO'],'<br>';
					#foreach($arrayNetos as $chave=>$neto){
					#	echo $chave,'---',$neto,'<br>';
					#}
					#echo $i .'--------'. $neto .'<br>';
				}	
				#echo "<pre>"; print_r(); echo "<hr></pre>";
				#echo "<pre>"; print_r($v->children()); echo "<hr></pre>";
			#}
			#break;
		}
	#}
}
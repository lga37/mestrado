<?php 
include('_header.php'); 

$q = "select nome,id from alumni where nome <> '' and parse=1 and edus <> '' and jobs <> '' order by rand() limit 10;";
#echo $q;
$result = $cn->query($q);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
echo '<div class="row">';
foreach ($rows as $key => $row) {
	extract($row);
	echo '<div class="col-md-6"><a class="btn btn-info btn-block" href="?id='.$id.'">'.$nome.'</a></div>';
}
echo '</div>';

if($_GET){
	if(isset($_GET['alu'])){
		$alu=(int) $_GET['alu'];
		$q = "select * from alumni where alumni_id=".$alu." LIMIT 1;";
	} else {
		$id=(int) $_GET['id'];
		$q = "select * from alumni where id=".$id." LIMIT 1;";
	}
	#echo $q;
	$result = $cn->query($q);
	$row = $result->fetch(PDO::FETCH_ASSOC);
	echo "<hr>";
	extract($row);
	if(!isset($alumni_id)) die('erro ao acessar alumni_id:'.$alumni_id);
	$q = "select * from jobs where alumni_id=".$alumni_id.";";
	$result = $cn->query($q);
	$jobs = $result->fetchAll( PDO::FETCH_ASSOC );
	$timeline = [];
	#var_dump($jobs);
	foreach($jobs as $job){
		extract($job);
		$ini = date('Y', strtotime($start));
		$fim = date('Y', strtotime($end));
		$head = sprintf("<a href=#%d>%s</a>",$company_id,$company);
		$body = $titulo .' - '. $regiao;
		$foot = $ini.' - '.$fim;
		$timeline['job'][]=['ano'=>$fim,'head'=>$head,'body'=>$body,'foot'=>$foot];
	}

	$q = "select * from edus where alumni_id=".$alumni_id.";";
	$result = $cn->query($q);
	$edus = $result->fetchAll( PDO::FETCH_ASSOC );
	foreach($edus as $edu){
		extract($edu);
		$head = sprintf("<a href=#%d>%s</a>",$edu_id,$edu_nome);
		$body = sprintf("<a href=#%d>%s</a>",$major_id,$degree.' - '.$major);
		$foot = $ano1.' - '.$ano2;
		$timeline['edu'][]=['ano'=>$ano2,'head'=>$head,'body'=>$body,'foot'=>$foot];
	}

	#echo $q;
	#print_r($edus);
	if(!$jobs || !$edus){
		$q = "update alumni set falha=1 where alumni_id=".$alumni_id.";";
		$cn->exec($q);
	}


	foreach($timeline as $key=>$eventos){
		$lado=$key=='job'?'left':'right';
		foreach($eventos as $evento){
			$evento['lado']=$lado;
			$ord[]=$evento;
		}
	}
	#echo "<pre>";
	#print_r($ord);
	if(isset($ord)) usort($ord,"sortByAno");
	#print_r($ord);


	?>
	<div class="col-md-10 col-md-offset-1">
		<div class="row">
			<div class="col-md-3">
				<?php echo '<img class="img-rounded" width="200" src="'.$img.'">' ?>
			</div>
			<div class="col-md-9">
				<table class="table table-striped table-hover table-condensed">
					<tr>
						<td colspan="2"><h3><?php echo $nome ?></h3></td>
					</tr>
					<tr>
						<td>Nascimento</td>
						<td><?php echo $aniversario ?></td>
					</tr>
					<tr>
						<td>Conexoes</td>
						<td><?php echo $conexoes ?></td>
					</tr>
					<tr>
						<td><a class="btn btn-primary" href="https://www.linkedin.com/profile/view?id=<?php echo $hash ?>" target="_blank">Perfil Linkedin</a></td>
						<td><?php echo $alumni_id ?></td>
					</tr>
				</table>
				
			</div>
		</div>
	</div>


	<div class="col-md-12">
	    <div class="timeline">
	        <span class="timeline-label">
	            <span class="label label-success">atual</span>
	        </span>

			<?php

			if(isset($ord)){
				foreach($ord as $evento){
					extract($evento);
					echo montaEvento($head,$body,$foot,$lado);
				}
				
			}else{
				?>
			    <div class="alert alert-danger alert-dismissible fade in" role="alert">
			        <h4>Erro ao Capturar Dados</h4>
			    </div>

				<?php
			}

			?>	
	        <span class="timeline-label">
	            <button class="btn btn-danger"><i class="fa fa-graduation-cap"></i></button>
	        </span>
	    </div>

	</div><!-- .col-md-12 -->

	<br><br>
	<br><br>
	<br><br>

	<?php
}#if($_POST)



function sortByAno($a, $b){
	$a = $a['ano'];
	$b = $b['ano'];
	if ($a == $b){
		return 0;
	}
	return ($a > $b) ? -1 : 1;
}

function montaEvento($head,$body,$foot,$lado='left'){
	$inv = $lado=='left'?'right':'left';
	$cor = $lado=='left'?'warning':'info';

	$html = <<<HTML

	    <div class="timeline-item timeline-item-{$lado}">
	        <div class="timeline-point timeline-point-{$cor}">
	            <i class="fa fa-star"></i>
	        </div>
	        <div class="timeline-event timeline-event-{$cor}">
	            <div class="timeline-heading">
	                <h4>{$head}</h4>
	            </div>
	            <div class="timeline-body">
	                <p>{$body}</p>
	            </div>
	            <div class="timeline-footer">
	                <p class="pull-{$inv}">{$foot}</p>
	            </div>
	        </div>
	    </div>


HTML;
	return $html;
}

include('_footer.php');
<?php 
include('_header.php'); 

#aqui somente quem ja foi match
$q = "
    select * from puc

    ;";

$result = $cn->query($q);
#var_dump($result);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

foreach($rows as $row){
    extract($row);
    #echo $nome,' <br> ',$ano,$curso;
}

?>

<div class="row">
	<div class="col-md-6">
      <div class="card">
        <div class="card-block">
          <h4 class="card-title">Grafico de Linhas</h4>
          <h6 class="card-subtitle text-muted"><a href="#" class="card-link">Maiores Info</a></h6>
        	<div id="grafico1"></div>	
        </div>
      </div><!-- card -->

	</div>
	<div class="col-md-6">
      <div class="card">
        <div class="card-block">
          <h4 class="card-title">Grafico de Donuts</h4>
          <h6 class="card-subtitle text-muted"><a href="#" class="card-link">Maiores Info</a></h6>
        </div>
        	<div id="donut-example"></div>	
      </div><!-- card -->
	</div>

	<div class="col-md-6">
      <div class="card">
        <div class="card-block">
          <h4 class="card-title">Grafico de Area</h4>
          <h6 class="card-subtitle text-muted"><a href="#" class="card-link">Maiores Info</a></h6>
        	<div id="area-example"></div>	
        </div>
      </div><!-- card -->

	</div>
	<div class="col-md-6">
      <div class="card">
        <div class="card-block">
          <h4 class="card-title">Grafico de Barras</h4>
          <h6 class="card-subtitle text-muted"><a href="#" class="card-link">Maiores Info</a></h6>
        </div>
        	<div id="bar-example"></div>	
      </div><!-- card -->
	</div>


</div><!-- .row -->
<br><br><br><br><br><br><br><br>




<?php
include('_footer.php');
<?php

function redirect($pag){
    echo "<script>window.location.href='".$pag."';</script>";
}

function inicializaParametrosURL(){
    parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $params);

    $default = [
        'p'=>1,
        'm'=>'v',   
        'o'=>'id',
        'sens'=>'asc',
    ];

    return array_merge($default,array_intersect_key($params, $default));
}


function order(){
    $order = isset($_GET['o'])? $_GET['o'] : 'id';
    $sens = isset($_GET['sens'])? $_GET['sens'] : 'ASC';
    return sprintf(" ORDER BY %s %s",$order,$sens);
}


function limit(){
    $pag = filter_input(INPUT_GET, 'p', FILTER_VALIDATE_INT);
    $pag = $pag? $pag : 1;
    $per_pag = 5;
    #$offset = $pag * $per_pag;
    $offset = ($pag - 1) * ($per_pag);
    $q = sprintf(" LIMIT %d",$per_pag);
    if($offset >0){
        $q .= sprintf(" OFFSET %d",$offset);
    }
    return $q;
}


function paginate($total,$per_page=5,$pag=1){

    $totalPaginas = ceil($total / $per_page);
    $pag = (int) $pag;
    $linkAnt = replaceLink('p',$pag-1);#nao usar incr/decr senao da erro, pois mudamos a variavel
    $linkPos = replaceLink('p',$pag+1);
    $active="";

    if($pag>1){
        $paginas[] = sprintf('<li class="page-item %s"><a class="page-link" href="%s"><span aria-hidden="true">&laquo;</span></a></li>',$active,$linkAnt);
    }
    for($i=1; $i<=$totalPaginas; $i++){

        $link = replaceLink('p',$i);
        if($i==$pag){
            $active="active";
            $paginas[] = sprintf('<li class="page-item %s"><a class="page-link" href="%s">%d</a></li>',$active,$link,$i);
        }else{
            
            $active="";
            $paginas[] = sprintf('<li class="page-item %s"><a class="page-link" href="%s">%d</a></li>',$active,$link,$i);
        }

    }
    if($pag<$totalPaginas){
        $paginas[] = sprintf('<li class="page-item %s"><a class="page-link" href="%s"><span aria-hidden="true">&raquo;</span></a></li>',$active,$linkPos);
    }

    return sprintf("<ul class=\"pagination pull-left pagination-lg\">\n%s\n</ul>",implode("\n",$paginas));
}

function replaceLink(){
    $numargs = func_num_args();
    if ($numargs >= 2) {
        $lista = func_get_args();
        $alterar = [];
        for ($i = 0; $i < $numargs; $i+=2) {
            $alterar[$lista[$i]]=$lista[$i+1];
        }    
        global $query;
        $a = array_diff_key($query, $alterar) + $alterar;
        array_unique($a);
        #print_r($a);   
        return '?'. http_build_query($a);
    } else {
        trigger_error("Erro - mandar no minimo 2 params");
    }

}

function invertePoeSetaOrderBy($campo){
    if(!isset($_GET['sens'])){
        $sens = "asc";
        $seta = "";
    } else {
        if($_GET['sens']=='asc'){
            $sens = "desc";
            $seta = "fa-long-arrow-up";
        }else{
            $sens = "asc";
            $seta = "fa-long-arrow-down";
        }
    }
    $iconeSeta = (!isset($_GET['o']) || $_GET['o']==$campo)? "<i class=\"fa ".$seta."\">" : "";
    $replace = replaceLink('o',$campo,'sens',$sens);
    return "<a href=\"". $replace ."\">".$campo."</a> ".$iconeSeta."</i>";
}




function msg($h4,$p,$tipo='danger'){
    ?>
    <div class="alert alert-<?= $tipo?> alert-dismissible fade in" role="alert">
        <button class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
        <h4><?= $h4?></h4>
        <p><?= $p?></p>
    </div>

    <?php
}  


/*
	Funcao que dado um array de itens do menu, imprime na barra de navegacao
	Se for passado $modo='icon' ele imprime o menu em formato icones.
*/
function imprimeMenuTopo(array $itens,$modo='texto'){
	$this_page = basename($_SERVER['PHP_SELF']);

	$active="";
	foreach($itens as $key=>$item){
		if($this_page==$item['link']){
			$active="active";
		}
		echo '<li class="nav-item $active"><a class="nav-link fonte-menu-topo" href="' .$item['link']. '">';
		if($modo=='icon') 
			echo '<span class="fa "'.$item['icone'].'"></span>';
		else 
			echo $item["texto"];
		echo '</a></li>';
	}

}


function montaItemVitrineBS4($i,$id,$nome,$img,$preco,$prazo,$estoque){
    if($estoque < 1){
        $disponib = ($prazo == 'E')? "esgotado" : $prazo." dia(s)";
    } else {
        $disponib = "pronta-entrega";
    }

	?>
       <div class="col-sm-3">
          <div class="card">
            <img src="http://lorempixel.com/300/300/sports/<?=$i?>/P,M,G,GG" 
            class="img-fluid img-rounded center-block" alt="<?=$nome?>">
            <div class="card-img-overlay">
              <p class="card-title"><?=$nome?></p>
              <h5 class="pull-right card-subtitle">R$ <?=$preco?></h5>
            </div>
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <a href="wishlist.php?a=add&id=<?=$id?>" class="card-link"><i class="fa fa-heart"></i></a>
                <a href="carrinho.php?a=add&id=<?=$id?>" class="card-link"><i class="fa fa-shopping-cart"></i></a>
                <a href="detalhes.php?id=<?=$id?>" class="card-link"><i class="fa fa-external-link"></i></a>
                <a href="#" class="card-link"><i class="fa fa-share-alt"></i></a>
                <span class="pull-right"><?=$prazo?></span> 
              </li>
            </ul>
          </div><!-- card -->
        </div><!-- .col-sm-3 -->
	<?php   
}

function enviaEmail($emailDeOrigem,$nomeDeOrigem,$senha,$emailDeDestino,$nomeDeDestino,$assunto,$msg){

	// Inicia a classe PHPMailer
	$mail = new PHPMailer();

	// Define os dados do servidor e tipo de conexão
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	#$mail->SMTPDebug  = 2;
	//$mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
	//$mail->Username = 'seumail@dominio.net'; // Usuário do servidor SMTP
	//$mail->Password = 'senha'; // Senha do servidor SMTP


	// Config Gmail
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->IsSMTP(); // Define que a mensagem será SMTP
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->SMTPSecure = "tls";                 // sets the prefix to the servier
	$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
	$mail->Port       = 587;                   // set the SMTP port for the GMAIL server
	$mail->Username   = $emailDeOrigem;  		// GMAIL username
	$mail->Password   = $senha;            		// GMAIL password

	// Define o remetente
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->SetFrom($emailDeOrigem, $nomeDeOrigem);
	$mail->AddReplyTo($emailDeOrigem, $nomeDeOrigem);

	// Define os destinatário(s)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->AddAddress($emailDeDestino, $nomeDeOrigem);
	#$mail->AddAddress('ciclano@site.net');
	//$mail->AddCC('ciclano@site.net', 'Ciclano'); // Copia
	//$mail->AddBCC('fulano@dominio.com.br', 'Fulano da Silva'); // Cópia Oculta

	// Define os dados técnicos da Mensagem
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

	$mail->ContentType = 'text/plain';
	#$mail->IsHTML(true);
	$mail->CharSet = 'UTF-8'; // Charset da mensagem (opcional)

	// Define a mensagem (Texto e Assunto)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->Subject  = $assunto; // Assunto da mensagem
	$mail->Body = $msg;
	$mail->AltBody = $msg; #texto PURO

	// Define os anexos (opcional)
	//$mail->AddAttachment("c:/temp/documento.pdf", "novo_nome.pdf");  // Insere um anexo

	// Envia o e-mail
	$emailEnviado = $mail->Send();

	// Limpa os destinatários e os anexos
	$mail->ClearAllRecipients();
	#$mail->ClearAttachments();

    if (!$emailEnviado) {
        $m= "Informações do erro: <pre>" . print_r($mail->ErrorInfo) ."</pre>";
		msg("Não foi possível enviar o e-mail",$m,"danger");
		return false;
    }

	return true; #booleano
}


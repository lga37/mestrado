<?php
require("../includes/init.php");
require("../includes/pdo.php");
require("../includes/funcoes_web.php");
$titulo = 'PUC - Sistema Alumni';
$menuTopoItens = [
                      ['link'=>URL.'index.php' ,'texto'=>'Home'      ,'icone'=>'fa-home'          ],
                      ['link'=>URL.'timeline.php' ,'texto'=>'TimeLine'  ,'icone'=>'fa-file-text'     ],
                      ['link'=>URL.'matching.php' ,'texto'=>'Matching'  ,'icone'=>'fa-home'          ],
                      ['link'=>URL.'parse.php' ,'texto'=>'Parsing'  ,'icone'=>'fa-home'          ],
                      ['link'=>URL.'rotas.php' ,'texto'=>'Rotas'  ,'icone'=>'fa-home'          ],
                      ['link'=>URL.'dicionarios.php' ,'texto'=>'Dicionarios'  ,'icone'=>'fa-home'          ],
                      ['link'=>URL.'stats.php' ,'texto'=>'Stats'     ,'icone'=>'fa-home'          ],
                  ];

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/css/tether.min.css">
    <link rel="stylesheet" type="text/css" href="<?=URL?>css/estilo.css">
    <link rel="stylesheet" type="text/css" href="<?=URL?>css/timeline.css">
    <link rel="stylesheet" type="text/css" href="<?=URL?>css/typeahead.css">

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <link href='https://fonts.googleapis.com/css?family=Orbitron:400,500,900' rel='stylesheet' type='text/css'>
    <title><?=$titulo ?></title>
  </head>

  <body class="container-fluid">

    <header>
      <nav class="navbar navbar-fixed-top navbar-dark bg-inverse">
        <button class="navbar-toggler hidden-sm-up" type="button" data-toggle="collapse" data-target="#collapsingNavbar">&#9776;</button>
        <div class="collapse navbar-toggleable-xs" id="collapsingNavbar">
          <a class="navbar-brand text-primary" href="<?=URL?>index.php">>__LGA</a>
          <ul class="nav navbar-nav">
            <?php
            
              imprimeMenuTopo($menuTopoItens);                          
            ?>

          </ul>

          <form class="form-inline text-xs-center" action="<?=URL?>listagem.php">
            <input class="form-control busca-topo" type="search" name="q" placeholder="Search">
            <button class="btn btn-success-outline"><i class="fa fa-search"></i></button>
          </form>

        </div>
      </nav>
    </header>


    <section class="conteudo">
      <div class="container-fluid">


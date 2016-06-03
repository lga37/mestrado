<?php
class Perfil extends PHPUnit_Framework_TestCase {
    protected $webDriver;
    protected $pdo;
    protected $url = 'https://www.linkedin.com/uas/login';

    public function setUp()    {
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
        $this->pdo = new PDO("mysql:host=localhost;dbname=mestrado","root","");
    }

    public function tearDown(){
        $this->webDriver->quit();
    }

    private function login(){
        $this->webDriver->get($this->url);
        $login = $this->webDriver->findElement(WebDriverBy::name('session_key'));
        #$login->sendKeys('gustavo3.8cc@gmail.com');
        $login->sendKeys('voipgus@gmail.com');
        $senha = $this->webDriver->findElement(WebDriverBy::name('session_password'));
        $senha->sendKeys('szgdfb12@');
        $submit = $this->webDriver->findElement(WebDriverBy::id('btn-primary'));
        $submit->click();
        #$this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);

    }


    public function testSaveHTMLPerfil(){
        $this->login();
        sleep(3);

        #$this->webDriver->manage()->window()->maximize();
        $results = $this->pdo->query("SELECT hash FROM alumni WHERE edus='' or jobs='';");
        $hashs = $results->fetchAll(PDO::FETCH_ASSOC);
        foreach($hashs as $row){
            $hash = $row['hash'];
            #$hash = "ABAAAA42jqUB3fGj_UY7G2v-AQYBbMzomRBV5Q0";
            $u = "https://www.linkedin.com/profile/view?id=".$hash;
            $this->webDriver->get($u);
            sleep(10);

            $top="";
            $html="";
            $top=$this->webDriver->findElement(WebDriverBy::cssSelector('.profile-card'))->getAttribute('outerHTML');
            sleep(1);
            $html=$this->webDriver->findElement(WebDriverBy::cssSelector('#background'))->getAttribute('outerHTML');
            
            $jobs=$this->webDriver->findElement(WebDriverBy::id('background-experience'))->getAttribute('outerHTML');
            sleep(1);
            $edus=$this->webDriver->findElement(WebDriverBy::id('background-education'))->getAttribute('outerHTML');
            $this->saveHTMLPerfil($top,$html,$edus,$jobs,$hash);
            sleep(1);
        }

    }


    private function saveHTMLPerfil($top,$html,$edus,$jobs,$hash){
        $tabela = "alumni";
        $pk="hash";
        $perfil['top']=$top;
        $perfil['html']=$html;
        $perfil['edus']=$edus;
        $perfil['jobs']=$jobs;
        $perfil['hash']=$hash;
        $set="";
        foreach ($perfil as $campo => $valor) {
            $set .= $campo .'=:'.$campo.",";
        }
        $set = trim($set,",");

        if($this->existe($tabela,"hash",$hash)){
            $q = sprintf("UPDATE %s SET %s WHERE %s='%s';",$tabela,$set,$pk,$hash);
        } else {
            $q = sprintf("INSERT %s SET %s;",$tabela,$set);
        }
        echo '<hr>',$q;

        $stm = $this->pdo->prepare($q);
        foreach($perfil as $campo => $valor){
            $tipo = $this->getTipo($valor);
            $stm->bindValue(':'.$campo, $valor, $tipo);
        }

        $stm->execute();
        
    }

    private function existe($tabela,$pk,$valor_pk){
        $q = "SELECT hash from ".$tabela." WHERE ".$pk. "='" .$valor_pk."' LIMIT 1;";
        echo "\n\n",$q;
        $stm = $this->pdo->prepare($q);
        #$tipo = $this->getTipo($valor);
        #$stm->bindValue(':'.$campo, $valor, $tipo);

        return $stm->execute()? (bool) $stm->fetchColumn() : false;
    }

    private function getTipo($var){
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


}

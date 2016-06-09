<?php
class Perfil extends PHPUnit_Framework_TestCase {
    protected $webDriver;
    protected $pdo;
    protected $url = 'https://www.linkedin.com/uas/login';

    public function setUp()    {
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
        #$this->webDriver = RemoteWebDriver::create('http://localhost:7055/hub', $capabilities);
        $this->pdo = new PDO("mysql:host=localhost;dbname=mestrado","root","");
        $this->pdo->exec("SET CHARACTER SET utf8;");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    public function tearDown(){
        $this->webDriver->quit();
    }

    private function login(){
        $this->webDriver->get($this->url);
        $login = $this->webDriver->findElement(WebDriverBy::name('session_key'));
        $login->sendKeys('gustavo3.8cc@gmail.com');
        #$login->sendKeys('voipgus@gmail.com');
        $senha = $this->webDriver->findElement(WebDriverBy::name('session_password'));
        $senha->sendKeys('szgdfb12@');
        $submit = $this->webDriver->findElement(WebDriverBy::id('btn-primary'));
        $submit->click();
        #$this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);

    }


    public function testSaveHTMLPerfil(){
        $this->login();
        sleep(3);

        $results = $this->pdo->query("SELECT hash FROM alumni WHERE captura=0 LIMIT 100;");
        #$results = $this->pdo->query("SELECT hash FROM alumni WHERE falha=1;");
        $hashs = $results->fetchAll(PDO::FETCH_ASSOC);
        foreach($hashs as $row){
            $hash = $row['hash'];
            $u = "https://www.linkedin.com/profile/view?id=".$hash;
            $this->webDriver->get($u);
            sleep(14);

            #$html=$this->webDriver->findElement(WebDriverBy::cssSelector('#background'))->getAttribute('outerHTML');
            #$top=$this->webDriver->findElement(WebDriverBy::cssSelector('.profile-card'))->getAttribute('outerHTML');

            $top=$jobs=$edus=$aniversario=false;
            try {
                $top=$this->webDriver->findElement(WebDriverBy::id('top-card'))->getAttribute('outerHTML');
            } catch (NoSuchElementException $e) {
                # nada
            }
            sleep(1);
            try {
                $jobs=$this->webDriver->findElement(WebDriverBy::id('background-experience'))->getAttribute('outerHTML');
            } catch (NoSuchElementException $e) {
                # nada
            }
            sleep(1);
            try {
                $edus=$this->webDriver->findElement(WebDriverBy::id('background-education'))->getAttribute('outerHTML');
            } catch (NoSuchElementException $e) {
                # nada
            }
            sleep(1);
            try {
                $aniversario=$this->webDriver->findElement(WebDriverBy::id('personal-info-view'))->getText(); 
            } catch (NoSuchElementException $e) {
                # nada
            }

            $this->saveHTMLPerfil($top,$edus,$jobs,$hash,$aniversario);
            sleep(1);

        }
    }


    private function isElementExists($nome) {
        $isExists = true;
        try {
            $this->webDriver->findElement(WebDriverBy::id($nome));
        } catch (NoSuchElementException $e) {
            $isExists = false;
        }
        return $isExists;
    }



    private function saveHTMLPerfil($top,$edus,$jobs,$hash,$aniversario){
        $tabela = "alumni";
        $pk="hash";

        if($top) $perfil['top']=$top;
        if($edus) $perfil['edus']=$edus;
        if($jobs) $perfil['jobs']=$jobs;
        if($aniversario) $perfil['aniversario']=$aniversario;
        
        if($top) $perfil['hash']=$hash;
        $perfil['captura']=1;
        $perfil['falha']=0;

        $set="";
        foreach ($perfil as $campo => $valor) {
            $set .= $campo .'=:'.$campo.",";
        }
        $set = trim($set,",");

        #if($this->existe($tabela,"hash",$hash)){
            $q = sprintf("UPDATE %s SET %s WHERE %s='%s';",$tabela,$set,$pk,$hash);
        #} else {
        #    $q = sprintf("INSERT %s SET %s;",$tabela,$set);
        #}
        #echo '<hr>',$q;

        $stm = $this->pdo->prepare($q);
        foreach($perfil as $campo => $valor){
            $tipo = $this->getTipo($valor);
            $stm->bindValue(':'.$campo, $valor, $tipo);
        }

        $stm->execute();
        
    }
/*
    private function existe($tabela,$pk,$valor_pk){
        $q = "SELECT hash from ".$tabela." WHERE ".$pk. "='" .$valor_pk."' LIMIT 1;";
        echo "\n\n",$q;
        $stm = $this->pdo->prepare($q);
        #$tipo = $this->getTipo($valor);
        #$stm->bindValue(':'.$campo, $valor, $tipo);

        return $stm->execute()? (bool) $stm->fetchColumn() : false;
    }
*/
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

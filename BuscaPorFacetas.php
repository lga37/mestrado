<?php
class BuscaPorFacetas extends PHPUnit_Framework_TestCase {
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


    public function testBusca(){
        $this->login();
        sleep(10);

        $results = $this->pdo->query("select rota from rotas where feita is null or feita=0 order by time;");
        $rotas = $results->fetchAll(PDO::FETCH_ASSOC);

        #$rota=$rotas[3];
        foreach($rotas as $rota){
            $u = "https://www.linkedin.com/edu/alumni?id=10582&facets=".$rota['rota'];
            $this->webDriver->get($u);
            sleep(10);

            $total = $this->webDriver->findElement(WebDriverBy::className('alumni-count'))->getText();
            echo "\ntotal = ".$total;

            $pags = ceil($total/10);

            if($pags >= 4 || $pags == 0){
                continue;
            }
            for($i=1;$i<=$pags;$i++){
                $this->webDriver->executeScript("window.scrollTo(0, document.body.scrollHeight);");
                sleep(5);
                #var_dump($links);
            }#for
            
            $perfis = $this->webDriver->findElements(WebDriverBy::className('title'));
            $arrayHashs = $this->extraiHashArray($perfis);
            $this->saveHASH($arrayHashs);
            sleep(1);
            echo "\n","============= pag : ". $i ." =========================================";
            
        }#foreach

    }#testRota


    private function extraiHashArray(array $perfis){
        $arrayHashs=[];
        foreach($perfis as $key=>$perfil){
            $v = $perfil->getAttribute('href');
            #echo "\n",$v;
            if(preg_match("/\/profile\/view\?id=(.+)&authType=/",$v,$res)){
                $arrayHashs[]=$res[1];
                #echo "\n",$res[1];
            }
            
        }
        return $arrayHashs;
    }


    private function saveHASH(array $hashs){
        foreach ($hashs as $key => $hash) {
            $tabela = "alumni";
            $pk="hash";
            $alumni['hash']=$hash;
            $set="";
            foreach ($alumni as $campo => $valor) {
                $set .= $campo .'=:'.$campo.",";
            }
            $set = trim($set,",");

            if($this->existe($tabela,"hash",$hash)){
                $q = sprintf("UPDATE %s SET %s WHERE %s='%s';",$tabela,$set,$pk,$hash);
            } else {
                $q = sprintf("INSERT %s SET %s;",$tabela,$set);
            }
            echo "\n\n",$q;

            $stm = $this->pdo->prepare($q);
            foreach($alumni as $campo => $valor){
                $tipo = $this->getTipo($valor);
                $stm->bindValue(':'.$campo, $valor, $tipo);
            }
            $stm->execute(); 
            #if(!$stm->execute()){
            #    echo 'Hash '. $hash .' nao foi salvo';
            #}
        }
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

<?php
class BuscaPorAno extends PHPUnit_Framework_TestCase {
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
    }

    
    public function testBusca(){
        $this->login();
        sleep(3);
        for ($j=1972; $j <= 2016; $j++) { 
            #por isso que nao tava funcionando, daaa
            $url="https://www.linkedin.com/edu/alumni?";
            $query = http_build_query([
                    "id"=>10582,
                    "dateType"=>"graduated",
                    "endYear"=>$j,
                    "incNoDates"=>true,
                ]);
            $url .= $query;
            echo "\n",$url;
            var_dump($url);
            $this->webDriver->get($url);
            sleep(10);
            for($i=1;$i<=4;$i++){
                #esta e uma limitacao, nao aparece mais
                sleep(3);
                $this->webDriver->executeScript("window.scrollTo(0, document.body.scrollHeight);");

                $perfis = $this->webDriver->findElements(WebDriverBy::className('title'));
                
                $arrayHashs = $this->extraiHashArray($perfis);
                $this->saveHASH($arrayHashs);

                echo "\n","============= pag : ". $i ." =========================================";
            }

        }


    }


    private function extraiHashArray(array $perfis){
        $arrayHashs=[];
        foreach($perfis as $key=>$perfil){
            $v = $perfil->getAttribute('href');
            #echo "\n\n",$v;
            if(preg_match("/\/profile\/view\?id=(.+)&authType=/",$v,$res)){
                $arrayHashs[]=$res[1];
                #echo "\n\n",$res[1];
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

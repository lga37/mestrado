<?php
class Linkedin extends PHPUnit_Framework_TestCase {
    protected $webDriver;
    protected $pdo;
    private $uploadPath='uploads';
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

    public function testBuscaGeral()  {
        $this->login();
        sleep(2);

        $perfil = "https://www.linkedin.com/vsearch/p?";
        for($i=1; $i<=3; $i++){

            $query = http_build_query([
                "locationType"=>"Y",
                "f_ED"=>10582,
                "pt"=>"people",
                "page_num"=>$i,
            ]);
            $url = $perfil . $query;
            $this->webDriver->get($url);
            sleep(3);
            $perfis[$i]=$this->webDriver->findElements(WebDriverBy::className('result-image'));
        }
    }

    public function testBuscaPorAno(){
        $this->login();
        sleep(3);
        $url="https://www.linkedin.com/edu/alumni?";
        $query = http_build_query([
                "id"=>10582,
                "dateType"=>"graduated",
                "endYear"=>2000,
                "incNoDates"=>true,
            ]);
        $url .= $query;
        $this->webDriver->get($url);
        sleep(5);
        for($i=1;$i<=4;$i++){
            $perfis[$i]=$this->webDriver->findElements(WebDriverBy::className('title'));
            $ids[$i]=$this->webDriver->findElements(WebDriverBy::className('bt-incommon'));
            $this->webDriver->executeScript("window.scrollTo(0, document.body.scrollHeight);");
            sleep(4);
        }
        sleep(4);
        $this->extraiPerfilArray($perfis);

    }

    private function extraiPerfilArray(array $perfis){
        foreach($perfis as $key=>$perfil){
            foreach($perfil as $k=>$v){
                $pos1=strpos($p,"view?id=")+8;
                $pos2=strpos($p,"&authType");
                $str[]=substr($p,$pos1,$pos2-$pos1);
            }
        }
        return $str;
    }




    private function bbbbbbbbPerfil(){
        $this->login();
        sleep(3);
        $results = $this->pdo->query("select perfil from perfis where perfil is not null;");
        $rows = $results->fetchAll(PDO::FETCH_ASSOC);
        $u = "https://www.linkedin.com/profile/view?id=".$rows[4]['perfil'];
        $this->webDriver->get($u);
        sleep(3);

        $html = $this->webDriver->execute("return $('html').html();");
        sleep(3);

        $q = "REPLACE INTO perfil (top,html) VALUES ('$top','$html') );";
        echo $q;
        $this->pdo->query($q);
    }

    public function testSaveHTMLPerfil(){
        $this->login();
        sleep(3);

        #$this->webDriver->manage()->window()->maximize();
        $results = $this->pdo->query("select perfil from perfis where perfil is not null;");
        $rows = $results->fetchAll(PDO::FETCH_ASSOC);
        #$hash = $rows[6]['perfil'];
        #var_dump($rows[0]);
        #$u = "https://www.linkedin.com/profile/view?id=ABAAAA42jqUB3fGj_UY7G2v-AQYBbMzomRBV5Q0";
        #ABAAAA42jqUB3fGj_UY7G2v-AQYBbMzomRBV5Q0
        $u = "https://www.linkedin.com/profile/view?id=".$hash;
        #echo $url_perfil;
        $this->webDriver->get($u);
        #$this->webDriver->navigate()->to($u);
        sleep(10);

        $top=$this->webDriver->findElement(WebDriverBy::className('profile-card'))->getAttribute('outerHTML');
        $html=$this->webDriver->findElement(WebDriverBy::id('background'))->getAttribute('outerHTML');

        $sql = "INSERT INTO perfil (top,html) VALUES (:top,:html)";
        $stmt = $this->pdo->prepare( $sql );
        $stmt->bindParam( ':top', $top );
        $stmt->bindParam( ':html', $html );
        
        $result = $stmt->execute();
         
        if (!$result)        {
            var_dump( $stmt->errorInfo() );
            exit;
        }
        echo $stmt->rowCount() . "linhas inseridas";

    }


    private function saveHTMLPerfil(array $html){
        global $cn;
        echo '<br>',$edu_id;
        $tabela = "perfil";
        $pk="hash";
        $alumni['hash']=$hash;
        $set="";
        foreach ($alumni as $campo => $valor) {
            $set .= $campo .'=:'.$campo.",";
        }
        $set = trim($set,",");

        if(existe($tabela,"hash",$hash)){
            $q = sprintf("UPDATE %s SET %s WHERE %s=%d;",$tabela,$set,$pk,$hash);
        } else {
            $q = sprintf("INSERT %s SET %s;",$tabela,$set);
        }
        echo '<hr>',$q;

        $stm = $cn->prepare($q);
        foreach($edu as $campo => $valor){
            $tipo = getTipo($valor);
            $stm->bindValue(':'.$campo, $valor, $tipo);
        }

        return $stm->execute();
        
    }


    private function saveHASH(array $hash){
        global $cn;
        echo '<br>',$edu_id;
        $tabela = "alumni";
        $pk="alumni_id";
        $alumni['hash']=$hash;
        $set="";
        foreach ($alumni as $campo => $valor) {
            $set .= $campo .'=:'.$campo.",";
        }
        $set = trim($set,",");

        if(existe($tabela,"hash",$hash)){
            $q = sprintf("UPDATE %s SET %s WHERE %s=%d;",$tabela,$set,$pk,$hash);
        } else {
            $q = sprintf("INSERT %s SET %s;",$tabela,$set);
        }
        echo '<hr>',$q;

        $stm = $cn->prepare($q);
        foreach($edu as $campo => $valor){
            $tipo = getTipo($valor);
            $stm->bindValue(':'.$campo, $valor, $tipo);
        }

        return $stm->execute();
        
    }

    private function existe($tabela,$pk,$valor_pk){
        global $cn;
        $q = "SELECT 1 from ".$tabela." WHERE ".$pk.'='.$valor_pk." LIMIT 1;";
        $stm = $cn->prepare($q);
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

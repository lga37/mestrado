<?php
class Rotas extends PHPUnit_Framework_TestCase {

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

    }


    public function testRota(){
        $this->login();
        sleep(10);

        $results = $this->pdo->query("select rota from rotas where feita is null or feita=0 order by time;");
        $rotas = $results->fetchAll(PDO::FETCH_ASSOC);

        $rota=$rotas[3];
        #foreach($rotas[3] as $rota){
            $u = "https://www.linkedin.com/edu/alumni?id=10582&facets=".$rota['rota'];
            $this->webDriver->get($u);
            sleep(10);

            $total = $this->webDriver->findElement(WebDriverBy::className('alumni-count'))->getText();
            echo "\ntotal = ".$total;

            $pags = ceil($total/10);

            if($pags >= 3){
                continue;
            }
            for($i=1;$i<=$pags;$i++){
                $this->webDriver->executeScript("window.scrollTo(0, document.body.scrollHeight);");
                sleep(5);

                #var_dump($links);

            }#for
            
            $links = $this->webDriver->findElements(WebDriverBy::className('title'));
            
            foreach($links as $i=>$l){
                $link = $l->getAttribute('href');
                var_dump($link);
                #echo "\n",$link;
                preg_match('/profile\/view\?id=(.*)&authType=/',$link,$res);
                var_dump($res);
                #$hash = $res[1];
                #echo "\n",$i,' - ',$hash;
                #saveHash();
            }

        #}#foreach

    }#testRota


    private function saveHash($hash){


    }


}

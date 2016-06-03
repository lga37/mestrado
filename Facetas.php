<?php
class Facetas extends PHPUnit_Framework_TestCase {

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
        $login->sendKeys('voipgus@gmail.com');
        $senha = $this->webDriver->findElement(WebDriverBy::name('session_password'));

        $senha->sendKeys('szgdfb12@');
        $submit = $this->webDriver->findElement(WebDriverBy::id('btn-primary'));
        $submit->click();
    }



    public function testFaceta(){
        $this->login();
        sleep(10);

        $u = "https://www.linkedin.com/edu/alumni?id=10582";
        $this->webDriver->get($u);
        sleep(15);

        $ul=$this->webDriver->findElement(WebDriverBy::className('alumni-facets-list'))->getAttribute('outerHTML');
        #$ul=$this->webDriver->findElement(WebDriverBy::className('carousel-content'))->getAttribute('outerHTML');
        #$uls=$this->webDriver->findElements(WebDriverBy::className('facet-wrapper'))->getAttribute('outerHTML');

        #var_dump($uls);


        #$ul2=$this->webDriver->findElement(WebDriverBy::className('carousel-viewport'))->getAttribute('outerHTML');

        #$ul=$ul1+$ul2;

        $sql = "UPDATE facetas SET ul=:ul WHERE id=1;";
        $stmt = $this->pdo->prepare( $sql );
        $stmt->bindParam( ':ul', $ul );

        $result = $stmt->execute();
         
        if(!$result){
            var_dump( $stmt->errorInfo() );
        }
         
        echo $stmt->rowCount() . "linhas inseridas";

    }



}

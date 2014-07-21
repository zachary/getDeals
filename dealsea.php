<?php
define('DS',DIRECTORY_SEPARATOR);
require __DIR__.DS.'vendor'.DS.'autoload.php';
use Goutte\Client;
class DealSea{
  const URL='http://dealsea.com';
  const PRICE='/\$[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})*/';
  private static $c=null;
  private $cond=null;
  public function __construct(){
    self::$c=new Client();
  }
  public function getTitle(){
    $a=array();
    try{
      $cr=self::$c->request('GET',self::URL);
      $a=$cr->filter('div.dealcontent > strong > a')->each(function ($node){
        return $node->text();
      });
      return $a;
    }catch(Exception $e){
      echo $e->getMessage();
      return false;
    }
  }
  public function setCond($cond){
    $this->cond=$cond;
  }
  public function doJob($mailer=null){
    $a=$this->getTitle();
    foreach ($this->cond as $key=>$value) {
      foreach ($value as $k=>$v) {
        $res=preg_grep($v['rule'],$a);
        if(count($res)>0){
          $m='';
          foreach ($res as $vv) {
            //print_r($vv);
            preg_match(self::PRICE,$vv,$matches);
            //print_r($matches);
            if(count($matches)>0 && $mailer->comparison($matches[0],$v['price']))
              $m.=$vv."\r\n";
          }
          echo "$m\r\n";
          if(!empty($m)) $mailer->email2($key,$m);
        }
      }
    }
  }
}
$a=array('zacharich@gmail.com'=>array(array('rule'=>"/macbook/i",'price'=>'$600.00'),array('rule'=>'/iphone/i','price'=>'$300.00')));
$ds=new DealSea();
//var_dump($ds->getTitle());
$ds->setCond($a);
require_once __DIR__."/myTools.php";
$mt=new MyTools();
$ds->doJob($mt);

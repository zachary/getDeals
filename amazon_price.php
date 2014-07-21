<?php
define('DS',DIRECTORY_SEPARATOR);
require __DIR__.DS.'vendor'.DS.'autoload.php';
use Goutte\Client;
class AmazonPrice{
  const URL='http://www.amazon.com/dp/';
  const PRICE='/\$[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})/';
  private static $c=null;
  public function __construct(){
    self::$c=new Client();
  }
  public function getTradeInPrice($asin){
    try{
      $cr=self::$c->request('GET',self::URL.$asin);
      $r=$cr->filter('div.tradeInButton > div > div > div');
      //print_r($r->text());
      return self::formatPrice($r->text());
    }catch(Exception $e){
      print_r($e->getMessage());
      return false;
    }
  }
  public function doJob($asins,$mailer=null){
    $m='';
    foreach ($asins as $k=>$v) {
      $p=$this->getPrices($k);
      $t=$this->getTradeInPrice($k);
      if($p && $t && $this->comparison2($p,$t,$mailer)) $m.=$v."===>http://www.amazon.com/dp/$k\r\n";
      echo "$k=>$v\r\n";
      print_r($p);
      print_r($t);
    }
    if(!empty($m)) $mailer->email2('zacharich@gmail.com',$m);
  }
  private function comparison2($price,$tradeIn,$tools){
    foreach ($price as $v) {
      if($tools->comparison($v,$tradeIn[0]))
        return true;
    }  
    return false;
  }
  public function getPrices($asin){
    try{
      $cr=self::$c->request('GET',self::URL.$asin);
      $r=$cr->filterXPath('//div[@id="olp_feature_div"]/div');
      return self::formatPrice($r->text());
    }catch(Exception $e){
      print_r($e->getMessage());
      return false;
    }
  }
  protected static function formatPrice($str){
    $str=preg_replace('/\s(?=\s)/','',trim($str));
    $str=preg_replace('/[\n\r\t]/','',$str);
    //print($str);
    preg_match_all(self::PRICE,$str,$matches);
    return isset($matches[0])?$matches[0]:false;
  }
}
$a=new AmazonPrice();
$items=array('B007471PZQ'=>'mc975','B0074721BI'=>'mc976','B007472SZ2'=>'md213','B0074703CM'=>'md101','B0074712UY'=>'md103','B007470SMM'=>'md102','B007472CIK'=>'md212','B007471D2Q'=>'md104');
//var_dump($a->getTradeInPrice());
//var_dump($a->getPrices());
require_once __DIR__.DS."myTools.php";
$mt=new MyTools();
var_dump($a->doJob($items,$mt));

<?php
define('DS',DIRECTORY_SEPARATOR);
require __DIR__.DS.'vendor'.DS.'autoload.php';
use Goutte\Client;
class eBayBid{
  private $url;
  private static $tools=null;
  const PRICE='/\$[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})/';
  const LOW='$555.99';
  const LEFT=7;
  private static $c=null;
  public function __construct($url,$tool){
    self::$c=new Client();
    $this->url=$url;
    self::$tools=$tool;
  }
  public function doJob($cond,$num=3){
    $m='';
    $arr=$this->getTimeAndPrice($num);
    print_r($arr);
    if($arr && count($arr)>0){
      foreach($cond as $k=>$c){
        foreach ($c as $key => $value) {
          // code...
          foreach ($arr as $v) {
            if(count($v)>2){
              echo $v[2]."====>".$v[3];
              if(count($v[1])>0){
                foreach ($v[1] as $kk =>$vv) {
                  preg_match($key,$kk,$matches);
                  if(count($matches)>0 && self::LEFT>$v[2] && self::$tools->comparison($v[3],$value)){
                    $m.='hurry up to bid for price '.$v[3]."\n";
                    $m.=$vv."\n\n\n";
                  }
                }
              }
            }
          }
          echo "here is $m";
          if(!empty($m)){
            $m.='http://www.ebay.com/sch/Laptops-Netbooks-/175672/i.html?_from=R40&LH_Auction=1&_nkw=macbook+pro+2012&_sop=1';
            self::$tools->email2($k,$m,'ebay bid deal');
          }
        }
      }
    }
  }
  public function getTimeAndPrice($j=3){
    try{
      $arr=array();
      $cr=self::$c->request('GET',$this->url);
      for ($i = 1; $i <= $j; $i++) {
        // code...
        $r=$cr->filterXPath('//div/table[@r="'.$i.'"]/tbody/tr')->children()->each(function ($node){
          //print_r($cr->filter('div.lnkClr  ')->html());
          //$r=$cr->filter('div > table > tbody > tr ')->children()->each(function ($node,$i){
          if('dtl dtlsp'==$node->attr('class')){
            $link=$node->filter('td > div > h3 > a')->attr('href');
            $text=$node->filter('td > div > h3 > a')->text();
            $l[$text]=$link;
          }
          if('col3'==$node->attr('class')){
            $t=$node->filter('td > span > span')->attr('timems');
          /*
          $dEbay=new DateTime();
          $dEbay->setTimestamp($t/1000);
          $dNow=new DateTime();
          echo $dEbay->format('U = Y-m-d H:i:s')."\n";
          echo $dNow->format('U = Y-m-d H:i:s')."\n";
           */
            $n=strtotime("now");
            $time=ceil(($t/1000-$n)/60);
          }
          if('prc'==$node->attr('class')){
            $price=$this->formatPrice($node->filter('td > div > span')->text());
          }
          if(isset($time)) return $time;
          if(isset($l)) return $l;
          if(isset($price[0])) return $price[0];
          return false;
        });
        $arr[$i]=array_filter($r);
      }
      return $arr;
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
require_once __DIR__.DS."myTools.php";
$mt=new MyTools();
$a=new eBayBid('http://www.ebay.com/sch/Laptops-Netbooks-/175672/i.html?_from=R40&LH_Auction=1&_nkw=macbook+pro+2012&_sop=1',$mt);
//print_r($a->getTimeAndPrice());
//exit;
$condition=array('zacharich@gmail.com'=>array('/md103/i'=>'$666.77','/md101/i'=>'$555.55','/md213/i'=>'$700.00','/md102/i'=>'$599.99'));
$a->doJob($condition,5);

<?php
require_once __DIR__.DS."vendor".DS."autoload.php";
use Swiftmailer\Swiftmailer;
class MyTools{
  protected static $db;
  protected static $mailer;
  public function __construct(){
//    self::$db=new medoo(array('database_type'=>'mysql',
//      'database_name'=>'test',
//      'server'=>'localhost',
//      'username'=>'zac',
//      'password'=>'xxxxxxx'
//    ));
    $t=Swift_SmtpTransport::newInstance("smtpout.secureserver.net",465,'ssl')->setUsername('zachary@peace2israel.com')->setPassword('xxxxxxx');
    self::$mailer=Swift_Mailer::newInstance($t);
  }
  public function email2($addr,$message){
    $me=Swift_Message::newInstance('dealsea')->setFrom(array('zachary@peace2israel.com'=>'Good deal for your wanted'))->setTo(array($addr))->setBody($message);
    return self::$mailer->send($me);
  }
  public function comparison($a,$b){
    $a=str_replace('$','',$a);
    $b=str_replace('$','',$b);
    $a=str_replace(',','',$a);
    $b=str_replace(',','',$b);
    if($a>$b) return false;
    else return true;
  }  
}

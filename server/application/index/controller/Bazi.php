<?php
namespace app\index\controller;
use app\index\service\Paipan;
use app\index\service\MangPai;
class Bazi
{
   

   function api($sex,$time){
        $time = strtotime($time);
        $yy = date('Y',$time);
        $mm = date('m',$time);
        $dd = date('d',$time);
        $hh = date('H',$time);
        $ii = date('i',$time);
        $p = new Paipan();
        $info = $p->GetInfo($sex, $yy, $mm, $dd, $hh, $ii, 0); 
        jsonReturn(['pan'=>$info]);
   }

   function test(){
        $p = new Paipan();
        $p->zwz = true; //分早晚子时
        $info = $p->GetInfo(0, 1989, 2, 4, 4, 59, 0); 
        dump($info);
   }
   function test2(){
        $p = new Paipan();
        $p->zwz = true; //分早晚子时
        $info = $p->GetInfo(0, 1989, 2, 4, 2, 59, 0); 
        dump($info);
    }
    function test22(){
        $p = new MangPai();
        $p->zwz = true; //分早晚子时
        $info = $p->GetInfo(0, 1989, 2, 4, 2, 30, 0); 
        dump($info);
    }

    function test3(){
        $p = new Mangpai();
        $p->zwz = true; //分早晚子时
        $info = $p->GetInfo(1, 2017, 12, 21, 13,30, 0); 
        dump($info);
    }

    function test4(){
        $p = new MangPai();
        $p->zwz = true; //分早晚子时
        $info = $p->GetInfo(0, 1989, 2, 4, 2, 30, 0); 
        //dump($info);
    }

    function test5(){
        //1994-10-31
       // $this->test3();
        $p = new MangPai();
       // $p->zwz = true; //分早晚子时
        $info = $p->GetInfo(1, 1994, 10, 31, 13,30, 0); 
        dump($info);
    }

    function test6(){
        //1990年5月27，下午，6点左右出生,排行老大，独子
        $p = new MangPai();
        $info = $p->GetInfo(1, 1990, 5, 27, 18,10, 0); 

    }

    function test7(){
        //guo郭家大少
       // $this->test3();
        $p = new MangPai();
       // $p->zwz = true; //分早晚子时
        $info = $p->GetInfo(0, 2019, 5, 12, 0,45, 0); 
        dump($info);
    }
}

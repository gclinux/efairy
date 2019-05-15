<?php
namespace app\index\controller;

class Index extends \think\Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

    function getJieQi()   
    {
    	header("Content-Type:application/json;charset=utf-8");  
    	$_year=input("get.year");
		$month=input("get.month");
		$day=input("get.day");
        $year = substr($_year,-2)+0;   
        $coefficient = array(   
            array(5.4055,2019,-1),//小寒   
            array(20.12,2082,1),//大寒   
            array(3.87),//立春   
            array(18.74,2026,-1),//雨水   
            array(5.63),//惊蛰   
            array(20.646,2084,1),//春分   
            array(4.81),//清明   
            array(20.1),//谷雨   
            array(5.52,1911,1),//立夏   
            array(21.04,2008,1),//小满   
            array(5.678,1902,1),//芒种   
            array(21.37,1928,1),//夏至   
            array(7.108,2016,1),//小暑   
            array(22.83,1922,1),//大暑   
            array(7.5,2002,1),//立秋   
            array(23.13),//处暑   
            array(7.646,1927,1),//白露   
            array(23.042,1942,1),//秋分   
            array(8.318),//寒露   
            array(23.438,2089,1),//霜降   
            array(7.438,2089,1),//立冬   
            array(22.36,1978,1),//小雪   
            array(7.18,1954,1),//大雪   
            array(21.94,2021,-1)//冬至   
        );   
        $term_name = array(      
        "小寒","大寒","立春","雨水","惊蛰","春分","清明","谷雨",      
        "立夏","小满","芒种","夏至","小暑","大暑","立秋","处暑",      
        "白露","秋分","寒露","霜降","立冬","小雪","大雪","冬至");   
           
        $idx1 = ($month-1)*2;   
        $_leap_value = floor(($year-1)/4);   
        $day1 = floor($year*0.2422+$coefficient[$idx1][0])-$_leap_value;
        if(isset($coefficient[$idx1][1])&&$coefficient[$idx1][1]==$_year) $day1 += $coefficient[$idx1][2];   
        $day2 = floor($year*0.2422+$coefficient[$idx1+1][0])-$_leap_value;   
        if(isset($coefficient[$idx1+1][1])&&$coefficient[$idx1+1][1]==$_year) $day1 += $coefficient[$idx1+1][2];   
          
        //echo __FILE__.'->'.__LINE__.' $day1='.$day1,',$day2='.$day2.'<br/>'.chr(10);
        $data=array();
        if($day<$day1){
            $data['name1']=$term_name[$idx1-1];
            $data['name2']=$term_name[$idx1-1].'后';
        }else if($day==$day1){
            $data['name1']=$term_name[$idx1];
            $data['name2']=$term_name[$idx1];
        }else if($day>$day1 && $day<$day2){
            $data['name1']=$term_name[$idx1];
            $data['name2']=$term_name[$idx1].'后';
        }else if($day==$day2){
            $data['name1']=$term_name[$idx1+1];
            $data['name2']=$term_name[$idx1+1];
        }else if($day>$day2){
            $data['name1']=$term_name[$idx1+1];
            $data['name2']=$term_name[$idx1+1].'后';
        }
        echo json_encode($data);
    }

}

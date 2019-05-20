<?php
namespace app\index\service;
use app\index\service\Paipan;
class Mangpai{
    private $pan;
    private $des=[];//所有结论描述
    private $mark = [];
    function __construct(){
        $this->pan = new Paipan;
    }
    /**
     * 用分数来判断五行平衡,天干阳加1,阴加0.5,地支+1,藏干阳+0.5,阴+0.25,三合+1,半合加0.5,季节+0.3
     */
    private function wuXingPingHeng($info){
        $tg = $info['tg'];
        $dz = $info['dz'];
        $wx_fen = [0.0,0.0,0.0,0.0,0.0];//木火土金水得分
        for($i=0;$i<4;$i++){
            $tg_wx = $this->pan->GetTgWx($tg[$i]);
            if($tg[$i]%2 == 0){
                $wx_fen[$tg_wx]++;
            }else{
                $wx_fen[$tg_wx] += 0.5;
            }
            $dz_wx = $this->pan->dzwx[$dz[$i]];
            if($dz[$i]%2 == 0 ){
                $wx_fen[$tg_wx]++;
            }else{
                $wx_fen[$tg_wx] += 0.5;
            }

            if(in_array($dz[$i],[0,3,6,9])){
                $sanhe = $this->pan->getSanHe($dz[$i]);
                for($j=0;$j>4;$j++){
                    if($i==$j){
                        continue;
                    }
                    if(in_array($dz[$j],[$sanhe['sanhe'][1],$sanhe['sanhe'][2]])){
                        $wx_fen[$sanhe['ju']]+=0.5;
                    }
                }
                //if(in_array())
            }

            $dzcg = $this->pan->dzcg[$dz[$i]];
            foreach($dzcg as $cg){
                $tg_wx = $this->pan->GetTgWx($cg);
                if($cg%2 == 0){
                    $wx_fen[$tg_wx] += 0.5;
                }else{
                    $wx_fen[$tg_wx] += 0.25;
                }
            }
        }
        //季节中蕴含的五行
       // if(in_array($dz[1],[2,3,4])){
         //   $wx_fen[0]+=0.3;
        //}elseif(in_array($dz[1],[5,6,7])){
          //  $wx_fen[1]+=0.3;
        //}elseif(in_array($dz[1],[8,9,10])){
          //  $wx_fen[3]+=0.3;
        //}elseif(in_array($dz[1],[11,0,1])){
          //  $wx_fen[4]+=0.3;
        //}
        $bianbang=[
            '木,艹,禾',
            '日,火,灬,光',
            '土,玉,王,月',
            '金,钅',
            '水,氵'
        ];
        $this->des[]="此人五行得分:木{$wx_fen[0]},火{$wx_fen[1]},土{$wx_fen[2]},金{$wx_fen[3]},水{$wx_fen[4]}";
        $max = array_search(max($wx_fen),$wx_fen);
        $min = array_search(min($wx_fen),$wx_fen);
        $this->des[]="其中".$this->pan->cwx[$min]."最少,建议改名时候尽量考虑{$bianbang[$min]}的边旁的字.并且你会发现名字中带有这些边旁的人会对你比较好.";
        $this->des[]="其中".$this->pan->cwx[$max]."最多,建议改名时候尽量避开{$bianbang[$max]}的边旁的字.";
       // dump($wx_fen);
    }

    /**
     * 判断兄弟姐妹总数,根据盲派的口诀得来,部分命格需要除以二,但我资料有限,不知哪些状况需要除以2
     * @param array $info 排盘结果信息
     * 
    */
    private function getBroSisNum($info){
        $tg_god = $info['tg_cg_god'];
        $dz_cg_god = $info['dz_cg_god'];
        //找出所有比劫和印的数量
        $yin = $bi = 0;
        for($i=0;$i<4;$i++){
            if($tg_god[$i]['index'][0]==0){
                $bi++;
            }elseif($tg_god[$i]['index'][0] == 4){
                $yin++;
            }
            foreach($dz_cg_god[$i] as $j=>&$dz_god){
                if($dz_god['index'][0] == 0){
                    $bi++;
                }elseif($dz_god['index'][0] == 4){
                    $yin++;
                }
            }
        }
        if($bi>0){
            $num = $bi;
        }else{
            $num = $yin;
        }
        //查找6合
        $dz = $dz_cp = $info['dz'];
        for($i = 0;$i<4;$i++){
            $he = $this->pan->getLiuHe($dz[$i]);
            unset($dz_cp[$i]);
            if(in_array($he['index'],$dz_cp)){
                  $num+=2;
            }
            if($he['ju'] == $this->pan->GetTgWx($info['tg'][2])){
                $num++;
            }
            
        }
        $this->des[]="你兄弟姐妹大概为{$num}或者".intval($num/2)."个";
    }
 
    /**
     * 判断兄弟姐妹排行
     * @param array $tg 天干,排盘中得到的天干信息
     * @param array $dz 地枝,排盘中得到的地支信息
     * @param array $gd 性别,0为男 1 为女
     */
    private function getBronNum($tg,$dz,$gd){
        //参考http://blog.sina.com.cn/s/blog_71ae96960101f6w0.html,经过测试,不准
        if(($tg[2] == 2 && $dz[2] == 2) or ($tg[2] == 4 && $dz[2] == 2) or ($tg[2] == 8 && $dz[2] == 8)){
            //日干为 丙寅、戊寅、壬申的家中必定为大
            if($gd == 0){
                $this->des[] = '你是家中兄弟姐妹的大哥,兄弟(不含姐妹)中排行第一;';
            }else{
                $this->des[] = '你是家中兄弟姐妹的大姐,姐妹(不含兄弟)中排行第一;';
            }
        }elseif(($tg[2] == 2 && $dz[2] == 8) or ($tg[2] == 4 && $dz[2] == 8) or ($tg[2] == 8 && $dz[2] == 2) ){
            //丙申、戊申、壬寅支冲长生,定居长位,如有兄姐必克
            if($gd == 0){
                $tmp_des = '你是家中兄弟姐妹的大哥,兄弟(不含姐妹)中排行第一,但可能不是第一胎男娃(可能有夭折或者流产)';
            }else{
                $tmp_des = '你是家中兄弟姐妹的大姐,姐妹(不含兄弟)中排行第一,但可能不是第一胎女娃(可能有夭折或者流产)';
            }
            $this->des[] = $tmp_des;
        }elseif($tg[2] == 0 or $tg[2] == 6){
            // 庚、甲为阳干；巳、亥为阴支。
            //庚干长生在巳,逆推—位为庚辰，庚辰为大，庚寅为次，庚子为三......
            //甲干长生在亥,逆推一位为甲戍，甲戍为大，甲申为次，甲午为三......
          //  $cs_dz_index = $this->pan->cs_tg2dz[$tg[2]];//长生所在地支
           // dump($this->pan->cdz[cs_dz_index]);
          //  $pai = $cs_dz_index-1 - $dz[2];
           // if(($pai%2==0) and $pai>=0){
           //     $this->des[]='你在家中兄弟姐妹中排行第'.($pai/2+1); //不准
           // }
        }elseif(($dz[2] == 9 && in_array($tg[2],[2,3])) or ($dz[2] == 9 && $dz[2] == 3)){
            //丁酉、己酉、癸卯”三柱自坐长生,就叫阴干阴生。如不遭刑冲必是老大。
            $chong = (6 + $dz[2])%12;
            $xing = $this->pan->getXingFrom($dz[2]);
            $has_xingchong=false;
            for($i=0;$i<4;$i++){
                if($i != 2){
                    if(in_array($dz[$i],[$xing,$chong])){
                        $has_xingchong = true;
                        break;
                    }
                }
            }
            if($has_xingchong == false){
                $this->des[]='你是家中兄弟姐妹的大姐,姐妹(不含兄弟)中排行第一';
            }
        }elseif($tg[2] == 1 or $tg[2] == 7){
            //乙、辛为阴干；午子为阳支。
            //乙木长生在午,顺推一位是乙未,乙未为老大，乙酉为次，乙亥为小......
            //辛金长生在子,顺推一位是辛丑，辛丑为老大，辛卯为次，辛巳为小......
            //$cs_dz_index = $this->pan->cs_tg2dz[$tg[2]];//长生所在地支
            //$pai =   $dz[2] - $cs_dz_index+1;
            //echo $pai;
            //if(($pai%2==0) and $pai>=0){
               // if($gd==0){
             //       $this->des[]='你在家中兄弟(不含姐妹)中排行第'.($pai/2+1);
              //  }else{
              //      $this->des[]='你在家中姐妹(不含兄弟)排行第'.($pai/2+1);
              //  }
                
            }
        }


    }

    /**
     * 纯阴纯阳
     * @param array $info 排盘结果
     */
    function aboutCunYinYang(&$info){
        //判断是否纯阴纯阳
        $cunyin = true;
        $cunyang = true;
        for($i=0;$i<4;$i++){
            if($info['tg'][$i]%2 == 0 or $info['dz'][$i]%2 == 0){
                $cunyin = false;
                break;
            }
        }
        for($i=0;$i<4;$i++){
            if($info['tg'][$i]%2 == 1 or $info['dz'][$i]%2 == 1){
                $cunyang = false;
                break;
            }
        }
        if($cunyin){
           $this->des[]='此人如果没有正式的干爹或者母亲没有重婚过,那么此人的亲父命不怎么长,很早就"走了";';
           $this->mark['keFather'] = true;
           $this->des[]='此人做事犹豫,无主见，爱撒娇，爱缠人，说话阴声细气，缺乏独当一面,';
           if($info['sex'] == 0){
                $this->des[] = '虽然是个男人,但真的有些娘;';
           }else{
                $this->des[] = '她虽然柔弱无主见,但心机重,外面是林黛玉,里面是薛宝钗,也别以为她好欺负.';
           }
           $this->des[]='此人像母亲，不论在性格上还是在外貌上都像;';
        }
      
        if($cunyang){
            $this->des[]='此人如果没有正式的干娘或者父亲没有重婚过,那么此人的母亲命不怎么长,很早就"走了"';
            $this->mark['keMother'] = true;
            $this->des[]='人性格阳刚豪爽，光明磊落，诚实,心直口快';
            if($info['sex'] == 1){
                $this->des[] = '虽然是个女人,但某层面上确实是个女汉子;';
           }
           $this->des[]='此人像父亲，不论在性格上还是在外貌上都像;';
        }

       
    }

    /**
	 * 公历年排盘
	 * @param int $gd 0男1女
	 * @param int $yy
	 * @param int $mm
	 * @param int $dd
	 * @param int $hh 时间(0-23)
	 * @param int $mt 分钟数(0-59),在跨节的时辰上会需要,有的排盘忽略了跨节
	 * @param int $ss 秒数(0-59)
	 * @return array
	 */
    function getInfo($gd, $yy, $mm, $dd, $hh, $mt=0, $ss=0){
        $info = $this->pan->getInfo($gd, $yy, $mm, $dd, $hh, $mt, $ss);
        $this->wuXingPingHeng($info);
        $this->aboutCunYinYang($info);
        $this->getBroSisNum($info);
        $this->getBronNum($info['tg'],$info['dz'],$info['sex']);
        //dump($this->des);
        return $this->des;

    }

    


}
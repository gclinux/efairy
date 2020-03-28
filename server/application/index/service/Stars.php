<?php
namespace app\index\service;
class StarsCheck{

	/**
     * 天乙
     甲戊并牛羊，乙己鼠猴乡
	 丙丁猪鸡位，壬癸兔蛇藏
	 庚辛逢虎马，此是贵人方
	 以年干查的之贵人为大贵人、以日干查的之贵人为小贵人
     */
    public function tianYi($info,&$star,$key,$value){
    	$tg = $info['tg'];
    	$dz = $info['dz'];
    	$map = [
    		0=>[1,7],
    		1=>[0,8],
    		2=>[9,11],
    		3=>[9,11],
    		4=>[1,7],
    		5=>[0,8],
    		6=>[6,2],
    		7=>[6,2],
    		8=>[3,5],
    		9=>[3,5]
    	];
    	$tianyi = $map[$tg[0]]+$map[$tg[2]];//日或年
    	for($i = 0;$i<4;$i++){
    		if(in_array($dz[$i], $tianyi)){
    			$star[$i][$key]=$value;
    		}
    	}
    }

    /**
     * 太极贵人
     *甲乙生人子午中，丙丁鸡兔定亨通;
     *戊己两干临四季，庚辛寅亥禄丰隆;
     *壬癸巳申偏喜美，值此应当福气钟;以年干或日干为准，四柱地支见者为是
     */
    public function taiJi($info,&$star,$key,$value){
    	$tg = $info['tg'];
    	$dz = $info['dz'];
    	$map = [
    		0=>[0,6],
    		1=>[0,6],
    		2=>[3,9],
    		3=>[3,9],
    		4=>[4,10,1,7],
    		5=>[4,10,1,7],
    		6=>[2,11],
    		7=>[2,11],
    		8=>[5,7],
    		9=>[5,7]
    	];

    	$taiji = $map[$tg[0]]+$map[$tg[2]];//日或年
    	for($i = 0;$i<4;$i++){
    		if(in_array($dz[$i], $taiji)){
    			$star[$i][$key]=$value;
    		}
    	}
    }

	/**
	 *天医:正月生见丑，二月生见寅，三月生见卯，四月生见辰，五月生见巳，六月生见午，七月生见未，八月生见申，九月生见酉，十月生见戌，十一月生见亥，十二月生见子。以月支查其它地支，见者为是
	 */
    public function tianYi($info,&$star,$key,$value){
		$dz = $info['dz'];
    	$tg = $info['tg'];
    	$tianyi = ($info['dz'][1]+12-1)%12;//以月份算,实现上面的口诀
    	for($i = 0;$i<4;$i++){
    		if ($i == 1) {//不看月支
    			continue;
    		}
    		if($tianyi == $dz[$i]){
    			$star[$i][$key]=$value;
    		}
    	}
	}
	/**
	 * 天德
	 *正月2生者见丁3，二月3生者见申8，三月4生者见壬8，四月生者见辛，五月生者见亥，六月生者见甲，七月生者见癸，八月生者见寅，九月生者见丙，十月生者见乙，十一月0生者见巳，十二月1生者见庚。
	 */
    public function tianDe($info,&$star,$key,$value){
    	$dz = $info['dz'];
    	$tg = $info['tg'];
    	//找不出什么规律
    	$tiandeMap = [
    		0=>[1,5],//1为地枝,子月对应为巳=>5
    		1=>[0,6],//0为天干,丑月对应为庚=>6
    		2=>[0,3],//丁
    		3=>[1,8],//申
    		4=>[0,8],//壬
    		5=>[0,7],//辛
    		6=>[1,11],//亥
    		7=>[0,0],//甲
    		8=>[0,9],//癸
    		9=>[1,2],//寅
    		10=>[0,2],//丙
    		11=>[0,1]//乙
    	] ;

    	$tiande = $tiandeMap[$dz[1]];
    	if($tiande[0] == 0){
    		$check = $tg;
    	}else{
    		$check = $dz;
    	}
    	for($i=0;$i<4;$i++){
    		if($check[$i] == $tiande[1]){
    			$star[$i][$key]=$value;
    		}
    	}
    }
    /**
     *月德贵人
     * 寅午戍月在辛7，申子辰月在丁3,
	 * 亥卯未月在己5，巳酉丑月在乙1。
     */
    function yueDe($info,&$star,$key,$value){
    	$tg = $info['tg'];
    	$dz = $info['dz'];
    	$map = [
    		3,//子-丁
    		1,//丑-乙
    		7,//寅-辛
    		5,//卯-己
    		3,
    		1,
    		7,
    		5,
    		3,
    		1,
    		7,
    		5
    	];
    	$yuede = $map[$dz[1]];
    	for($i = 0;$i<4;$i++){
    		if($yuede == $tg[$i]){
    			$star[$i][$key]=$value;
    		}
    	}
    }

    

    /**
     * 禄神
     *
     */


    
}
class Stars{
    /**贵人的序号及名字
     * @var $star_name array
     */
    public $star_name = [
    	['tianYi','天乙'],
        ['taiJi','太极'],
        ['tianYi','天医'],
        ['tianDe','天德'],
        ['yueDe','月德'],
        ['luShen','禄神'],
        ['yangRen','羊刃'],
        ['guLuan','孤鸾'],
        ['kongWang','空亡'],
        ['sanQi','三奇'],
        ['tianXie','天赦'],
        ['xiuDe','德秀'],
        ['kuiGang','魁罡'],
        ['jingLuo','金神'],
        ['tianLuo','天罗'],
        ['diWang','地网'],
        ['wenChang','文昌'],
        ['jinYu','金舆'],
        ['fuXing','福星'],
        ['guoYin','国印'],
        ['tianChu','天厨'],
        ['xueTang','学堂'],
        ['hongYang','红艳'],
        ['liuXia','流霞'],
        ['jiangXing','将星'],
        ['huaGai','华盖'],
        ['yiMa','驿马'],
        ['jieSha','劫煞'],
        ['wangShen','亡神'],
        ['yuanChen','元辰(大耗)'],
        ['guChen','孤辰'],
        ['guaSu','寡宿'],
        ['diSha','的煞'],
        ['zhaiSha','灾煞'],
        ['liuE','六厄'],
        ['gouSha','勾煞'],
        ['jiaoSha','绞煞'],
        ['tongZi','童子'],
        ['ciGuan','词馆'],
        ['hongLian','红鸾'],
        ['tianXi','天喜'],
        ['taoHua','桃花'],
        ['ganLu','干禄'],
        ['shiLing','十灵'],//甲申、乙酉、丙子、丁丑、戊午、己丑、庚寅、辛卯、壬午、癸未日
        ['shibai','十恶大败'],
    ];



    /*
	public $ctg = array('甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸'); //char of TianGan
	 * 五行
	public $cwx = array( '木', '火', '土','金','水'); //char of WuXing
	 * 十二地支
	 * @var array
	public $cdz = array('子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥');
    */

    function getStars($info){
    	$star = [[],[],[],[]];//年,月,日,时
    	
    	

    }

}

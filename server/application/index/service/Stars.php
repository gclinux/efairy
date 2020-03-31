<?php
namespace app\index\service;
class StarsCheck{
	/**	
	 * 空亡,在Paipan类里已经实现了
	 */
    public function kongWang($info,&$star,$key,$value){
		$kongWang = $info['xw']['index'];
		$dz = $info['dz'];
		for($i = 0;$i<4;$i++){
			if(\in_array($dz[$i],$kongWang)){
				$star[$i][$key]=$value;
			}
		}
	}

	/**
     * 天乙
     *甲戊并牛羊，乙己鼠猴乡
	 *丙丁猪鸡位，壬癸兔蛇藏
	 *庚辛逢虎马，此是贵人方
	 *以年干查的之贵人为大贵人、以日干查的之贵人为小贵人
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

    	$taiji = array_merge($map[$tg[0]],$map[$tg[2]]);//日或年
    	for($i = 0;$i<4;$i++){
    		if(in_array($dz[$i], $taiji)){
    			$star[$i][$key]=$value;
    		}
    	}
    }

	/**
	 *天医:正月生见丑，二月生见寅，三月生见卯，四月生见辰，五月生见巳，六月生见午，七月生见未，八月生见申，九月生见酉，十月生见戌，十一月生见亥，十二月生见子。以月支查其它地支，见者为是
	 */
    public function tianYi2($info,&$star,$key,$value){
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
     *甲禄在寅，乙禄在卯， 丙戊禄在巳，丁己禄在午，庚禄在申, 辛禄在酉，壬禄在亥，癸禄在子.以日查4枝
     */

    public function lushen($info,&$star,$key,$value){
    	$tg = $info['tg'];
    	$dz = $info['dz'];
    	$map = [2,3,5,6,5,6,8,9,11,0];
    	$day = $tg[2];
    	$tmp = $map[$day];
    	for($i = 0;$i<4;$i++){
    		if($tmp == $dz[$i]){
    			$star[$i][$key]=$value;
    		}
    	}
    }

    /**
     * 羊刃:八字中，以日干:甲木见卯为羊刃;丙火、戊土见午为羊刃;庚金见酉为羊刃;壬水见子为羊刃,阴干没有羊刃
     */
    public function yangRen($info,&$star,$key,$value){
    	$tg = $info['tg'];
    	$dz = $info['dz'];
    	$day = $tg[2];
    	$map = [
    		0=>3,
    		2=>6,
    		4=>6,
    		6=>9,
    		8=>0
    	];

    	if(isset($map[$day])){
    		$tmp = $map[$day];
    		for($i = 0;$i<4;$i++){
    			if($tmp == $dz[$i]){
    				$star[$i][$key]=$value;
	    		}
	    	}
    	}
    }

    /**
     * 孤鸾,日柱和时柱上只要同时出现以下干支组合中的任何两组，就会“命犯孤鸾”：乙巳、丁巳、辛亥、戊申、甲寅、戊午、壬子、丙午。
     *
     */
	public function guLuan($info,&$star,$key,$value){
		$tg = $info['tg'];
    	$dz = $info['dz'];
		$map = [
			'1-5',//乙巳
			'3-5',//丁巳
			'7-11',//辛亥
			'4-8',//戊申
			'0-2',//甲寅
			'4-6',//戊午
			'8-0',//壬子
			'2-6',//丙午
		];
		$day = $tg[2].'-'.$dz[2];
		$hour= $tg[2].'-'.$dz[3];
		if(in_array($day,$map) and \in_array($hour,$map)){
			$star[2][$key] = $value;
			//$star[3][$key] = $value;
		}
	}

	/**
	 * 三奇贵人,即奇门遁甲里的三奇
	 * 天上三奇甲戊庚;地上三奇乙丙丁;人中三奇壬癸辛。三奇不管顺逆，只要连续三个天干相连，即可入格，重逢更贵。
	 */
	public function sanQi($info,&$star,$key,$value){
		$tg = $info['tg'];
		$tg = implode('-',$tg);
		$map = [
			'0-4-6',//甲戊庚
			'6-4-0',
			'1-2-3',//乙丙丁
			'3-2-1',
			'8-9-6',//壬癸辛
			'6-9-8'
		];
		foreach($map as $sanqi){
			$v = strpos($tg,$sanqi);
			if($v === false){
				continue;
			}else{
				if($v==2){
					$star[3][$key] = $value;
				}else{
					$star[0][$key] = $value;
				}
				$star[1][$key] = $value;
				$star[2][$key]=$value;
				break;
			}
		}
	}
	/**
	 * 天赦,寅卯辰月见戊寅日；巳午未月见甲午日；申酉戌月见戊申日；亥子丑月见甲子日，都是天赦。
	 * 
	 */

	public function tianXie($info,&$star,$key,$value){
		$dz=$info['dz'];
		$m = $info['dz'];
		if(\in_array($dz[1],[2,3,4])){///寅卯辰月
			if($tg[2] == 4 and $dz[2] == 2){
				$star[2][$key] = $value;
			}
		}elseif(\in_array($dz[1],[5,6,7])){
			if($tg[2] == 0 and $dz[2] == 6){
				$star[2][$key] = $value;
			}
		}elseif(\in_array($dz[1],[8,9,10])){
			if($tg[2] == 4 and $dz[2] == 8){
				$star[2][$key] = $value;
			}

		}elseif(\in_array($dz[1],[11,0,1])){
			if($tg[2] == 0 and $dz[2] == 0){
				$star[2][$key] = $value;
			}
		}
	}

	/**
	 * 德秀 :
	 * 在寅月、午月、戌月出生的人，如果八字中天干见戊癸合，另外四柱中再有丙、丁其中之一者，为德秀贵人。
	 * 在申月、子月、辰月出生的人，如果八字中天干见丙辛合、甲己合，另外四柱中再有壬、癸、戊、己其中之一者，为德秀贵人。
     * 在巳月、酉月、丑月出生的人，如果八字中天干有乙庚合，另外四柱中再有庚、辛其中之一者，为德秀贵人。
     * 在亥月、卯月、未月出生的人，如果八字中天干有丁壬合，另外四柱中再有甲、乙其中之一者，为德秀贵人。
	 * 都是三合局的月
	 */
	public function deXiu($info,&$star,$key,$value){
		$dz=$info['dz'];
		$tg = $info['tg'];
		//$de = $xiu = []; //德必须两个都要,秀只需要一个
		if(\in_array($dz[1],[2,6,10])){///寅午戌
			if(in_array(2,$tg) && in_array(3,$tg)){
				$xiu = array_merge(array_keys($tg,2,false) , array_keys($tg,3,false));
			}
		}elseif(\in_array($dz[1],[8,0,4])){//申子辰
			if((in_array(2,$tg) and in_array(7,$tg)) or  (in_array(0,$tg) and in_array(5,$tg))){
				$xiu = array_merge(array_keys($tg,8,false) , array_keys($tg,9,false),array_keys($tg,4,false),array_keys($tg,5,false));
			}
		}elseif(\in_array($dz[1],[5,9,1])){//巳酉丑
			if(in_array(1,$tg) and in_array(6,$tg)){
				$xiu = \array_key($tg,6,false);
				unset($xiu[0]);
				$xiu = array_merge($xiu,array_key($tg,7,false));
			}
		}elseif(\in_array($dz[1],[11,3,7])){//亥卯未
			if(in_array(3,$tg) && in_array(8,$tg)){
				$xiu =  array_merge(array_keys($tg,0,false) , array_keys($tg,1,false));
			}
		}
		foreach($xiu as $i){
			$star[$i][$key] = $value;
		}
	}

	/**
	 * 魁罡:壬辰，庚戍，庚辰，戊戍。日柱是这四组的话，就是命带魁罡了
	 */
	public function kuiGang($info,&$star,$key,$value){
		$day = $info['tg'][2].'-'.$info['dz'][2];
		$map = [
			'8-4','7-10','7-4','4-10'
		];
		if(in_array($day,$map)){
			$star[2][$key] = $value;
		}
	}

	/**
	 * 金神,　
	 * 算法1:日柱是乙丑日、己巳日、癸酉日,月支有火星者方眞。否则须运逢火地才能富贵。
	 * 算法2:时是乙丑时、己巳时、癸酉时,但必需日干甲或者己方是,否则非。又须月支见火星方眞, 否则须运逢火鄕方能富贵。
	 * 
	 */
	function jingShen($info,&$star,$key,$value){
		$map = [
			'1-1',
			'5-5',
			'8-8'
		];
		$fire = [
			2,//寅有丙火
			5,//蛇有丙火
			6,//马有丁火
			7,//未有丁火
			10//狗有丁火

		];
		$day = $info['tg'][2].'-'.$info['dz'][2];
		if(in_array($day,$map)){
			if(in_array($info['dz'][1],$fire)){
				$value.= '(带火)';
			}else{
				$value.='(无火)';
			}
			$star[2][$key] = $value;
		}else if(($info['tg'][2] == 0 or $info['tg'][2] == 1)){
			$hour = $info['tg'][3].'-'.$info['dz'][3];
			if(in_array($hour,$map)){
				if(in_array($info['dz'][1],$fire)){
					$value.= '(带火)';
				}else{
					$value.='(无火)';
				}
			}
			$star[2][$key] = $value;
		}
	}

	/**
	 * 天罗
	 * ，年柱为戊子、己丑、丙寅、丁卯、甲辰、乙巳、戊午、己未、丙申、丁酉、甲戌、乙亥(纳音为火)的八字，而日支中又出现了戌或亥为天罗本文
	 */
	public function tianLuo($info,&$star,$key,$value){
		if($info['dz'][2]==10 || $info['dz']==11){
			$map = [
				'4-0','5-1','2-2','3-3','0-4','1-5','4-6','5-7','2-8','3-9','0-10','1-11'
			];
			$year = $info['tg'][0].'-'.$info['dz'][0];
			if(in_array($year,$map)){
				$star[2][$key] = $value;
			}
		}
	}


	 /**
	  * 地网
	  *年柱为丙子、丁丑、甲寅、乙卯、壬辰、癸巳、丙午、丁未、甲申、乙酉、壬戌、癸亥；庚子、辛丑、戊寅、己卯、丙辰、丁巳、庚午、辛未、戊申、己酉、丙戌、丁亥的八字，在日支当中又出现了辰或巳为地网
	  */
	public function diWang($info,&$star,$key,$value){
		if($info['dz'][2]==4 || $info['dz']==5){
			$map = [
				'2-0','4-1','0-2','1-3','8-4','9-5','2-6','3-7','0-8','1-9','8-10','9-11',
				'6-0','7-1','4-2','5-3','2-4','3-5','7-6','8-7','4-8','5-9','2-10','3-11'
			];
			$year = $info['tg'][0].'-'.$info['dz'][0];
			if(in_array($year,$map)){
				$star[2][$key] = $value;
			}
		}
	}

	/**
	 * 文昌,wenChang
	 * 日干或者年干为甲，地支见巳者；
	 * 日干或年干为乙，地支见午者；
	 * 日干或年干为为丙，地支见申者；
	 * 日干或年干为丁，地支见酉者；
	 * 日干或年干为戊，地支见申者；
	 * 日干或年干为己，地支见酉者；
	 * 日干或年干为庚，地支见亥者；
	 * 日干或年干为辛，地支见子者；
	 * 日干或年干为壬，地支见寅者；
	 * 日干或年干为癸，地支见卯者。以上皆为文昌贵人
	 */

	public function wenChang($info,&$star,$key,$value){
		$tg = $info['tg'];
		$dz = $info['dz'];
		$find = false;
		if($tg[2] == 0 or $tg[0] == 0 ){
			$find = 5;
		}elseif($tg[2] == 1 or $tg[0] == 1 ){
			$find=6;
		}elseif($tg[2] == 2 or $tg[0] == 2 ){
			$find=8;
		}elseif($tg[2] == 3 or $tg[0] == 3 ){
			$find = 9;
		}elseif($tg[2] == 4 or $tg[0] == 4 ){
			$find = 8;
		}elseif($tg[2] == 5 or $tg[0] == 5 ){
			$find = 9;
		}elseif($tg[2] == 6 or $tg[0] == 6 ){
			$find=11;
		}elseif($tg[2] == 7 or $tg[0] == 7 ){
			$find = 0;
		}elseif($tg[2] == 8 or $tg[0] == 8 ){
			$find = 2;
		}elseif($tg[2] == 9 or $tg[0] == 9 ){
			$find = 3;
		}
		if($find !== false){
			for($i = 0;$i<4;$i++){
				if($dz[$i] == $find){
					$star[$i][$key] = $value;
				}
			}
		}
	}

	/**
	 * 金舆
	 * 甲龙乙蛇丙戊羊，丁己猴歌庚犬方，辛猪壬牛癸逢虎，凡人遇此福气昌,以日干为主，四支见者为是。
	 */
	public function jinYu($info,&$star,$key,$value){
		$find == false;
		switch($info['tg'][2]){
			case 0:
				$find=4;break;
			case 1:
				$find=5;break;
			case 2:
				$find=7;break;
			case 3:
				$find=8;break;
			case 4:
				$find=7;break;
			case 5: 
				$find=8;break;
			case 6: 
				$find=10;break;
			case 7: 
				$find = 11;break;
			case 8: 
				$find=1;break;
			case 9: 
				$find = 2;break;
		}

		if($find !== false){
			for($i = 0;$i<4;$i++){
				if($dz[$i] == $find){
					$star[$i][$key] = $value;
				}
			}
		}
	}

	/**
	 * 福星:凡甲、丙两干见寅或子，乙、癸两干见卯或丑，戊干见申，己干见未，丁干见亥，庚干见午，辛干见巳，壬干见辰是也。年或日
	 * 
	 */
	public function fuXing($info,&$star,$key,$value){
		$tg = $info['tg'];
		$dz = $info['dz'];
		$find =[];
		if($tg[2] == 0 or $tg[0] == 0 or  $tg[2]==2 or $tg[0]==2){
			$find = [0,2];
		}
		if($tg[2] == 1 or $tg[0] == 1 or  $tg[2]==9 or $tg[0]==9 ){
			$find[] = 3;$find[] = 1;
		}
		if($tg[2] == 4 or $tg[0] == 4  ){
			$find[] =8;
		}
		if($tg[2] == 5 or $tg[0] == 5  ){
			$find[]=7;
		}
		if($tg[2] == 3 or $tg[0] == 3  ){
			$find[] = 11;
		}
		if($tg[2] == 6 or $tg[0] == 6 ){
			$find=[6];
		}
		if($tg[2] == 7 or $tg[0] == 7 ){
			$find=[5];
		}
		if($tg[2] == 8 or $tg[0] == 8 ){
			$find=[4];
		}
		if(count($find)){
			for($i = 0;$i<4;$i++){
				if(in_array($dz[$i],$find)){
					$star[$i][$key] = $value;
				}
			}
		}
	}

	/**
	 * 国印:甲见戌， 乙见亥， 丙见丑， 丁见寅，戊见丑， 己见寅， 庚见辰， 辛见巳，壬见未， 癸见申 以年干或日干为主, 地支见者为是
	 * 
	 */
	function guoYin($info,&$star,$key,$value){
		$tg = $info['tg'];
		$dz = $info['dz'];
		$find=[];
		switch($tg[0]){
			case 0 :
				$find[]=10;break;
			case 1 :
				$find[]=11;break;
			case 2 :
				$find[] = 1;break;
			case 3 :
				$find[] = 2;break;
			case 4 :
				$find[] = 1;break;
			case 5 :
				$find[] = 2;break;
			case 6 :
				$find[] = 4;break;
			case 7 :
				$find[] = 5;break;
			case 8 :
				$find[] = 7;break;
			case 9 :
				$find[] = 8;break;
		}
		switch($tg[2]){
			case 0 :
				$find[]=10;break;
			case 1 :
				$find[]=11;break;
			case 2 :
				$find[] = 1;break;
			case 3 :
				$find[] = 2;break;
			case 4 :
				$find[] = 1;break;
			case 5 :
				$find[] = 2;break;
			case 6 :
				$find[] = 4;break;
			case 7 :
				$find[] = 5;break;
			case 8 :
				$find[] = 7;break;
			case 9 :
				$find[] = 8;break;
		}
	
		for($i = 0;$i<4;$i++){
			if(in_array($dz[$i],$find)){
				$star[$i][$key] = $value;
			}
		}

	}

	/**
	 * 天厨,凡甲干见巳，乙干见午，丙干见子，丁干见已，戊干见午，己干见申，庚干见寅，辛干见午，壬干见酉，癸干见亥。
	 */
	function tianChu($info,&$star,$key,$value){
		$tg = $info['tg'];
		$dz = $info['dz'];
		$find=[];
		switch($tg[0]){
			case 0 :
				$find[]=5;break;
			case 1 :
				$find[]=6;break;
			case 2 :
				$find[] = 0;break;
			case 3 :
				$find[] = 5;break;
			case 4 :
				$find[] = 6;break;
			case 5 :
				$find[] = 8;break;
			case 6 :
				$find[] = 2;break;
			case 7 :
				$find[] = 6;break;
			case 8 :
				$find[] = 9;break;
			case 9 :
				$find[] = 11;break;
		}
		switch($tg[2]){
			case 0 :
				$find[]=5;break;
			case 1 :
				$find[]=6;break;
			case 2 :
				$find[] = 0;break;
			case 3 :
				$find[] = 5;break;
			case 4 :
				$find[] = 6;break;
			case 5 :
				$find[] = 8;break;
			case 6 :
				$find[] = 2;break;
			case 7 :
				$find[] = 6;break;
			case 8 :
				$find[] = 9;break;
			case 9 :
				$find[] = 11;break;
		}
		for($i = 0;$i<4;$i++){
			if(in_array($dz[$i],$find)){
				$star[$i][$key] = $value;
			}
		}

	}

	/**
	 * 学堂
	 *年纳音为木，月日时支见亥，；
	 年纳音为火，月日时支见寅；
	 年纳音为土，月日时支见申；
	 年纳音为金，月日时支见巳；
	 年纳音为水，月日时支见申
	 */
	function xueTang($info,&$star,$key,$value){
		$tg = $info['tg'];
		$dz = $info['dz'];
		$naYin = $info['na_yin'][0];
		switch($naYin[1]){
			case 0: 
				$find = 11;break;
			case 1:
				$find = 2;break;
			case 2:
				$find = 8;break;
			case 3:
				$find = 5;break;
			case 4:
				$find = 8;break;
		}
		for($i = 0;$i<4;$i++){
			if($i == 0 ){
				continue;
			}
			if($dz[$i]==$find){
				$star[$i][$key] = $value;
			}
		}
	}

	/**
	*    ['hongYang(','红艳'],
	*日天干是甲，地支见午；
	*日天干是乙，地支见申；
	*日天干是丙，地支见寅；
	*日天干是丁，地支见未；
	*日天干是戊，地支见辰；
	*日天干是己，地支见辰；
	*日天干是庚，地支见戌；
	*日天干是辛，地支见酉；
	*日天干是壬，地支见子；
	*日天干是癸，地支见申。
	*/
	function hongYang($info,&$star,$key,$value){
		$dz = $info['dz'];
		switch ($info['tg'][2]) {
			case 0:
				$find = 6;break;
			case 1:
				$find = 8;break;
			case 2:
				$find = 2;break;
			case 3:
				$find = 7;break;
			case 4:
				$find = 4;break;
			case 5:
				$find = 4;break;
			case 6:
				$find = 10;break;
			case 7:
				$find = 9;break;
			case 8:
				$find = 0;break;
			case 9:
				$find = 8;break;
		}
		for($i = 0;$i<4;$i++){
			if($dz[$i]==$find){
				$star[$i][$key] = $value;
			}
		}
	}

	/**
	 * 流霞
	 * 甲日酉、乙日戌、丙日未、丁日申、戊日巳；己日午、庚日辰、辛日卯、壬日亥、癸日寅
	 */
	public function liuXia($info,&$star,$key,$value){
		$dz = $info['dz'];
		switch ($info['tg'][2]) {
			case 0:
				$find = 9;break;
			case 1:
				$find = 10;break;
			case 2:
				$find = 7;break;
			case 3:
				$find = 8;break;
			case 4:
				$find = 5;break;
			case 5:
				$find = 6;break;
			case 6:
				$find = 4;break;
			case 7:
				$find = 3;break;
			case 8:
				$find = 11;break;
			case 9:
				$find = 2;break;
		}
		for($i = 0;$i<4;$i++){
			if($dz[$i]==$find){
				$star[$i][$key] = $value;
			}
		}
	}

	/**将星,三合局里的中间
	 * 寅午戌见午，申子辰见子，巳酉丑见酉，亥卯未见卯,只有3,6,9
	 */
	public function jiangXing($info,&$star,$key,$value){
		$dz = $info['dz'];
		$day = $info['dz'][2];
		$year = $info['dz'][0];
		$map = [
			0,9,6,3,0,9,6,3,0,9,6,3
		];
		for($i = 0;$i<4;$i++){
			if($i !== 2 ){
				if($dz[$i] == $map[$day]){
					$star[$i][$key] = $value;
				}
			}
			if($i !== 0){
				if($dz[$i] == $map[$year]){
					$star[$i][$key] = $value;
				}
			}
			
		}

	}

	/**
	 * 华盖 三合局里最后一个
	 * 寅午戌 申子辰 巳酉丑 亥卯未
	 */
	public function huaGai($info,&$star,$key,$value){
		$dz = $info['dz'];
		$day = $info['dz'][2];
		$year = $info['dz'][0];
		$map = [
			4,1,10,7,4,1,10,7,4,1,10,7
		];
		for($i = 0;$i<4;$i++){
			if($i !== 2 ){
				if($dz[$i] == $map[$day]){
					$star[$i][$key] = $value;
				}
			}
			if($i !== 0){
				if($dz[$i] == $map[$year]){
					$star[$i][$key] = $value;
				}
			}
			
		}
	}

	/**
	 * 驿马 申子辰马在寅，寅午戌马在申，巳酉丑马在亥，亥卯未马在巳。
	 */
	public function yiMa($info,&$star,$key,$value){
		$dz = $info['dz'];
		$day = $info['dz'][2];
		$year = $info['dz'][0];
		$map = [
			2,11,8,5,2,11,8,5,2,11,8,5
		];
		for($i = 0;$i<4;$i++){
			if($i !== 2 ){
				if($dz[$i] == $map[$day]){
					$star[$i][$key] = $value;
				}
			}
			if($i !== 0){
				if($dz[$i] == $map[$year]){
					$star[$i][$key] = $value;
				}
			}
			
		}
	}
	/**
	 * 劫 jieSha,克三合局的属性
	 * 以年支或日支为主，其他地支见者为是。申子辰见巳，亥卯未见申，寅午戌见亥，巳酉丑见寅。
	 */
	public function jieSha($info,&$star,$key,$value){
		$dz = $info['dz'];
		$day = $info['dz'][2];
		$year = $info['dz'][0];
		$map = [
			5,2,11,8,5,2,10,8,5,2,11,8
		];
		for($i = 0;$i<4;$i++){
			if($i !== 2 ){
				if($dz[$i] == $map[$day]){
					$star[$i][$key] = $value;
				}
			}
			if($i !== 0){
				if($dz[$i] == $map[$year]){
					$star[$i][$key] = $value;
				}
			}
			
		}
	}
	/**
	 * 亡神
	 * 申子辰以11，寅午戌以5，巳酉丑以8，亥卯未以2
	 * 
	 */
	function wangShen($info,&$star,$key,$value){
		$dz = $info['dz'];
		$day = $info['dz'][2];
		$year = $info['dz'][0];
		$map = [
			11,8,5,2,11,8,5,2,11,8,5,2
		];
		for($i = 0;$i<4;$i++){
			if($i !== 2 ){
				if($dz[$i] == $map[$day]){
					$star[$i][$key] = $value;
				}
			}
			if($i !== 0){
				if($dz[$i] == $map[$year]){
					$star[$i][$key] = $value;
				}
			}
			
		}
	}

	/**
     *  ['yuanChen','元辰(大耗)'],
	 * 地支六冲前后,根据性别和年的阴阳决定是前还是后
	 * 
	 */
	public function yuanChen($info,&$star,$key,$value){
		$dz = $info['dz'];
		$chong = $dz[0]+6;
		$shunxu = $dz[0]+$info['sex']%2;
		if($shunxu == 0){
			$target = $chong-1;
		}else{
			$target = $chong+1;
		}
		for($i = 0;$i<4;$i++){
			if($i !== 0){
				if($dz[$i] == $target){
					$star[$i][$key] = $value;
				}
			}
		}
	}
	/**
     * ['guChen','孤辰'],,
	 * 子年寅、丑年寅、寅年巳、卯年巳、辰年巳、巳年申、午年申、未年申、申年亥、酉年亥、戌年亥、亥年寅
	 * 
	 */
	public function guChen($info,&$star,$key,$value){
		$dz = $info['dz'];
		$map = [
			2,2,5,5,5,8,8,8,11,11,11,2
		];
		$target = $map[$dz[0]];
		for($i=0; $i<4; $i++){
			if($i==0){
				continue;
			}
			if($dz[$i] == $target){
				$star[$i][$key] = $value;
			}
		}
	}

	/**
	 * 寡宿
	 * 子年、丑年、亥年 见戌10 命带寡宿 年支为寅卯辰见丑1为寡。巳午未的见辰4为寡。申酉戌的在它支见未7为命带寡宿。
	 */
	public function guaSu($info,&$star,$key,$value){
		$dz = $info['dz'];
		$map = [
			10,10,1,1,1,4,4,4,7,7,7,10
		];
		$target = $map[$dz[0]];
		for($i=0; $i<4; $i++){
			if($i==0){
				continue;
			}
			if($dz[$i] == $target){
				$star[$i][$key] = $value;
			}
		}
	}

	/**
	 * 灾煞,将星相冲
	 * 申子辰见午6, 亥卯未见酉9, 寅午戌见子0, 巳酉丑见卯3,以年支为主, 四柱地支中见之者为是
	 */
	public function zhaiSha($info,&$star,$key,$value){
		$year = $info['dz'][0];
		$map = [6,3,0,9,6,3,0,9,6,3,0,9];
		for($i=0;$i<4;$i++){
			if($i = 0){
				continue;
			}
			if($dz[$i] == $map[$year]){
				$star[$i][$key] = $value;
			}
		}

	}

	/**
	 * ['liuE','六厄'],六厄为地支三合局之死地
	 * 申子辰水局死地在卯3,寅午戌三合火局，死地在酉9；亥卯未三合木局，死地在午6；巳酉丑三合金局死地在子0
	 */
	function liuE($info,&$start,$key,$value){
		$year = $info['dz'][0];
		$map = [3,0,9,6,3,0,9,6,3,0,9,6];
		for($i=0;$i<4;$i++){
			if($i = 0){
				continue;
			}
			if($dz[$i] == $map[$year]){
				$star[$i][$key] = $value;
			}
		}

	}

	/**
	 *  ['gouSha','勾煞'],勾煞和绞煞气一起
	 * 阳男阴女，命前三辰为勾，命后三辰为绞。
	 */

	function gouSha($info,&$start,$key,$value){
		$dz = $info['dz'];
		$hit = ($info['sex'] + $dz[0])%2;
		if($hit == 0){
			$target = ($dz[0]+12-3)%12;
			for($i=0;$i<4;$i++){
				if($i = 0){
					continue;
				}
				if($dz[$i] == $target){
					$star[$i][$key] = $value;
				}
			}
		}
	}
	/**
	 *  ['jiaoSha','绞煞'],勾煞和绞煞气一起
	 * 阳男阴女，命前三辰为勾，命后三辰为绞。
	 */

	function jiaoSha($info,&$start,$key,$value){
		$dz = $info['dz'];
		$hit = ($info['sex'] + $dz[0])%2;
		if($hit == 0){
			$target = ($dz[0]+3)%12;
			for($i=0;$i<4;$i++){
				if($i = 0){
					continue;
				}
				if($dz[$i] == $target){
					$star[$i][$key] = $value;
				}
			}
		}
	}

	/**
	 * 童子
	 * .命造生在春季或秋季的（以月令算），日支或时支见寅或子的；
	 * .命造生在冬季或夏季的（以月令算），日支或时支见卯未或辰的；
	 * .年柱纳音为金或木的，日支或时支见午或卯的。
	 * .年柱纳音为水或火的，日支或时支见酉或戌的。
	 * .年柱纳音为土命的，日支或时支见辰或巳的。
	 */
	function tongZi($info,&$start,$key,$value){
		$dz = $info['dz'];
		$naYin = $info['na_yin'][0][1];
		$target = [];
		if($dz[1] >1 and $dz[1]<=4 ){//春天
			$target =[0,2];
		}elseif($dz[1] >4 and $dz[1]<=7 ){
			$target = [3,7,4];
		}
		switch($naYin){
			case 0 : //木
			case 3 :
				$target[]=6;
				$target[]=3;
			break;
			case 1 :
			case 4 :
				$target[]=9;
				$target[]=10;
			break;
			case 3:
				$target[]=4;
				$target[]=5;
			break;
		}
		for($i=0;$i<4;$i++){
			if($i = 0 || $i=1){
				continue;
			}
			if(in_array($dz[$i],$target)){
				$star[$i][$key] = $value;
			}
		}

	}

}

class Stars{
    /**星煞的序号及名字
     * @var $star_name array
     */
    public $star_name = [
		['kongWang','空亡'],
    	['tianYi','天乙'],
        ['taiJi','太极'],
        ['tianYi2','天医'],
        ['tianDe','天德'],
        ['yueDe','月德'],
        ['luShen','禄神'],
        ['yangRen','羊刃'],
        ['guLuan','孤鸾'],
        ['sanQi','三奇'],
        ['tianXie','天赦'],
        ['xiuDe','德秀'],
        ['kuiGang','魁罡'],
        ['jingShen','金神'],
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

<?php
namespace app\index\service;
/**
 * @author joffe@pentamob.com
 *
 * 此日历转换类完全源于以下项目,感谢这两个项目作者的无私分享:
 * https://github.com/nozomi199/qimen_star (八字排盘,JS源码)
 * http://www.bieyu.com/ (详尽的历法转换原理,JS源码)
 * 
 */
class Paipan{
    /**
     * 是否区分 早晚子 时,true则23:00-24:00算成上一天
     * @var bool
     */
    public $zwz = false;
    /**
     * 十天干
     * @var array
     */
	public $ctg = array('甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸'); //char of TianGan
	/**
	 * 五行
	 */
	public $cwx = array( '木', '火', '土','金','水'); //char of WuXing
	/**
	 * 十二地支
	 * @var array
	 */
	public $cdz = array('子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥'); //char of DiZhi
	/**
	 * 地支对应五行
	 * @var array
	 */
	public $dzwx = array(4, 2, 0, 0, 2, 1, 1, 2, 3 , 3, 2, 4);
	/**
	 * 地支转天干,子为阳水，是阳中藏阴；丑为阴土；寅为阳木；卯为阴木；辰为阳土；巳为阴火，是阴中藏阳；午为阳火，是阳中藏阴；未为阴土；申为阳金；酉为阴金；戌为阳土；亥为阴水，是阴中藏阳。
	 * @var array;
	 */
	public $dztg = array(8,5,0,2,4,3,2,5,6,7,4,9);
	/**
	 * 地支藏干
	 * @var array
	 */
	public $dzcg = array([9], [5,9,7], [0,2,4], [1], [4,1,9], [2,4,6], [3,5], [5,3,1], [6,8,4], [7], [4,7,3], [8,0]);
	/**
	 * 十二生肖
	 * @var array
	 */
	public $csa = array('鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊', '猴', '鸡', '狗', '猪'); //char of symbolic animals
	/**
	 * 十二星座
	 * @var array
	 */
	public $cxz = array('水瓶座', '双鱼座', '白羊座', '金牛座', '双子座', '巨蟹座', '狮子座', '处女座', '天秤座', '天蝎座', '射手座', '摩羯座'); //char of XingZuo
	/**
	 * 星期
	 * @var array
	 */
	public $wkd = array('日', '一', '二', '三', '四', '五', '六'); //week day
	/**
	 * 廿四节气(从春分开始)
	 * @var array
	 */
	public $jq = array('春分', '清明', '谷雨', '立夏', '小满', '芒种', '夏至', '小暑', '大暑', '立秋', '处暑', '白露', '秋分', '寒露', '霜降', '立冬', '小雪', '大雪', '冬至', '小寒', '大寒', '立春', '雨水', '惊蛰'); //JieQi
	/**
	 * 均值朔望月長(mean length of synodic month)
	 * @var float
	 */
	private $synmonth = 29.530588853;
	/**
	 * 十大神杀,他们分别两两一组
	 * @var array
	 */
	public $ten_god = [['比', '劫'], ['食', '伤'], ['财', '才'],[ '杀', '官'], ['枭', '印']];
	/**
	 * 十二长生
	 * @var array
	 */
	public $cs = ["生", "沐", "冠", "临", "旺", "衰", "病", "死", "墓", "绝","胎", "养"];
	/**
	 * 本气
	 */
	public $selfQi = ['禄','刃','长生','墓库','余气','死','绝'];
	/**
	 * 长生位置,天干对应的地支索引
	 * @var array
	 */
	public $cs_tg2dz = [11,6,2,9,2,9,5,0,8,3];
	

	/**
	 * 因子
	 * @var array
	 */
	private $ptsa = array(485, 203, 199, 182, 156, 136, 77, 74, 70, 58, 52, 50, 45, 44, 29, 18, 17, 16, 14, 12, 12, 12, 9, 8);
	private $ptsb = array(324.96, 337.23, 342.08, 27.85, 73.14, 171.52, 222.54, 296.72, 243.58, 119.81, 297.17, 21.02, 247.54, 325.15, 60.93, 155.12, 288.79, 198.04, 199.76, 95.39, 287.11, 320.81, 227.73, 15.45);
	private $ptsc = array(1934.136, 32964.467, 20.186, 445267.112, 45036.886, 22518.443, 65928.934, 3034.906, 9037.513, 33718.147, 150.678, 2281.226, 29929.562, 31555.956, 4443.417, 67555.328, 4562.452, 62894.029, 31436.921, 14577.848, 31931.756, 34777.259, 1222.114, 16859.074);
	/**
	 * 计算指定年(公历)的春分点(vernal equinox),但因地球在繞日运行時會因受到其他星球之影響而產生攝動(perturbation),必須將此現象產生的偏移量加入.
	 * @param int $yy
	 * @return boolean|number 返回儒略日历格林威治时间
	 */
	private function VE($yy) {
	    if($yy < -8000){
	        return false;
	    }
	    if($yy > 8001){
	        return false;
	    }
	    if ($yy >= 1000 && $yy <= 8001) {
	        $m = ($yy - 2000) / 1000;
	        return 2451623.80984 + 365242.37404 * $m + 0.05169 * $m * $m - 0.00411 * $m * $m * $m - 0.00057 * $m * $m * $m * $m;
	    }
	    if ($yy >= -8000 && $yy < 1000) {
	        $m = $yy / 1000;
	        return 1721139.29189 + 365242.1374 * $m + 0.06134 * $m * $m + 0.00111 * $m * $m * $m - 0.00071 * $m * $m * $m * $m;
	    }
	}
	/**
	 * 地球在繞日运行時會因受到其他星球之影響而產生攝動(perturbation)
	 * @param float $jd 
	 * @return number 返回某时刻(儒略日历)的攝動偏移量
	 */
	private function Perturbation($jd) {
	    $t = ($jd - 2451545) / 36525;
	    $s = 0;
	    for ($k = 0; $k <= 23; $k++) {
	        $s = $s + $this->ptsa[$k] * cos($this->ptsb[$k] * 2 * pi() / 360 + $this->ptsc[$k] * 2 * pi() / 360 * $t);
	    }
	    $w = 35999.373 * $t - 2.47;
	    $l = 1 + 0.0334 * cos($w * 2 * pi() / 360) + 0.0007 * cos(2 * $w * 2 * pi() / 360);
	    return 0.00001 * $s / $l;
	}
    /**
     * 求∆t
     * @param int $yy 年份
     * @param int $mm 月份
     * @return number
     */
	private function DeltaT($yy, $mm) {

		$y = $yy + ($mm - 0.5) / 12;

		if ($y <= -500) {
			$u = ($y - 1820) / 100;
			$dt = ( - 20 + 32 * $u * $u);
		} else {
			if ($y < 500) {
				$u = $y / 100;
				$dt = (10583.6 - 1014.41 * $u + 33.78311 * $u * $u - 5.952053 * $u * $u * $u - 0.1798452 * $u * $u * $u * $u + 0.022174192 * $u * $u * $u * $u * $u + 0.0090316521 * $u * $u * $u * $u * $u * $u);
			} else {
				if ($y < 1600) {
					$u = ($y - 1000) / 100;
					$dt = (1574.2 - 556.01 * $u + 71.23472 * $u * $u + 0.319781 * $u * $u * $u - 0.8503463 * $u * $u * $u * $u - 0.005050998 * $u * $u * $u * $u * $u + 0.0083572073 * $u * $u * $u * $u * $u * $u);
				} else {
					if ($y < 1700) {
						$t = $y - 1600;
						$dt = (120 - 0.9808 * $t - 0.01532 * $t * $t + $t * $t * $t / 7129);
					} else {
						if ($y < 1800) {
							$t = $y - 1700;
							$dt = (8.83 + 0.1603 * $t - 0.0059285 * $t * $t + 0.00013336 * $t * $t * $t - $t * $t * $t * $t / 1174000);
						} else {
							if ($y < 1860) {
								$t = $y - 1800;
								$dt = (13.72 - 0.332447 * $t + 0.0068612 * $t * $t + 0.0041116 * $t * $t * $t - 0.00037436 * $t * $t * $t * $t + 0.0000121272 * $t * $t * $t * $t * $t - 0.0000001699 * $t * $t * $t * $t * $t * $t + 0.000000000875 * $t * $t * $t * $t * $t * $t * $t);
							} else {
								if ($y < 1900) {
									$t = $y - 1860;
									$dt = (7.62 + 0.5737 * $t - 0.251754 * $t * $t + 0.01680668 * $t * $t * $t - 0.0004473624 * $t * $t * $t * $t + $t * $t * $t * $t * $t / 233174);
								} else {
									if ($y < 1920) {
										$t = $y - 1900;
										$dt = ( - 2.79 + 1.494119 * $t - 0.0598939 * $t * $t + 0.0061966 * $t * $t * $t - 0.000197 * $t * $t * $t * $t);
									} else {
										if ($y < 1941) {
											$t = $y - 1920;
											$dt = (21.2 + 0.84493 * $t - 0.0761 * $t * $t + 0.0020936 * $t * $t * $t);
										} else {
											if ($y < 1961) {
												$t = $y - 1950;
												$dt = (29.07 + 0.407 * $t - $t * $t / 233 + $t * $t * $t / 2547);
											} else {
												if ($y < 1986) {
													$t = $y - 1975;
													$dt = (45.45 + 1.067 * $t - $t * $t / 260 - $t * $t * $t / 718);
												} else {
													if ($y < 2005) {
														$t = $y - 2000;
														$dt = (63.86 + 0.3345 * $t - 0.060374 * $t * $t + 0.0017275 * $t * $t * $t + 0.000651814 * $t * $t * $t * $t + 0.00002373599 * $t * $t * $t * $t * $t);
													} else {
														if ($y < 2050) {
															$t = $y - 2000;
															$dt = (62.92 + 0.32217 * $t + 0.005589 * $t * $t);
														} else {
															if ($y < 2150) {
																$u = ($y - 1820) / 100;
																$dt = ( - 20 + 32 * $u * $u - 0.5628 * (2150 - $y));
															} else {
																$u = ($y - 1820) / 100;
																$dt = ( - 20 + 32 * $u * $u);
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		if ($y < 1955 || $y >= 2005){
		    $dt = $dt - (0.000012932 * ($y - 1955) * ($y - 1955));
		}
		return $dt / 60; //將秒轉換為分
	}
	/**
	 * 获取指定年的春分开始的24节气,另外多取2个确保覆盖完一个公历年
	 * 大致原理是:先用此方法得到理论值,再用摄动值(Perturbation)和固定参数DeltaT做调整
	 * @param int $yy
	 * @return boolean
	 */
	private function MeanJQJD($yy) {
	    if(! $jd = $this->VE($yy)){ //该年的春分點
	        return array();
	    }
	    $ty = $this->VE($yy + 1) - $jd; //该年的回歸年長
	    
	    $num = 24 + 2; //另外多取2个确保覆盖完一个公历年
	    
	    $ath = 2 * pi() / 24;
	    $tx = ($jd - 2451545) / 365250;
	    $e = 0.0167086342 - 0.0004203654 * $tx - 0.0000126734 * $tx * $tx + 0.0000001444 * $tx * $tx * $tx - 0.0000000002 * $tx * $tx * $tx * $tx + 0.0000000003 * $tx * $tx * $tx * $tx * $tx;
	    $tt = $yy / 1000;
	    $vp = 111.25586939 - 17.0119934518333 * $tt - 0.044091890166673 * $tt * $tt - 4.37356166661345E-04 * $tt * $tt * $tt + 8.16716666602386E-06 * $tt * $tt * $tt * $tt;
	    $rvp = $vp * 2 * pi() / 360;
	    $peri = array();
	    for ($i = 0; $i < $num; $i++) {
	        $flag = 0;
	        $th = $ath * $i + $rvp;
	        if ($th > pi() && $th <= 3 * pi()) {
	            $th = 2 * pi() - $th;
	            $flag = 1;
	        }
	        if ($th > 3 * pi()) {
	            $th = 4 * pi() - $th;
	            $flag = 2;
	        }
	        $f1 = 2 * atan((sqrt((1 - $e) / (1 + $e)) * tan($th / 2)));
	        $f2 = ($e * sqrt(1 - $e * $e) * sin($th)) / (1 + $e * cos($th));
	        $f = ($f1 - $f2) * $ty / 2 / pi();
	        if ($flag == 1){
	            $f = $ty - $f;
	        }
	        if ($flag == 2){
	            $f = 2 * $ty - $f;
	        }
	        $peri[$i] = $f;
	    }
	    $jqjd = array();
	    for ($i = 0; $i < $num; $i++) {
	        $jqjd[$i] = $jd + $peri[$i] - $peri[0];
	    }
	    
	    return (array)$jqjd;
	}
	/**
	 * 获取指定年的春分开始作Perturbaton調整後的24节气,可以多取2个
	 * @param int $yy
	 * @param int $start 0-25
	 * @param int $end 0-25
	 * @return array
	 */
	private function GetAdjustedJQ($yy, $start, $end) {
	    if($start<0 || $start>25){
	        return array();
	    }
	    if($end<0 || $end>25){
	        return array();
	    }
	    
	    $jq = array();
	    
	    $jqjd = $this->MeanJQJD($yy); //获取该年春分开始的24节气时间点
	    foreach ($jqjd as $k => $jd){
	        if($k < $start){
	            continue;
	        }
	        if($k > $end){
	            continue;
	        }
	        $ptb = $this->Perturbation($jd); //取得受perturbation影響所需微調
	        $dt = $this->DeltaT($yy, floor(($k+1) / 2) + 3); //修正dynamical time to Universal time
	        $jq[$k] = $jd + $ptb - $dt / 60 / 24; //加上攝動調整值ptb,減去對應的Delta T值(分鐘轉換為日)
	        $jq[$k] = $jq[$k] + 1 / 3; //因中國時間比格林威治時間先行8小時,即1/3日
	    }
	    return (array)$jq;
	}
	/**
	 * 求出以某年立春點開始的節(注意:为了方便计算起运数,此处第0位为上一年的小寒)
	 * @param int $yy
	 * @return array jq[(2*$k+21)%24]
	 */
	private function GetPureJQsinceSpring($yy) {
	    $jdpjq = array();
	    
	    $dj = $this->GetAdjustedJQ($yy - 1, 19, 23); //求出含指定年立春開始之3個節氣JD值,以前一年的年值代入
	    foreach ($dj as $k => $v){
	        if($k < 19){
	            continue;
	        }
	        if($k > 23){
	            continue;
	        }
	        if($k % 2 == 0){
	            continue;
	        }
	        $jdpjq[] = $dj[$k]; //19小寒;20大寒;21立春;22雨水;23惊蛰
	    }
	    
	    $dj = $this->GetAdjustedJQ($yy, 0, 25); //求出指定年節氣之JD值,從春分開始,到大寒,多取两个确保覆盖一个公历年,也方便计算起运数
	    foreach ($dj as $k => $v){
	        if($k % 2 == 0){
	            continue;
	        }
	        $jdpjq[] = $dj[$k];
	    }
	    
	    return (array)$jdpjq;
	}
	/**
	 * 求出自冬至點為起點的連續15個中氣
	 * @param int $yy
	 * @return array jq[(2*$k+18)%24]
	 */
	private function GetZQsinceWinterSolstice($yy) {
	    $jdzq = array();
	    
	    $dj = $this->GetAdjustedJQ($yy - 1, 18, 23); //求出指定年冬至開始之節氣JD值,以前一年的值代入
	    $jdzq[0] = $dj[18]; //冬至
	    $jdzq[1] = $dj[20]; //大寒
	    $jdzq[2] = $dj[22]; //雨水
	    
	    $dj = $this->GetAdjustedJQ($yy, 0, 23); //求出指定年節氣之JD值
	    foreach ($dj as $k => $v){
	        if($k%2 != 0){
	            continue;
	        }
	        $jdzq[] = $dj[$k];
	    }
	    
	    return (array)$jdzq;
	}
	/**
	 * 求出實際新月點
	 * 以2000年初的第一個均值新月點為0點求出的均值新月點和其朔望月之序數 k 代入此副程式來求算實際新月點
	 * @param unknown $k
	 * @return number
	 */
	private function TrueNewMoon($k) {
		$jdt = 2451550.09765 + $k * $this->synmonth;
		$t = ($jdt - 2451545) / 36525; //2451545為2000年1月1日正午12時的JD
		$t2 = $t * $t; //square for frequent use
		$t3 = $t2 * $t; //cube for frequent use
		$t4 = $t3 * $t; //to the fourth
		//mean time of phase
		$pt = $jdt + 0.0001337 * $t2 - 0.00000015 * $t3 + 0.00000000073 * $t4;
		//Sun's mean anomaly(地球繞太阳运行均值近點角)(從太阳觀察)
		$m = 2.5534 + 29.10535669 * $k - 0.0000218 * $t2 - 0.00000011 * $t3;
		//Moon's mean anomaly(月球繞地球运行均值近點角)(從地球觀察)
		$mprime = 201.5643 + 385.81693528 * $k + 0.0107438 * $t2 + 0.00001239 * $t3 - 0.000000058 * $t4;
		//Moon's argument of latitude(月球的緯度參數)
		$f = 160.7108 + 390.67050274 * $k - 0.0016341 * $t2 - 0.00000227 * $t3 + 0.000000011 * $t4;
		//Longitude of the ascending node of the lunar orbit(月球繞日运行軌道升交點之經度)
		$omega = 124.7746 - 1.5637558 * $k + 0.0020691 * $t2 + 0.00000215 * $t3;
		//乘式因子
		$es = 1 - 0.002516 * $t - 0.0000074 * $t2;
		//因perturbation造成的偏移：
		$apt1 = -0.4072 * sin((pi() / 180) * $mprime);
		$apt1 += 0.17241 * $es * sin((pi() / 180) * $m);
		$apt1 += 0.01608 * sin((pi() / 180) * 2 * $mprime);
		$apt1 += 0.01039 * sin((pi() / 180) * 2 * $f);
		$apt1 += 0.00739 * $es * sin((pi() / 180) * ($mprime - $m));
		$apt1 -= 0.00514 * $es * sin((pi() / 180) * ($mprime + $m));
		$apt1 += 0.00208 * $es * $es * sin((pi() / 180) * (2 * $m));
		$apt1 -= 0.00111 * sin((pi() / 180) * ($mprime - 2 * $f));
		$apt1 -= 0.00057 * sin((pi() / 180) * ($mprime + 2 * $f));
		$apt1 += 0.00056 * $es * sin((pi() / 180) * (2 * $mprime + $m));
		$apt1 -= 0.00042 * sin((pi() / 180) * 3 * $mprime);
		$apt1 += 0.00042 * $es * sin((pi() / 180) * ($m + 2 * $f));
		$apt1 += 0.00038 * $es * sin((pi() / 180) * ($m - 2 * $f));
		$apt1 -= 0.00024 * $es * sin((pi() / 180) * (2 * $mprime - $m));
		$apt1 -= 0.00017 * sin((pi() / 180) * $omega);
		$apt1 -= 0.00007 * sin((pi() / 180) * ($mprime + 2 * $m));
		$apt1 += 0.00004 * sin((pi() / 180) * (2 * $mprime - 2 * $f));
		$apt1 += 0.00004 * sin((pi() / 180) * (3 * $m));
		$apt1 += 0.00003 * sin((pi() / 180) * ($mprime + $m - 2 * $f));
		$apt1 += 0.00003 * sin((pi() / 180) * (2 * $mprime + 2 * $f));
		$apt1 -= 0.00003 * sin((pi() / 180) * ($mprime + $m + 2 * $f));
		$apt1 += 0.00003 * sin((pi() / 180) * ($mprime - $m + 2 * $f));
		$apt1 -= 0.00002 * sin((pi() / 180) * ($mprime - $m - 2 * $f));
		$apt1 -= 0.00002 * sin((pi() / 180) * (3 * $mprime + $m));
		$apt1 += 0.00002 * sin((pi() / 180) * (4 * $mprime));

		$apt2 = 0.000325 * sin((pi() / 180) * (299.77 + 0.107408 * $k - 0.009173 * $t2));
		$apt2 += 0.000165 * sin((pi() / 180) * (251.88 + 0.016321 * $k));
		$apt2 += 0.000164 * sin((pi() / 180) * (251.83 + 26.651886 * $k));
		$apt2 += 0.000126 * sin((pi() / 180) * (349.42 + 36.412478 * $k));
		$apt2 += 0.00011 * sin((pi() / 180) * (84.66 + 18.206239 * $k));
		$apt2 += 0.000062 * sin((pi() / 180) * (141.74 + 53.303771 * $k));
		$apt2 += 0.00006 * sin((pi() / 180) * (207.14 + 2.453732 * $k));
		$apt2 += 0.000056 * sin((pi() / 180) * (154.84 + 7.30686 * $k));
		$apt2 += 0.000047 * sin((pi() / 180) * (34.52 + 27.261239 * $k));
		$apt2 += 0.000042 * sin((pi() / 180) * (207.19 + 0.121824 * $k));
		$apt2 += 0.00004 * sin((pi() / 180) * (291.34 + 1.844379 * $k));
		$apt2 += 0.000037 * sin((pi() / 180) * (161.72 + 24.198154 * $k));
		$apt2 += 0.000035 * sin((pi() / 180) * (239.56 + 25.513099 * $k));
		$apt2 += 0.000023 * sin((pi() / 180) * (331.55 + 3.592518 * $k));
		return $pt + $apt1 + $apt2;
	}
	/**
	 * 對於指定日期時刻所屬的朔望月,求出其均值新月點的月序數
	 * @param float $jd
	 * @return int
	 */
	private function MeanNewMoon($jd) {
	    //$kn為從2000年1月6日14時20分36秒起至指定年月日之阴曆月數,以synodic month為單位
	    $kn = floor(($jd - 2451550.09765) / $this->synmonth); //2451550.09765為2000年1月6日14時20分36秒之JD值.
	    $jdt = 2451550.09765 + $kn * $this->synmonth;
	    //Time in Julian centuries from 2000 January 0.5.
	    $t = ($jdt - 2451545) / 36525; //以100年為單位,以2000年1月1日12時為0點
	    $thejd = $jdt + 0.0001337 * $t * $t - 0.00000015 * $t * $t * $t + 0.00000000073 * $t * $t * $t * $t;
	    //2451550.09765為2000年1月6日14時20分36秒,此為2000年後的第一個均值新月
	    return array($kn, $thejd);
	}
	/**
	 * 将儒略日历时间转换为公历(格里高利历)时间
	 * @param float $jd
	 * @return array(年,月,日,时,分,秒)
	 */
	private function Julian2Solar($jd) {
	    $jd = (float)$jd;
	    
	    if ($jd >= 2299160.5) { //1582年10月15日,此日起是儒略日历,之前是儒略历
	        $y4h = 146097;
	        $init = 1721119.5;
	    } else {
	        $y4h = 146100;
	        $init = 1721117.5;
	    }
	    $jdr = floor($jd - $init);
	    $yh = $y4h / 4;
	    $cen = floor(($jdr + 0.75) / $yh);
	    $d = floor($jdr + 0.75 - $cen * $yh);
	    $ywl = 1461 / 4;
	    $jy = floor(($d + 0.75) / $ywl);
	    $d = floor($d + 0.75 - $ywl * $jy + 1);
	    $ml = 153 / 5;
	    $mp = floor(($d - 0.5) / $ml);
	    $d = floor(($d - 0.5) - 30.6 * $mp + 1);
	    $y = (100 * $cen) + $jy;
	    $m = ($mp + 2) % 12 + 1;
	    if ($m < 3){
	        $y = $y + 1;
	    }
	    $sd = floor(($jd + 0.5 - floor($jd + 0.5)) * 24 * 60 * 60 + 0.00005);
	    $mt = floor($sd / 60);
	    $ss = $sd % 60;
	    $hh = floor($mt / 60);
	    $mt = $mt % 60;
	    $yy = floor($y);
	    $mm = floor($m);
	    $dd = floor($d);
	    
	    return array($yy, $mm, $dd, $hh, $mt, $ss);
	}
	/**
	 * 以比較日期法求算冬月及其餘各月名稱代碼,包含閏月,冬月為0,臘月為1,正月為2,餘類推.閏月多加0.5
	 * @param int $yy
	 */
	private function GetZQandSMandLunarMonthCode($yy) {
	    $mc = array();
	    
		$jdzq = $this->GetZQsinceWinterSolstice($yy); //取得以前一年冬至為起點之連續15個中氣
		$jdnm = $this->GetSMsinceWinterSolstice($yy, $jdzq[0]); //求出以含冬至中氣為阴曆11月(冬月)開始的連續16個朔望月的新月點
		$yz = 0; //設定旗標,0表示未遇到閏月,1表示已遇到閏月
		if (floor($jdzq[12] + 0.5) >= floor($jdnm[13] + 0.5)) { //若第13個中氣jdzq(12)大於或等於第14個新月jdnm(13)
			for ($i = 1; $i <= 14; $i++) { //表示此兩個冬至之間的11個中氣要放到12個朔望月中,
				//至少有一個朔望月不含中氣,第一個不含中氣的月即為閏月
				//若阴曆臘月起始日大於冬至中氣日,且阴曆正月起始日小於或等於大寒中氣日,則此月為閏月,其餘同理
				if (floor(($jdnm[$i] + 0.5) > floor($jdzq[$i - 1 - $yz] + 0.5) && floor($jdnm[$i + 1] + 0.5) <= floor($jdzq[$i - $yz] + 0.5))) {
					$mc[$i] = $i - 0.5;
					$yz = 1; //標示遇到閏月
				} else {
					$mc[$i] = $i - $yz; //遇到閏月開始,每個月號要減1
				}
			}
		} else { //否則表示兩個連續冬至之間只有11個整月,故無閏月
			for ($i = 0; $i <= 12; $i++) { //直接賦予這12個月月代碼
				$mc[$i] = $i;
			}
			for ($i = 13; $i <= 14; $i++) { //處理次一置月年的11月與12月,亦有可能含閏月
				//若次一阴曆臘月起始日大於附近的冬至中氣日,且阴曆正月起始日小於或等於大寒中氣日,則此月為閏月,次一正月同理.
				if (floor(($jdnm[$i] + 0.5) > floor($jdzq[$i - 1 - $yz] + 0.5) && floor($jdnm[$i + 1] + 0.5) <= floor($jdzq[$i - $yz] + 0.5))) {
					$mc[$i] = $i - 0.5;
					$yz = 1; //標示遇到閏月
				} else {
					$mc[$i] = $i - $yz; //遇到閏月開始,每個月號要減1
				}
			}
		}
		return array($jdzq, $jdnm, $mc);
	}
	/**
	 * 求算以含冬至中氣為阴曆11月開始的連續16個朔望月
	 * @param int $yy 年份
	 * @param float $jdws 冬至的儒略日历时间
	 * @return array
	 */
	private function GetSMsinceWinterSolstice($yy, $jdws) {
		$tjd = array();
		$jd = $this->Solar2Julian($yy - 1, 11, 1, 0, 0, 0); //求年初前兩個月附近的新月點(即前一年的11月初)
		list($kn, $thejd) = $this->MeanNewMoon($jd); //求得自2000年1月起第kn個平均朔望日及其JD值
		for ($i = 0; $i <= 19; $i++) { //求出連續20個朔望月
			$k = $kn + $i;
			$mjd = $thejd + $this->synmonth * $i;
			$tjd[$i] = $this->TrueNewMoon($k) + 1 / 3; //以k值代入求瞬時朔望日,因中國比格林威治先行8小時,加1/3天
			//下式為修正dynamical time to Universal time
			$tjd[$i] = $tjd[$i] - $this->DeltaT($yy, $i - 1) / 1440; //1為1月,0為前一年12月,-1為前一年11月(當i=0時,i-1=-1,代表前一年11月)
		}
		for ($j = 0; $j <= 18; $j++) {
			if (floor($tjd[$j] + 0.5) > floor($jdws + 0.5)) {
				break;
			} //已超過冬至中氣(比較日期法)
		}
		
		$jdnm = array();
		for ($k = 0; $k <= 15; $k++) { //取上一步的索引值
		    $jdnm[$k] = $tjd[$j - 1 + $k]; //重排索引,使含冬至朔望月的索引為0
		}
		return (array)$jdnm;
	}
	/**
	 * 將公历时间转换为儒略日历时间
	 * @param int $yy
	 * @param int $mm
	 * @param int $dd
	 * @param int $hh [0-23]
	 * @param int $mt [0-59]
	 * @param int $ss [0-59]
	 * @return boolean|number
	 */
	private function Solar2Julian($yy, $mm, $dd, $hh=0, $mt=0, $ss=0) {
	    if(! $this->ValidDate($yy, $mm, $dd)){
	        return false;
	    }
	    if($hh < 0 || $hh >= 24){
	        return false;
	    }
	    if($mt < 0 || $mt >= 60){
	        return false;
	    }
	    if($ss < 0 || $ss >= 60){
	        return false;
	    }
	    
	    $yp = $yy + floor(($mm - 3) / 10);
	    if (($yy > 1582) || ($yy == 1582 && $mm > 10) || ($yy == 1582 && $mm == 10 && $dd >= 15)) { //这一年有十天是不存在的
	        $init = 1721119.5;
	        $jdy = floor($yp * 365.25) - floor($yp / 100) + floor($yp / 400);
	    }
	    if (($yy < 1582) || ($yy == 1582 && $mm < 10) || ($yy == 1582 && $mm == 10 && $dd <= 4)) {
	        $init = 1721117.5;
	        $jdy = floor($yp * 365.25);
	    }
	    if(! $init){
	        return false;
	    }
	    $mp = floor($mm + 9) % 12;
	    $jdm = $mp * 30 + floor(($mp + 1) * 34 / 57);
	    $jdd = $dd - 1;
	    $jdh = ($hh + ($mt + ($ss / 60))/60) / 24;
	    return $jdy + $jdm + $jdd + $jdh + $init;
	}
	/**
	 * 判断公历日期是否有效
	 * @param int $yy
	 * @param int $mm
	 * @param int $dd
	 * @return boolean
	 */
	public function ValidDate($yy, $mm, $dd) {
	    if ($yy < -1000 || $yy > 3000) { //适用于西元-1000年至西元3000年,超出此范围误差较大
	        return false;
	    }
	    
	    if ($mm < 1 || $mm > 12) { //月份超出範圍
	        return false;
	    }
	    
	    if ($yy == 1582 && $mm == 10 && $dd >= 5 && $dd < 15) { //这段日期不存在.所以1582年10月只有20天
	        return false;
	    }
	    
	    $ndf1 = -($yy % 4 == 0); //可被四整除
	    $ndf2 = (($yy % 400 == 0) - ($yy % 100 == 0)) && ($yy > 1582);
	    $ndf = $ndf1 + $ndf2;
	    $dom = 30 + ((abs($mm - 7.5) + 0.5) % 2) - intval($mm == 2) * (2 + $ndf);
	    if ($dd <= 0 || $dd > $dom) {
	        if ($ndf == 0 && $mm == 2 && $dd == 29) { //此年無閏月
	            
	        } else { //日期超出範圍
	            
	        }
	        return false;
	    }
	    
	    return true;
	}
	/**
	 * 获取公历某个月有多少天
	 * @param int $yy
	 * @param int $mm
	 * @return number
	 */
	public function GetSolarDays($yy, $mm){
	    if ($yy < -1000 || $yy > 3000) { //适用于西元-1000年至西元3000年,超出此范围误差较大
	        return 0;
	    }
	    
	    if ($mm < 1 || $mm > 12) { //月份超出範圍
	        return 0;
	    }
	    $ndf1 = -($yy % 4 == 0); //可被四整除
	    $ndf2 = (($yy % 400 == 0) - ($yy % 100 == 0)) && ($yy > 1582);
	    $ndf = $ndf1 + $ndf2;
	    return 30 + ((abs($mm - 7.5) + 0.5) % 2) - intval($mm == 2) * (2 + $ndf);
	}
	/**
	 * 获取农历某个月有多少天
	 * @param int $yy
	 * @param int $mm
	 * @param bool $isLeap
	 * @return number
	 */
	public function GetLunarDays($yy, $mm, $isLeap){
	    if ($yy < -1000 || $yy > 3000) { //适用于西元-1000年至西元3000年,超出此范围误差较大
	        return 0;
	    }
	    if ($mm < 1 || $mm > 12){ //輸入月份必須在1-12月之內
	        return 0;
	    }
	    list($jdzq, $jdnm, $mc) = $this->GetZQandSMandLunarMonthCode($yy);
	    
	    $leap = 0; //若閏月旗標為0代表無閏月
	    for ($j = 1; $j <= 14; $j++) { //確認指定年前一年11月開始各月是否閏月
	        if ($mc[$j] - floor($mc[$j]) > 0) { //若是,則將此閏月代碼放入閏月旗標內
	            $leap = floor($mc[$j] + 0.5); //leap=0對應阴曆11月,1對應阴曆12月,2對應阴曆隔年1月,依此類推.
	            break;
	        }
	    }
	    
	    $mm = $mm + 2; //11月對應到1,12月對應到2,1月對應到3,2月對應到4,依此類推
	    
	    for ($i = 0; $i <= 14; $i++) { //求算阴曆各月之大小,大月30天,小月29天
	        $nofd[$i] = floor($jdnm[$i + 1] + 0.5) - floor($jdnm[$i] + 0.5); //每月天數,加0.5是因JD以正午起算
	    }
	    
	    $dy = 0; //当月天数
	    $er = 0; //若輸入值有錯誤,er值將被設定為非0
	    
	    if ($isLeap){ //若是閏月
	        if ($leap < 3) { //而旗標非閏月或非本年閏月,則表示此年不含閏月.leap=0代表無閏月,=1代表閏月為前一年的11月,=2代表閏月為前一年的12月
	            $er = 1; //此年非閏年
	        } else { //若本年內有閏月
	            if ($leap != $mm) { //但不為輸入的月份
	                $er = 2; //則此輸入的月份非閏月,此月非閏月
	            } else { //若輸入的月份即為閏月
	                $dy = $nofd[$mm];
	            }
	        }
	    } else { //若沒有勾選閏月則
	        if ($leap == 0) { //若旗標非閏月,則表示此年不含閏月(包括前一年的11月起之月份)
	            $dy = $nofd[$mm - 1];
	        } else { //若旗標為本年有閏月(包括前一年的11月起之月份) 公式nofd(mx - (mx > leap) - 1)的用意為:若指定月大於閏月,則索引用mx,否則索引用mx-1
	            $dy = $nofd[$mm + ($mm > $leap) - 1];
	        }
	    }
	    return (int)$dy;
	}
	/**
	 * 获取农历某年的闰月,0为无闰月
	 * @param int $yy
	 * @return number
	 */
	public function GetLeap($yy){
	    list($jdzq, $jdnm, $mc) = $this->GetZQandSMandLunarMonthCode($yy);
	    
	    $leap = 0; //若閏月旗標為0代表無閏月
	    for ($j = 1; $j <= 14; $j++) { //確認指定年前一年11月開始各月是否閏月
	        if ($mc[$j] - floor($mc[$j]) > 0) { //若是,則將此閏月代碼放入閏月旗標內
	            $leap = floor($mc[$j] + 0.5); //leap=0對應阴曆11月,1對應阴曆12月,2對應阴曆隔年1月,依此類推.
	            break;
	        }
	    }
	    return max(0, $leap-2);
	}
	/**
	 * 根据公历月日计算星座下标
	 * @param int $mm
	 * @param int $dd
	 * @return int|false
	 */
	public function GetZodiac($mm, $dd) {
	    if($mm < 1 || $mm > 12){
	        return false;
	    }
	    if($dd < 1 || $dd > 31){
	        return false;
	    }

	    $dds = array(20,19,21,20,21,22,23,23,23,24,22,22); //星座的起始日期
	    
	    $kn = $mm - 1; //下标从0开始
	    
	    if ($dd < $dds[$kn]){ //如果早于该星座起始日期,则往前一个
	        $kn = (($kn + 12) - 1) % 12; //确保是正数
	    }
	    return (int)$kn;
	}
	/**
	 * 计算公历的某天是星期几(PHP中的date方法,此处演示儒略日历的转换作用)
	 * @param int $yy
	 * @param int $mm
	 * @param int $dd
	 */
	public function GetWeek($yy, $mm, $dd){
	    if(! $jd = $this->Solar2Julian($yy, $mm, $dd, 12)){ //当天12点计算(因为儒略日历是中午12点为起始点)
	        return false;
	    }

	    return (((floor($jd+1) % 7)) + 7) % 7; //模數(或餘數)為0代表星期日(因为西元前4713年1月1日12時为星期一).jd加1是因起始日為星期一
	}
	/**
	 * 将农历时间转换成公历时间
	 * @param int $yy
	 * @param int $mm
	 * @param int $dd
	 * @param int $isLeap 是否闰月
	 * @return false/array(年,月,日)
	 */
	public function Lunar2Solar($yy, $mm, $dd, $isLeap) {
	    if ($yy < -7000 || $yy > 7000) { //超出計算能力
	        return false;
	    }
	    if ($yy < -1000 || $yy > 3000) { //适用于西元-1000年至西元3000年,超出此范围误差较大
	        return false;
	    }
	    if ($mm < 1 || $mm > 12){ //輸入月份必須在1-12月之內
	        return false;
	    }
	    if ($dd < 1 || $dd > 30) { //輸入日期必須在1-30日之內
	        return false;
	    }
	    
	    list($jdzq, $jdnm, $mc) = $this->GetZQandSMandLunarMonthCode($yy);

	    $leap = 0; //若閏月旗標為0代表無閏月
	    for ($j = 1; $j <= 14; $j++) { //確認指定年前一年11月開始各月是否閏月
	        if ($mc[$j] - floor($mc[$j]) > 0) { //若是,則將此閏月代碼放入閏月旗標內
	            $leap = floor($mc[$j] + 0.5); //leap=0對應阴曆11月,1對應阴曆12月,2對應阴曆隔年1月,依此類推.
	            break;
	        }
	    }
	    
	    $mm = $mm + 2; //11月對應到1,12月對應到2,1月對應到3,2月對應到4,依此類推
	    
	    for ($i = 0; $i <= 14; $i++) { //求算阴曆各月之大小,大月30天,小月29天
	        $nofd[$i] = floor($jdnm[$i + 1] + 0.5) - floor($jdnm[$i] + 0.5); //每月天數,加0.5是因JD以正午起算
	    }
	    
	    $jd = 0; //儒略日历时间
	    $er = 0; //若輸入值有錯誤,er值將被設定為非0
	    
	    if ($isLeap){ //若是閏月
	        if ($leap < 3) { //而旗標非閏月或非本年閏月,則表示此年不含閏月.leap=0代表無閏月,=1代表閏月為前一年的11月,=2代表閏月為前一年的12月
	            $er = 1; //此年非閏年
	        } else { //若本年內有閏月
	            if ($leap != $mm) { //但不為輸入的月份
	                $er = 2; //則此輸入的月份非閏月,此月非閏月
	            } else { //若輸入的月份即為閏月
	                if ($dd <= $nofd[$mm]) { //若輸入的日期不大於當月的天數
	                    $jd = $jdnm[$mm] + $dd - 1; //則將當月之前的JD值加上日期之前的天數
	                } else { //日期超出範圍
	                    $er = 3;
	                }
	            }
	        }
	    } else { //若沒有勾選閏月則
	        if ($leap == 0) { //若旗標非閏月,則表示此年不含閏月(包括前一年的11月起之月份)
	            if ($dd <= $nofd[$mm - 1]) { //若輸入的日期不大於當月的天數
	                $jd = $jdnm[$mm - 1] + $dd - 1; //則將當月之前的JD值加上日期之前的天數
	            } else { //日期超出範圍
	                $er = 4;
	            }
	        } else { //若旗標為本年有閏月(包括前一年的11月起之月份) 公式nofd(mx - (mx > leap) - 1)的用意為:若指定月大於閏月,則索引用mx,否則索引用mx-1
	            if ($dd <= $nofd[$mm + ($mm > $leap) - 1]) { //若輸入的日期不大於當月的天數
	                $jd = $jdnm[$mm + ($mm > $leap) - 1] + $dd - 1; //則將當月之前的JD值加上日期之前的天數
	            } else { //日期超出範圍
	                $er = 4;
	            }
	        }
	    }
	    
	    return $er ? false : array_slice($this->Julian2Solar($jd), 0, 3);
	}
	/**
	 * 将公历时间转换成农历时间
	 * @param int $yy
	 * @param int $mm
	 * @param int $dd
	 * @return array(年,月,日,是否闰月)
	 */
	public function Solar2Lunar($yy, $mm, $dd) {
	    if (! $this->ValidDate($yy, $mm, $dd)) { //驗證輸入日期的正確性
	        return false;
	    }
	    
	    $prev = 0; //是否跨年了,跨年了则减一
	    $isLeap = 0;//是否闰月
	    
	    list($jdzq, $jdnm, $mc) = $this->GetZQandSMandLunarMonthCode($yy);
	    
	    $jd = $this->Solar2Julian($yy, $mm, $dd, 12, 0, 0); //求出指定年月日之JD值
	    if (floor($jd) < floor($jdnm[0] + 0.5)) {
	        $prev = 1;
	        list($jdzq, $jdnm, $mc) = $this->GetZQandSMandLunarMonthCode($yy - 1);
	    }
	    for ($i = 0; $i <= 14; $i++) { //指令中加0.5是為了改為從0時算起而不從正午算起
	        if (floor($jd) >= floor($jdnm[$i] + 0.5) && floor($jd) < floor($jdnm[$i + 1] + 0.5)) {
	            $mi = $i;
	            break;
	        }
	    }
	    
	    if ($mc[$mi] < 2 || $prev == 1) { //年
	        $yy = $yy - 1;
	    }
	    
	    if (($mc[$mi] - floor($mc[$mi])) * 2 + 1 != 1) { //因mc(mi)=0對應到前一年阴曆11月,mc(mi)=1對應到前一年阴曆12月,mc(mi)=2對應到本年1月,依此類推
	        $isLeap = 1;
	    }
	    $mm = (floor($mc[$mi] + 10) % 12) + 1; //月
	    
	    $dd = floor($jd) - floor($jdnm[$mi] + 0.5) + 1; //日,此處加1是因為每月初一從1開始而非從0開始
	    
	    return array($yy, $mm, $dd, $isLeap);
	}
	/**
	 * 求出含某公历年立春點開始的24节气
	 * @param int $yy
	 * @return array jq[($k+21)%24]
	 */
	public function Get24JieQi($yy) {
	    
	    $jq = array();
	    
	    $dj = $this->GetAdjustedJQ($yy - 1, 21, 23); //求出含指定年立春開始之3個節氣JD值,以前一年的年值代入
	    foreach ($dj as $k => $v){
	        if($k < 21){
	            continue;
	        }
	        if($k > 23){
	            continue;
	        }
	        $jq[] = $this->Julian2Solar($dj[$k]); //21立春;22雨水;23惊蛰
	    }
	    
	    $dj = $this->GetAdjustedJQ($yy, 0, 20); //求出指定年節氣之JD值,從春分開始
	    foreach ($dj as $k => $v){
	        $jq[] = $this->Julian2Solar($dj[$k]);
	    }
	    
	    return (array)$jq;
	}
	/**
	 * 四柱計算,分早子时晚子时,传公历
	 * @param int $yy
	 * @param int $mm
	 * @param int $dd
	 * @param int $hh 时间(0-23)
	 * @param int $mt 分钟数(0-59),在跨节的时辰上会需要,有的排盘忽略了跨节
	 * @param int $ss 秒数(0-59)
	 * @return array(天干, 地支, 对应的儒略日历时间, 对应年的12节+前后N节, 对应时间所处节的索引)
	 */
	public function GetGanZhi($yy, $mm, $dd, $hh, $mt=0, $ss=0){
	    if(! $jd = $this->Solar2Julian($yy, $mm, $dd, $hh, $mt, max(1, $ss))){ //多加一秒避免精度问题
	        return array();
	    }
        
	    $tg = $dz = array();

	    $jq = $this->GetPureJQsinceSpring($yy); //取得自立春開始的节,该数组长度固定为16
	    if ($jd < $jq[1]) { //jq[1]為立春,約在2月5日前後,
	        $yy = $yy - 1; //若小於jq[1],則屬於前一個節氣年
	        $jq = $this->GetPureJQsinceSpring($yy); //取得自立春開始的节
	    }
	    
	    $ygz = (($yy + 4712 + 24) % 60 + 60) % 60;
	    $tg[0] = $ygz % 10; //年干
	    $dz[0] = $ygz % 12; //年支

	    for ($j = 0; $j <= 15; $j++) { //比較求算節氣月,求出月干支
	        if ($jq[$j] >= $jd) { //已超過指定時刻,故應取前一個節氣
	            $ix = $j-1;
	            break;
	        }
	    }

	    $tmm = (($yy + 4712) * 12 + ($ix - 1) + 60) % 60; //数组0为前一年的小寒所以这里再减一
	    $mgz = ($tmm + 50) % 60;
	    $tg[1] = $mgz % 10; //月干
	    $dz[1] = $mgz % 12; //月支

	    $jda = $jd + 0.5; //計算日柱之干支,加0.5是將起始點從正午改為從0點開始.
	    $thes = (($jda - floor($jda)) * 86400) + 3600; //將jd的小數部份化為秒,並加上起始點前移的一小時(3600秒),取其整數值
	    $dayjd = floor($jda) + $thes / 86400; //將秒數化為日數,加回到jd的整數部份
	    $dgz = (floor($dayjd + 49) % 60 + 60) % 60;
	    $tg[2] = $dgz % 10; //日干
	    $dz[2] = $dgz % 12; //日支
	    if($this->zwz && ($hh >= 23)){ //区分早晚子时,日柱前移一柱
	        $tg[2] = ($tg[2] + 10 - 1) % 10;
	        $dz[2] = ($dz[2] + 12 - 1) % 12;
	    }
	    
	    $dh = $dayjd * 12; //計算時柱之干支
	    $hgz = (floor($dh + 48) % 60 + 60) % 60;
	    $tg[3] = $hgz % 10; //時干
	    $dz[3] = $hgz % 12; //時支
	    
	    return array($tg, $dz, $jd, $jq, $ix);
	}
	/**
	 * 计算凶亡地支
	 * @param int $day_tg_int 日天干索引序号
	 * @param int $day_dz_int 日地支索引序号
	 * @return array('index'=>[凶亡的地支的第一索引,凶亡的地支的第二索引,char=>凶亡对应字符]);
	 */
	private function GetXiongWang($day_tg_int,$day_dz_int){
		$xw_start = $day_dz_int - $day_tg_int -2;
		if($xw_start<0){
			$xw_start+=12;
		}
		if(12 == $xw_start){
			$xw_start = 0;
		}
		$xw_end = $xw_start+1;
		if(12 == $xw_end){
			$xw_end = 0;
		}
		return ['index'=>[$xw_start,$xw_end],'char'=>$this->cdz[$xw_start].$this->cdz[$xw_end]];
	}
	/**
	 * 计算十神
	 * @param $day_tg_int 日天干索引序号
	 * @param $other_tg_int
	 * @return array('index'=>[第一层索引,第二层索引],'char'=>神煞的字)
	 */
	private function GetTenGod($day_tg_int,$other_tg_int){
		$l2_index = ($day_tg_int+$other_tg_int)%2;//判断两者结合后是阴是阳,从而决定数组的第二层索引
		$day_wx = $this->GetTgWx($day_tg_int);
		$other_wx = $this->GetTgWx($other_tg_int);
		$l1_index = $other_wx - $day_wx;
		if($l1_index<0){
			$l1_index += 5;
		}
		return ['index'=>[$l1_index,$l2_index],'char'=>$this->ten_god[$l1_index][$l2_index]];
	}
	/**
	 * 获取天干五行索引
	 * @param int $tg 天干索引数字
	 * @return int 五行索引
	 */
	public function GetTgWx($tg){
		if($tg%2){ //如果不是偶数,减一变偶数,以判断
			$tg--;
		}
		return $tg/2;
	}
	/**
	 * 获取三合的地支和属性,也适合用于半合(3和7除外,卯未为破不为合),其中第一个必须存在才能有半合或者三合第一为帝旺,第二为墓,第三为长生
	 * @param int $dz 地支
	 * @return ['sanhe'=>[三个int,对应地支,其中第一个必须存在才能合],'ju'=>'三合成的五行局序号,木火土金水']
	 */
	public function getSanHe($dz){
		//0子旺,4辰墓,8申生 | 3卯,7未,11亥生 |6,10,2长生 | 9 1 5生
		$fir = $dz%4*3;
		$sec = ($fir+4)%12;
		$thr = ($sec+4)%12;
		$ju_array=[4,0,1,3];
		$ju = $ju_array[$fir/3];
		return ['sanhe'=>[$fir,$sec,$thr],'ju'=>$ju];
	}

	/**
	 * 获取某地支的相冲的地支
	 * @param int $dz 地支
	 * @return int 相冲地支
	 */
	public function getChong($dz){
		return ($dz+6)%12;
	}

	/**
	 * 根据某一个地支,获取刑它的地支,刑是一种单向的制约关系,目前没找到啥数字规律 只能写map
	 * @param int $dz 地支数字
	 * @return int 刑它的地支;
	 */
	public function getXingFrom($dz){
		//子刑卯，卯刑子，为无理之刑；
		//丑刑未，未刑戌，戌刑丑，为特势之刑
		//寅刑巳，巳刑申，申刑寅，为无恩之刑；
		//辰,酉,亥，午，为自刑；
		$xing_map = [3,7,5,0,4,8,6,10,2,9,1,11];
		return $xing_map[$dz];
	}

	/**
	 * 天干相合的另外一个天干与合成的局,天干相合有条件
	 * 第一,必须是相邻的柱,否则不能相合,
	 * 
	 * 1.甲己合化土。必须是在辰、巳、午、未、戌、丑六个月令地支时间内，才是甲己合化土。即：甲木的性质不复存在而变成了土的物质，当然己土还是土，表示土的力量增加，木的力量没有了。若四柱命理喜甲木而被己土合化，则人体胆经或头部有疾；若忌甲木，则己土来合化甲木代表人体健康的信息。
	 * 乙庚合化金。必须是在4辰、8申、9酉、10戌、1丑五个月令中，才是乙庚合化金。表示乙木的性质不复存在，金力加大。若四柱命理喜金，则人体健康；若忌金，则乙庚合化金，主人体肝经有疾。
	 *3.丙辛合化水。必须是在8申、9酉、11亥、0子、1丑五个月令中，才是丙辛合化水。表示丙火、辛金的性质都不复存在，水的力量增加。若四柱命理喜火喜金，则双双合化主人体小肠经、肺经有疾；若忌火金，则又是人体健康的信息。
	 *4.丁壬合化木。必须是在2寅、3卯、4辰、11亥、0子、1丑六个月令中，才是丁壬合化木。表示丁火、壬水的性质不复存在，木的力量加大。若四柱命理喜木，则是人体健康的标志；若是忌木，则人体心经、膀胱经有疾。
	 *5.戊癸合化火。必须是在2寅、3卯、4辰、5巳、6午、7未六个月令中，才是戊癸合化火。表示戊土、癸水的性质都不复存在，而火的力量加旺。若四柱命理喜火，则是人体健康的标志；若是忌火，则人体胃经、肾经有病。
	 * @param int $month_dz 月的地支数字
	 * @param int $tg 天干数字
	 * @return false/array(['he'=>[天干数字],'局'=>五行数字)
	 */
	function tgHe($month_dz,$tgA){
		if($tgA>11){
			return false;
		}
		if($tgA>=5){
			$tg_from = $tgA-5;
			$tg_tag = $tgA;
		}else{
			$tg_from = $tgA;
			$tg_tag = $tgA + 5;
		}
		$map = [
			[1,4,5,6,7,10],
			[4,8,9,10,1],
			[8,9,11,0,1],
			[2,3,4,11,0,1],
			[2,3,4,5,6,7]
		];
		$ju_map =  [2,3,4,0,1];
		$dz_must = $map[$tg_from];
		if(in_array($month_dz,$dz_must)){
			return ['he'=>[$tg_from,$tg_tag],'ju'=>$ju_map[$tg_from]];
		}else{
			return false;
		}
	}

	/**
	 * 六合 也叫暗合,2寅11亥合化木，0子1丑合化土，3卯10戍合化火，4辰9酉合化金，6午7未合化土，8申5巳合化水。
	 * @param int $dz 地支数字
	 * @return array [index=>与他暗合的地支数字,ju=>化为哪个五行局]
	 */
	function getLiuHe($dz){
		if($dz == 0){
			$tmp = 12;
		}else{
			$tmp = $dz;
		}
		
		$he = (13-$tmp)%12;
		$hua = [2,2,0,1,3,4,2];
		if($dz<$he){
			//dump($dz);
			$ju = $hua[$dz];
		}else{
			$ju = $hua[$he];
		}
		return ['index'=>$he,'ju'=>$ju];
	}

	/**
	 * 获取六害的另外一个地支,也叫相穿,或者制,穿的杀伤力比冲厉害
	 * @param int $dz 地支
	 * @return int $相害的另外一个地支
	 */
	public function getChuan($dz){
		//0子7未、1丑6午、2寅5巳、3卯4辰、8申11亥、9酉10戌,前面特征是和为7,后面特征是和为19
		return (19-$dz)%12;
	}
	
	/**
	 * 获取相破的另外一个地支,地支相破：0子9酉、3卯6午、1丑4辰、7未10戌、2寅11亥、5巳8申等这六个地支在一个命局中相遇，即为相破。其中位于四个帝旺位的想破强力(即子午卯酉),其他力量很小。
	 * @param int $dz 地支
	 * @return int 相破的地支
	 */
	public function getPo($dz){
		$map = [9,4,11,6,1,8,6,10,5,0,2,11];
		return $map[$dz];
	}

	/**
	 * 计算十二长生
	 * @param int $tg 天干索引数字,一般排盘时候用日为主
	 * @param int $dz 地支索引数字
	 * @return array ('index'=>十二长生索引,'char'=>长生名字)
	 */
	public function GetCs($tg,$dz){
		$cs_dz_index = $this->cs_tg2dz[$tg];//长生所在地支
		if(0 == $tg%2){//阳天干,正向取长生
			$move_num =  $dz - $cs_dz_index;
		}else{
			$move_num =  $cs_dz_index - $dz;
		}
		if($move_num<0){
			$move_num += 12;
		}
		$char = $this->cs[$move_num];
		
		return ['index'=>$move_num,'char'=>$char];
	}
	/**
	 * 计算命宫
	 * @param $year_tg 年天干索引数字
	 * @param $month_dz 月地支索引数字
	 * @param $hour_dz 时地支索引数字
	 * @return array (index=>[天干,地支索引],char=>宫名)
	 */
	private function GetGong($year_tg,$month_dz,$hour_dz){
		$gong_dz = (29  -$month_dz - $hour_dz)%12;
		$gong_dz<2?$xi=1:$xi=0;//系数,由于从一月开始 子 丑 要算作 12 和 13
		$gong_tg = (($year_tg%5)*2+$gong_dz+12*$xi)%10;
		return ['index'=>[$gong_tg,$gong_dz],'char'=>$this->ctg[$gong_tg].$this->cdz[$gong_dz]];
	}
	/**
	 * 计算北京时间属于哪个时辰,并划分 时头 时中 还是时尾
	 * @param $hh 时间的整数格式,如 1 或者 03 或者11
	 * @parm $ii 分钟的整数格式,如 0 或者 01 或者 22等
	 */
	public function GetCTPart($hh,$ii){
		$des = ['时头','时中','时尾'];
		$hh = intval($hh);
		$ii = intval($ii);
		$shi_chen = intval(($hh+1)/2);
		$part = 0;
		if($hh%2){
			//如果$hh为单数,如23点,即为时辰的上部分
			if($ii>40){
				$part = 1;//时中
			}
		}else{
			if($ii<20){
				$part = 1;
			}else{
				$part = 2;//时尾
			}
		}
		return ['index'=>[$shi_chen,$part],'char'=>$this->cdz[$shi_chen].$des[$part]];
	}

	/**
	 * 计算本气
	 * @param $tg 单个天干的数字
	 * @param $dz 单个地支的数字
	 * @return array/false [index=>本气序号,char=>本气名]
	 */
	public function getSelfQi($tg,$dz){
		$biao = [
			[2,3,5,6,5,6,8,9,11,0],
			[3,null,6,null,7,null,9,null,0,null],
			[11,11,2,2,2,2,4,4,8,8],
			[7,7,10,10,10,4,1,1,4,4],
			[4,4,7,7,null,null,10,10,1,1],
			[6,6,9,9,9,9,0,0,3,3],
			[8,8,11,11,11,11,2,2,5,5]
		];
		$index = false;
		for($i = 0;$i<7;$i++){
			if($biao[$i][$tg] == $dz){
				$index = $i;
				break;
			}
		}
		if($index === false){
			return ['index'=>-1,'char'=>'--'];
		}else{
			return ['index'=>$index,'char'=>$this->selfQi[$index]];
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
	public function GetInfo($gd, $yy, $mm, $dd, $hh, $mt=0, $ss=0){
	    if(!in_array($gd, [0,1])){
	        return [];
	    }
	    
		$ret = array();
		$ret['sex'] = $gd;
	    $big_tg = $big_dz = array(); //大运
	    
	    list($tg, $dz, $jd, $jq, $ix) = $this->GetGanZhi($yy, $mm, $dd, $hh, $mt, $ss);
	    $xiong_wang = $this->GetXiongWang($tg[2],$dz[2]);
        $pn = $tg[0] % 2; //起大运.阴阳年干:0阳年1阴年

	    if(($gd == 0 && $pn == 0) || ($gd == 1 && $pn == 1)) { //起大运时间,阳男阴女顺排
	        $span = $jq[$ix + 1] - $jd; //往后数一个节,计算时间跨度
	        
	        for($i = 1; $i <= 12; $i++){ //大运干支
	            $big_tg[] = ($tg[1] + $i) % 10;
	            $big_dz[] = ($dz[1] + $i) % 12;
	        }
	    } else { // 阴男阳女逆排,往前数一个节
	        $span = $jd - $jq[$ix];

	        for($i = 1; $i <= 12; $i++){ //确保是正数
	            $big_tg[] = ($tg[0] + 20 - $i) % 10;
	            $big_dz[] = ($dz[0] + 24 - $i) % 12;
	        }
	    }
	    
	    $days = intval($span * 4 * 30); //折合成天数:三天折合一年,一天折合四个月,一个时辰折合十天,一个小时折合五天,反推得到一年按360天算,一个月按30天算
	    $y = intval($days / 360); //三天折合一年
	    $m = intval($days % 360 / 30); //一天折合四个月
	    $d = intval($days % 360 % 30); //一个小时折合五天
	    
	    $ret['tg'] = $tg;//四柱天干
		$ret['dz'] = $dz;//四柱地支
		$ret['bazi'] = [];
		$ret['sc']= $this->GetCTPart($hh,$mt);
		$ret['dz_cg'] = [];
	//	$bazi_str=['年,','月,','日,','时'];
		$tg_cg_god = $dz_god = $dz_main_god = $selfQi = [];
		for($i = 0; $i <= 3; $i++){
			$ret['bazi'][]= [$this->ctg[$tg[$i]],$this->cdz[$dz[$i]]];
			$tg_cg_god[$i] = $this->GetTenGod($tg[2],$tg[$i]);
			$tmp_dzcg = $this->dzcg[$dz[$i]];
			$tmp_dz_god = [];
			$tmp_dzcg_char = [];
			foreach($tmp_dzcg as $cg){
				$tmp_dz_god[] = $this->GetTenGod($tg[2],$cg);
				$tmp_dzcg_char[]=$this->ctg[$cg];
			}
			$ret['dz_cg'][$i] = ['index'=>$tmp_dzcg,'char'=>$tmp_dzcg_char];
			$dz_main_god[] = $this->GetTenGod($tg[2],$this->dztg[$dz[$i]]);
			$dz_god[$i] = $tmp_dz_god;	
			$selfQi[$i] = $this->getSelfQi($tg[2],$dz[$i]);
		}
		$cs = $year_cs = $month_cs = $hour_cs = [];
		for($i = 0; $i <= 3; $i++){
			$cs[$i] = $this->GetCs($tg[2],$dz[$i]);//日长生 主要
			$year_cs[$i] = $this->GetCs($tg[0],$dz[$i]);
			$month_cs[$i] = $this->GetCs($tg[1],$dz[$i]);
			$hour_cs[$i] = $this->GetCs($tg[3],$dz[$i]);
		}
		$tg_cg_god[2]=['index'=>[5,5],'char'=>'元'];
		
		$ret['xw'] = $xiong_wang;//凶亡
		$ret['gong'] = $this->GetGong($tg[0],$dz[1],$dz[3]);//命宫
		$ret['tg_cg_god'] = $tg_cg_god;
		$ret['dz_main_god'] = $dz_main_god;
		$ret['dz_cg_god'] = $dz_god;
		$ret['day_cs'] = $cs;
		$ret['year_cs'] = $year_cs;
		$ret['month_cs'] = $month_cs;
		$ret['hour_cs'] = $hour_cs;
		
		$ret['self_qi'] = $selfQi;
	    $ret['big_tg'] = $big_tg;
	    $ret['big_dz'] = $big_dz;
	    $ret['start_desc'] = "{$y}年{$m}月{$d}天起运";
	    $start_jdtime = $jd + $span * 120; //三天折合一年,一天折合四个月,一个时辰折合十天,一个小时折合五天,反推得到一年按360天算
	    $ret['start_time'] = $this->Julian2Solar($start_jdtime); //转换成公历形式,注意这里变成了数组
	    
	    $ret['big'] = $ret['years'] = []; //八字,大运,流年的字符表示
	    $ret['big_start_time'] = $ret['big_god'] = $ret['big_cs'] =[]; //各步大运的起始时间
	    
	    $ret['xz'] = $this->cxz[$this->GetZodiac($mm, $dd)]; //星座
	    $ret['sx'] = $this->csa[$dz[0]]; //生肖

	    for($i = 0; $i < 12; $i++){
			$ret['big'][] = $this->ctg[$big_tg[$i]] . $this->cdz[$big_dz[$i]] ;
			$ret['big_cs'][] = $this->getCs($tg[2],$big_dz[$i]);
			$ret['big_god'][] = $this->GetTenGod($tg[2],$big_tg[$i]);
	        $ret['big_start_time'][] = $this->Julian2Solar($start_jdtime + $i*10*365);
	    }
	    $ret['years_info'] = [];
	    for($i=1,$j=0; ;$i++){
	        if(($yy + $i) < $ret['start_time'][0]){ //还没到起运年
	            continue;
	        }
	        if($j++ >= 120){
	            break;
	        }
	        
	        $t = ($tg[0] + $i) % 10;
	        $d = ($dz[0] + $i) % 12;
	        
			//$ret['years'] .= $this->ctg[$t] . $this->cdz[$d] . ' ';
			$tmp_year_dzcg = $this->dzcg[$d];
			$tmp_year_god = [];
			foreach($tmp_dzcg as $cg){
				$tmp_year_god[]=$this->GetTenGod($tg[2],$cg);
			}
			
			$ret['years_info'][]=[
				'year'=>$yy+$i-1,
				'index'=>[$t,$d],
				'char'=>$this->ctg[$t] . $this->cdz[$d],
				'cg'=>$tmp_year_dzcg,
				'cs'=>$this->getCs($tg[2],$d),
				'tg_god'=>$this->GetTenGod($tg[2],$t),
				'dz_god'=>$tmp_year_god

			];
	        //if($j%10 == 0){
	           // $ret['years'] .= "\n";
	       // }
	    }
	    
	    return (array)$ret;
	}
}
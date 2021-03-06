<?php	
class GameUser{

	public $gameid;
	public $uid;
	public $nick;
	public $type;
	public $head;
	public $hourcoin;
	public $level;
	public $tec_force;
	public $tec;
	public $last_land;
	public $land_key;
	public $coin;
	public $prop;
	public $diamond;
	public $energy;
	public $rmb;
	public $atk_list;
	public $def_list;
	public $hang;
	public $active;
	public $card;
	public $pk_common;
	public $regtime;
	
	
	private $openDataChange = false;
	private $haveSetCoin = false;
	
	private $changeKey = array();
	
	public $openData;

	//初始化类
	function __construct($data,$openData=null,$type=0){
		$this->gameid = $data['gameid'];
		$this->uid = $data['uid'];
		$this->nick = $data['nick'];
		$this->head = $data['head'];
		$this->type = $data['type'];
		$this->hourcoin = $data['hourcoin'];
		$this->level = (int)$data['level'];
		$this->tec_force = (int)$data['tec_force'];
		$this->last_land = $data['last_land'];
		$this->def_list = $this->decode($data['def_list'],'{"list":{}}');
		$this->pk_common = $this->decode($data['pk_common'],'{"pktype":"","pkdata":null}');
		$this->tec = $this->decode($data['tec'],'{}');
		
		if($type == 1)
		{
			$this->card = $this->decode($data['card'],'{"hero":{},"monster":[],"skill":{"201":30,"202":30,"203":30}}');
		}
		
		
		if($openData == null)
			return;
		$this->openData = $openData;
		$this->coin = $this->decode($data['coin'],'{"v":100,"t":'.time().',"st":0}');
		$this->rmb = (int)$data['rmb'];
		$this->regtime = (int)$data['regtime'];
		$this->diamond = (int)$data['diamond'];
		$this->land_key = $data['land_key'];
		$this->prop = $this->decode($data['prop']);
		$this->energy = $this->decode($data['energy'],'{"v":60,"t":'.$this->regtime.'}');
		$this->active = $this->decode($data['active'],'{"task":{}}');//活动
		$this->atk_list = $this->decode($data['atk_list'],'{"list":{}}');
		$this->hang = $this->decode($data['hang'],'{"level":0,"cd":""}');
		$this->card = $this->decode($data['card'],'{"herolv":{},"hero":{},"monster":[],"skill":{"201":30,"202":30,"203":30}}');
		
	}
	
	function decode($v,$default = null){
		if(!$v)
		{
			if($default)
				$v = $default;
			else
				$v = '{}';
		}
		return json_decode($v);
	}
	
	function addTaskStat($key){
		global $returnData;
		if(!$this->active->task->stat)
			$this->active->task->stat = new stdClass();
		if(!$this->active->task->stat->{$key})
		{
			$this->active->task->stat->{$key} = 1;
			$this->setChangeKey('active');
			if(!$returnData->sync_task)
				$returnData->sync_task = array();
			$returnData->sync_task['stat'] = $this->active->task->stat;
		}
	}

	
	function setChangeKey($key){
		$this->changeKey[$key] = 1;
	}
	function setOpenDataChange(){
		$this->openDataChange = true;
	}
	
	//体力相关==============================================
	function testEnergy($v){
		global $returnData;
		if($this->getEnergy() < $v)
		{
			$returnData->sync_energy = $this->energy;
			return false;
		}
		return true;
	}
	
	function getEnergy(){
		$this->resetEnergy();
		return $this->energy->v;// + $this->energy->rmb;
	}
	function addEnergy($v){
		global $returnData;
		if($v)
		{
			$this->resetEnergy();
			$this->energy->v += $v;
			$this->setChangeKey('energy');
			$returnData->sync_energy = $this->energy;
		}
	}
	function resetEnergy(){//每一段时间回复一定量
		$cd = 30*60;
		$time = time();
		$add = floor(($time - $this->energy->t)/$cd);
		if($add > 0)
		{
			$this->energy->v = min(60,$this->energy->v + $add);
			$this->energy->t = $this->energy->t + $add*$cd;
		}
	}
	
	
	//==============================================   end
	
	function addDiamond($v){
		if(!$v)
			return;
		global $returnData;
		$this->diamond += $v;
		$this->setChangeKey('diamond');
		$returnData->sync_diamond = $this->diamond;
	}
	
	//取钱
	function getCoin(){
		$this->resetCoin();
		return $this->coin->v;
	}
	
	//加钱
	function addCoin($v){
		if(!$v)
			return;
		global $returnData;
		$this->resetCoin();
		$this->coin->v += $v;
		$this->setChangeKey('coin');
		$returnData->sync_coin = $this->coin;
	}
	
	//因为主人关系而使钱发生变化
	function resetCoin(){
		if($this->haveSetCoin)
			return false;
		global $returnData;
		$b = false;
		$this->haveSetCoin = true;
		$time = time();
		
		//生产影响
		$cd = 60;
		$step = round($this->hourcoin/60);
		$add = floor(min($time - $this->coin->t,3600*48)/$cd);
		if($add > 0)
		{
			$this->coin->v += $add*$step;
			$this->coin->t = $this->coin->t + $add*$cd;
			$b = true;
		}
		
		
		//奴隶影响
		$list = explode(",",$this->openData['masterstep']);
		$len = count($list);
		
		if($len > 1)
		{
			$this->setChangeKey('masterstep');
			$this->openDataChange = true;
			$this->openData['masterstep'] = $list[$len - 1];
		}
		
		//计算需要扣的钱
		$count = 0;
		$lastData = explode("|",$list[0]);
		for($i = 1;$i<$len;$i++)//选处理多次变动的历史
		{
			$temp = explode("|",$list[$i]);
			$t = (int)$temp[1];
			if($t > $this->coin->st)//未处理过的
			{
				if($lastData[0] == 1)//成为奴隶
				{
					$t0 = (int)$lastData[1];
					$count += floor(($t - $t0)/3600);//每小时结算一次
				}
				$this->coin->st = $t;
			}
			$lastData = $temp;
		}
		
		if($lastData[0] == 1)//成为奴隶
		{
			$num = floor(($time - $this->coin->st)/3600);//每小时结算一次
			if($num)
			{
				$this->coin->st += $num*3600;
				$count += $num;
			}
		}
		$this->coin->ss = $lastData[0] == 1;
		if($count)
		{
			if($count > 24)
				$count = 24;
			$this->coin->v -= $count*floor($this->hourcoin*0.2);
			$b = true;
		}
		
		if($b)
		{
			$this->setChangeKey('coin');
			$returnData->sync_coin = $this->coin;
		}
		return $b;
	}
	
	function getTecLevel($id){
		global $tec_base;
		if($this->tec->{$id})
			return $this->tec->{$id};
		if($tec_base[$id]['type'] == 1)
			return 1;
		return 0;
	}	
	
	function getHp(){
		return 2 + $this->getTecLevel(2);
	}
	function getMaxSlave(){
		return 1 + $this->getTecLevel(3);
	}
	function maxCardNum(){
		return 9 + $this->getTecLevel(4);
	}
	
	//取道具数量
	function getPropNum($propID){
		if($this->prop->{$propID})
			return $this->prop->{$propID};
		return 0;
	}
	
	//改变道具数量
	function addProp($propID,$num){
		global $returnData;
		if(!$this->prop->{$propID})
		{
			$this->prop->{$propID} = 0;
		}
			
		$this->prop->{$propID} += $num;
		$this->setChangeKey('prop');	
		
		if(!$returnData->sync_prop)
		{
			$returnData->sync_prop = new stdClass();
		}
		$returnData->sync_prop->{$propID} = $this->prop->{$propID};
	}
	
	//改变技能数量
	function addSkill($skillID,$num){
		global $returnData;
		if(!$this->card->skill->{$skillID})
		{
			$this->card->skill->{$skillID} = $num;
		}
		else
		{
			$this->card->skill->{$skillID} += $num;
			if(!$this->card->skill->{$skillID})
				unset($this->card->skill->{$skillID});
		}
		
		if(!$returnData->sync_skill)
		{
			$returnData->sync_skill = new stdClass();
		}
		if($this->card->skill->{$skillID})
			$returnData->sync_skill->{$skillID} = $this->card->skill->{$skillID};
		else
			$returnData->sync_skill->{$skillID} = 0;
			
		$this->setChangeKey('card');	
	}
	
	//改变英雄数量
	function addHero($heroID,$num){
		global $returnData;
		if(!$this->card->hero)
			$this->card->hero = new stdClass();
		if(!$this->card->hero->{$heroID})
		{
			$this->card->hero->{$heroID} = $num;
		}
		else
		{
			$this->card->hero->{$heroID} += $num;
			if(!$this->card->hero->{$heroID})
				unset($this->card->hero->{$heroID});
		}
		
		if(!$returnData->sync_hero)
		{
			$returnData->sync_hero = new stdClass();
		}
		if($this->card->hero->{$heroID})
			$returnData->sync_hero->{$heroID} = $this->card->hero->{$heroID};
		else
			$returnData->sync_hero->{$heroID} = 0;
			
		$this->setChangeKey('card');	
	}
	
	function addHeroLV($heroID){
		global $returnData;
		if(!$this->card->herolv)
			$this->card->herolv = new stdClass();
		if(!$this->card->herolv->{$heroID})
		{
			$this->card->herolv->{$heroID} = 1;
		}

		$this->card->herolv->{$heroID} ++;
		
		if(!$returnData->sync_herolv)
		{
			$returnData->sync_herolv = new stdClass();
		}
		$returnData->sync_herolv->{$heroID} = $this->card->herolv->{$heroID};
			
		$this->setChangeKey('card');	
	}
	
	function getSkill($skillID){
		if(!$this->card->skill->{$skillID})
		{
			return 0;
		}
		return $this->card->skill->{$skillID};
	}
	
	//取英雄等级
	function getHeroLevel($heroID){
		if(!$this->card->hero->{$heroID})
		{
			return 0;
		}
		if(!$this->card->herolv)
		{
			$this->card->herolv = new stdClass();
		}
		if(!$this->card->herolv->{$heroID})
		{
			return 1;
		}
		return $this->card->herolv->{$heroID};
	}
	
	//取英雄等级上限
	function getMaxHeroLevel($heroID){
		if(!$this->card->hero->{$heroID})
		{
			return 0;
		}
		$arr = array(0,1,10,30,60,110);
		$num =  $this->card->hero->{$heroID};
		for($i=5;$i>=0;$i--)
        {
             if($num >= $arr[$i])
                return $i;
        }
		return 0;
	}
	
	
	


	
	//把结果写回数据库
	function write2DB($fromLogin = false){
		//return false;
		function addKey($key,$value,$needEncode=false){
			if($needEncode)
				return $key."='".json_encode($value)."'";
			else 
				return $key."=".$value;
		}
		
		global $conne,$msg,$mySendData,$sql_table,$returnData;
		
		if(!$fromLogin)
		{
			$returnData->sync_opendata = $this->openData;
		}
		
		$arr = array();
		
		if($this->changeKey['rmb'])
			array_push($arr,addKey('rmb',$this->rmb));
		if($this->changeKey['level'])
			array_push($arr,addKey('level',$this->level));
		if($this->changeKey['tec_force'])
			array_push($arr,addKey('tec_force',$this->tec_force));
		if($this->changeKey['diamond'])
			array_push($arr,addKey('diamond',$this->diamond));
		if($this->changeKey['hourcoin'])
			array_push($arr,addKey('hourcoin',$this->hourcoin));
		if($this->changeKey['head'])
			array_push($arr,addKey('head',$this->head));
		if($this->changeKey['land_key'])
			array_push($arr,addKey('land_key',"'".$this->land_key."'"));
			
		if($this->changeKey['tec'])
			array_push($arr,addKey('tec',$this->tec,true));
		if($this->changeKey['prop'])
			array_push($arr,addKey('prop',$this->prop,true));
		if($this->changeKey['coin'])
			array_push($arr,addKey('coin',$this->coin,true));	
		if($this->changeKey['energy'])
			array_push($arr,addKey('energy',$this->energy,true));	
		if($this->changeKey['active'])
			array_push($arr,addKey('active',$this->active,true));	
		if($this->changeKey['atk_list'])
			array_push($arr,addKey('atk_list',$this->atk_list,true));	
		if($this->changeKey['def_list'])
			array_push($arr,addKey('def_list',$this->def_list,true));	
		if($this->changeKey['hang'])
			array_push($arr,addKey('hang',$this->hang,true));		
		if($this->changeKey['card'])
			array_push($arr,addKey('card',$this->card,true));
		if($this->changeKey['pk_common'])
			array_push($arr,addKey('pk_common',$this->pk_common,true));	
				
			debug($this->changeKey);
			
		if(count($arr) > 0 || $this->changeKey['last_land'])
		{
			$this->last_land = time();
			array_push($arr,addKey('last_land',$this->last_land));	
			$sql = "update ".getSQLTable('user_data')." set ".join(",",$arr)." where gameid='".$this->gameid."'";
			  debug($sql);
			if(!$conne->uidRst($sql))//写用户数据失败
			{
				$mySendData->error = 4;
				return false;
			}
		}		
		
		if($this->openDataChange)
		{
			$arr = array();
			if($this->changeKey['masterstep'])
				array_push($arr,addKey('masterstep',"'".$this->openData['masterstep']."'"));
			if($this->changeKey['mailtime'])
				array_push($arr,addKey('mailtime',$this->openData['mailtime']));
				
			if(count($arr))
			{
				$sql = "update ".getSQLTable('user_open')." set ".join(",",$arr)." where gameid='".$this->gameid."'";
				// debug($sql);
				if(!$conne->uidRst($sql))//写用户数据失败
				{
					$mySendData->error = 4;
					return false;
				}
			}
			
		}
		$this->changeKey = array();
		return true;
			
	}
}

//获取其它玩家的数据
function getUser($gameid){
	global $conne;
	$sql = "select * from ".$sql_table."user_data where id='".$gameid."'";
	$result = $conne->getRowsRst($sql);
	if($result)
		return new GameUser($result);
	return null;
}
?>
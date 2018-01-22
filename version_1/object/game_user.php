<?php	
class GameUser{

	public $gameid;
	public $uid;
	public $nick;
	public $type;
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
	
	private $changeKey = array();
	
	private $openData;

	//初始化类
	function __construct($data,$openData=null){
		$this->gameid = $data['gameid'];
		$this->uid = $data['uid'];
		$this->nick = $data['nick'];
		$this->type = $data['type'];
		$this->hourcoin = $data['hourcoin'];
		$this->level = (int)$data['level'];
		$this->tec_force = (int)$data['tec_force'];
		$this->last_land = $data['last_land'];
		$this->def_list = $this->decode($data['def_list'],'{"list":[]}');
		
		
		if($openData == null)
			return;
		$this->openData = $openData;
		
		$this->coin = (int)$data['coin'];
		$this->rmb = (int)$data['rmb'];
		$this->diamond = (int)$data['diamond'];
		$this->land_key = (int)$data['land_key'];
		$this->tec = $this->decode($data['tec'],'{"main":{},"monster":{}}');
		$this->prop = $this->decode($data['prop']);
		$this->energy = $this->decode($data['energy'],'{"v":0,"t":0}');
		$this->active = $this->decode($data['active'],'{"task":{}}');//活动
		$this->atk_list = $this->decode($data['atk_list'],'{"list":[]}');
		$this->hang = $this->decode($data['hang'],'{"level":0,"time":0}');
		$this->card = $this->decode($data['card'],'{"monster":[],"skill":[]}');
		$this->pk_common = $this->decode($data['pk_common'],'{"pktype":"","pkdata":null}');
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
	
	//体力相关==============================================
	function testEnergy($v){
		global $returnData;
		if($this->getEnergy() < $v)
		{
			$returnData->sync_energy = $userData->energy;
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
	
	//加钱
	function addCoin($v){
		if(!$v)
			return;
		global $returnData;
		$this->coin += $v;
		$this->setChangeKey('coin');
		$returnData->sync_coin = $this->coin;
	}
	
	//升级科技
	function levelUpTec($type,$id){
		global $returnData;
		if($this->tec->{$type}->{$id})
			$this->tec->{$type}->{$id} ++;
		else
			$this->tec->{$type}->{$id} = 1;
		$this->setChangeKey('tec');
		
		if(!$returnData->{'sync_tec_'.$type})
		{
			$returnData->{'sync_tec_'.$type} = new stdClass();
		}
		$returnData->{'sync_tec_'.$type}->{$id} = $this->tec->{$type}->{$id};
		
		$this->resetForce();
	}
	
	function getHp(){
		return 3;
	}
	
	//取道具数量
	function getPropNum($propID){
		if($this->prop->{$propID})
			return $this->prop->{$propID}->num;
		return 0;
	}
	
	//改变道具数量
	function addProp($propID,$num){
		global $returnData;
		if(!$this->prop->{$propID})
		{
			$this->prop->{$propID} = new stdClass();
			$this->prop->{$propID}->num = 0;
		}
			
		$this->prop->{$propID}->num += $num;
		$this->setChangeKey('prop');	
		
		if(!$returnData->sync_prop)
		{
			$returnData->sync_prop = new stdClass();
		}
		$returnData->sync_prop->{$propID} = $this->prop->{$propID};
	}
	
	//受科技和挂机影响
	function resetHourCoin(){
	
	}

	
	//把结果写回数据库
	function write2DB(){
		//return false;
		function addKey($key,$value,$needEncode=false){
			if($needEncode)
				return $key."='".json_encode($value)."'";
			else 
				return $key."=".$value;
		}
		
		global $conne,$msg,$mySendData,$sql_table;
		$arr = array();
		
		if($this->changeKey['rmb'])
			array_push($arr,addKey('rmb',$this->rmb));
		if($this->changeKey['level'])
			array_push($arr,addKey('level',$this->level));
		if($this->changeKey['tec_force'])
			array_push($arr,addKey('tec_force',$this->tec_force));
		if($this->changeKey['coin'])
			array_push($arr,addKey('coin',$this->coin));
		if($this->changeKey['diamond'])
			array_push($arr,addKey('diamond',$this->diamond));
		if($this->changeKey['hourcoin'])
			array_push($arr,addKey('hourcoin',$this->hourcoin));
			
		if($this->changeKey['tec'])
			array_push($arr,addKey('tec',$this->tec,true));
		if($this->changeKey['prop'])
			array_push($arr,addKey('prop',$this->prop,true));
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
				
			
			
		if(count($arr) == 0)
			return true;
		array_push($arr,addKey('last_land',time()));	
			
		$sql = "update ".getSQLTable('user_data')." set ".join(",",$arr)." where gameid='".$this->gameid."'";
		 debug($sql);
		if(!$conne->uidRst($sql))//写用户数据失败
		{
			$mySendData->error = 4;
			return false;
		}
		
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
<?php 
	function getPVPLevel($score){
		$pvpLevelBase = array(0,100,250,500,800,1100,1500,2000,2500,3000,3500,4000,4500,5000,5500,6000,6500,7000,7500,8000);
		 for($i= 20;$i>=1;$i--)
        {
            if($score >= $pvpLevelBase[$i-1])
                return $i;
        }
		return 1;
	}
?> 
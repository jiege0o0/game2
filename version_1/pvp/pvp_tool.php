<?php 
	function getPVPLevel($score){
		$pvpLevelBase = array(0,100,250,500,800,1100,1500,2000,2500,3000,3300,3600,3900,4300,4700,5100,5500,5900,6300,6800);
		 for($i= 20;$i>=1;$i--)
        {
            if($score >= $pvpLevelBase[$i-1])
                return $i;
        }
		return 1;
	}
?> 
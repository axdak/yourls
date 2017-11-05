<?php
/*
	函数库
*/
	function json_error($msg,$status='-1')
{
	$json['status'] = $status;
	$json['err_msg']= $msg;
	echo json_encode($json);exit;
}


	function label($id='')
	{
		if(empty($id)) return '';
		$data['id'] = (int)$id;
		$model = M('label');
		$list=$model->field('content,status')->where($data)->find();
		if(!$list or $list['status']==0) return '';
		return $list['content'];
	}
    function get_tinyurl($integer,$base ='0123456789abcdefghijklmnopqrstuvwxyz') 
    {   
        $length = strlen($base); 
        while($integer > $length - 1) 
        {   
            $out = $base[fmod($integer, $length)] . $out; 
            $integer = floor( $integer / $length ); 
        }   
        return $base[$integer] . $out; 
    }  
	
	function getfreetiny($arr)
	{
		$tinyurl = array();
		if(isset($arr[0]['tinyurl']))
		{
			foreach($arr as $v)
			{
				$tinyurl[] = $v['tinyurl'];
			}
		}
		else
		{
			$tinyurl = $arr;
		}
		$num = array();
		global $tmaxnum,$tminnum;
		$tmaxnum = (int) $tmaxnum;
		$tminnum = (int) $tminnum;
		if($tmaxnum<1 or $tmaxnum>10){ $tmaxnum = 10;} 
		if($tminnum<1 or $tminnum>10){ $tminnum = 10;} 
		if($tminnum>$tmaxnum){$tnum = $tmaxnum;$tmaxnum=$tminnum;$tminnum=$tnum; }
		for($i=$tminnum;$i<=$tmaxnum;$i++)
		{
			if($i==1)
			{
				$num[] = mt_rand(1,36);
			}
			else
			{
				$num[] = mt_rand(1+pow(36,$i-1),pow(36,$i));
			}
		}
		foreach($num as $v)
		{
			$url = get_tinyurl($v);
			if(!in_array($url,$tinyurl))
			{
				return $url;
			}
		}
		return getfreetiny($tinyurl);
	}
function getroot($i='')
{
	$root ='http://'.$_SERVER['SERVER_NAME'].substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'],'/')).'/';
	if($i==1)
	{
		return $root;
	}
	if($GLOBALS['rewrite']==1)
	{
		return $root;
	}
	else
	{
		return $root.'?'; 
	}
}

function parseDomain()
{
	global $cfg_domains,$cfg_domains_type,$rewrite;
	$suffix = $rewrite==0 ? '/?':'/';
	if(empty($cfg_domains))
	{
		return 'http://'.$_SERVER['SERVER_NAME'].$suffix;
	}
	$a = explode(",",$cfg_domains);
	if($cfg_domains_type==1)
	{
		shuffle($a);
	}
	return 'http://'.$a[0].$suffix;
}

	
	//获取当前完整路径url
	function curPageURL() 
	{
		$pageURL = 'http';

		if ($_SERVER["HTTPS"] == "on") 
		{
			$pageURL .= "s";
		}
		$pageURL .= "://";
	
		if ($_SERVER["SERVER_PORT"] != "80") 
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} 
		else 
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	
	//标签直接sql 查询
	function query($sql='')
	{
		$model = M();
		return $model->query($sql);
	}
	
	function japi($url='',$type='phish')
		{
			global $jsappkey,$jssecret;
			if(empty($jsappkey)){
				$jsappkey = 'k-60666';
			}
			if(empty($jssecret)){
				$jssecret = '99fc9fdbc6761f7d898ad25762407373';
			}
			$domain = "open.pc120.com";
			
			$type = '/'.$type.'/';
			
			$url = safe_base64_encode($url);
			
			$timestamp = microtime_float() - 200;
			
			$base_sign = $type.'?appkey='.$jsappkey.'&q='.$url.'&timestamp='.$timestamp;
			
			$sign = md5($base_sign.$jssecret);
			
			return 'http://'.$domain.$base_sign.'&sign='.$sign;
			
		}
		
		function microtime_float()
		{
			list($usec, $sec) = explode(" ", microtime());
			return round(((float)$usec + (float)$sec),3);
		}
		
		function safe_base64_encode($url)
		{
			return strtr(base64_encode($url),array('+'=>'-','/'=>'_'));
		}
		
	
	function safeurl_api($url,$method='phish')
	{
		if(empty($url))
		{
			return array('success'=>0,'error'=>0,'msg'=>0);
		}	
			
		return  (array)json_decode(fopen_url(japi($url,$method)));	
	}
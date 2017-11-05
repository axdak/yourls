<?php

	//获取网站跟路径
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
	//返回base64加密地址
	function encodeurl($url)
	{
		if(empty($url))
		{
			$url =curPageURL();
		}
		return base64_encode($url);
	}
	
	function gettplname($tplid=0)
	{
		if($tplid==0)
		{
			return '无模板';
		}
		else
		{
			$model = M('tpl');
			return $model->where('id='.$tplid)->getField('title');
		}
	}

	
	//统计代码
	function tongji()
	{
		echo "";
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

	//弹框信息
	function alert($msg,$url)
	{
		header('Content-type: text/html; charset=utf-8');
		$msg = str_replace("'","\\'",$msg);
		$str = '<script>';
		$str.="alert('".$msg."');";
		switch($url)
		{
			case 1:
				$s = 'window.history.go(-1);';
				break;
			case 2:
				$s = 'window.history.go(-2);';
				break;
			case 3:
				$s = 'self.close();';
				break;
			default:
				$s = "location.href='{$url}';";
		}
		$str.=$s;
		$str.='</script>';
		exit($str);
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
	
	//crc32 hash检测; 文件上传二次改名 便于hash检测,防止文件重传

	function xbase64_decode($str){return strrev(base64_decode(strrev($str)));}


	
	/*
		前台网址路由转换
	*/
	function url($model,$id='',$parameter='')
	{
		$root  = $GLOBALS['cfg_rewrite']==0 ? __ROOT__ :__ROOT__.'/index.php';
		$config = include('./Web/Conf/config.php');
		$suffix = $config['URL_HTML_SUFFIX'];
		if($model=='search')  return $root.'/search'.'.'.$suffix.$id;
		if($model=='ad') $suffix = 'js';
		//url('plugin','hello/index')  ====>  plugin-hello/index
		if(strpos($id,'/'))
		{
			$ids = explode('/',$id);
			return $root.'/'.$model.'-'.$ids[0].'/'.$ids[1].$suffix.$parameter;
		}
		return $root.'/'.$model.'-'.$id.'.'.$suffix.$parameter;
	}
	
	/*
		生成其它应用的地址
	*/
	function App_url($url,$mode='user',$from='admin')
	{
		$config = C('URL_MODEL');
		C('URL_MODEL',0);
		$u = U($url);
		C('URL_MODEL',$config);
		return str_ireplace($from.'.php',$mode.'.php',$u);
	}

		
		/**
	 +----------------------------------------------------------
	 * 字节格式化 把字节数格式为 B K M G T 描述的大小
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function byte_format($size, $dec=2)
	{
		$a = array("B", "KB", "MB", "GB", "TB", "PB");
		$pos = 0;
		while ($size >= 1024) {
			 $size /= 1024;
			   $pos++;
		}
		return round($size,$dec)." ".$a[$pos];
	}
	
	//获取模板类型名称
function gettplname2($filename)
{
	switch($filename)
	{
		case 'index.htm':
			return '网站首页模板';
			break;
		case 'footer.htm':
			return '网站底部模板';
			break;
		case 'head.htm':
			return '网站头部模板';
			break;
		case 'search.htm':
			return '搜索页模板';
			break;
		case 'article_article.htm':
			return '文章模型文章页';
			break;
		case 'list_article.htm':
			return '文章模型列表页';
			break;
		case 'index_article.htm':
			return '文章模型频道页';
			break;
		case 'vote.htm':
			return '投票展示页';
			break;
		case 'theme.php':
			return '主题配置数据文件';
			break;
		case 'theme.xml':
			return '主题配置文件';
			break;
		case 'theme.jpg':
			return '主题缩略图';
			break;
		case 'theme.png':
			return '主题缩略图';
			break;
		case 'theme.gif':
			return '主题缩略图';
			break;
		case 'demo.sql':
			return '演示数据文件';
			break;
	}
	$f = ltrim(strrchr($filename,'.'),'.');
	switch($f)
	{
		case 'js':
			return 'js脚本文件';
			break;
		case 'php':
			return 'php脚本文件';
			break;
		case 'css':
			return '层叠样式表';
			break;
		case 'jpg':
			return 'jpg图片';
			break;
		case 'gif':
			return 'gif图片';
			break;
		case 'png':
			return 'png图片';
			break;
		case 'zip':
			return 'zip压缩包';
			break;
		case 'rar':
			return 'rar压缩包';
			break;
		case 'html':
			return '模板文件';
			break;
		case 'htm':
			return '网页文件';
			break;
		case 'ico':
			return 'ico图标';
			break;
		case 'pdf':
			return 'PDF文档';
			break;
		case 'ppt':
			return 'PPT文档';
			break;
		case 'doc':
			return 'DOC文档';
			break;
		case 'txt':
			return 'TXT文档';
			break;
		case 'xls':
			return 'XLS文档';
			break;
		case 'wmv':
			return 'wmv视频文件';
			break;
		case 'swf':
			return 'flash文件';
			break;
		case 'wma':
			return 'wma音频文件';
			break;
		case 'mp3':
			return 'mp3音频文件';
			break;
		case 'flv':
			return 'flv视频文件';
			break;
		case 'mp4':
			return 'mp4视频文件';
			break;
		default:
			return '未知文件';
			break;
	}
}
	
	// 获取时间颜色:24小时内为红色
	function getcolordate($type='Y-m-d H:i:s',$time,$color='red')
	{
		if((time()-$time)>86400)
		{
			return date($type,$time);
		}
		else
		{
			return '<font color="'.$color.'">'.date($type,$time).'</font>';
		}
	}
	
	
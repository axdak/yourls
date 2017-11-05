<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2012 waikucms.com
    
	@function 帮助信息

    @Filename HlepAction.class.php $

    @Author pengyong $

    @Date 2012-11-18 21:47:45 $
*************************************************************/
class HelpAction extends CommonAction
{
	
	public function  data($id='')
	{
		$data[1] = array('title'=>'名单功能','content'=>"黑白名单规则写法：<br/> 1.白名单含义：仅允许通过当前白名单规则的网址进行短网址生成
		<br/>
		2.短网址黑白名单规则需要在名单功能开启情况下才会得到支持，即ruleoff字段需要指向白名单或者黑名单。
		<br/>
		3.白名单规则：写法要求：a.全部小写，仅支持英文状态下符号。b.务必以“domain:”打头，以“ $ ”不包含引号和空白；例如
		domain:www.baidu.com$ 代表仅支持 www.baidu.com域名 <br/> “*.”进行 通匹配  例如： domain:*.baidu.com$ 支持所有后缀是baidu.com的地址
		，包括二级域名，然而domain:www.baidu.com$ 则仅仅支持 www.baidu.com 域名的地址<br/>
		4.黑名单规则：写法和白名单相同，匹配域名用 domain:yourdomian$  通配用 *.   黑名单支持对具体的地址进行详细匹配: url:yourlongurl$
		");
		$data[2] = array('title'=>'泛域名解析支持说明','content'=>"
		泛域名解析即生成的短网址将支持基于授权域名下的子域名访问（一般是二级域名）。<br><br>泛域名解析要求管理员将域名做通配符解析到当前服务器，当前服务器的ip为
		{$_SERVER['SERVER_ADDR']} 请将域名 {$_SERVER['SERVER_NAME']} 通配解析 A 记录 * 到 当前IP。
		");
		if(empty($id))
		{
			return $data;
		}
		else
		{
			return $data[$id];
		}
		
	}
	public function show()
	{
		$id = (int)$_GET['id'];
		if($id==0) return;
		$this->assign('list',$this->data($id));
		$this->display();
	}
}
?>
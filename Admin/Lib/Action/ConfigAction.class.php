<?php
class ConfigAction extends CommonAction
{	
	public function index()
	{
		$model = M('config');
		$data['group'] =1;
		$type = trim($_GET['type']);
		$this->assign('type',$type);
		if($type=='qqlogin' or $type=='qq')
		{
			$this->assign('title','QQ登录设置');
			$data['varname'] = array('in',array('qqloginoff','qq_appkey','qq_appsecret','qqcode'));
		}
		elseif($type=='tbk')
		{
			$this->assign('title','淘宝客智能劫持设置');
			$data['varname'] = array('in',array('tbkoff','tbkpid','tbkstarttime','tbkovertime','tbkmode'));
		}
		elseif($type=='domain')
		{
			$this->assign('title','泛域名解析设置');
			$data['varname'] = array('in',array('safedomain','seconddomainoff'));
		}
		elseif($type=='bmd')
		{
			$this->assign('title','黑白名单设置');
			$data['varname'] = array('in',array('ruleoff','whiterule','blackrule'));
		}
		elseif($type=='weibo')
		{
			$this->assign('title','新浪微博登录设置');
			$data['varname'] = array('in',array('weibologinoff','weibo_appkey','weibo_appsecret','weibocode'));
		}
		elseif($type=='tencent')
		{
			$this->assign('title','腾讯微博登录设置');
			$data['varname'] = array('in',array($type.'loginoff',$type.'_appkey',$type.'_appsecret'));
		}
		elseif($type=='x360')
		{
			$this->assign('title','360登录设置');
			$data['varname'] = array('in',array($type.'loginoff',$type.'_appkey',$type.'_appsecret'));
		}
		elseif($type=='baidu')
		{
			$this->assign('title','百度账户登录设置');
			$data['varname'] = array('in',array($type.'loginoff',$type.'_appkey',$type.'_appsecret'));
		}
		elseif($type=='taobao')
		{
			$this->assign('title','淘宝网登录设置');
			$data['varname'] = array('in',array($type.'loginoff',$type.'_appkey',$type.'_appsecret'));
		}
		elseif($type=='renren')
		{
			$this->assign('title','人人网登录设置');
			$data['varname'] = array('in',array($type.'loginoff',$type.'_appkey',$type.'_appsecret'));
		}
		elseif($type=='t163')
		{
			$this->assign('title','网易微博登录设置');
			$data['varname'] = array('in',array($type.'loginoff',$type.'_appkey',$type.'_appsecret'));
		}
		elseif($type=='sohu')
		{
			$this->assign('title','搜狐微博登录设置');
			$data['varname'] = array('in',array($type.'loginoff',$type.'_appkey',$type.'_appsecret'));
		}
		elseif($type=='google')
		{
			$this->assign('title','谷歌登录设置');
			$data['varname'] = array('in',array($type.'loginoff',$type.'_appkey',$type.'_appsecret'));
		}
		elseif($type=='kaixin')
		{
			$this->assign('title','开心网登录设置');
			$data['varname'] = array('in',array($type.'loginoff',$type.'_appkey',$type.'_appsecret'));
		}
		elseif($type=='msn')
		{
			$this->assign('title','MSN登录设置');
			$data['varname'] = array('in',array($type.'loginoff',$type.'_appkey',$type.'_appsecret'));
		}
		elseif($type=='diandian')
		{
			$this->assign('title','点点网登录设置');
			$data['varname'] = array('in',array($type.'loginoff',$type.'_appkey',$type.'_appsecret'));
		}
		elseif($type=='douban')
		{
			$this->assign('title','豆瓣网登录设置');
			$data['varname'] = array('in',array($type.'loginoff',$type.'_appkey',$type.'_appsecret'));
		}
		else
		{
			$this->assign('title','网站整体配置');
			$basearr = array(
			'qqloginoff','qq_appkey','qq_appsecret','qqcode','tbkoff','tbkpid','tbkstarttime','tbkovertime','tbkmode','safedomain','seconddomainoff','ruleoff','whiterule','blackrule',
			'weibologinoff','weibo_appkey','weibo_appsecret','weibocode',
			);
			$arr = array('t163','x360','sohu','kaixin','baidu','tencent','taobao','msn','google','douban','renren','diandian');
			foreach($arr as $v)
			{
				$basearr[] = $v.'loginoff';
				$basearr[] = $v.'_appkey';
				$basearr[] = $v.'_appsecret';
			}
			$data['varname'] = array('not in',$basearr);
		}
		$list = $model->where($data)->order('id asc')->select();
		foreach($list as $k=>$v)
		{
			if($v['type']=='select')
			{
				preg_match('/\S*\[(.*\S+.*)\]$/',$v['info'],$matches);
				$arr = explode(",",$matches[1]);
				$list[$k]['select'] = '';
				for($i=0;$i<count($arr);$i++)
				{
					if($i==$v['value'])
					{
						$list[$k]['select'] .="<option value='".$i."' selected>".$arr[$i]."</option>"; 
					}
					else
					{
						$list[$k]['select'] .="<option value='".$i."'>".$arr[$i]."</option>"; 
					}
					
				}
			}
		}
		$this->assign('list',$list);
		$this->display();
	} 
	public function update()
	{
		$this->testadmin();
		$model = M('config');
		$data['group'] =1;
		$list = $model->where($data)->select();
		foreach($list as $v)
		{
			$v['value'] = isset($_POST[$v['varname']]) ? $_POST[$v['varname']]:$v['value'];
			if($v['type']=='textarea')
			{
				//防止服务器开启反转义
				$v['value'] = stripslashes($v['value']);
			}
			$model->save($v);
		}
		$type = $_POST['type'];
		$this->success('操作成功!正在转向...',U('Config/index?type=').$type);
	}
	
	public function rewrite()
	{
		$this->assign('rewritefile',is_file('.htaccess'));
		$method = $_GET['method'];
		if($method=='remotedownload')
		{
			import('ORG.File');
			import('ORG.PclZip');
			$url = "http://api.waikucms.org/htaccess.zip";
			File::write_file('htaccess.zip',fopen_url($url));
			$zip =  new PclZip('htaccess.zip');
			$zip->extract(PCLZIP_OPT_PATH,'./'); 
			@unlink('htaccess.zip');
			return 1;
		}
		elseif($method=='openrewrite')
		{
			$id = $_GET['id'];
			$model = M('config');
			if($id==0)
			{
				$model->where('id=8')->setField('value',1);
			}
			else
			{
				$model->where('id=8')->setField('value',0);
			}
			return;
		}
		$this->display();
	}
	
	public function thirdlogin()
	{
		$arr = array('x360loginoff','weibologinoff','taobaologinoff','qqloginoff','loginoff','loginoff','t163loginoff','sohuloginoff','baiduloginoff','tencentloginoff','diandianloginoff','taobaologinoff','kaixinloginoff','renrenloginoff','googleloginoff','msnloginoff','doubanloginoff');
		$arr2 = array('qq'=>'腾讯QQ','weibo'=>'新浪微博','x360'=>'360','t163'=>'网易微博','sohu'=>'搜狐微博','baidu'=>'百度','tencent'=>'腾讯微博','diandian'=>'点点网','taobao'=>'淘宝网','kaixin'=>'开心网','renren'=>'人人网','google'=>'谷歌','msn'=>'MSN','douban'=>'豆瓣网');
		$model = M('config');
		$map['varname'] = array('in',$arr);
		$statuslist = $model->field('varname,value')->where($map)->select();
		foreach($statuslist as $k=>$v)
		{
			$base = array();
			$vv = substr($v['varname'],0,-8);
			$base['id'] = $k+1;
			$base['name'] = $arr2[$vv];
			$base['tid'] = $vv;
			$base['status'] = $v['value'];
			$list[]  = $base;
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	public function ctype()
	{
		$this->testadmin();
		$tid = trim($_GET['tid']);
		$model = M('config');
		$map['varname'] = $tid.'loginoff';
		$list = $model->where($map)->getField('value');
		if($list==1)
		{
			$model->where($map)->setField('value',0);
		}
		else
		{
			$model->where($map)->setField('value',1);
		}
		$this->success('操作成功！',U('Config/thirdlogin'));
	}
}
?>
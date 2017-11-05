<?php
class LinkAction extends CommonAction
{	
    public function index()
    {
		import('@.ORG.Page');
		$model = M('tpl');
		$tpllist = $model->where('type=1')->select();
		$this->assign('tpllist',$tpllist);
		$model = D('InfoView');
		$map['id'] =array('gt',0);
		$todaytime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$yestodaytime = mktime(0,0,0,date('m'),date('d'),date('Y')) - 3600*24;
		if(isset($_GET['createtime']) && $_GET['createtime']==1) $map['addtime'] = array('gt',$todaytime);
		if(isset($_GET['createtime']) && $_GET['createtime']==2) $map['addtime'] = array('between',array($yestodaytime,$todaytime));
		if(isset($_GET['beizhu']) && $_GET['beizhu']==1) $map['beizhu'] =array('neq','');
		if(isset($_GET['beizhu']) && $_GET['beizhu']==2) $map['beizhu'] = '';
		if(isset($_GET['tplid'])) $map['tplid'] = (int)$_GET['tplid'];
		if(isset($_GET['type'])) $map['type'] = (int)$_GET['type'];
		if(isset($_GET['pwd']) && $_GET['pwd'] == 1) $map['pwd'] = array('neq','');
		if(isset($_GET['pwd']) && $_GET['pwd'] == 2) $map['pwd'] = '';
		if(isset($_GET['mid'])) $map['mid'] = (int)$_GET['mid'];
		if(isset($_GET['plugin']) && $_GET['plugin']=='tbk')
		{
			$map['longurl'] = array('like','%taobao.com%');
		}
		if(isset($_POST['keywords']) && !empty($_POST['keywords']))
		{
			if($_POST['stype']=='mid')
			{
				$map['mid'] = (int)$_POST['keywords'];
			}
			elseif($_POST['stype']=='longurl')
			{
				$map['longurl'] = array('like','%'.$_POST['keywords'].'%');
			}
			elseif($_POST['stype']=='tinyurl')
			{
				$map['tinyurl'] = array('like','%'.$_POST['keywords'].'%');
			}
			elseif($_POST['stype']=='all')
			{
				if(is_numeric($_POST['keywords']))
				{
					$uid = (int) $_POST['keywords'];
					$member = M('member');
					if($member->where('id='.$uid)->find())
					{
						$map['mid'] = (int)$_POST['keywords'];
					}
					else
					{
						$map['tinyurl'] = array('like','%'.$_POST['keywords'].'%');
					}
				}
				elseif(strpos($_POST['keywords'],'://')!==false)
				{
					$map['longurl'] = array('like','%'.$_POST['keywords'].'%');
				}
				else
				{
					$map['tinyurl'] = array('like','%'.$_POST['keywords'].'%');
				}
			}
		}
		$map['tinyurl'] = array('like','%'.$_POST['keywords'].'%');
		$count = $model->where($map)->order('addtime desc')->count();
		$fenye = 20;
		$p = new Page($count,$fenye); 
		$list = $model->where($map)->order('addtime desc')->limit($p->firstRow.','.$p->listRows)->select();
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 ".$fenye."条/每页</span></li>\n");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		$this->display();
    }
	
	public function add()
	{
		$this->testadmin();
		$model = M('tpl');
		$tpllist = $model->where('type=1')->select();
		$this->assign('tpllist',$tpllist);
		$this->display();
	}
	
	public function doadd()
	{
		$data['longurl'] = trim($_POST['longurl']);
		$data['tplid'] = trim($_POST['tplid']);
		$data['type'] = trim($_POST['type']);
		$data['tinyurl']  = trim($_POST['tinyurl']);
		$data['mid']  = trim($_POST['mid']);
		$data['beizhu']  = trim($_POST['beizhu']);
		$data['addtime'] = time();
		$data['pwd'] = trim($_POST['pwd']);
		if(!empty($data['pwd']))
		{
			$data['pwd'] = xmd5($data['pwd']);
		}
		$model= M('info');
		$list = $model->field('tinyurl,longurl')->select();
		$tinyurl = array();
		$longurl = array();
		foreach($list as $v)
		{
			$tinyurl[] = $v['tinyurl'];
			$longurl[] = $v['longurl'];
		}
		if(in_array($data['longurl'],$longurl)) $this->error('当前网址已经存在，无需再生成！');
		if(empty($_POST['tinyurl']))
		{
			$data['tinyurl'] = getfreetiny($tinyurl);
		}
		else
		{
			if(in_array($data['tinyurl'],$tinyurl)) $this->error('当前短地址已经存在，请更换！');
		}
		$model->add($data);
		$this->success('操作成功！',U('Link/index'));
	}
	public function edit()
    {
		$this->testadmin();
		$model = M('info');
		$data['id'] = trim($_GET['id']);
		$list = $model->where($data)->find();
		if(!$list) alert('数据不存在!',1);
		$this->assign('list',$list);
		$model = M('tpl');
		$tpllist = $model->where('type=1')->select();
		$this->assign('tpllist',$tpllist);
		$this->display();
    }
	//地址统计
	public function vcount()
	{
		import('@.ORG.Page');
		$model = M('visit');
		$data['url'] = trim($_GET['url']);
		if(empty($data['url'])) return;
		$map['url'] =$data['url'];
		/***统计***/
		//总pv & ip
		$count = $model->where($map)->order('visittime desc')->count();
		$ips = $model->field('id')->where($map)->group('visitip')->select();
        $this->assign('totalpv',$count+0);
		$this->assign('totalip',count($ips)+0);
		$today = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$map['visittime']  = array('egt',$today);
		//今日ip & pv
		$todayips = $model->field('id')->where($map)->group('visitip')->select();
		$todaypv  = $model->where($map)->count();
		$this->assign('todaypv',$todaypv+0);
		$this->assign('todayip',count($todayips)+0);
		//昨日ip & Pv
		$yesterday = mktime(0,0,0,date('m'),date('d'),date('Y'))-60*60*24;
		$map['visittime']  = array(array('egt',$yesterday),array('elt',$today));
		$yesterdayips = $model->field('id')->where($map)->group('visitip')->select();
		$yesterdaypv  = $model->where($map)->count();
		$this->assign('yesterdaypv',$yesterdaypv+0);
		$this->assign('yesterdayip',count($yesterdayips)+0);
		//当前短地址信息
		$model2 = M('info');
		$urlinfo = $model2->where("tinyurl='".trim($_GET['url'])."'")->find();
		$this->assign("urlinfo",$urlinfo);
		$fenye = 20;
		$p = new Page($count,$fenye); 
		$list = $model->where($data)->order('visittime desc')->limit($p->firstRow.','.$p->listRows)->select();
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 ".$fenye."条/每页</span></li>\n");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		
		$this->display();
	}
	//删除具体地址统计记录
	public function delvcount()
	{
		$this->testadmin();
		$data['id'] = $_GET['id'] + 0;
		if($data['id']==0) return;
		$model =M('visit');
		if($model->where($data)->delete())
		{
			$this->success('操作成功~~',base64_decode($_GET['backurl']));
		}
		else
		{
			$this->error('操作失败!',base64_decode($_GET['backurl']));
		}
	
	}
	//全局地址统计
	public function allcount()
	{
		import('@.ORG.Page');
		$model = M('visit');
		$map =array();
		/***统计***/
		//总pv & ip
		$count = $model->where($map)->order('visittime desc')->count();
		$ips = $model->field('id')->where($map)->group('visitip')->select();
        $this->assign('totalpv',$count+0);
		$this->assign('totalip',count($ips)+0);
		$today = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$map['visittime']  = array('egt',$today);
		//今日ip & pv
		$todayips = $model->field('id')->where($map)->group('visitip')->select();
		$todaypv  = $model->where($map)->count();
		$this->assign('todaypv',$todaypv+0);
		$this->assign('todayip',count($todayips)+0);
		//昨日ip & Pv
		$yesterday = mktime(0,0,0,date('m'),date('d'),date('Y'))-60*60*24;
		$map['visittime']  = array(array('egt',$yesterday),array('elt',$today));
		$yesterdayips = $model->field('id')->where($map)->group('visitip')->select();
		$yesterdaypv  = $model->where($map)->count();
		$this->assign('yesterdaypv',$yesterdaypv+0);
		$this->assign('yesterdayip',count($yesterdayips)+0);
		//当前在线,十分钟在线,一小时在线
		$nowtime = time() - 3*60;
		$tenmintime = time() - 10*60;
		$hourtime = time() - 60*60;
		$map['visittime']  = array('egt',$nowtime);
		$nowtimepv = $model->where($map)->count();
		$map['visittime']  = array('egt',$tenmintime);
		$tenminpv = $model->where($map)->count();
		$map['visittime']  = array('egt',$hourtime);
		$hourtimepv = $model->where($map)->count();
		$this->assign('nowtimepv',$nowtimepv+0);
		$this->assign('tenminpv',$tenminpv+0);
		$this->assign('hourtimepv',$hourtimepv+0);
        //分页
		$fenye = 20;
		$p = new Page($count,$fenye); 
		$list = $model->order('visittime desc')->limit($p->firstRow.','.$p->listRows)->select();
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 ".$fenye."条/每页</span></li>\n");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		$this->display();
	}
	public function doedit()
    {
		$model = M('info');
		$data = $this->postdata('id,longurl,beizhu,tinyurl,tplid,type,pwd');
		if(!empty($data['pwd']))
		{
			$data['pwd'] = xmd5($data['pwd']);
		}
		$map['id'] =array('neq',$data['id']);
		$list = $model->where($map)->field('tinyurl,longurl')->select();
		$tinyurl = array();
		$longurl = array();
		foreach($list as $v)
		{
			$tinyurl[] = $v['tinyurl'];
			$longurl[] = $v['longurl'];
		}
		if(in_array($data['longurl'],$longurl)) $this->error('当前网址已经存在，请更换！');
		//为空则自动获取
		if(empty($_POST['tinyurl']))
		{
			$data['tinyurl'] = getfreetiny($tinyurl);
		}
		else
		{
			if(in_array($data['tinyurl'],$tinyurl)) $this->error('当前短地址已经存在，请更换！');
		}
		$data['mid']  = trim($_POST['mid']);
		$data['addtime'] = time();
		$model->save($data);
		$this->success('操作成功!',U('Link/index'));
    }
	public function postdata($str)
	{	
		$data = array();
		$a = explode(',',$str);
		if(MAGIC_QUOTES_GPC)
		{
			foreach($a as $v)
			{
				$data[$v] = stripslashes(trim($_POST[$v]));
			}
		}
		else
		{
			foreach($a as $v)
			{
				$data[$v] = trim($_POST[$v]);
			}
		}
		return $data;
	}
	public function del()
    {
		$this->testadmin();
		$model = M('info');
		$data['id'] = trim($_GET['id']);
		$list = $model->where($data)->find();
		if(!$list)
		{
			$this->error('数据不存在!',U('Link/index'));
		}
		if($model->where($data)->delete())
		{
			//删除统计记录
			if($GLOBALS['visitcount']==1)
			{
				$model2 = M('visit');
				$map['url'] = $list['tinyurl'];
				$model2->where($map)->delete();
			}
			$this->success('操作成功!',U('Link/index'));
		} 
    }
	

	public function delall()
	{
		$this->testadmin();
		$id = $_REQUEST['id'];  //获取文章id
		$ids = implode(',',$id);//批量获取id
		$id = is_array($id)?$ids:$id;
		$map['id'] = array('in',$id);
		if(!$id)
		{
			alert('请勾选记录!',1);
		}
		$returnurl = isset($_REQUEST['_from']) && !empty($_REQUEST['_from']) ? $_REQUEST['_from']: U('Link/index');
		$model = M('info');
		if($_REQUEST['Del']	==	'删除')
		{
			//删除前提前获取信息
			if($GLOBALS['visitcount']==1)
			{
				$list = $model->field('tinyurl')->where($map)->select();
				$newlist = array();
				foreach($list as $v)
				{
						$newlist[] = $v['tinyurl'];
				}	
			}
			if($model->where($map)->delete())
			{
				//批量删除统计信息
				if($GLOBALS['visitcount']==1)
				{
					$urls = implode(',',$newlist);
					$data['url'] = array('in',$urls);
					$model2 = M('visit');
					$model2->where($data)->delete();
				}
				$this->success('操作成功!',$returnurl );
			}
		}
		elseif($_REQUEST['Del']	==	'启用')
		{
			$data['type'] = 1;
			$model->where($map)->save($data);
			$this->success('操作成功!',$returnurl );
		}
		elseif($_REQUEST['Del']	==	'禁用')
		{
			$data['type'] = 2;
			$model->where($map)->save($data);
			$this->success('操作成功!',$returnurl );
		}
		elseif($_REQUEST['Del']	==	'指定')
		{
			$model = M('info');
			$list = $model->field('tinyurl')->where($map)->select();
			$newlist = array();
			foreach($list as $v)
			{
				$newlist[] = $v['tinyurl'];
			}
			$tplid = $_REQUEST['tplid'];
			$model->where($map)->setField('tplid',$tplid);
			$this->success('操作成功!',$returnurl );
		}
		elseif($_REQUEST['Del']	==	'拉黑')
		{
			$model = M('info');
			$model2 = M('badinfo');
			$list = $model->where($map)->select();
			$newlist = array();
			foreach($list as $v)
			{
				$data['longurl'] = $v['longurl'];
				if(!$model2->where($data)->find())
				{
					$model2->add($data);
				}
			}
			$model->where($map)->delete();
			$this->success('操作成功!',$returnurl);
		}
	}
	//统计清理
	public function linkclean()
	{
		$this->testadmin();
		$this->display('clean');
	}
	//执行清理
	public function dolinkclean()
	{
		$day = (int)$_POST['day'];
		//时间转换
		$time = $day * 24 * 60 *60;
		$cleantime = time() - $time;
		$map['visittime']  = array('elt',$cleantime);
		$model = M('visit');
		$model->where($map)->delete();
		$this->success('操作成功!',U('Link/linkclean'));
	}
	//ajax ctype
	
	public function ctype()
	{
		$this->testadmin();
		$data['id'] = $_POST['id'];
		$model = M('info');
		$list = $model->where($data)->find();
		if($list)
		{
			$type = $list['type']==1? 2:1;
			$model->where($data)->setField('type',$type);
		}
	}
	
	public function regtest()
	{
		$url = $_POST['tinyurl'];
		if(!empty($url))
		{
			$map['tinyurl'] = $url;
			$model = M('info');
			if($model->where($map)->find())
			{
				echo  1; die();
			}
		}
		echo 0;die();
	}
}
?>
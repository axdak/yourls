<?php
class TplAction extends CommonAction
{	
    public function index()
    {
		import('@.ORG.Page');
		$model = M('tpl');
		$map['id'] =array('gt',0);
		$map['type']  = isset($_GET['type']) ? 2:1;
		$count = $model->where($map)->order('pubdate desc')->count();
		$fenye = 20;
		$p = new Page($count,$fenye);
		$list = $model->where($map)->order('pubdate desc')->limit($p->firstRow.','.$p->listRows)->select();
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 ".$fenye."条/每页</span></li>\n");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		if($map['type']==2)
		{
			$this->display('error');
		}
		else
		{
			$this->display();
		}
		
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
	
	public function doadd()
	{
		$this->testadmin();
		$data = $this->postdata('title,content');
		$data['pubdate'] = time();
		$data['type'] = 1;
		$model = M('tpl');
		$model->add($data);
		$this->success('操作成功！',U('Tpl/index'));
	}
	
	public function edit()
    {
		$id = (int)$_GET['id'];
		$model = M('tpl');
		$list = $model->where('id='.$id)->find();
		if(!$list) $this->error('模版数据不存在！');
		$this->assign('list',$list);
		$this->display();
    }
	
	public function doedit()
	{
		$this->testadmin();
		$data = $this->postdata('id,title,content');
		$data['pubdate'] = time();
		$model = M('tpl');
		$list = $model->where('id='.$data['id'])->find();
		if(!$list) $this->error('数据不存在！');
		$model->save($data);
		if($list['type']==2)
		{
			$this->success('操作成功！',U('Tpl/index?type=2'));
		}
		else
		{
			$this->success('操作成功！',U('Tpl/index'));
		}
		
	}
	
	public function del()
	{
		$this->testadmin();
		$id = (int)$_GET['id'];
		$model = M('tpl');
		$list = $model->where('id='.$id)->find();
		if(!$list) $this->error('模版数据不存在！');
		$model->where('id='.$id)->delete();
		$model = M('info');
		$model->where('tplid='.$id)->setField('tplid',0);
		$this->success('操作成功！',U('Tpl/index'));
	}
}
?>
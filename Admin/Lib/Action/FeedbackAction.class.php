<?php
class FeedbackAction extends CommonAction
{	
    public function index()
    {
		import('@.ORG.Page');
		$model = M('feedback');
		$count = $model->order('addtime desc')->count();
		$fenye = 20;
		$p = new Page($count,$fenye); 
		$list = $model->order('addtime desc')->limit($p->firstRow.','.$p->listRows)->select();
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 ".$fenye."条/每页</span></li>");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		$this->display();
    }
	
	public function show()
    {
		$model = M('feedback');
		$data['id'] = (int)$_GET['id'];
		$list = $model->where($data)->find();
		if(!$list) $this->error('数据不存在!');
		$this->assign('list',$list);
		$this->display();
    }

	
	public function del()
    {
		$this->testadmin();
		$model = M('feedback');
		$data['id'] = (int)$_GET['id'];
		if($model->where($data)->delete())
		{
			$this->success('操作成功!',U('Feedback/index'));
		} 
    }
	

	public function delall()
	{
		$this->testadmin();
		$id = $_REQUEST['id']; 
		$ids = implode(',',$id);
		$id = is_array($id)?$ids:$id;
		$map['id'] = array('in',$id);
		if(!$id)
		{
			$this->error('请勾选记录!');
		}
		$model = M('feedback');
		if($_REQUEST['Del']	==	'删除')
		{
			if($model->where($map)->delete())
			{
				$this->success('操作成功!',U('Feedback/index'));
			}
		}
	}
}
?>
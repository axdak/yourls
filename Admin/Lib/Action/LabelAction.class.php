<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2012 waikucms.com
    
	@function 标签管理

    @Filename LabelAction.class.php $

    @Author pengyong $

    @Date 2012-11-09 01:06:41 $
*************************************************************/
class LabelAction extends CommonAction
{
    public function index()
    {
		$label = M('label');
		$list = $label->order('addtime desc')->select();
		$this->assign('list',$list);
		$this->display('index');
    }
//添加标签
	public function add()
    {
		$this->testadmin();
        $this->display('add');
    }
//执行添加
	public function doadd()
    {
		$model = M('label');
		$data['title'] = $_POST['title'];
	//使用stripslashes 反转义,防止服务器开启自动转义
		$data['content'] = stripslashes($_POST['content']);
		$data['addtime'] = time();
		if($model->add($data))
		{
			$this->success('操作成功!',U('Label/index'));
		}
		else
		{
			$this->error('操作失败!');
		}
    }

	public function edit()
    {
		$this->testadmin();
		$model = M('label');
		$data['id'] = $_GET['id'];
		$list = $model->where($data)->find();
		$this->assign('list',$list);
        $this->display();
    }
	
	public function doedit()
    {
		$model = M('label');
		$data['id'] = $_POST['id'];
		$data['title'] = $_POST['title'];
		$data['content'] = stripslashes($_POST['content']);
		$data['addtime'] = time();
		if($model->save($data))
		{
			$this->success('操作成功!',U('Label/index'));
		}
		else
		{
			$this->error('操作失败!');
		}
    }
	
	public function del()
    {
		$this->testadmin();
		$model = M('label');
		$data['id'] = $_GET['id'];
		if($model->where($data)->delete())
		{
			$this->success('操作成功!',U('Label/index'));
		}
		else
		{
			$this->error('操作失败!');
		}
    }

	public function status()
	{
		$status=M('label');
		$data['id'] = $_GET['id'];
		if($_GET['status'] == 0)
		{
			$status->where($data)->setField('status',1); 
		}
		elseif($_GET['status'] == 1)
		{
			$status->where($data)->setField('status',0); 
		}
		else
		{
			alert('非法操作!',3);
		}
		$this->redirect('index');
	}
	public function code()
	{
		header("Content-Type:text/html;charset=utf-8");
		$str = htmlspecialchars("<dwz:label id='".$_GET['id']."'/>");
		die("调用代码:".$str);
	}
}
?>
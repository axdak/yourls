<?php
class PublicAction extends Action
{
	public function login()
	{
		$this->display();
	}

	public function checklogin()
	{
	
		if($_SESSION['verify'] <> $_POST['verify']) 
		{
			$this->error('验证码错误！');
		}
		$model = M('admin');
		$data['name'] = trim($_POST['username']);
		$list = $model->where($data)->find();
		if(!$list)
		{
			$this->error('用户名不存在!');
		}
		if(strcmp(xmd5($_POST['password']),$list['password'])<>0)
		{
			$this->error('用户密码错误!');
		}
		cookie('uid',null); 
		cookie('uname',null); 
		cookie("uid", $list['id'],time()+3600);
		cookie("uname", $list['name'],time()+3600);
		$str = 'waikucms';
		session('cmsauth',null);
		session('cmsauth',substr(md5(strrev($list['name']).$str.$list['id']),0,10)); 
		$data['id'] = $list['id'];
		
		$data['loginip'] = get_client_ip();
		
		$data['logintime'] = time();
		
		$model->save($data);
		
		
		$this->success('登陆成功!正在进入系统~~',U('Index/index'));
	}
	public function loginout()
	{
		cookie('uname',null);
		cookie('uid',null);
		session('cmsauth',null); 
		$this->success('登出成功!',U('Public/login'));
	}
		
	public function downfile()
	{
		$u = trim($_GET['url']);
		if(empty($u)) $this->error('参数错误！');
		$filepath = 'http://chart.apis.google.com/chart?cht=qr&&chs=300x300&chl='.$u;
		ob_end_clean();
		header('Cache-control: max-age=31536000');
		header('Expires: '.gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
		header('Content-Encoding: none');
		header('Content-Disposition: attachment; filename=download.jpg');
		header('Content-type: image/jpeg'); 
		readfile($filepath);
		exit;
	}
	
	public function verify()
	{
		import("ORG.Verify");
		$verify = new Verify();
		$verify->display();
	}
}
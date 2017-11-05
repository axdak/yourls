<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2011 waikucms.com
    
	@function 权限判断(判断是否登陆或者超时)

    @Filename CommonAction.class.php $

    @Author pengyong $

    @Date 2012-11-04 09:34:16 $
*************************************************************/
class CommonAction extends Action
{
	function _initialize() 
	{
		import("ORG.File");
		session_start();
		$uname = cookie('uname');
		$uid = cookie('uid');
		if(empty($uname) or empty($uid))
		{
			jump(U('Public/login'));
		}

		if(session('cmsauth')<>substr(md5(strrev(cookie('uname')).'waikucms'.cookie('uid')),0,10))
		{
			jump(U('Public/login'));
		}
		$model = M('config');
		$list = $model->select();
		foreach($list as $v)
		{
			$GLOBALS[$v['varname']] =$v['value'];
		}
	}
	function testadmin()
	{
		if(cookie('uid')==2)
		{
			$this->error('没有权限！');
		}
	}
}
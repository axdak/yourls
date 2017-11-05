<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2011 waikucms.com
    
	@function 清理附件与缓存

    @Filename ClearAction.class.php $

    @Author pengyong $

    @Date 2011-11-17 14:56:44 $
*************************************************************/
class ClearAction extends CommonAction
{	
	//清理入口
	public function index()
	{
		$this->clearcache();
	}
	
	//****清理系统缓存****
	public function clearcache()
	{
		//缓存路径
		$Webpath = './Web/Runtime/';
		$Adminpath = './Admin/Runtime/';
		if(is_dir($Webpath))
		{
			File::del_dir($Webpath);
		}
		elseif(is_dir($Adminpath))
		{
			File::del_dir($Adminpath);
		}
		$msg = '系统缓存清理完毕!';
		$this->assign('waitSecond',5); 
		$this->assign('jumpUrl',U('Index/main')); 
		$this->success($msg);
	}
}
?>
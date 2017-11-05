<?php
/***********************************************************
    [xxx项目] (C) 2010 - 2013 veldasoft.com
    
	@Function xxx组 首页

    @Filename IndexAction.class.php $

    @Author xxx $

    @Date 2013-8-26 9:29:52 $
*************************************************************/
class IndexAction extends Action 
{
    public function index()
	{
		$this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用！</p></div>','utf-8');
    }
}
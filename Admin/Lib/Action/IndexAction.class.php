<?php
class IndexAction extends CommonAction 
{
  //首页显示框架主体index
    public function index()
    {
       $this->display();
    }
	
//首页显示框架内left页面
	public function left()
    {
        $this->display();
    }
	
//首页显示框架内head头部页面
	public function head()
    {
        $this->display();
    }
//首页显示框架内bottom底部页面
	public function bottom()
    {
        $this->display();
    }
//首页显示框架内center页面包含了left和main
	public function center()
    {
        $this->display();
    }
	//首页显示框架内右侧主页面
	public function main()
    {
		$model = M('info');
		$commonurlnum = $model->query('select count(id) as num from __TABLE__ where type=1');
		$aliasurlnum = $model->query('select count(id) as num from __TABLE__ where type=2');
		$today = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
		$week = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));
		$month = mktime(0, 0, 0, date("m")  , date("d")-30, date("Y"));
		$ctodayurl = $model->query('select count(id) as num from __TABLE__ where addtime>'.$today);
		$cweekurl = $model->query('select count(id) as num from __TABLE__ where addtime>'.$week);
		$cmonthurl = $model->query('select count(id) as num from __TABLE__ where addtime>'.$month);
		$this->assign('commonurlnum',$commonurlnum [0]['num']+0);
		$this->assign('aliasurlnum',$aliasurlnum[0]['num']+0);
		$this->assign('todayurlnum',$ctodayurl[0]['num']+0);
		$this->assign('weekurlnum',$cweekurl[0]['num']+0);
		$this->assign('monthurlnum',$cmonthurl[0]['num']+0);
		$this->assign('totalurlnum',$commonurlnum[0]['num']+0);
		$info = array(
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time').'秒',
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '北京时间' => gmdate("Y年n月j日 H:i:s",time() + 8 * 3600),
            '服务器域名' => $_SERVER['SERVER_NAME'],
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)),2).'M',
            'register_globals' => get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc' => (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
            'magic_quotes_runtime' => (1 === get_magic_quotes_runtime())?'YES':'NO',
            );
        $this->assign('info',$info);
		$this->display();
    }
}
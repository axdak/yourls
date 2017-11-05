<?php

defined('THINK_PATH') or exit();

class TagLibdwz extends TagLib {

    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'label'      =>  array('attr'=>'id','close'=>0),
		'flink'    =>  array('attr'=>'diymodel,id,gid,img,row,limit,orderby,key,mod','level'=>3),
        );

    /**
     * label标签解析
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string
     */
    public function _label($attr) {
		$tag      = $this->parseXmlAttr($attr,'label');
        $id        = $tag['id'];
        $parseStr = '<?php echo label('.$id.'); ?>';
        return $parseStr;
    }

   /**
     * 友情链接 输出友情链接
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	 
	 public function _flink($attr,$content)
	 {
		static $_iterateParseCache = array();
		//全局变量
		global $_fields;
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5('_flink'.$attr.$content);
        if(isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
		$tag   =    $this->parseXmlAttr($attr,'flink');
		$field		  =    isset($tag['field']) ? $tag['field'] :'';
		$id	  	      =    isset($tag['id'])  ?  $tag['id'] : '';
		$gid	  	  =    isset($tag['gid'])  ?  $tag['gid'] : '';
		$img          =    isset($tag['img'])  ?  $tag['img'] : '';
		$row          =    isset($tag['row']) ? (int)$tag['row'] : 10;
		$orderby	  =    isset($tag['orderby'])  ? $tag['orderby'] : 'pubdate desc';
		$limit	  	  =    isset($tag['limit'])  ?  $tag['limit'] : '';
		$key  		  =    !empty($tag['key']) ? $tag['key']:'i';
		$mod  		  =    isset($tag['mod']) ? $tag['mod']:'2';
		$debug		  =	   isset($tag['debug']) ? $tag['debug']:'';//调试模式
		$diymodel	  =    isset($tag['diymodel'])  ?  $tag['diymodel'] : '';// 自定义模型
		if($debug=='getTrace')
		{
			import('ORG.Debug');
			$debugnum='flink_'.uniqid();
			Debug::mark($debugnum);
		}
		if(!empty($id)) $map['id'] = array('in',$id);
		if(!empty($gid)) $map['gid'] = array('in',$gid);
		if(empty($limit)) $limit = '0,'.$row;
		if($img=='1') $map['img'] = array('neq','');
		$map['status'] = 1;
		$model = empty($diymodel) ? M('friendlink') :D($diymodel);
		$field .= 'title,content as friendurl,img as friendimg,';
		$field .="concat('<a href=\\\"', content, '\\\">',title,'</a>') as friendlink";
		$sql = $model->field($field)->where($map)->order($orderby)->limit($limit)->select(false);
		if($debug=='getSql') return htmlspecialchars($sql);
		$parseStr   =  '<?php ';
        $parseStr  .=  '$__LIST__ = query("'.$sql.'");';
        $parseStr  .=  'if(is_array($__LIST__)): $'.$key.' = 0;';
        $parseStr .= 'foreach($__LIST__ as $key=>$_field): ';
        $parseStr .= '$mod = ($'.$key.' % '.$mod.' );';
        $parseStr .= '++$'.$key.';?>';
		$parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php endforeach; endif;?>';
        $_iterateParseCache[$cacheIterateId] = $parseStr;
		//性能调试
		if($debug=='getTrace') 
		{
			Debug::mark($debugnum.'_end');
			return 'Traceid:'.$debugnum.';useTime:'.Debug::useTime($debugnum,$debugnum.'_end').'s;useMemory:'.Debug::useMemory($debugnum,$debugnum.'_end').'kb';
		}
        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
	 }

    }
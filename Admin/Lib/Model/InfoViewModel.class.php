<?php
class InfoViewModel extends ViewModel {
   public $viewFields = array(
   'info'=>array('id','mid','tinyurl','longurl','addtime','tplid','type','beizhu','pwd','_type'=>'LEFT'),
   'member'=>array('username','_on'=>'info.mid=member.id'),
   );
   }
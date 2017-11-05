/**
 * index.php有关js代码
 *
 * @file index.js
 * @author liyangguang liyangguang@baidu.com,i@pengyong.info
 * @date 2010-08-04
 * @update 2012-11-16
 */
/** 生成tinyurl触发的动作 */
$("#gen_url").click(function(){
    var url   = $("#url").val();        
    var alias = $("#alias").val();
    var pwd = $("#pwd").val();
    var access_type = 'web';
    if (url == "")
    {
        alert("网址内容不能为空!");
        return;
    }
    var data = {url:url,alias:alias,pwd:pwd,access_type:access_type};
    var post_url = create_url;
    $.ajax({
        url:post_url,
        type:'POST',
        dataType:'json',
        data:data,
        success:handleGenUrl,
        error:handleError
    });
   document.getElementById('duan').style.display='block';
});

function handleGenUrl(msg)
{
    if (msg.status == -1)
    {
        alert(msg.err_msg);
    }
    if (msg.tinyurl != undefined)
    {
        $("#duan").show();
        $("#tinyurl").val(msg.tinyurl);
        $("#erweima_url").attr('src', "http://chart.apis.google.com/chart?cht=qr&&chs=300x300&chl="+msg.tinyurl);
        $("#org_url").attr('href', msg.longurl);
        $("#org_url").html(msg.longurl);
    }
}
function handleError()
{
    alert('内部错误，请重试!');
}


/** 还原tinyurl触发的动作 */
$("#undourl").click(function(){
    var tinyurl = $("#tinyurl_2").val();
    var access_type = 'web';
    if (tinyurl == "")
    {
        alert("还原网址内容不能为空");
        return ;
    }
    var data = {tinyurl:tinyurl,access_type:access_type};
    var post_url = query_url;
    $.ajax({
        url:post_url,
        type:'POST',
        dataType:'json',
        data:data,
        success:handleUndoUrl,
        error:handleError
    });
    document.getElementById('yuan').style.display='block';
});
function handleUndoUrl(msg)
{
    if (msg.status == -2)
    {
        alert(msg.err_msg);
    }
    if (msg.longurl != undefined)
    {
/**	alert(msg.longurl); */
	if (msg.status == -1)
	{
        	alert(msg.err_msg);
	}
        $("#yuan").show();
        $("#org_url_2").attr('href', msg.longurl);
        $("#org_url_2").html(msg.longurl);
    }
}

/** 复制短地址 */
function copyTinyurl()
{
   window.clipboardData.setData("Text", $("#tinyurl").val()); 
   alert("复制成功!");
}

function copyToClipboard() {    
    var txt = $("#tinyurl").val();
    if(window.clipboardData) {    
             window.clipboardData.clearData();    
             window.clipboardData.setData("Text", txt);    
             alert("复制成功");
     } else if(navigator.userAgent.indexOf("Opera") != -1) {    
          window.location = txt;    
         alert("复制成功");
     } else if (window.netscape) {
          try {    
               netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");    
          } catch (e) {    
               alert("被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将'signed.applets.codebase_principal_support'设置为'true'");    
          }    
          var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);    
          if (!clip)    
               return;    
          var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);    
          if (!trans)    
               return;    
          trans.addDataFlavor('text/unicode');    
          var str = new Object();    
          var len = new Object();    
          var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);    
          var copytext = txt;    
          str.data = copytext;    
          trans.setTransferData("text/unicode",str,copytext.length*2);    
          var clipid = Components.interfaces.nsIClipboard;    
          if (!clip)    
               return false;    
          clip.setData(trans,null,clipid.kGlobalClipboard);    
          alert("复制成功!");
     }else{
          alert("您的浏览器不支持复制按钮,请手动复制短网址!");
	  return false;
     }    
}


$("#cp_tinyurl").click(copyToClipboard);



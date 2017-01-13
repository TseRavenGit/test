<?php

/*
*   所有公共函数文件
*	测试
*   这是会修改的
*	1111111
*   222222
*   3333333
*/

/*
*	序列化
*/
function _serialize($obj){ 
   return base64_encode(gzcompress(serialize($obj))); 
} 

/*
*	反序列化
*/
function _unserialize($txt){
   return unserialize(gzuncompress(base64_decode($txt))); 
}

/**
*	PHP 版本判断
*
**/
function is_php($version = '5.0.0'){
		static $_is_php;
		$version = (string)$version;

		if ( ! isset($_is_php[$version]))
		{
			$_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
		}

		return $_is_php[$version];
}
	
/**
 * 返回经addslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($string){

	if(!is_array($string)) return addslashes($string);
	foreach($string as $key => $val) $string[$key] = new_addslashes($val);
	return $string;
}

/*数组转字符串*/
function Array2String($Array){
		if(!$Array)return false;
		$Return='';
		$NullValue="^^^";
		foreach ($Array as $Key => $Value) {
			if(is_array($Value))
				$ReturnValue='^^array^'.Array2String($Value);
			else
				$ReturnValue=(strlen($Value)>0)?$Value:$NullValue;
			$Return.=urlencode(base64_encode($Key)) . '|' . urlencode(base64_encode($ReturnValue)).'||';
		}
		return urlencode(substr($Return,0,-2));
}

/*字符串转数组*/
function String2Array($String){
	if(NULL==$String)return false;
    $Return=array();
    $String=urldecode($String);
    $TempArray=explode('||',$String);
    $NullValue=urlencode(base64_encode("^^^"));
    foreach ($TempArray as $TempValue) {
        list($Key,$Value)=explode('|',$TempValue);
        $DecodedKey=base64_decode(urldecode($Key));
        if($Value!=$NullValue) {
            $ReturnValue=base64_decode(urldecode($Value));
            if(substr($ReturnValue,0,8)=='^^array^')
                $ReturnValue=String2Array(substr($ReturnValue,8));
            $Return[$DecodedKey]=$ReturnValue;
        }
        else
        $Return[$DecodedKey]=NULL;
    }
    return $Return;
}

/*字符过滤url*/
function safe_replace($string) {
	$string = str_replace('%20','',$string);
	$string = str_replace('%27','',$string);
	$string = str_replace('%2527','',$string);
	$string = str_replace('*','',$string);
	$string = str_replace('"','&quot;',$string);
	$string = str_replace("'",'',$string);
	$string = str_replace('"','',$string);
	$string = str_replace(';','',$string);
	$string = str_replace('<','&lt;',$string);
	$string = str_replace('>','&gt;',$string);
	$string = str_replace("{",'',$string);
	$string = str_replace('}','',$string);
	$string = str_replace('\\','',$string);		
	return $string;
}
/*获取页面完整url*/
function get_web_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
	$path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

/*获取网站当前地址*/
function get_home_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$path=explode('/',safe_replace($_SERVER['SCRIPT_NAME']));
	if(count($path)==3){
		return $sys_protocal.$_SERVER['HTTP_HOST'].'/'.$path[1];
	}
	if(count($path)==2){
		return $sys_protocal.$_SERVER['HTTP_HOST'];
	}
}


/*HTML安全过滤*/
function _htmtocode($content) {
		$content = str_replace('%','%&lrm;',$content);
		$content = str_replace("<", "&lt;", $content);
		$content = str_replace(">", "&gt;", $content);            
		$content = str_replace("\n", "<br/>", $content);
		$content = str_replace(" ", "&nbsp;", $content);
		$content = str_replace('"', "&quot;", $content);
		$content = str_replace("'", "&#039;", $content);
		$content = str_replace("$", "&#36;", $content);
		$content = str_replace('}','&rlm;}',$content);
		return $content;
}

/*手机号码验证*/
function _checkmobile($mobilephone=''){
	if(strlen($mobilephone)!=11){	return false;	}
	if(preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$mobilephone)){   
		return true;
	}else{   
		return false;
	}
}
	
/*邮箱验证*/
function _checkemail($email=''){
		if(mb_strlen($email)<5){
			return false;
		}
		$res="/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/";
		if(preg_match($res,$email)){
			return true;
		}else{
			return false;
		}
}	


/*加密解密 ENCODE 加密   DECODE 解密*/
function _encrypt_pay($string, $operation = 'ENCODE', $key = '', $expiry = 0){
	if($operation == 'DECODE') {
		$string =  str_replace('_', '/', $string);
	}
	$key_length = 4;
	//md5指定字符串   G_WEB_PATH=域名 或者 我们自己定义字符更安全
	$key = md5($key != '' ? $key : G_WEB_PATH);
	//md5 两次
	$fixedkey = md5($key);
	//截取16位以后字符 在md5一次
	$egiskeys = md5(substr($fixedkey, 16, 16));

	if($key_length){
		if($operation == 'ENCODE'){
			//microtime(true)   当前Unix时间戳的微秒数(true为浮点数)
			// substr(md5(microtime(true)), -$key_length)  md5时间戳后，截取最后4位
			$runtokey = substr(md5(microtime(true)), -$key_length);
		}else{
			// substr   截取字符串 前4位
			$runtokey = substr($string, 0, $key_length);
		}
	}else{
		$runtokey = '';
	}
	// substr 0，16开始的截取前16位字符     16开始的截取第16位后的字符
	// . 拼接字符串   md5 加密
	$keys = md5(substr($runtokey, 0, 16) . substr($fixedkey, 0, 16) . substr($runtokey, 16) . substr($fixedkey, 16));
	if($operation == 'ENCODE'){
		//sprintf 把百分号（%）符号替换成一个作为参数进行传递的变量
		$string = sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$egiskeys), 0, 16) . $string;
	}else{
		//base64_decode 对使用 MIME base64 编码的数据进行解码
		$string = base64_decode(substr($string, $key_length));
	}

	$i = 0; $result = '';
	// strlen 获取字符串长度
	$string_length = strlen($string);
	for ($i = 0; $i < $string_length; $i++){
		// ord 返回字符串的首个字符的 ASCII 值
		// chr 从指定的 ASCII 值返回字符
		// ^  异或运算符，按二进制位进行异或运算（XOR）
		$result .= chr(ord($string{$i}) ^ ord($keys{$i % 32}));
	}
	if($operation == 'ENCODE') {
		// str_replace  将 = 替换为 空      / 替换为 _
		// base64_encode 使用 MIME base64 对数据进行编码
		$retstrs =  str_replace('=', '', base64_encode($result));
		$retstrs =  str_replace('/', '_', $retstrs);
		return $runtokey.$retstrs;
	} else {
		// substr 截取 0-10字符  第10-第16   26位往后的字符   0-16字符
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$egiskeys), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	}
}











/*加密解密 ENCODE 加密   DECODE 解密*/
function _encrypt($string, $operation = 'ENCODE', $key = '', $expiry = 0){
	if($operation == 'DECODE') {
		$string =  str_replace('_', '/', $string);
	}
	$key_length = 4;
	if(defined("G_BANBEN_NUMBER")){
			$key = md5($key != '' ? $key : System::load_sys_config("code","code"));
	}else{
			$key = md5($key != '' ? $key : G_WEB_PATH);
	}
	$fixedkey = md5($key);
	$egiskeys = md5(substr($fixedkey, 16, 16));
	$runtokey = $key_length ? ($operation == 'ENCODE' ? substr(md5(microtime(true)), -$key_length) : substr($string, 0, $key_length)) : '';
	$keys = md5(substr($runtokey, 0, 16) . substr($fixedkey, 0, 16) . substr($runtokey, 16) . substr($fixedkey, 16));
	$string = $operation == 'ENCODE' ? sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$egiskeys), 0, 16) . $string : base64_decode(substr($string, $key_length));

	$i = 0; $result = '';
	$string_length = strlen($string);
	for ($i = 0; $i < $string_length; $i++){
		$result .= chr(ord($string{$i}) ^ ord($keys{$i % 32}));
	}
	if($operation == 'ENCODE') {
		$retstrs =  str_replace('=', '', base64_encode($result));
		$retstrs =  str_replace('/', '_', $retstrs);
		return $runtokey.$retstrs;
	} else {	
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$egiskeys), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	}
}


function _getcookie($name){
	if(empty($name)){return false;}
	if(isset($_COOKIE[$name])){
		return $_COOKIE[$name];
	}else{		
		return false;
	}
}

function _setcookie($name,$value,$time=0,$path='/',$domain=''){		
	if(empty($name)){return false;}
	$_COOKIE[$name]=$value;				//及时生效
	$s = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
	if(!$time){
		return setcookie($name,$value,0,$path,$domain,$s);
	}else{
		return setcookie($name,$value,time()+$time,$path,$domain,$s);
	}
}


/*
*获取字符串长度
*一个汉字2个字节,字符1个字节
*/
function _strlen($str=''){
	if(empty($str)){
		return 0;
	}
	if(!_is_utf8($str)){
		$str=iconv("GBK","UTF-8",$str);
	}
	return ceil((strlen($str)+mb_strlen($str,'utf-8'))/2);
}
	

 	/*
	* 调用模板函数
	* $module   模板目录
	* $template 模板文件名,
	* $StyleTheme    模板方案目录,为空为默认目录
	*/
	
function templates($module = '', $template = '',$StyleTheme=''){	
		if(empty($StyleTheme)){$style=G_STYLE.DIRECTORY_SEPARATOR.G_STYLE_HTML;}
		else{
			$templates=System::load_sys_config('templates',$style);
			$style=$templates['dir'].DIRECTORY_SEPARATOR.$templates['html'];
		}
		$FileTpl = G_CACHES.'caches_template'.DIRECTORY_SEPARATOR.dirname($style).DIRECTORY_SEPARATOR.md5($module.'.'.$template).'.tpl.php';	
		$FileHtml = G_TEMPLATES.$style.DIRECTORY_SEPARATOR.$module.'.'.$template.'.html';	
		if(file_exists($FileHtml)){		
			if (file_exists($FileTpl) && @filemtime($FileTpl) >= @filemtime($FileHtml)) {					
				return $FileTpl;			
			} else {			
				$template_cache=&System::load_sys_class('template_cache

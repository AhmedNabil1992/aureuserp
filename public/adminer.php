<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 5.4.1
*/namespace
Adminer;const
VERSION="5.4.1";error_reporting(24575);set_error_handler(function($rc,$tc){return!!preg_match('~^Undefined (array key|offset|index)~',$tc);},E_WARNING|E_NOTICE);$Nc=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Nc||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$vi=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($vi)$$X=$vi;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($g=null){return($g?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$wb=adminer()->credentials();$L=Driver::connect($wb[0],$wb[1],$wb[2]);return(is_object($L)?$L:null);}function
idf_unescape($v){if(!preg_match('~^[`\'"[]~',$v))return$v;$le=substr($v,-1);return
str_replace($le.$le,$le,substr($v,1,-1));}function
q($zh){return
connection()->quote($zh);}function
escape_string($X){return
substr(q($X),1,-1);}function
idx($ta,$z,$k=null){return($ta&&array_key_exists($z,$ta)?$ta[$z]:$k);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
number_type(){return'((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';}function
remove_slashes(array$sg,$Nc=false){if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){while(list($z,$X)=each($sg)){foreach($X
as$de=>$W){unset($sg[$z][$de]);if(is_array($W)){$sg[$z][stripslashes($de)]=$W;$sg[]=&$sg[$z][stripslashes($de)];}else$sg[$z][stripslashes($de)]=($Nc?$W:stripslashes($W));}}}}function
bracket_escape($v,$Aa=false){static$ii=array(':'=>':1',']'=>':2','['=>':3','"'=>':4');return
strtr($v,($Aa?array_flip($ii):$ii));}function
min_version($Li,$ze="",$g=null){$g=connection($g);$eh=$g->server_info;if($ze&&preg_match('~([\d.]+)-MariaDB~',$eh,$C)){$eh=$C[1];$Li=$ze;}return$Li&&version_compare($eh,$Li)>=0;}function
charset(Db$f){return(min_version("5.5.3",0,$f)?"utf8mb4":"utf8");}function
ini_bool($Nd){$X=ini_get($Nd);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($Nd){$X=ini_get($Nd);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
sid(){static$L;if($L===null)$L=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$L;}function
set_password($Ki,$P,$V,$H){$_SESSION["pwds"][$Ki][$P][$V]=($_COOKIE["adminer_key"]&&is_string($H)?array(encrypt_string($H,$_COOKIE["adminer_key"])):$H);}function
get_password(){$L=get_session("pwds");if(is_array($L))$L=($_COOKIE["adminer_key"]?decrypt_string($L[0],$_COOKIE["adminer_key"]):false);return$L;}function
get_val($J,$m=0,$mb=null){$mb=connection($mb);$K=$mb->query($J);if(!is_object($K))return
false;$M=$K->fetch_row();return($M?$M[$m]:false);}function
get_vals($J,$c=0){$L=array();$K=connection()->query($J);if(is_object($K)){while($M=$K->fetch_row())$L[]=$M[$c];}return$L;}function
get_key_vals($J,$g=null,$hh=true){$g=connection($g);$L=array();$K=$g->query($J);if(is_object($K)){while($M=$K->fetch_row()){if($hh)$L[$M[0]]=$M[1];else$L[]=$M[0];}}return$L;}function
get_rows($J,$g=null,$l="<p class='error'>"){$mb=connection($g);$L=array();$K=$mb->query($J);if(is_object($K)){while($M=$K->fetch_assoc())$L[]=$M;}elseif(!$K&&!$g&&$l&&(defined('Adminer\PAGE_HEADER')||$l=="-- "))echo$l.error()."\n";return$L;}function
unique_array($M,array$x){foreach($x
as$w){if(preg_match("~PRIMARY|UNIQUE~",$w["type"])){$L=array();foreach($w["columns"]as$z){if(!isset($M[$z]))continue
2;$L[$z]=$M[$z];}return$L;}}}function
escape_key($z){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$z,$C))return$C[1].idf_escape(idf_unescape($C[2])).$C[3];return
idf_escape($z);}function
where(array$Z,array$n=array()){$L=array();foreach((array)$Z["where"]as$z=>$X){$z=bracket_escape($z,true);$c=escape_key($z);$m=idx($n,$z,array());$Kc=$m["type"];$L[]=$c.(JUSH=="sql"&&$Kc=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^json~',$Kc)?"::jsonb = ".q($X)."::jsonb":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($Kc,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($m,q($X))))));if(JUSH=="sql"&&preg_match('~char|text~',$Kc)&&preg_match("~[^ -@]~",$X))$L[]="$c = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$z)$L[]=escape_key($z)." IS NULL";return
implode(" AND ",$L);}function
where_check($X,array$n=array()){parse_str($X,$Sa);remove_slashes(array(&$Sa));return
where($Sa,$n);}function
where_link($t,$c,$Y,$tf="="){return"&where%5B$t%5D%5Bcol%5D=".urlencode($c)."&where%5B$t%5D%5Bop%5D=".urlencode(($Y!==null?$tf:"IS NULL"))."&where%5B$t%5D%5Bval%5D=".urlencode($Y);}function
convert_fields(array$d,array$n,array$O=array()){$L="";foreach($d
as$z=>$X){if($O&&!in_array(idf_escape($z),$O))continue;$ua=convert_field($n[$z]);if($ua)$L
.=", $ua AS ".idf_escape($z);}return$L;}function
cookie($E,$Y,$te=2592000){header("Set-Cookie: $E=".urlencode($Y).($te?"; expires=".gmdate("D, d M Y H:i:s",time()+$te)." GMT":"")."; path=".preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]).(HTTPS?"; secure":"")."; HttpOnly; SameSite=lax",false);}function
get_settings($sb){parse_str($_COOKIE[$sb],$ih);return$ih;}function
get_setting($z,$sb="adminer_settings",$k=null){return
idx(get_settings($sb),$z,$k);}function
save_settings(array$ih,$sb="adminer_settings"){$Y=http_build_query($ih+get_settings($sb));cookie($sb,$Y);$_COOKIE[$sb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==1))session_start();}function
stop_session($Sc=false){$Ei=ini_bool("session.use_cookies");if(!$Ei||$Sc){session_write_close();if($Ei&&@ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($z){return$_SESSION[$z][DRIVER][SERVER][$_GET["username"]];}function
set_session($z,$X){$_SESSION[$z][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($Ki,$P,$V,$j=null){$Ai=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($j!==null?"db|":"").($Ki=='mssql'||$Ki=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Ai,$C);return"$C[1]?".(sid()?SID."&":"").($Ki!="server"||$P!=""?urlencode($Ki)."=".urlencode($P)."&":"").($_GET["ext"]?"ext=".urlencode($_GET["ext"])."&":"")."username=".urlencode($V).($j!=""?"&db=".urlencode($j):"").($C[2]?"&$C[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($B,$D=null){if($D!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($B!==null?$B:$_SERVER["REQUEST_URI"]))][]=$D;}if($B!==null){if($B=="")$B=".";header("Location: $B");exit;}}function
query_redirect($J,$B,$D,$_g=true,$yc=true,$Gc=false,$Vh=""){if($yc){$vh=microtime(true);$Gc=!connection()->query($J);$Vh=format_time($vh);}$qh=($J?adminer()->messageQuery($J,$Vh,$Gc):"");if($Gc){adminer()->error
.=error().$qh.script("messagesPrint();")."<br>";return
false;}if($_g)redirect($B,$D.$qh);return
true;}class
Queries{static$queries=array();static$start=0;}function
queries($J){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(preg_match('~;$~',$J)?"DELIMITER ;;\n$J;\nDELIMITER ":$J).";";return
connection()->query($J);}function
apply_queries($J,array$T,$uc='Adminer\table'){foreach($T
as$R){if(!queries("$J ".$uc($R)))return
false;}return
true;}function
queries_redirect($B,$D,$_g){$vg=implode("\n",Queries::$queries);$Vh=format_time(Queries::$start);return
query_redirect($vg,$B,$D,$_g,false,!$_g,$Vh);}function
format_time($vh){return
lang(0,max(0,microtime(true)-$vh));}function
relative_uri(){return
str_replace(":","%3a",preg_replace('~^[^?]*/([^?]*)~','\1',$_SERVER["REQUEST_URI"]));}function
remove_from_uri($Mf=""){return
substr(preg_replace("~(?<=[?&])($Mf".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_file($z,$Hb=false,$Nb=""){$Mc=$_FILES[$z];if(!$Mc)return
null;foreach($Mc
as$z=>$X)$Mc[$z]=(array)$X;$L='';foreach($Mc["error"]as$z=>$l){if($l)return$l;$E=$Mc["name"][$z];$di=$Mc["tmp_name"][$z];$ob=file_get_contents($Hb&&preg_match('~\.gz$~',$E)?"compress.zlib://$di":$di);if($Hb){$vh=substr($ob,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$vh))$ob=iconv("utf-16","utf-8",$ob);elseif($vh=="\xEF\xBB\xBF")$ob=substr($ob,3);}$L
.=$ob;if($Nb)$L
.=(preg_match("($Nb\\s*\$)",$ob)?"":$Nb)."\n\n";}return$L;}function
upload_error($l){$He=($l==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($l?lang(1).($He?" ".lang(2,$He):""):lang(3));}function
repeat_pattern($Zf,$re){return
str_repeat("$Zf{0,65535}",$re/65535)."$Zf{0,".($re%65535)."}";}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
format_number($X){return
strtr(number_format($X,0,".",lang(4)),preg_split('~~u',lang(5),-1,PREG_SPLIT_NO_EMPTY));}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$Hc=false){$L=table_status($R,$Hc);return($L?reset($L):array("Name"=>$R));}function
column_foreign_keys($R){$L=array();foreach(adminer()->foreignKeys($R)as$p){foreach($p["source"]as$X)$L[$X][]=$p;}return$L;}function
fields_from_edit(){$L=array();foreach((array)$_POST["field_keys"]as$z=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$z];$_POST["fields"][$X]=$_POST["field_vals"][$z];}}foreach((array)$_POST["fields"]as$z=>$X){$E=bracket_escape($z,true);$L[$E]=array("field"=>$E,"privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>1,"auto_increment"=>($z==driver()->primary),);}return$L;}function
dump_headers($zd,$Ve=false){$L=adminer()->dumpHeaders($zd,$Ve);$Jf=$_POST["output"];if($Jf!="text")header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($zd).".$L".($Jf!="file"&&preg_match('~^[0-9a-z]+$~',$Jf)?".$Jf":""));session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$L;}function
dump_csv(array$M){foreach($M
as$z=>$X){if(preg_match('~["\n,;\t]|^0.|\.\d*0$~',$X)||$X==="")$M[$z]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($_POST["format"]=="tsv"?"\t":";")),$M)."\r\n";}function
apply_sql_function($r,$c){return($r?($r=="unixepoch"?"DATETIME($c, '$r')":($r=="count distinct"?"COUNT(DISTINCT ":strtoupper("$r("))."$c)"):$c);}function
get_temp_dir(){$L=ini_get("upload_tmp_dir");if(!$L){if(function_exists('sys_get_temp_dir'))$L=sys_get_temp_dir();else{$o=@tempnam("","");if(!$o)return'';$L=dirname($o);unlink($o);}}return$L;}function
file_open_lock($o){if(is_link($o))return;$q=@fopen($o,"c+");if(!$q)return;@chmod($o,0660);if(!flock($q,LOCK_EX)){fclose($q);return;}return$q;}function
file_write_unlock($q,$Bb){rewind($q);fwrite($q,$Bb);ftruncate($q,strlen($Bb));file_unlock($q);}function
file_unlock($q){flock($q,LOCK_UN);fclose($q);}function
first(array$ta){return
reset($ta);}function
password_file($h){$o=get_temp_dir()."/adminer.key";if(!$h&&!file_exists($o))return'';$q=file_open_lock($o);if(!$q)return'';$L=stream_get_contents($q);if(!$L){$L=rand_string();file_write_unlock($q,$L);}else
file_unlock($q);return$L;}function
rand_string(){return
md5(uniqid(strval(mt_rand()),true));}function
select_value($X,$A,array$m,$Uh){if(is_array($X)){$L="";foreach($X
as$de=>$W)$L
.="<tr>".($X!=array_values($X)?"<th>".h($de):"")."<td>".select_value($W,$A,$m,$Uh);return"<table>$L</table>";}if(!$A)$A=adminer()->selectLink($X,$m);if($A===null){if(is_mail($X))$A="mailto:$X";if(is_url($X))$A=$X;}$L=adminer()->editVal($X,$m);if($L!==null){if(!is_utf8($L))$L="\0";elseif($Uh!=""&&is_shortable($m))$L=shorten_utf8($L,max(0,+$Uh));else$L=h($L);}return
adminer()->selectVal($L,$A,$m,$X);}function
is_blob(array$m){return
preg_match('~blob|bytea|raw|file~',$m["type"])&&!in_array($m["type"],idx(driver()->structuredTypes(),lang(6),array()));}function
is_mail($hc){$va='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Wb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Zf="$va+(\\.$va+)*@($Wb?\\.)+$Wb";return
is_string($hc)&&preg_match("(^$Zf(,\\s*$Zf)*\$)i",$hc);}function
is_url($zh){$Wb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^(https?)://($Wb?\\.)+$Wb(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$zh);}function
is_shortable(array$m){return
preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea|hstore~',$m["type"]);}function
host_port($P){return(preg_match('~^(\[(.+)]|([^:]+)):([^:]+)$~',$P,$C)?array($C[2].$C[3],$C[4]):array($P,''));}function
count_rows($R,array$Z,$Xd,array$s){$J=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($Xd&&(JUSH=="sql"||count($s)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$s).")$J":"SELECT COUNT(*)".($Xd?" FROM (SELECT 1$J GROUP BY ".implode(", ",$s).") x":$J));}function
slow_query($J){$j=adminer()->database();$Wh=adminer()->queryTimeout();$mh=driver()->slowQuery($J,$Wh);$g=null;if(!$mh&&support("kill")){$g=connect();if($g&&($j==""||$g->select_db($j))){$fe=get_val(connection_id(),0,$g);echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$fe&token=".get_token()."'); }, 1000 * $Wh);");}}ob_flush();flush();$L=@get_key_vals(($mh?:$J),$g,false);if($g){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$L;}function
get_token(){$yg=rand(1,1e6);return($yg^$_SESSION["token"]).":$yg";}function
verify_token(){list($ei,$yg)=explode(":",$_POST["token"]);return($yg^$_SESSION["token"])==$ei;}function
lzw_decompress($Ga){$Sb=256;$Ha=8;$ab=array();$Jg=0;$Kg=0;for($t=0;$t<strlen($Ga);$t++){$Jg=($Jg<<8)+ord($Ga[$t]);$Kg+=8;if($Kg>=$Ha){$Kg-=$Ha;$ab[]=$Jg>>$Kg;$Jg&=(1<<$Kg)-1;$Sb++;if($Sb>>$Ha)$Ha++;}}$Rb=range("\0","\xFF");$L="";$Ui="";foreach($ab
as$t=>$Za){$gc=$Rb[$Za];if(!isset($gc))$gc=$Ui.$Ui[0];$L
.=$gc;if($t)$Rb[]=$Ui.$gc[0];$Ui=$gc;}return$L;}function
script($oh,$hi="\n"){return"<script".nonce().">$oh</script>$hi";}function
script_src($Bi,$Kb=false){return"<script src='".h($Bi)."'".nonce().($Kb?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
input_hidden($E,$Y=""){return"<input type='hidden' name='".h($E)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($zh){return
str_replace("\0","&#0;",htmlspecialchars($zh,ENT_QUOTES,'utf-8'));}function
nl_br($zh){return
str_replace("\n","<br>",$zh);}function
checkbox($E,$Y,$Ua,$he="",$sf="",$Ya="",$je=""){$L="<input type='checkbox' name='$E' value='".h($Y)."'".($Ua?" checked":"").($je?" aria-labelledby='$je'":"").">".($sf?script("qsl('input').onclick = function () { $sf };",""):"");return($he!=""||$Ya?"<label".($Ya?" class='$Ya'":"").">$L".h($he)."</label>":$L);}function
optionlist($wf,$Zg=null,$Fi=false){$L="";foreach($wf
as$de=>$W){$xf=array($de=>$W);if(is_array($W)){$L
.='<optgroup label="'.h($de).'">';$xf=$W;}foreach($xf
as$z=>$X)$L
.='<option'.($Fi||is_string($z)?' value="'.h($z).'"':'').($Zg!==null&&($Fi||is_string($z)?(string)$z:$X)===$Zg?' selected':'').'>'.h($X);if(is_array($W))$L
.='</optgroup>';}return$L;}function
html_select($E,array$wf,$Y="",$rf="",$je=""){static$he=0;$ie="";if(!$je&&substr($wf[""],0,1)=="("){$he++;$je="label-$he";$ie="<option value='' id='$je'>".h($wf[""]);unset($wf[""]);}return"<select name='".h($E)."'".($je?" aria-labelledby='$je'":"").">".$ie.optionlist($wf,$Y)."</select>".($rf?script("qsl('select').onchange = function () { $rf };",""):"");}function
html_radios($E,array$wf,$Y="",$dh=""){$L="";foreach($wf
as$z=>$X)$L
.="<label><input type='radio' name='".h($E)."' value='".h($z)."'".($z==$Y?" checked":"").">".h($X)."</label>$dh";return$L;}function
confirm($D="",$ah="qsl('input')"){return
script("$ah.onclick = () => confirm('".($D?js_escape($D):lang(7))."');","");}function
print_fieldset($u,$qe,$Oi=false){echo"<fieldset><legend>","<a href='#fieldset-$u'>$qe</a>",script("qsl('a').onclick = partial(toggle, 'fieldset-$u');",""),"</legend>","<div id='fieldset-$u'".($Oi?"":" class='hidden'").">\n";}function
bold($Ja,$Ya=""){return($Ja?" class='active $Ya'":($Ya?" class='$Ya'":""));}function
js_escape($zh){return
addcslashes($zh,"\r\n'\\/");}function
pagination($G,$zb){return" ".($G==$zb?$G+1:'<a href="'.h(remove_from_uri("page").($G?"&page=$G".($_GET["next"]?"&next=".urlencode($_GET["next"]):""):"")).'">'.($G+1)."</a>");}function
hidden_fields(array$sg,array$Bd=array(),$lg=''){$L=false;foreach($sg
as$z=>$X){if(!in_array($z,$Bd)){if(is_array($X))hidden_fields($X,array(),$z);else{$L=true;echo
input_hidden(($lg?$lg."[$z]":$z),$X);}}}return$L;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),(SERVER!==null?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
file_input($Pd){$Ce="max_file_uploads";$De=ini_get($Ce);$zi="upload_max_filesize";$_i=ini_get($zi);return(ini_bool("file_uploads")?$Pd.script("qsl('input[type=\"file\"]').onchange = partialArg(fileChange, "."$De, '".lang(8,"$Ce = $De")."', ".ini_bytes("upload_max_filesize").", '".lang(8,"$zi = $_i")."')"):lang(9));}function
enum_input($U,$wa,array$m,$Y,$kc=""){preg_match_all("~'((?:[^']|'')*)'~",$m["length"],$Ae);$lg=($m["type"]=="enum"?"val-":"");$Ua=(is_array($Y)?in_array("null",$Y):$Y===null);$L=($m["null"]&&$lg?"<label><input type='$U'$wa value='null'".($Ua?" checked":"")."><i>$kc</i></label>":"");foreach($Ae[1]as$X){$X=stripcslashes(str_replace("''","'",$X));$Ua=(is_array($Y)?in_array($lg.$X,$Y):$Y===$X);$L
.=" <label><input type='$U'$wa value='".h($lg.$X)."'".($Ua?' checked':'').'>'.h(adminer()->editVal($X,$m)).'</label>';}return$L;}function
input(array$m,$Y,$r,$_a=false){$E=h(bracket_escape($m["field"]));echo"<td class='function'>";if(is_array($Y)&&!$r){$Y=json_encode($Y,128|64|256);$r="json";}$Ig=(JUSH=="mssql"&&$m["auto_increment"]);if($Ig&&!$_POST["save"])$r=null;$bd=(isset($_GET["select"])||$Ig?array("orig"=>lang(10)):array())+adminer()->editFunctions($m);$qc=driver()->enumLength($m);if($qc){$m["type"]="enum";$m["length"]=$qc;}$Tb=stripos($m["default"],"GENERATED ALWAYS AS ")===0?" disabled=''":"";$wa=" name='fields[$E]".($m["type"]=="enum"||$m["type"]=="set"?"[]":"")."'$Tb".($_a?" autofocus":"");echo
driver()->unconvertFunction($m)." ";$R=$_GET["edit"]?:$_GET["select"];if($m["type"]=="enum")echo
h($bd[""])."<td>".adminer()->editInput($R,$m,$wa,$Y);else{$nd=(in_array($r,$bd)||isset($bd[$r]));echo(count($bd)>1?"<select name='function[$E]'$Tb>".optionlist($bd,$r===null||$nd?$r:"")."</select>".on_help("event.target.value.replace(/^SQL\$/, '')",1).script("qsl('select').onchange = functionChange;",""):h(reset($bd))).'<td>';$Pd=adminer()->editInput($R,$m,$wa,$Y);if($Pd!="")echo$Pd;elseif(preg_match('~bool~',$m["type"]))echo"<input type='hidden'$wa value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked='checked'":"")."$wa value='1'>";elseif($m["type"]=="set")echo
enum_input("checkbox",$wa,$m,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($m)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$E'>";elseif($r=="json"||preg_match('~^jsonb?$~',$m["type"]))echo"<textarea$wa cols='50' rows='12' class='jush-js'>".h($Y).'</textarea>';elseif(($Th=preg_match('~text|lob|memo~i',$m["type"]))||preg_match("~\n~",$Y)){if($Th&&JUSH!="sqlite")$wa
.=" cols='50' rows='12'";else{$N=min(12,substr_count($Y,"\n")+1);$wa
.=" cols='30' rows='$N'";}echo"<textarea$wa>".h($Y).'</textarea>';}else{$qi=driver()->types();$Je=(!preg_match('~int~',$m["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$m["length"],$C)?((preg_match("~binary~",$m["type"])?2:1)*$C[1]+($C[3]?1:0)+($C[2]&&!$m["unsigned"]?1:0)):($qi[$m["type"]]?$qi[$m["type"]]+($m["unsigned"]?0:1):0));if(JUSH=='sql'&&min_version(5.6)&&preg_match('~time~',$m["type"]))$Je+=7;echo"<input".((!$nd||$r==="")&&preg_match('~(?<!o)int(?!er)~',$m["type"])&&!preg_match('~\[\]~',$m["full_type"])?" type='number'":"")." value='".h($Y)."'".($Je?" data-maxlength='$Je'":"").(preg_match('~char|binary~',$m["type"])&&$Je>20?" size='".($Je>99?60:40)."'":"")."$wa>";}echo
adminer()->editHint($R,$m,$Y);$Oc=0;foreach($bd
as$z=>$X){if($z===""||!$X)break;$Oc++;}if($Oc&&count($bd)>1)echo
script("qsl('td').oninput = partial(skipOriginal, $Oc);");}}function
process_input(array$m){if(stripos($m["default"],"GENERATED ALWAYS AS ")===0)return;$v=bracket_escape($m["field"]);$r=idx($_POST["function"],$v);$Y=idx($_POST["fields"],$v);if($m["type"]=="enum"||driver()->enumLength($m)){$Y=$Y[0];if($Y=="orig")return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($m["auto_increment"]&&$Y=="")return
null;if($r=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?idf_escape($m["field"]):false);if($r=="NULL")return"NULL";if($m["type"]=="set")$Y=implode(",",(array)$Y);if($r=="json"){$r="";$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}if(is_blob($m)&&ini_bool("file_uploads")){$Mc=get_file("fields-$v");if(!is_string($Mc))return
false;return
driver()->quoteBinary($Mc);}return
adminer()->processInput($m,$Y,$r);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$ch="<ul>\n";foreach(table_status('',true)as$R=>$S){$E=adminer()->tableName($S);if(isset($S["Engine"])&&$E!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$K=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array())),1));if(!$K||$K->fetch_row()){$og="<a href='".h(ME."select=".urlencode($R)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]))."'>$E</a>";echo"$ch<li>".($K?$og:"<p class='error'>$og: ".error())."\n";$ch="";}}}echo($ch?"<p class='message'>".lang(11):"</ul>")."\n";}function
on_help($fb,$kh=0){return
script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $fb, $kh) }, onmouseout: helpMouseout});","");}function
edit_form($R,array$n,$M,$yi,$l=''){$Hh=adminer()->tableName(table_status1($R,true));page_header(($yi?lang(12):lang(13)),$l,array("select"=>array($R,$Hh)),$Hh);adminer()->editRowPrint($R,$n,$M,$yi);if($M===false){echo"<p class='error'>".lang(14)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";if(!$n)echo"<p class='error'>".lang(15)."\n";else{echo"<table class='layout'>".script("qsl('table').onkeydown = editingKeydown;");$_a=!$_POST;foreach($n
as$E=>$m){echo"<tr><th>".adminer()->fieldName($m);$k=idx($_GET["set"],bracket_escape($E));if($k===null){$k=$m["default"];if($m["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$k,$Gg))$k=$Gg[1];if(JUSH=="sql"&&preg_match('~binary~',$m["type"]))$k=bin2hex($k);}$Y=($M!==null?($M[$E]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$m["type"])&&is_array($M[$E])?implode(",",$M[$E]):(is_bool($M[$E])?+$M[$E]:$M[$E])):(!$yi&&$m["auto_increment"]?"":(isset($_GET["select"])?false:$k)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$m);$r=($_POST["save"]?idx($_POST["function"],$E,""):($yi&&preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$yi&&$Y==$m["default"]&&preg_match('~^[\w.]+\(~',$Y))$r="SQL";if(preg_match("~time~",$m["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$r="now";}if($m["type"]=="uuid"&&$Y=="uuid()"){$Y="";$r="uuid";}if($_a!==false)$_a=($m["auto_increment"]||$r=="now"||$r=="uuid"?null:true);input($m,$Y,$r,$_a);if($_a)$_a=false;echo"\n";}if(!support("table")&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>"."\n";echo"</table>\n";}echo"<p>\n";if($n){echo"<input type='submit' value='".lang(16)."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($yi?lang(17):lang(18))."' title='Ctrl+Shift+Enter'>\n",($yi?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".lang(19)."…', this); };"):"");}echo($yi?"<input type='submit' name='delete' value='".lang(20)."'>".confirm()."\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
shorten_utf8($zh,$re=80,$Ch=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$re).")($)?)u",$zh,$C))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$re).")($)?)",$zh,$C);return
h($C[1]).$Ch.(isset($C[2])?"":"<i>…</i>");}function
icon($yd,$E,$xd,$Yh){return"<button type='submit' name='$E' title='".h($Yh)."' class='icon icon-$yd'><span>$xd</span></button>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}@ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M��h��g�б���\"P�i��m��cQCa��	2ó��d<��f�a��:;NB�q�R;1Lf�9��u7&)�l;3�����J/��CQX�r2M�a�i0���)��e:LuÝh�-9��23l��i7��m�Zw4���њ<-��̴�!�U,��Fé�vt2��S,��a�҇F�VX�a�Nq�)�-���ǜh�:n5���9�Y�;j��-��_�9kr��ٓ;.�tTq�o�0�����{��y��\r�Hn��GS��Zh��;�i^�ux�WΒC@����k��=��b����/A��0�+�(���l���\\��x�:\r��b8\0�0!\0F�\nB�͎�(�3�\r\\�����Ȅa���'I�|�(i�\n�\r���4O�g@�4�C��@@�!�QB��	°�c��¯�q,\r1Eh��&2PZ���iG�H9G�\"v���������4r����D�R�\n�pJ�-A�|/.�c�Du�����:,��=��R�]U5�mV�k�LLQ@-\\����@9��%�S�r���MPD��Ia\r�(YY\\�@X�p��:��p�l�LC �������O,\r�2]7�?m06�p�T��aҥC�;_˗�yȴd�>��bn���n�ܣ3�X���8\r�[ˀ-)�i>V[Y�y&L3�#�X|�	�X�\\ù`�C���#��H��2�2.#���Z�`�<��s����Ò��\0u�h־��M��_\niZeO/CӒ_�`3���1>�=��k3����R/;�/d��\0�����ڵm���7/���A�X�������q.�s�L��� :\$�F�������w�8�߾~�H�j��\"�����Գ7gS���FL�ί�Q�_��O'W��]c=�5�1X~7;��i��\r�*\n��JS1Z���������c���t��A�V�86f�d�y;Y�]��zI�p�����c�3�Y�]}@�\$.+�1�'>Z�cpd���GL��#k�8Pz�Y�Au�v�]s9���_Aq���:���\nK�hB�;���XbAHq,��CI�`����j�S[ˌ�1�V�r���;�p�B��)#鐉;4�H��/*�<�3L��;lf�\n�s\$K`�}��Ք���7�jx`d�%j]��4��Y��HbY��J`�GG��.��K��f�I�)2�Mfָ�X�RC��̱V,���~g\0���g6�:�[j�1H�:AlIq�u3\"���q��|8<9s'�Q]J�|�\0�`p���jf�O�b�����q��\$����1J�>R�H(ǔq\n#r����@�e(y�VJ�0�Q҈��6�P�[C:�G伞���4���^����PZ��\\���(\n��)�~���9R%�Sj�{��7�0�_��s	z|8�H�	\"@�#9DVL�\$H5�WJ@��z�a�J �^	�)�2\nQv��]�������j (A���BB05�6�b˰][��k�A�wvkg�ƴ���+k[jm�zc�}�MyDZi�\$5e��ʷ���	�A��CY%.W�b*뮼�.���q/%}B�X���ZV337�ʻa�������wW[�L�Q�޲�_��2`�1I�i,�曣�Mf&(s-����Aİ�*��Dw��TN�ɻ�jX\$�x�+;���F�93�JkS;���qR{>l�;B1A�I�b)��(6��r�\r�\rڇ����Z�R^SOy/��M#��9{k���v\"�KC�J��rEo\0��\\,�|�fa͚��hI��/o�4�k^p�1H�^����phǡV�vox@�`�g�&�(����;��~Ǎz�6�8�*���5����E���p����Ә���3��ņg��rD�L�)4g{���峩�L��&�>脻����Z�7�\0��̊@�����ff�RVh֝��I�ۈ���r�w)����=x^�,k��2��ݓj�b�l0u�\"�fp��1�RI��z[]�w�pN6dI�z���n.7X{;��3��-I	����7pjÝ�R�#�,�_-���[�>3�\\���Wq�q�J֘�uh���FbL�K���yVľ����ѕ�����V���f{K}S��ޝ��M���̀��.M�\\�ix�b���1�+�α?<�3�~H��\$�\\�2�\$� e�6t�Ö�\$s���x��x���C�nSkV��=z6����'æ�Na��ָh��������R�噣8g�����w:_�����ҒIRKÝ�.�nkVU+dwj��%�`#,{�醳����Y����(oվ��.�c�0g�DXOk�7��K��l��hx;�؏ ݃L��\$09*�9 �hNr�M�.>\0�rP9�\$�g	\0\$\\F�*�d'��L�:�b���4�2����9��@�Hnb�-��E #Ĝ����rPY�� t� �\n�5.�����\$op�l�X\n@`\r��	��\r���� � ���	������ �	@�@�\n � �	\0j@�Q@�1\r��@� �	\$p	 V\0�``\n\0�\n �\n@�'����\n\0`\r����	��\r���\0�r����	\0�`�	���{	,�\"��^P�0�\n��4�\n0���.0�p���\rp�\r��p���p��q�Q0�%���1Q8\n �\0�k�ȼ\0^���\0`��@���>\n�o1w�,Y	h*=����P�:іV��и.q����\r�\r�p���1��Q	��1� �`��/17����\r�^��\"y`�\n�� �#��\0�	 p\n��\n��`� �r �Q��b�1��3\n��#��#�1�\$q�\$ѱ%0�%q�%��&�&q� �&�'1�\rR}16	 �@b\r`�`�\r��	�����d���	j\n�``��\n��`dcсP��,�1R��\$�rI�O �	Q	�Y32b1�&��01��� �� f��\0�\0���f�\0j\n�f`�	 �\n`�@�\$n=`�\0��v nI�\$�P(�d'�����g�6��-��-�C7R��� �	4��-1�&��2t\r�\"\n 	H*@�	�`\n � �	��l�2�,z\r�~� �\r�F�th�������m����z�~�\0]G�F\\��I�\\��}It�C\n�T�}���IEJ\rx����>�Mp��IH�~��fht��.b��xYE��iK��oj�\n���L��tr�.�~d�H�2U4�G�\\A��4��uPt����谐����L/�P�	\"G!R��Mt�O-��<#�APuI��R�\$�c���D�Ɗ����-��G�O`Pv�^W@tH;Q��Rę�\$��gK�F<\rR*\$4���'�����[���I��Um��h:+��5@/�l�I���2���^�\0OD�����\rR'�\r�TЭ[����Ī��MC�M�Z4�E B\"�`���euN�,䙬�]��t�\r�`�@h��*\r�.V��%�!MBlPF��\"��&�/@�v\\C��:mMgn����i8�I2\rp�vj�����+Z mT�ue��fv>f�И�`DU[ZT�V�C�T�\r��Uv�k�^���L��b/�K�Sev2�ubv�OVD��Im�\$�%�X?ud�!W�|,\r�+�cnUe�Z��ʖ����-~X��������BGd�\$i��Mv!t#L�3o�UI�O�u?ZweR���cw�.�`ȡi��\rb�%�b���H�\"\"\"h��_\$b@�z��\0f\"��rW��*��B|\$\$�B�נ\"@r��(\r`� �C���(0&�.`�Nk9B\n&#(���@䂯��d��^����� �@�`�I-{�0��\n�B�{�4sG{��;z��b�{ �{b�ׯ�){B��xK���Ň5=cڪ��y��&�J�Pr�I/��� \0��V\r�׉��=����N\\ئ=�K��}XV�x�����إ�ˋx��d�Պی*H'�δ��{X�=��=\0�8�\0����[ɫ�J��t��O�e����ɋ��\r�����DX���Ň��}�z������)�y'��'��я�I��(�[�l(5�`f\\�`���e�.lY(�=z�ה!�Y%h��O�+����`ٙ\"e� ��ė���K������������ߚ�#�S��E�I�Y����.H�JtG���`��H�J5���5��~ ��6C��h����XDz\n�x��ysh���FK�c�zj�Z�Y8(��%�|y�I��ߑ؃���e��Y�X���u�� ��i�]��c���M��;�ȧ���>ǡ��Q�T����� [~W�~��c݂z�����z�����\r�:  \0�rY��x)��!��ɡ�K��+�z!��ӀC+����ٮ�ï:ݎ�������Zg��~z4f��	�:����s�Ӫ��+��x�%����=��G��I�f3?������+Y��q�@��G���y��o��Ѵ�p\r�~�{W���[����y�:\0�\\���;e�ۡ�YI\"��zdk�Z�|[u��u��+�׹9q��nR ˮ�B����ׁz|\r�ᤄ��k�^��[1��%�.��pA�2<��=�ء��\$�;�5�)��m��!���XX���Y�x�5vT\\�Q�%:��>��ɛ�;��e�|/���y����W��xנ|g������C��\\�����<��9z\\�#�.FV;8��N�X7����\"8&d5�P�4Gj?�\0�?\"=���HER");}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M��h��g���h0�LЁ�d91�S!��	�F�!��\"-6N����bd�Gg���:;Nr�)��c7�\r�(H�b81��s9���k\r�c)�m8�O��VA��c1��c34Of*��-�P��1��r41��6��d2�ց���o���#3���B�f#	��g9Φ�،fc\r�I���b6E�C&��,�bu��m7a�V���s��#m!��h��r���v\\3\rL:SA��dk5�n������aF��3��e6fS��y���r!�L��-�K,�3L�@��J��˲�*J��쵣����	������b�c��9���9���@����H�8��\\���6>�`�Ŏ��;�A��<T�'�p&q�qE��4�\rl���h�<5#p��R �#I��%��fBI��ܲ��>�ʫ29<��C�j2��7j��8j��c(n���?(a\0�@�5*3:δ�6����0��-�A�lL��P�4@�ɰ�\$�H�4�n31��1�t�0��͙9���WO!�r��������H����9�Q��96�F���<�7�\r�-xC\n ��@�������:\$i�ضm���4�Kid��{\n6\r���xhˋ�#^'4V�@a��<�#h0�S�-�c��9�+p���a�2�cy�h�BO\$��9�w�iX�ɔ�VY9�*r�Htm	�@b��|@�/��l�\$z���+�%p2l���.�������7�;�&{��m��X�C<l9��6x9�m�������7R��0\\�4��P�)A�o��x���q�O#����f[;��6~P�\r�a��T�GT0���u�ޟ���\n3�\\ \\ʎ�J�ud�CG���PZ�>����d8�Ҩ������C?V��dL��L.(ti���>�,�֜�R+9i��ޞC\$��#\"�AC�hV�b\n��6�T2�ew�\nf��6m	!1'c��;��*eLRn\r�G\$�2S\$��0���a�'�l6�&�~A�d\$�J�\$s� �ȃB4���j�.�RC̔�Q�j�\"7\n�Xs!�6=�BȀ}");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("':�̢���i1��1��	4������Q6a&��:OAI��e:NF�D|�!���Cy��m2��\"���r<�̱���/C�#����:DbqSe�J�˦Cܺ\n\n��ǱS\rZ��H\$RAܞS+XKvtd�g:��6��EvXŞ�j��mҩej�2�M�����B��&ʮ�L�C�3���Q0�L��-x�\n��D���yNa�Pn:�����s��͐�(�cL��/���(�5{���Qy4��g-�����i4ڃf��(��bU���k��o7�&�ä�*ACb����`.����\r����������\n��Ch�<\r)`�إ`�7�Cʒ���Z���X�<�Q�1X���@�0dp9EQ�f����F�\r��!���(h��)��\np'#Č��H�(i*�r��&<#��7K��~�# ��A:N6�����l�,�\r��JP�3�!@�2>Cr���h�N��]�(a0M3�2��6��U��E2'!<��#3R�<�����X���CH�7�#n�+��a\$!��2��P�0�.�wd�r:Y����E��!]�<��j��@�\\�pl�_\r�Z���ғ�TͩZ�s�3\"�~9���j��P�)Q�YbݕD�Yc��`��z�c��Ѩ��'�#t�BOh�*2��<ŒO�fg-Z����#��8a�^��+r2b��\\��~0�������W����n��p!#�`��Z��6�1�2��@�ky��9\r��B3�pޅ�6��<�!p�G�9�n�o�6s��#F�3���bA��6�9���Z�#��6��%?�s��\"��|؂�)�b�Jc\r����N�s��ih8����ݟ�:�;��H�ތ�u�I5�@�1��A�PaH^\$H�v��@ÛL~���b9�'�����S?P�-���0�C�\nR�m�4���ȓ:���Ը�2��4��h(k\njI��6\"�EY�#��W�r�\r��G8�@t���Xԓ��BS\nc0�k�C I\rʰ<u`A!�)��2��C�\0=��� ���P�1�ӢK!�!��p�Is�,6�d���i1+����k���<��^�	�\n��20�Fԉ_\$�)f\0��C8E^��/3W!א)�u�*���&\$�2�Y\n�]��Ek�DV�\$�J���xTse!�RY� R��`=L���ޫ\nl_.!�V!�\r\nH�k��\$א`{1	|�����i<jRrPTG|��w�4b�\r���4d�,�E��6���<�h[N�q@Oi�>'ѩ\r����;�]#��}�0�ASI�Jd�A/Q����⸵�@t\r�UG��_G�<��<y-I�z򄤝�\"�P��B\0������q`��vA��a̡J�R�ʮ)��JB.�T��L��y����Cpp�\0(7�cYY�a��M��1�em4�c��r��S)o����p�C!I���Sb�0m��(d�EH����߳�X���/���P���y�X��85��\$+�֖���gd�����y��ϝ�J��� �lE��ur�,dCX�}e������m�]��2�̽�(-z����Z��;I��\\�) ,�\n�>�)����\rVS\njx*w`ⴷSFi��d��,���Z�JFM}Њ ��\\Z�P��`�z�Z�E]�d��ɟO�cmԁ]� ������%�\"w4��\n\$��zV�SQD�:�6���G�wM��S0B�-s��)�Z��c|�^R��E�8kM���s�d�ka�)h%\"P��0nn��/��#;��g\rd��8��F<3\$�,�P);<4`��<2\n����@w-��͗A�0�����Lr�Yh�XC�a�>��t��L��2�yto;2��Q��t��frm�:��A�����AN��\\\"k�5oV�Ƀ=��t�7r1�p�Av\\+�9���{��^(i��f�=�r����u���t�]y�ޅ��C���������gi�vf���+�Ø|��;�����]�~��|\re��쿓�݂�'�����������	�\0+W��co�w6wd Su�j�3@���0!��\n .w�m[8x<��cM�\n9���'a���1>���[���d��ux��<\"Y�c��B!i���w�}��5U�k�����]������{�IךR����=f W~�]�(bea�'ub�m�>�)\$��P��-��6��R*IGu#ƕUK�AX�t�(�`_��\"���p� &U���I��]��YG6P�]Ar!b� *ЙJ�o��ӯ�������v��*���!�~_���4B���_~RB�iK����`�&J�\0���N\0�\$�����C�K �S���jZ�����0pvMJ�bN`L��e�/`RO.0P�82`�	����d Gx�bP�-(@ɸ�@�4�H%<&���Z����p����%\0�p��Є���	��	��/\"��J��\ns��_��\r��g�`��!k�pX	��:�v��6p\$�'���RUeZ��d\$�\nL�B���.�d�n����tm�>v�j��)�	M�\r\0�.�ʊH��\"�5�*!e�ZJ�����f(dc��(x��jg\0\\������ Z@���|`^��r)<�(������)������@Yk�m��l3Qyс@���ѐf��Pn�����T��N�mR�q���Vmv�N֍�|�ШZ��Ȇ�(Yp��\"�4Ǩ���&��%�l�P`Ā�Xx bbd�r0Fr5�<�C��z���6�he!��\rdz���K;�t��\n�͠�HƋQ�\$Q�Enn�n\r���#�T\$��ˈ(ȟѩ|c�,�-�#��\r���J�{d�E\n\$��Br�iT��+�2PED�Be�}&%Rf��\n��^�C��Z�Z RV��A,�;���<���\0O1���c^\r%�\r ��`�n\0y1��.��\r�ĂK1�M3H�\r\"�0\0NkX�Pr��{3 �}	\nS�d��ڗ�x.Z�RT�wS;53 .�s4sO3F��2�S~YFpZs�'�@ّOqR4\n�6q6@Dh�6��7vE�l\"�^;-�(�&�b*�*��.! �\r�!#�x'G\"�͆w��\"�� �2!\"R(v�X��|\"D�v��)@�,�zm�A�wT@��  �\n����ЫhдID�P\$m>�\r&`�>�4��A#*�#�<�w\$T{\$�4@��dӴRem6�-#Dd�%E�DT\\�\$)@��WC�(t�\"M��#@�TF�\r,g�\rP8�~��֣J��c����ĹƂ� ʎ\"�L�Z��\r+P4�=���S�T�A)�0\"�CDh�M\n�%F�p���|�fLNlFtDmH����5�=H�\n��ļ4���\$�K�6\rbZ�\r\"pEQ%�wJ��V0��M%�l\"h�PF�A��A㌮�/G�6�h6]5�\$�f�S�CLiRT?R���C����HU�Z��YbF�/�.�Z�\"\"^�y�6R�G ���n��܌�\$���\\&O�(v^ �KU�Ѯ��am�(\r������\$_��%�+KTt��.ٖ36\n�c��:�@6 �jP�AQ�F�/S�k\"<4A�gA�aU�\$'����f��QO\"�k~�S;����.��:��k��9�����e]`n���-7��;��+V��8W��2H�U��YlB�v��⯎�Ԇ����	����p���l�m\0�4B�)�X�\0��Q�qFSq�4��nFx+p��E�Sov�GW7o�w�KRW�\r4`|cq�e7,�19�u��u�cq�\"LC�t�h�)�\r��J�\\�W@�	�|D#S\r�%�5l�!%+�+�^�k^ʙ`/�7��(z*񘋀���E��{�S(W��-�Xė0V��0�����=��a	~�fB�˕2Q���ru mC�����t�r(\0Q!K;xN�W������?b<�@�`�X,��`0e�ƂN'�����&~��t��u�\"| �i� �B� 7�R�� ��lSu��8A��dF%(�������?3@A-oQ�ź@|~�K���^@x��b��~�D�@س�����TN�Z�C�	W���ix<\0P|��\n\0�\n`�����\"&?st|ï�w�%����md�u�N�^8�[t�9��B\$�������'\">U�~�98����ÔF�f ���u����/)9����\0��A�z\"FWAx�\$'�jG�(\"� �s%T��H����e,	M�7�b� ǅ�a� ˓�ƃ�&wY�φ3���� /�\rϖ�����{�\"�ݜp{%4b��`팤��~n��E3	������9��3X�d���ՏZ��9�'��@����l�f����Q�bP�*G�o���`8������A��B|�z	@�	��b�Zn_�h�'ѢF\$f���`��HdDd�H%4\rs�AjLR�'��f�9g I��,R\\����>\n��H[�\"���\rӁ����L�,%�FLl8gzL�<0k�o\$�k��`��KP�v�@d�'V�:V��M�%���@�6�<\r��T���LE��NԀS#�.�[�x4�a�̭�LL����\n@��\0۫tٲ�\n^F�������5`� R��7�lL�u�(��d���� �\r�Bf/uCf�4�cҞ B���_�nL�\0� \$��aYƦ���~�Uk�v�e�˥�˲\0�Z�aZ����Xأ��|C�q��/<}س���ú���� Z��*�w\nO��z`�5��18�c����������I�Q2Ys�K�����\n�\\��\"�� ð�c��*�B����.�R1<3+���*�S�[�4�m쭛:R�h��ITdev�I�H���-Zw\\�%n�56�\n�W�i�\$�ōow��+�����r��&Jq+�}�D����j��d��?�U%BBe�/M��Nm=τ�U��b\$HRf�wb|��x d�2�NiS���g�@�q@��>�Sv�������|�kr�x��\0{�R�=F������#r��8	��Z�v�8*ʳ�{2S�+;S���Ө�+yL\$\"_��B�8��\"E�%������\n����p�p''�p��wUҪ\"8бI\\ @���ʾ �Ln���R�#M�D��q�LN��\n\\��̎\$`~@`\0u�~^@��l�-{5�,@bru�o[�����}�/�y.�� {�6q��R�p��\$�+1�3����+��O!D)����\nu�<��,����=�Jd�+}��d#�0ɞc��3U3�EY���\r��tj5ҥ7�e��wׄǡ���^��q߂�9�<\$}k���RI-���+'_Ne?S�R�hd*X�4��c}��\"@��vi>;5>Dn� �\r��)bN�uP@Y�G<��6i�#PB2A�-�0d0+���gK����?�n���d�d�O������c�i<����0\0�\\����g����ꡖ��NTi'����;i�mj�܈�����u�J+�V~����'ol`����\",�������F��	��{C�����T a�NEۃQ�p� p��+?�\n�>�'l��* t�Kάp�(YC\n-q̔0�\"*ɕ�,#���7��\"%�+q���B��=�i.@�x7:�%GcYI��0*��Ðk�ۈ�\\����Q_{����#��\r�{H�[p� >7�ch�n����.����S|&J�MǾ8��m�Oh���	��qJ&�a�ݢ�'�.b�Op��\$�����D@�C�HB�	��&�ݡ|\$Ԭ-6��+�+ �����p��ଡAC\r�ɓ��/�0�����M��iZ�nE�͢j*>��!Ңu%��g�0���@��5}r��+3�%��-m��G�<���T;0�����DV�d�g�9'lM��H�� F@�P��un�tFB%�M�t'�G�2��@2�<�e��;�`��=LX�2���X�}oc.L�+�xӎ�&D�a����ɫ�F2\ngL�E��.\\xSL�x�;lw�D=0_QV,a 5�+L��+�|\$�i�jZ\n��D�E�,B�t\\�'H0����R~(\\\"��:��n*���(��o�1w��Q��r���E�te�F��\$�Sђ]�\rL�yF���\\B�i�h��hd��&ᚇh;fo��B-y`���0��J�lP�xao�\$�Xq�,(���C*	��:�/����HG\"��c��C���Q�\nF�Ԅ�#�8��F:У\0��Ok��D��])�ϚtT8L�𒨔�n�`���|�HJ���� �� \"�6�{����?=I<HGc ŤF�@�,C ��@j�\$L���(�nEʑP��jb�n�Α���W� \r�Lq����sPH�ꉝz\\V\$k�ҏtr5�,��l����<�'\0^S02�0f -5\"ac�\"3U�p��\"ܘ�%��\0'Zt\"96��9_ @Z{�0I��D�ZE@��N�h`�\"�`�\0�����ɹ(G�H��Ch� �I��f`@ZD�\$)�K�;Z��\0�/�C�T>r_R@O�`1r�TҨIb\0�*�8�����h\$�_�p�Rĕ\$��Ni^ʪP/O)��.ŹT6�\\�ٔ@T���rą`)���T=�n\0��2��e�+�9ʢ\\��@�����>�PH�1	�y#���r�<�a�e�K��/�c�M@_.\09ˈ��������B����0i���a�\n��de�a�%|S2�����#����n��D�\$/�+E�d����_2P��\$s,ok�#�<�	�A�đr{B���A-Q4Ҥ�\n�\ry�!�b䱎���O��@ɬ��k�� �\"�r��*�݇��Y��/��ȑ a0��%�.gE~��&� 89����#@M_ ���7K䃸J`�X)�B\$�(	:�g��n*�|�M6PZ��Ht�Jtq�Cx�[ڼ����l=\n���U3�f\\̔J�P	,�:�}TA�SYH(�\n���I�ٲ�!t(2U\"�\\�X�^s�	��a!�\nPr��`�X3fnb�����J���&�z�zQSf ���t�!T?�9%�(Q��B�}6B�kP\0�>�g�&~fhU�r��,� p5Hi��p����qɚ�g�V�V��Og�WEJ8�0G��ak���@N NM��U�UxȪ��S�x	��	�K�@c�1y�VlϠ��C����2Q^rP6|�I^M�,�j%d�`ܫ��F��\\#%�|�C����7싢�G�TN�����i��H���Q�O���C�yB��\$�%T���*�>z\r�MM Kp� ��J7O۷�4�%�\$�p����4������͂��EҪ\"T��\0O�\0��@>	r�O�]���x�}^�I��@� źqn��0�Bb�ȵ�I�(�M/�;���}RN\n�C�<�b�PԵu?�=Pe�C����L^'�S��?}4)��S-���1\r5S�OE�SF����AOR+�ޙ+v��5�&C)ِ��KSDB߳N|E\rc�U�Yʾ���V���?H�)実+sF��k�LPW-�,�U:�&��t{��Vo���J�l'��W�e74X�n GF�'���`��Cc��%Il�j�u6����v�U��Z�\0*���Nԟ#��(���n�-;|��4�]X���y'����;��Z���) s9����%��R+\$��	��Q��(\"�_kX��������\nM#���\"!p~:�*����\$�3O������6�+���\nB�{1��|H�K<[`3��#��F@��ǐ! |�؊\0��>�����[nrMM�+��mO_�2��Ȇ�\0�e^	�7Z�&�B�J褓h7QO%rf�p��΁�֞�m�ب�Ç�4E�l���+���V��i�N S�Z�Wt�2W�[;��v\"%��\$^�-(I\$��S@R-&�T�z��k(��	�%R8�uY\0[9-���(�)E��8�=^����G�5#����)�1V��b\r]�Ne;&�Y�`r��I��Pݱ���ֲ��\0�@P�7���0H���؍R�x�\0000C|�n=��`��TT��\rEhON���'��&�tc�K ��ܕU5��������P3\\��2\"\0y�5�V]���6>�U!��@�hu��(�\"E%07B��6��d�HN������ij';@��e�MzlSfjKY�֍���-uh��H���smL@��\"r�j���j'l7	�(u�u��E��e�a�@�+�K�:ӕ�%n�z�V���;�[�_Vz_��E���8�<�Sb�������6g��:c����7\n����%Q�� K�7�ܮB����w�u�5��0��֚���y�ncnK����T8�ʙ�s��W=+�=K\n_[p�G���C5����'�D\"��M<\":|Mq4���f�s�x	�qlͰ��QP��aOY�E=���6nT떒�Bt�h�C\0p��@n��D(a�P�\"���'ZN��۬��\r�LNX�g��<!w�����[��B)��)~���c�x��v�i¦�q�����a�@K��7s�EQdý��k����?\"�3�-\"U��|������|21D>߳�]­&���\\h�TƳ5�\0`Tz���s -�N����\"�f��N�LU�]n(D�(��&%\"�e\\��O��N�Inۿ��\0����ƕ���@����V�|R�MYC�T����b�UH�p)���S�s� q�i���`Z5vt坉�*�OO\n�(�����F��58�!ax@�{^P����?���eh}\\�j^2�L�,6�.�N	K�%����u���ip��!?�l��� -5�w���K\"V��\\�Is��2!��\$4�5v\n�����gr��N��}��;��������W%D(pWa�\0�v'��6��V��ƿ0W��E4�EUl�8�LD��E�<kO��H��DU�	`vS��L��!DTMbnWV��Cd��)Ze蟀���:�2�d8��K�ބ�4�-G�b;wQW�30\r�f\0�,�`Qhl�֍�0�P��0h@\\�r�8��T���⛜�1�`�&���w�X�>�F?��|P�*�M�qZѯ��}��0k`��#�իc�'[�ֱˍ|s�IJ��\r����<OaƼ@�W��u�T��:��E^������!k�����a\$�>5��u_��KcCQ�r-ъ�'\r�iC������@8�S�PS�_Xgl�%�	�n1r.<�w_aɺĳ�Gh�4\n�W�Z��aBn,\\\0���DU�\nbbZ'���72���r�¢��}�Y>/�w\\Y�`^7J�j�S�������S.��o%�Jg\0GD,���>7���R�0������3��6�%i\0S�^L��A��\ri��O<���a phv[�{���\0�E�^x�ܼg�YzW�yG�a��:(�>C�����e\0���])�3yts_a�7�+��B��C�eT��f�o�P����2E�C��v�>�w�l�z�*p�Y����q�����Q�p\nv[|q�ҨE[�Xi���=�z(	�M�n�]7F\r��Cs4|-} ���Ŀ(NU�?,��څ��������q	��p�q~��� ��F��%�88��靦��\$�ް�[���r�o!3��(����g���ץpJ!���q�Z�v?��c���L��7��6��\$�m���q��8l!��5�C�;Q,��d�sF�-O��fÈ�\$���6�%U�C��f\"��e(j�\rMt�F����R�x;n�B\$��SS�x'��G��陊M�	��4ͬ'k��~��#9e��Y���~��뭈;f�+�j�K�9p���M�'X�/rt�\0�\\�J%Q���R�\rвO3�|�寚���ϱ�4��xF���s5E�Ԑ;ԒWR��JX�ʶ�J�\$��wzO��&ǵ��z�k�S�\n�\nNUP���.��0���bdk��P���	G6�+B�z�1ΎhQ>sHv�����Q�٠E�p��M��)��\n�\\�ў�Pz���.s��� g��)a~��ȥ�!(!�G�hr[�*�����բ�`��~�\"!�O���5�G3Ş*qkgB�,\$���**1�c.�n	8��\$d���VSne�MiZ���7žg�A�5�����\n�`�,�2��a�ү��mMkʻ��ɯ��/-��6�@?#`��)�Ԁ�ha���)Vc�]�_=�Rz\\�VR��=�ط�(-�ot�\$ܥ�\n���dSm�y��fө�N\r�m(t;D���p�2�ݶ��ZRl)�9M̛�,/��Yix��kя)�.�2@S^���u���d�6�!��>VB�� x<��Kt06���@��\nG�A�P�(��NbD��K\n�\"��cN��\ră.p���'2L��d�ꟲ���\\Ly�A=	��D��m3�%�@��������8�qbSP\"�ޢ�Ʈ/�Dz�C&�O��\0007f��D^1�X��/��,\n��v�Wx%f)��' �D�dQ@��I(ҋ7Y��|���A�Q��D��ڠe 8ׇ7k)_ �@\"\"��%�}�	�(��1�1؍�\r����e���?-ɵH��&�����\rL���'�eۮ0�T�]��C!�emNz�	Uz���Ɉ���S�ܜaf�7�M�^C�D���(_������#\"�dr5�9���81��hf�ȭ�a_�×tZX\0�U����{2nn]��;FR��!�}>s�Hi��y#���?\"Ť�����>{���/?7�F��Y����?Aj��.�U�!5`H��\$r\0��'\n�\":.��dԂٙƪ�q�Rխoh��>���{��1��+�>����t��k�%-D�=9�}�C@�8cm�Hr���W�n��\0Ď<(�RR�8����YV��`�pp�.U�e_`����^���쵛n^�_�R|�r΅p�7/!M5���|���\n�&�F��VVz��O�A�~ш|ƛ��4NȒ��Ք��g�yh-���\nN\"r\"���Gc�s����D�'�Xo٧���O�{��{Y{��E�=T�e�Z������{\";�H��Xz�t��w�*-����U���w�-��\"��<A^�O��T �]�D?:���������<��p�q�[���,)�&`�{xKI�I`�`��c��0����D�y8���qC��Y��CF���J���nk�[�8����:\n^�ց��T�!X*M�<�5`\0��6A�2o�P.��a�AH��#x[�����▞�� '�o@��O0^���h|�P�=+�)�d[����X-��W�!����Æ�/:\"�0k#XǞ<����h�CG�ݠ@F�(�k����l�&H�F0OSz���w�Q��3���z|+��\r9b�T�}'ܬwA�\r�nF�����!�g0�lp��l�1�+�|�h�kz��i&��u�D�{K��\\���\$t(�;���ì��H�r|Bw�D3[M�!:(�{�Z��(|-�Hy0�^�'׽�}�*����NK������5KU���jM�\"��w�]%���{1q��z���)]�Ů[k�\0O4������UF�\0�c���mZEGt�sDQZ�)n;7�<�qhlXx�I��^�V��&�ͷ�C�`,ɑ%��1\"@1�|�)�R�k��V��}S,�#!��G���]��Ex���YT��<%�Qѿ�@�����m���Jc��B��B i����G��f2����cD��nէ�=J���I_�������'����iA�&,��{��c��4��oV�%�d�2�x�e���#s_U�H�ՉW�!  =۷�O�<(y\0�.��G�'�\r����57�pV�(�þ:��}�RRHHy[��	����� 1����O\")��L�l���1�������������+<~�	\0���s���?�B@��d�����?n��~�&LЄ��?���@:@;��y���Q�>�����f���:\0�t�+j�sz�K�,b^�p���HX�?�P�\\D�?v\"�����\"�&� ?�����t��`�V?�\0���J�wC1O���#�Ɛ�*	��@̿�\0���Ƈ���/#8\"�O�\"�\0���6�Nc�ä�[�p@C�h\0{\0	�pDO��Ft��H/!h@��L�;�@���w���I��~C�ˀ¸)�E��4+���)���Eb�?]�d��\$�<���`o������?}�8�b���/�J���o#��IV,Ac��3�Xa ��o�xi����\"椌CU���D�k�YȊ�}�\n\r\0,G�\0�|q�� �.Ŋ���N�q�pN�Д�jBO\$|C�p}��4`����\\*4��bA���+�D_������X�\$�����@��6\n\0\$�~ˣ�\0��Jb݅��� U�p�X�iD\"�ێ��lg�t'���� �+x�<���N��51e���0`��B8q�\"O- 	C!�Қ�mɵ����*��f@#�6�ZЛ9���ZR�ǁ������	HZL� e����9�9�� T n��?xX\$0��%\0002�\n�y�!��e�:\$�QssA��nxK���l1'��Nz!p���.Ṇ�c�p���1@��)m�:@P�\0�1\n�(CR�5D(���P�1#	�d7�+\n��Bu��ha�M	a�\0�>�1W���\0a�4 s�-ׂ'�jp���\nJmQ����)�");}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("v0��F����==��FS	��_6MƳ���r:�E�CI��o:�C��Xc��\r�؄J(:=�E���a28�x�?�'�i�SANN���xs�NB��Vl0���S	��Ul�(D|҄��P��>�E�㩶yHch��-3Eb�� �b��pE�p�9.����~\n�?Kb�iw|�`��d.�x8EN��!��2��3���\r���Y���y6GFmY�8o7\n\r�0�<d4�E'�\n#�\r���.�C!�^t�(��bqH��.���s���2�N�q٤�9��#{�c�����3nӸ2��r�:<�+�9�CȨ���\n<�\r`��/b�\\���!�H�2SڙF#8Ј�I�78�K��*ں�!���鎑��+��:+���&�2|�:��9���:��A,I��v4Ǣ�ꆌ��P-�\nҸ����%>(�c(P����74c8X��`X���:\r��3�� �KIAHH��s�\"N�8R�0HY5G�D�W(���3���Ut���  P�9M����Vd�?�4\rC�P��bؼ2*b�3�T`��n�VM�sb��0]pG�%n�\\�E�]�8ߋ�h�7��E`���@PI��jV��T��z�\rC+���R8\r�\0a�Rؾ7��0�����l_�2dYAxPZA���@y��A�R��T �o��^CK~c����⊰{}c����Z.���~�!�`���@C�.���ޒ.�������y��\n�l��9wt\\C\$pըp��8�/�媤eyn_��������H�!fwZ��%h����c5~[�H{\$��\n��\r!��4��n���n6͊�cH����J.6�|`ӛ�;.�ް[����p����W�ݪ��>��\\���hW��Z����O��7P���xA�pUW�)������!�/�p�i�[�����~�X�\nR�����\$�8?BE�y!c�P�C��5.\nH�]=�y*\$��s����t�`��5��7a�\r\0�5�j��-g������\0�ͤ#���oA�����\"p�;��\nH<�������m!������dÙ�K�>+d�=�p)�pP	#�|�<)�70���-�����(ek��9H��E��9���������.��N�䔒�J� �hL>e<ۿ�C�`K��xVA�� �a�P�A9W�I�y�4Wj�p�W����d�ER�2�ip#)�������CD?�r�u���xs��|ϸ�AX+?��l��<H�&������T#�|�РQ�b �-\$�}Ah�:t0�P��D�9!9Sm��H�i\ro}���ƪ�P_�E�a��x�f��u��{�Ӳv��<)�/#�QC*ܪ\0�rNir��t�GNo�w>�����M�Ӽ�� DJ��Cv`�`N�a@]�(�U ��S5{��=����9N����8z��3�^<��	��	��X�c�\n=@��s�3&�ꚠ�d����Aj%\r��y\\{<#�	U��g�R`��^��K4l�!�t���{�\0��W�&��|-���U��/7yU��C�����X��R�6u�H���V�u|I�V��\nq<鼇*p��)�����&N��q��/�Rل\nV	�8���������3�<;������}_����ph\r��� ӊpt�9#%<��2i�d3�R��s�\n���kOf����9pA�\n��9� ���� I��Y����C�c,U���2�^�\0�0\$�N��qsJ�+d�*�@1:u���������kΆ�!�4;�@z�Z��&��d\n3\$����ݠC�]����Q��BVwp�.K�\\άԌ\$9�i<2Zp:a�`U�����S�3���|T!�&P���,c=��0�=���N���d�뛭6n�ZyiTTJ��w��eS�u�'�n�m틸I�n\r;��ݔ���*)A��i���1�yQ�\r�_8?�՞��7�6�����l1�ǽ����{��������c����vr����{\\��.�,ۼ��e�v��k�ۛe�~L�^��7����\n�@.s����8t�}ɘ8�C�-�ѻ�-��4�I�dO{s�ջ8��[˵�f�;}Q����s^ݹ�Q�2[�(@�\nL\n�)���(A�a�\" ��	�&�P��@O\n師0�(M&�}�'�! �0�{6���}���k�ʘ@;�px6��zg�|+����D���+��Ϥ�yJ��L#�}��~��*/}����4���|�Aw���<���wO����X\0�������~����\r�ڏ�ޏ�����Z���*��\n�ϧ\0v�0 �����*���/�hD�?O�\rn���B�PF�o����0\\�`�0f��k��r�O�H�p���h��x�pq��֐P�T�b�����OP�į�8揢���P��O�o�.��0�·\0�\r��	������PE�K����͙\rP)\r���o��T��v �\r�Dܯ���o�����M�A(XhC�L&��\"h\r,�N�^qKkb���\"��	��}qy�\"��R�`���\0�������n�+���\rn���qH�HL�\0V�%��F: ؎���\$\r���f鬶јj�B�m�Qm�G\\�蕱���nk��%\"V��d��k��@� ��!2+6��%�� �~���Ğ%� r.�R[� 2?\"̹#\0�Ԁw\$�U%�#!%�)\$�	\$L�mA-W��{@ܷ��#�_&��x���]\$S'\0�\r���g�@m�0�`d�f�`G&L\0�':x�jx�*о�D�L�����������(��q����,&����l�Nt*�\n��	 �%f(����о�kZ�	���%i�n\".��Ļ�氮�~\0�U@��d��4��'r�\rn#`��2H� ��g�6�&��v�����'�\rr��S^�\$�@��Xf>΃k6�r7`\\	�5�V�'W5�\rdTb@E�2`P( B'�����0��/��w␑s����&r.SVsє9�JJ�x&�8������v��!`z4\$k�\0��x�7pI� өA�9�;����\r�~��4��>~'�\nP��s0P��QA+/7`WO���G1�Fp暴\n|�\0P�G�Gt�I\"T�iG�O@��F�V~G荔2�\$��%��96�,7L����LSoL�h��P5ʼ�У\0����P��\r�\$=�%�nUjXU���k�ϋ�N\0��\r��)F�*h�@�k�B���5\$��56Lbs|Mo8+8\"�:��G4�ON�S5��#j�\"�Nn��c�Jt�T�%(D�U�S�]M�j\$TK`�5��o@�������rYSNR1ER�\r�����E��Xr�NJ�7��b��gTUx�M�5�*�0r�:3��	��	�2i��1Q���k�F��0��YZst�e����c\n:oH�FE��xu���#��4�S#	 	\$�t?��E(p��(�R\"|eB�X���8	4�>\r/�<�\0E,^�D.��E{5��a�܆*��\r��Z��g�|��~�\r:moc��9���J�v*���B��7rT�&��nlH���PV�6��mDw�)m��\r��CV�w���\$�u�S���wS`AD��L�S6q�k��)Jkl�'L�hB9h�� Jimn<\0�  �<�\0�[��:\0�K(���~����s\0�K̒��Y'ʈg�a��O������(��]v�:�&!`�P��xV^w����n�Ĺ��7\0�&�g|B\0(����*,��ľ�²d��7⛬t��z�w�z�\n�E\",\0�\"fb�\$B�(�h(�4ժ5b?�΍w��q|@Ƙ+���؁�޶���&Ɋ�~Nⴎ��ח�N6<u�FxWQ��^�^���;P.#/����|Wȃ8k.��/7K/w�Ql�8�~Qψ��\\1�\\���&\"ئWR��/�)|��A5r��eE�@��k��\0O��wK&�f��\"'Lm����l@��ۄPZ�����7����\r�#�o��x�`]��b̄NzZ@�0NR�,�x[P����c���8z�X�\r�?��Ǎ�?�9�2�x�}�L��F'LP�yzð\\ƙǌT�� Ť��i�N��ǀ����Tx%�xau�cw��#l,��\"�P���b�*���g�#Zud��,5\$�D��3]�؛?�h~�0\n�y�N7�b�����z�\0�a5q���k�p��v����Q����,D�[��A\\E�yK�yP#U��Zk��&)��E�9q������\"�7�����!���[��Q��Md۔�uQ�J#\$o��]�jۥ�g��O�\n�XD��6�꣢�e�����X�Z�����:���E�:O��U��b�z]�7s�����D��c��0�`�?��\\�S{�y����S�ih�z�Ei�ij&��׫e'�k����X�y f6V-Z�WewŊ;G�\$���{S���K���7	�1n��>@�iz��z�w�9����{�x;���\0���\nI�����yk���[���7{�޻8-~���w�,[lȌ�@Ϸ���VԘ+��Ӌ���ؿ���j��c�ؤ���\\qǊ����Y����'���z��Y���ݻ���˜���?a�A�:�Q٭���(��} �\n��y�#S�y\0�[��?����/�����]������M�y�{ˣ�9��=P�ϫ�O��Ls\\sWD��ػ�˱�|7��jN-�E�˕+�`u�Ƽ�\rM}��~���I���~i�ڴ���|�lv��}�Y��L1�l>\r�������9��,o�Y��9�}�����Sgg�����銼����:��u)���E��̀C���R%���~|�~�w���0]�|��\\��yϙ��y�\\��ج7й���e�,m��u���7��(T],w�θfU=����TRW6�<���Kֽ���g�;����||1�\0Qy�\"9�vb\$5�mw��Ά�o��\r\0xb�kH��|�ɚ �Z\r�h��Wʜ\\��Ա���.��3U�\r˽ؘ\r���>?2)�᩟�/�=��5��0@ƅH�~<�н�x���_��/˾3�~I+~l~�H�Y��{����Y�^]�^a�e�^h���^r+>C���bB�,����2/L�����R�#m�RKI�K�'픕E�W�1�]F�z�_]�T��%4̔\0�V=�4�;\$T� �枍{��?��לּԞ�3��n\r�z ��X?c�p�\n?�#��a�d���X�\n��:z��-�^X�!��`�:\0���y,Dl��J`��A)h�U�������+������5+�����~_���������+<�b]<m5�~'������]��')�ެ��ܺ/���P��r�4�o�{��_�ng��HF�pBs�H�1)��b��b��?�톼\"[�C<�U~<0��y�:�G�@}脬z��޺�w)}��[ꖞ���<8�&�X\"`�B�Ww��{��k��U������.���E;�=�pQɢ��R)t\0;��Լ��*��J�C^ �d��,�+d-��~�*��xpn��@��A�?�Qh{䄳'A5�P{dX�`�H+���sS���kX/��E(3=�!00�4��\rjł�Za����>�m���4����?og3xƕ�JW\$�EQ���^&��\nQE���h��j���qC�N��Ơ,y��H���β\$'@\n��;\0\\]�ϛв(�\n6ar�ǩ�u�P�/�;P�#q1���\n�PB.�6�����`\n�Fٰ�͒W�������3db�ZU��֜�=����x�a�@�=����f���Z��;B�k謀�����mJ��N�g�^���p�r����ٲ�(Ilc�������p*���A��O��U�7\\D<T���f+�TH��Ϡ`�R���Zq�[`of\\���\"�πx�|E��f�����ŰP/�S\"�_�8�-C�F�]\"j�h��F�29��!E����b[�����E�*���M�x�\0�`9�DU_�t����юq�^��(����j!���tX�'��E�_ػ�M��Qd^b��|��,�{4\\M�X�Ff�-�kN`7,���BJG5�&�*1L��4	#��-�����`'\n�L?\0)�|�r	X���|���e\nJ9@ʬ��ȥ�6q�X\"�qE�	Pm�¢N��Җ7�}	��<I\n�A�͌j��u���L+F��'��CZ�d&Rn�cI��l�\$����\"�)|7�4hCvcs��}�s���G0~#f��e�B����.��r�O!<]/�d�[A\$��)�J�P���\0Y%��F`&B����vM�II�P�*7��֐2��&l��Xo�.\0�KZ��Bq&<J�p	��e�i;\r��0��PB��H��M���L��İ=�T��X��c1&y-I�6fN�|���&yR�n0r�	�%V����RKR�d��H�� ��A���Y\n��<Jĺ���L����'�~V \"����l!d��'�`��q���>Iit3:Lɲ\\s%�ͪ�E@HC�����\nf\"����@ 1�1 l�n͆�������/X\\�DK �^-�n�|�\"�\n��8@�{�)P��(P(��s f y0��M��@�\0&b�QX�]3	�8���<��#11<�.b����f*p'<�4���)1�\0��)��n�~cȁT�S���tI�11�(\0�P,��d\"=��@�6��\0��w\\�fzY�L�n(���O}5	���W=����2Y�͖e@Ol܀�7I�N�mX\0���N:n���B��\0�k�|��,p>Nxn�xh��5�Θ	�G�d'��3�M�S\$H��1i�N�0�݀8��Mv�ĝ\0P�\\��NH�\0|9�@\0!d�H�NɥL�\nS؞�؀��*MQu�@&�7i�8򓖜��)1\0#Ljr�3\\��9HK��d�?�hg�:	�Ozvs�������O�|\0F4���>��ϾpS�|��<*LBw)�<�?9��@	3���+7�ϲes��\0@��Ђy�\$��\n(#B�'�R�ӫ���5Ci�Р4:��C� о}4D���(i<j�P�Q���\0AD���f��%����>��L���4�T��@�I�O��X���X �(�&l�')}\$�eI�f�N_% �4��i�\\���Uh�C�=D�u�����'@��v��8dB�-%(�T�%�7��㖨�f\n�X\0m��@C��0��I��\rɽ�w<�Q��hS0�9@��I,t�')˦\0J7��\r���\0�!��ƷW1\0���~�_��\r�2\nf܊����@QK�9\r���\rXi{/�~�������2Z_������2'*o����	Uس��\0�{�e(\$���i�M�4T4�4��}6)�����mV}A�3Q\0��l��/=@QZ�:�k�N���|Q��&��4J���R*iSP��5��\n���t@���_�)��QI�MXo�ޠ�k19B7�=����\0�ɷ̆l�|����[aa�.�Ԩ��\n\0�49�Βv@G����PO'�ZH�X'VZ@T��n���g�7�>�l3c�D���XZ��fj�Y��_�mX)ʀ�zG������\"P2|\0N�j�X�����{�\0�0d�Tl�� \nq;�߁:bS����hfy���)�Q+jSCQ����yS����0�H�q�`	��`�F��l�pT+�y��r�jZ�K�c���WmA�:��y�5�\0P&����zW��Z�)D�	T�vD�V��3V���F�ȭ�Rj֭��p�v�5�)���'X&@.��C@�`�pT��lSw_��	��#��:!/�5�rr��r��;�F�&�M@�\\C\0\"�\$��(T�X+���\$t+�r��84Xf��I���d�#&��cI�P��Z����l�̱(l��Z�����6^����3��|��s�\\�=��E��r�����3���w+�(�,� �c�����^�|�:`�h[�Uah�t��Z���Զ�O;��qy�v�\\��A^����x!�j2VդմE��d�0�ر�ְ4H����Y�Hz ��0+���Rj����f_k����AJ�j��[��,U\\jX�X�=���ZDw5uˤ��՟n�	%'��}�&�p&� )����q�X��\0+_9�C)�Iۊ)�R��짇`ĵ��@�/!+UAf�����\0R�=�A�%�r3{�\0`%z0�\$�>Ѹ��=�h��]/�6����4\0i�_2�U���e���;:J�Nu�V|�@�	���G�hU�=Qh'�(T>,�n�?#��ts���f�=c�Vvu`�U'X)�M���Q��p�p7פ!a��J�l�0@ZF�E��=ClJd�������uAJ�tȪp��0��W��Uw���Ɓ����Fa\ni�ݻX��J*���o*6����k�8�N��[*�/�u�MCUMaJ�޲�V!���U�!+�Ŭ�p�xh��<@B�����] ;���  �u����_2�R�L���:�߈	�4�.f1�@b�%\0���!{�=Mۿ�|��`�x�	\nтo�!p)_�t�Ⱦ����#��p�a����i\\���3D���.��񶕁Y�2�x�F�g�넞�8'(�0BJ��@b�Z�n	p\"Ee9�����J�0X3���b�\r; �S�1[y�=(73��	Ñ��2����*��l0��!V�lr�Z@<�����T��Km��XiF\nU��?fT�\$i8GS)L\$�8B�iD!\\B#<4aT���+�@�-�7\\��x6�p����?�\r��N/黰�%L+`�h�t��<W�>�{��~(@������R�06ǞP+��{Esö\$�*��b�	�&�#��[X̯����&�����b���n���S��U���l�,0G~�}��cUf'dCs<m\r;�<���*4������~�ǉoam4�]/�0��2c�Fxw�H;R��qﵾ&	kX�?AIƠ�\">����x�?��,P��b�iū�)c<\\+�+�^n3�ő���|N'!+PG�N5�T����BK����!�1\":�2bP�,�Fy*��NÓ<a[&�3���t閇7��\$\\�qߔ 2ecIn�T�y�2�c_	@\nu�p ��x��+��X�Uq��<�A.��Kʕ�ʎ!2�?�8�fr˗8��\r8(��p^�!����!�Y�=q>��\r�v-πٗ��	�1��g�f,��[�,e'ZX:2\\H�������y<�1)[α�;��D|#�H@����LS�3��>;��]2X�vj�.GE�Bi+d�%���,Qr%Ц¶*��I����5`�t�-�s��b�8E�۾���e\0=�2�/���Yq9-eZ���1\\���^�U����`&g�WJ��Y�hK]8W@;�p��#���#B�ynqĕ��\$u���Y��!�\$���)(rX@/+�L8�O^�ʔp6,���Ѱw�<%MS�S=Z%��W���\r�\nHy/�2+e��1�E��ɣ\\�Uw	(p\n-���I��S�E��ZiI@1	����`��\$�44����8��>\0���i�M��ӈ4��Q��j�Y��y�p#�x�`����m'�Zڂ6��za�S�i�&���ʒR�>z�\n����{Ti�P:�����j�Zj�T�t�R���@:����ޭ5��h�j{\r�f��r��\"�x��|�cx�?�r��k��p���.�r��>tq�C���	k5h��a��\n�U:y����xW8�k���)3�!ҋk�^�t�}���-x5�^��B(q@��Qd]ƴCr�\"kw[&��u�s��W:�ꕝN�@����d�����=���+Z9��N��������@�m���{-%>�H�����R0*�7K/<~���,js���n��P\09.�͵����Sj\n��74�ݱ,�\$;E����-���m�\0*Ȼv��7�c;u&v�ֲ�37�ء�y(��t��n;J���A���G4�hf����R��@5�)V{[�Y��m�b�����6��1��p�J�6�����;[�.�Ŋ[r���b9�V��0���\rw݀��C���w���VT��&=�,�h��zH��)���8���E�sI�t<@e+0y��nj�T���Ʈ�w��~�d�J��σ��@�)c��+h�,���ث8p��L K��:Q�A��og���1�o���?I�Z.�?�=~�����n����kF�!n%/�E�t0'̔�P<Ƶ�G�qP䴓F��xA�q�����⃫vn�`,��cW�{�9K��߇{|�+s�<��4Z+צ�6�P��PL������(L=�ծ��jf�h��>)�A�혠�q��pK̆�����Ҡ�~�6d0���Y�#y�}�tO��R��CS�_�燜���|bHw�s�O%U��w�p��N򈍜���Y]�����U\"rM�t���\0jxoW�D���[[�M� �y��T��8��@�9��h����!����̋r`����\\/�4�u{�d�8Sǡ�sb�\"� ����i�;��ji�ǿ�k�j}v�i�74߽�J��9=՗54�0'�?���(�7��qg��� t	��_����[����z�ӌ\\w�_>s���_����g\0�����V�|\$�p��-��Bs�X܇�.���;��3����g���PCD���Gy1���j\0y=M˞;F��m(�oD7y�k����b�o�=�!:�.��%C�%��t߿���Xm\$��6&�P�bj���T�u�*�Tx�\n�d5����Νt^d�(S|���-q�������\0����(tXYQ!H�F�k����0t�����4H|��oNo��N��%�\\��w\"0��Bq��\$[玙��f�|q����7~Ey���X���q�ר>|� Ob*�\n���Im�c�EЮ��e��6e���v˟L���nɩ�Kxx~a��ǜ�f)9�˟]F�!�s�I�iN�h~�Ӕ���R����.������GF������8��/�zdC�f�6-�#g|���t��;���4�TV�)�kV�����/y��C������9��07h@����).Hq��E���N}��K�+��Y�r�\nb3@��K1 �)�l�A˧�=#��HiL���ʄ5�o�A�������B>Y�@\n1H��!+��ȣs�0�GH~^7�ـ����QrI�8���\0Ì�`��\nw�=0A�y�[Q�8H��O���g m���#ʮukHB����#�o�uf�oݐ�k����^!��p{�}�����4Iv������?x{���CY�-�ICמ���Ȓ>0��l\r��\0��|Q�1��5L�/���j��3;�Lﴷ�^�{�U�n(}�����b��W��١��+�>��'�����{WsC~qM;P��R�v̢�Ɗ�:p���Q���G�� 7��a�;���_�z���)|���:�g\0Y�*�/kė\n��>U��0�x�H@�-=\"0H^U��E+�x+��#�;���1��k�y������Th�:G�&�-�!qs�3^|���xW�-l��!׸��F��X��t]��BXY;Q�L�������0cI�oj��A�Q�����L��GG��%\$(wҹ�Eh�XK�a�����o��b����5��������sA���t/\r�ݒ`�w�7<MP��*yY�h>P�r��=zjW01�g�dl�iD/�}^V�\"b��>������X���Rn����r�.0�����̙9@���� ���ۮȷ�;�&�^�2��hYXh�(���b��\0�؀�/�\0�l�:0��܂�?���t%�> ���CG4@���@�E�<��� �h	O��0K�\0�@r�[�\"���)�A�oX�4�z� �NR��̃�`��j�k����P���� �]O�l����2\n��*�b�5Dn����2��(�\$��<)�Hac:�ϋ�/�8�i:�n6:�0;�<1�LP\$ أ�Y���\$�����:0�����������jI�P�\n�rL!w������N\0�>~/`4�+\0��<��^RX�U�6���:\0��bN�莌*�.�N��pxp_�� 8\0Xo�Kb蘖�|�l\0Ɩ���)\0��P���:<pl�\n�@�A�SPP���ƚ�\\�� A��03\0006 ��(�.���pv�}��9�z������@N\$ņ?5�ㅟ�i+Av�8`��y�� ���\n;� � ��V���p�߀�\"��j���E=�x�0d\$�P��V�	x�X����g\\?\0ePaAJ/`�pS��LЙ�	�(PYBq��ОA�!.�b�Vs���\$�	|Pf%gzT�A���k���0l%.��l�5I��+�8I+������BH�*�p�Q\n��G^B�	rLPUB�q��hB����nB�,b��4����Y�`��	|#`.B��.��B��\$�6!�*�s\$�#<�B%�����Q�*�e	�N���rL��\0�N1!i+\0��Ѥ�\"�60bCgaN��\rPUCq�/P�\$BNIB��,%#�-�\r�+e��h&p�º/d+����P�C-�B�D;D�C}<BB��:0�Ï�\rP�CP���Bi�1���r���	�'�����c[��\r?P�*?��³�+p�A�,1Q\0�L@�qDh.���Y�P��������`0�@���6Q�b\n\r�ޕ�0��\r\$1��@�����=ĺB֔�� -�t�X��B��C1��,�+BI�����%���	��ֳ(���KT\0��F@�/�7X�\nD��`���`�[Ε�p�D��L��D��Q\0\0��N`3�^�\n@��%�	9��������\0���[� �	�L����MA믤Q�2Q8)��HW�GD�b�%\np�	�S������(�#�t�����D�HQq[�f�]�\\'(B�@�^�(CCv���V�[Ř`(�^E��Zc!�7�ÑE�*�Y1mEu�Y�_E��\\`�E�c,[1e���]�`E��\"䆬86 ��z�q�]�hŀx�@����OE��\$O�6�}�Qq=����!\nŐ<b�:�Q�c��O���'�b��\n�T��(|QqF��R`&E*1�R�!L^�f���`�`ņ�c\0^H�!���yg�|F�\r�bF���l�<Ɛ�cqeF��i�pF��hϖ\0��]Q���hqbƨ4O #\$=\$g��F��k�F�p.�<\0��k`���n��Ʀ��O(J ��[q�F�j1�F�4�\\(���3\\Tg�D-�T�CA�+ \r�7���M���x��	\0Z	R�\0005��p\r1�E\n�V�I�(;R��~[>`3�6��rp��	�%���-��Ж\0�	dCñ�(9����A�x�@2���!���*`\0002ǲ~8S���	P��Aڏ�/ �#揩�-�8�1��n��t�*\0�#O���0=0	�'\0d��	��( x�G�  \$��\0�H��(�2�\n��3��f��n�� 7�\ñ`7G�F@>H.�5 >?��B>�<�z�\$`��>0�R��tY�����+ �F��Q΃� �l@>\0��̅1�0��\$V�ƴK�&\0���@0� �H�>4��o\0006���q��8�1�P=9�\n���1�7�\0��D*�<���#H�\"1|�#��!K3�=~=nm�H��,��#�,{I#�t���~)� ֓\r b6�I�!1g�E�/�r�\$���)\0�����6�̎'�0v3g@��\\�@\0!� �3!4��HdL�f�_��9r�,[�x�eb�f �HHhpf1~�D%,��a)�0�֥�%<�N���c�>�& K!K0�-��;��H0���&�܏ଛ.�|#���.�@3��!K�\0002\0�!�07�ʿ�eJ9h��\\=d�T��\\\0�Q�DI�d�\r(II 8�Dr�Z~(;b�J8��C��U!�RH�\r�� 3I��ė�_���A�N#�32���1�@��{��@�J+#�=R}��2�-�~H.�!`:���T��^�!K��)�Jл(\\�-\"#���L0	�tʃ)d�/!�r��]8��ы�.SKҐ\0�H\\wG�:�)�G��ʘ����5+�N	���N�\r��J�����t�0&�(\n�a��!I�s�!!d�1��0���D�	2:�5����3)�W�J%����:0���˂�#Z*�H�-k�״�\r�R��<-|Z��2�L���\0��dx�K�Aty2v��L�A8�\0�K��3�+\\������{e����뼠�����!ֲ���\$�g�I�.���O#��򝂵)�>�\$g!P؀6KF�#�q\n�W�P2N�zS{)|�/'�����(k����fQ*��JdJ���>�����!0�2�\0�0�Rbr��4���yJ���\r <�^�H@���S���d��WlT���\\�T���T�F;q!�MC�x!\$���9�_�Ay?���z�2|�I|��.��2�)*c�BR��+����2ԭ`K�2�\\R�˛+�B�0˙L��4��3d��)���(��G,��\"�̈��Ғ3x�S?L�3<�SAM.t���2�.�� !+�%8!�J�H��@��K%�����4���A��3���N�*d�,�M4�/ſ�Zc��O1�\r�\0\0�(����M</���Q��5̭38J75��S_=�5ܩA�-�0�:)~HR�Qt�͑\0\rsdGsdL�.���[6@��5��6�2�M�̒�-/��	\0c�L�Φ����M����e��7x�q�\0003�b�4���4`1.j�@�H46\$W�6\0�\r���i�\n����y\0�8h;��>���Q_<40?RH10�G�(L�fQ5/��xX����s���bL�`<��8{�r���S��ɭ9@B���8x6�'��N\n��R�Qf`���8|�\n'��ԟ�6T(2^A*/�'0E�t�*�x�ɇ#d�g�H���RH�(B��U#`\n�\$��c��\0(#�9�2\r>����\n�`Cr�K�HP �����eP7��\$�8҆�6�����Y�6�|�T��h���RЄ,4s�Ǌ*�� �K��)Ds�JV�ԟ��5�\n�MH?�/|�\0006@.L�h�˨�>��Kv�L�\"!K(\n��I�����Ov��)I ������v6Ü3��>d���!����O��f/�K�����Lv���0�	T�\nؑ,�,����ܹ�6�M�|��Od�!'U=�C/�Jd*�ǭ�F<�Ӹ��!H;�ڂ;љA~�^<aSe�2֘�=O�1� 9L{9��,:�9��,�7����-\0`�\0�`� �u:1| �e1�V�L#A��	�Ap\r���`���7)��@��,dġ��{+�����5�\0b��`���Lh�U\n�Цe\nS�Ζ}�\0��JA��!2\0�A,u\0006PKQ|3�O	PS�68\0%@0ɦ\n�L��5.ə ��\0',��<��)@���?(P5�J���g�I�\rd��9\0��T̒��)�B�LA\$c�b)��\r��X��C�O�(�\$�N�J��qHV `\"M�8�t���\r�;0�3|QC7��XLu1}3X:�bO4]���U��MaE�?T=��%0�␂C!�T��L�H��`��*P�8K�/\\��K6{�SY+���͐�����KQ6@6�V͑<�eI�P�6CS�zO�\0��P�f�����>���80�����a�\"gI,gmF���ᙔr��Id�|�F�R\$� �ȋH�!�2FܦP �HR�	�	�8��a�5����Ih.�QL�(@;�\r�Z��3%\0=)@�B�*��H�?T�\0�#�����I\r���Jp���L�JU%�eR_I�&@:R�%�&ԪRq+*��R�Y�?R\"��J�*R�3�J�iԬ�\"�,����F5&j��6�.���'KE+ԣ�&u)T�RML)T���LD~ԹR�E��SK�	Ԥ�K�1�!��L���7��D�c�K�-3�4�D����A/x%T@SV�I��S^= ��`\r���\n5#��0	R2Q�8��T�I#�%S��w��ނ�|�R�7Ӝ�/E��\0��������~0A/�\$��ӿ@82L!d������t�Y�O�|7<a�(�ʮ6�4d@\r�O�;t��A�?�Q�iIᏐ�|&�,�<w�����-�����C����\$0ۀ�+�����B1����P�\"�����*��#\0�H���E�JO����5�\0\r���]O�첈�^\rxz�:�^	��_(���6M'%[�-i�]F�#ғ��N����\0ĕ�LN�4�f�#i:S�X�@4��%�[�ʙ� �=��&#e���Hc�\0�8�f  �p�4�\0�ag���/eD,Aڀ@�bE	\$P��Jj�2\0�*�:�� .����24ࣨ-P@u��=��)�\n=x )\0���򔂀�=�\n`+��H()\0#��x��&�=:ki�&��)�H+>�U��ՇChi\0�OZ~@+��x	�\"�.F?B�	�B�mA���C�[��D��mQuF�j��R/4��T�uK%�TMD�c�W!B\0�E��Г�`ɏ=���&L=�cϏB(\n̨[��0�U\n ��O�ό1�EUTu]�\rU�\\D�[���\r��G��`1��'X�d��U�T�?C��H�sV3WEcuu�!X��u�\$���,B�}e5~�U�P�1�.��'��A:>p� �è��i�\n���S��S��E\n1�\"�T@�@#��lN�1�[(ñ:���PQ�U�l4D�[%l�Є%[X�u�D�5�b��	�kH�b�D�pq�W]p@9\0[R8��Ŋ�(-R�N��.��.Up�TW%\\�*U̦)\\Ⴡ@��C�tU͟�j\"\0<WRc}uu�u]v3���Q�s���!]}��WX]�w���.�mu�<I\\ 5�׹���r�����d�h�^usM��+ 9�Eu��\nj��W��y��^��U�W[_E{�҄�H�>��W�A�~U��_�:��k]xٵ�\0b(�\\B�\$��89�\\�d��X4��Uمa�H8W�#es��^�A?�+=u��\0�`�ĵ���|\0�z)�u��p����Q�u��)�`����]xOu@6�ew���`q����9��?�� �W@h��v#	�@m�\$�X��+ <�~(�f\"�j�D���e��1X�_=rA=��W9�\\ 5��c=�E@�l��1X�̐: �^��\0�\$=bM�s�خ��FV��b�\$�Y	b8W�C�T�s6%Y ��@�(-��)�T�b���/��D�ؑd���H؜�L�`�@d	VG�=eU�������vX\ne���ZWF{��MMY9eM��Ѳu��2GN(��-(�+b���	K_fX#�f�9d��c�vnRb��%4'-�\"�\n9\$(J��W%fՙ6j@7gvr4�d/1���g��׀�[e��}Ӈc%�����c��N�J\$��aa٬N��	Y2�dS��b��Q���`\0Š�W�\r���r�,H��\r��h�rQ���\"�����i\n��F�KK�;���iP+V��iu�p�c3�]֚Z`8j\0�-�����Ei����+d]�\"ىh�	VLϼ�ŕ���f}�1��j��6��7dM��\"XYj�W@١��A\r�e%�\0���j�b�tY�:����Z������e�v��(ծ6��d�A�Z�k\r�i�E��K�6���l*�<J]hu������ �����6[#lb�;��bճv�dtt�7�Jt�A���و��8����A\nlxҖ�Z�&�!~�Ҙ����[OmH�մ~��<\0�m����Z�m��,�[Gd�u�i-�DqZL��66[�nE� �ۇe�j1�[l��6ޗJ}s�/[�me���#�m���J��v�F7n?Jt�[R�\\6�Yo��L��c`�5��o������vm�X�-��6��a�c��Zsl%�C@�[����Wa� ���[����W[Mo���g\\��/\0�o� \"��sbM��9��d�ģ�����5�8<v�#4�m�,}ن��V|Ml���S\\����im;��ᆞ���+YQrEȷ%��rj�7ۯa}���Z�MrW\"ܝs�3�؊(�B�4��q�76q�Co�*��+q�����hs�v<��oՔW-��r�7?ܧt\r� �صt-��>tH�)�rJ7\"@w]Ŭ7IY�pmҷ7XYt\nG��]\rp\r�Q\n8\n�2�ѰTezP�\0�=�v�NE�#�ܽu�̗E�iu-��N��s��^�.�}��Cܕd˂2�]1f��.g�r0�4���x���>WvE�7.]�j��U�\\k4�q�!��t��wI��F]pwq�5w)\0����q��p\0\"\0_az]^�7v]�[-ܗ<��vޖL7�c]�a7?\$�-�7;^w5�v�]=va�g�v����	k\$w���;Z�!��V�ql@;V�0�l�\\�[�v-���`X �xW�_��\"��n� �<^uw���^ew=ӷ�]��r�u�p��*�u�r�Z[]����G�>é5C�̻���(0*ɒ����V0W��	�����(\nՂ�[z��`)^�Zkɱ'�#��\n\n^�()�����BiW� �\n�\n�ݦ ��7���<��'�@	��^�:�3\0*�@���\nЏJ���&h\n���|� ��z��\0)_\n=젠_D=h\n5_Z���`*\0���3�.��(\n�'����!h'�(�`«><� -\0�{zi /��L8�`/�:�I�Uv�@Z]��<����_{�0���~=�`�m{,7�E{=�+�c~�l����}5�W�_�X��&\$Jk���1m�7��O{%��8hx`\"�������'{}�7�^袍��Ѐt��@\$��������z�\n���Vi�߱{�i�&J��W���|�\n	�UZ��w�_�NW��(���:_R�R��3}���`&\n�iAh0��\0�	�|E�S����_%���_2֠�7�߶8�����|{8%_Y}pc�\0�}��k�_k���_s}����_~Ώ?�`	׻�{����L�pf8;\0��^8\0����_�E`�\"b<�w��ߍ~@	��_�-��Jߣ�̀��_���-��:fW�_�~M�i�a1~~w��=�&�_?�Zo���uU��7�|�XJ�aUrb�d��pZ	�ը<�w��Й0f#�U{~@&_��	�*�����Ԁ�{zw�_3�`�#��\$(0�gU��PX?��Rk`>+\$.8g�Æ��x���lI�a���8[�{��X8a����I�����\n�\r�c~��aͅ�Wx�?�\r�8&�L>!��^��|�ڏt=�a�`=��؍߫ %�����d	��M}x\nW��+|-� ��U��x	i�aC����W�a�-�s�߯�����'f(Z�'}����צ\"^'�b���\nxkb�=5����h�dCݏz=6\$C؀�&+���~�%8�\0W�`���'����`�v&�Y�����\n&0<�cͨ�<�	 !M���\0���,8�bɊ�\$�ď?E�X�c\n����U��&Aha�10����8���V�F2ع�e�NI�bq��3��>��\0V�..�؀W���ɗ�b���#a�=B��Ϩ[�P��*=�	��*���踂`7���q+H� 	`\$�U�5k^�=�Z	�b�=����}�X�c��.(�����d�_3�)��_�3��C�b�\nF1���O��,���Q���yc���.�#�c���	���.A��`>���5��L�F9�:<�+u��ߋ\"����V�[(=>X%Ҍ=H\nS9a��x�I�vE �a[ &2��dh=PZVU]U�#��d^\n����7�FG�\"&���T�&������F?���\0&Hy)b��K9)�w;P\n�\$d���#��bˉ&J��g�vK#�=�nLX�㍓H*ا^ۀ�� *'K����ቍn�,�`�:�:\0V�	)�&!|�Pز���VO����-�ԧ}��{��b	��P�Ee#��O��cg��P�+�Zrt�(O{��r����ByL�N��2�2�(�&U	����\0X�぀��k��M��f5]���VXUU\\<�p��aH�#\0�=�Z���XvW	��,R�`���)�f���6Ucᄝ0��,�b���ըf'`%ᛔ���e݀�^8\0���^����.\nX__���P\0)��.B@�d�va9)'���N�����O��+~�+��+��XA��-��	�j3��	��M��aX��]�\"f)ـ�>y:�L�\n�Ņ����4�V��X��_��p��0=-�@§z��UY�b���\0����ȸx\n�&�jX�&���*ɋ_r{R7�K��i��+q3�kw����8f9��{�6]�a�����V�l�b���>jy�f��y���<��)���(\nkf�j�ԫ@�X \0Q~���@��F8C��L0�@*\0��`���^�~r�x�gD�6t�T�|�x��# >u���	�e)YϪǝ�u�����Jky���2���&��eU�{�M�;���i��U��L=�u#�g�X*t���+�0׾ȁ��	 '�����UY�^)5VՓ��*X�-U�a\"�ǆ���8`�}a�ו�U�{g����d�n-8�\0���t9��<�L��d��h� ,��=I0�h�8���\"��:���8	ԣc��3h1�\0i���z�8���6�c��d���8��1�hi�ֆ��'���X�9�|��̏_��a��K���)�߯Xn�9��Z0�5�\0��ƈx�\0�<���������\nX���.U��W�ic��:�:�������	����Q&����\nZ\r�<��Y���փ������9Vg��R踶����b��Z��b���*��I�G���k�����c�.���rmP\0�3�Y\0\"'���2��Uj�I�iI���9���]W`+��L>��-c�<��|\0���Z煥�\0��e�{�󀉎N���H�����=h+�&��@�1��d�S^��h�y=fW���Ua3~��XN�;�B���+F�O�va\"M��wg턆cȀ����G'����Gi�b)���@�XC�f<�\0������``�ڴ:�c�<��p�6<Ɗc�a�X^Y�c�=-ax����CRK�Z<ƛ��cf%�?�����n,نg種��>h/�x��H筑Vye���YWVZ��}l���\0��-��j���O8�_[�0\nY\\�Q�~��#���n�Z����>wy�৪��Y_[���X\r궚ޫ�|egUj�:�Ս��sˢa��\nZ&ȭ\r`��.����I�`{��.�n冭��\n�q�ƖW�d.���)��a��`>g����X��}�8򸋀��(�\n�.͛v{�T�7���,�3��d��g�~0�ؔ��	<�{�<}�3��\$�ϔ)�8�@�R34�:���䵥]��~6U!<�@�jZ� ֺS���^��ӈ��B�~ 5��ɳ,��S�,(�M���9p 3��X�/Z��u'�:�F�p\\�)|ƣ�X\r�<�Y8Xt脕!`6 4��.��6���+Z�P۰�n���<�0���&�b�w�0>��Sr<�@6�d\n��z�lO��FRlO�;H5�ڝ*��L^x0�>g6�s�/�q��Ye\0��@<�z=\$�Cئ���\n`+'S��9��X�8�_/���i�j��	��h7�5Y\0&�c��w���a�֡�E�ɟ�Xyfu��b{5�ͳvG�jg�n�Xd��\n�{��lǫ�͙���~��V����	?�3�����W}���R	Ch0�>R�=� 7����9�x��>S�\"ӂ4z����Ҵ0f\"`�1��:愸x:���x;Z�d�֔��e��K�;��@�g�TML��7N3R�k���N(i�F�;)�Orn:Ӷ�\"�>�^#�e;n�!Q\0�/ԆfU\0\"�@Rp6����B�m��0��	���!�Y�r/T��t�1�V�eOQzBr�9,�@9m�!��t���11;�\"cmMu,S��<ŮW\"�yv�K)V��m�V,��b��k�\"�1���hx���F&����9�t��?�܄Iq����\0g�5�MK�!,��?��!PSQG�e�@��a:�b��Hd@(: ��Dd��FJ1Ḣ 8�w&��@⍅'i�VmL� E/�[R�R˻Z�n�fՏ��ٴ_�G�hl�bŇ�����l�.����3%I�[c6�� 3k�,�ID���X%��JSox�v��K�B�q[iD��a�n�O@�۱m��/�|�iK�2RjfV�r��ZEJ�n�g\0_�H�;PHD�n�-��i�Q��a�)�)�Pg���L6����%l��k�:��s�k�1��S�m�NF���o�(��SJIe:��ԺJ��=l���F���)ﲋ\$x��ͣ�>��Æ9�X��o�<�'��mHf!���D;�\0�f�SA��r/�;V�l<�e�������ػ2J�A\$��k�U������Y����� �	d�\0׻|pP�!f���Z\r@����O�'_x6;\\4�%�Z6[�6��t���KŰ#�u1|�2�XOo&�6~��Dq��O<�<�:Ӷ|���p%%�ֳR�&\r�*oʡx\0C[ʸ#���:lpw��\$KL��;sh�`�aRn�z�;ϔ�;��|8L����=OEǥH�* )�-�T�/⛲_�H._%�����H���7TH�SD5>���S�_��cr~�y�E�\0�*�^���,͎�FS��=�\0�#�>��@c�E���MA7o�\r�R��q�{S����մ1�'����'m����{p0f2/���<m:-HƸ�%N'[P�wd��f��\n�5<t��	<n��5�F3��+�b���*�q�ed��ƫ0��@�S;�鞒d�����:�;=>؁=���#������N�R2�`�F��d�\"��wQݵUN����R������utvӡ\r�'�K#�����O\0ɰ씑��r��'�r�H�.|��;O�K.8��L��܄�HT�|sr��̃IMr��/4<䏦�<�r��Ɩ0�5n����ln,�l?�N�ࢃa�f�ܠ��!f����o~�|���@2/���<�����%|���1~�iL�	_1 5�'�}���r�!�1�6��Eo2��s��.���� \r��r���7<����2<�sn;r5*O��lLD�F@�l[͜�;�]ͦ�HaGw7�5�Y�̚���5H��\$�A��\\<t|�A�RQD��!D\\��I�qv@�}�����_��lh�(����SK�i�)|iwT���μ�O>�,a����>T���4|�s�d`#y\nu�,���\rη@A�s�8�=��t�d��Y�w&�		N�B�h�R��2�t %��q,T����wM��[!O=��x��NHJ@�C6�F���~�a\n.�G�q�\"'(���q�\r8CvhJ`9\0�/���|t�&w@�&��+�\0����]���<bt���?�DqO:����</a��X�!�8��YE�U�E��l��7G=��aL�t���@1^����><�ЇQ�X����܀	\0�}?CwԜ�]�=�EW����\r�������NR�+�N��#�]?�1�oG���}.wG����}%�6Q�����#��@.��h;N� <P��䐠1��8�t��⡼�u�πI��M��!]tU֨D�7q&0ܛT��z���\0�qH؇tI�|`6lIe�R�����M����^�.��5��u�e^�M���_\"���7_�'o+��0��s��Q�?�F������H�݋u�XR+�J̢O�\\�\r�7`�N��|Et��H��w�Y�_�ݻOa#b@��G�k��N��0�� S����U��������h7=\$�_�0c�x��7e2qtD�p��0��u�m�b���ر*]�^蝍%�7N�?�l/P�Z��wO=���gnV����ŝ� v�eթwn1a�^]��]<p\\�ޜ������&AzX4#�ݯ7د_=��k`Ћ}�!E�������^���Kطt2_�Cط\\IX3ط��ѝ:%՝�w/P�Q�.ę��Gݬ�A?�[��ݓ�'�wd\n9p)�Ƀ�r7vj[����@!��w^�R1�ڤ7[�����e;���q�m[�wkش����/5٢r�, ?b�']��r��[\r�/5�ݰ�ׯ�r�.��%�U�Zv-�l�6~��B�C��������G�h����S �ougM���p,�O?������D�d�mw�?]�u!� R�4�V�_!�xIw��^�H9_=>�}�0��ނ�)��=�H��O_=���dء���펝� ��d� 3��]�������_6:x�Dx7B��Ӽ\0�]�/�=�vI�J<A���X΁,M��e��;G\0002x|&\n�tS�����c4u��}��pv]\"9x_Ob�'S�K���(�K�a�D��X���Sm�Ǎ煇��3���_<;��T��M�����M�\rHo��*��_������-ޘR���'N�U:��׬��H�ڪ�%\0�5�g-t�\ri�k5���\n���`�Hs��ͯis�~qOo�\r�^m>\r��O�/f�&?w���	��r�3*}�t󵉏�<���wO����\\e�V&d%}!.����{J��fם&Fx�8+R���:�������,x���\r�^���2u��V]�qOs]��x=#C�I}��e���1bҚ�o�t�J�I�wP�\$v��<Fp�<���yN�\\�\0�=����+5��Pl��\nS�ȳ���Ӿ�\\6탿v��i��9q4�g#��|���Ԥv�z�K�޽��t�/�/��5���j_I��o%!7I��t��Q���k�\n�M�wLM���pu!0K��\n/W����]ڐ���[�﮴jz����)��}\\X�)�O���>}��S���8�@\0�1�G�{?��yU��a�G��/�/�K�~�K���-��O_7�}�*�Oa��{z%���/��,Ե�\0#�S���=�`�7��aZL����{�H^�>�p��7EO��\"�����/���{���=TN�뽏�]��G.Ԡ���W�������?]�O߿vLVFʝ�{�����J����Tmio�����@��xO�?z�/0O�Y�|.P�b|+ۏ����\n�JaW�/��+!���?���\\V�_�W��|��=�����O��ʆ\\�F�hab��oC��@��Oq�|��\nz��O�2��j=E�Q�I��������v07�4͈ͮ7�=�z����2�M���=)��M7[�!����j���J	��}�^�w�����m�hz�x9�{�ݶM����A��|��������>o�)�0����\"�Bx}40�K�(�\rmݹykߧ�1|�OnP�t�謔�{������Cq�ۏ�\\d�1ҟ�a.u���]t�7�cn��Y�DEV��d��5(}�	g׽u\r������<T��\\���\$�cԫ�*T����֜;�[���q{���]U}ն	�_t�����5ud���kmq_޾7�K���>�+y{��&�a?�>N,��5�Q���^��}�ѡ.}aEr>��+�̧@������n���?ce|X}'o��c�e_�vo��{��}-�x�~S�_���=�wn\0;�=��|��'�8�����']��{CÏ��n�?��[�uI�ֱ4�)~���=�uM���=��1�]>F\$�sR������O���\r���Mw{�/I��]��?��5�����N���������6�u���R��yJ�� t��Z\"��p�t�������V_ȩ����f�sۇo�z�����f�ɹ���7̫���fV��AY\$���|��z��[n�P;��[�\0��[��/��eCҝ���O)w��U�BV�W�\\�����p�T��]����_���	�G��#�_v}��5^��M�/������\n�D���d��K�\0B��\$��Qz��j ����MD)5�đ�4�;");}elseif($_GET["file"]=="logo.png"){header("Content-Type: image/png");echo"�PNG\r\n\n\0\0\0\rIHDR\0\0\09\0\0\09\0\0\0~6��\0\0\0000PLTE\0\0\0���+NvYt�s���������������su�IJ����/.�������C��\0\0\0tRNS\0@��f\0\0\0	pHYs\0\0\0\0\0��\0\0�IDAT8�Ք�N�@��E��l϶��p6�G.\$=���>��	w5r}�z7�>��P�#\$��K�j�7��ݶ����?4m�����t&�~�3!0�0��^��Af0�\"��,��*��4���o�E���X(*Y��	6	�PcOW���܊m��r�0�~/��L�\rXj#�m���j�C�]G�m�\0�}���ߑu�A9�X�\n��8�V�Y�+�D#�iq�nKQ8J�1Q6��Y0�`��P�bQ�\\h�~>�:pSɀ������GE�Q=�I�{�*�3�2�7�\ne�L�B�~�/R(\$�)�� ��HQn�i�6J�	<��-.�w�ɪj�Vm���m�?S�H��v����Ʃ��\0��^�q��)���]��U�92�,;�Ǎ�'p���!X˃����L�D.�tæ��/w����R��	w�d��r2�Ƥ�4[=�E5�S+�c\0\0\0\0IEND�B`�";}exit;}if($_GET["script"]=="version"){$o=get_temp_dir()."/adminer.version";@unlink($o);$q=file_open_lock($o);if($q)file_write_unlock($q,serialize(array("signature"=>$_POST["signature"],"version"=>$_POST["version"])));exit;}if(!$_SERVER["REQUEST_URI"])$_SERVER["REQUEST_URI"]=$_SERVER["ORIG_PATH_INFO"];if(!strpos($_SERVER["REQUEST_URI"],'?')&&$_SERVER["QUERY_STRING"]!="")$_SERVER["REQUEST_URI"].="?$_SERVER[QUERY_STRING]";if($_SERVER["HTTP_X_FORWARDED_PREFIX"])$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));@ini_set("session.use_trans_sid",'0');if(!defined("SID")){session_cache_limiter("");session_name("adminer_sid");session_set_cookie_params(0,preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),"",HTTPS,true);session_start();}remove_slashes(array(&$_GET,&$_POST,&$_COOKIE),$Nc);if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);@set_time_limit(0);@ini_set("precision",'15');function
lang($v,$F=null){if(is_string($v)){$gg=array_search($v,get_translations("en"));if($gg!==false)$v=$gg;}$sa=func_get_args();$sa[0]=Lang::$translations[$v]?:$v;return
call_user_func_array('Adminer\lang_format',$sa);}function
lang_format($ji,$F=null){if(is_array($ji)){$gg=($F==1?0:(LANG=='cs'||LANG=='sk'?($F&&$F<5?1:2):(LANG=='fr'?(!$F?0:1):(LANG=='pl'?($F%10>1&&$F%10<5&&$F/10%10!=1?1:2):(LANG=='sl'?($F%100==1?0:($F%100==2?1:($F%100==3||$F%100==4?2:3))):(LANG=='lt'?($F%10==1&&$F%100!=11?0:($F%10>1&&$F/10%10!=1?1:2)):(LANG=='lv'?($F%10==1&&$F%100!=11?0:($F?1:2)):(in_array(LANG,array('bs','ru','sr','uk'))?($F%10==1&&$F%100!=11?0:($F%10>1&&$F%10<5&&$F/10%10!=1?1:2)):1))))))));$ji=$ji[$gg];}$ji=str_replace("'",'’',$ji);$sa=func_get_args();array_shift($sa);$Wc=str_replace("%d","%s",$ji);if($Wc!=$ji)$sa[0]=format_number($F);return
vsprintf($Wc,$sa);}function
langs(){return
array('en'=>'English','ar'=>'العربية','bg'=>'Български','bn'=>'বাংলা','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','el'=>'Ελληνικά','es'=>'Español','et'=>'Eesti','fa'=>'فارسی','fi'=>'Suomi','fr'=>'Français','gl'=>'Galego','he'=>'עברית','hi'=>'हिन्दी','hu'=>'Magyar','id'=>'Bahasa Indonesia','it'=>'Italiano','ja'=>'日本語','ka'=>'ქართული','ko'=>'한국어','lt'=>'Lietuvių','lv'=>'Latviešu','ms'=>'Bahasa Melayu','nl'=>'Nederlands','no'=>'Norsk','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','ro'=>'Limba Română','ru'=>'Русский','sk'=>'Slovenčina','sl'=>'Slovenski','sr'=>'Српски','sv'=>'Svenska','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','tr'=>'Türkçe','uk'=>'Українська','uz'=>'Oʻzbekcha','vi'=>'Tiếng Việt','zh'=>'简体中文','zh-tw'=>'繁體中文',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(21).": ".html_select("lang",langs(),LANG,"this.form.submit();")."</label>"," <input type='submit' value='".lang(22)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$ba="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$ba=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$ba=$_SESSION["lang"];else{$ga=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$Ae,PREG_SET_ORDER);foreach($Ae
as$C)$ga[$C[1]]=(isset($C[3])?$C[3]:1);arsort($ga);foreach($ga
as$z=>$I){if(idx(langs(),$z)){$ba=$z;break;}$z=preg_replace('~-.*~','',$z);if(!isset($ga[$z])&&idx(langs(),$z)){$ba=$z;break;}}}define('Adminer\LANG',$ba);class
Lang{static$translations;}Lang::$translations=(array)$_SESSION["translations"];if($_SESSION["translations_version"]!=LANG.
3675122236){Lang::$translations=array();$_SESSION["translations_version"]=LANG.
3675122236;}if(!Lang::$translations){Lang::$translations=get_translations(LANG);$_SESSION["translations"]=Lang::$translations;}function
get_translations($ke){switch($ke){case"en":$e="%���(�n0���Q�� :�\r��	�@a�0�p(�a<M�Sl\\�;�bѨ\\�z�Nb)̅#F�Cy�fn7�Y	�����h5\r��1��r��N�Q�<�ΰC�|~\n\$��u�\rZhs�N�(��fa���(L,�7��&sL �\n'CΗ��t�{:Z\r�c�G 9��\0Qf� 4N��\0��;N���l>\"d0�!�CD�����FPV�G7E�f�q�\nu�J�9�0��ar�#u���DC,/d\n&s��S������su�9nO4c)�{Wќ��(A�x(-�������b�����7��p(*�k�2��'�� �ª��z��+ �80*�1�i�����1A!\0��L�@0�2�\0x�\r\n���C@�:�t�㼜\"�۪��#8_\n�s�j\r�\nb7�\$�8��^0��˺2�j`ŷ������8����66J��O�:Ԍ��D�-��+�s������<�@M*R�\n!��bI\n��yP\r�󊮎�p@��o�(Ȼ�0�:�\0�0�M�7� P�:�SC&���:�3�#K����9���4*��J��R�/D�B��B�;h��6ta����n{z(���T��(ޭ�Ûv�����*�%z[�!�����05�v�)�\rR�)F��_�.BԮ����#lHʁB(�8��ضlc�!J�`��F�G�����+���x�<�Sb;����Ϻ�|��/jj����砰*��{.��B�[��	� ī��&p�\"�3���4�ģ �\n� P�2�������<��`�3�d8��c<mr��|\$7(�'=�#��3W��G�Z/f0��h�JUh�\"�;�Z���E]@��A��Z9t/(���4�kOsdl�E\rp����5�s*�:�cp�0�89G�&4۪�~�HR\$�\$IRd�;�4�J�����X�t@����nt˸��0�W��!h���aH�>�a-:dtE� p��4W�REH�%%�Ԟ�_;�IY,6��q�_e�IYV��R5o.x0��bP��rs��6����%hO��76�c��[]v�DdF�V�/x�`��[�z���<Gb�͡\r�l�/� ��J�n>D�q�6�2(3BT�H\n4�Cs&A5-4�+�6ny�:����L�HY�\"�Й�b���9���I]�c��,y\r�q쭓�]�\"ZE.�A���Q�@��ڭ&�`�2�6qmz�o+��)� �kb0n����K����\\�#Ā�@@��rd��Hr4wK�\rE\$�H�&[�*�F-|�2�X����\n7�jL��%'�3�J�R��g�1�2�m\r�Z5oa݆#\"xS\n����P�J���'h6Y�[(�[h/��0�b�{+�ё���a\"dU�����.F��`�\"���e�H呚A��'	���،|�Q�\$����P�*PY�\0D�0\"��2��i�?����k��]+�'��A_-:�x7��#c\r�<=�*��G4%u\$a=�1r�L�	�nhP��G*��?NH���#J���3.���b���x�*�����	�����C2C8h�*F6�MO�D]%�)\n\n�sZj���e�c/T�&_��R�\r����YD�A�g�Ŷ�S\"G���DYD(��TM�P�Il ��hq�[��'�apY\\\ne�9\"���)���]�����ep�\rl7�k��cX�r�%��0�\r�Ƭ��\nr�:�A�E	�W,��z���	3,�Y�w���@��@ �F�9Έd*%�a���۔�*���)�2����(���S��\"�<%���f������Ү�J&q� �:gl�f��zN2�˯�?<Uv�n؎����4��Ύ�Kf�\0L��rЁ,�`�2��B�k�T\"갪��? (+�PŬ��,!�@),J�B��������� l�t9P�L�s�a�L����8IQƀ�̫H�N���G�n;F{M{8�;�6�C��-�:E^����v�����Ő���~�\$�U����w<v�<{�҇�(V��V���i�yg@T���u��5�A����nU�,8:���8&��a�*R���\r��*2\nB��	����gl���;����kw�'��׸���Qۼ&}o\\��HgR���lŐ��,�!:�+��NJ�1���3g��Y�C���/^�s���=�EŐ�����[�~J�W�� ���_�<�\"�+u�).ꉋ�\n�s��-\r* �����p)hqT�� Q6rg>�}*MTR�d�Y��r�Q6�[��̸]���ć�?C��͸�	���>�Go����2�ş��:�m벯\$�+��YQB�~6,��g��\$1�������b�����\0Efo�Hf#�����(�����N'��7��8���n��p.�o��08�N��.����`�2X��A�\0���E �O�bGPX��b4�^����?�\\T�J\ro6���a���,�В���#�Z�`�ţƐ�����d;�0�pH��M\nPo�^?O�6p�?@�\$Cx��V\n�)\n�6�0�\r��p�?o�`	�[��p�nUjN�ވ�ʔkfEO6\rh�υ7Kh5�R�Q\$��:b'�6v\"1c	h��ܐ>�M��1R\r�Z�N�����\r�V�����r#*6g<\r���B9b�%�f�Gd\n���Z�8b�|M*���/l�c+��\"�\"D���l�ZU�|�t^M�	��\r1���ج�\n9C#�2,0��U��gB��W��z*�L\"�`��c�@�+n\n�j%��W\"���O0T�\"��KH����?��_�\0���\$��G#�2�%�oK�7&4a�,ʣ���Ff�*�O�K�D7��\0��e�-,)���@¿��N�H��\\ba	�\n`�0��+�NKHŲ6;��7�\\(,\n�N���\n����W�5-�\"�0�T!��";break;case"ar":$e="%���)��l*������C�(X���l�\"qd+aN.6��d^\"����(<e��l��V�&,�l�S�\nA��#R����Nd��|�X\nFC1��l7`�jR�[���-�sa_�N����vf�|I7�FS	��;9��18��+�[�x�]��š'�\$�g)EA��x�����Dt\n�\"3?�C,�̅Jٷd�j=���v=��I ,�΢A��7����i6L�S����:�����h4�N�F~��.5�/LZuJ��-xk����莿bĔ*�x�B��4��:��I(�F�SR�2�P�7\rnH�7(�9\r�&���B��r�٬�cY��3,�2�l�j\$�����ɓf�G��P��sfU\$(RĒ���(����))r�\"�K-3��7�@�2\r�DD�{�9#��,�0�c�9�8@0͎�Ⱦ����S����c�J2�\0y@\r���C@�:�t��D3D�D�8^2��x�FQÐ��I�|6ī�3D�k�4��px�!�~�&�mIhD���Ze@HS�D%(�MZ34r�	n>���d��	rݖ�BI!x+�#��>��(Ȉ�IBh�hćyF��\\܌�cnJ��#R1�S �P�2���6NC��6�,(���t��,�2�����II�U��\n��JJ�&ۖNK%J-�fY@��[�R�ZŬ�Kw�-�j#ccc��:( �0�Фqu�i0�(����V�/C�r^�b��M�&hHN�oJ���[��\\ݶM���\\s��)#����f�v��5����j%!仭�+ƌ�*�J���摚�18\"v�y;���\ru1b����7�x\r���9Ru��:���^���<�v*A�a��`��H�䌾�|�C\0������,B7|��ߌ�ú7O����Da�!M�Bq�FƘ�-B��i1>L�36��B\$�BĄÐt\r�(��5�_�xf��5�*�V��,��\0���R�\r��W�S�{�l���@sR!�9C@���|K�� ��wA@s&��ĩ\nR�+3��ݚf+I\0T8�p���\rS�		�4��D�C�\r!�5�3���S�yP* �4nU�U*�^�_`tX\n����K�'���s��)���`F��Q)e�n��O�ɕL����wc:�j5G��I{��I)H�TڝS�Q�Xڈ�r�Uj���7�#�I\r����X����!}�w�CZ�NA�C)���T�udQu��v��@�\\��\$���x�K(`�Ճ���3Me�C4�Np�Yö�� P�|�O�f� /A�:��ߺ+,i�И�cXK`m��D`�Xh\0��R��]f����I�JC\$�\r	��pc�Q�oÜt�9i���Ӽ�\r�ގ� ��z�i����ܻR�#3�+�����OO��t��OQr�X���xh���M\nEK�[xA���dS\nA���#Z	t`�N��է=b�.6hY-6�RK��B\$��d+�4^)L4U \$�l�0&DЛ�*z�\"Ԝ��c�`�d�|e܁����#�WNE�7M��uK�qd/dHc\\�Mu�8|_��VU�N֑�����a\n<)�B,\\܂�qƌ�K�<����\$h�R\"H�m�����rE�\0[M�KOpMe=|M��D���	�@\$�s�� ;0�R����c	�v���0T\n-}�����\nr������q�20�<���HIKs�Bg�k:���xNT(@�.X�A\"�����5J��f�\0��mf.i�;�<ɚ��i�ؠG���6(���-{�I[ЄX�WL���F�5���,��s���\0��J�0#n2�J�ۣ�FM�l �n�r.dl;7�t��I5}�\$S�0Ӕj̘����#J�M���Y[��O���\"��1?��RS)��)�h8+yR�K1�d��;�����Vh�2.\$\nC�\"z�N'� �N�J�����0z��SP:�\n�ڬa��\0�],K�H�P��ޛؼO��#-�i�8R;L�MKm�<��kL�M&sF�X��﹏^|%���G�H�y�Y�(!π��(C/gr�����#���G=�ԓ�]iw\rܙC� �Iy[N'n��\n�H�sDЄ!%{�NI����\"��*1Y|*��DA�7����Âu�5t��\$xD\\ �qXu+�3�h�DL��D�,X���7p�!`���l\"H�kn��{}]���Z�m�㫨T��2���	q���XjW�G�>��j�2v��T]11���ɠٷ����R*��Yn�Ċ*|������;�R%rIQC�R~:�v(B6w�T��^GL��\"�\r\"���V��\"X�>�w��o0�AF=ap�c\"rp�jÂdo�x\"H\0p��m��D�G����*�b��e�!����r\"@'�h֢��!YƗOTfPl\\n4��0z>��LPv��k\n��2L�	�Rt�|\$e��l0�O@��@�(\"b�H�bx�&Dh�H��ʆ�F�2��G\0�*�GF�0�>�d3��[�̳nZ2��FN\\�`��JP��\r��\r�@\r��΍��p�+�P�	mDԆ\$n-d��i���JBT�)@�ĒJ�x�P�pK-b�\r3��	�\r/Mf�Ƽ�e�jP���u\$���:�\"�Kpt�N�GO|���l�F�1\r����\"p�1�0������Qz�1��GŚ'��1ђ��`)�dub4P-��udz4���#f�+�\"(�\"	>>�&��&��F��fr��	\"R�H'Q��k�8ڱm#��%��3�C#�R�g����n��3P��d�����.�q�ب�O�ʴ#*Y�d�]+Am'h���,	'��,U�w�gэ+Nr����.��)�S-�S�/��[CI�j�i-x?��ڨ*@I)�qO�]����*��3Y3P�/�����M3������4�6�D��\\�sE6s[\r#j�q,��[n���8�����U��\r�ȗn��ӈ:�9>�)83�8��8���c�9��c�ְ3���(�,2��F7�=\"�.��%��)�V\"3���s�f�s�3/M<�6�\r=p���F3�,�,o\"�2� `�<6���lF�2�#2on�S\\،B�;Ͷ5��ÓB���\\����he��o�PϮ@4��+��]�D���G�F'E��Ȉ\r�V���8i�P<d�� ����hP�@�jx+����\n���Z֎4�A0v�'XF�\"0�j+Z_�!�~լ?G�]\n�Z	��J�����bNa.0f���3��`@Px��Dѕ��,�aHʧ�@�ZW�P#���L�2�(�G4�GX�B�g�@��v�0�D��o��)K\nh�l�o�r���W�Q\n�	�mXp�\n��:��8�9\0�	M-�9�hp�P�h���g6YBB[BGTe[M���oDx\$,�+!��dţ]��d���X��MT�H��O\0��0l�B�\$f�l�f�>O���h�Uw0�4����mR��T\"MWHX^\r��NdBT���,�jf�\"-�-��	\0�@�	�t\n`�";break;case"bg":$e="%���)��h-Z(6�����Q\r�A| ��P\r�At�X4P���)	�EV�L�h.��d�u\r�4�eܞ/�-����O!AH#8��:�ʥ4�l�cZ��2͠��.�(��\n�Y���(���\$��\$1`(`1ƃQ��p9�\$�+Jl���Yhm�r�F�� ���@��#e�����&���a9�kG:�~���drU��I������z��a��y2Ɓ��Ѣ����^ЦGeS2u���J��\\nE���W��&�oI\\q��Վ=r�Bz�~̲7F�p0��bv�%�6ڰ�È���k��;\r�l��JK��=/\0X+ĺL�=\$\n\r\r6���3L[ʏ;�q�lq*o�Y�σ�h��A9�s����r] �ˈƹ�\0*��X�7��p@2�C��9+̤:ǻ,�ԍ�R�O�1���1���hL�\n6�'��H�*Q����h��ii�\r�v۾��� ��h�7J�\0�7���A'�� �8�*0�c\"9�c8@0���0�MCT;�#��7���@85��+��`@T�CL3��:����x�e��\rEʁt�3��(�փ�m\\��\r��UE�Ҹ�S\r#x��|֩���C0��O(�3./;/�j�^>������-���k��N�눦'�ê«\nr�A��(+�#��P��(Ȍ1�����B^���Қߨ�b;?������4��p��JF奄�\n\$I5�}�������Cr�8��#��o28�����*���\$h/rJ��H��� ���9O���-��nE^�&Đ+�����?:�Z,�<o��Jzo���\\ꋒ;�M�r{�'�3��8<%��ж`)�\"b0��o�ʃk{k����C�b��a���PD���X�*�t������8�=�3�_�9z����M߫	#\\���ͧ����61(v+{�_:����,��i�=]\"��F���H.]�@����w�Ԫ:���I\0\n�	�/���� \r��9�Ux��0t��Q�&B�y]����p�����3°���\n,Փ0�C��\r���t��TLRQ:�P���]F��\ntj�H3�B����<A���.��\"\"�YLa.G�<��\$�{;P�l�����6%tÜ�\\�F�R+�ŵ��Q���iA��rrL�\"đ����BNɅ>��tSe/3�`�c:Ɠ�Zd�@�t\$�K�r��b0N䬾6�d�I�3'�E�H\"R'�O'�L�@2��J��-%��*2���S�d^L�7a���6D��3\n��0��\$�C� ���(����ql4�E���a,E��R�Y��h�5���|P��k� }DW@n�,��Tv�\$iK�}8������\$����RPY%9�<H�JJ��`E:fFp�:6���v�U��Xkc����jϟH9-E��b�Y��uo���cHQ�1�!o3�[�*�= &�4L<3�%��TƟ��p��S�C�QmD�~�m+R-\rx�V\"�Q�Ph5Jr���@�`l���<hed�3E��ê�S�:٠��<DU��*�Um 4��kQ0�\"�>����zS9�oD ��\nYeexkE�����\0�@P	B⻻�b�=ll�zBt��G\"��������Y� ��LK��Y��%�oSve(Z#�S�uC��53��#�i{�3dR��RJ�9�E=ҕ���X�5j�Ó'�4�0���óf�0�	.(�\$���XD�H��fе�2b4Dq\"���O�)Āp������\\�OkJ,B<����LoY4&�����xYE;7E8�4�Y%=6<^�kJz�.&�S�Z����|�/�;EB�:�D�]Ԭ1b�z�\$�)�yI�E?����L�B��oe4�1���	�H��s0\nl��\$������U���g�\\&��o��g�WK����l�8�ƽ�C��FЯgq�_5��Q�;�a�G?���;\"�oJ�\"�`�#K�G,IK�Y��X���e{�P�o�1#�1I�d�m���O9�]6\n;G�N�7���q��K����+OR�1[��32�NΔx������L/\n��y6h�������%�;-���3��B\$)���w/��yY��pW�����W�l��uQ��I���ڼ�O�i�_�����o><՗>g��_\\mEǸ]ݶbO�~|=��������9�π�Bp`E�:��좍�.��kw~䓒�Vۮ�!�'6�σ�P�򞃕��c�;�1�E��B��S���r��#6�8ٍ�#�Ҝ[O	��g_����\r�9۸��â����5�\"t%�8��P�{wY�ӑ��W�:N��`)0�C2<���)�h��M��9��6���ǈf�2A&�y���D\0����@�\rb\ncLT�T�����9☬._D	����*\"\$��D�U��@�㜮o֎>1p -�l\"GB�,D\$�\"����(���{*FÒ������\n �\n��`�\0��ZR%F4�R�PS4��hӂ&6\"0I��bfZ�@��ND�Y��v�D�K��p�o!`WFZD�B��\0�6��z�����ą,Η�\n�\$Lrb�!�B�)�����?�����M�y1\0�Òq\"&fZy0���#16�!��,&����\\��H��q���br��7Q6�c�-�	�<;Cx�\$�ˠ--�L+��\$�K�|��cVԱ-q`��7�DF�	��,��ɇt^h'��d�#\r*� �J�df�����i���N)�p<�\rq�\rl�i��I`c�z����(�H�,��H'�#�s\$\"��b|il�H:g����qjz`�\0#�F+�*�0��X�����|8��u�A r�����(�%r���D���ʼ�Ѡ�Җ�22Ф�z�jG��&�͊���/b��V42\$|��ّ�%G N��~ds+�-��|�,�|s��G1T��\$�o���na�=��0Fn����Q<@�\r��J�Z\r��/�\r�� ˦)����:pK��Z���N����\\����G�(�.�l�3pMt��ރ��c����1h��(r���+mI9��:�s����R���:���3�8���-��~\n�!���<�z�G���=\r�<b����s�kS�\$丧\$�q�\r��(�5�p�.�s\$S�h�|;t L�sBr�*���:�	�%D�;���\"M:ӘgS�t� qCpi����=�������z\$��2���p�s���K\nM�Q�b\"N�g.��\nf�e)�>���F�މ�m�q.���C��'��E.�ۇ�F�y����A�|��)����rD;��;�_&4��4SDCIHT�I�T��X6��K2��T��5�(!\nG>��+%�(��'��	�S��8�-O�EP5H����J�75R�\$��6xt��!S��V�@��{T�z�F6�~f#�\$�r6D����1��&l�9�=�OO���쳙[�~T??�w2��}��@��S�	V����N�F�l�R����&��]H!X1���\$��EV	n�`u�`�\n|z(L�g�p:\rj5fZ�.k<���<��\$��D��M�9�?e`�dKwVM3��Q�9��n͹,�UUA\\�E�?h0UP��2�z9R}�\\S�EvRvb#jJ�a�%Z��k�~%7��=�|t���\r�^�w(�{Q%��\nC :��D5�\"��m�b�6�h��:��g����.)^(8�\r�:��o�`��)t�\$Gp���B�!���~\rR�3%MwD'z�T�k�6�d��BFv�\0o�p�	f259&�O�@(��)	qw|z��E'�5G���\0�b�=B>����Q�ncBM1G㴭�Y2��+���Ⰸ����-\0�\n���p�ƳvOjԑ6�I���d9�>t�uRp�\"w�2*w���Ɉ��&�grlV���\$/k.UZL�̑�1@r(m2��r'�xeIV�HEL�N�7�Wg���C'�L��ypw#i���f1x{\"3�Tԃ�gP��`1r9T�3(g��.�28�G\n;L��*���2oy�B�o���Ђ�G�x��Q�g*tӄ�!�7X{�u�ǎ큋��ɣЖ��q��m��?�͍��K��Q�mf캆�R��	�јɏ~�dՑ����{`N����oy�ִ�3��T2�dM�g��R�A#��N4'��9oNX�\n�u���Ef��o�6ԕ�m�T���Z��l�;{Gҁ�<�����΄�.�\"4aN(\r��J�����b��`F�l�\$�s���1nY�2�l^��N�";break;case"bn":$e="%���)��U���t<d ����s�N���b\nd�a\n�� ��6���#k�:jKMŐ�D)��RA��%4}O&S+&�e<J�аy��#�F�j4I���jhj��V��\0��B��`��UL���cqؽ2�`������S4�C- �dOTS�T��LZ(����JyB�H�Wβt|�,�G�8���r��g�u�\$��)��k�����2���~\n\$��g#)��e���ӫf\n���VU��N���(]>uL����]	�q:���jtZut�*#w=v����p�=�L˨\r���?J�t�H;:��������B�6�c����z��*\n���(�:O�-*�X#ps���{��B�P�B/���j{��B�Z��-I��N���J�GED!�Q�Y\$IMV�.��<SP�w@H<ٗ��x��m�^&Hۼ�������4�Ě6ش��|�/��\"Aj�U<#���'˒��*Io>��)��2��,��p�,6I�QI�4��ϻA�Q�U8\$�X�GK�p��M�E>��]FD��GV9�JW�O�<۫u�&����-[X1aB�*��YPju4��t�!E�*lS���p��x�2\r�H�2�Sݠ��Qj6FS����V˿X���m���p�œ{֠Wj��\$���sBR�}N@��X�@48�0z\r��8a�^���\\0���\r����p^88Ø�xD�Ȍ�=G�5͆��xä�ޝT:�J���m�d�i��PK*�:K(w{-�������έ��PzN��7����1f�%�f�ɨ����r�w@I�f�F���'\r��O+��J�l�2�!s+���J-j��K9'ŏ��(JOT�}��dļ�E����4��us�tٴ���JT4�e\"7X�sг�f*�W�2u�A��8�Ax���h>1XR��dE{(�eghv��m}���l�9ֈ8�UA��v���y1�̼�Rއ\\9Bh�:��BL�Y������;�*I�S'%^R��FL ɖ6�wېo�i&7e�PI�����@\$��I>}��+C�__�W]NF!B�P�jǂp����^a��Tm���N��VƜٺ,�jT	�Һr�eJ�Mxp\$Sƃ��LyRr|�!���c�>���!�7LK�0��Xzi�9qyE\r�9@�]��7L5\r�����ca�q\nR7XC�n�����AZ�S0rx9��\"l�ɧ]���\$�b�I�\$��f����X.)�^�Tp���Ki�cJ�Oɳz)��Fɖ�!��C\n�\"=�I!��7�Za%��W��v��qN��h.*&ɒ��R��X��Ǵ�r5�=��0�\\�Mc��M�ep�P�Xž4�䬸Ui��rs�JF���zG�(���#�Zc�m��B��|��/-��L\$\"���0���yD\"�PM���ѷ����<�x�5�IR[Uk./�S��5Qʬ�����#d�����Z�وwfl֫3�t��d\r�0�@�^�4�@��H�y~x_;�o�u�0I8�2�hu��ѐ�#rn���ʛ�c���R��#��?J����W�a[Z6~R��(F̹]�͡�og���+�H���z���j��يy�ek dL�2�fPʙc.fɚUVn�Y�=g��:3��_��G���g��Ee��y�j���͑[�m/1ZL�{{���y79�긏�%�L���V�ڛ�}�6����j�,5��)M����1��W@b��: ���\"�a)O5�&7-�f�j]��H\n�1�OqE6/3a�\$\$\n0)oEmi.�×F=]��`*�~\"��F2=�VP`��\"��teʖ*��٫�*��r�O.%~���o�cV�L�/�w���P<�Z�\\¥=��8m7�{*�gT4Fج�h���I��!�0���.�Ĝ�}��鯧�]0�fȌ�*AͲ38;�{�%�Z�Y/I4d�JB��4�#��i���pr�\\3͒5ڠ���@�|}�M�2g���{j��F57k�U�Wj�wU�v9�Q�B5+3i���r+V���{;���5�OѲf�M�8��:%Im�\0�T#l���Z��[���\$|��,x��@�_U.������<�|b\n�w;��nd{�g!݁*d9A��k�����G&x� �3W9�%FN�#UwNn����L�K�T�a�f�������_@�#ԏ]֬n�\$��\"Y#�'���B�1����\r�r0��U�\0�K�K6��R���!#'F\"\$��U9�Q/\n�Fh��q,1n]�e/���A:�t�䗝���k��79JS��rՆ��d�LH�7���Q~~C�x�کKu+�9�\n�QA���[���̉]����e����2�E����O���+���E�﬜?/1�f\",�FK��t7��Q/L���qOlrf�ϤX'��,�t9*f������I4�O.�Δ�-�q%.��@4����L�؄�2#ԿgHS��nhmI\n����P�^������7���#z�ƺ/��T�S��*%��L�˯�Ĵ��I��(����,r��&_�*��T�l�4�TF�vm�t��#j6/�ZD�R�d(\\��\r<�l+nNv�����N�F���O�3�x7��u�V��d\"�,���1(���9�f�+���\n�n�n��Ĭ\nC��VO��k���v�t4������	��l�nA�\r�\r+�r��0��\r\$�pH�*�J���j�*l�Q8�n:T�UP�C�����/b �\n��`�6���Ȇ,|��Ό4W\r�?�5G�c�����<�Nr�^(r�Ԡ'5!Er�N�0~|l���Ȅ���ʪ:�B�!�VoqJ.̂\"���*�Ā#�#*�媉oL��3#pM%Jg\$���4�K�'%2��&���Ąw\$�Zl�m���#\0[�h�h7ds�xǃ�ï����&�O8�o���H3*��W1�m1#��+����-,B�,�o(i�&m:l�V��\$K]&*���!0D\n�2X|��/���tʧ�>�Br\$��!����9���V�z1���s(�-��t�dt1L�(q\0Fsr�-jB*,.l��'B���d�S !\0�bx>�@��Ų�LkY/�\$ǰV�4�b��М�<`��%I�;�;f�I��¯32�s�/�q<̬�+�p��+��qR�s<R�ˉ���,SS�\n*�>4��m�m�C*f�t@4t!4�%�.�rP�4CI2����M8��HȅCN�yG��� v�TR/�/��7������#��r�� �#N�t�쎝+nF%����PWq���S���|L��CG�z�5CJuP\"�R�\$t�GE'wC�=�W���`�KЉ�:��@��[B�A��O��OϷD�o?j��,��P�EP�Q,gO�:�1OD�R����X��@�t))�L�?#���h����c�RuU��>�rC�3b���K�v�~��CU��%�YV&�Vr�X���r��Ơ5}K1X�\$��X�WY�����S5\nV�C3J��.��BUL�UPD՝<�q\\#\\Q�S��U5�>D�Wh��lN>��S ڨ�<0�6	�Cu.Z�t��o�dQ.���d�v/6-�R%V�|�lYLAM\nHm\"[��]�VW0��v�K�~G�n��\n�n��.�����H�[��3\$?Hȳ��\\t�FA]\"���ȶ ��T�U=3�R��(���S�A|�\"�]olF�_q�S�IT	�lv�SvިgEJ��TcPc��t��[�p�욾R1(Ի:��[����o��m�?B��P��p�%at�ֳ��NSoH�f'��+t6|�'��j[#�7'�)�FNN�\r�Q�O�ZN�Q^q1:ԚzU�o�m5�o��7��u��q/pwU�Ay�in�iy5�e�l��k�-11�ْz5��S�ޠ7�i�{�sr�}_�yjj���I7��V�t����Q~��a�Qbޏ���+r���WH1\\#��}hz6Ը6xw���4�yEq�8\"�5�?���1u����<7�`{�3�)W����K�1L1s}Xf��M~8qnEq�l��Dg����s�����/������sK�W�����ϭ1v�	�AW/�D�3jˋi�|uw.�J����٘Є�����S�q�a(2`�ay��\\r�b���'mvB��o���{\$c���wZ�F5�\$�f�A���-2�ZU�J9 \$��E�����6��N��m��¤V�#�uu�-�&�&Ñ�O>����d9'շ1���d�z�\$�Ex:&`�� �l\0x5�M�y�?Z@�\n���p��f|i7�	��\\VY�S#ϗ�{W�M�d���6���`yS{9���;��;��z���'V�2����xA�K R�	G�z.�G�V��ֽ�O(�R���#2jc��b��6O�N��fP�s�4c�Z4�2;'s�S��ٸ�&���<`�_\rY�\nd\rC@�So\"ܙ0�%��8H�F��E���x:!\$��&\$e5G;��D���v,�(<i�/��=����廫֏:.<#b����W���??6�\\��\\���P⩓~�dp˵b�(]�X-��~�'���ed�#v�#iL��Ƭi�H�\" �x*T��ET[b+KN58(>u���`��84�/;fASP�{Gx��`2w?0�\"��GM�\0006;s��1�)�\$��J~5����=�\0+z�,O�9�����j\\���V����\\k�����:y��I�3/��JT�H͂�P7�_~�	7,����	|";break;case"bs":$e="%���(�l0��FQ��t7���a��Ng)��.�&�����0�M磱��7Jd��Ki��a��20%9��I�H�)7C�@�i�C��f4����(�o9Nqi��� :igcH*� �A\"PCI��r��D�q��e0��	>�m7ݤ�Sq�A9�!P�tB�a�X.��	�B2��w1{=b��iT�e:E����o;i�&��a��1����l2�̧;F8�p������Ȑ�3c�����{�1�cM�Y��d�2��w���T/cg��̒d9���\r�;P1,&)B��M�5��Қ֍�[;��\0�9K��\"Cj\r�����i�5��K^	�Zp������l��\n�� P�9\$iˊ�ǀP�25JH�9n\"�9�X�:�1�c�p3ʩ���0(`�4�m�0#���`@%#B�3��:����x�?����B�r�3��`^8M3X�2��1��7�\0ڇ�#x��|�	1�J���{*�Q�0����:C��:.㬍X��L�5:��t ׋\rT��@Ȑ�	K�;F�¼Kh(J2F:6��m�6��m\rH�rE`�6�1����t�#� �r\r/�� �3#��*77S�R����#���ռ[���/|r�H<z1\r(�@3�����\$`P�F!�P�ŁB<��q�:�,�Љ�0ׇ�h0ֿ��z1\r���غXK��\n\"`A�!(vL:%���+m�_W�=�=7��Ǻ޺�@�(�]��}�;Utb�RS��UV��Z[��W���#�p��шH�mjZ=5zB0��M�u-���:��mjl�0�k�p�J��� 5D�N��O˫���>�wC���4�Gz,�b�ng�ό��\$7KT�|��7�u�c�u�?¦p:E���!3��ߖ��{_ZH�V7m���\r�0̚),.C\"��{��py#/@:�t��p1\$�9��i\0�w��o\$��A@s\$�*��ʁ�ʪGf�C~�Nb�Idi-���P�noH4����\r�sN��<����ú�Pi)C%���1-*yH�蒧�\$YX\n�r�!�!��5��ܐ�d!�	T��ZHi�\"I-F4ԛ�x\n�2�@\\�����i�='���0Q#���cѐ�H9����NB���ᾰЪ�k�F�m*��\0`t����Cb�:d,��9 f\$��.����ZLa�6-����Q�[�3F���t��x3���t�D� ��ׂ�ei3�L�#n���N?D%�-�X�HHi\r\0�(��J�)\0�#VJ�i�!\$�ن��J�)㜘�R��:�53���� 	��ܫ�e�.��ų���bXR�F-��ཎ\"����9�j�:w�Ԓk0D�����0��3OUQ���hB�\$b����L�H��m(T�)�֖��l\"�e꒢XK���U,R;��\\���%\$�m�BM�\r��9* ܶ��/��:�����m��\r�D*��d�'1F��_ɚ,�@'�0��ge��̇H\0S˛bK���+` {G�ƺ\$P��@a�x�GEnN	�Oƚ�V&�]c�p)��@�^l%!*N��yZ)`J����d�	�V�����V#h�m\ni\0;�p�O�O	��*�\0�B�E\0�!�F���%��R�P �&[�|�}Uq�_��H�ո�K�v�!��b@}i\0�3Ed�Bxpg�����	Q��'-�A��PNC;#U�����Rm�XU��x��2��o+wFHѽZ3N��y�)@�	�h٨s�)����ב�aȭ��G0��\"K}V�V�}�?�M/���sG`�ᔳI����|��x��s*D-�ɧ�ߝ\n\0�0}Yk�y���\n�+5�S���,�H��uK��1mE�\n��`�3^�W��&K����4T�P��LuG�)��lXC�)�%���[z�'z���;�c2\\Jy\nk/,�u�`�c��|��Y��P`O,�8�8���~�<;_�%�Q�'\$���6X�Sm��?���4(D�Y_\0�BH�\$��]�pJDJ;2��+�I�9�]-�^\0R�6ˈ��U�ʣ��AV9�B0F�����J���w0[b�6p��O4����Bp�+ڪ}le���C/3'���pj��M�\\��r�nns�Eڦ4�Mڅ\0*L�%=G�#�&H�#��3����j�H�<׫����<�f3��4�Jzg9�����#ϵ*��d;�Rz�<Fj�dQH�)s�~�P��P�=Ō��z����6K��p���\n�U��,�b��U�!�v�~�A���]A��~#'��cv�ڋf�3�O�?�9�AFU3O��)��q�߯?��&���؊��Dd�D��#�� �;�X��l����j����-�2z/P5kL|�6!f�����\nǶ�(-��ل�ɇ\n��&\"�J-E/��\r�b�\0�C�fR�<3��Ei��)rF)b�F��/�����k0oo )B�ECY��	��o��П	,��L���g-�̿��6�&.�6���)�,X%����j� �\r�\\Ɏ�m�e���1#9��\rp�e�>�\r���������˸ۋ��0�1���0&��kr\"�)#�a�_��]qLR�F7�8-#�)�-�i��Bse�%1\\[)U���C��1J%0\n4�@�����KD\"��1R�,�˼�ͣ1�ׇ6�CX-���qXWLX�.��q7��YQ&���11��\0L��q��G;gX���((c���� G�B���Wf�ڥ�6�d�E�U\"p\r�l6n+�~ �r,��1�VER�O�\$GQ���O%�m ��\0002Y\r��N~4���+�a�v\"�E�8��K�V���ȁ(R���}(ҐW,f{B�b�\r��|0)|1��8����+�4��3�֢2�-H7���Ě�J㠂`k\n����\r��	�*�Hj<15&�01%�9!���*#TX��0���VEq�qfJ+�Ar^�dF,N1/�1�4�E1�*	�&E�o��&�\0)p�-��*�����\r���I��C��l'��(��ӂ^�ff�� VÞ8e��f8;�Y��p����n�!�\0�s��\"������V��h�4M��(�P����&:!�BJ�J�`�\n���px�f�\$����n�Jv��X�̘;�\0004*�.�B42��'b�'L�s��r�tT��&��-�^���48,��7�cF\"`7�q3�)�҃���i�1��@Т��gHdxCѮ��)c�fzg���`n�;<�fr�c,�K��f�/�V�-w\0�.]��Bt�Ґ] ����Lǈ�5P�!,�n��M��>b�Vt��\$Ѣ�.��{�æjHP�fP�'-�g\"#��UC�	D`\nT8G��`�]ssO�\n�C �Ʀ�&-e:�Y��r��6J�5d#2���`�4�˕z!o�5�f�LD ���\nCJMt\n��2�G�2!��";break;case"ca":$e="%���(�m8�g3I��eL�����a9����t<NB�Q0� 6�L�sk\r@x4�d�	��s��#q���2�T���\0��B�c���@n7Ʀ3���x�C��f4����(�i8hTC`��u�ADZ���s2�Χ!��c9L7�)�I&ZMQ)�B�>�MΒ���c:N��!��i3�M�`(�Q4D�9��pEΦ�\r\$�0߯��Q��5�����M�]Y���bs�cL<�7��N�	]Wc��E��Y!,\n�N���x�m��oF[��7n��絆^����4C8)�l�lމ-�ޙB�26#��r*��Z�;����93� �4��, )�N���7�j��k�>cz,90p�܈Q�*��I��4�cJN� ��a�p<�4ɌJj0���#\"S*�1[���#���S.1��pΈ�|;��C�31,�;�� X�8�����D4���9�Ax^;сr%-�As<3��^8N���2��\r�� ���d����^0��62�r,l��((����@��C���/m�nۣ�uv��M�a�6,�����rӹ�zl�\n�����(�Cʪ���!F�`�6�k2еF�|����M+\"#���������g0�wP�'!���Y�B2+Y?2����!�Mp��+�ؼ�z86\r�}�'�pr�s@ܝ>�a�V�2��4#��o�H�<J�&a��|0�h#\\7Y@�����&Cx���Ye�7ؒ%�pG�\"W�Yz囯Wa:o���Ȇ���49퐂麱�>��lU�(:n��B���HQ@���eB�4�B+�� �<ȋ�c�/�n+d`VP�!N,I\\�J2P�L3�>��C�;*\"0��<�UbI�a�j7#��܇�+�¼�&�����4��=W����F�4[��B ��qH2�\n&B�*\n!4W��2�Ǔ\r���9�� 7��4(�O)7h�*��ryQ&A�<����A��]��B~Sڹ���3�D��*[_A�0S\0Ylz�X��� T5�d�*R2Z�( K*@9'�~IQNO��@(%��B�Q��G%�HԚ�\r���5Q�>��}�CI�����\0Ҏ�3ƍ6\$����-J�8ڬ�UHʘ!jio�X�C .O��\"�5\n��J�Q�>'� ���*�)�>�z���`蜖��%�5�4�M�JĐ!�@@���/F�T�PP�ijG���b�����82E�D%D6&�|0�h𘕊J�l�A��'��'��`fM#ڕ��I=v,o�Y�5����\"����@\$���` �4HBV�㕐��C�D	֐\$�B��\nxoL̐7�y+&�H���b\\�d�\$J��F0�<�Ta���u2�f!�a��	1���`E���J�BS\nA���U �C�\$�\0�B0�6e�s����@6��PK��.(���`@K���D��@�@��˟�}0\$�^w聁m�P��]�|�C����I?D��0�a����T �1�\"IؚT��J@S�;��*&�{V��m�y�3h����R�m~�ɥ����H�lF���2x�*p �R{%E�L\\��L!��\$��s�46��%D�\0�D}��&���P�*V�w� E	����kY�]����0�t�k��A�V�C� �3��N��\0>������phb�����b{�R=o�B����G� ��N�;DcB	�Z�J0��:��K\$��a����tox�Ф'����d�(�\0��\\^[�ZI\\+\$�,�(:x�������D��أ/�H�a�G)e�9s nA�&��p��\\HiL��F��U�W �(��\n߶���VZ`^�rJ�5�\$��3��:��;�l�Y�	��\0������P����f̳��Bfz�\ndl��I������	���:��\n�����\0c\rdhӅ�CZX��m�,�N��nS�\$Y��vz\\��72@�0�:R�7YP�Tnuv���η\"޽/ T!\$J�<g����)��z.\\���@-���nW(/\0)r�c�W\"��Σb=�S	c_F2����{����N׃�..��>��8�+㶧�nEĹ'�����)�v.13)ܿ�{��x/*\\���(Ǡ4��]#�?S��9����ө9��nBf)L(�;�2&E_r1�nUv��`F'�9�3۝��]{���ÒXv51���Sۑ\"D[�rOW\n�c�{ŐP����3��g�0O�a���|Hpq:\n�M�Lʴ�H��4�U��oZE���4F�0�e�E����Y��������牅\$|rk[��tL�_K�lt�\nT+���vK�����a��D@l\n1}�b���<�k�o�c�/�[	�����\n�K*��\\L\"6�AZՈb]���V'L( ��<N �^%�]�\nd��#�vE��>O���P��T�f���CI���\0�8���ƌ\\V�����LwpR�Nvl�oPdo�Rm��I���pW�24nn&j�Ǿ�Px0�oP�}��	�\n%���j�.����פ����Q�x��G������c�\r��	���%ر�Oc�C�TlC\$ͤ��iv)��T��3\"�d�p�*�����ZB24P�T�;	����q�j9�;W��p��0�l]cF�������s�[�����Z\"/0I�\r���*0�1y0�O�W�&�0�0�mQ���Ѻ֐��F��C����Z��7&�i�1�����k�����ѧ�p��0BO1�\n��I������gh�m�D�6�5Ѡ��͊�ä�r-���D���F��9��@��f�\r��&�%`\rrT��4����&�o%���-�	��rkl��m�لC �m2��ˣ ј-��٢I\"%�*͗*R0n'g+��,�LP��Py�V��?����`F@�ҫ.*�@�0i��g\r	cr�|J�8d)KǾX\$�\\��o#\n�Jvo1-\"���P4���\$4�X2�\nS\$?�~� \0{⊏�o;�er\r�V���M��`���Nk��d�H�G�\r�X�>\n���Z��cnMPj�\n��F8�Q1�\r:�:Nq:�{G,!Bi\"��/ީ��2'\n#f*�I\0007\"Boh�ͺ6��E�޻��B2*�`�I)�\0[�Bj��\0�\"D\no�h]�V��Ɓ\r=\0�D�{Q�e�_��;�&�\"n�����C��{SL�h�D��E4B��UC�^6t@��(CX9�pC�`3\"R�\"�n�<B�F��]2V���kAC�L��Jg8g�!�f�6gT�m0z��e1�(/f8���4]lHh��Yİ4d(�-=k�7\"�8q�>��>�M6��'Eu0P{�GPF��4Zj���:`��3��#�B�B�Vd�	\0�@�	�t\n`�";break;case"cs":$e="%���(�e8̆*d�l7��q��ra�N�Cy��o9�D�	��m��\r�5h�v7�����e6Mf�l�����TLJs!H�t	P�e�ON�Y�0��cA��n8���U��a:Nf��@t<� �y��a;��Qh�ybƨ�9:-P��2�l�= b����q�a27�G��Ɍ1W�����a1M��̈�v�N���Bɲ���:[t7I�e!��;�����ɲ�Z-�S�D��κ���fU������F�cga;da1�l^ߐ��B͘e��64��\$\nch�=-\0P��#[h<�K�f��I��cD 0�C�z��� �9&��۬�H�	(�B\"��##��\nh�4@��q��0��!��2��o�2\r�H���HȄ�J*\\����\ro�f1F�H@1��@4E�`��ȋ��0z\r��8a�^��\\0��F\$8^1�ax�0�c���xD���QZ1�r�̐@�X�|�\nˈ��!n��7�0<�:(CX�:�Q��=\r�X��:�KV:�U�|�!1㜃2��ch0�ViB��\$�0�C�l�v��o�C�:20\" :�(�\n��%��jf:Id� ����7c��{��{�idօ35uS>A\"0�7\rt��3�C;��6UZ\rc ʝ�#�D�Ѝă/9D�&\$�����o\"�<]}��������9#4�Y\n\"d�L����\r�H�.6mP�۶b�w^��[#��X��}��R:&��4m0&�T���������|7Ӌ��<�a\0͉�֛���2%)���	#l�و��/횺��.͈\"@R��6j8@6���9S��N#vx�RT�`9��2v7�H�\\*kOpSأ�B��h��O�9���*��V����l���R���F!�E��e[��+�H4\$��:9c�,)�(�ڹ�R�2�\0G���� ҍ�b�AD�����S\$A<2\$�|n�L�����2�	;A�ȜV����L1��=B7�uA2�\r<���Cq�;`&��8MCl�!��XB���*%��8a��4<��C[�?���W[�<I/~&:�^Db�R�έ)H'aLc2��X�xDOa	7���&f+Md1<�\$:�����#��9&����zO��@(%	\"I\n�Qj42�*���� 4�0�r��T� ��fLQJE��Ob���nN�)��Ir�uµ�ƒDpt3\$�5��K\"�l}2ػ\$����s���<�����wPj)ɐ䢔`/*�7K@ܥ�Õ�{�o*^;�~d(��t�j\$�-<���BL���\r2�:5��J�s��i��;S,LRh�\$A�&p�Ȧ	�9��A��.�1�04A�����ȑ-!���ԣSq�:*�b&jNB�Pi\$F��Q\0@����4�I�Xi�%E4ɩ+%��MP� \n (TuwRD�0�ttBCR�2���bC=W`Դ�3�ڔ�3h���Q�q4�S��f:�I���\"Y�d��8Y�D�b!t�5��&���w84\ne0b�	� aL)i�)�&OIO�'Y\nD�131D� �[#Z��ԠbLp�S\n%�b��[�E�\0����H�0�k�kGim�[׼C,��24�Oq9� �D(b��aV�����)��a-5�\rE�nj�P	�L*7d8��I�I��X�_�҄}��7~�^��44�%lY�#�\n���@�I1�����|�ș\"�f��\00T�����s���H(�E �C�\n?��\0��3�I�o���㖎2�s@�%k�М�b/#���e��c��+�Z:6h�M�1E���Z��ء�u�9�2:F� X�eg�~I��\n�	��bI�L��?&6����!W�0���kP~��I��6�?��	�W5�9	���AvM�Ò6z`����V��H�Ov��B��M/\r���y��xb�p`�bhb��^`՗<FNI�=@�\$�îl�~����\ni�~��>�4��:�t�\\Ձ�F�8���rEr��\$�EtǶ�FZAh2�sO_�<gF����R�φ-P��>/�:#��|rI-�b��/9<�	��.u�&^�Cb�Ho3�<�Ɏ��爌����g@��4�\n/R�\nʃ#g�\\�␥���n���P*�\nu�#�U+�R�D22\\��Dp��\0/*˂=� ֹ}5��.�{ =!Q��G�<o�^��\r�OF���h.�i�x��#�L��G9��	��![^]�E�c��h��x�F�=,h��0��Y�̍C7F��ĘK���WH.�񪥊��;�C���u�^��{_�澿�@�����e~�8Z�(T�&`�H�}dd���TPV Z�H��B��mz����>����o��h�H�Dǜ'�bv��C\$ ���%d����P(�dX�����8#�p5����jh�WE�<�J<��@�C�\\j�|Z/\$<��V�\0J�Ƶ�U\"l;�H\$@�ېu�6V�:W�m	nl�WХ	P�~>�f�В ��h��.jN<,\"��`��&�.�����(��\nEv��r���&P�r6.l�F��!Q\0�\$Xa��.n&*��N�m?	B�4*� خ(�\$]L�b� &kL�dƴJXb�����\nО.�=��l\ruĐ�Ԟ��Ҧ���/�hرi�o��9��/��&\$o�W�M�2�j�/*��jO5�P�A�Z���Q#��Hc���ū\"�q�G�+!E|��W�����#��C�p���\r�W �KC�!rɁ х\0�y���Ś-�ȧ� ��]lȳ�ZaBP�|�k�U������\"����^���]��urN%�\$#fDx+v1q,\r�(p��@��&��F*@=R\$�R�A&ͱ�J�.0��:Rr]��kF= �Q\".�\r0�-�-R\"�<&eQ�q�\"���!er���%q%�\"��P����\n?/cfmmL�G!0M�y��0D^��]��!�|r\rt�~�fAE/�p�r��їc�,�*�3a6�1q�y�H#gb���n0��bX�).�q�9�j���X��vB�%��^S<��6rs��q\"v���<�.�8S�w��H�zŒ-�^�>��P�?d�.3�س�1�>��:MO@��|�~rDN��'�/��#4&,�*�7C\\E;\"�.��\r�AS5��ZF�2�py��l\$�ˀ�R�R���E�5F�	G	<�q�:�\\��l\"4�9Tt3(�H�`\$@�\"vA%�h`�8C�Ha7l8��,ѷ���H�d�=�~t~��\"2��o-ς�h\r�V?�te�����\"�vNlDcLC'h\"o(6B��x�r~��i:�(\n���Z{,z���N���t���>C�R��ԑN,r�AR���G�F!B!��NUgA	��8chC�C@]�R7\0�QOv-�چ&a�#�n�2�#\n-�K��H�����[��&��֫��E�4\0�'L1N���\n��	��C>�0�h��-|bh�_��b'`�%��1mA^ͻ_�_�`�\08�&@�sT)F�@�'a�Jc�dc�XFn�t,\0\$2A�P�#P�'u0f>��f��.�\n�b��R'��~#gpIK�z7�b�~=B�>qi��\"�McDoP\"A�C>���Ҁ\"*��ƾ�dl�\r\n3#�i��aV�'PG�6�Ĺ�� ��8s�\0";break;case"da":$e="%���(�u7��I��:�\r��	�f4���i��s4�N���2l��\"�ц�9��Ü,�r	Nd(�2e7��L�o7�C���\0(`1ƃQ��p9�GS<��y8M�DY������C�Q\$�c�f����2 ���)����R�N�1��7��&sI��l�������36M�e#)�b�l51�#�����l�g6�rY����&3�3��1�@a��\r�I��-	��r��ɺ6G2�A]	!�τĝ4z]Nw?��t�\"�3������o��b)�t�3�˭Y���ESq��7��\nn�5�P�<�ΐ������&�p�7 �z,Č�P��2ː�	�+�	�b�\r�&�B��6��@�7���@��c�\"8;-��1Ʃ\$߸�����:\rx���!\0�c�0�4&C0z\r\r��9�Ax^;�r+!Mk3��\0_)�r�0�I�|6�([31-�L7���^0��ج�\r���B������:��a����&t�:�!c��2��P�<�n�\n��71�:\n��\0�V�Mq]W�(�7�n@*,���(�eM2x�-˃�f�C(�0��c;1�?E��h#!��B>\r�7[c�h��i{�4�H�7���h��+�68�@��v:����3%�	\r��6V�=�\\�`Z9�m�(��h�~D�6B��QS�]�C�eH�!�'��h�0ԍy�z(1U32�,B���PX��ɭ4\$8H�h�\r���SB*?�4��l����!R*(�	[,�3�mL�-�~��#�\rD#r��!Ј��L���|�<ˣ\\����ĨLp��m���2�	V�u�(؜����6:Zo*M�t�����54!1�%4�_�[�г'n�X�3آ��.����}��+Ƣ:���n�[9���0�����d�2��S�<�\n��ʔ:#b���#��p:�N�]6&��ơ	'	h%Ŵ��tLi�3����roN!��3��\"�X�+WF�Zus�\0��&K̹��������Dd0ɑ�:��a���_��)������\"fM�5\"�!���sD�B'��Zə=褛�g\"�L:~�!@��9/8�X����l H.p�#�z�L|.vD,�to_��G�a\$�utB�te=���5���S��7�X����Ids]�-krLc˩���S5bk(��\"�:�^m�� \n (�He1H��S{Xq]�,8��2Ct�I���[6Rh�ߜ�\0gH�U�������\"H!y��\n��9F�D9�4��Rb~\r��'�\$������4�0��LZa��bN-�k6f���!�0��k�!���h���E�@H�!�2��35�P��A�ɠ�(�!Z���6�tF�I.��+��Mg\nLRd�8�&�aW� m2��D˝BF�L����xS\n���P�J�i�~\0��d�Ni���ʤ8P�aH�R��&�PCIt�+�ũ�:�r,᭻�)*�!-rM��0o�2Q�P(C�H[魆v `B12Y�����m\0R�kd݊���P�*Vok� E	�ڡU��)B\n\n�M��\$�ej`�Xǟ��)���F笕.pث'ֻ)�`ԄO�	3�9��v|�*6'L2��r|�S�R*2�^�>ؙ�/C����_�-չ�)Ɵ*��6�p8�#,�������2t��]�S�}����#a�*0���O�\n���������k/�H��L�p�TΥ �W��Z�\09��LGZ�i1���eP�r~+9fD���a��4,,�)�Zy��,A,��`�`�^T���b^��ёv���t���.\$�O�˞9���Lۛ�.���R��r��R%���f�S�Q�GKx�.u�]=�1��m�Aa T[X�]�7\r��;Z��OC(j7��2�P���W༦��T)�R�W;�c�^>n&'�����YJFS\"�\0HK��i5�A�v���%#_�]���������1\$i�эS_�F	�#V�	`�e\r��&��Dl%Ʀ�����EW9� �yMp���+�����`a�t�ѭrd�6\rd �r�0	�!���*�w橙,a��*��|����yG�],�9r���Ň6��|z���ɇ*K唌�\$)<�v��.8\nO����S�����3ŋt��[.[Iq��EdI��b�>!�>�'ʧSYo1+;����9����To:I�I�c��AXkC�:��Ug���}�����A:E��)�@Uda#��w�^_i8�oI�^�������&�ih�ﭧ�MN��{�4��ZT���Y����Z����V�9����?_�v�9�o��;/��?��o���L�`�ʅcʭ!J\r�F�␏|~d ��=�8�Kf������Fao�m�Ї0\rİ<�� ��K��&Dl�P(B�uɈ��F��>��c&d&L�\r���P�^REP��e����	���T��ϑ\0'<��U&��*���ж8p�@Bp���)s��\n6�C�\r��ng#�j�T����L	\nVR,z�N�f����SO�F0��/�쐔3o�������\"���1.���3�	f�zc��-G��,�|3�0ko<l�\ro|F�.���������b��H1減r��`~qd�HZÁ@����\0��K>�r~1�&P�Ѩ1m	\rP\r�z91��-C0�Doq\n��B�	.�#q�1��7CM�>��ə���M5bJ#\$�u�~>�:�`�/Ğ�!K�jJ�!Ѽ	e�mf0�c���4(N��/��\nU��ےJ�@�R��`�b�`ݨ��L�(ƌ�8�8\\�~(0c&Ê� �\n��\n\"��?�,V�6�\r��B��P��5�K*�V�Mį��E�+���B��2&j�M��ÿ��-�����7��&\\(�,\"�lF��RB�c��>�#�	f!p\\[�0N�,�,������dkV��B//2�tx%X���Ţ2�lgFF%3S%4�s\"�3&)<0n�\$�bb���@�hSbū,�Ф ���*!��6B8�c�OR�d>���&�*�M2ǎ�%��GC�gp�f�X��\0�1˯<c8Je�#�04�����F%���CLXsT,J�&����\n�	�%�0��Jt�\$��";break;case"de":$e="%���(�o1�\r�!�� ;��C	��i���9��	��M��Q4�x4�L&���:����X�g90��4��@i9�S�\nI5��eL��n4�N�A\0(`1ƃQ��p9��S��]\r3j��P�p��v� ��>9�M��(�n1������\$\$�N�қ���bqX�8@a1Gc�\\Z�\n'����X(�7[sSa�\$�NF(�X�\n\"ڌ�5�M��R\r�6��e�]�ͤ<����#(�@d��DM^�|z:��gC�����ܮ�vܧ��DSu�ﵗ6�-��l�\"�䇾����*,�7m����+��\râ5���0� P�:�c���.\"���\rc�\n�\"26�J;)�CZ�<�Cj��r±��(0�:N��B`�3�R�;��#�	6��6�O��Jx��Hj-���3 cꁺTj\r�CRH���Ƅ\n��������D4���9�Ax^;�r.51�3��h^8Jl`��J |6�c�@���bT�\"Hpx�!�d�2�DRD�p�9��SEK2n�����8���\n�h�d�T	�(���C�*=B�d7>��J��C�,�aʋ �{:2�Kp��\r�mI\n�P��7�����^�@�K�o���(-s|�D�\"@P��<cM[,����3���R���oc)�n�C&>3U�������\r�=�2���d���+��ml�h��N6��4����حc\\�\n\"`Z%ީS�7噚/���L��ctmd������V���m�];ͼ�3c*T:N��7����2�6��ۈ�n�OW�ű�G<rr]b;�r[0�۬�!Xr�l@9�\rD��\r�8m~�cZ:����I�a��7-#=~2�!��K�\$�x���.�o�:m/���{��'�K��%�-�\r���-5���\r���M����0�D�\"b���KS���8e^���/\0خ˓�//�7��ؗI AJ�6&g�x^Ú%ʤ6p���NJ��f��K�.]G�����ɞ��'�x����KQd'����ZZ�Pl�%4����Ѐ�B\"�	a9�O��]�2%���2����&��`���:\r��:�J2~�X2\$X�����\$E�=#�k�J�\r\nC(��r��I-��,��I��%@���!��4����Hp!x�\$�3@���Xd�&�4��&���k\$U22u� Z�S�Bf^V	_\n�\n��C���Tz�Rr8)u2A{86��K*z��srO-=Ǧ_�h|�T���h\\�Z!��:T8^�\nDɁ1�LFM�\n (ЦG���t������)j(�H ��6YJ}��n%G\$�c4ji�%��3Xsi���Z&��GPl�3�.��~�52Ũ���S��5z�}��D�L�����S�\n\n )'��#9&��>��̺4�ꆨt�cf=Z�*�~Ql�\"H��9����Ơʿ^���|�WP�	�e02��ȉ��7,�թ���lt0�]�T�=�ì\rd��bHR�l7���k���N���Pu!l���RY&!�*6���bQkbn&D� �7�K�.���a�ƒf��\$p�*�B\\	��\$�\\�=������m�V��d�l�R�����\\� �����,���>�\0� -a�,��Rp��*:Q�==�O�99'd���{\$0��@s��]���\"��w�_׹HO�ضEh.��{�]��l1Y�@�r��Lϸ��TV�mX^ĹW\$I3\0�m������f�'\0� A\n�*@�.[=K��H�35���\r�I��Y��	��V2��Z�\r7�:����#%DqU��\r�&�̇6[L���s�B���Q\n#5�ٲ':�ԋ�PA��'���a�\r&cM� �EQ�nlyョ�I*P-����D=N�T�\r�1���y����\$/:�2\\p�:��)���l�����[��,���6��|bi�����S�:�@SWj��@Sqz`K;g,f�_Б�u�z���u/;��6�oe��\\/�#���!{�Y0�d�y�7���R��6��x*&2��i7#��4r�\\x.|�1��*��fL��q\$(bq�J�\$����	fTܯV堨BHu�������}���ɒ}�!�T)�%�Y���Ut������8�u��O�u07���wTf��)���6c�����ǂ�/O��7�W~0��B��*�uV�˖�ռ^�q&71���f_�1�\rt��Q߽Q����\nc��׿.=��y�h̋6�&�#��Q�>�����5����H#��r�Fr\$X��@|����F]�2���g>	�鼥��~͙��D;�� �B|#�v�6��[o�e0��)�8e�t�#H��@�3�X�&!�ZЃ�\r�08#��2sP6�f��HυHi�M��Bؗ��f��Y�6�&fgF6tgPҍ�/��1��z\0�GP�?�<����6P�cź-��i���g\0Z^D<��?��GE���n�e�g�\\^\0�\"C�٭/0:�\$\0�l��D'Ж9�TB�����Ѥ�иZC>C��M��\nG\0001����/&�/*��6�6M�^1\$p1+\0|�@�����'�<�q@�,�oL�\$:0�Y�=,��ql��q�����f\r�j!Qn5���H\n%�5�ZǇ���\"̇Kc\"^��5����l�� �P:�ht���c��`��9\rK/��p�}��P�����2eh\r����{1{�9X�X���^.�0�&njfnBA�nY��Qm#?#�P�E�Yњs��#�H��q&�q��rE%�(5����]�L5�?&Ru&�Jqs%�TWP�B�@��e��|z�>o~���̆��}\$\r*\$�G���7Ҿ��yc>bf�ήYmDԊV��bF)-��HmF,���rzե�i�P\nGhv�\n-�N�m�XMFdK?1k�>�k\$��1�S-���.^Tҹ�I3�g21�]4r�s�X^3N?2�4C�.��QR���Z(����3�y7\$�4�o7Υ2�T3��0����,����\n��m%x��]2�Ԫ�#��\r��Ώh.���(3�;�L��V]*x3L�I����]'\\1Þ%�^��ev\r�V	h2��}�b�'��hh��ط���q�\n���Z\0@�縢P\$��p�R������1,0!�:���*`��B2E�4\$r��0������./�?��H�ÂF�vT0tV��@�\$�АtB!��\rbc�C\\L���%�\r��v�I7\0\0K\nO�P�c\0002�S4-�2&�!,��4���;��M�L4K�*\0P�F�߭AOLw;�K��mb�ot�@�A#�څ|���@j�Pp5'�!�p�B{��S�&^f1��r��S��DmR`�	c@΂R�-Di�⸢VG\r�:E8���L�-L�rt����m#�Ӵ���t�\"�u�9��\r���ސb/Z�f6���b:v��L������s��(C������";break;case"el":$e="%���)��g-�Vr���g/��x�\"�Z�А�z��g�cL�K=�[��Qe�����D���X���ŢJ�r͜��F�1�z#@����C��f+���Y.�S��D,Z�O�.DS�\nlΜ/��*���	��D�+9YX��f�a��d3\rF�q��n�F�W���B�WPckx2V'��\\��I�s4A�D�q��e0�̶3��/�����tf���O��j,��Q#r��D��I���jI\r�Qe�^D����A���J��u�C��\"\nΕ�ӗ�M�s7�����>|��w2�U:����R�J.(���E�,Z7O\" �(�b�<K�����42��L�N��pR8�:��8�<�,�r��Z��\$���39q���!j|���Rb��ʒZ���\rCM�r�GnS�1�˔�>̂���j�� ĚdڨQ�o(�����!r���{��L�qvg���%�|<�B��5��x�7(�9\r��Q��o	N-��\"J�22q�0��Z%	��ړ(Q�4����t��H1\$\"�\"�T�rx^���\rJ�|��q8�!Ӛ%+ؖ2�8# �4��p@2\r�(�Q#�:��#q#�ov��6��:#RՎ�H�4\r��\rH�;�# X���ӌ��D4���9�Ax^;�p�k[p]H��x�7���xD��m\"��5\"6�cH�7�x�<��]��q}�i�x��P3�qehv�^�,<0�7(�S�j3�:Aڲ�\rk)D���{�֦q;��\nr|��3��+�#��w �(�C��2�;��[�A=P�E+�TT�0��'���%V�i��g��6�H��b�*(s��Y����(�3���ܴ���j(��D3y����̅[Ю����1	����v��Y%4�����F���19)��\\t�u*�\n�����iEȩ)K����K�g�yY�?B����S\n!0�?�Jn�CJ%���%U�)�XJ-i���n3��g�\r]�A6���0F*��������1���!2����r&��X/TʲN�\0GC�(aQk��'�ƀ���&�<�\$N��x)q�G^Г������w,]�E'�\0���1v.1٧��huk����N�z^\n1�� �Y�;&��9�����0i��2�|k�5��\"��Ca���EF��1&�����<80ܻY��s%9��8+�0�)�5g��ZE�q�O�؃	�bE��&n�˩�C	�	V/�FDӟ0JA2 (6@䭌y�\$�P�\"\"��Ω�S��,t����d�kId8�`�\r����]��XS�QG:�4������3�7��{�\\'k�`��ŞGZ]�1��(��3�dj\$b0\0�^�g��7dJz�i��I�-�x���]hD���r�6� �[G��^�4J&���\n�\r��X�Z!�ԧ&�O�\n_v4��B�l@aP�}� A�p Z�]l�&�Hi�i��Xkb,M��v2��#d��-�<�\$@��3P��qg���i��I��I�d���u���H�\",z�G �!�)t�E�z�12��e^�fZ�\$��0�Ø�b�Y�v4�++L���)J��E�e�]4�6bӚ�D29%r�UZ��I9��*��l�.��,��L�;�Wǥ,�Y~��q=�J盨P�]Q�.gbu*�]J�\$*V'à���]6���c�5a�GU��[�a��0꺗`f�D6��\$������|.\r6dv\0000��T3��\"Rޏ�by8s��\$_@�>�����V���X-�d����|��D�+\n (\0PYKE6�y3ö�wR�\$�[�i��c���\0�Hv\r2d3�\0����\\�ta��w�A�>UP���S�)T��5U�B�f}x�6�䊌Ś�8/�\0�� ro��4��#��bK�`c�e,��Y��1x��KT�0!�0����R���'�<��)*���{D3�)u}M; �f�O��%+�����|�㝕�\\�ֺь��%���e��W!���Q|nB�H0�4[g�DŽ��\$l�;��WGYI��%D�����D����\"�0Xޖ��fb�� �\0�T�/|�?v��wzINdeɦ+�ԑXߨ+�\"�Bp�o\$KW<�Y:�l�&��+�4*�]��|�UFR'��\$��VN`L89��\0�2�X�7}�fG���� �YFp�����T����'�/wH�,V\0�ф�7���	,N%��\"�!�;b����!Wˌ�p�xi� ���Uc��a�v۰��.(��:���S�4U�Ē��U�&���#�4��A{�!M���rΓW?��^5�\"~�z��?뺺]ky�2.�#�#}e����ڊD���7,�},���K��.�|R�r�o��f�'����S+��|*Ly���o�.v�l����\$�ۏl#o��n.�0��(�~���K���>/���Ҝ�˃XqKX�F*���J%bȈ��}#�N	�\r�D̥6���'��JO��r���9Ax��H���r�\$��C�	��cl�\0��N��lb�����7\$��������ip���}ƾSd�4`��,���R\0�S\"����f������\$hzXF�7h��eh&���l��tH\"���J��#���k�|���D�m�@�� �\rb\ncN^cV�����#�\$�H8qT��zFQ\\,+B8�QE���{��u�@\"Ţ���|)�*�D�1ƚo�&�Dw��WNWQ�T�%PUG�ɠ �\n��`�\0��\r%�^N�e����\\�qG|�f�\"�6�v��NG�A#F�?��\0'#�B?��TB�K�DO�0H��ِR�t\"��J�,�&*z��,rHKP���(r-%�dn'�&�ɲf�k&��'\"�'cw\0�|�2C\$�@��\"�+��.+�&'Ľ�^&*�3\r���\"�](��Bg&1\$q��'�%&��'�-�b+.r�)�c)��)�p����*lF���N�>B\"��?�\r�i���ЎG�쟂��D���0�z�-*�s@'*0��FSN����>333���\r Sd6jEh8k�+�L-�b(2\"q�2m�]2o�!�(x�yl�2opD�\\�K�ѱ�iA3-K��PHJOxx0��p��'���Om�,�np��^n�M\0dt�S�N����uIg%d�\0�1?�O?�9�0?Ô2\"`*�� �>��\nG�L�p?2�@�)B�T�ﶂ(:C��@��C����h��w\"�F4%E/b���Eq �@&\n)f��'M�f��6u�F� X眖�\0��13��I�s0��(b\$��K��s�0x\"�\$;���D�g\0�4q�[T�U��\r��R%�\r	 �\r��\"�}�j�)����_K>H,�0�45APC�j�M���fwd\"R�@�#Q(A�^�cE�A���%D�sMt<��o-%��h\"��\n̒�0�J��(ҥ���A҂��`j�d��A2�&Up��wS\$�.G\r\$L�5(�0�Lqj�b'>�Q�5Z�+Z��b��G�A�^B�I�;TUW%\$��i5Σ#u]T\\���T5USt��ds-'���R��D~�C%br�+�6kڲa,QR��ct-BF1\$4���@6\$�h��g꟔Ľ�~V�'��a��MK�dҾH.�?%���F����\rU�[u�\"p�B�E\n7b��!���c����e�*���V�1f�}�؃��MU!?�!k��[��U�>V�ۑ_hSF�^\0���TX�V�n�m��\nc:!�)4�7\\ip4:s)T�J�Sl��-���gnV7oW#m�lT���{r�:�o36�UEp�I\n�H<C=dlv�cvO&�!k����\no�살4���?��X��Vw\rQ��F���0�l1-7y��y�X�=_��z�FlTЕ�@J�l�P4��]�>��^�,�}Et�!|�R�d�'�{�4Ǖ}��#��g��#����9=�w�֭�R�FF[t����y��[���&Ƭ9!��!��<Q�cX/Nl8�3rd\rDxڗ�?{�ۆ7a�h�L�OrerXsn;71~cۈ79�~&WؾB[(}\n��_�S@1�(JTA���Lȝtf�B�RPE-C%5y����\r�e�lw�Uxی̛�?u�`_4�Ⱦ��SB�={#�	�x%�O��Y.\0�o2�I3\0�UoW��=w�!���y+YI�*�K�Xw�Η��LqPt5��Hr��y�);���zlΩW�.�F�A3�n҄������H���Z��Nr�3 ����`�\r�]���8+���\"��H��7v/}W�Pn�`�7K�c�B��HU�?�pV�}f�\n���Zl�+.RKBOB\n��[(N��6\$Kr�s	Ĳ!K��ϊC�G�s����%~�^��(�Dʨ������ˑ�ϐ������r�bD�XuE4B�{�T�.��b����ͤ\$g�G�\"�%�.�~��ǭ�8�!rXr�&NHP�҄�G)a���1�h5\\xO�,Z4��Z�edV�uY���F�D#�8/ř#UG���ݯ(ɮ	�:��כM�)�!��k[��D�m�0�Y<�Z�qHa9q	�I��B,�V�,��N��27�:��%~X63��iC��bSG���O��w�W	>/��\nM�2q!8�\"�--.��q�z\rL��ǹYlRѳr)�e���4>�Ii��ODޝ��V-�w��1��6�Ϩ�Qآ��;kJ\"�)3.�w��񧇱n�\r��Q��`�j�f��I���=\r�36ޗ+��q�h�\$Oq|�J'W�F��";break;case"es":$e="%���(�oNb���i1���g�BM����i;���,l�a6�XkA���<M��\$N;��abS�\nFE9͎Q� �2�NgC,�@\nFC1��l7AECL653MƓ\$:o9F�S��,i7��K��_2�Χ#x�I7�FS\rA<��M�Ӕ��ia���	�r�8�MNf�D�l4�̆��g�M�jE*����p�2i��i��N@�	����:�.O~i��r2�,�dQ�CO&p9H3���,�0�gKv���I�y�f�G��{��[� <�\r�ℶ�8ܲ��J���Ӫ��P�0��Β4kR݉-�ޔ�Nj,�K���o��Ǭ���:-�&10��*��(�2�P��ݹ���o��9F�X�Q�d�1�&\r�@����R�I\n�����:(@�7��P�1�hR2:��h�F�̇�c�;�`@<#C3��:����x�G��±G�r�3��(^�c���xD�´Ț��b\$9��x�\$b���/:�-��\"�ڹ/C�:�kܖڶ��2ؽ�U��\r-�A\n�rX�:��Ȥ�9+��2O�k�ǰ�PJ�Cʘ�aʦ �(�:������2(������0C-��!�%���B �3`C`�g�V���H��81���7��\$�H��nǲ2���j�'�N,G8������C�?mC��2�`�7�c �X�T��9J++���\nb��3��v�K-ݗc-2l�N�.�f�Í�l˳�j�<,��l���¨�Q�n�[��_v1ڌ�p���\0��SLF\"�sw���M�%�B ��(*x����?�5#S��82���7յzG@T�(ܰ�R@�؇�:���9�n����ƐyZϩ-z�9h1�]^�V�2���4-X�Ieқ����7!��,>2��[%����:OK��4���/=�����Yb5��'��ڙ���dM�.����q#Gp�O2�<)l1RLQ�#\n�p7���lB��m�\$x���&��@��*�Q*-F���T�cJX�����:Q.<�C;��D)���\$�!~1�y\n#`���-�Q=�C��		#\nO*\0@JOI|8a�4��Z�!�*!E(���6�j^')�~q^�TT�4�/���w~؄���Z<u�1�ƄeO��8����D,����\$HQ#	��4#���ğ���08D���pZ�}�:�I�Z�����'-c��%\$-~�Wj�ɬ�����A��pn\$NlNP�.XP	@�&��h�( ����(L��!����7lRb�\n#�1j��v�zp唅�\$�dÄ�4%�٢�[�@y�!=!H��Q,H���NH�@a?.���2BL�e#jr:�\$�(�!���=��C�EąA�5�\0�F�;�A²�\r���T*F]y_l(ȍ�\0�C��_D�<&�nh�GigPѝ\nLIJO\"�.�Ѫ�A\0PF,0���%���BA�2�3+W�9i����vY�L5R����c�Z\$00�c�xS\n�@�Y�D�A�i����z\"�����VB\n�ۘ7�W�[w	�A�sJaЙU�t�dڈLɔI�nӊ` �Rz ���Ô�3V<���|Z(4mw�=ԮR{njW7h �0�o�IA)˴�xR\nX���`��o]��i!���r%��#��3�u�('�2��)�7�ڭ�[X�.3��5J͝RLI��R��q�~:��=ju�%�[\"M�����*�mMw�V@�m�;\r�3�P�[�c.�|*`��<Rd�Z�*feqr>w�ԟ<��<JiUj1��L�^z+�+TF���\n��p�5j�W��)�J�R㞆2A�\n�+M�,F(�\nk�H>\0�_9�%%0��S�h�B�+z0���Џ\r//��1��klk9�l��g8�\$��%vm�a�c��[�Ӈ��C����9D��֡q��#�7���'ܑ��k]nvn��\$�7_��)Zs3BZXbѪ�aH�L\$@�g}i}���@�/�H����7�Q*1�Sb���[XrK] ����9�WA���i�r��\$�Oܙla�8�s�'���19�a0�����o�h�#	�Y맔?l���!=cD��t\n\n�����>;1�f@��������_]���Zx�L'�Q|�d4���wq���r�������݄.QR�G��;ʧ|I;������2z\"���e�1He;��l\$>s\"vXF_\\�\"��'*�5d�d�Z���D�b�Oo�V˟A(��-fl��b�P�FG}���������.��;�D��fSTZ(Qب���j�_��/�e��22c<���S�a���l�����]|��x�h��2^n2��dd\" ��L��ׅ�^	�G^�v�� dJ6?l��\n����=̞Ȋ�0b�������������L��x�/�	��%'&?n\\��4nf�f�����E�y�|�΃�V,Њ�\nm����d250c\n�L���Ϝ��+��#\"��)v��%��2�L�C&?#4?�h�G�AJț �&n\r��&�V8gΏ��2p�*pH�؟+�	�#p����-P�\nG���e�)q��`��ܾ0������n�D(�'�F��?L&^Ed�pj��._d�1|�p��ZN\$�F���q'pl��`�0�n�b3�������,f2h��H�D7�6E#\"N<��^G�,/��p�Q��Q�o�51v�όo��q��B��0��!��6��	!���!��!�h�\0�&x��I��Q��@�ۑ�/�\$�L�i`�D����\\|l�\")&\r��f>���%�ƙ-�F1���'�='re/�l��(҂�;��݃v+��!��p*<HB�'�n���O����.���b3op`cR�\n�o�v],����W0�.��ˤe/��F]'�FRّ�.c�ҩ,`0���w0I*p\"\np�\n��\r�VOE�����F!#�@	d�1�#n�'#�:�@tp�E1d��\n\n���p\$nih�&N*\$N�\n(�>z���\"\"\$/����ͭhkm�Z��\"(dF\n����(#c��Ğo��>mHBV�P�\nJ�\$���J\$�=n��,%�Lסta�d.%�������<�'.!hX �tb��ȶ��*G���B@/��R�����т��i4�Vx�:�4C�'��m�ACi�?1�\\ �-���*pi	jbo���t�Pf��r�&x\"qM�I(\nn4.��dT��Ā�DM\rc/�h�AJ4�(�v�K\"�\$ȗ�*]PZ�4�t(7`�[����`�r#bg1j���ȗt�k�\\��̞���:S���";break;case"et":$e="%���(�a4�\r\"��e9�&!��i7D|<@va�b�Q�\\\n&�Mg9�2 3B!G3���u9��2�	��ap�I��d��C��f4����(�a�&�	�\r1L�j��:e2\rq�!��?M�3����ϦV(�6��b��y��e�Wh�sy��g�D̀����n�ZhB\n%�(�� �����i4�sY��m��4'S�RNY7�D�	�4n7���hI��8'S���:4ܜ�>NS�o�z����ZW<,5!�Z 6�N�~ޓ��0�~3?���r3�̾�!�Ϋ'\n3R%�����b��5��2�C���-x�2�H�X6��{�94��}@㪴�\rȰ�\n\$��P���*�&\r�տbS��0kL.�-��f�B\0�7�\0�9���'�#ƈ%��@��̲��(��ʈڰ9��h���4.�0z\r��8a�^���\\�?C�\\ƌ�x�7��4�C ^'a�8����ƍ����x�\$�&�5ћ2	�\"���i�֣&�X�о*�.� ��]X1�������'���\"��Ln�>+X�5?b�]��(J��*(k�����C��+���\r�l.7+Qtc]5N6I�mC���\"���z#� ���m�F#�X�C�J�&\r*+�	�\\Vڍ6���k�Emkx����Y��M�2�v�64�cZp�8��g�\r�c�1#Ø��4�����0���S�1e)�@��o{���*\0���u��C�eZW5�k^�\rZ:��c�F�b5�ha��~�����lk%\"��@4���P���ۿ\"��(�*\nvP�6�H�\"�|�W��	�s[-�؄fM����0�+��90�t��#�;O��u\"!��6��a�t��:N\"Y��𜱡���0���CÞ7a��N'�n��4�\n�<�����(���e�� ���#w�-��WCKF��7H�����Q\$��H\n�C����B��VK���rF�lWPX0�ra�J�0h�2�@R�O\$\$�|��j���'�*22VL�8h�� �r2�`c0����t�nӊsN��<�����FB\n�(e�ں�0�<F4�)�K\nlgjq�IGM�(60�<d;�`o��3NI�I�I**?(�4�i.�!�����xOI�?(���\$^P�iC���\\�p!�:<W��Έo-'*\n7*�ÂS\$evF��q.�Դ�u2g�3�񌥰��2ag+t�K�Ò-!�3��M �~���6A����L�j(\\BС��\$d�\"�2�g��P�\0��Ι#�5:�br��-!@\$	�g�5����!�e��V�qN9�c� ����Cxw-���]U{�kj���� F���QI]J�c�Lq��\n-5&��A�1��fN4��q�\r�L�`@R�́b8�\n�y�b=�� 	R2D�C\"��ñ!�a�[L2�.����NHy7)(�\n<i�|�:e��P�̄�lD�O^��`�7M=�'*(�p�T��T�3Q#i\n����UH��c5�<��3jIP^���\0�i�XA��׶~a�d�pGİ�p@�08�}�\0�(2�R�a�J�ab\"\"�`(+C=�	�-h �a��yYExR1�Hd�xNT(@�+�A\"���~.��7H����\\R��ȩ��f��)GYmD�C��؃v<���9�C:&>\$��a���*�\r���0�fb�Z4CHQ�5�z�硭`���w	!�͵�7E�Z�6o(x��zρd�k�:����iFZ����\n�:l5�\"xPJ�%R����G�57L���d*�|X-UV�P���iC��xb\"1�B\0Ǧ�,#cL��L�����	A]d@���C�@+��>�2��Ls�뤸��Y񢦥���p��Y�64�֓']c�e���Z���C+���MN]��a�<�љ�P�C9�K)��ᴅ&�paP)���(Rr��T[���������V. �aQ��Y�NY8-EcA�C	\0��+�J�)�>�p�G�DA\$e�h��/����Y�rZ\$4FB�Hw�>�@��\\�g�^�\rdlن\"N�6�KM�\$�֪�&>1klشc����9\$��*��E i�;\0���\$�)?Wp�DTFg��=����v��ʷU<\rTɅ�g�vzIg�gJ�M����Mi��J���:�Ƨ��Q!f���k���Z\$��y���\r�g�*�)h:���:��Y��ʫx��[G�ǈ\n�C�K���7�֔u�b����I�M��K}���\r�f�?�<��!p8���px���2���N��Y��ﺷ�!�!xpn���n�%�?\${�P�	������O��\\�n�LF�x�\r�\$�, ���� \"����d,���,v@,D��eg��,I�|D���3�6�0:�oR����j+l��V*���.��2�n��.�E��|��9%T-�\\���M���2(�h,Nl'C>��j�k�!p*��g	p���j�\r���\\�ψI����\nP����y��	�T9�Z}bXN�|B��#zpM^4�>1�~<E����f�+�x!�M����?|gP�B̔c�	!H>�f-�^�d�8���pP�3BW��\$ha\np�؍��`�����&�\$ ?�DXGXŐ/_�{��*�X��Y1b�JT##Q����<����X1���4�Fn\n�e�k�xq�#q������1�\r�>k-dq�=1�k��s�Gr'&YMn@-\$)b5 ���o!J=��£� У��̘o�mp��~�f���iP��0�N/�8z��I��������J-#rHl��&�)p���rY'l���[��\$�o��IO��#>m�7b�2��j_*��)�͘��(^�����12�%��r_m\$-�ڈ�2<�/�~Ϋ'.r�.�C�[&F��-�-��T���2�-m�\r�����Db}���A\$�/./�D�O�Y0�4/~�	43H�F�����c��\0P	�c �YRElmf��T��X��WPJ��n���3������8��	o�\r����nE��k�8��Z���a-o޲0�=n����W@�`�\$�b-�\r�|�q�I�.���\n���pzB�F���Q��!e������n�l�����Pf`�@@�� �G�RR4m�������5�r�lEDC�ު�ȧRb4K�X\"��F��c.�!��	f?l��,�ڣ�_�R���v����\r�/Q��)���-�D�1��4�5jL�&A��	��I2?3ϐy�r7�}*\r��[�.ӣ�WF�W��!B�<E���(b��G3��.'0�i�+��E���`Ǐ��_l�C.h&�\n\nP��\0�R�)M�-%�]�\$��\$��%��T�T���ŌAD��̐m�>b4�4`�M�<\r�F�R?o%P�mi�+`½��2`�	\0t	��@�\n`";break;case"fa":$e="%���)��l)�\n���@�T6P��D&چ,\"��0@�@�c��\$}\rl,�\n�B�\\\n	Nd(z�	m*[\n�l=N�CM�K(�~B���%�	2ID6����MB����\0Sm`ێ,�k6�Ѷ�m��kv�ᶹBhH��A9��!�d+anپ���<�W-l'�D�q��e0�̳���\nX���v��C�����-*Ue�KY\$v⬅�5��N�W�f+PdF���Z\\a���T��綷J��ė\\V�L���ã#u\r�#���H�����e��)��nZ4��Į>��N����(�N죂ͺ猪��j�(l4�{\\)�#��7�lX\$�d˨��)�S�C�B��5��x�7(�9\r�,�;P�\n�!��b�()�\n��M�*��Rr�?H��F�1|��I��ܶ1l�7��n�0Q��2\r�|T�x�9�(�������1�c�3��0���Ȳc��:\rx�6��\"9��h���41�0z\r��8a�^���\\0˒�]��x�7�0�DQAxD��lZ���4Z6�H�7�x�5NqT��D�C�è�8zT�;0�?���ؑ	\$\$ͤ	5��.%PU0���P�0�Ct悄�\$f@M�y�q��l��R��%k����ҙُ��/��J\r|�(��m�(6����b���`\r�Q��3\ņM�N�+6��:˜�[\$#\\�&�2�|��:>�ܗ��gN�UCIL���E��L6>�\nb�����h��ϣ�X�O��߼̖�I�*�d���g\"֊7\r%r�Z	^��o��{�I�̟d>K���(h��+4�����	���D=�e�t�k�6�\r������Ҭ�[Ј!c-�&A\0�:�s=X1��vαE�9#�{_����u��4�����̠A��x�4�s�����lR7{���v�(�:T�v��M��3��]�\n\"����!h�O��s�%�9Äs3;�8�3���l:K,��\"@I\ni�0x˜�\\]1�J����Ô�O��\$�1B,,��7#��؈\"F �r�3D9��B,�D2����8K\0�;�6P�ABխ��k��xrGa�\\���D�̻C�dq��6VEa�݅@�QdV��;���]\rȩF7�C\"_Q��HU\$���SJqO*�r�Tʡ0�@讕@\"�U\\��R��5}���)�\n�0����]F�ب����N�5/���ƒ��	��m���	k�nJ5G�&�T��Sjt;��C�rT��S>��&r�mN1(���G^�<+(~-Q���Kl�8	κ\nZK��5ҁ\nW�D�|#��M!?\$�웝�d�t�W`�?����9��ʼ�f}��9�TޜC0u��7�w���\0 PM���@c�����6?	��{9��#�\"�D��P'G���HY�s�%4\0�%!�;(`��Y�D�A21���j�D,�GL�����c��B��\n�J{ត�Tؙ�e\r鶋\"j>j�,��8��ȓQ\r%F\$�������sP��뢊ggC��P�D�%��@ia��=+F��a��R^R��5�r�d�?:ƠK�,�\"F%B��4���I�:'��a�\$<Q�JQ��Q�KȻK�Z%�~V�׭<��Pm�C��@�ʂL�&s\$����&<8�Tƙ�2,\r��k��f�,2T�ԦE\r_�6*u5(N��͚8�Ȍ\\2WqF�\\����\n�-@n211�2�J�bQ`�B#[h�iF07��������@�Bd1��:���r,�R�X�4�I�Z^BH8J�A����o?�;�C�Qb��Ȏ@���('��@B�D!P\"�L�(L��8��ٴ(Gn�TE���9i�Fr:wz\\��@�\$�&2A�)m��.;�B(V�>Җ!�挄Ю��c�����J?�.�U3��g.![�Z�@ł�si�-�ZbD@�VF��z�`7	(�a�\\Z����h`H!|��T+�+�W��ܳ��稧G�q�2��HH���R��ɓ\\��9\$��s��g�-�&�	��\";=�;&9���B��ި+�p\r�֗�\n���P!�efb�}��gu���Z!Xn\r�?�,�6�/;������{�B��*\\�������*Q��1��f����k �ǧ�'j�/adI�n���������b-4�Tb-�Y�C7�.6�u�@u�]���~��T!\$��u�ə4&��a��=D܇\$2�)�˴^@��CC�CW�T����\r�P����Ѯ����O[��'���@�T}�z����c�m�ay:+���z�gw�i��ɳű̎Ъn�C�\\�4g2��<�f��r)N\$�źf�܎QG),x�C���G2����B���̬w\0mzsH�IK��Fj_��nb\$H��\\lF�\r��I�J�G��˧\0��7i��f��	r<���ź�C-r;��nph��4ŘuP\\�\r�F��/�����pm�Y��.�X(�;�����B�8�P�\\���D��<4.��Bt胫�\\�jڔ�.�����\0��ZO��\r�y�F\r�L.\"��J�0W`�O�1\r�ڍ��.7-\nGM���p�l�����j7meM�©�o-�qGm�?c{0�7a���!�.tB�\rS	�s�M�b�O~4F�\"˧ rv\$'N�\r�����̠hf�!Q�l��M��9�-%Ќ���pp���Q��qƠP��wc�\"�r@fm�C��s�\nj�9\"F�,F�m�HAR�GV낤Ӱl)�B�����`ʠ�\r6P(�-��Y�U#]�:�R9�S�H����Á)q2A̼]\r��Rbm��;P�R4�r9&�*�Rw(��H_	M�魞D��/�&�:p��F�#N%rX��M%�m��'�w&�'/�/�&�8pN�Z�`���l�D�G\$��-�B��.oF����~���4�%�.�\ri2+`���\nj6�o1sL�2��x�o@���1��3\0ʷ�p��)JF���m�����-5��'��c��/W-r�Sn�r�3~d3\0&� ��\0^%���7�)0��G����u��0�GŢq)����!CI\0�0C�t\$2^��h��ӵ*�\rMT!��r\n\$���E�=��ۨiE��q�π��b��A>�A1dG�:H�\0bţ����@�l�S���\0�u+�*c.�@�\n���p��Dq�:/�f�TkP�8s���,�P��M��.6?�N\"���ԍ6���f(s6lwBM-��0b,<\\����EB\"6���������ih��Mj�]+O�!��H()�Zq!DX���*�o0x<���BZm��R��O+QLH��oD��] t���5�1Q5�c�P��E�X�P&sC�%(3I�Xd�l���F��*&��cQ�u�3��\r�p-*ӯf�F�Ji�.f��\r\\m�KY��+\$`dT�i%�o��*B����ıC}Q�S;li1Yo�8�\r�R��\\\$2\r��E@����BŖ��ӧ��Ѩ��]I��ߤ";break;case"fi":$e="%���(�i2�\r�3��� 2�Dcy��6b�Hy��l;M��l��e�gS���n�G�gC��@t�B���\\�� 7��2�	��a��R,#!��j6� �[\rHy�W�U���y8N��|��=��NF��I7�FS	�� �ѧ4�y��0��&�~A�H��k�!2�2����p2����p(��M�SQ�RM:�\rf(�i9׫�h��CcRJJr�Tf!7���Y��4���֣��I7�uz��^�\r2Û��O�� ��6�y�bk������O��d{�%z�M���s2�4��*�6�Z�����݊���:���:c�ДBR�90���6�b�>��m*���%#�7\$�JT���ă!kg��LH0�� �	��1���<��\\�ֹ��ö���@Ó�������I��p̧���\r1<�9���0��f:i�899�l��xн���D4&����x�G��ԸЅ�x�3��8^�c��K�xD���Ҽ6;A�^0��H���K�@���t\$����S`���_%��@7+�X�?� *���Q� U^9\"��ֿ-a(ț�#�����ڊk����8ഫ�|�çX���M�h��\$���͢�\"��a<W�4,5�}�3����}\r8t�0׭��)#0�N�E^��8������<c-��&Jkh�ȪJ7-#%z%��U_���5�P�l)�\"`Z���hp��R8�<-�cL��ը��{���cc;@O{4��j'��vt��Nxҗ�IV\\������#�@�\r ͎�k!�=H�Z%�Pf�J@�!�\0�R/i��I��<�UbS@Ԃ�3\r#:Z2�!�\09�	^��O��[�e��rH�ch���[w,Ch�<֏_p\"L'o�,(:W��.f�оHԠ��3\r������t҈�-�̥ͭP9-�r�3�h���:w�rCH����l��`m(+2�1HPQ�B�=c-#�Y�&�1'�����\"u'�56��XΐbL\\�qb�#<jɢK�\$ۆ��\\��	�n�n�)R\r����qڄD����NaIB[�_!��|��!�H���懁�����B�@x(LR@@���.+�Ԋ�,eP�D���ú�Kf49)U.�Cp/!G��ȐD�C(h������Z���1�j:�R`�'�7!�/aSH�D��a�y}C�����Y�9F�6l_S�.���=��袔b�R\nJBHe0��(xC\r���Hq�#��ׂ�JCC!/ŕ�!�p˖�MI��d�)?(Hj�9���cα�+���A\r�@��\0�sL<i��.�Xb��RU.��őS���b;(X7I��E�jE�	���u���\n�>�4�D���� \n (�2�)Jۦ �����rK�kC�����ʈ�巑jENi�|a�4��d�4\"5X��Ji��̚��\\���&��.�#�`á�,� ��R�q�(��i8�nx��a��|��Ġ-�5gC�\\�/��F���T���L+�}\\>R�si:��2�D�L+�&��,�p��\$��@��X��;4Br_�l�o���)bM(]����]�uf��\\{���)�����dT���\nCDpi��<'�Z,����!����\"z�C�[��2v��r٤�Ҁ:�`�Rg'Hl`��V�����.�C\"f(��UPw��@'�� 3&Z��=lT��@B�D!P\"��@(L����L��H�\0��ԱѮC�� ��<f`<:����RT�Õ��ܐS����q�h,�v����\r�x�ff�##	�̕MW0ud�[Cly��@���z?t�b�S֏ˑv�9- ��]3����Q�5H\n�34B\rC����3+�CL��|��<�L4����	�/��W�3��ݱ�!��o�V�ye�+���łi�40����=]R�y�%e�G������3-��]�s:�E����:)�6�m�/\$�Z�Q��LЗ�J�M(CC�m���^�P {�5�p�G��'�K5~�>��.��hao� ��py��6!%'�#\r�(\"�����7x�*�[�<�`��p�N\$A��j��R�%��Te��Ax r+|����I��<��FU\"\\��f�2_�_T=Z1����o]nv��'ndCOM�Rũ��JW*�蝎��g�{���2�Z��+����[΅/�1)�Xdڢ�{���a�LC㄄ih�GX�=�Q��V\\7��[�Iۯ��ͬ��v��hv����с���r�%r��s<a�K^��l\r��l�n#�������[�ρI�{��홙0�%�I��<��8Ճ�5i��'��C���a�������(a�%�4&��B(-gj3�.�.����\$��.-�-p\"����@�,/\r�/�p2��bj����e�^\0�\r�������L5`�o fBX ���'L�+�(/��\"2���O,�o���̴#(��ko�60��Г	G&X��	c��onF�Nк���e�鮾&��\$iD����N��r���-�v�vP��0�A��б��\r�j_���������'��\0�ք�t,�TJ��!��bnl��-Ҏp��>\"ZGd&�N2�@%�B\"Cҽ��{���Md�;B�,�:���d\\\r��XE�X?�,]�l����IO���:�����\no��F��\r�Y��&�.������Q����&��p��P�ܢ9f�j'\\�.��� ���� ��M\"T4k�P�C�>��\rn֞l�P�\"����#0�\r�-�ܠ�afЅ�1�\0!+����\$��Ց�VH�ZF�T#\$�!�VF�����\r�M�Ve�]�%2( �(r�c�C)-����v�1͈�@�%0Z�n�����f1ԻN{. X������rD5�#��\$�6��\$n-_ '�;��A�@�C��L�h�0dd���S�3:��1.d>�'#�l	�<�RA���{%f�_�\r-��4b��#NΈ�|��j.�{'�A\rt4�g����k\$\r�����|\\��J�|퀆��\r�V<E�����Уh�I���2�}q:�ਲ@p��b'I��p�:d�a����\r\$�\n�(�o�)��&��&f�.o�L�=ϣ0O�:�>h��^{���;CP;#��������EgB�d��J8e�q��\$�H^\"��\r>�X1�,T��\"���x3O�������f9���*�r=1������2�Z'G¸��P���E���1C#g�V� �\r��nH��b�b	Ъ��`��-\nO�7l�\"���\"���� #���g���t`�����Ar�φ�GT��-R��zH�[\"81�T�O/����,��{�Gp�\nN|L*�G\$��";break;case"fr":$e="%���(�m8�g3I��e�A��t2�����c4c\"�Q0� :M&���x�c�C)�;��f�S�F %9���ȄzA\"�O�q��o:��0�,�X\nFC1��l7AEC��j :�%f���0u9�h���Zv�M�q�M0Pe��cq��e0���:N+�M���ޏR��5M��j;g*�����L��'S����\$��y����y�=�W����3��Rt��\"p��v2��Ln�d��N�hM�@m2�)��@j�F�~-�N\$\"���s����9�3�N�7��8�-L��?O\n�77eKz��T7@���<o���0½�)0�3� P��\r�cr\"�L;��?�t\0ъc��a�\0)�.��Eb�2���z�:lKLJ!\$���� �X�4�kn��04�,KJ4��,Ò\\ש�+T2\r�c0נ�����胨�\r�p�1�	��:�\0�'&҄�* ���<�%�`@\$�C3����t��D3d����8_,���;�� ^(��6�*&7Ȳ]���^0�� ���B��Jj�Y	�����X:.Sh�7�ؓ�(xm�l�\\��(��:`@�\r��4:5N�.�1W�\n��\0�ϼ M�`�!��d�5,�67���Tð�5�,����2#�J+&R\"4�#L�S-1��2��d܈̞Z�ʣ;�T��)e���i..j):#�\\�c��d�0�|QW���;SP��ę\":�(��5Y�()�tS+��(���=�Vy�eV�)�\"cH��K��������ܶ�OA�L��J����:�\"Ay8>os2��T���.�2�_	'��m�(iC�W�x��?�x��c�����dKBT5��ǣ��7\"&�\\�b�&�S���R���)�)Z�����l�Z0�6��zc�}�W�5�����}L`\"8p�@�y�A�\r7��#^~�Fc.}ˬ6�#AB\"C?`�,-��Ƽ��3���\r0s8��N\$@(���W{r,᠋'�Lk�#�(�ד\nxp2�ѯ@%��`uJ�H����tWy�\r˰�5�Ȣ��M�\0��|:I�(�8H\n�C3\$-�D��s\"b\"1A�/ئ�_q��A�@K\r���+׈C *�ׇ%\$�Ha4#JR)u2�â�S�Q�X�H�J�\r��æ5f���#�ˌ˿�f�I#�VCΑx�i0��cAQ����eUq\$�x8J�R~�b��aM)�<�wT�L%T��Y!�MY�R�غiO���p�abHyᄏD�G8n;	،�v0rR\$����,�	`\$g����'?Ô�|��'��&����!�3���3�M�8�<1�,�\nD\"�.>\r~mJ��S�cm5F��BwK 4@\$�ȰtQ\0���f�a	\r�4��ROJh:P-�:WN�I�9���'w�ZM*\r4i}�%�ʰ!�`�K8���_�h2����֢�l�I̔�n�i:g�����\nh�kl��х@��� aL)dT�I4?AZ|�ȑ<K�G[�_V&h�\$h����(Cl;� ��R\"gq��vvXU�μߣ�\nv\nYc�s�E^K6�����aۄ*'��:��5\$3�aA�>�X�s�{��.��\0�¡(� ��(�l~����(�6��g��\$Jt�5��o��Hmva�N�,Q�#Ȓ�ք�O��9gt�\$�+���>>���bI#A*S�6��EO-���iu.�1A���L�?)�(3��9K��c[�,2�0�p \n�@\"�r>I�&\\��#���%�� ��a���V��Q�O*���hЂ��1�ۅ���OB2�d�����N5�!�<�bNpג˓ �f�)gs����������Nm*s�iJ�����E(jJ���x����O[렪��\"���Sm �L㲛9w%F\0���bN�f�92�A��4�YH��\$�����hR��&	r�SK?�k�=u���&F�62�R����9��v���7-_��nM:yF2ͨX���W��4��ŭ�(T�[����S�i�&���8>���;=%��Ŵ������R�q��J#�ɨ!�����h�K��XfC�,\r����a����?�e����I)\"L��EnP�Pa1LŹ��G%���hH%�����Q6��P �0�s�s��{M4D�|n���v����x y���s^��\$/uA��V�C*Σ��8������N�Ś(�����Ά�+�G�'��^c��b���w�g�1Β#?�5���0>��eO��ޟʧ�%����ş�zR��=O�}���G{�So,��ig��[�̱9.!2��B1�\n�z��g�A\0W���_v��/�7ȷД�(l�t#\$\\.#h��01C�Ȧ0��dB\n	�4�b��.+-%qm���ޜ&2�R���ӣ^<�(B�o`�)�\$�8��p@Adf�\\�R�#2C�>�ʜx�8\$�S0V�*�%Pfb�S���px�|�nd\"�;����NcM��m���0`�{B��#0d���͌��0��P�eЮ!��F�/b3�Ha��WnB ��JJ�2ap\r�aDF� �C��Lԇ\n���O�f�9*ձ0Q�j6�/m�ԧB��u��,t\r+�G��\0�N`�!�B�XM�Z�O���X#oPX*���-1����E����Q	k�0��B�,zYB�m��q�ǎg0����w��J0f�<,���(2��.\n�AD�'�P�ƞ:\r���X���#�%�R\"�]Ģ���9����d�b�ˆ^Ҋl?�Ɓi�p�2�c8�xvE\"r'�z��lq��ne'L���� ��g(���j�N]0����(����	1+)����%��e�D���nw0�8\"�u�9+�ڱ��[+r�G�-��['�5�,̍*�5-�q,��B\\�0,��N���g���0c�ܣ �H0��[)��ӫQќN�ҡ3S+*r�4r�Zb:fE��nmj _��b��+N0�S6.Z�\$�2b��2��=(e�ց��N\n��r,�ȠNm8h &ӌ�\"dw��9s���:��u:��8�0���0��Gd�S,�;-d���=:��<��kSD3��4�����dN����?��?��ȥ:.Gm-��ќE.�>�g�-`OA��첛-CB�;��	�Z	>\r8gq~5�:�%�g��7\rkR�{C�nKt�`!hP��f��m�n2cr`h�cbRݠ�GϮ`pLs�t5G�_E� �#)#��B�\r�V�@�v#�8�d���;�)\"F�,:2�ҧ�r�C\n6��� �\n���p��[3�fł.��q��'��P#'v!)N*����L�5D�5&x[���rD�־�E+\"�4@N�c�Tj0���f�	�\r%q\$�槴u,��Ri�2�nfgc�8������b�	bmG���\0�n\r=MP�,���L�sQB�&ՍP���1�}կY��J�yBU�[Պ�U�N�h�-DڬK�sg\"�B�'b{(�\ng��h\n��u���?Ư�\n:�`f�;���@�殢 )�9D����\n\n�ML�]�T��'��&Dl7+�&H7(�Yd�l���'SncKYW.}Z��[�r���`	,�����B�-��f�g\$��I5�Q��� �";break;case"gl":$e="%���(�o7j���s4���Q��9'!�@f4��SI��.��i����Xj�Z<d�H\$RI44�r6�N��\$z ��2�U:��c��@��59���\0(`1ƃQ��p9\r0� � 7Q!��y�<u9�cf�x(�Y���s��~\n\$��g#)����	1s|d�c4��p�MBys�����B0�2���jn0� �Svݣ���F�]�ɨ9b\r��g�a��8�ɲ5E�A�5�iÊv�U�XلA�:^���Z��:n���<oU�����,KV�ƍ�PQ��<������\r����/��!2��6	�0�B��	��p֪�J~�I@�4�#*Z\ni����0���B:BH�`�Lr(��C��(ƍ8\"�~���2~r,�ԯ�Ƞɨ�2������1/������0�C|��7��\"�c�*2�\0x�\r`��C@�:�t��52�~2�8^���=>AxD���k+\r�3*��h@x�!�L2�c0�<�� �P�9.G����h�ߧO+�P�v\r�4ب���x�VSi!B�6���(J2�6��q˒ �:���ҵ����k�k����J<0ΣzdP�r�+6,��--�5��5�`��ë̳h(�3��<���K��	�\nK<C{��Bm����,(a��&!	��p(�Z�l5�(7/Þ?&�Ҋ.��S[sd7�\$���b��4�+��@\r�b\$/,zǭ�\\���vf�]�c��C\0�;\\V��[f�7l�!�����b����d���|(�;Z�'1�b(��8;s�X�H\"@S���r�\r�Ŀ?�c�9!0k�9BC�WV�@�F�(��1��|�-����CH���v�\"L�ۃty�h�!����i2\n\"M��)9�Q�R�B5�p�*�,���fÜ�Y��(ɩ�47%RDyLynm�6EPK�-�%����PZA!���B��ʱ	\\�a~�� (i��F6����0N\n�.� �������d�^�ހ,�(R͉9i4hx��b8^� J�H9'��D	�O��@����B�Q�8;���Ԛ�R��S`�c \"љ6!g|J�W��5C����aZdq2�ST�ayN��%�@�CN+SR��H�Ar~P\n\n+�u��j�R1��%0��SʁǑ5���t�-!3�F���yL�؟�I@A-�ي���m2H�����T��9H�8i/�F^k�0�b�GʲL�:�r��q����2W�P1��� �2�*��	@#6A��7y�V~[B�H\n7�	�\n\nX)�&߇2Kȉ�!&̳jJ���\r'`��x��ã�K��2����p�f��PAxFkP�G��t��H;3NQf@T\$f0���t��4���8'T���{N���B��2�\"���1�pJ�o\rs\0!�0��E=4���\0���G�Yi�@��Y�Suz��\$���*o`��fMU&��Z�\"���\0P`��=0e�)���pи��C�e�mW�x8>�C'aI#�.NA��f����6�øG�^,�O\naP���X�\r42D)G��y���˲��o�d��z�@��<2 *����#�Tς�\r�:s��\0A\\N�F\n�\rC---I#��X�h�e�����P��<-c�I#���L�&�� ���J�C\n�T ����R�<'\0� A\n�\n�p@�xR\nX��P�B`E�xT�Θ��Wb�ǵ�ȑ�f���}f���RpJC��d����}C�\$K	�G�`DVV];��Њi��k�[<�XQ���Y�2L��mo=�CG���m�6�Vߠ�~}HhT���0sHl�u�_�y}\n��{A��OB����Oקj��e-\"Q]3U:x\0*�c���PX�(��ә��l�a�\n�C��B��%��؆:�pXbF�S�9擂���p7<��s�\r��P�M�i<��r-\r�����e\n\"�`���?d�t�U�B�n��\$�⇆]\n�/�����jjO��q���6�F�Yrɲ��p���D�J�3�ec�Cy08[̭.��X�zv6[��Y�w`FT���׍��1y^�X*L2u8�<��D�^S����3���K��ʸ:�[\\�au�ǰ����/{�l�S���zj�U���#S�������1�;᛻�UMi���.�ۈ�(\\�`�v��jz�pJv`��o(��zu����p_�o����aw2\n����3�J;T`���b��6�\\��(Z��yr9��5�;�-(�Z�Ҫ��h�uR\n1�8�	�2J�O��Xy�4p�q*����<1(�u�)�i�@�ɘ~����ڿ����%�N��ie��G�������sEDHC�`���㬏��Ig�6\$��x\n6`'B<�0�B2��Z0ȰN�f�EFbÜ'��z��-�`�D���l0r7������#��m�F��]�\n.̘(L�ĕգ�t\"������\$~�P���^p�0�o0���-\$��m	��Ybu\r�\rM�\0�4bZr��\rG6�����������AB�(p�)O\0���p�\0n\$\"���\r�C��0�q4��=�(6	r������5�*���\"��ɀ8��&\$��\"�!��'	�;N���������D�W�\n�lqa#q*��D+�D�kNC27�	��|@ڼ\"���\0000�Y�4�Э��7����\0��p�Α*�B�q��f����Z�\0HP\r\r�0� c�qg�� ��.%f@�@g�݀�h���g�<�����K�\r!�ԗ0f\$����\0_2�^\$�c%rl�\"bh��I&�U\".6��%�\$#�a(e�]�l+�b�Zc�^.<�l����8��ȿ�\rr��I�6)��1�,2Ɨ�_9+����?,2J�����F'RO/%/Ri ���2��r�犡�\nr��#*-\$T!��\"���T�J�0�:��4}R3΅4\0	\$`�֐�m�&#������)m\$#���Bp(c���h.h�*�r���	�&\\ɀHB46HA\$�����\$���(K:\r3�p\r�VcNjJ�#Ң`���P.��<�F�b8J2g'�����Z%�\"�5n���*��ޞ�!�آ\"(s��h�\0��(^V����I3\0cN�.f)3���%E!�г3�H�F��aB��D�4A˸���9#�&�hK��>R>i6\"y���Q�����l����Hp蠣24��4���'\nԁIH���(.eI�l�4�f�m�IQaf8c�j��JP�(f��f��t�f���/�4Bn� ޢ�#c�|��K4��D&^MMÂ\$wQ�\"�C���xBl�I�N�ql���6r��d���e�����CgC(��J�*��";break;case"he":$e="%���)��k���ƺA��A��v�U��k�b*�m������(�]'���mu]2וC!ɘ�2\n�A�B)̅�E\"ш�6\\׎%b1I|�:\n���h5\r��4�-\$�L#����@�'b0�T#LIR��Q\$�c9L'3,��.�N(�	\\aMG�X�k�1U�P��tf�O�n1��[	��SV��qC���lql�{Q/�CQD#) �g��+n^U��¤��VnB�����i�'̱k\"1hD�A���b�;9QӉu����v�G���J��]/�)\$Q)��\n*��f�y�����7�Lą2��>�2�Y�ļO�>��(����6�\"��Ή-�z0֡�DB��i��åIz��# �4��@2\r�(�?��:��	#�q��1��:#����H�4\r㬈��;�� X�hм���D4���9�Ax^;΁p�F�]�x�7���/�!xD���l�ƃ46��H�7�x�@�����{���3��<�ƎTn2l�U��ë���%��\"���H+�#��% �(ȷ����\$�2�k�LCS!	\"N�-i*�%K�\\�A�Z4,\"U\\!h54��:���{]�	{)S=�u#��R���zĦ��ON!Sƴ\$��P�[Ȓ�%���!H�w��)T��Ĝ���!�\0�(����K��X�,P�7OC�~_Q���USVJX�gp�u��)6f���f��Ȳ^�,	+?[O>0�;Q*�y\$�T>���������RD\"'�E6'�\0�:�q��D/#��_I��z9#�+K���t��4��������A/�\0�4�r�ؾ�0\$\0007s\\�w�n#(�:O�M&��KC��~�\$UMq�.U�ۨ�s>�%��Z\$j;r�.7.9�b��ᩕ��\"��MN�b�NE����{JN��µ8�\$�ƣxP�[Q�5����>��qDH�!:K�z�S�\$��}K)o���P�_ic2K�2��SJkM��8�4��O��@#�@��@����H'\\�-�\$��T���}d:��N��)%(P���T�b,��C�`�M��8'\$�Ӳx���9'����;��*D\"Vj��-n@����G!�`�`��`��'\$��8�D��OU���*20KI�\n����XP��U\r��\"�\n�A�2�0��Da�#\$��d�l\r��%T��}s2p�:��@a\r�����E�Q�_����0@@P\0������^��\r��1ܒàoK��4�`���<�K�\r��x�\$�?�u4��n��Y0���4��ZQ��&�4��ܙ��48%����\0rXa�4����M�T��0�[�e@Ϡ�\"�\\sD�\r �>��{��%>B� �6h#�EVf�s�uM���Hn֕������`م'=���5F'j1��؉=�K��ED�z�������9Nܣ�qDDj\rZ���\rԨxS\n��B�j���7q䄛�,\$*X1�V\$T�mmDS�ؚ\n�a��1(#I�IHp�EG���S<���\\'��\"s�r���k���b0B�#^ke���`��y/cg@ĬrJ�m�iket<s(wr�k+����5�1l%��Y�BJ�ЬU�Ͷ��B˭COau��Wc�E�d\"��[@D��!;���3��{lZ�g�ՙ^K�A��/���?�̇Ѝ�g��k�����a�}������1#TǙ%�����DD� ���Ǭ�		]�͵<s?WV�|(䂗{�=ϥ�A07�y�`u�����9l�R�t�M��'zIEH -f��8H�������dMy�~���[/H�c%�#p��h��D�ZZQv�`C���5�P�^Ry}��>ZC@���Θ��pCٖP\nV�A�<�b�z���>W���\$�b� �ᤐk���A�,f]�\0��Aa!j��Y	R\n:\rD�HmV��\"~`�)?=0ԋC\"��_c��!�xJ&=��'�~,p�J�j�G	Se\"�D�*l�)�0�	a���Ԗ&�4)?�C\0KA����R�䳘�(��5�Fi��ÄK}��FV�LS���OG�w����\\�er0\$�~\$�+Q��L�l�_%���T6�?,vJ�y�;j�y���l���C7�c��ER}�ZR����t�7�����Y^�>!E�PI.[�ۋ�\"��+s�-�\"2������)d>kt�*����=E%\n�k��Y�!�3�D�3��!�\"0�Bw	O�A�+B��n�'z����{���ʣˑ/3y����ī����ϙ2��P����T[[v����-Օ��Q��9ܧ�K�f}����D?9N>#��q�Y�����B	y�E_���]���/`Գ�RUo��U����4��o��'���e�%�̯b�2�%��Tm.͏����mC��@T�0�h�:&��E^\$���\r�z�{o�\$.���>f�Y�L�i��2�H��B6B�ƺ�Z�j�/�f���Rmh\"ή�ʾ>l�\$���klh���c��m�Z�n���n0j���%��\nMl��T>l�f�%�\0�xu�H0���.\$܊H�K[�k0��p�P*��,=�PL�Ѐւ�����F�ē\0�0ϑGi-�t�)��`��\"?�l�1O�)k�vM�8!�(�N�n1b_�N���c�]\rN��n��j���!/�,d\$�0b`��\"?у�l�u�Hk&�\"n<�b,j���ݭ�\$*��L<%�.:<ӱ�X�Z;	C�Q�\rB�j��o�����䒀VEN�0�b<Ȁ;\$H;�;\n��%���(�ʹ��%p\0��@K@��q�|���l#�m�p��<�8UcA bЧ�d_�Q�Ee���|7e��|\$���c��1���.\"5ON���%�6f˽Kj�L56\$�N_)�\$��]�T�p*�P�c��BDq_+�,�w+�<�p�鮆��+,\r�x��j��9LVYF�Œ���?��^���j�EZ������_ˌa�.�� �h�K8�#(�)���J��P_��DP��fY,�kL��!�X��Z��\"R�]�-�b\r��@ ��`��qz���i��+�7�";break;case"hi":$e="%���p�R��X*\n\n�AUpU��YA�X*�\n��\"��b�aTB�t��A���4!R���O_��I��Q@��q���*���`�j:\n�	Nd(����O)�������!�\"�5)RW��	|�`R�ő*�?R�T��DyKR�!\n�D�J��\"c�U|�\n���Գu%��g\$�I-=a<�f�H�QH��AԴ�%�[M���.�_���D�q��e0�̵�����G����YH���s�z.�K`RC�3�u�e��\"#I�r�������U���쒮��I�B�#�R�E#�ɿ҆�>+���Iڧ5)\\���/��b���H��h�����jڥO���M�h����\n+���;Ⱥ��)��HP4J*�\r���j�-O�4@#M-H��!���&���1��|�H��\"��,��L�D'��H�?Dz�1�Ӹ�20c+2�s50������!H�(�Rj��-�~���d�O�t���Ū�B�4<3���8�Њ�QJ�MK����\"O�{\"	����;nTTt�r�P�êRؖ2P�O�YH�X�#��.�j���rF�!(�^@��>�Z�re`QJ=ʪ���iq�YQBk�O�`@!\0����D4���9�Ax^;�p�2\r�H�2�Ap�9�x�7ヒ9�����J�}2���+*@��KR�^0���΋ר�yLFmF�4m�a;JM��>��̉�O|�6Q�-MKP�I(��Ζ���v����/XkJ=:տD���	�;���l���rX���4/���'��o¿%&�:�I�=-+�SlC?�ix�p-���?VCv�^[��]�Ί�o�%��r�����1I-�\$�������.p;D�[o�,S�:6`R\\.�wd�i�}Ľ�u?�\\������Rx��	�:�*�+�on�7��K�d\nb��zܭuݺ�ǀ��k�H������}��WK����g�[�tF��ƾ��z�Mȭt@cڈ b䂪���p{TO'𙛒bxf�b\">f	�(��mq�V��L)�0A`�<5͐��f�m��D(oa��(.�b�g��_A���*�+H�ɰF]M!\n�\r\"�n���gL�x����ѷOǎ/6^�\$4\"D��d�,#X>9��6����#�MP��w޿�Z���\r�+�#�jI����E\rL��f�eZ���-���D�G��8B&d��̢�T�ћr��!�o-�(���6��KJ��G�W�m��\n\r��9D\":F���s�U���x��t]�E�'���.F�I��Vmٛ�>�5,9�	�ٓv��W�ަ{nE�\\����m��4�gg�'�󛍱���D��Z��Nt�(�<JSkUˉ�\"*UJ\$̺�e\\r#P�=O�KAX`�!�0����Y�1�8Ǚ\"d��7���MMe��)����-�4.[BSnҍ5I�E�M��_J�`K�A�h-6��XM��q�y�X����m=��T�!*�;�8�ڗ��/.Qw�Z���z�oqB��J�	�|��	e��t��+	al5��&�X�clu��Fȃ�tdaΨ�F[�I�3�16AW4��\rx�5�N�e8a�J��jqw(1]G�r��	�9J��.Q�嘵�b%��:D�g��]�4�r�S����U���{����X39���\$�7�&ܫ�_���dO��tK.h\0���)uI�N��AZ\$j���~gcnxG�oʗ/j�wΊ������h��u�r�=�r�|��W��0<;��#&wa��]z*Ys�b.\\1��y%[>E�f��/�}.�����E⎏�rJ�1*�&5����@RϵޒSa�B�_>=�j�cH��I.k5C|~S�lW2~�9�UL�|�DVIZ�XN��AD�V�Rk\$��Q�A�R��ѮEXԍ]ti�	�2����ݟ�nAxp��u(�&L 0�\n.7��A���Ss�,Xs�Ic�\n<)�E̋����Q�\\-[��2�.�m���qo�G��(��f���&ьRj��Fv~�Fo�����T�F\n�F���T�&��/=&��UnfA�����k�7Z�1��۩��Զ8[G�3��X�6y���\n wB�ː���^9�Af��Z��޹�74��VM�}F�ݴh�nn=��O��Y��Wg�R�M�E ����P������z���O1S�<���6���T�e���}i����~���C��M}x:v��b�؃���H�>�,9?�Naxa�v\\_'�h���_1�d+@��0,�/����oIt2�8;\\;�5��\n��a�s9eg��O(�U�7�b�c��,���S�^=ZM[���6CmӪ���k�^*��g��+�y,\\\r�Og���l�!i�J�5�ZZ��� ����K�.��D`\rz+��G���\0?�{H �Fį�b�й��&���E.���Gz�mL& ȮH��(�nF��I�_b�'6�����\$8��c�ҶF�@�IdY�&��Lu�����lN@�`#���� �:��zU����Ԩ\$?�tNt]Ʒ\0'l�In�E��tĎN�(�C�}�v_�*�,�(�n�^�XtDpo&�A��R�b�➃p�H��j&����\"B#n;�,ʆ�(i�c��^�o�E�)��aH0Kfp���A��0�,T�&m�*tQ0�7�K����lR3�R��I�v@�2?��Տn�(����-�Z.F�*\0����K�[Q_%p��3��V��R�O1�	ѽ���Q�|̶�Q�ѐ�?��?*\rJZy�;o&@��(F��uF��z?.!�_4�o�9\"Ŵ,i�#\"�9�>N���I�:���3Hp�d2��M%/%K�\"5&���\nmc�OD8h�sψK������1�����l�|�D�h03&�4�0\n;�S*��Nh�gF*����(�7+�B��J)�*�,��6©J�2�?hM,��N�+.'�\"�\"�/�,ȍ.G�D�޲��0��\$��hv�ĪwJJ6��M{�Zi/*p��8F�D���J���cjp���l���5�N�3b��(�l�\0�&��M.�L����7��7�e#F��N�6v�z�Ĥ�cRt.�M�?G�7 j��z���텶�@�r���^������l�20TZ�'2N��1���N�>s@�,��?��<�3�?%�?r�\r��<�H�'m4\"E�Ʈȉ1	 �V�\r5sSQP���m�\"KC3�C�Q�&��	�T@�F�08�.Y�%?��Fi\n�o�GT-H�j�~���HD|�*�H�>��-�?�5q��/�,k�RM��\n�>%�F���j��\$���(��p*tQ���LM��������NdLj��r�_!�9\nI-W��.D[�M��G�=����K�ZΉ��.�R	:��s��Ϣkԓ0J�	I�2���.��03�0t�|�eW0���W�muqV�H6���RϳWtyI����}Zc2�I�A�yY��[�|4�IҴSu�P5J�U�Z��\\o��#h����o2��\r=í�����Q��lP����WS�E��\\�`�d���[Ug,��Ȉ_V\"�5�1�-/U��tL]p4��С��N�&�\nupKd��Y�\\�o%ee�c�EK��\r9�N��^�yX�=0��l\r>�0tiO\rh���5{i5��e�H���?qV�.�6���kd�lv?_���\\���c�l���ph[V� ��?��frx�l�\"](0U\nT:б��G��h�a�1\n��\n��YAV�a��d0��j�)�1g3�n�Am���%sc1�cgtǨ����pն3��`��6Sopq�U��[�	V�E�oFI_MW~7�w\\�K8�K\"sZ\\�i\$\0�^�!ω2��tiDRK�i8��z�S�x͍��ˊw�Kls_�^��di�\r�Wz&d�U5F# �\n���p�Jj�nvr�.��yP���G��<wtr���	ʕқZq�A��C�a��5��u���|��憡\n]p�\"7|_Jh���-e|7p�������mt�h7>wԑ)o��}�6E����ЦW� 2�Q�A�\\^�?qW�St�,�zN!���,�pҀ��j����-��qa_rX?�?Hw�n��Zp�\nhsq��מޘ�����/6t���݉��8T�|���S:4��C��#i���ې�/O��h���yl�G7���@�Cn���9��Mm�'�H(�șTy��h[U]\n���z���1�L�4w��X�r�+@��{����,鹮AY�N�%�\r6�=א�f�<�k��\",|r��󉙑���(MuLTPaF��^��on��ϧv����_�f��3�(���gy�wJ�bxk_i���܋d�g�օcM|7}N��'��v�";break;case"hu":$e="%��k\rBs7�S��N2�DC���3M�F�6e7D�j���D!��i��M����Nl��NFS��K5!J��e�@n��\r�5I��z4��B\0P�b2��a��r\n#F�������Q�i��s�'���jb�R�I��;�g�:ڊl�ƣ���jl��&虦7��C�I�i�Mc���*)����-��q֞�k��C2��Q�\rZt4O�h�97eE�y�Ac;`�����i;e�:؟P�p2i�3D�&aҙeD�6��7{�ɭ�W�������ăc�>O��]\rO@�,��j)�.��3�B�:9)lr<�C�\$,�2�\n�p���9\r㒄����.\rJ趫ر��:da�`P��A��FEh�C#���l2�C�@9 �jV�`@�;��z9��Z��#�֎m\0Α��������9�C��c�Z2�\0x�\r	���C@�:�t��\$2�E�8^���?C ^'����M�9\r��7���^0��P�0�O�؋�B��9�Z8�@P�H� �,9�����Rz��v]�\0@F45\"jb�6B�p��k�J2Ai8���J�uݲH�׉�-v8B�����-��<�PЂ:��h設�::��+��2��ꍄ��:��P��-\r���0�4�ZV3���δ@�5fCRH�4?u�Z1\$����`+��f�[X78`P��e^��m˞�,��X��@61��c'^�\"d�92y�6�.Lﳽ���\$ƖM��Z�<)��n�k7��{����~2捖��F1�͘U�P��0��4��>L)`�\0ߓ\nqh�=<\\ܠ9�*c��\r�5s� ���5�3K��������&X�Z���z{Aԩ�(92��F9l��eZ%^ �u��4��w��@�0iP��=����w��xG�2�\np�6�%{�KB\"�\"P0�+L�E\n�h��� �\\�p�E���@DlڜÜ�o�����T��jO\n���*Ӓ\ndMI�312�ܛ�P*�<�0�_�]���0@�\n�)5E��\0�e�bN1�ر�bj��BmB`�#��.�C��yA���R�Q�AIu(��`.S*l7�,��:�T`�7��&�[!\ra���zdT�ݙ��x@ᩲ��S��{i�?�W^I�d�	BE���j�R*MJ�h����~O�6�G�]I�����=�ОAu%�������0��=G�@O*'� � &�^�K��l�tb11���*pIc�&Q;5�`���m�0�i ��+�p�m�����0H��f�hi!���@��2w!���\$�m��5#E1��#�h�<7.�,\0�(L�����4] m%OT7�ě���rji��`MO2b\$m`7�s6J��A���x�Q����i�RH��k{�P�*��[�򟒡��0�A���1'IP*D'%o�\0@RČ=+��E��	c^�Ⱥ\r��\$��+540S0t�d�������A��S��&N�t�9���p��|�'����Nj_�7�2A.O)5'aŌ2z�a��vH�S(�ۤ<��OM5ll�A\ri�(�	05��4#��bVR�(��e��L��aP�NǼ^AKo��\0�-YL��WjU8'��a\$�i�� @�� 2k��;	zIm�\\F�ɦe[l�D����4Y���4sjb�����)z���Q�)�q��>-�R1PHj�F�X�M�P���A��~GDe����,�P#d;��\\*\rC����6���>VIG����fCp<��;���ˉ�f=ڸG?���y����b�]M��d�@��k@S�GA ZB~�h�P���!Yp�ttY`��o�U��I�\" Ɣ�9���^������N#7���b���J�����pk��6hk�i�!�����vC(wZ�i/������:��'ے��߄���p<7�FE�aք9���V�e�'V�\"(C�t��3��Ypf\0���˼��e3|���\$wV@ݎd�2���;���b����V�J���WX!Gŧ8l���T!\$i������2�z�p2��3��^v�]\0��.��b����\"܍2y�O@ɴ��He+S)p��y�%ϟE49E�����:��9�DN�[����ӫ��I�['�+���һ'Le���u\"��CW��G��J�}�=My�28,�e~����g�Y�*�§�v��L�W���#s��a�c(ڡ�����eG����Q��`�еE��O15�f������j��ׂ���ή��d-��h\nbZI���j�����9�������#���_�F�FY��@�,\\ba�6���i��ˬ�4�V[�����%04gf)Lڐ!l뭍b�\$<���#L�Er#K �c��5�L�X/`�Wde`u�j)�T\\RW �����pi�P�L�1�D%d�4\r��f���#g>?¤�g�[��-�\0�E��\r�z�.\r��l�(j�9��V\0�N���F�\0��C���n'��	����\r��<�'p��ޱ��\0��=��\r|C�0`�)����������κ1|� �p>:�(�/cs��.��%θ���;l �P^�̼�Z��bqXܱ^��)��#��\$��@>&�ƚ�@.�L\r��D���j�:o�	�Q�)J\"���P\"I!R1�^�6�p�#�2��@Ϙ2Lvs�ZM`}Q����\r#5��%Qj��`��CLL�&L��H^ќ�o�Ye�0\"%�'��P0�%#������\"r>v��Jφ9\n%#P�8�R��(���%2a#�B%@�%�ֆ��o��''K'p���	b���2	��\$`##vg�<��B�\0e�R2�/�J�3qC+g�&ф����Pu'��p��\r�-�&��Ҍ�-�H�J���.H��(��G@1��1�D��|�\r޹dx����J!i51��2�2G�#�2�s9�6/\nܟ�u2�0������M�+�g\0SZ�Ľ.��3\\K����\n�r�2���7�prLY��h��%PȄxDD~��6OO9�6Hb0���FY/�-\"TD\rl��BJ&���C�{0��B0Zu��?C��h����cN���F\rS�7�1?L�>��&K��	b�:lh#���c(\\�?�^]��1n�B�����8)��C/�~\r�V�j�e`N,c\nbT���jN�T����l�g�\n���Zҋ0�BbU'�u\$�-IIbeI�9I�WJ0\"\$\"�,\"L�c�\\2bD\$�d! �F�\nJ&0lOh�B�C�&K�M��@��@,5C)1�C\0̞ �\n%@�Յ�T�vN(P�*M�?c�\\?e�H/�A�^�2TZf�Ş��\nȂ�Nm�@vF��g�8)�O�UO�bb!�U/��ӦV��&��0SH2��`�x��Pp#Uq�aM��M)��ӁB\rfp� ����	��5��چ���k�@g�s�XΤ��,	��X�.dG.)\r*��.\"#=`�B%��\n	\\��7�*�U�\rV����fq`v=M� �u��G\0��f2a8)��=qR��{�\0.p�AfР�\0t\r��";break;case"id":$e="%���(�i2MbI��tL��9�(g0�#)��a9��D#)��r��c�1���M'�I�>na&�ȀJs!H���\0���Na2)�b2��a��r\n%D�2ÄL�7ADt&[\n���D�q��e�g�QB���e�\$��i6��3y��i�R!s�\r�6H�qj<PS��N|L'f1I�r\"ɼ� 4N�#q�@p9NƓa��%�k��I��t4V��-�K7e���L�xn5b#q�)53򍼈e�����_K�b)�\0�A��u���R`Q-\n����mi�p��Cx��m��ˣ�X享�З��@�7����X�p�^��#r&���jj���{p֢��8@H��1�i+��1��B9�C���4ѓ^�c�2�\0xƍx��C@�:�t�㼴'Q4E�C8^���p�\"��xD���l�&�4���x��|�\n\r�ҍB�P�?�� �9C#����\n4:1�(��Q�s��?\"(�C�(�N:�B�N7;(J2#q��%��`��5\"V:���\rk}k	�x�W�H(�2���6\"��6�*�?\r\0P��;�η����K�\"���{�*��8�Qh�%H ��^5%�:�2�h*��L�|S�� 6��>8A��6��b��P���o���p��bh��5��\nF:и�:�c�\r<�غ�ضm�x\nN�jXkx��[�\$�pqz�X��\"�%�@P�hI�\"�\"\\ٛQi����C�<OI�9���3\r(�߮˘@��i��������iۦ�2�\r����ˢ�3��5�\r��p;�TS/N2� �3V�kn�-^2ӀU���:N�\r�0���I�IdX�7���\"��i�>\0007���\$���m�eN�����aJ���.��Y�:D*5)�Y9%��1,N9I#�E[Q'J��+K��.|R�]0�cp^2N�욁��,&T3��\$IT�Em\$�%���'���!�|J2hJ��4��rJI��(�4����[K����\$ę�~p�5�x\r��m\r��\$P���md@��\$BDãnC%��@�P��c�\r!�놂�ӫ�6�\$�!�����rp\\�Wn��ˉU��-�� a\"@��P�J�.�z�%�b�Qb�����@@P,����H�LA?%@�D؃r\"pܚ�Xm\r�v5�Ė\$XNCb��ؑ�n\n9��!�*���H!F�����|DH�\r�\0�pCD��)��\\��G�\\7��pR�RYihXi6!����<�2AԄ��k�\0u\r���(��`W���R-y�fML  =�d8�`ɻm-d<�bb�%:<,�����P( �r>��%�8j�QB���+nb�'�0��Ñ<Y��� 9��׊�B��Hx�G)�1-ᬣ�0���-8����P�N\rڮ'D�T�&(Ԡ�Ƅ`�\"��kg�Ȉ#�5G\"	#���5:�̵G'�T'��@B�D!P\"��� E	��\0���mrU��\$6�H�u]�ƿ%S#��G��z{`�2�9'���&�T��)3x�Zs�jO�6����T��;R}B�[PX���\$�x7��\n��RaMH(��GjZVmQ܊��!���(���0B��x�蹠�0QJ��v^������yH �ɸ�{q�PA��J(pcaq�B����м�&���fC��R�=������n�ax�T�=&�|��0�x̹ \$�IGa%*yKmoF�d!�`ǈo!�C!�쉃�����܀�fG�1�V�	�^���	�0���{4eM\rB��.}�T\n�!��AE��i7��`xʋь�>�-B���.ii��������Y�ľ[ ؒ^9��4�/^p�-�I1�x��4e�^<�g��-�U&:l��Е�	��Ph�� �AO�y#G�����\n\$d���1�(b���N-xABDx�!�����Oi�R�,6P���iq����3��So}�@�ޠz���S�H�Ҝл���CN��!��j�LYA�Ӷa��g�o�t��Ky���Ts׹\\7���C��d�I�\$oa�4.0��DAI�j]Ր̑&�Y�+(�W�.O��@N�8z��G���X+D��bB�:G��%�[�J�f[���0_��e���n~�-�Z�]wu���K�=�����w׉��=����c��|p�`�*��19|�h�Q��ѪH�ی��p���cs���@� +���/�{/�N�V��ˉ�A͎4Խ��K�&0�N����'�q00��zbM�	Ǯ>��5;��盛j �П�J_���|�p���Z�Ź�T����Z����<�`��K�%Ś!Bh�O�3�u�wt{����ir\"o��lB#@�gKx\"\nD`�X��1\0�RBK�(��`�,ԣ�&CL1�m���\"E���.���FfPJ�m�\0F2�E�1^6���\0�`\"��ˈ�@\$x��V#�Ѧ:4#?,j�n��X#�\0�N+�@��{	�S\0����	0�z0��Lt7Ц%�E	�	L|c���/��\$�\rP�������4���*�����%Bq�@���A��\r��.̾,/�.���\0C�h��TfT�cbp����h����B^\$@�B���~R&�-�3���\r�B��.P��jG\rbNʨYE�7�b\r���fG�Dʴ��2���\n���Z�-��;D�#E	�L&#�@QHxe���p�7��f\$B`C��2��,*6�1�؀���:�*R�H'��T�D	��U�-bt=c�'�BH.!C�ApY�#M®��d�p�G#�b�ie��>���\$�P�N\$O��N&����0-�#�d��%TR'	D�윑�	�_N�^G\$�e�:���촵\"HGd\$� ���K+&��3��<��,��3%�����8��;,F#*�K|��_R�\$Ò='P@�A@��E����q�&]��q��4�.o��lV@";break;case"it":$e="%���(�a9Lfi��t7��S`��i6D�y�A	:��f���L0č0�q���L'9t�%�F#L5@�Js!I�1X�f7e�3��M&FC1��l7AECI��7�����!��l�i��((�\n:����Q\$�c9fq���	��\"�1��s0��C�o���&�5��:bb��14߆����,&Di�G3�R>i3�d�x��_��!'i�H@p҈&|�C)yN��ȃ2b���c���l��D8��&u�����L�������r��s<Ix(�l����̙��\n�C�9.NBD���:�7��z�2�KsJ;4�֦�B�f9����k��B\nԇ;�X����h�7�H�9&Qj���k&ࣈ�X8��0�cV��++��p����:9�`@%#B�3��:����x�3��s�As 3�� _(�����I�|�e�@Ɍ̂e��Hx�!�L+0��=I��Q�b�4�	J�9-C���3����P�-\nR�)���:��\"�B@��tX��� @1*h^��\n��\r�b:�+j�n!��k[��&�V�}n+,̂4���V�iBY�3[t5���7B�!��bֹKh�*?@S�!Ȝn�GP��֌�@�L��e�yD��H�jh�R3K;�hF�\"\")�\"`0�MC�T�SQ�N|ITUH ǅB\"�Mfp�Ü��TF��-�z�\nCz��W��'�Т�	#l�93/��깲��c#c�\"@SΌ��~\r��+�3��OH�ù@��C�)4�=Zr.�/;�|�s�<��;Ź�<��?o\n7���¹�r]B�c�����</{��\$�<0!?]\0�2�#/L�=Ô\$�=6\\���:��H\n�/I��0�NL�\\�Tc+ywc�!-�b�<�=}\r�X\\������#q�y��y��'�77��������2i����4H�he���)�Dw�uH�쭛`��*�G����\nkE��+@�\\D��X	j%Ծ�SeL��4��J���p!���C�GS�>��N8\$j��2ME�0�`�yO,��`��1(7�I�]��T� �sl�in&ę2hMOr���xe�͆�𞚈p#&	�8'v݋hvF|��2>�M+^7����I���&���UdG\nI44�iŹ#�a^@�\n A�(d�e?�0�9B\"{s�:��H<Q�|�;e�\"�.�y-������I�5�\$͑�30�( \n (@��\0����T��+��[\"�&L�HK}��%6��[��̆�C�֥�iR�R)r'0Q5�E��\"^FΌS&��;8c�BNk9�G�2�#�&hy�D,�� ��Y)� �?%�icф)S&o	L�0�4��<����+ф(�U�Ȉ���8��?\r��\rO<�SL骭9�]���3 �pV�XAF���?D��Gƒ��0�A<)�JYJ���e�}\$9�P�*q*m�x�������qB�����A��\\73F�\0L�d0���&��O�H4�B�����SJ�W��q-�q�1\0��\0U\n �@��x �&[ljd\r�Yj�@��쏃��)Lۀ�)�a|��ʐj��-�`� ���+����<iM+t7�g�����dE�%\$H֯��>�d��R䑤�\n[�&N���fH�4��˃��Z�~�SQ�\"X��Li;�7��q[�Y-y�F�[q0g?X���H����7����^���z�Ă�\"��@o�	��dŧ�DX(���u��\n�D��Fa��eL�����u�1P4�,�����X��*\\��!\$�8 A��M;y�5��`Юj\r7N���+�OAMW������a�&��c0�����5���r�mB�Aa 䜲R��sAZlY��\"ޡkR�\\�x KȆ�����w��1�YFD���A�𖕵�%,VM�c2�����'el͝�6�aN��>l�Q�lt��YS*��&X�ww�YY&��oy�m�R�V��[��o�U08	�`�Tװ��a�\n����!4*譣���\n�9W��;���'X�������\"8��r\\D�����3X���>�d��Lk�]�{�GCd~��ol�D�|Lt5�>��_'u	rڣ�>���Dl�\n�c@+�sڷQ	�ˆ2�[�Q���̺!���/��C\$ �].Cb�wb��5B��X��f�\r�4�l�:���c�%�+�ܶ�;��#������o?1ח��9��?���3�z��^�GY!f8!�d\"�Z:-�s�#�W��v�l��#k���w�3���pp�ǵ>^�Z\\���w��?]S�f���<S+�gK���A���z�0�\\ ��T1��h�����i.Vi49\r��	r�C�Yô=B&B�0�c�#̺!I\"�h��\\����\n�*,�&��/&��G��e�����\r��L�Y�1C��M	�R���%T�t<�Ćå�����keXj��i�{.�i�%�����Oj��l!b�	��f�V�F�nƐ	��]�*b���kF{�hϠ�.�:O�Gor�n����#�\"R��Wl��D����S���&���HȾb��vB�#��#�΍�\rhF&e~�D��\\+Kq:p���K��`����>�Ok�J=Âg,��È8.��n�2-\0�p��q~��'\n�:�EI*ӇMgl_k�P�B�����=�1��q�@�&fC��-�zF��ed4E�z��.Nr߅}�܃K�Ne�Y��\" 3\"24f,ʢ�.�*4�,�4#���G�X�hZg�(c^Ғ��\"{/�\"�P\r\"L�n�c�\r�Vc��Y)��%�81���#�#P��J���� ��Z\r��'�%f���~���jI���`ah�\0�0��R�\"@4��&ĉ(쑔@O�*?\r&m�T9�)�\"�e�/� J\0�O�� ��Zn�F#�!�V��\"2Ҭ�����\"�eZ�@��`�	@#Nė1����0�#0�112x+�/2N �&�Q�3�\n��E3s(�C�GE3)��g���Ι�H�\"^��52�N�\$���\"IEn�Na�&H��+��:@�.D�����	�a5Ƙo���`�bE0i�¸��1��<3,p�ں�Z��A&�r��lEbb�|̣P\n	�,ETD�)��9�V�8";break;case"ja":$e="%��:�\$\nq�Ү4�����(b�����*�J��q�T�l�}!M�n4�N �I*ADq\$�]HU�)̄ ���)�d����t'*�0�N*\$1��)AJ堡`(`1ƃQ��p9� ���b�:�W&���K�<�^�\n2�&��(�z�>\n\$��g#)��e�����u@���x�n胐 Q�t\"�ʊ\\�q4�\nqCi����\"��V�ηT:Shiz1~�B�AXM�����We[�W���Pq�I9�kG2Ya�A\"�ʅK�2���z����ė��:��\0T��9S�3�P41�y�_��yA	A���\$#�L��+D�O�H��U�1z_��Qi�L�	T�+DR�\$M��A��_�*cƆ'9PW%b�'�y�<k��'�K��r�j�H�I^ӑ+��0I���E��Ozr��iy`\\B95(�<OI�� ��h�7���@�d�:�SbB�1�B����9�\$	qM��qK\$-^r����_���CB?���@42�0z\r��8a�^��h\\0Ѵ}\"\r����p^82Ø�j��xD���ARY��`�|��Q~�S9-J���<�\\tj�M\"i����VA��Q%�a	{�t�zV!��4��s��iLr�\$P��D���\r!�p���c(JA�DZt��J�Iy.Q`g)�51�EF'I,Qĩ`9D5�Biچ������Q@��vs�}�^���2F�%�r�����/��D��\\T��0)J���8�7M���+W��QπP�(���OO����Hd�0լ!Ps���9^3��%6-�/8)I��w�c{4��!��=���\$T7���\$b�����C<��qz�Qc�η�u'g9j����M���t���^��ļ��s�\"Z���d�&W׷!�B���4z��1+�w+1\n���?G�@J�#�Se��z�s\\���@|��\"����>X�,F��Z|Y!�\"\0@�ak��¤��1zfT�^�\n 2`\"M{�4qB(H a2\",m�4!J9��.=��V�w�\\Q��|��=�3� �	o��A�qBES3A�:%�ZB{c�Ea�!a�E)f��'�F�K�QmM�1b�[�R7�͂H1�Nԃ%���a�/���F�aLu!N���B(r����gH(�ȆX�b�R�M�(�J5+F�@�0�ȁ�Bݩ�3-�h(Vr�<��X+\rb�u���hwY�F]�E���d\r�0�@�:�'|��<�`�4�	�2jƍ	0��H7� �1-��%̈.9�\\�\$1>(��0�S��6\"\rJ�CR#(��2P��-I�Se`U��2�YK1g-	t�֪�[+`<E��z�\\pji\n�(�(��,��ki	�����2�\nZ�x\"s\nVI��wD�db~����H���B9W	�rd7�xAKx���Iµ�P�Bťt���\0�UY��D�����iQ_����C�����;�\0������øz/Lh�(�6�ϓI,뭯+�@�\"pv�	�\"����y#z�U\n�V)F\0,G(�MqB�mg�8GU�X�\r���'Dג��B)I	�Je-�V9�H��e���h	=�Ar��)�G\r	ݾB0r��>9D�O�\"�W��m�cowN����F�))J��W�6L��BG)��@\$:�D�'[�q/%�ܫ/{EQ0&DЧ�� ��3�P\0[��J\r*�,Q?�	+�'���K��/Y5&�4���h��)JxBQ@-%��g*0D��D�N53_V엊l�O\naQP/)'�	%��`�a����?EŋE	���\". :��Vz�Y\"�*Xɞ�G�M��<o�,!*�-MX�zi��3K+�*��)h�eH,�m5��J�0)��n5�Fr����('��@B�D!P\"�͜(L�Q��`(\"f�[y�/-���ъ��!����&of1-t��\r��զ[��j�!���0TP���\n�N�~'.\nzH�inc��xgߩ�۾��K��3�:��\\)�9�vm�G�� �	K��S���cD�E�@�υ'i,��3)�W�1����~�'-���r'��\n��O�ރ02����+����M1���N�D�+E�t�x9��o|���t��T�l<�j��m\\΋��|�WV��!���	al5.����KK�����\"Ny�z�e\$O��g���ϻ'h�Hch���8�Aek�k�a;�alF��\na \"�0�������{��ȟ�xr��u�ܣP�(cxФCM>e!�<�6.�~U�����\"�ف�^6m����J.Ǣ�>�ݴ A�p_+��U�bVej⩜��^���B<<H,8d�R�A\0@:�H�L0c\n�+ �FR��j\"���c��r�g+Z�C��e�`��L�Ci#\n:aBtgJ:��qf⠅�\"���G�sD�4�foc�ʃ:*N�Ƣ���DL���{��I�B������!ZllÉz!~tGHt��(���l�dpl��9#�.&0Fr���!p>��/c��=��ɠ�\0�b�-F8cã�2)�_�a��1D�\$�_b_�6��coOl���\\<�N�����&\"-�����Ɔh��~�T�S��Ϩd1��d�7!r��q�<��N�y�ꑲ!��m�h�V�\$ެ�K���/E�J��4c����bۨ����(J0\$� ���:)��2In6w�Q��H�\"��L��\$�h\$,�,��2�'�k2E�.��4u�9f��X�!\"���¶ycNy��a�r\$*��E&qk&�n���b	��*�-����a�e*�U)�mRQ+�R੎�D�����#DF^!(�U� �o�Iff�%<R�}��#��#���-0��f(_����J:��Dߋ�*°�b�.�2���e��*|�%`��&o/(�R\0��x/��r�&��s]Q�\"���d��,3n��fN�<��7#*�*sm%Q����K9G8g��o�}q{7��߳�)F\"*S�����a��\0���t9����b/\n@}�>�\$uxKR�n;Hy�?s�&��&�}?G�<2R8s��,w;�x�i\n��h!1�BA��%CBd��/C���I/���~n/�8��@��%�wEO��J�9�+�f��YF�\0���GO�Eo�DC�f�����T]S�IO�5�Fp�I�\$�ScJ�KP�t�,�}�4����f�;Ә�\$��r'E�@�K��MҷI���ROLr�	,���@�r'���N����^>�qa7c�K�0(����;=ԙ4�e+	���.�(�R�\\^�V��=u*�Q;c���GH�\r�W1O��ED���\\�0<�8&�\"`�\n���p)3�A0�Lށ�C���+~�t��_�\"�k��P�q��@\"]I\n;�h�OWՀ��U�2��}DhF�x�	���.d���c�B9�a~����h%�c=jtaj��L}+�(*�[3M�\ng��;��\"g,�P�d�Ic��6�����[Qt},di\"���e�m+%��V24�7�_��k�����q�h��g3�i�Z�\"I\n@kl��*�>�ggt�\0� ���\r��f�g-�ƺ�?�;b��@1�/��c�@�F��%�\"SMd6F�OdG��eO#\nC�T��g�za� \"#�n��n���\0";break;case"ka":$e="%���)RA�t�5B�������Pt�2'K¢�:R>�����5-%A�(�:<�P�SsE,I5A���d�N����i�=	  ��2�i?��cXM���\"�)�����v���@\nFC1��l7fT+U	]M�J��H���^��x8��94�\$�{]&?M�3���s2Ԏui�z3`����̞*Z��%\"�xܢo��Ji�t�ҵTA��=D+I?�� �y��12�E�Q~\r����u�x�.��ue}��2T���?����r����������N�S����zhĬ	Z�ԸH:�����\0'�i.�o�.ķI�ă���[2H��ָ�3�Ђ�\0��[W-o:\r�p�\$H<C'��or.����+����(��d��ɒ.׽�\\3�����)�V�D+댙9�;j�Ȭ:Vץ3Z��j=9>�Zؕ�	�<��S��FDh��,����;�&:��O*�Ob���s�����4Z*��\n�~�\"��f���A5�4���6�#p�9��0'M[k.����5�r�#,*@��U6��!\0x0�C:3��:����x�q��\rgZ��p�9�x�7�>9��X��J�|�(N�P��x�?{W+�JN᧨԰��/m[H�ԓ�����X�<�-%���t�H��+c�l�9���%>-[��@(���Nn�N�%>�#5�e6��;խN�dL�*��Rث��%-�į6��EN�O�ͭK����	^Ʃ������04��'AR����ޚE�^kc���g{M%�ˌ:���u9�9Ԇ�Ul�I��ZdM���znK�JQM��A���L������/���'9�5��鋵����F�*��b��\\�=�	�������iG�@�w���P)�4{9G�IB�]�{�[��x}b�Μ��J�E�g]��#<G��ӡ2,����Թ�YN��?��P����=��x\"I��F�a,�h���}�)����� \r��9�E�Wѝ��9�@a�aA�0���Ɋ�_A7���C<4�� @�M�o�߫��T�r�e�*�/�`u�Ю�´Z��o&�׵')UR9��X���\0��\\h�e�A%.�#�\0@\$����j�hl�ɕ�ƺ���(^/�F���ӃQ�)�ʡ�;�c'����Rk�{2d��՝VZo�CW1+ޱVܣc������T�J�!�����#�V������8��V�H̕	�ո�ȩM\$�D%�*\$TFLL�\$\n�s�ep���\\�f2n8��nNj�r�u����8��E+Mj�u�����\\+�;�YʺWZ�]�2DP�LE_����|�˹(�tHDÎj�j�~��{?U��Mz�2	Č�eQ<��0+E9�w�W�9j��DI��'	���.|�	2+.�)DH�k��&yH�;���kU�����\\�r.ei9�R�]��2���Ý^�9�H�����1O�K���VS��4T���4H��!��B���Z���0%i�c�|��)� �P(�N�=�����4�y5�H*�bz�3���b�h��e�X�K��}���v��e�k�\\㎯�\n.\n�*��h��|�\n�d��S���3�ۅi��b�X��\nM\"�����Yjc�G��AR!�4kT��%xᶷ�-�Hv�K���_�Y'�͞0�D�a/D�&�=�ޜZ��t�����cT�1�����'Ɛ��3�g\nX aL)g��D�i�b_7[�z�k��,\r;ҴbS�N@4��&<pA�A�%v�9*���m��a��GRM�`kGҾ��D|(�0�t���(�T�!+H�����[��Qj9�;�<�6C��Ft+*���F;=��U���b��j+�\0�ʛ���P	�L*Y%\"��	4�h:QA�\n�ɼ�\"�֊������R�:,���n�� �ZSK8m0�e��cE)����Oʾ��k����z��]�,SI����a8�����7���[�@b��N8x�8؍k/[:�Y�ǉ����U8�d�L��ɷ��9�S|��ʺJ&�U�Rp��{{z���]��컗E����+7��1�8�b�-����F�F)&3aHMA�8&@���0��LJV���Y^:w��1�_����\\��8�p��<���>�&L�J&������j�����E0u��H�9���k��MF1D�(�˨EiK��«��=��S��s;�F������^>�ɛ�`�*�p\"��8L\r�2����gV�9���Z@o�˒9&Q4:��\r�\0=D���#d7�nC�)��7��d�u�Wp��m�W���>:�K���JJm������n�&�!�m����C���5�P�ghy��5�h9:�;�����	�]��fk�/p�@@�vC��Bn��J_����+ʘ��jXM��'&MnX���O���gd�O��ǒ�V1JHT� �\n��`�y�e.��;���,Uk��͚B����z��hBWC�g�@��	��Pb1\"�S���z��vV#.���	��䣒��h���p�(��f`�0l��	�܄0J�δ,�Ԣ~�LB�)�	\0HhtK��ʺ������>���mT�����PP�q�H�-��M��60,h	FH����f�L2��4�+��BvP��f�����Q\rԓ�����+hy���G,�1l�.b�l�l�(�xمu�IDm���]��������m����12����ě�d�C�¨5�+�bٰ�&�n�h:��j�uNJUc^l��N�:cpi�F��ҕ�'\"�3:�BԌ���\"�lꍿ#R84��n�l�N.��r԰�t��/�MD�4���O���n��� ����\0��Ȉ�\0�\r�\n�^�Q�K�H�\r���\n��.nA�^8�'���H��h�O+HF�X�B�B�I+p�1�+1���.ӱ��W.'�b��,��-N�l��1RZnd�*I��5ö�Ij�B������-������\r2��q�2�3���8#1�2�ʿ��e��,��L�\rp>��4����\$\$���#�nr+M52��\n��L@�l�\$�b\"��#�k7R`��49͹9�#5�,݂؛�EJ@&����p&�G1��4S%s�R)��gw�d����%6�L��Q�������P���0Rh�*�3���4r�f۳,�B4���o����J-�1t�K�B\"�\"�4�-R'�LsK-�q4ZR(F0�,�-,�lN4^�%�����\$pTnp�sHm��K4�38C�5k/J'���O�7cP�g��ǡK.�J�|OJ��Q/�%E3(�'{/�VD4�@�iT�N(5MeQFt{C1B���n�H�U\$��MR�������P3�ooC�0z4�Q2���RcH?D%Ub���)��1����Z�?\0/>������*&5SU`�U��U�n�EoV����X�5v]�@����c'��U�����+M��F����Zt�\rZ���4�I[���Q�%U��8��<RJP-\"eI4�����+	iRJXTW�Q+���y3�_\$�_v ��O�4�`�����t�S4�U���Ա��Ba��3&�/t���HsI�IM��.�!�\rJ���S�a�2��P���\"�\$�,���t�T�`�q�r�H�I��CH�s�;\r;K&R�sL�b�9�@\n����p4�V�RN��j�0�ZP��8D��:p8��79c\\����Q��f�5��NpPDUfuo��#�m���e�I�\rh���������jhy0�p��H�i��C�<�oo�&cQ��V*�%v����@1 �r���wpXh�G\"���c�~���'yˣ+¼���SP��`�WlRN�r?��6���Uw�{��cH��h�7�R�!=��j>���b�輳776N�Ʀ7D�V����1�HGDH�����/<��vt�~�ȱ�9�V�]ǠjF5�q�v��y�~XZ���&V��hsC�Į~g�5�o�l~����%�<�I��if1zTD�+��pv>�eVlP�0���*|�3A����.R��v���n����@>_`UN��l��ب�c苅S`pr��� �N#P�";break;case"ko":$e="%��b�\nv�������%Ю�\nq֓N�U����������)ЈT2��;�db4�V:�\0��B��ap�b��Z;���aا�;���O)��C��f4����(�s2��C���s;jGjYJӑi�R�AU�\"K�`�I7�FS\r�zs��a��V/|XTSɇZ�v�HS��^�+v&�������k��C��i���=#qA/iHXE�l�KȤ���;Fv�(�=�v!ȉ�VWj)q������s���s]�)Kq�{�����f�v!�����松i<R�o�@���Y.H ��(u3 P�0��H�3�k�N.\$�zK�XvEJ�7\rcp�;���9\r�Vu�N�Xk򊑔��0J�D��B\"ő	D�J�H:e4�u�\$!���\$A�L5ѣ�睱�r�����/��zN��a0@�E�FP'a8^L�:*uL�c\\ ��l,�� ޷�P�c��8F48�1�kx��\0�@�#��11�(@;�#��7��p@81�����`@R�C3��:����x�_���?�Q����p_TuUX�I�|6�4�,3F#m24��px�!�g˒�J^ �!@v�dꚹCK��RSlFP��i\"�/m{b�@�\0���fZ'I�Y��X*6V��LS���I�޸k�PA(���h��\$�C+���F���u���S��\0�(�0��e;#`�2�w]ڄ�A؏�%9\"�����A�3TT\"���J�eX����Cj�\nu���=&d�b@�L�\n�.�P����]66SØ�0�А�&@�R&P;���sɑ����EG�^��iRe9��,y�s���2�mrJO(^�e�z�o,�Q�i�/gi\0��sdY.���̽e�Lb��A6X?��@���bx�'����ݒ|\r���C��:��[�RÐ�0�6��#�|!ۣ0��he~��ǂ,c�XD\r!ͽ��ƌQpn��>0���Rkt�*�Yq/hA'���r\"��0�t��Jj>e,�9�;D�R(����t�����M�0xo|�\0<�\0��J�RA���\0��:j�:(�C8a���>D,�C((`��t��H�!�X�Aj]M!�4Ī#��\0Ø [��)@@��n-*�1����\nɝ+Un�UڽW��`�9�C��Y �AA �P\"ҕm�xj!��8网�(ة�9��T�˙r#1<���\"׉�y��&��2Z�b�L~�\0�b� .U��Y�ep���X\nD��\\��Bʂ�f(J����Hm\r�6���\0�,�TA���H���Z�P�9-�����DĨ��u� �Dh����*��x0�>@Ч�|�[�|4������sՏ��3�@u�K6*7��Qj�TE�����=A��B���Gh�H�zJ@PP	@�T��\n	�)8�R�rg%�  �x�T�I���E9N��\\J�4�V���Q��Pޣ�2+�f�͙�>\$&nL�����ҵ�\n�j�H�dZ���n\n�S��W4��h\r!�4F`έ�(��y��`�a�4d!�0��І]b��'��/l�=��ׄ�^f�P\$`���@�T��\"hM��R#x�.rA�����_��PC�H�ìE�L��Eʙf���H�d}~P�7O�E LXqho�A#\0�!���1�s��ꨯ�(�;�K��� qJa�\"��\\4�ɹ9'w�t�H~x�(�A�;*�[�w��_	�r�E�M���\$KɰF\n�C[��^N@�2��ˮ�/���E?/,��(\r��~���J\\Dȸb��9����`;)m�����Ĩ�i��M<�מ\\Q9�'&1N�Ł`M,��=\n�\n*C��51�:#;6.\"q)R��]���o=ҕll<���0:�o�;�\\���L�;r�ǩ|��K���]�pїvP�+��8��uj<��m�C�:y��҅B�!0�\\D�]�q��(�>U�J�T��)L~���]9'B*��-�׵<����;(��n�\n���Z��:f� ⧺}���S.��h86��Hp&��^g�u����4�à\n��>������݃=G�´1~�I�6�)�'�@���x+��	��sFj̈́i��t��r\03�mgZ\rd �hBs��&j���L�vZU\ryA�&��zӊsN�wq��-���=� ��V�*K�op�C	\0�wL]��*-F�2�/ӥ�m�/b��:��x ��@��V�;=f��l�?]��)=���&	�.\"n�';���{��p���BVǽa�7�ٓl]���^��v�%XvGyb��1����BLH���7�&���\rt�Y�#�s����KF�a�H'�\\\"�f���@s�0_E�l��D2Cf�k\"A��?��i D��4Co�9�b(�Bb.E����'�p8�#�8pB7Np6Ci7h�����CB�:Nr:�!F,�o�_��\$�H�6�\\^�V5� FpF\0�1�(Gt;if������6M����� r��Md���bT �\r��\r�\r��\nF�xMH���B��m�rm�ŭsU	-dr\ri�G	�vd!��z4q(�q�̛Ƿ	\r|9�*�GZG�J���qS���\0v�rwh�J�z���B48��{A��,�ͱ �vԱ��t�_�d߈��QJb��g�T5�p�\$&�\0\"\")!:&�`\"\\<�2���G�Gd@*J1a*�k�p�S���\"j���VF�:vB�)j+�L�����Dd��f�X�Ѧ���&a-o-wR;\$���OѬFanaRX�?\$�B�21%g_��d��a�{Rh^A<7��B&�\rr�d �?\"4c�q�ޖ�sR�/�g%pwR�(1�&�+R��.�30�#f�̒0�C�=��i�2f���.�L�\n�\$d�� ��i&1�R��P0�-ɲ��1)�h)���\r1�2�����3\"&|PI0��d���mo��,�D��5����6N�G\r�-a6��72�5Sg&R�/C1	�'��\n&IF�5���s`7S�I&_6��o�:�9�W(�x���1�>�q,Gh<��@���3������\r#HrL�p7�l�� H\rv �����)�0xz5�kB\n�t���@�@r�>�Z�7�!�Q@����l�`�\nFR�hE<��ȟ��ȚT\"V�JS����T��\n���p�).����/x�ly\"#k��z��]-����9\n���<)�(Ah`c�7c{#m\n��#�)��s�R:0&&����\$I)����F��s�!���s�����͕���h)\"����PNj�:k&<!dÁf��oG ���K!�\n5\"�I�7ԢMA\\/�T�-J49��UQ%;C�\n��(��=2�Z\r������\"tn�s�N:�\r&5�{�'Vvdjo^�I����g�[�SO̢NF8�adCS�.\"f�\n�����Pr�ۡf��q,@�@�j3Ta4Ç�.k���E�l��f1	aUb�G���'`\rLH��kok8�ƫ�";break;case"lt":$e="%���(�e8NǓY�@�W�̦á�@f0�M��p(�a5��&�	��s��cb!��i�DS�\n:F�e�)��z���Q�: #!��j6� ����t7�\rLU��+	4����Y�2?M�3��te�����>\"�K�\$s����5M�s��:o9L�t�u�Y��)���,�#)��g��ALEu��y��&��C\\��M�Q�p7C���j|e�VS�{/^4L+�R:I���'S=f��P���k�ʼ�L��nx�\n�����O��4���DX�i:z�E?F�Ĳ����C\n錎�*��[r;��\0�9LB:)�#*H��c�&�*����1��0�)�_\r�z\n\"(ck*�����\nJD��m��+�N7i��1i\"\n ��k	��{�7#��D�0�c��0aX�9�x@;�Cg-ȉ��;� X�`ж���D4���9�Ax^;Ёp�&��\\��{*�#��8��h\r�B���BH���px�!�: ��\0�񌑋+\nQk�U#�Vb�c���<�\n,�2ț�W��|���2?Bs����¿�⺬71�:\n��\n�'@M�o#/8�q[���?j�83*�H�BX�2ʳ ��ԶCL���*0:��BJ:��UQU4B35�r8�%��kϊ��Hʿ��z9�����\r5��Ek�ԑ�å�9\rp0����ʐ�H����*�9�@V\$6\r�[��c�7?b����c\\N���\n*�O��;/tb��[{�j�ka�zċi�D4�\nu�J��;��mth0�ĺQ��q��޲�XՋ���2W�\n)�|cH�����6�*�\"�<���ʽ�XD�!Z� ���\0ړ�ӭ.��C�#��P��<�u*;�a��P��VJ2�!���)�:\"&c)�����b2��ܑT+����&!4�B&��>,�3��Y�J�5�l<5�+�8em\n�#�b(ɍ��*��7�`�^�����B�y\$��<�7�R�_� Ɇ��Ӛ���|��L��N\r��2���\nH�JW��AC^<3!Ĉ�!�.GB��A��N��N�AL�HM9�7�C\"PN��<'�����P��C(�x��b�\r����A�>��il��.p�(<���P��9x\$@�8h|}�;`J�<�䀔��Ku��gd\$[N�/'���T�P�N4%���|��K��8e�ty�=DԷN\$\"E�+/\0��wGeԄ�r�RQ^d�X1+��DC��\r�18ޙ��IE�_D�d�Cf2�y�B%�I��b[��Q� `e\0�W�j���� ��̄�5����*C�T�~kܺ�\0�\$��UlPPM!�\nxḞMh�p�+�7�u����SH\r��Lg�ۖ�H��\$��ξ��(MM��BR�Н�@8/�ڤ���L�\$1��db���.ظ4�NH�2�����F+�����O%�!���E��%��l���W=Dt+N�ݑ \$D���)[�\r���0��^���gd�B#�^K�i�.H��0�l�dXJ�d�YprIAm,0�t*b�fJQ�26ꝨM���#��g���	�L*W6�lue���Vˌ�,�igdAޠT�e�m�� �9�R��kS �chr8�mC�%�b�KR\"��d>�@���E��%�yk���HyE�Bb�<���\0��a<~a��ƇV^cL���a��5�c��mY6c	#��-P�\"P��������5��!�8e�Dϱ�?AA	�\"(E���E�vc�hD�+CA�#��\rM/���綕��^�to�����GG����Y��S�Xm�5�r-M�kuH�7���c��&<%��\n��+/A\n�T�p\"; ����C��\r�P�8Ԍ�(}�FiUQ�p��	�C��(��\r!醼w9\rᔚ�w1���XB`oP�v��CAZim2��502?8�azՋv�tC��������܌L��kE�B�\"�1�y�Q�(	-D���r�PV6������&+�Z�?A\r�2CF�kZ���5������[�t+��سK�<�3����1��<��(FSϽ�Jr~�)�fh�����Aa Z�>��]�J̐8:*Z�1�j���0�Qj��!q�%��A��Dg���^,�[Uj�H�9�Zal� m�����\\�HU�]��j\n4��E�vW�Awi�mc��ο�Q�(&��g�a@L�^��t:���f�R�����_��Q�}��.�ߍDG�^8��������L�+!P��IQ\"���Z_������c\"��C�\0+z�,F�t�A���c��ҷE��Yp��^:	�	<\\ı�<IZ���D���3K��1�sK�vt�������زL��6�2�I6�e�#��XA.���(�\0/����\0��LTĆ�fb�/M�h@�/����k��j\$e�2od*�-\0�>��<EgbJ|��e�sPO�7B�e�h�8,�0�M�gc��N�m�\r��f �^�����͸x�L\r��k�^D~�mJ �6%��`�����M��>��ˎ�'H�;�ˡ\r�G<�0�!/�\r����·����������n��;L�����\0G W/,���+�\$�c�ޤ\09B�Ub�!/������H��OqUG��1\\pnR�M��'���\"��eh���C[ɜ\"�@���+�ޅ&��i�>\"`�������`�A\"��-��~,�����d\$'�W�B��G�L^Q�1��X�F�m,�f�i����_/���S e��\$����͌�aM�?G.!�����\"c�˱a#!\\s�O��L���\"Q!�JsPG%�\$G�\0@� �4r�Hf�;C�,1-,\$��\0��\";(�%\$Pm��\"�&c��)��Ujdq�W/�%R�A��=rtĉ+.WC��̱0s	�߀���j/��2�Ye��;-��6��'�v\"�y/\n4���/��`0P��i�����*�r0�C29 S(��u!p|Ҩ�*BrQ,3A3RP	��1�4D�*�(���D\0��Z�ʱ�(�*��E5�3��#��\r �CB1�@��r��e�V�D\\cvί�'e�,Ӡ0Σ:l��HfU#�*��:R���;;L#��T&T\\idj\0	ӯ<\$}#�|[�!1`���Χ��?�;/%R\r�V���.B�Œ\$ʦ�D[8�:?n��f����@�\n���p{H��\0�#�8�e�\",�(Ϋ@��\$�}7tZ5�KEB)\0�\"6^�Xu��t����B`�⠤\"�}��<��A@�1�3Ĝ<�;I��-�x021�A+�I��7�2�D*Y�\0\rt�%4%��\0��[\$p\$�N��\nR,��F�z�R��a\ntp�@���O*(+�PG\rL�\"����b��5�H5(,��@�zĦ6�<�R�\r�Rg\r�B�CFmQ�L;œ �c�Bd,���\0KpD#j�@o��/�� �����P&%\n\rQ��Z��*����ۀ�U\$�O�⎂���1��eD~��k��Q5'�z�J�t�p�e���b@UPH�T�&��a�М\"�1��L�P6��ͥ�b�	#�";break;case"lv":$e="%���(�e4���S�sL��q���:�I�� :���S��Ha���a�@m0��f�l:Zi�Bf�3�AĀJ�2�W���Y�����C��f4����(�:��T|�i8AEh���2��q��1�M���~\n\$��g#)��e��\$����:�bq[��8z��L�L4��r4�w���a:LP��\\@n0����=))L�\\逆X,Pm��@n2e6Sm'��2���	i�� Ǜ�f��S0������Ɓ��M�3���{�q�[����ܾH=q#�\n2�\rc�7��;0�\0P�֒c�~�\r�x�0���2�M!�Y�^�\\&��jr.�;�\"L� ��ʰ�cR\n(#��6�SP�5�Ȓ�E�P�:i#|�\"	�ܵ# ڽ\$�{\n:C�%΂�P����X�p����D4���9�Ax^;΁p�'J�\\ό�x�7�9�����J|;#��5�#p�Ϧ�\n��\"z:�x�%\"H�h�1\$�Sƨ��U�0p�:�c�CY�Bj8��ۺ:��`�W��ԁBP��'):d��BCNb����8��6Ŵ4����*�P�e�@P�� �X�6��{!�\r\"5����h	�x֏m�T���Uʶ\rIS,�T�\rt�\r!� ·bUʙS@�H�2�C[ہ��z	k�Îܯ��72ip ���,<j:�����������l���^=G\"��&.h��90�{���+~9����\\�uz3UG�3`�u�b4D���%Þ�I�\r;R��̮�Z�Wʱ�m\"�����#X�\$���H�5�H-���R{\0006���\"�\\���I0�2:��m`���r>���24SԎO\\�Z��N��L�Ec}\$4��h���M\"ĥ\"\"p�Gs>O���z���<��g�1N�pC�V�\"Bot�\"��z�!CMrbC-ދ��A�:%��H�q#)t=5Ƹ����C)�����I\$\\�-VxG�2�\"\n9B\$m�\0M\"᜽�E�B[)<7\"�b�L|!�\n�|4g��B�_R�%30�	8X��|/p�oCHl��t9,^���\$��D��#�4k	��ʅ0����//Q]���\$d�U��\"��d?!��4�*4\r��1!�\\N�Aa\rA�\$�hI���-T:%j�g��5��ޜS�uN�]='���!�,�D�&�\"+�D��4���G	���u��0�̓\\\rj�B��\$R��] �Ha\"a5���6t�����7'��vO*V�\$�����|A�Z(���K�G觉9&��a/E�JJ4�M����|�Ē\"I�q^��˲f��BUe�\ng��\r�xsT�m=j������bRH�f�gP�-�ȺD\$4��g�( �KL|E��� @P5�j��i��\r�~����xe�`�����OH��@R&��\"�h�w4N����T́�5}e��RA�!�����CDr�BMD��JQ�6z�q�b�gpiLh�*�0G���3(����H�C\naH#�}@�4�_��B�&�ial��>aHiD�(��LI��&�@��H���y\$\r�tb�������񴌔8�K��Wa����\$�ڭیs6K'��gɗEp��X؈P	�\r�&��(2�}�0�sL�p\rK�3D�O��V���|C]�e�_֭���C�3<�F���Ӻŭ��E	#�����#䄑�RN~���&���0�JF �0�s\r�E\nؚ��f\n��!b3\"J�e��.�ok��ؔ\0002�[r�#��zÃ�PV��v�˫%F��,�d3���G_#��A�x��hߐzcIsdo\r2�7tzD\"K�Бv�4Q�-N��:FƎ�Yj\n��J��*�X�����V�	%%�<��R*U>�Ƭ�p�g���]~��y��Lc������\"��QB�/�T��i#~zw7ȄD�\n�K���᫨\nR�	7��c�P��vq�B�4FhF��m0!�鵄�U�z!d��\$ �X�N��ܜ6�o�e�:�ԋ�;��r�{�1���a\rA�|�5��@��k?�h���`h8�4�q���a�\r�m��/�Qj�P�cQ�������,\0��Aa\"la[µ����U<���R�����N��yR[�	�#�7v�\n��/�-/��H,H�����A���k�KE���P\\t��u��]��5t��<m-��P��i�-2,�[6��I)[����J��g�ϯv�y&�׹�^��{T��ɗE��+\0�Q.�=h��da9����B���v40�٪,W�H�1#�\nC�����\n\"������r�r��v��-b.�#�Zc~7#v�lV?���~�� i�T/0�P�\"��N\r'`�j�oP\n��%��\0*��&�%�.%Q�XӢ��D�B��E�}�Z:b�M�\0_�d`L4kG\0B��/��Bb(W��n~`�^�K`��l�\0��fp.�b��lT.&B]b\n ��Nv\rx�<\r��FtVfz�png��9�x�t���K␍#�X}m1<��z]O�{	Tpa�A�=Nڱ�mHp�Vj���C8�q0\"��\r11H��J�+��sQ?1E�E�G���:&��F�-����q�/��o\r�Ȏ?�^tpb%%θ�T2J�ȋ2#*�\"�\$\r��0��6���c`k��*:#j#.f����ȱ��r�Ao�FB�TmNd8\$�:��X\r��ќmG�!h�d'�!����)Q�V%�X&��m\"P'\0�@�!��,RJ'25#%\n�r�>aQ'&2K!E\0��,�5q'g���>���lVpK\"pX�`�p�n#�NlR�(�%��lq �b)zXlJ�)�u)�i#r�+��Ӓ�jr��pHf*\"@���NF�J�#�K��U\$l\0�7BS.��\r��C,��I0R���\"�=m�� �1s��W/Z\n�^��#��3��3�lSC3�X���^��g5�&�l�%ΥrH�d*�dc(|�\\���%�K���Y5r(<	�V�-�HB���HB3#6��@�2�H����\"�;MH&h�F�������B\"�#P\r�چM\0�N,��L�� �%Ec�5?qy�7�*%`�P�p\0���;@����\0V.b&2����B�kt%BX����\0�\n���p�lT9�Jr�x#\rf[.�%\0�4V3����3�bs47�EE��42�\$�@M��7,��\"\n0�7��@�F�H��i(��< OJ��(�U\r,U\$vt&�\$r\\�H�D�Nq��>Q΀��2Y\0��Hh���?�;Nʖ\rs�G�����bSu\r>�I�F�HndQQBZ}�ք\"�cE�ǧL�LC3�FP�#�\"�fz00�*�v^e�U�\0��^B��F�&0p��S�\n�J����6�Q��:��.e_\0��\$��΀@/#SL��l�yO�tU��ONl@�å�_��'��G�d�┩�6����";break;case"ms":$e="%���(�u0��	�� 3CM�9�*l�p��B\$ 6�Mg3I��mL&�8��i1a�#\\�@a2M�@�Js!FH��s;�MGS\$dX\nFC1��l7AD���� 8L��s�0A7Nl�~\n\$��g#-��>9�`�\\64���Ԭ��\r ���pa���(�b�A��S\\�݌�Z�*�f�j���Si��*4�\rfZ��e;�f�S�sW,�[\rf�v�\$d�8���NJp�ƹ�iɺa6���Ӯ`����&��s=2��#���*�L�=<�Cm���(������5��x��=c��99��X\n*v�3�s��,)bȳ	hK ���C��&�۬����7��#8Eqk6��q*�2\r�#֗\r�*��O(�8AhP�1�q�H�&�t<�k��4��@�:��X�9��X�� 4.�0z\r��8a�^��\\�G#s�Ac8^�.�C ^'����� ��*(�7�x�#\"�4��C��:	�l���[z92��\"ɧU*x�Ӯ���%\r�\n��⥈(J2��5�{_��P�7�/¸⠶;nωi���2��	�\nDl!K��3��B,�\"�K>!��Z^�%cZ��ڲ�Vx��3�Bqc����%��ԑ��\$�/�����,���\nb��;I��<,�/U�C<J�E5Rz9�i�H9k�)�KZY����\0ڤ��tϊRÈ�_rã��h�ܮ\rl���9�ˈ�buH�!C��'�څLT*�Q�ɵm�4�&�죘|!��0Ҳ.�p|�9N�~�����1\r�����	�+�F�2�PܒQ�Ͱ7�0[v�t���#C�H�#V�,ňZ�H����Ս�δ������0��N��b�BHEH*=q\0�^�iR��C��ۑe���Z���H��s1n\"͊r��#!aN��}�6q\rm^�l�T���\"B�-�ǁ�!��ƀ8AG�9&�m��77a�ސ�� eLɡ5&�ܜ�t���9'��y	\r*=A�bdY!oG5�N��!5���-������a�(���R�q�h����d�raLi�3��֛Szq���d�S�nOnD�9H����!�y�=�l����7�<3�TJg�xa9��5��*u��\$,��DQC1�~����uu��l\"d�=�%���!��\"RBJ�ԉ��XqSJ��*�p�v�s�����i�:�d�7-)�\n (�A?\$��GH�)\n1\"�%(�)S�vԹx�\"���\r������g �Ly�s*S��z�I_v�A\$��I!�3KIq/J�C=G!�4�	�*C	���: @Rӌ��X�s�ᄁ��1 �~�Q)�'a��5dM�YՔ�IU�w\n���0���LI��� �@����y��[!�@����C�eE2Y�'TRM�ZN4��'Tv��X��U��h�DC��(�)rx��S��@�C�<�h����MY1	<��\"�i���Y�}3�C��Ljk�cYM[�6!*K�Ds�&'A�ؖ&J��e�\$�%�p�A;��('��@B�D!P\"�KL(L��l!sd�	=�~�̸P�����`L;��D��\r'��(�S�]cF��P�z�Q�y��\$Eg��\nD�:���O�\$0����E2L��\r�K\\vm\\o�j��������2�*<�[{_.�!'�c���:o�I�O.)����=\$]CC)�\"��	a��H��e�`�\0��\"��+j�6�8��K1���\\|g|��8�e���&��T]J�UNFs�KVy�6&�8�tF�q��U%�B@�W�2\n?�0�FON&e ��잼mYN�g�*�5٣b�] C����2�BeN{\r`*�\$�oH(T��C��ZT�j�P���8��\0���H\n�L����Ć��Q��<�I�HU�!�9�bXFB��!G@�V��`W��l'��@V�՚��^N��3Ɂa2�XCPe2R����-D�j�kkh�;1����&!�f�M�)�0nwċx���6�/d(�X�j�K���\ni�?�С���&a��z�>�?'�BJ�#N��ZUN�p��8֡��Ko�0�I�g=����YH��ׇbpr�Qo{2e�,��f��&&�?->�K��xӘ*�_p�D9=�32���YƤˏ�U�F�)���ʜ2�}��T�gZ6䱥{M��a[^��ˁi���u�WJ�d�e�6t�A���#�;�a��s��<��;�~E4����=����l�5���Czo�鷼��Q�/g��=<�z�9�Z\r5�|��{}��o|~;h�Y�����/�o%�����%�������RgT���ۂPysl��R�80��j�i�[{ef_�,�\n`�=�G������:��'{�%��)b������6�<�Bt�l�h�jxm'��Y.8�ϔ\r.�ɧ��:���U���N��n��i\"��N��>�&�O6����yf�\\eI��Ԥ1�R?�'`_�P�c�1���J��\$|�jv���BD|!���)Ao���0c��.�b2�I6W#�L\r�l_pH\"�u�,�p�Ym�\nMV�\\�J�+�b���AD��b2�@�ϐ�~\n'I+��\"0�\0��M\0��v\$v�1\$\\���υ�X��4����2ѭpG\rQX���/�3\r�t��@��N��4@T��5�d��p���-���Lu<ƥ�\ri��*(��	i<3�J ƌ�J\$B=��	d(���Nn�i*�-��D\r�VcO�8�v6-��ҎN�CniD�|��8�@�\n���p#-��0.����r��l�JَSq�����>�(��EM�nN��E�猂W�NK�Ebp�Q�|��\$I�T���M�g�ãN���&X���v���.��Nw���9r�*p�\r��6�N�h8,�9*D`Gn:%ةj�|c�T�jhk¸�@�e6%r��F/\0@�����,H�� �,R�,�-�>)r'B�+�!�12�&�\nRҠ<봿����[c��#gx@����0R�Ң�|��J#�j�T���";break;case"nl":$e="%���(�n6���Sa��k��3���d����o0���p(�a<M�Sld�e��1�tF'����#y��Nb)̅%!M�уq��tB�����K%FC1��l7ADs)��u4�̧)іDf4�Xj�\\�2y8D�Es->8 4_�F[Q�~\n\$��g#)����)UY�v?!��hv��,�c4mF�\$��r4��7�e��5�č�ʰ*�w���EI}Na#�fu��Vln�Soгi�@t��\r��2a�1h�l��� ���-���湓���6��F�G��5��!uYq�|���P+-c��1������\"̴7H:\$���0�:��(���r&<����\nk[�95�zҵ�cs���p�:���2P�cB\n,6���!!�R�9�z2\r�X�*�DB:�\n�^���Η*\$81C��(��eF� X�\0���E�� t���\$2hܵ��8��a{�9�q�n�J(} =�,99����|����F����ƍA-�z���=F:��D�T;��û��VN\0����ҍF���W��\0��6B#e�BXޏ�b��2�B� 9{\r�\0ܖH\r@�3ĉļ0��5ؑBZ���ҥGy�ȫh�I{�\r�8�֊m=H�#2>����ʼ�M���΍G���z�n��� ��)�\"d�8��πa�\0��>�̿V�ur�\nm\0��0�=5.r��m�C�~C� P�2��\0��kuZ�D����(���(�6�2�����9`�S��f�3�\"@P�|(�W�M�s:U���veC�1M\$���[Cr�3��8|�(k�(��mἳ���ϴ-@2�	��y���4⟁�y�\0��.��\n���	�tC!�#��J��܂�����[`�8Ȕ�7��SV��2H�w/n�C\ns۸�;+Us�sX��\"O~^�W���?��t�VC O��,��S���c>�@��{�I�^+`�����.(`څ0���s?'h3\$�HIB��\r����28��8 )1'&��U:M��4'��xOA�>B���rPj2BfJ�����(UV�=D�4;*�����F�`e~'��mI:!I��SNF\"�/�i�7�T���9O)�>���r�t�~\$H��gd���\$�yH�4��t�~b5��܏%��M��h��q�{��4\0�x� e:Ҧ+�G�R%�E\$%�\r�s��v\r��Md���D03b��)mx����H�q�&��24@@Ptr\r㺂\$�J`]��PRIP r��^�T]:�[�\r!�4�CT_Q��FҜ�.\"�'�y�\"�X��y�7�ygEe���2J\\�*	\\9��,PI��*���n��1PI� ��k%�<�4Fj���HjA\0C\naH#����\"��'�'�i�\\^�a��C�LJ	Q,f��,LrlcJ�%�J��.�Ca��R͍Pb�?t+OǼ��\"�B�XnMd����w��M�x���g\r�P	�L*ԼEb�@'d��O�m�v��|��F,:nz�T9UԷ\$J/qƥV�p�A\0k;�^œ�@�`-�먎�A/�@ �Rq��J�[m�����L�\0PF4�ԓRԈ\0T/6q�`��P�*[� E	���t�#\n�0�2�z]�#b�`'�,AB��?e�:�pxm��6�e�<Y���n�� ��z����넑�JT-A��&�F��>�L7�ԊE츆ɡW\0A[�'t�:��8-���nQ��i�aƥ��p��R����3���>c\$�/�Ɇ�\0G!�s�-�n��:�r\ng�b���쓪K�CLpV��2�|�l�@n0&�:BRx��I\"@�C2�O�D��B^kQ�6�'��U��s�(yn8�l��	Ɍ&dm�i�vj9-����6��N{W��e\0��Z��#d6������(ҧ4���SD�.�aP*�Z-n�^���T!l#IP�ܔ.T��A�,�^7)8k7u�Z��t�K��Q7hjf���?��Z�ќ�E�5�㇡��C3i����f\\�@�&�gn-50���|z�}ٴw�  ��-=��ѼQ���-�MH�_�\$��je�)U��cÒ'&�ԋ��im�g�80�徟�\n�����jU2y�����\"�P��A���`�,��&#�\$��I(IX\r������U{p¹w���پ�y�7�l@�����F�t���Fu>�q۞�a�[lW�/2}l�����j��b��!U�wT]��`�_��G�Ic�LY��^�R����!04���{_�f��7�۷~SJ����A�{���y{[5F��8�����%fTh��\$j����~;I�� ��l���It�����L���ۯ���N������1\0�|���рZtj^��ּ��[�J)��;�D0`ؙ��C�`#i\\�GFI�/Į.-�j&�^�D�c��)ɭ�L���c����c���\0K�G-6\"ȦT*�JZE�?��8\0�V�X�0�ί��\r�\n�l�o��p��C�k�g��\$۰�J��#\n�0֤p��Т��*\rQ�����]&^1�1�L���Zk�\r��XϺį�#o�\r\"J�Q#\n��������^�?%g��x�D8�1QBG�9�9��i�\0C`Z�:j 4.�J�Re��۠���r1�l��l�C��&�1�4ۮ�T�@ԣt\n�P��&��X�#�\r�3\r[��A1��P�AQ�I�B_�%E�-�B�n�A͑-�1�����5�t0\$p\nM��x0�F~��y�_圁�J�(e��\"o�r,鏪2��j���*@K��8��b[2#X(r\\\$��y��k-&Ntc2�).:�����7#j	���T/��\n���q^A�����3�d?bJ���\r��a^l���ҽ'�����,���Jj!*�!�<n��:�&ni�v�R�b1�'B�ׂ ��\r`NJ�:�|#b�gƁ+�L�|M��&Nr�)2*,jJ�x/v;l�c����B&��4�!���\$J�h&�ɳ[\r��-b=42�2���kpbe���6��\r�6�38#T`b�-�HgC�5s}�΢\n_�&�\"�n�\\�i*�F0ЯV���<��8f���ReXDk�:�!�\$gy妠zE��D�(͵	 �7�'7�ϩ~�b�l�&����3r&b\nZ@�)�J`\$X4�l� �P�����#\$D�\$`";break;case"no":$e="%���(�u7��I��6NgHY��p�&�p(�a5��&ө�@t��N�H�n&�\\�FSa�e9�2t�2��Y	�'8�C!�X�0��cA��n8��G#�<�t<�'\0�,��u�Ck��Q\$�c�ġs��n,p���&�=&��%GH��i3��&�m�'0�����t�e2b,e3,�	��hG#	�*\n\"Z\r��Rs3��\r�,�o�&wÜg a�hf�\$�A���29:t�a3��\\��TϾ�ͳ��3}�u8���h韡B��>���\n)�%�˂k�W?Sq��7��p90��<��k������p�;\r�P��6���#�\"�:o#��¢�pաCs�6<�� ��j����x�޿�8�:��3�0�cn�/�\0��ð�l�H�4\r�~����`@H�Bf3�Е��t���4��9�0��!|�9�r�4�I�|6��Z�30�ZP7���^0��謚:�\$�\n�2�+������#�2L�O!c��2��P�<��(+�#�ܽ��(J2�5�s]��0�9 P�����6�0���Cz�5�vdT��0�:��8�Ŏ�M6�P��\$#U͌6Z�m����&��;0͸�٦���,sr�Y\"���!�2�����L	\r`�6V�5�62��cX�\n\"`Z5�Dj8#�\".�F�t�AS��5�C+V	�ag��fi���b�S2�J�)JQ���\rphܛS��(��#h�S�����:���P!R�0��	\"'-�3�aM�Md��#�F#���!���M���|�7�����3\"�q�~��k�z��(��j�M9��rKf=�\$:7�κQ\r�m҂�:j6��/fL��+pΰ�V���ak�	���2�����6(�9�A)X�YU�Dw �c5�E�N�-7Uc�0��u}7JC(P9�(�:|�3��Cʖa0ha�B���0M��-7FM19K��/-�âeL�5�֤�C�sN��	(pܟ�\$C���ц&Y�JA���AV\r�QZ������\r��\0��jwC*��8ja�K�]2&dК��lF0}9'HN�\"Q	�?5��Ζi8qG\r����i�Õ��\0�p�L1�<�9\"����tc�И�N�vwG���Ȯ��#�P٤`�	�a)W\$.>@U��fkd:������q�5'�y*�i.af���\r	�JJ��U�[�F��HAq!��P	@˸ M	�\n\0�����NLvvd���rd��\$�����K�G�5�~`��~��<��2�Q#�V�O%����8�Y#�4�����PA�8%\$����L\r�1���l�9��@��@r�����ӈ!�0��0o``�Èz�95ft�M�\0G�\"\$���!�FI��Be7���#e�7�%75���i/8̫�rA\n\n�I�X�����2\$�)�P���bK�f�*��|oș�fRŇ(*bЌ�.\\�������g9	h���Kɉ3��\nH�\"v��)�Ss�љ�gg5y5�5���K�l�\$�)�ZF�¦H�*ZC�`f&>��TR)�FZ�|7/S\\��[b�<'\0� A\n��\\P�B`E�k	b\"������khI\n7f�±�\\�2t:�6Q\\��\"9b/z�i\nCa��*AH��IT}�4Sk��b�x9�\")\0�I!�3�����&�Y4l\n�����߻;�y������+j�ʙ�:^��v�C9}<�}bl^���\"������å�'T*�f�HzY.�?)x��D@��&�Jf�l�����fPC�P_�~����2�m��\"��@�t�F4���h�Ǽ%��N	��&�cOy�:��ԇ���l�!A���*t��0�Ĥ]�`I>�v��8Ep���'5�5�e���C��B�;��΢�?E��@��@ ��\r���;�\"=+��s[�N�7�\$ě�|�r�4��^���X�Y�c	H3G���ZZ�F� �UL�y�;���M�7F���\\���߼H��1�e�\$ma��&4}_Y��7)1��,m�A÷�������L]�2<�;�R~���b���*���(bk��|_ǣ�-1��Р�p�q�R��n�F؃�0ʝcL��C��h��%���'8�ٮ�r&��Ȝ@��z��d&I�f�z䆀��u���;�n1�\$���5��B�\0��c���\"��̖<�E�alB�|�O�Ϯǔċ���2Q�ڿ�R��Ƶ��4I�9�&À�r�X�z��i=8L�Z�,��\$']y�fa3D�e\n�8����Z�	��FN	��J��\\��}��}������1�@�\"����U�@����\"3�ߠ:~���hi�j�h�0��p)n�).���|�o�тh�oTT�\0����\0P�N�����ʇ�n-��))8H ��Y�����KH�Ä�Gv&�/lꭃ�f6d�B\rĵ����;��)f7�Ab�(���� �pѢ<�&�TM���ctQVR�P����T��ζ�P������C�:н��U �kP+\0�5h����#��EP*�P�e���l�3�Y�V%�>\$�_������.�+Ic\$[���/�\0��2ƣ��@���\rp\rB|C��TJ�*3�Y,g�2]]� >��&��`2�4+��m \rl\0��1�.���1C1f��)61�fC��%�)-.� ��Kp�>�C<+q�Ӄv�1-�1�,qt3�u��\n� b\n���A� �� ���;!sk4b,�3��%������i����0,a%}�,�L��\$1bO�BrK �X�ޫI��^�Z_������N\0���ݯ܀\0�7@�`�b�ݨ�񍠎c��	��\"�(P�\n����px��?��V�;'.�\$f�km�'0�'2�l0oB��e�9F4(1U\"h���C���+��;�p��QV7�T��9*�CEN��ngP�C؈�����\0r��[�<���I���3�&d���2�4,4c�zH���@!sD�c�d��T���R�2�st�y5��%Vb����3?.rp� �����!�2��<����a����Y+tT�\$��6��l�f�8��k�g\"E�\$���2�Y���L_�������X������>`�\"�pJ�'\0�0���Ƞ�\"D��1)��7�T";break;case"pl":$e="%���(�g9MƓ(��l4�΢劂7�!fSi����̢�Q4�k9�M�a�� ;�\r���m���D\"B�dJs!I\n��0@i9�#f�(@\nFC1��l7AEC��� :��'I��k0��g���e����ň����\$�y;¨���\rfwS)3�	��1����i��z=M0�Q\nk�r��!�c:��DCy�ê�I�#,��d����	�C�A�2eӘ�F��աњd��	��B�7N�^� ����q�R �yW~�X�z�q���u&�p7v����\n���BBR�\rh0�1!���	�`��?(�.ǌ� �֏Mz(0��P�2�I�\0,K ���\"@�9��{;%�s�1���7��8�4�����.�<9�����##4�J*7�Q�:Ǫc�7����������Q�(˄D^��!`@%�Ch3��:����x�?���4�p�9�zEE��C��xD��˱21��8�3P�[�4��x�(CzЏ̌�D:C��:�K�2��42�u�� ����u�^�=�tV�����	�L\\�*���\$>5�M�k�vլ15�r	cx�:��r��܃x�iH�p ���V�0�~�R��#�zb��Ì6���Z��0�B0�7\rnS� �(�0���W5�P�2Hbh6&-�EC�\rRV69�x�	�K5�������jQX-K`�7g�<��kz.����\$�#i�CKڱ��\0�(��P�:�5���x�^��\0���/Ts��N �#�\r�*������)į��t\0ߌ.\r�ͬJʳ�b��L����؞*�DM�D7c����Y��\$��nԁB*Q�e��Y����!�V�3�d+�'�\0�:��x�JC���rq#H�ƻ�5I8R���3\r1�Z5z���9����K׃s�I�.�/W���ă���c�ھBDHz����ؗ��\"��ٵ)(���R�v��`*�#�U�Ua�A�V T����3b e:;K\$ 	��qK�#\$l:�T�o�3�H��&�	�b<�5��@�K��I�Ի��u!��d2�P�C�\\A����ܰb�B9��h���Y��A��\$�Q�W��Cŧ�C2q��Fx���)��M�.�KHa\$����\"��\"9/�'�#\$Ԟ�!��0*�8&��S�yOi�?��%Ò�Q\n(6�K��i2jHd�z���\$��%��X�V��v#�ZC�ۊ/�<^4F�ST������Fe��M���I�ʝ�xOI�?(	N�\$�Q*,2��@���Ra�FlC���5/��\$^�{����h���H�؜%��RXx�)����^b��#J�餤��s@P	@���3�,G_�f_�-2&C`���I�`����%��5�ÚCm͵3Su�n���\r���:X41Eڂ2��T��%\"\n����Bf�Y��l5��CQ�l/M���h�\n	�)JF&���~\nh�/��������`�DŘ��P��R8�%s2��C+�&!��%RCj1�/�����H�Sy�6�r\\��Z�\"f����{b%�uh�@��iё<�<�Y\$l����S- @R�p)Ǔ�/�=S����^�\$�NI��&d�#�\$䥗��I�M�8cH�-//��hs\"4�Q鰋��s�q�+��KN����m\rd,��G��yBY�\n���+Fcl2���n6r�{��K,�����:��fd�4:���c��5^k���'\$D�5�������܄{C�\"&�\r<�`�V�z�C�ݖGbI K\\���)�t�洔���5�0�`pPD��\r�\n�k\"o��ךZBr 0'�s.��3�/�D%w���j��EY������F�xt>���\$��C;b=�U+�n�@uYLK���-��Ecv�5lq�T@�ДB�-Ah6���[��av����́�4X\r�`9�6കPSH����u�k�:|rF��O�ԃ;F\$�����}�l���a��]!�L��0�h���Cy����z[2 %�F�j4A��F�a�|��Ճ�^�����۲\$3d@�5=��07o�^DZw��-2,�2N\0W�k�\r���Ц��(;c\\ś����V��՚�<��zJ����SĿY��oY�c��I,c�ۢ���n��Y���2^	T�a[D�D��t��u��و���\0�V�C�M\"��~��T!P*�%#a�`��SB�Z�!�xӡ��/)�md��lͪ�>Λ}�u|��4��HjfF�B�!q�\$���G\n(Դ���c!��G�Z��-oJi��^�^���L�W�Ŏ/�y�C�=��5��!K5v�w��~�����~\\N��BD|���d�ʇ������?��a����Tf�n�� �̊��\"B� ����(���J��\0U�p]ʩ\0�.n�%D<1\nt�@�'`�B�2r��T�x'��&b3c��	�'��GD�I�h�L5�=��).�O��JV\"����Z�@���d��:U��%��㣴CP�x#\$ցKg\n\$�RV�_p��ЎΤ�0���	�#o�����P�M��l����j�`�M���F\0`Bjy����T@&&1�z'��\n_jC��q&=G8�0��q(j��qj���nk��p��p�M �i����]��]#��&w0��qBU�(A �F�7	%	ҷ�pq����LZ�P����ѡ��Q�Dg����\\\$����]˶rc��Q�V ��4��co��І�Q�r�e0���������M������ Q��o��7,��,��r�p�r\\�0�r5�\"�R>�Q��@��Q�>�|��\nKe0Gl�d���g�mk�\"�z�� ��cB2<d�M�&\"6\r)\"6g��'�-Í&�9�t]\"�P�@xRz�0g*&���D���j)�P222+p�F�'�m����]-6䥆�q���HE������p�\n�����0F��GjW��%���Dt���#,�#�@|�8���#/�\"Mu4�=�A2�)����F����M�1�X!��2#���d<#�fd (��@K�6���F�Y�@����rR��!��\r�S���\$��;)<S�%���f8�2b�p�j��S�C�2n�=�7>�g2��V\$�yF8�r�]��\r��40�n�U��;p�0�A�'A�=1�=s=��Bή.��r[AD�#�0.�3752L|�C-��=S##�8��P�spv�l�N�-h3sl#dP��q�#Ds	@�q��4cE�ԘE�Z>P�;��&�?��!B�\n�%)�5�r7ÁLB�Ү�\"ba7�kJ���j�~����\"����\"�V7�tu��1V��N �9���C���% ���WR� �%05)\0o�-EX\r�V�\"��aPr��*1*=\"z0\"n�#Z�ϝP2�x��'\nq-T!��\n���ZH�ЛI��ʑju-XOEX�Rʴ��Ք�)3�#X�V�Ӓ!��F(X���!&{�=��F;UGYIt�9��\"c�S�MQ�(&�01\r\"��0:��2�\0��5Ue�]JV)Q@�g�&}y&aY�F�?5P��6\$i�6�1Yb\r�!��\0�:��)�=bTH�vM\r�?�4�e�d�vp.�d�2�/��tC�d���,�Ҋ�]r{QV��c�J-��v��P��̪en\rF恮\0\\Z�\n�vc\n�>5K�8(E�%�8u��M�6+Q5����2��Ռ7n�6`��:�X%��l�E˨�l���m�J��Q�o)\0";break;case"pt":$e="%���(��Q��5H��o9��jӱ�� 2��Ɠ	�A\n3Lf�)��o��i��h�Xj���\n2H\$RI4* ��R�4�K'��,��t2�D\0���d3\rF�q��Ti���C,��i�؎hQ��i6OF��Te6�\"��P���D�q��e0�̴���m���,5=.ǋ�������o;]2�y��g4��&�6Z�i��C	�-���M�CNf;�7b��h<&1N�^p��|BRY7�D�V�\n8i�f�)��b:[NL�,�h�l���I��]���b�o7[�������2�X��O��ԸI2>�\$�P��#8\"�#kR߉-�ޖ�B�<�\n�p�7\r�܌I8�܍�\n<���\"�/���Cq��.����	x�FLS.��h(���M���4���#.� ���ܿ,c{�뎈 �823(�1���\"�I�������3RĎc�J2�\0x��8��C@�:�t��\$.���4�8^����?@�xD��ɻ5#3J����x�!�R)��(�4��l�Mҟ(����9-ì�^�m�D��V �cH��x�#�ڟ#��x�\nN�`��B�:���:\n��\0�<�\0M�u]��!��`��+�}��)�L7�2���!\0��Ci��1�\"�0��d����,p�V���#^�	kyK�N=y\n	8�h.Ë�6eS\0�7�mX'���~�`C�����bm;��ZZ��R�<\\��X�؋�N�	���ކ����&M�����dZvZ=\$��doiYY��9����Y���H�.��I{D�]��(����Ey���m�W��Ta�L*9'\"��x��-��<s+��#ӓn�����\r���A5#g�Q:\\�E#�W�)MR^�rC����|�U:��������m*0�Z���ujj���\nR��Cj�^���鴲8��\$�>}k��SW��h��.΃�GLXo�����R�^>�(�� �ɉ. �{�0��s�D2�PpT\nÂa�3�R�J�KŸ2���\nU�2�@9@��}��M�̔�����#ι7��,�{���(3���B�Q�9H) ��R�eM��^NMR����F>�^41����Lh�	���&�т\$[f���D�DU鍤����a�,QQj5G�&�b�+��q����U�sq����x],�����t	%�����,E�����}c9�1��Ŧ�ĝK�6Ҥ9D'ĺCf�����/�:v<�̒BXb�d?\$p6K��B�A38�l�4���C�-I��J��,�\0P	A39��X�AA\$`��#x�yg\"�������g6g`���q�����D���,�0sh���B�Hv&e��@rH��I{�	�S�s�KC�n���?�S�H�a�QK��MVb늉*\r��0��3ɤB���@����\$\n����pu��*�(�����Y2&�ؽ�bd�k��ܴ_M�i��A�GE�gKz�\$�0�tKhL�`7J3�X�;-uᘓ���\$f�/��%sҧ��)\n<)�D�Y[ėH`�3�\nTA��Թ����ɂ/U�'x���>�ӭ\n>c}�j�P82���k-n9��ATA\0F\n�З.�l�UBe��m,\"*�י�\"��_���+i��30��P�*[� E	����ē��_f��\0�GF��(5��`�Ã# �����<vF�&��d]���K�dé�Ob�C�\0[��滑�RmJ�e������g�{�0�O�~����^�h\n}��%��DU�V��̊�&���\n\n��r����A�J4�T�3��\$���SР�Oh�a7�� ��V���l2fU��m��(S.LYzX�ձP�+�����{�]7:��\nΌ<�&�^�V�\\C�#a�;�`���~tf��x�c@P���/&صX�։���c�]\"�5,[�d�o0��w�Ŭ��Q�Q\$a���3T��\rd�_���F@t;wG����J�ABU%��MN�A�L�4�Vg�nIȱ���]��T!\$Zi�\0�pY4Q*4QI\n5V�1\$���yS]������?W&�l[��)��L��m	�./�f~O�\n��kx�.]�q��8�S�Gy�'6��wĮjA���(�tS��<�]7��i+����戱0ήm*�As}��3�,�I8w �b�S2OhD!G	\$B�f��a���p��b������\$�l�\"�/)���r\n�4�374o(��[�1��m}[�u�\$�s��A��j��F����ڛf�Y������χ�2A������j�*c;'������	8p1��0��DK��dFVY_�����e���-v�ɿ�I��y]r��\r��J����^��8 �`�_mh�Llm'\0�v4BR�L�-.V#\${&�8��AF�3��m�@�bX7�h�Β���N>�Zΰ�.f�,�N�nT�}��pX:͜\\���lư�٫�	H���T�p����������^�Id�;�NG�c.����G�bn6o�P)h٣�AI���٤����^���p�?B���Y�p0�\$�p�8�\$�\nِ����X�o�D��N\0�&5��\nž/pR��k�	���B/��\n��Z�BBg*���ǈz�f&/hYom�����mʟ%��q��\"f2i�l^cD�o�G)p!L⸮��&LqJ�)�jHk5cy����zq\n�qg-�-pc��ύ�q������t�c��#�ѹ@����b��F���x�%l\rc�#l�@��\"��!�,R\"v�\$/�4�r0:�4�\$`C�\"���4�m�4��l���#�G�z\n�g�mc�'\\��(��|��.��Q�\n��HB�&�)D�.r�&������қ�z	\r\rE�/\n�ܣ qB�]�\0�f��b��l�<��e�e�,E�D�6�X�~]�/.�h�/P�.��X`�`�.&Z����)^\r��T� �.�0�\0��^(��u�^�hN\n���Z�a�x\\�R�oX�\n�2Pu6GR��k��2�~,).�d���:/o�������@*�*���!ӝ3l@5hh#�R	�\"�O��� .�\"�bf��atbBl�C���\\�<�0��\0PXe�f\"���,S�g�/,XƉv�ø,Q!#c��5�D�J)m�z���Lt\$�N\r��-�,�2PoF�H�1e����;�Ty�^�#U@�����h�DW�n�\$`\"p\0oE<2�g��0�o6b\n�FN�&gD��cLZ��5M�.źģ'�JM#���LpFs�=94��L*���a\0}@,�i	��\0d�";break;case"pt-br":$e="%���(��Q��5H��o9��jӱ�� 2��Ɠ	�A\n��N����\\\n*M�q�ma�O�l(� 9H������m4�\r3x\\4Js!I�3��@n�B��3���'���h5\r��A�s�cI���E�GS��br4��Ecy���U����z0��D�q��e0�̢\n<��m������i��i�Q��b4�(&!�No���d?S4�L�<ي-���L���,ݒ�q`��S �쪧(���o:�\r�>yx���s-��s8kj�F���I�{C�t�6}c�3�ܡ\rê:�8l�ܛ��ɭ�@ҏ;���cp����͸�K�7�`P��8���5��x�7#��9>M�0���6�ī��Ʃlh!2,�Դ���\"���&\r�X���b2�BȠ\r���7������3/�#\n���8@0ȍ��\n\\:̈s\"9�����41�0z\r��8a�^��h\\�K	�\\��z2���9�xD��ɫ-�3`��O�x�!��\rk�����\n&���΃��*�����׃�}6\n}�h��\r�p���P����в\n��\0�<��M�o\\(!��`�Y��}�Jq���1�ܦ�3�@��O�0��#��>�P��U����[:a��4�4���\n(:���M�E-\r��h	��2�-W��¿�8��(8:R����&�<����h؋��1��~h�\nb��4�H�?/��a@r\$�H����X��B!�޽��Ǭj��\"��R�6R0�7��j�/�j8�����#2��\"���#��z���XVO��\r���?T}�9E�d�#�WV����]r�3��/F2�B�ؤ�#r�2|�`�Vc��#ܚ��8��擠��ڟH� �ͣ�a\np�҂���!-��#����X.+�죬��3�T��0zP7��`�]k���c6\nNi�	?p�ߊ,vk�,'@�\n�)~L֭�v�)�5��*�NZ1.��%e\"���x���'㴠T�P�%E���ҹmRJQK�^M՚����D��]z�\"&� �XS���;�C(-j����ZI�i�XI؈���TÂ��'��\"�Q\n)F(�!��<R���5d���<Qx::�b��.[���փ�Pf�L�&`z�a��lX��ʇѧ5&�����hCBN�������[��3F7������.�8�@�{��yfǖ��Gc�6�04��#�� �d4RV�D|8�A.�\0��5��pRF\n��8ȧ�(E�;�K2�H��ۏ�[����R��*�fZc\"���\n-�ؘ��6J%:�=�eL�UH�%�\r��:FD𞓁�r��u��`&J��S\nA���CC�0 Im��b���ދ�L������:�2d��Dؐ�!�.H���� �V��A��BJG��w3(\$�0�vICK��ȳ�Z{*�L�8M&,l����ܦO!'\n<)�D�S�ّ#�3��U��������	q}Z�&���%�0��լz�\0S�H�0�Yg�Bji!2)��'O\0F\n���-�h��\"_�ֶ\"*�9�\"�d����)�g�i��P�*VIu� E	��	�n��F�9x5��GF�yhH�32XK� pb�HᰥfG���d̚��qq�Z�D8-w���&�6���K����%PA����4���{	r\${6����o�-�=����}�+�((H��H�ib|��QGR礪*KD�Y M��Q�MH���估V1�\r&!î�8g�y)7���<���A��� 2ʘ֕q9�I��5���\0�z����:`g�O�Fo[�l2�r�nJ�(4�0�Q�+�\nb5�����Y\"zwO�P�yj�*���5=B�o�+�6ʮ��e��':��\n���z����+��\r�}�q��V[�|}\$��\0-���Dh�;�/O;�<�˰�/�*@��@ c��.���]S��i�gh�;3`)q��Ί�\r�n𓦑�IuX)���T���Z�=5�d6�Ap	�<��,f�o�n��i�ճȚ�[.[�xGy/'#��q®\$�1�k�&�����b�<��s�\"A�B��'����UD8=O��`�Πw �K�3DN��3H=-�Tnl�L'}��v�<�(bv)�J�N�.�����@�͑jW4�xz��R0HFX�/�^fMl��YXu\"�\$4��o:�Ε��8��#Tb%/���ɪlJ�tG� �5�VF\rRX�{���~m��6{i�b�������MO9�Ǭ�ǖ�2�� ��bb�k�x�~��~�/�myT����^�ٰ\\�<�i�HK�B%O6�#�]��k�`��`NMR]�\r��C6���E�Ө���Zk\"��������d��>�CE'��ow�D�������el��IJւT>��n���NR2�V�/@��J�0~6p�ꐆ�P�.�m�g��������Dïw\n�l��r��p�0��mj�P�O��Pc��]+��'�6\"2z#J@l\"�9�y`�&�,��O�ԓÜ�i\r<2d�Tn��%g�@�dRz\"��TP\"|#%b�q���\rMq\r�\$�kE-X���8jjMbab=E�\0���s�qJ|�PB��},����m�`����^��b	�to��\$�����%q\r<@q�gL`#��^i�a@�h\\�J�?�0����FƤ�&�	�A�g	m��r\r�K0��< ��@Xbc(�&��\\�!�#\"lt8i��k\nwd�sC`E-x`֔\"�7���\0�LM�֍{%�-���%rZ;r^��^�'�]�O%�:\n��KQ�OM�����*/y)�\rѾ�Ҏ@r���|�\r<�R���7e�/:�\n�)�k�GM�/2�R�-��2+�N	\"\r\0 �2�2�p2*8���)q/���f��#��d�7� Vnb\\�B��[�.<�66��rK|9�@�Г2fb8�C2�:�\nd\$\r�V�`�c�\r��Tb\$�\0�5`Zd\"OƄr���f\n���Zx?��B�y���~�%\n2�;c5\n��!B!� \"I�\"�\$�Qh�ϙ�`37��R*C�D(O^}M�D��M�kF�X��8N�e�TF��tM-�&\$�F��E�&�\"<�H(��Hբ����7��]f��b�Dm&͔Jr��É��C�-�&\n�&`��`�E�UE�,f48SF�B;�p#��(B�M�l��#~�1~\"F,¢�@�<�ΟD�\\�-%�.Ƌ�f���\n�e23�e��1��\n\"\nǦ4��II��>̌/b8��VcTD��@c�)�P>�80�B0��iQ%��Z\"|��J��<��]b�XO�5����";break;case"ro":$e="%���(�uM����0���r1�DcK!2i2����a�	!;HE��4v?!��\r���a2M'1\0�@%9��d��t�ˤ!�e���ұ`(`1ƃQ��p9\r0ӑ�@d�C�&�I��t7�AE3���ed&��3I��rE�#�Q&(r2�Nrj����E�Dj9��M�� 4ݤ'��Lq��L&�V<� 1m�y1��&�A.����Ś2�ȦCM�e�yS�\"��Dbg3�Bi��M��A�SM7�,�kY��F\\S��>t4N�;�g竔��sg�A��@1��B:���޲����I��йlK����p���9<�P��6 P���\"��5��x�פ#�╊i��6�iBB��kj���Zڷ� P��\r�`�M�4�,���:N@�7��L8\$)��2��@���)\n�7�j�9����#�9���(����(���c9����4410z\r��8a�^���\\�'R��.#8_0�B�C ^(�ڸ��̸�r\n�}8��#����|��bh��#��<�IeY�M�1�=Lcֲ���<C�+��sB3��(�\r���:7u�y/#H!�#x�79�}�����\r#���#�����-����(�0�j@�Վ�,r��O�X��w�� ��D��_h�-4�*�.s~`�\\J5��Ql��K�Λa������O��K�<#\$i\$�����ܹ�\"`@8㣐�g�o�,�Z�M��-���c�N�\0:�@ʙ�{<\0��)�[�5���6�)\nˡ�\\M�2�����&L�?��U�c(Ɣ����a`P��h�\n6�Ð����Z:o/��ߵ� �1y~�(	���#\rZ�!�_�#��<��WEU��܎/�G�'��C�b\"v�1��90|���x*���\r0(敠�k\$�z5H=e��CI\",���D�Ù�Gc���z�+^gXĨ��KK�3bؗ�XI/}�@�xR�y=g(:�\"�ܩ\$3m�(��\n�\nY{�}{)�A@s%\r|�FqPM��������)�\ru�ƧI\n�@����cQ�EI�U.�C��L�yP\"F�xd@����\0D��n��i풲���!�/��4�b4T�-�!r�C�	�nx��S�k�hu%a4�!��B���lcRJQK)�4�*�T1�Q�t�_*�U�%�V��G��e��PCZ�)	p9%庯\$�j�P2�&�I��	#�l������	W��>�Ho�(r�H�w��'�Zs�����|L�l����Ё�\\M9�x�>�nHᝁD\$��ւWPc,ǆ�?	�{��@\0��D6���Nǩm�Ȋ�M�x�&��w���5D�Z�ang�`�*dd��Q�e1�NB�I�j���^|�n!�-S�d�|[2/�D�j�^�aL)i�2My6M��1�L��?3�0�Dzd��3&�-(#�uJ[�'i�uV֨e�{ďo�I��\\CE1�����Y�J��,7.��|�C\\3�m��,g�`@fm=��x��<�(��5�T�)lʓ\$�)��e'E�\"�\"W�_Oa�#\$GK҂XS�H��B�p�`�N2RY�������p��l�\nmd��J0T\n&ΚX��H֔��\"R���v,��1����sA�0�*��FYf@\"e�vX�Jq:��%���N�!PL������\"&i�%/�clC:	��9i��_��	��0Ac�*¬y&��Z\r�Nce�2l��\\9�����0�Bb�(6	;#� �sf\\�,y�r4��{�H�Y�P��\\��\n\n�_0��Pc)�����/�@D�ХB�sƂ1�T�,������@�|p�mc���v?%�Cą�rqJC�I����t��\$�\r&�M.��C�\n�h2��	t��0�ز�H�iNIhIi��Ե�I��[0�)�~�\0o`m�h���1�\0p\$���g�rva����g��y%:2����%*�,Ah{ԏ\$@�\nNN�8��15��b6��Ʌ�d��H�,�з����8Hl�@�BHkmt�T��f�jo�a�	�1鯴Mx/`^9��ˈ̯^�t��AE�¢��xtQۡ��Ƀ�y��'��:���D�����Y���6������\$��������a-໣r��\\���[�%B��p�tJ��l\"�\nk���2���2Ko�,)D��<��۱�3kͳ�:�5�V���n��L�Pz�l[)G5	,!��,^]�oS��T���W\n��i�1��o唊��N/�2b���RH݉)P��8��I���h~&�O�I?WiZ�����;q���0#�H#E�i&�_D�`lؼ�z4+��npb�d�0���<s�V����/�\$�.]G�˨aC�bʰ��d��ԛ��,bb�B�ĦN\r��5�G\r\$G�����h��,@�i*>Ah�I��6���L�2��%l�I��O�x������Ў�o��I���mТ\$��O樤LK��d@�\nb��n�A,�(�\"XN�쮈��x���B�0���P�FP�Y�:N̰&!\r�R����'\n���`	O�	�L�1\"ܤ?��)��� �q8\$1<�C��%m\$ìB������A �\r ���\r!���\0��hl1d�*m(Q��\r��d #�V�͔��8_�4 [�,��&`-��D�p�ƍ��\$_��9\rv[e���?��Q�ױ޷�,8ϟmt�x�ż#QP�Q2������<t��u&�,��Äu	~�1/�t�([�-peĳ#-�Z��#b�o/�\$��e�\"��!����@�3h%�<f\0�l�lr ���l�Xq�\nXR!��%r�r�204җr�SRF�G,/E��q�v��r�(�R�+��`0\\\$��/�(,��M֌��T�K����\rr�'�~�CVa���;���U*��0dN���\rb�,����;�\"��%2��\"�������93�r�Y�2Gm�v��2dv��!�ԗ�\n���.\r2����W6���B�7A3�37�lg����	n��H�	)��?`�K�JcFZ�=�C�:���\"��:��3�9�_c6K�D5ψ5n�,3�LS9�dꎐ\\c���S�1���ð_d�\r�V�e�/�><,�_��K��\$D�:%bZj��xb���\n���Z:\"��CN�]���ޭJ2��Eb'd&��]�!K�!��W�{I.v�4٥�k���i\nG 8�a\0� ���#��i\$�Kk8��b\"�\0`@sp�\"�H%mL]�^\\��It�]d��#TF2&�~\r�[f2-���q����|b���8M\"�>�>���pYP���/P��'��;��5���jF�o�b�[2�B�f0�Lq\0(,e�_��F�uZ@�a\n�s�0.eL_\"-�~g�]��͌�G�	��SE�dG�J��oDL�JsZ\rv2��O��?`�!�r��dm�VU��F�&D\r���c�e\"%�ҭBb�C\$��	\0t	��@�\n`";break;case"ru":$e="%���)��h-D\rAh�X4m�E�Fx�Af�@C#m�E��#���i{��a2��f�A����ZH�^GWq�����h.ah��h�h�)-�I��hyL�%0q �)̅9h(��H�R��D��L��D���)������C��f4����Ըh/����������	4&�����Y9ڡL�Q�c�Q\$�c9L'3-��hK�c�lqu0hʮ����s�i�zx�r#��^3����KB�!��A%X֡P��T�B�/��G����\n�>#=�Ii�\\��\"��\"�\$���=i��9*J�Q�I�`�=I3(�@n:4�<){���)�h���4�@F���:�P��D0����\r\"�,f�ƨ�I�o#4���c������A�%!1�c)��x�%�����\$�*J�)G1ۧF�딿�Ɓ^����\0�0Ŀ�̂8�@+�h��ڢ��-�����!\"�S��9S�\0�\n\0�2�\"8�Bk�@���1>�H,��\".r5%<�;��-��:\\ ���.���@���.4\n	j# �4��(��S���R+!'�(z*��E�4]<V��[�n��1	Դ\0X�@46#0z\r��8a�^���\\0��E�\r����p^86c��xD���U2FPo �%H����d2~\\�x�-\r�;@ƅGF�T�Q>�	𻷔�GaF��؎�o��#��#�*z[�l��a��d%K�L�6��Qil�R-���!(�n�	0�l�L��x�ʾ8�3+!�+J~_���ؠ��h�(1 ��\n�;�Ԗ�<n���/1!�73��.��|����h�2�GNqVY6��^��L�hhpm�<Y��mQ-����8ȑ�Gb��x�XU��f��iNN��9�)|���~���B������{Me.@���{hݚ����S��1i\nb���u��\"��zHW'ɕ*��Z�Z*gG���5��8�h�m�3�vVCj/1@�s<�Lp�0ɞH ���.2�U�#IU����®�^[�>����R���\$�(a\n�T��r�3nO�D<����i��+7n4�Ano\$�\n׾L��!QP�h,{X��~-\"'xUӪUN�o�D�u`ab�,�aϜ2&KM��rr�c�Bka ��\n��6š[?�|#�F.�П�\"����騬ȵ~�R���BOR�)��_i×4�,�T6�\"�,\$~�с�wR��c4I\\I�E�Ȑa&g�jl)�������/�!���;-�A�e%]Y���C�xO�6UJ�\r��&>�%\0Kb�/�eg\0\\F�{IH)Ԇ�R�O�j!n]�.f�V��w\$�\0�����em��I�K�&�0F{*�>������#�\"�AR�W�\$�P�P��{�*�����+E�D�(�����H���\\ni�υ�g�:�\0�?�MAe%jTԛ���i�~\$ӌE��|eJ��CϮ_ӷ����jR�Q)Y�*2��3t˙�����X�\$9.��Eɓ���h��fv[#�1[ev���ת�_+�~�u��l#`�!�@��t\r6��1Y��M9���I���ZK[�[��:L���ddj��r�.�]n�.n�)�>�՜�'����N�2�\$�[�/q�ǟ�UxU���_K�0��+a, <F�[\nba͊�C@DO�p�\nzG���v�����+�\\����5[�QUN�E��饨��	��d��C&MJ.|-��\",E<����r�Y�	� �р�֢ل���&\n��j�LY��KV³P��U���@����<�iH���i��h�\r�J�s#��2�\n�����}]ZU��#�\n()-��g�,���No�'�2�Hxni��H#�I���Ccw���i��'8ˈ���|SUn&;��[ڲ�b�O�cȈh��T�f��\n�xT��D��v�`���Hq� ����I�|v��;�12�\0C\naH#�o�M,���MՖ:M!�&i�H�R6ZJ�Ve��k�\r޹BӦh��-���3�e���\$����Fr�ں�R��	R}'��ڋd\\�*މ6�9Թk���`��9����dG��av\$�+���Pd��Z,��1:Q>eN�*�\0�T\n��ZX��j�PP�sr~R�x�EN��mR�b?4�z5`�1������m��8O���Q�eb��cߙ���ý	\\����/��i2̑�5����W�pJ�Y��#@�q9��p�FF-����\$�))��)�ˇ�?J(��^��3\\M�n��Y�|�v������ZK���:��k��}j�ߒ;h��:3x���=�u1^�/���n�ܢ��xLp�5�H� 	��U�r\\�ǔ�	�\\������r��O����\r�� �hU	²�eZ�Ǟ�æ�N�eh�i��L����GF��)�R�Ί��b��8�Mj&F���nh��n��K�,i�`�)�E�\$(\n\0�\n\r��,���F�bʛ#6\"���\r*@t6�jb�hG�����(ф�i�^��h\n0~P�+G�Rp\n�'t���\0�L�Bj�(D�M�r\"#v\$K`�'22�,v�\"~`�6\0ʾ��\r��'C��� �~So���p��	c7	�&TF�i�\n��-C�\n/�5��Y@���\0�@�\r�t%�'�(�p@�m���(�(ªF'�θM�醎M��\$��\n���\0�N�Iʺ��.3H�W\$|��<'�1��-�h�'�jW�c v��\$Z\r�&Q*��@�w\r�n\0�8����p��C�����bc�����x���\0�mFy�����\n�� �	��g�n\$�E�&��ji0\$�.�(��,P�\$(r �F��mh�� m�T�)%RN�\"\r�2/\"��B*B���OQdL���j���e!�hj��'�'kD,R��('t�#��&���t��z��*2��'��\r��e���Tf�i�ls(�#�֯���vj�҅�+.�#������.�y/\$=/r�/��	'{0�0B�0���B(��p�z�(@�V��b7Fj7�0-�%��9��nF�|R*���8o�����5\"���).-&�Ď�dV�mlo��r6n�����8��A1I���l�ovy��ID��Ą3>Aj�#*�DBP�(����i	��=SƓ���l����-�\$X�s�P<�O?P��FD���.R��	�@��*�	1�@o�Oe���\n1Pn� [T7@�:r�8r���bD�\"�t&zz���,�X��4g5\ntm�O�C*���SMT\"/�f��R�<3�@���.�1�\"���4@���\r��`�*\r8`�\r��\r�� ����G�0���L@��*�%Nf1��T8a���N�O\$�j�iO(��=��u���@)�N�)PS�Ad�O�UG�\0T�>��M�NHbdD�g ]r�j���U/��@j,5?-uE6�TƖ�US+�j�K�UÉS�/UgV��S�G5tkuxU15XA��O)�'�/O@Ǥ��Jt��S���e1s[�x��\\5�*�s\\�CB���]�\"U��S%x���5M?I\$|���>�M֞���8�\$C.��x��\"��<u�\r2��\$�{�>\"˓Q��b�M_�*@M\"��@(�r+zԄ�^�˕���A\ne�a�I���Bh:��@�D�&[m��HVjsT��iυQ�'Z��2|�qi�s5�@4�[F]j�[i�]j#�\\���D�V���ц�6�ku�lV�n3��c�kua[3����Pk�l�+o�jsG	7M��-l��BtYj&���f�2\"&������2�r��+~Jhhy�r\"��UV��=֭1w[Y���u�S4-k�h�wml�Q�\$-ch,��u�Ghh+m�{T�ԃPryuht\$��zv�K5sw�BV�/��g��)	�n�~�c�#�c'�g}�i#��wC�~#�}��pׇy�\r{�s(4Q��j�w�����A5�(�-8\n-����<��qQ�����ѵq��<�=��N@>��rf�*�Ƨ d��c\\������1^�4%�8l��_e�e��*�V�J����;����u��J�oC���.#����V��)n*2�W՘Á���Xx��)�+�{�6�Տ��t���lo��sT�jB�oV�g��S��4lS�&̴e��AW�X��Uu�\"�-Qw92y �2�����\$�sy2wR�t�������a�1^q00���*F0�\$m�L>b4�֤�7�#�63J�D�+�1U����'!@�\n��6�Q�b\rITrL�����=�\r�y��fa�y�d���8�lr^Q���6�@1�S(��f�d2��BE�;x��M������\0kNI�x�8��v�3N���+��\n��DŖb�₾��@����Cq4x\$qHL\"�a6C���4EM/���u�&�\"��L9<�n��=�MA��hO�O(|��bcU@��ڏPz�P�>�p�ډh#HҊ���Jt�n���)�������V�jt#�����Q���Q�?z����m�qȮ�)��V�&�4�D@Ђ�n��v@�a<tΦ*�&*�;l�dJK�C��Ͻ�e%�`>��s��2I����؟&�0pSN}:ݙ�:(�Fqo�P���ZTo�x���W4\"=ghH�[�;x��k�P�oOW�9���,'u�Tj��cMƠ;�+��q�(cQo��\0����\$'�����\\";break;case"sk":$e="%���(��]��(!�@n2�\r�C	��l7��&�����������P�\r����l2������5��q�\$\"r:�\rFQ\0��B���0�y��%9��9�0��cA��n8���U�\rZv0�&�㙭��'�(�a7��&��(�n1�����!��%iA��D9ϡf�?B�Ke�|�i3�fR�Szi0�\"	��75�d%S�t�i���ы&�K���uqmN�e��mB~��Q%b	��a6OR���j5#'Mn�q��o��I�{<��q�\"7)R����P�cC���(p��7��G�)B�,CX�Ԧc��Ch½7\"T6<�m��1#�Ȝ2�M4@1¤�*��6��(䚎@P�7��\"̴-I�\$��0�KZ�/Q�,4\r�@���P��x@ڍ2�����P�2\r�l��C\"\$�(r��*#��\ra(�Rb�1E\0�\"�a`@!���3��:����x�I��LʈAr3��h^80���xD�͸�1�\r̅�.���|��˜��N�C�\r�\$7�â��!\n06�	���\rc��f�(�Л�+�(��OP��ȫ��B�(�K����E�<ݗ=��0�bC��1�˘%��`�:%Ȃ:�A���	��'�\"\\�4s�&7\r��r8WmȌ:��[�2�C;3��DEk��K�\rc ʜ��B-c(�\"�IҤ8\"\0P��\r8x�|,��⧼C�őeB�\0�q`ʘ\rMc\\�.���&M�5�=X�\\�7#���6��%O%�iGc�*@1��\\��>�4oH6�T�s���.�{�P�Ά�9\r�#d빚j�eB�(�*#J�6�xx�<t1����+ai�;WEl,ܨ�\0�:�4�T0J��7:�*��5�l�Е@�7��3@:�^0|��uo����P�9}��2�R�(7��_ =A�3����a��!8BrE|:���F��P�H�rK��]B�`�#�VV3*\r�{@��1�\"�'�D�(R��jE���hZϊ�fP�&五a`�����QJ)>���UΥ�@u�&��J(a:�|P����)ц��]Cr�Fa�h��9v���,G�,�%�����2�p��Ö1��]�B�:A�=\"��6��2�2eSY��E\\ ��C��G����ؘԺ6�	B'��R�Q�AI)E,��ʛS���G�ZCK�T��.5�xC+�#D�a��\\�JEJo0�0䃎�aPQ><�,���f1e��&�;?)@�!R�H�_L&�ԖP�%E���T�wR��P)��s�0�˰ܩ�@It��)a,�a���(#ZR��\"�d��b�F%�s�ɹ������]��(gZy�!���*D��U_H9�?���ɐ� ���8״/S,hBp��9�iI\rI���ip�����0��.�e2qh	hޜ\"��j2e���M*J�fr���\nIR\r\$H����\r�0�Z��x���M�x�\n��&}����:�^�*��dR���\0Qj���L\$�x�|H\$�в'�8�Sx�>��W�-b�k& �!�0��=ĥ��E^��0!�0��\"_E���p^�bHI�A*�H�����%_[��p�/V)�rDV�����Y�&�6��{�[.%�Ԉ2t���<BPYb��Ôm!\$�ٖC�t\$7 ���xS\n���\"�@�Cr��0�>��.��bƩ��L�;�75��@�`�Ag�`+��qZ�`�aԟ�a�g�:Dm�V�Q� �P(?�L�O�<����ToeIC�Lˆ�nB.h\nMfP����Z����`�2�=����@'\$�XTW�ph_6�(X��#�!�����b.����*Sl��d���Eт�J`ش���D���fޞd�I:��.�_P��1��\\\"<{��;st��J�������A�S� ����?���4Q���rU`(����UL0��F���C~�(�('�(�{�5˲90�GTy�!�'F�)��Y�!�	���;��	�7!07��#BC��\r�9���6y#-G�ڹj����7�ų���~/��?�`��C3�F�����I�伜29\0���H\\�m\n~CS;t�+Qt}W��Xļ���h~i7C��X�p΍�6W�G�#n7�m���h�\r	�j�c7R֩\0���������c���L(Ie�0�C	X�����}~���Ly,�\n]���.Ӊ)�Z���/NZtEltQ>M��~�	Œz�Ϸbcq!�<I��6�C��ߝ��\0��/IP=1�KK���/Xl�'�C~r>�7���	\n#�]��}�2�����ػ�Ӥ�����f�>�]�\r�;}|~�+�����������R֯����&e�\r2�O&8��1\":}�\")e�gF�pZB�Ƨj:`���\\,�G<ݧp��2DrC<�n��,E�O��XM�'��]0HDb�	\0�FAf8�D�g:7#h6���rⅼ!ϔ��4/CF�\"^��l�:���C	�v��@��3��J��	b^!�RX�W��0�Bo������P������*Y�BA�\r� bC6��Â�ۀ��\r�?\"�/��/&d��|��LLj��ή9��.�	.c\"�䂞������B\n��z�JZ@�\r�����F������`�(�Q(^��\r�MG�I\rlnP�о���\rH����f\nB�mci�\r���P��1��&e�r'&B:�?к4e�P��K���V��]\0�Y1��q�oj��ހ��rQ�Q�/Q�%�	1����\\�����K&,����~�I�����R\$笱\"��J�#r\"�(>�.v���BNZ\r��[b�!�F&�d���rJ0JbCBx�Jn�嶸��%e�&�}(jr���jrv븣m*P�H���B�N�Cj�.���v�B�&�?H�r&�΀�\"�[h��4�~Ѕy)��_�v����E%�)/�/��0��RR2V�Z��&Z�g4;\r�?0�0���&s�y	��372��\r��3C�#��s(�N�mRss*{+F-#��#�_��#�}j�@DvN��y��\"B�j��I���G3u1� �3�F%\n�ב�p�1SKR�\0�d���S�F�;N�;��;��1��:�.�#����6Ε���\0`g!ӥ\r�?��U;t;��?�J�@��S�\$n�A��\$3�1�*�\"��%��?.�BZA4F����C5�v��H!}0q3B�ZG�^-r//�5F�HuT��)��#�C+�>�Gf&=�<-ф7/�\0�Ӈ��6��o����K��t�:����J�Lo����K�z�\0�#�e��r@�'\$h\n��=��	#zfoH##(�\0�q���%�\r�VB@�\rc9c��kD�rPC�!d�@'\$9or7�w\"M'�*-�f���@�\n���p��>����O}\0��V��+��W1�WrW��!T�!����ubM&\"���8vF_.�t}J#b�NPt檮`C����f0.Gđl'+��[��T��m���?-�B�,%~.�����d.\nthd\$.\"D�b*�����`�!p�4g`q`����q�m\r�@u�<��5�����V?��Fq�b���9�4CP��\"�pd5b�S 5.�2�W��@ƴZ�@QL\r�'E_V�')��om\0���H���)g�mM�b̎e�Ap�-r\"V\r��l6�[�~ck3b�\n�M&Ң2L�K/�n�d҃~`F�#~�u�vr�\0�7Kȑ�(����";break;case"sl":$e="%���(�eM�#)�@n0�\r�U��i'Cy��k2���Q���F��\"	1��k7�Α��v?5B�2��5��f�A��2�dB\0P�b2��a��r\n*�!f���P��s��S��Y�Pa��D�q�a9Ύr\"tD�g�Nf���o�B���A��o�B�&sL@�����Vd��k1:0v9L�&9d�u2hy��r4�\r�S9�� ��դ�h4�Ε�܈�h9\\,���xA���cF�Q�� =p���t��g�t���f���Y�yS=��b�X,ģ)�^�+N�ĳ\n�p�ǎ�`�9H[���:І\"���\0+j꾰��H�2�B;|�B�= P��%M\"	\n��Ԉ6�L[�# ���h���6�����)��1�m���	[��/@�\r\r|��/��`@%n��3��:����x�7���v�Ar3��p^8K2��2�v!Hc�3!cj64��^0�����h´�Pޡ\"���3�# �7��Z�3#7R\nu4>�+C;v2 �x�ی���:B��9+�p΂�� @1\$��b��H�e��@ԋ�m�[,���8�*\r#�%��{�9>HP�2�m���1����:����6�'U7\0P�:��mC �:Ө�칬	B�5�#�`�,�Z/S�451�l@&CȴR\"\r�e;(*@z���|\r���c�(O�(��P�2-�P����*W�@�VU�B4!� ��^MƧ��jW�D[�\$�I��8,K:>�.���Ō�H�;\r����nڸ��5^^!CM)��hH�K�\r����&����<�ԂE0Pb32|p�ʇ�\n�'i��M�1�G��(��\r�[��F)A�\r#fN���s����6�)���w����\\���i7���6��3�r��	�\$Z�/6���QCp��v���%��@�2���:_Z�0�%��é��\$V���+p�p�D\r�7� 9\$���9Nh-/7nW\\LĆ&D̚RlM��9#����n��(��A�4\"]a:��	Y�+E��@�Ɋ\r�x���c�z5|dx���G��|ii.:��YL�F�i�&tҚ�jo��B����zvN��C%��I\r��ݮg@�HJ�J�ਢ@uɚ�O��H��	+E��(V�E���m������C1\0�)��j��R��}c�#�I1m��W�v��8|9�>���������C4S`'cj�ȋ��}2ռ�\n (\0PN�I#Ѻ� ��n(��&�n�⹚SPߜ���e0o��RZ�J!�ld�o�E\r=�XsO�\$�˲eC�Km?,`�r�Ga�3�rq�C:p����g3�� =�)� ���'p��V�� , ���#�%\$䤄xD���'%zk��NB�/\$P���RY\n��ڡ�����y5�fOr��!�7�08���i�P����D�̻�����bKIz�hdl�M�VxS\n�->��X*�:5�M���oZd ��x	[�Ob`�y�I�ɂ~�7d�b\0�&�pX�y\"ȋc\\Qxr\$��o��0��ub@(����̋l�v1�X�Z?��݈���pgE�_�â���'e,���&/�y�e����fyM�)Emy���/܆:��˾duM,�3t��r��Q;86Ƞ�UG�}�MD�Y�+�4��PzyF�7�5��ZY����L�����?ĩԃ�\0���װ�J���R��a��簘LlN�POd�N�����z03F쳸\$@��D ��s���rn�2Ԯ���� ���G@TV����i(wSJ�\\�`���It��Z	��Z��j'Xʮ���T�t]\rކ����K(c�\0��wID�j��G΂�Zh��B�����,Y��\$ ��V��-��Z!� ����B�NA�	��Y�`J\$���0�0�Z8he#��C	\0���T���2DcS�K&��\$ʈ�,�^Sa�5L�j�\".������|�55���Ȋ&\n�X�C��w�z�b�����F��\0�c�	�Z\n��8^���-��D����\r ���1����\\�p�xf��xK���&�yg�<�b�x9��a����1�&�������U���[�Q��g�y�&����zV�j�v(����)Y����>Y���m6���\"N�?.������6���u�%��3\$Gd��w���&���G����/w���:ى��SІ�a��a��`���8r�qF;;��S8EH\$�6�Cs0�୞j�bW�\"��)����]�z���WP�]8iuj_�WA� �h��S\n,-�������*���?_2oj���)~9o�Db1�7,�-#PW+�\"�^A��u\"�#�E�%ɤSP'�,\$�Ȫ\"��7d@\n�?b�.�Ib�ntU\\#�\$p\r��J��sE�\r�jL��F?��/�Q���	&0O���DDЎ�\"Cʪ^HȂ���������1а��\\�`�%�t=�@fj��8[���.Fu���M�����!̺P�#���������V0�o���l���P��+��R�KM�{\rq��P�/\0����:�����,-�BQPZ��+�o�k/�n�ъ�ң�lŶ[�+c��0B0��,��	�9bT\"�~:0b>��t\r)����(�0��@���Acz����W�=!�t%p\0003dR�\r0����R[���,�X����J¡|1�Vo�p��QjoF��Q��f�#����Q\"`���#	6��\n���G�4+\r���>#��1��\\c[����S*b�T�\\����c27	M\"�l��#�(��\$2��\$��`�lj��%���b`��`�A%*Ҁ��챎\\-D�Cb/\n<��l��DmG#cd7�T��\"�\r�.��.�ڮ�N܀�_\$z�-HiMjBϨhl^q\\Յ:��_)52-i2so�\$r��2\"�(B�kN1c��/\$hr��\$5(\"����;66g\0C7(-)�F�\"`4�T�c*�.e	�,����ye�����A�f哢-�i��:�}C;G��s�ģ}3�:q��LH���\$P�1�\$�����;�&�>�>���;����?����C@��	��W�4���A��V��\0�k�2���~�G<f*�z!\0�!J<���\n���pu��hc�3����'��F�.��\0��t1�y�V����7F� �L!b��~o����@�x\nQ�+��m'<4x+�j<H��	�\$�(!	&��+DC�!�L\$LȵE\n!�T�4�[#���%I�3m�D�ٯ�3nyKdd�/P�g�]&����`��R��琓\"�4���6Ci/#�l�.Gl�.F�QE�SjV%g�\"dI�S<f�� ��p�\"\\&R�[|�'ѣ�K�\nĆ�)�H���H��x,�NpS�����b�#-[F8�\"�>s�QaC>�RCN#&1e��ֿ�aS\r,����p#°X�V�4�G���,e�";break;case"sr":$e="%���)��h.��i��4���	������|Ez�\\4S֊\r��h/�P����H�P��n���v��0��G��� h��\r\n�)�E��Ȅ�:%9����>/����M}�H��`(`1ƃQ��p9�C�\nD�?!�G���:�� ��'�a%e���|���D�q��e0�̢\n�m=c�/\"���mF����:���D\"U�j8����k:]\nHƖ�H�������r9�a �(�h�����_(ә�HY7�D�	�Fn7�#I��l2�̧1��:� �:4c��4����1?\n���+ʆ4��I�(��k���+�<F�\$�70�)�p�E0�k��/쒎�x)��H�3�Σ�˩C�hH�2x�Kʾ�\$1�*��[�;��\0�9Cx�/���\"��������/;hz�'RZ�߻�rxƥI�&�D3��Oȉ\nF�CH1E\"B��\"_Ъ���B�6���@2\r��@��\$\08K��1�o���\0�M�p[Bю�L��ϓB9�����^43�0z\r��8a�^���\\0��̿/�x�7�w^�C ^+��/4q��/\r��7���^0�Κv��餡G�s�2� �\$�79Ų@�\"��\$]a1\"J�ȑ��\n1w��\"�ó�Y�I{W\ra\ng<\n��7U�(J2\".�(��9~b��%�k�_9��	�*:t�QJY���fs4�,��M\"�!�F�\n��(�0��eB;#`�2�7���Ш1I	#�a�6�S��E����NT#���`�:2X����k���O\n ��T�_��-�6;~��BX3��Xd�͒�3�R�?L�s��#��T�!S,h�b��)�\"b#��.r??��}���\\2��b'�I�K~O�����^vh�.�0Rq-RC�'�^cB4�1��x�c��ԫ��ʝv�1A����`a��H�տ\"bb���>'m��@�nȎr�o�\$�/�*BXxDD\0Ae�v\n� \r��9���L�t��W%�T�y^KЗBp�����3°���?��!���Y��@�/%��M!���2���Z�elH�B�b�z�������J��I�0�#��ȡ�\"�I��dM�P�)�#�zUq����3�2i���hP7���� �`:��T�� \r��3����r�!�3����\r����\nJ�%�2�b��dR%�P�%S��Ô�Uh�)iT��\0c�A�2)��z�*�Y+-f���֚�\r�l-����O��n }8Wr?�mŽ�n�S)�RF̲%BQł�jIB\\�������6/ܗ�B�&\n�j�_%��I���`�9��R�Y�Ai-I��Ò�[kj+-��9W(I\r���ն\"K��ݘ�DCZ�T!�-��^V��P�;��ZMg�0�\$@N��DS�\0y*�˰�h�<�^\0�Z�������`R�`C5TRZ�Ɋ�&��YV���rip 3��f����b��IE9��7����VG���S�xE B�\0���7v�\0PVIu�Ƥ\$��a.�!�m�9�1)��>������b�T\n����_\n�0(�jȣ��y i�v�Y-����Ú�U �\" e����WT-p3\0��h�P�d�]�\\4W�\\F�I��aL)g~�h�z/�`���dj]'�B>JV��\"�t^�Ct�1�x%�;�����x�\0�*aƋw�5�����8��͐0� \$�S�P� �MP9�3�ű�P@��m��iM]d��٢��!]���)�M�(�,{�_��,\0�ѳ�u�Լ��Ӂ\nc���3��,��61���1��L��uͼ�4;�Rc\"���X�z�JY@mj*���w��%e^���8��R�Ku@\r��#ܗг|_U�	�,R�G._8�EHO	��*�\0�B�E������k��6I�`\n@V�\"����vإ-�F<,_u��@�'��,��B5A{�E�C�3Q�-ο�6iԆ|�Q�¢���M�)1J՝������p�;�^-�#�c)��&�DQ�^�Rr��'���\$\\_܆�@o�@�N�X�QdŪ�Ok�T�t�����ś�����;�!�5捈F|�u8'������^�9�4���Byy�KR@�%ԳTF�Ӳ�gٵ�;ͤB`1�|AI�}�p��`�	�F����y�f��E��r\na�=6HsJ�cd\na��Z��6�!H�(&��m�`u�\$9׺L1�\n�� 4�	m��e�\"�2h�[9�o�r��@�/�exf�����R����-�!<q\0A�1H>��o���m3}՘�W���U�k �Ϡ3GxC/��O�������C����U��A�E(­-�{H/'�uQ�|��H��̏,�ݎ4DL����`�*\n�� �	\0@Ǆ�\r%<eC�ƅDHD�h�od������a�l�@��`�&v</~gFl�,�sc�����b��h���)haLXjy�x�c�#N6\0\\.'Px2�g��6P�����I�(c �P�+�CП\n)�\nbM\n�k\n�WI�6��~�P��0�	��\"�D�m��ovC��,�n��.c�b,��\rP}\r�c\r�+S0P̉�,�U\n�����&�����0�\$����1	��%�!#�	��SBK�� �T6�^��x��6A�/��j���cz,\"��'�1��1��\r�\0�u�*�Q�t� �.\"��g0؇ێ\np�HM��\$��c,�^N0�2�K���G��\0���HF�F���1�d�О�����A���\r�70��&L`�������b�1N7H;!\r��c��h����#rJ9\$�o	1�#G��c�܎F��+#�n ø�l����X�|FK�&�u�~�d�1@�K���C5\0���G�(�����'��.Pm)�\n�N�cR E�-f�0�~Nv�.^R���V��\r��\r��\r�� �hި�sbF��V�*�\r��l�L��MC�H�M�O���2>Q�60���&�2�\"v:r�1���! �@^�V:�Z�V�\$E(��\$����L�d=pd<p�cЖ�Н\nl�Rm3f7��8Pm�3�ӕ6�&Q�9�80�8n�d�:��62�/��MZ��S �(�2)5S���L��:��	��=d;=��&SR�Nd~�#�@0��(�.+A����%G0�9ʌ���!e�t�CA��x,N�Q�o:�/x�S�1�R^�;�s#�#D��F� H�%B΀�b��t�CQ=3�?��)/�HGAFi��>��2Cp�A�19f>�i?2���\0��K�\$N�K��K��@GY&����\"֊�8�\\�È!���%>�b�����7<�U?t�O�)N��&�T�0lC��O���Q�WQǘ��v̶/4\$EK�0Q�_/�<z����]S�G�0��>3V�:YPePU_V3�t�&n1>�Z{/~1�m�L�T&��93�.�)aѶ��uMM�XdZm�<\$Ns\0.wJM����T�do�v�&hϬ�\0�=cB�ŶSr���D���]#�?��]�¡�\\��]U��گo�@�k�4��N0O�U��Mo�a��KSgK�)v'<�y[t�X\$�V\$Ef�Z4%d1dA�,GX��RuR����#v\$t�f4�faub�l+gcfM�P�{@���MP�UUdCϜ�,J�m�;H1Fl��N�0�_*�96�[���St*6��k��k�_k6����Sf\"e�kBEQ�RsO+�:��F<�+8�Ϊ�!�8�]C�\0�� �n)h.)�q,�w1г�@���D��B�+�K �\n���p�ɔ�\$~%ЏL�E��(0�vkl,P��iw0�G�k7oq����&���2:��w&	��� ���7r�H�ä���x�.w�iPĀR�,b�0���(i�#4���'(s�9�O��[�I\"�6F6�Zb�\0r^��kl-�!mK�#o\0F��CS�:�%�'�w�P�wԇ\$�\$��2v�x9'�=:�;��^��GՂ�7�'t���K��'lr�GG��*�7I�nx\"�\$�('(D�s�Zs�5�L~��U.���C�z�Y���P�h\0�T����z�!8c���L��t���δ|��#�ifr�x;����Bו9�X��qX��n���i)��5J� �������V)\n�d&3T� 7����cՓ;�~";break;case"sv":$e="%���(�e:�5)�@i7�	�� 6EL���p�&�)�\\\n\$0��s��8t��!�CtrZo9I\rb�%9���i�C7��,�X\nFC1��l7ADq�zn��������\na�!�C�zk��D�q��e0��t\n�<p�ō�9�=�N����7'�L�	��n%�#)�Hr����L��×3�|��+f��-��5/2p9N�\\��C*ĝ!7��K\\ 2Q��ч9����g6����f���s��+��Ϧu����CS�7Oe�n���T��ޝ�0֭����	�Z�����<��(���ò�\0+�\"��&�h�	�R�7'�0�k�8�3(�(,�h�7\$�J 6E0���j �&�f�\"�Ƅ���:���b(�+����p܌C�,B�84.�0z\r�9�Ax^;�r+F\rp3��(^�c�����	�\0�ԎQr`7���^0��8����S�4��9�à�:�c�곭+�ͳ��:28��	��0��0�0�PJ��C�\$�aj� �X�#�@P��U��6�5��\$\r�&�����L�������TYIF�B0�7S#M		�#8λ��6)�O�U	5������@�ʣ+\0�C-֯�v��2��e1�A\n:!i�q3�k�6c\\_�\nb���vl\\�\r�A,���㪴��9'Q3D�4�#�:�ө�\r�\rHf3�oEm�v�7�8�\$Gk8�2�p��	����\$�����B(�,O@��%�uB�@T+p(\0�:����=����)����45��S؇B\$C=62���A��X���j(;q�c��<#�8!��B�m��u:5@�c�q46�.ܕ�t�X����`�{��-��7 P�K �k�7��0���dP�I��!��9N�`�1�NJ�]V�7�����K)ټZ/6Ip#zco���7��q�j�����>���b�~ؼb|qmI�BN~��8\$\$�D��I(妾D*�\\\$+����o�!	K@�.T��dLɠ;��H�AroN!��E�4D@�#&���N�eN�&Rd-��������}d�\$P�C\$WC脄ER��QK��t�eL�5�؂��raH�Ĕ����A���8�<��C�\"�\r�\$2p|m,���_�� �H9ē|��*\r��9�JI�b+l�2QҌ�5�*d�����\"�}����h�Zǌ0�b���d�45�����k�9L(��f��t�L9��-�!J\0��蘉��!���Č��Jb�����i��`%��'5E)��'�7�d�\\Z��g���l4m\$A����Ĳ�E#!���Am�[�d���4��a�E�2-GKCI0��)� �8yp\$������wTuamԇ�x�J�bI�2�}*MM��!'2�p�nNMz>�hJ�q|@���b�3���h3-!JaR�uz����|x( �� ���&R�ˁ򔍒dVT�9)��tA��4�1�2B\\�9bڴ�;&K��BT-��&�`H�#�̛��:��l�dp#@��OkYkv|��iȄK5��HRL���,손���a�I��*�\0�T�H\$���vn�B	�H*@��u���`Q�°Bu�Jr�[�B�a��Xc<��>]UV\"\\�^��z+j���º?t��F`^M�@/7%-�R\\d��,J)F(�>A[agx����H�Ϛ�0��������)K�R�@t�`;훳~pಀe����#��F���X����cr���BnĜ�\0Y���2���ĉ�y0�ɔ�˰e\$��4��Q%�2�N�J�N�>al�ў��S]r�t9!�2◤�*�# (&��D�U!��T��\\R{s��nft6z�(C.�3w�Vò�f�<b���.�_F��\$�:,�����D,¨tPL#���~1gh���xopC	\0�]W��N,�*�%,�3;Iw-v�\nȋ��A������&�6�c]��������1z?�����)p��k�t�ᦽ�͋ȑ|M���OM�Hg�<9�+�Q���,�fv�����s,[\"AnW\rƔa�z!�Y�ˎ�2W�;6G/�	2�K���ԉC�2&����h�-2�-vr*��sqAJ�ְ\"�Rvm(#.;�IV6��[�\$�)��|O��Qނ훾�Tk��������{�lC(l6��qӐ��[G%\r��BP�P0��/e��Ɣş�zH��LG�2�O��~�I���X�6s\"BpwԂ��0����pe��(M����g�����K�{��\$&R�/';����(���o����:�Ą���e�6�j��w�γ�,ܰ��`�B�������6���P\"mT{������,T�����F�-V���.��3�6�����4��z�J\r���b��	T6��E�%���!d_��\0�X\r��t�ګ��(l,�\nI�X�J6&�A�F� 2�;�@3�*�\$8��HIp�X-��<���P(���UOl��B8Tƺjϥo�Nj�c����ٰ��/Tc���=&�I����\nd�^�\\@�_��\r#89pZ�Cv�Q!\0��\r�\rQA\0���;�M\r�;�8��\\����Ll-��1`\\����nSf8ͺ�prխ^��3���>4bN��ʃ#h�L�o�	H-�Q�(���1���|���-rMv.��\r/�\r�cQ���l�1�-yq�@�Q�e�'\r�\n��@p�^�۱����ycv!.���K!d!�k!���p�h>�\"'!� ���:��	�6���&\"fU�4�r7lN��1I�&qliH7��'���F�Rv�\\xg��s&L���E#h4��,߅V.E#2FA�*��ե,\r�V\rg�����V�\"&��c㎬\"�3�HJb�+�\n�~\r���H\$�\n\"��\r+�l0>�.�.�0J4�څ��)��[��l��Cq]�:��,���#O\"�A	h��X�ʄ�R���\"�'�祐-e �,V � �N��Z%3	\n(xm^�#(�*Cd\"�5��R�!ǔ���83������\n�S��G���ދ31�:\nf�4��L�e���,|	�,,�T2���|��	����b\nNvv\"z#d	;�^4���b�2�=D>�������\$�Ԣ���Ɣ#9̷:fī�t'�l��g�\\tcVRB.(�`@qi ";break;case"ta":$e="%���)��J��:���:������u�>8�@#\"��\0��p6�&ALQ\\��!����_ FK�h������3Xҽ.�B!P�t9_��`�\$RT��mq?5MN%�urι@W�DS�\n����4���;��(�pP�0��cA��n8�UUɼ��_A���rª�Z�.(��qg��+S��\\�+�5��~\n\$��g#)��e������GKN@�r��|�,��F���,u]�F�d�X�Gi���ST�r�P��+�_�5�ȕʙ���a�^i6OC����q)�J��j�^E.Q�@�+�W@J����,W(I{�����\$�#x�\r��\r�x@8CH�4��(�2�a\0�\$� �:7��4�#��1E��H��%��!�p�����#%9n�җ@P#x�;�j�\"r\\��K<�<�2Jj��2�t��8���1�Pd���2��N�x)�CH�3�(Q*ڒ��Ţ���2�(�7�L(\n�p���p@2�C��9HJ�p���S���c��\r�J���E��L��\rYPDc�eI=LkԪ5L�\$��o_).��1Am\"��Jϔ��Y�Ui��S�둴,G(�,�X+�K&��C(Եv�]u<�#8�Z3�# �AT\0�7�J: �8�Q��1��3����p;�1�=�����;�c X������D4���9�Ax^;�rW��\0]Q��x�7�E�C ^-���Q��Q����7���^0��4���ܨ�2�*лB�zd�)�j�T5���O�V�����D�7�9����m���չ���A�Z8��3��<^{�AZ�(XIu\"��rʺ�Q��9\rؚ\n��r�{��OY�p��=3�)]umo,#v�h�g)l��B��������*{��\"�0�:��@�0���ʤ�I�ʨl�sI�җ�:�C��7 �]����2J ��;h3���~�NC�)\"8���J݉�ZN=�����Rm+HM#!��e 2�Lg�.��z���]�I��!���P�\$rbL�	�}a�6#\$~�H c!�?��K�ˀ>���\$*�I.�)�\n�e���Qy�а�'�)�K�o鐣72�u�1�^�Q�9S���r����A���##��,�,Sy�4S�&b��<��qd8񰚜���[���E�f��8�����z�j��]{ǳ��c�Vv¸�´�ta�m�{��-]�z+��i�	dp��;��!/��	(I\r�,9G��c;�Ab�_�����\\o�<9��y���W0�\"'��	��Lн�.�huU�0��h��b�yӇ �Z�\\)���6��Hg�!�|��r�)Csq9��\r�:���C�8~��@2�丁�\0���~q��Y�00�u���jfL0x₀�J���J2��#�7���<.���Z[f`�,\$2IB8e\r�2Ӏ��rHu�sp�0f\r�\0ݖ��g���q`(*�L�Cpy�9�V�C3��3� ����&��6xQ�(`���JT�\$�n1�����d�mj�6RKF��R�!SHũ�*�Ĕ _��P2p���d`,�2�\\��4f��;����#@hM��7��X>��YA�UJڈ8�+�� A�5�]&�1�KRr~V�� �C��WH��^i�{C��yH	��Zf��%S��8�h/%�eoV۳f�Y�9gw��&���hlJ�4���(�P7JA�u��t��X@pT���K�������T�O��[q6� A;����@\r���y�C@@��*V��:����	����Z�BX�:FY ��\r�e�Xt4��;w�=J~���Q�����)�E!\\؀PP	@�cZf���pB�����MKf|�\$���?�s=o���DP��b.�H��#x��{\r���;椄��Lv���E,%1�\"��Mng�-hUQF,X9�f!B�:j!�8X�C~C���1���@Y�OG9�N��RS\nA�2���v3m-�?]sV��&�ȉB�ȯy���ȥ�=%�\n�b�F�p,���y\"��.�ƫ��8TB�\\4��3dZo2�Wd3��7A�����G���a��	P����8��KŪs�S)c��5p����\r`�y{Ѽ��P�\r.���F����16���N�̨�m�����l�<�I��z��\"�v�d�<���3k0@xS\n��'��C��N=]\n>�d�+�?B�q�v�*R��zR��bKk�u\0�S�%��i��Pe�Q�����:L:S�L�el�@A\0S����C�Vł0TА�ֆ���(C��O�%{cb_8��1(I:�*qZ�*F���p \n�@\"�@W�\"������Lu.U�k��wQN}��E��c�jA��j��4��ҩ�B�0�%f9L���*�����<\"h�w�\0A�\$p��|o��t8�(��k�rp8q��e�\n\\��%�f\\HV�(��M�l��Ō��v��@�#VX�6�Bk��[J�V�r�Mv�����Nt��z0v|5��ÆrDOg��-ց*G!Ę���� ��h?H򩈖/m��M�P>�I'+K���\$��t��H`�D��(��FJĦ�|�Nt����#�JGED����Ќ��g�7#奆���|q(-�2O�B��t\0�z�h`��?�w�Oi�\nԚd�\r �{���k{��Z�l�s\0���F%0��l��`�� �k�ͪ�'ZE`��7�q\0Լ.���6,�E 贎��n���*�� ��\r�d[������c�6�<q��B{B�/��!I�q��r�H���)��-�c@l�`� ��6�n7\r��Q���sJ�\r�t@Rg\0r8x)�.`|CA�@c�F@�\\�g&2�r�q%k.<����\raI�W��	C�\0�j�n���q �QF���l�\r��\r)�@�\n��`��(�\n��Pa\n�	�G\0\$� -}/ʖqk04��O�f��u��<(�e2����)<wR;	�x��`�p�<QP��\r�+��b�d�g2�ʊ���M5��sd�`֨AsMr�ײ���ǳU6	H�Si3��'ώ�I7(�7ӛ7�bXcډSQ,�w9\$�.(���(�qԒS��JX�-���j���1�c\0��<�5<�\\�ӜU.f�6��?�1�f���7���Y;M�=�H���9VKЪ�����:D��ⲣ���4yä%K�Om/(D=0N��b;�����G˪7�/��X�TP'p�0)-�^�\\�\nU<���:��BI�*���r��B%V@���d��\r�X�-n�ӱ\rdp&\0�E�N��N��~�h\0J���*-��c*ԙ7t;��7�w.\r�Q/P���uLԱQ��U�D\rQ�B�#Pi�V\r#?Tl�ET�StNS2馽u\rL�QT��U�|�iE8r�B�wU��Xs:�4QQ���U/@53*�wX�SUn�r�;Gi?t|��\\�mJ�N��?\rS��zǰ��Qd���p8��Dr5�� D+9s~A�މ��)vJ��<0�Qn\rT'K���~��A���:��9��*5�;��,��_V\$�ǉb�0\0�,TV@�\r��Tf:\r\0����{��~���#Iյe��\0�c��	�{2�_VwJ�gY�U@k���AS[6�`�2t�p�V���XU_W�bR�o�L!]YO�Y�Sa�WV��h��8�}jvt�P�kiwm��Q��^l�E&�ML��T�P��c�|Li?j�I:6I�Gn�qHH��^�qBm�86�p0�*�O^-v,�V�!c��<�n���!Fs?bu�c�nէ*�5�X=�;�t�RAgVPSu!uV�S��7�8O�v�*�?գl�xR/x�3-�>�0'S�z��ϲ&��d�H�b)X��d6� ���6�2w���Ay�=����Ǫ�#d�o�.�\$�ry]�^)F�Y=S�A�'�H�+�&ad�yB3�x�l=O�>,�����WvW�}��1p�h�.�p	2�W]�6P ��\$�8^r빂5�x,�Rj���{R6�sF��ucՏW�\r\"Lv��WW�.�'or3=��w(4e�ȱ�)��lص#v�V֟���r�x������位�H�k	�.X�V��o��v�\$K������p��l4�`�\0P�Fí%�i�ݐ�c�k�)k��,ŲM�ِW��?��C�U���+���P��x�[67zإ|�Z��m9G\0���1zXK)Y��I�*���kl���)�EX�\ri]T@QGA��-�K��Qdǆ���#�3ƁH\$���)K�L�_iCZ�I���|7�m�Yp�tC/�w����{�yY��V�|s�y�o��}�?r��F3�nb��h��G���o0k\r�'|�i����zO��בN.��AG�\$�`\rd@6ʹhf^�U0�u%zF,�����\\��O��C�\0˨�%�e��%�bK�w�7�ru��וT-���Bm�z:-�y�ou8��eY�ө���0�u:˗�u���z	W�-����{���:�=���D�\\Ja\"���%����-\"i�(Wqr�H矚\r�H�|cw�������6}����[+n�%&�y�W-�/����w����	l�\0�o�t�pv�)}]�\nw*�׃+\n�)�Z0}3+���p���\$+?;*y�8�yyU��@����I�����2���;�#;К��c19eNf��Y͜���\"�u�ս�Y~�{�W���i�I���4]�Qwr�s��m%��k-�Е��u���\0�n�\r:���,{g�G*�\r����c���B�	�a���\n���p�K_h%)�7��6�\\�F�ɂ���ZT�Z3�V��S�}��46��W�J���d�&��&�\r�P��أ���g\r\n���f�����k�<��\0؟>e�	�h\r<m\\6��|�+�^\r��~ɹ){\"2����S�o4���3�7`ly�t��ծ��7��TR�����)�%i�j.G����P��q��3�\\^����d�����5��F륻I��W7�J�؇S���1c�V����ݗ��X�����R���,(��OEs[��|����tQ�W�|}��@���bD\$G��<\r��}��ܓ�N���x]�z��v����9���4�M(�jG\r�^�T�1V����[���\r��`ɿ{�iHWl��~��w\\_`�c\0@�h{�YbWHP�`��g������}\0P	�D��|�q��\\C�o��a\\md&5}���\\�Xm�⏓��J\n���rZ򻄚݀0���2-�J\nU\\�3�pKOY�~�w���#@\r�z����5x:�9�^x����_M}�F��	\0�@�	�t\n`�";break;case"th":$e="%���OZAS0U�/Z���\$CDAUPȴqp������*�\n������*�\n���W	�lM1���\"��T���!���R4\\K�3u�mp����PU��q\\-c8UR\n��%bh9\\��EY�*uq2[��S�\ny8\\E�1��B�H�#'�\0P�b2��a��s=�G��\n��AS�Z��g\\Zs��f{2��q4\rv����u��Tq,�..+�h(�n1������s��6t9�K'��v�K�֗!�AvyOS��.l�U����شt.}p����Tk�p���+n�C�퓴�>���>�B���i���\"��X� �*~�-h+��#�\0,��@4#��7\r�\0�9\r#��6�8�9��R:� �:8à�4ƃ��1FK��=��\n[;I쎷+c�:l��ִp,,�C�����셗\$45�����0��=�9s?��B.j�Q@o뚑B��`P��#pΞ�ϓ.�(c��Os̅B��5������7�NK��=����j�悶���F'�Ku43N̳J<�*z�B�;�:zH4���.�*MV�-J�ί�<�G�al����U���iWV�'k�O�Pi8 ��mP� ��@�q��R_c�1�c�3�\r��n#�;�1�C��N �;ԃ X�P����D4���9�Ax^;�p�x�u-H3��(���09�xD��mH�Q#5H6�cH�7�x�@0E��\r�Ue��zZ��ju��)�d歊s��?�{#F�O���J�[H���&�=e>Ԛ�[s\\��cl��H,���_�7x+�#�݃�`PJ2 ���m:�����Z-s�J'�j=>\\-��\\Y��o��\$>\n���Cԟ�0�|�B �3#��}���:��~�ϐ)�\$-�[\$�XZ�klP��Z��I���\r�X��{��z�F���p��*��\rq�C����ll��B����m��sw��H��z����('t���,�PlF�\r/��Ct\nv�9���Q	��;����ȸJOTʝ��K�oL�\0�ŐJW�Me/�Xh�\\Bn�ȵ�`�ZA=l�D��h�T0c�� �-��E�G<�*%�ִ���5IB�L �:��yX~��)�P��3Y��AM\\wI0eD�\$T߃�j�iŮ6����q�t��P��+TI������h:W\$�T��A�<�֞O%Ps�\r��`��pe����q��DH��JEH�t̘�>RP��sig ��Đ���'��\$D6���A�Q��3���A������x%f�E��!��M�?(d���!*�XrC腄� @�0f\r��䡆�AK���\n��	�z)i�<�\0�6C�`���\0��:�ll:*JC8a����UQ�(`���J0G���U*�p�7�R�OM�ԞDH�ѣFT��(�@��nT,l1Ͱ��Dl��2FL�S,치V&d��6��h�F���>��%EL2yC��S�I��0���So1���Z\nCW�?�MԪ;&ϩ|C�ٶ3�z��M8�򭳛������@\\��ldl����V�Y}aT ���Vo5�Ŋg�\0\$����k6�b�\$���!��/��i�=5E��NO���0Z�Ub�	u��� !b8�|9U���f���KK!/�*}�#�k|C�a� ���Z���Λ���T�~���6ɑE���0�B/���G@11�B0F*�\0�\0(.��2ǤS������K��E'`*^��|�:5E(���c��S\0F�|��~xw��&�,��R�rP	|ٞ�ڇ���h�х6r�&2�G�7\n��m{���1S�������p��H-�V��X�VS\nA�����ns�HB������<n�J�\r���90�b�[,�[xж���J)LCԖ�z�ZA�YH�jl�\0vX���I�o�y�������qCu�G���G�f��2�\r�~��L���/8�?.3�e�/&�:x��b�Ifb�n~�\0�]�.�\\;0�FSQ\"#�An��K9����-o��5�u^�H���[�9�y^��,�� DP�O��^��N����	}	Qs\n�SB�0ns\0�ۣu/�B��,\n~���%��ڼ.'��v�*��0O	��*�\0�B�E]�@�/iV��\\r�Σ]	�t��I�,=���5\\'�Yhm�Vt���(�S��\reÿU��ϏP�I�U(\0���h?F��:�u��QQ���ϴ��ba����PHmE���Y>�T,��M��w?�O� l�tI�>�J)->N����'I��!��fҲ�N�;>'lߊn��.��F�=��u����)'@��姰 ���|�Ȏ%�~�5O����6�6���r?��[��S	��&�\n`�G��������`�l,�c�	��e찹���g*l*f��\r�KT���y�.�\\��d ����~#~��X���H.>%l(\"\"������p���GJI0�J�p)ML�������'T�Ȟ/��n�Q��\\-�OJBO,:�����/Th�`�r��8Dvѐ�\rgԏ�K\r�s�f�OBjE��FJeBb,�@�C�-�+O̝���L̉�s\n\rn�(Βh�K�PJ�:'�`�Ț���n��WC>ȥ*ne��\0� �	\0@��X_\"D����_��G�l�NNAf��d(̈́�k�r�0����P\nj6k)�W#�&HV��L�D⌬V.g1d��\n\0�b��DN\r�Ν�L�N�U\"�D#qj)\"y��nP��*�t��n��V� %΍�3e�	��b @T`�r�>IV'�d0��4\\%Z\\%b��h\n��C�N��̬8�lR��g�(��cpm�R[n��r~Y%�B\$���¢�\09��J����=��(iВR�\$c!(xd�Y�d��&�Kn&B~+\rbnp�u���!���c�N2*�p�Ú5\"����& Ò�iI.�9��!���c����9/�r�l-/ƸM��Kf�	51!N9(���ؓ���óL9%m*'?5�*�����׏t��&�䥋,1k/n4��T`�8�~'��&Hr;��5�~[h��)��:D��n�s�xA3�:0���ʣ0�o��o�}\rB�I��;\"��7N8��\n�]���Hb �\r��\r�\r����<��=�fM�&\"���q^�����363����h��7�bo�1Qd�(�B����+t5(~�3��7��B�|n��>���d�W��X�Sb�2D���4r���!�26q�B�gH5��OÒ&T}N�Eƭ�o��o=\r������⅛����\r⼴K4T�kNL1M/SR�39/�yOPnq���0���/�k �F�&D0Q���Q���~��N9�,\\0��l�\r� ���s�73��ADe�E?3D�u(o��nn���k\$�Km\\�����P���:X3�4\"}Gfk5rt�tpx�CmH��C�g\n�t���p��/u�2��\\�܍溒�]4�2s�#zp�Z����i)��C4��4u��Lu}F0�\nU�E��`�9�]����^6A�FT�F��a�_b. �Un�@���_��2�#8�N��(\"�)2{_d�Y�W�5\"�/��f#�pE0\nF&^s#M��^S�hʁicF�\\;a�V����i5܎1g6�D�`��h�@8�6�NP�l��EEF`�Ș4O5VФ�iv�PV�\r��n�\\��3�<16�u.��U\0�DF8��l^����r\$D�h�W.y�X�l�7's�,���x'�^��r1\n��b�P�Mv�v֣[�9a�OCWy��\\�Kl3x�n'��C��v�BY���N\n�6Uir\n3�oH-)֯m��m��F��@��TgGv�b־�W�Wk�|��C@�8�О����n&�i\n���N���I����mm/6�)���_�q%��q����.N[.�zv��Y�7JdV).MJ��K%�|��0���m�\r7\$�N�դ�@��8J2'��'iL�*�.�\n���p�\n�ۅ ��J����(2�wT��-�A.&ȉ�V��ͯ\0��d�Pd\r���/\r�k\"�v�Ꜣ\n���d�.'&�V�\\)t�4�N-;>C,�ʙjVNb�q/vm���phJGD�Y9��:D�mW8v-f��|q����-5�:GYT�Lr�����l-��u�Bu�l+m��M��7F��GY�sm��MW���hD�Mr�.\r�����\nwb�355�nJ���s�f#���(9�3����tC��c����V��������s�\n�\\+3��9؎qHNO�\rt��`�`@����B9\"U~�(�W(a=�g�>��Ӗ2�U��r^��*U�-�<�P'�����2&�{���WH�C�I9���h=x@\r���z\$�v�p(2�_�VȬH��S��6z�-�P	\0�@�	�t\n`�";break;case"tr":$e="%���(�o9�L\";\rln2NF�a��i<��B�S`z4��h�P�\"2B!B��u:`�E��hr��2r	��L�c�Ab'��\0(`1ƃQ��p9Φ�a�l�1�N�5��+b�(��Bi=�D�q��e0�̳���U���18���t5�h�ZM,4��&`(�a1\r�ɮ}d=Iⶓ^��a<���~xB�3�|2�u2�\"�SX���S�8|I���i�1�gQ̑�̚\r;M�no+�\$���#ӆҙAE>y���F�qH7ҵ\\����Y��;�H��#���9����:�ê���j꾰� P�0�j�+\$�.1+ɠ#�(��O��;�4#�o\r#\"։(�x�9��V9�����5c�1��p�3�X����;�#��7��X༎c�42�\0x�����C@�:�t���1�B������|�9�2�^'a�ڍ/i̍\r�h���x�#��&��\0,�#�hԖ-�m\"L�f�&JB���,F�M��6��[TUB�T\rmCT�!I�b��@PJ2%�@��v(������,����h'��}T���6�u�~�VD�B)ҥ8#<CZ܌���W�-�o P�ߔ�J�\"H�֛���=a6x�4��h�ñBH����N��̻��3,�d�Q#�D)�\"eqm&�0��˖��p�A���L4֖	�O\"�5Ӟ�N}�Q!�:��śI��A�94*�\$�Ci�RX�{��M`����{#�: �y�D��s�m��5�\r�f�� ��R��@6��D�0�+�b9\$WD0�4m�K����Hϧ��|�7�v�@���#L0��tO2�Hܷ<K��s^̺&4:!D���3�X}@��,H�4�c-�í`�`�d:����>�j���z����^�Z����\\]�\$�J\"77ks9L��g�X��E�(iW'��� r1o�<�����q�D\r�_6r�dq�8Љ^e\$�=�|pR	j��5�`�:_	�2�4ʙ�JkM��8�����y%.�T�\$��\$(����\n9dD�^�<G�N}��h�s�J_�uK���@�I��\$1�H�	`�je(3s�H��e���0��@i�a4B�ᒓ�\0b��4�ə4&�؛��pNOЍ�d��ñ\$��L(\0��B%!�W<�!	_\r�b��@cN�Ő�P�?�6�X�K�����\0�a파U���\r�\nw���j��&�zY�&H������r�9�r*�	P	@�NuRY��\0����f��(E�3��ʾr�X�RsD��G�h�JB�B��-�\"HD�a#2�#��\$[�Ҹb,ް �0�\n���(TqҺDt1��:�Q��}k��;�����gLİ��\"�@�(e#�u�*A<�S\nA{�x��~^��F�y���#�`�QrL�I�}d-��E�ՒJ'���?���8T�Jxd��ɼp5����aS�r����%(��z8Ԅ1%�_LPW)]pJ��!�8b,��РT:\$L�%��RKzP���r��B3�:J<D�yèysd�f����9�\$Ԏ`�;+\r�}��^ZAyIX(eD��W�zN&`g� Ҋ��\0/g�����S�q>{I%զd0���r�K�Tu��p \n�@\"�g�H=���s�j�\0D1����חS�()�D\"�����N�6?�cV��Y��h%��Ē_���	�޼c7�cy\rg��?CҰM��G��N��C��,A������NL{hW<ڛS�6QD}H�Vn�[��-ؤ���'��ˀ]�H��:��j�#�����@[�\"��������*�^�t_낝S��N�y�2�G�ن����U�QL�/�]�����m(kظ\n�K�8!��\n��ʡ07��5hu�nͿ�`�&қE���\rn�dTZ� [h@ܫ�!.a��0�#��c69y<�=���hl���W��Q��\n4f���^7�h���`c^@(!�}�)�e�FY�c��26�کG[� �s�	`�ᄤФD�h*�5�kf� ~�9�\r�`�`�BH<1\0��]9�=@��;T��b�X�����dj��00��sNY1��a����PMF��ر�+[�~,MYĬ���>k�:S��G��>�N�����d�u���?Nh�,��3z�a�3�qd����L�B��.��U�no�x\"��׆���wo�b/0���`�]�����p��\$�(����E�Z��U\$��ޚr�(���d�\r�S�P,�)Ⱥ'��]������Q���n\\!��o��`�ET�n�����aמ��J�>�h�vw6u��d>�8��I���qDJ\rl�%�wЯb�]b(/���,r���\n4`�e^y�;o�B�\$)*�T-�\"-�F��ª0̭�\" �CŢ,���� JE�r��\r�x��� �)&\\F-�<Cވ��͌�g�̏�ܕC|c&�\"�IƢ�F����f��\n%;	�-��⌦ZD�;N������h�n���L�.���k������}\rn���H���M�o��Mྍ�dm�P��P�0��F}Ъ9M�w�X�FVYŀI-�',�,b�6�\"�n\"K�&��*�\$0���k\"X+dQ.\"w�\0ȴBKV<��(�VY�\\ӑ��C\rT�L�3M�h��ť��-���_���%V٭���q��f���\n��HZ���Hl�Qж�Q�1�ѵ&t�q�UQ�f�L����(w��cô+���\0�Z��DP�O���;p�o�!q� p�^�!%A��ZKxA0�\r�\"��>+�\nq�26\"�\"y�&:b�M3�ߍ�7�XG\0���P@d|=�8�&���2�.��'�f�m'\0��(�W(�&n����3�U+Q�\"Q�+%R�*�{+�C�?1���\$�L��pH>���P�#�\r�g	+��/j����R\r%�V��\$R�Į�X%�h�8��\\/&�#�2RP�s+b8Š�!D4[-[E����E��N�S��yBN녒�N�\r���@�`p���h7�\$C�\"g�\0�z`�e�4�̳�`�@��ZV� �j��t��l6��Rk��.��f�P�<#�<s<��6�BL�:��#ǖD�`X#��c�݄;!�~��%�c���\"�\\�'(@��X}�}����G\$ˮ���X#س��e����,��.%��e~�s��d�sT�\r(esBa�YD�u*�G\r�\$K��np�ǉ�Hg�E�t<��T4x�첸�	��>P����>e�:v��\rP��<�`�^�Ƅ�_��R������G�L��z�P\"�ô`t�Ln�zC^�Hp��T^�eDɳA4E�\r�.��O���Fy�؜�}-l�D-	# ";break;case"uk":$e="%���)��h-ZƂ���h.���� h-��m��h���Ć& h�#˘����.�(�.<�h�#�v���_�Ps94R\\����h�%��p�	Nm������c�L��4�PҒ�\0(`1ƃQ��p9�(���;Au\r���*u`�C��d��-|�E���X~\n\$��g#)��e����x�Z9 �G\"H�ES���X�j8��R��9�ֽ|_b#rk�:-H�B!Pń�R��D��i�yA	�ǖx]5���K�Oc�J�vf[5�{���f�t��k���,TIjh���0�'\rz~�8ȋ��\$\ry��*�.�#΋4n���N�ƃ4����ê*��0(r}��48죎��'plA\rDn�<�����@���#)ی�F�^�ƭs�����	�X �������?��V��	��/傼P�RD�h�\"�#O��J#�\"JB��dr�Dh14�ͩ���1>�H�r�Nh�m�,�hh�\\ʔZ8^�t�&�����P�2\r�H�2�O��1E��!j�JR����0�RK좝�v�[̬=��\0x0�C83��:����x�q��\rYWVp�9�x�7�<9��X��J�}-�i�h�+�%N\n��A�|\0�|�&���\"X���2,�W�\n�0�<A��sg=E�|,�5I��d�D���G��,�6�����8��E���I�(�e*��i��8���\\5���e��M����+޶�!��G��1!M\$[#��L�{�_����|n�0�>��n4�H�Lf�I��y'��������\n�&P��f�~l���ѡE�\$F>�bQ�lF��k>�47��h3��&��9���t��l�~�'N��,�'nk�0rD�s�;�&z�OC���(��Yf�)�\"g�<H4��0),��IQ'3/fľ؈ʅJz��K�|��B��QBI��}���?Ö����N-]\"�gp����SK�\r�t���SGG��� 󰭙�s�\0�#��PC�m0�0�,4168E�d�%^C�u�	>��`#*{,%T�@����C�h�0�x����A(䴤FM�aĺ(5�B��tL�8��	(+i�f�eT0��(T���C�p0���&I_SH5�2:�BW�:mAIa౷�%�C~0�h\n�O���M���ɒ�,8�L�	3�ۏ��m	86@����%DH����Ԓ��z�ܓHh���#�)�Wi	��G�&�0\\F\$�}t��7������G\$)f5�����%��Yx�Z����a�u-1��əo�f��4Tl�jgȍMs�6[��#%e���k8S�.����ك/݄�7�ZbN��<Ht�SS҇L�2(Ʌ�j�~�'�6�|ݠ��pQ{Q�0AR��)�t���h�����	�jp�Dh��xq_+�-GآG%V�UxrZ1J��9�|���Z�X2������\\K�sUҺ�j��7���Mm_��!�hC��gMu��=��k[���6��7NF,4M5é�T#�>�G����?�^/H嘋��i+��Ef��hH�i\"�=i�U��V��[�q�uʹ��]��xP�xs�+�|��*v����F{C*I��45�Q�k4�%�9�K�f�͙,OD.��kAU�H�	l��5z�`\n\nl�Z�W��ꩉ�v�����	gf񪋰�5���/��=v��͖Y��'am,P�&�lУ�E��J��L�����^Ha6h��Ӑ��-č�@ �rAZ7P�`*��N(b:8�AT۔'�O�ɕ�>�Z�8ȔĬ)�~��ػhєLh�\rɴpf�5�X/�	؞)\n���[zج?�r�G<���V�g��v�'����\0����H�Ē���sI\nVׅ��2�R�R� ��,{w6Q��@,�QX�#4��mMM�#��,Ue��H�����,��љ|˩��Bcڜ�-��rt��7�[����6a^�.��V[H��J!�\n����>jsS�ER��J��Z�%�4d�\"g#yw���{-}�L�B�� \n<)�GS���f�ijM���Y{�g�U��l��Y�y��u)�HS &�9߲RMQ�r�^�hsV}���5���8d�R6�]����C�Y�*�Y��;z�-���S�#hf`l߮TvS)�+�����b�\\L�HL!�݌hvQ�cD��mblhC�ڏ*�4����҂u��#]�%�m*R�xN�Զ����.�X�������(��X�j�Y'�C�nq3<>-�e2Jiy�9�q�2˥yH��=��8#�R��2���S�N�3�)w�H�\rI����)X ���p�F�9l�o����4�����4 �9��_�q�&�-爕�a��7G��~����T�=y7F�{�B:�)��Kkz/V�C�KmTb��J	܈\r�˄���|	hs-�<��3`ʸ �\r��%�`�f�#f#D���f�l@�s�)�/\0,2B�3 �V\0���\0�@�\r��\"�'�B���\$\\�p��'��G����k�͠�/��O(H�`y�2�t�&Z1.�D�0)<��|�H�L��-�/(\$f�rr0�FS�03\0� �`�i`�&��F�Ŭ�ְ?\0pl�l�b�����G��o�#��H��?L�;�\$U@�\n��`�u-ȓ���,|V̀(�tȈ��̜L�zK\$pqh�f��ڛ�<g�F�=�ߑ������e1h�&�kǪ��tv��!��W-���	N/���9/wjA	����g��є���j�h�e+��A�\$��|���\$���1D�� G�A�\\j\$���#�i���R9nk \$ q�\$r!�Lj�P|�)\"�W�Z�2^�S#�wo�	1\"�2�P6E����xD	��\$r�����o�b!F\$T�P\r��(�\\*�\"d@X\n\\�j��d�8*Q����~G^����ؒ�\"P�:��`ǘK#���\0�>;N��d~!1�M��0��O{\$�hFsH�A3�ڐ��2�b,��a�V��*�s ��γH�\0h3��x�]\0P|@�dx���/�MN��sO\0�h(m�jF�Q~@�b܂p���B�\"�0\$L+C.�����c�0���g�8/8e#S�*�o��	bw\n�i:g��\"y6��3F�\"��}���eT\r��]p�\r�`�\r��\r�� ��xrΑ�0�A\0����e �k,��S4,�3[Ce\$���t>Ǯ�?�fw�� oiC�n����zP�F+�d�Ti6N)��4�p�\"jM\$�3�rT�	��c��*:K��\$�}1�k�5��I-IT��f3*�@��\$WGJreJ��~�G�3&�I'It��t�L��L��#���T}Kk?I��I\rM/�1R��B�S%Pp�P�6��P�'� 2P�Rp��u.<|o5d�+�-���)n�)'�S�\nB��Fd��� Q�tH� <Na5R�o2�*�dS?ð֏\$N�B����6��0�xn��/WX�-\"n�R2S�\r��.Įz�ze�<}��K�J%H���Cc�#Q�R�1K5_fn'��f�#PV˥J�EGuG��_�ny�&����HV,aA_��7�c�QT4n�Ts^e<U���/��b�\\t�cj�PT�a0�Pb\"!�[�pr�c�5�\0H��GE;X����`�4�4��;j4����_�c��������a�QQ�Te��&y,e���1O�dv_b2�m�+c����k6�dG�/S�ǋ��h�Cqz���E��)j�5tEQ�q��M51dN�q��q���`�u5n6\$h�;r��Gj�y5�WT��3d5H�Wb*Vd�OHWIeq£o�KU�n ��f��'=gʒ�l�WX�Wjlw���U64�s7���z�l�utB��6`0bA�pʄ)3�س�4̶V�T�5V���F�no�1�3.ܖ��#�\0���N4�G���DNq�V¼�kf��H�v2945v��}(�������+��k�3Orf&x�wiǾ����8{XiO�N�t*���p�6h�aN3LJK=֔�˪:�bN�HR�,u�,�)X��	�ˎ�\n���Z6���!uT4Z��<�eN��ه(�U퍬�x�(�ٍ�/��F�N~�hGv�5�}n�mHD@%Bg4�Ƌ0��J�#������'Z��������ә�a3p49�G�.��~��������BvM\"W�7�����Snϸ���/Sp{U�80��_�Y�c7LoM{y�]��d��̷��o�D!E\$�`�\\2��8�76D�hl(f0�)臨>sL�y��NQ`b|r!Pc��K�@S��DR�-97ab��#G� wz�5�	�{���\$d�0��I�fWDfqo�(#�H���YEh'f'\"mf�Q�_[�#,��\$�Ӱ��0�OּϖEn�\$�@�N�lw9���r)G'A��#���MUbJ�H�#�h1�l�Vo�/j�4=QnV�WEx���\\";break;case"uz":$e="%���(�a<�\rƑ��k6LB�Nl6�L��p(�a5��1�`��u<�'A��i6�&��%4MF�`��B���\"���u2Kc'8�0��cA��n8���!�\"n:��f�a��r��� �I��o7X�&�9�� 5�瑃H��q9L'3(�}A��a�p�-r�Lfq��J��֘lX�*M�F�\n%�mR�p(�+7�NY�>|B:�\rY��.3��\r���4���A����s�ҙ���uz�ah@t�i8[����-:K�Z���a�O7;��|k�u�l��7�*�'���֊����+ɜӉ����h@<6`�5��F쌣���j꾰�A\0ܽ-b��9\r�?\n?�81#���/[N\"\r��<��Z ������_�*\$&6B�|m���v�H��|8��b\"1�x��J�ʸ4��\0x�4c(��CC�8a�^���\\�IQ\\،�x�7�p�9����I�|���`8�Q���|��r�j���\0��\n����A�,9%O�.�!Hb,=&C���z�V��=q�mh��!�(�2��8���xЯض�^�Lp<��M�,ۋH*�k0�U#��=Z@P�;�z��Iϛ�S+��@8�h͞���2�2u#;���0��J7I�ڌ�J��PkZ5�3T�\"Ϛǈc@ʈ\r�<���S�\r.�rItL#M0݌��J9�[J�\n\"b(:�)8��cz�֥N�[Pʲ�gGO��a�R3�<Y�saW6���c~;Yj���Gh&���j^�W�a�(ȳ��B\\���Z]\r\"��7�iU�\$���cg��%]Vy���u��!U3��M�\r\$��C���<��NSώ;[�T���J������7�.aO����.����vΗ��p˧X�E��;~.���d7�7>�48�����B�Kj�`��q\0P�2�IrՇ�C`�u\nk���y\r�t��RΕ�J)�J�XCf&����Y��n-)�o���H�3 �Y���| �{ &���:�xR��ن)0y�B�	;����B�Ψ �2���\r�O�v#�!@�8Y�Q]&��� ץ���&�t4/�ڛ�a�Ni�;����C�I%�A(E�!p%a��)\0|�K_j������I^`�\"�t@��m)t����L��m&���4�	�]>�M߰䚓�,��P��r4G\0U<��\$E���i�9���P�PѾ�	�5�`��|O�:%�*�P��:(rET�s̄�%��[��txgP2���\ncL��4\"t��2&�T���᝜S\$�P�!r`�UVF��I(c�M?�-B0N�f�1Ӏ���:�92[�����D�Zg���W� @@P>/J��u~�AA>'02�9�Z��F)(��� ��Mr�Y.L[��'%\$��\"`Fg�B+���pFT�V��䗙\"h���l9s�Ɔ����mF�#�'=%�d���ꤕ��V!�)� �SI��L�����\"�5��bz	�&MQ��B3}7�!p�Ѝ��@_�#��q�Yz)(�r�h�9c}&m\r�JW���m�����CDI��:A��B�J�P	�L*P�Ze�m�H���Ź8b�w�7`����M���4֨�d����\0��I�7z�*R�c\"iq!�����\r\r\$uA��]�t.f��/��W���/!�����8I	�\\#�p�P'a \$�,5�Bv\\k�㢤X�sY������AY�7�F�t�I|�N�s��߁�>�+�\\�R�@ƴ'��&x ��u�Fʱ@Z�Yk\$q�Քn����ɵ�2],��/C�`Y�3�;���٭3t�YT`	���-h�\"�ȸ�YY��H��̤���Ѡ�R����e�P��rt�>K!�����sI�����B\r�T2�<�S4�dŜD�u}\0(9�35�{�q�m\")H��5>]!y�Z�TjݰfF�\$�6V�~�\$*���V���r8e�ĕLB�1�-�9�!�X�>F4�So�ܲ���!��\r�g��Z�8gD+�*p�l�����:j����BH����ٻ+A�f\\���4]D効pUdB�T8��2�Ӿ5bdLӴ��:vU{-9o�㥾�r߄�9-��͋��.��Abj87fFLçW��?9�\0����V�}`�u��� �O6˰�n��:���ڴ�Nv͙�6�uo���{LJ�=��w~��z���+�%��L9�y�������S�5��ߣ����X�2�K��o�	w����5�5�`���5�h]�I�Պ�4%�<3�tvC!�\n�𚣰VZ�\rĳ&v>�b�7���������\\��U3����b�������w�����fz�I�2��_��`�_��t�.@��G`�'��Fr1��]��I�\r��4B��-`�_�F]M���3�n���/�L\$),-E�&,���@�F\$*\r�\nfEN�D\"��n�c��Ά�KBD���˟�ƈIB������j��𫰨Jp�\nП/�	fR�f(od�0��c�A���.�6/�k�0�o��>N_��\rΠ��PV�@�ތ ����������Wn�-�m�s���>�'�>2݂48�X}ڟ���j�^�BZK����d\$�m���-#�.pZv�V�Z\rc�9�����蠰�m�f(��[P&�I�\$,���&xg�l��W��\\���\"�5�������#���������q�do�5�q��PW��BU��̒\0lQ��� EyL�A	\\VO�j�T�b@fm|�hD�ǒj#���p�r<o�A �\$p�!Q�a�\r��%\0\r��F)%��`��cO&P�'�\\�}��ߦ�\$1�/u2a��rT��k�kD�ę�+g\$�+��̎��*��*��I��Ǉ��dJ��X\$\r%nT&r���\$�dR�1	�ZȾ��#�:6�5��ep���x[�!�;�,���2l�3�~<25\0�.\rRd\"z���2�A2\"�YO��r�e�5ϴ�2��C\0�`ܲ�8itŠ�`�b*>��b\n���p^l(4��i�D1�[��rq�S5\r��;%�2�s&|�����#\"6��4��8�\"PFt/�hcN7_��	r�C�>�}D���/�{%zR�.��j1OD�mz\r��@& :��&�\r�F��7�|^�O�Ն�Ȣs44F3���nE\$�,\0�C��-W	t]+����64��̀�ns�G�f;͝�G#���K�\$����\0YP��|'b���J�m)��]O:�%�5/�`@�GbGG�(�(4%��� A&'��6�L����q5L�\n��PD ��d`I@�'��-�1���D|k8b�?e�";break;case"vi":$e="%���(�ha�\r�q���]��Ҍ�]��c\rTnA�j��hc,\"	�b5H�؉q��	Nd)	R!/5�!PäA&n���&��0��cA��n8��1�0�L��t�hb*L �QCH1��b	,Q^cM�3���s2�Nr=v�����8]&-.��c��\rF 1X�E)�C������	��nz4�77�Jqm��U`�-M�@da���H��9[�׵�\r���H��!����y� i=��Y���d\$�I��XW��xmmt���WjYoqw���D��:<6����\nc�4�`P�7�e'�@@���#hߢ,*��X�7��@�9Cx䐉	�N�A\"l<��1N\n�򐅥(�:���Z���@���K�!��\n���v�\r�A����!��Z8B;��&r����ڈ2\"�8�0��P�2\r�DFߍ�(�	���:��M!#�=pB����0�K�;�(p�:Ё\0࿎c�L2�\0x�����C@�:�t��d3����8^2��}69Ӵ�^'A��00@�\r�p�7���^0���2�\$���0�).�:Ft�6o���5�<-uHc@�,ed�ۿ�P�0�Cu���#0<��(P9� P�4�8޷\r8R]\$�KLJ�)4A\" �3#��!��:��U��A�j�ñ3h��kQˑ�Y\0\roq~�4,�1�K(a�O3f]���K�u�l��<�/%h����NA�8.;J���8�	�ˍ�d�(��S5A�����3CwzB�6'�X�(��P񕍏,h41��]����)��+o�휊��\r��7�cG;�\\Hm�s��*n�v�tλ7-K��.:N�(��F�#w���2��\"�����a���;�p�!p�Zt\r���!Tv*�:��A8|q'iZ��9���3\r#?�2��AB\"\r#� �D��^?�����ႆ���w�@���.��yb�K���	0se��\"�.Qz4a�)�	��Ð\n*]���2Q\r�����(Xim)F�a�RH�t&�\\�³Zu[�J,�y��PܨH@�F	����!��SLM!�I\nU2Ğ� �<=N�=���Ҋ	�:ð�C\r!�;�0@�X��UJ�W+dբ����\\��z��ZI�ܳ�[�\$!@4�lM�\0@����8֖E�t���1���\"�J)�� \$\$�.�3G2��!5�@��\"��J�V��b�լ}DrP9+�x��b����b>�n�ˈ�\$��6�V�D���h��H���\$V�k����AB�\$,��Wp��\r������m�0�f���Q���ޛ�B�!Ĝ���]�\r��ANPˡ�	e�=���]�\n'����зHA@\$Nϊ�rJ�	фZ�1��\0pA��\0�B�҃Oo�>(J�(Y=�k�'��Q�Od�9���\nAY!�8)�4��r`A�R�4��U��ѐ�C	��hrW&�4G\r2�-!7�db�b�Ey3��Va�>HN\\=v�.�htc'�܂�@\n#Q���]�4���Z9��[A;S�!2&�ƨ��R|Q)�L��!Y��g��\$�\$�0�\r\$����70\"W��OI3\"T�.�7�\n~���Wgn�E̒�\n<)�H��b�s��V�EY�\$�:��!!�X�%���7JB�&�����`��Έt����\$QH�I'�FS����>?�Y2��`]JKi5m%�hd⬘\0�²����2���w�-�L��IO:�K�(�\0���\r�KF�l�����Ōg�pr�R/˦F�i�̧ĉ���E�WT,��e���h	\\e����p��lϡ[�.�?��>����l���\\2[4�4�1���)%1�;�?pQO)���oV����d�d5kR\n	�\0�v��7�P(�	-z\$��`�PJ�xF����\"!��T?a�֬OF(s/��mC�Tr#M�D=U�C��^i�'h�KӢ�a�:�m�.�M.>k��!�nY�A�ȣ'ճWy�_�!� ϑ�����:0���Q�\r`*R6|�[�Y��f�����YwS`A�<�}RtC��>w�nsf^���MI���Q[���0�o���G˸ �@�BH��=C[���\n��,���9nI�o�C�X��\0\nz�\0�.�b��ر%�p����B�g\$�=&��{�Db�/F�AfoK����w��	!�EŤ��ס�1J�d!�b�P������dꐑ(w �8ǟ\"_~t�R�f�JxP(Q�/Fd��g#'��\n20[[��.�Y�(94�vh;����C���8ϰ��9Ut�R�Н����PPƑ!�q���F�XjN\"]�	����B���sr0l�2�.&khl&s���P@��\"�=�8?E�B���:��\\�)Z.����D�6'@~�B����AbA)Hi����b�j2jW!hI�!�>��͡t6�����/\0�\r�.�`�7G�d�ځ̽��l&#��}˺R��aM\n���d��G4�C&�����(а�\0��%����\"��2�\n`v��T0�s\".�C��v��\nOA\r�l��������;�B �(ȱ;�)�\"y�⹈,4�Z�l�HI\\ٮV1���k��B�i�\nu\nnC,&�#7�j�lfǫ�JbN�db�n�Av(�<�qIn0�O2� r��f���1��\"�k,^���#P��\r�^��\r�51�Q�>��V�bzs���N��o��G�\\�q��r.>�p0� !n�6N\"��ﮘ�r\"�Z�A_�L��	n~?�Η���@�%&&eB�d\\�� �/ �JS�!p\r'�w\"n�'j����!�4�u�N#�GA)�:t�����Њ�0E�*?c�bND�����x7�T���Ѳ�q��M�)L����N9,��.j@/�d\$0���B�q��Xi�/�&�����\n�=1��-�0^,�*�o2�\\��0M��N%�\"28�qJ���Gl��s�q5��6' fM�_K�̪m)Rבlf��3�6:�\\;Dh��[9�N(�wӱ�\$�@[O��\$'@\$��K�t���F1\n[\0��B	ĲCGR\\��f��LnM�A+�q�%1����\"��\0004j:e�H��\n���Z��V�.�I�|[�F7�J+��[)��;�X��0�S�1t5�tO�?Q�)SI>�49c�eK���C��D�Lb�K�A ���n���^=�:΃\0Q�B;82��%\0Pop���>��F3��L��1/���q�	��& @<�ds��L�l'� M���wc�j��GQ�\\��4���IP2�6=*�I혜l�G,Q��9��\n��B��,�]�ҝ�zM�)�&r�4�=\"�+�B��a8F.��je�^-m�JO�A,�Ϊ�k,pIC�ǎ����e����(b\$;��K���F��e,��+F��H���\\M��\r獫V�,+�;O��";break;case"zh":$e="%��:�\$\nr.����r/d�Ȼ[8� S�8�r�NT*Ю\\9�HH�Z1!S�V�J�@%9��Q�l]m	F�U��*qQ;C��f4����)ΔT9�w:�v�O\"�%C�B�r��i��x�M�3���s2��b��V}��\n%[�L����`�*9>�S؜�%y�P⣎u�YоH�Q�)\"�:���Vdj���d��K�:�t�Rd��(�t/�0�Vc5_�hI�G*��\\���?M[��h9����ͣ��Qp��C��q���H\nt+ծ�B�_�c��S�>R�\$�2���A�Y�n���(\$QBr�%B��+E��H�a^C���Mb�]j�2�����s�DNr� P�2\r�H�2�GIvL\$�\"�s�|X����K�͓,r�p����Zrċq!\0к���D4���9�Ax^;�p�F��\\7�C8^2��x໎c��2�p>%1�A��AN�8GI+��|�\$jE�2�V֣��|s��ӦI����I\$�r�T�NS��)6_/���]�T�D��1�H�@PJ2\n.tH��D겼�2E�QEW�AU�QPr�D�G�Q�xW�)\0^[���J1�9{K[��ZH� N�D�e��B&��s��r:G4�ոܨ83�Fuqh����XsST�tܒ9l�Z�!zN1�)P��IV�ۂ��&Whqv�䤙�^>�#���55m)ę�rsyynX��gZ�9����uLA::��aZH�A*h�5�d���ƌCE�K���9t��եQ�J�-#I�5��\r���:�������70�C��<��h|�oØ|!��p�4�������A/K��+�KB�{��LD�')bJ��U�Ӕ��>s崏��G9XS!%��a�(AtF8:1��%<J���a{���䱬�4]�	\rE�TMz�!bt��XʵN	��R��[���C\$�\$r�!b�����DP��]�'�@�Lx���f�Q�r~¼[��\0 �@�\$ԭB1v+�ZK�y0&\$ș�@wMI����Өdr��:�*�A�7b�_�:X�%��P�Q�'En.qG��\"&������;�6-���o�����P��qtN	�w\nR�eK�}0�4ʙ�Jk�	�8'\$���tN��'T�䟻%v.�*:a,�Ř�\$��W1�,��\"r��,R(��0��TV\"�YH		��Z)0��xN%ZQ�:��PĎq6.��%B���EуԀ�S��k̡��N�\$�i*��@\$\0A4ݘ��TDbq�)\n\$�\\<9�p�X��s��~m�8��Q��P�DD�DF���d!E,�I�q�,y �*�P��p��!��LO�^/��H�t�!1B�r��Ԉ�@R����I�3����\"YY�\"�I1��6���-�(�b\$}�N�NJIZ�¸Z�e�Nߺ��TP�@���6\\��aE�tM��qS�\0I���\"\n/�Af��4���:(��T��H�\nE�)=X���G�!\"�<��f�9��!W�e�ւ-y�E�\0��(#@�`�0Y��&�^tkBZ�=���B%\0���a@��x�A<'\0� A\n���P�B`E�mXK�1j����JZ�-�*/Ҡ�\\�&&\\#�y&q�4� ��AE��w��\nqv{�=-n�PE��{D̰���H.e)~�+Eh� ��6�Z��U�L-��\"��on�D��\$[����2��7�����̓�q8=b�ߑ�~�.��nqB9�rɍBɹ��׀xL\r�2�\0��gF��2�dh\r�Ы;	:g�c�>�W��\\�B8\r!ѻ�`��iV^��3eJ.W��΄t]	�&�H�����r�\"�!�bQ�<��^Te��ǣ�^cWT�!,ۅ>�:t�����^M�<��6.�H�\"L_OMA�Kk�͙��.����B�n��ٰ�nPT!\$c�|E���=6�&U����l�	�Gv����8�������FN!.���S@و�6��'37v��m����r��=����cj��� _�q�jb�i\n�d�� W����B�_��,8\"B������Ô@�!Z�ȸ�b�mN�tcr��s�G�J��3��������=ז_�������+�iz��'V����s����?���u��5BC0^	Yb#��;G��ru�^���U�1O,��ޤ�k~��C�\n��9�g>�WJ�!�3��3����7e@؝8t\r1Q`��n��B�qeݰ����\"r�\$E���pDvow�<K?�w���~�XkMs|�c�V��;�y�y�4U���;���;7����>���\$�����dJF(J��Ɔ�_L!0����1+R��Mk���H��p<�p?�z�`(Ήz�/d�IH;���-`N��eP�#��k�\\�%U�^��́|�����{�T��`l&�#/���U�}�i�0��tTЀzc��'��\$\"*�vT:Aa^h�\\��c(\0\"�f9!����o`�, k�����f\r�a�\r(�arm%���H�L m��E��'���6����� KB��l�p������/��� �g\n{\$q/P�n�.N�@��*���iQF�h�LP�DӰ��MB��d����8B0���a�#\rA1��G~yPo����o�ao\rQ�#	�� �jϔkf�<6�A>�\r�8���\r�:l<��r('�d'��B0�e���-ܞo��h���g�\r���g���� 8��y���#^���	x+\0�\n���p;��Ϣݐ���J�\"!��\\�R>.(y0<���!(f�*!^�#�42.,��4\rf֩��^�EƔ\rz]cz~��c�),��h�^�<\$\0#�,S����z�\$��2겐���h����\\���x(�=-�hLz�O����Ì����2�Ύ���D+��,��N�1�K�i��H��NJ!h� Ȍ\n��`��ڊB.l�#�G+C��EB���\$\$3+��>{,���\n�i*��ʾ�`cC��8a,>`a� 1�p��-Q���1`";break;case"zh-tw":$e="%��:�\$\ns�.e�UȸE9PK72�(�P�h)ʅ@�:i	��a�Je �R)ܫ{��	Nd(�vQDCѮUjaʜTOAB�P�b2��a��r\nr/W�t��ЀB�T)�*yX^��%ӕ\\�r�����|I7�FS	��99��S�TB\$�r��Nu�MТU�P)��&9G'ܪ{;�d�s'.��̖L�9h�o^^+�ie��D����:=.�R�FR��%F{A��,\\��{�X�s&֚u��\0�r�zM6�U��!TDǇ�E�����t��l6N_����'��z�V���~N���Z�RZRGATO\$DЭ��8U��Jt��|R)N�|r�EY�Y�g9jX��tШd�P���L��)^C-e�޵!V�%�>R�e�pr\$)�\"��P�2\r�H�2�GI@H\$Ej�s�iZ\$EQ�J��3wG�ړDRJ����\r���C@�:�t��2��,�x�3��(����;�� ^'A��T��\nt�[�Ԕex�!�t�%��I��2,Ey�RQQ s3�]05��aX��ER�;���Rq6WA�iLr�\$P��\0�<��(P9�,XB+\$mp���:D]�9G��1J�#o\\Kr�V�h����^��6C�����f����vs�}�s����GQ&��d1T��\\w*Z���P�2��@t���HS#׺�N#�*� 7�)A��~)�\"`Al��s\$��K,�����JX�\$�dl3W_X�w^�nވ��\rm!n,럗�A�#��r���]?��l�C���Đ��=���n��q-qX�d=�9�h�9�ӵ:�����7P0�C��<�uk�a��7��0�3��/b0��0G9L@��	�D�9?�A�KyD�h���1PP�D#�J�\n's�+BO�����h�ZK��K��#��WJ<\\���*�`tF��6(�p�~��\$��R �C���%D��!o�X�`&���m��n�:D�F�0t�q��1��E<���N��V	Ҳ�BSJ�\\9B\$ @�����\"vD�vO	�>'�\0��\"��!EDE�Ԋ���0�@���>�5_��|]@�pXKs,�Ё.�%ߔ��C�s���ǁ#�<FTи�@�R�zFGh�B���Ob�y��>����*�Q1\rF(� ���5RR\n4)�k�Z�z��J���%����W	2��RL�,KG�+\$ �,\"�8��eN���F��f!�9ņc���*9�����a\$U����Lr±�'Ź�8&\\��������ܜq�E��L	�ZQ�H\nJ{�W����p'Q�H��HI�l|R�`9�L�9�P@��E��Dt\n�p� ���B�Z�������9�J/�:*s�����`W�1:;�M�rAC29D�%���JҒLS\nA@�ԑ=��F��2/�!5c�K\n�B-��#䄑�w�9�p�����Y�'0&B\0^V��~f�0(r�a9��8b����E;D�BQa�'�\0H[h�\"\nE�.'���J<��:(�PP	�L*�)11'��M�:X��_�f�ȼ��z�����T6 ��P(�b�kL	����ȋ���FU:G�r`%� ���4Z�1@(I��	�8P�T�*��\0�B`E�K�t҆\$h�\$dxG����p�g�]3��M��6�����J�(�+⟊|U�PY��xB9QV�[%��	�@e����=�6�v�7��H#��Ȝ�5h4W�BX����@\"\r�.�i�ahE\n��^�O��` ����8VéL�JQ��������4A�����%U�:A��ޢC*��:�t��Ps/A�U���@�3fq,`�R��7)�2��BX\r!��`��i�����������z��_!�.��Zoi[����l����t�@��c\rz`!���_��\rh4S��Kz���rKr�te���r�zEo����mW�6��X!���a*�\"9�V����u�0��i�B�Aa s!�?n��^ҕ�>��A�p�@����#��yq�.xKN��y�.9E�\"|_�+=�D�Qb�����p,��KL0Dj>Xp!#O;\\�T�;ѹS/pۖ�sPL1#���^�8�Q�w�7��}���X8}��@�D��B�Q�D�U�vC�|�l���f\\|B\r}2��t�7(*c��ZB�B��\"�P_IGD�E�8+��1K<Ozu�d�Q�1���<q�뙊�̓ۛ㿎�X���z��\$�5���.\n�Z��Z0\0�0��af��������>9��{�5剰���7a���Y>�A\r��Gk0Ѯ]��\r��La�v#ό�gn\r���i��F�C��|�L�6���l�M�Ȱ&�/vl,�HK(.��F�o�:�Ξ�f�#���p�Ny�@��޾+��E0fڋ���0j�\r����\$>\r��p!ʙD�>�v�	Ī�l���N���\r��+�	����p�#��\rz=\"�z���lP�	�+(�Phګ�P���x:��n9�]I%�Y��\rmW�c�\r\rX<�-S���pb��p�m��l2b�7o���:1Hp�\"?�	pk6,�����^a)�-�yb^���(��<q/FE��(�!������M�\n�F�\$�%��\n��a�TĶ<����\"p��M���O�[�8u\0����\r\"0Q�`����R�\$����Q�s��R@@�m���E�R�Q��H^-̭��ŉ\"���25\"�e22ݲ@�2=\$����}�R#�+��#<�d,���b+�p2��/�b��fc��9�Lb�|�\rni�	\0+|2T���D!HV�F��b�*|!k+�)����8�nj�.v�2���h\r��.��?n*%�_��P28B-	i�(2l\0�\n���p>\${�����JL!b�%hS�d��2W^��Bi��#b�Aji0�.���>��h���\$,�(V?�xj��4Fг1�ed��/l�!���3�#�&��1Ҷ��S����(����m�V�f�n����������b&&#���S�/�ít�%�g/�Vʀ��, ��\nɨ�\n��`��ڏsz+#<MPN4��0�-~��R�qӛ9�\\��>�~�tF�\$:�A�L��c:dD#���%a,}�3�";break;}$ki=array();foreach(explode("\n",lzw_decompress($e))as$X)$ki[]=(strpos($X,"\t")?explode("\t",$X):$X);return$ki;}abstract
class
SqlDb{static$instance;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach($P,$V,$H);abstract
function
quote($zh);abstract
function
select_db($Db);abstract
function
query($J,$ri=false);function
multi_query($J){return$this->multi=$this->query($J);}function
store_result(){return$this->multi;}function
next_result(){return
false;}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($bc,$V,$H,array$wf=array()){$wf[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$wf[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($bc,$V,$H,$wf);}catch(\Exception$wc){return$wc->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($zh){return$this->pdo->quote($zh);}function
query($J,$ri=false){$K=$this->pdo->query($J);$this->error="";if(!$K){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(23);return
false;}$this->store_result($K);return$K;}function
store_result($K=null){if(!$K){$K=$this->multi;if(!$K)return
false;}if($K->columnCount()){$K->num_rows=$K->rowCount();return$K;}$this->affected_rows=$K->rowCount();return
true;}function
next_result(){$K=$this->multi;if(!is_object($K))return
false;$K->_offset=0;return@$K->nextRowset();}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($Te){$L=$this->fetch($Te);return($L?array_map(array($this,'unresource'),$L):$L);}private
function
unresource($X){return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){$M=(object)$this->getColumnMeta($this->_offset++);$U=$M->pdo_type;$M->type=($U==\PDO::PARAM_INT?0:15);$M->charsetnr=($U==\PDO::PARAM_LOB||(isset($M->flags)&&in_array("blob",(array)$M->flags))?63:0);return$M;}function
seek($jf){for($t=0;$t<$jf;$t++)$this->fetch();}}}function
add_driver($u,$E){SqlDriver::$drivers[$u]=$E;}function
get_driver($u){return
SqlDriver::$drivers[$u];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;protected$conn;protected$types=array();var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$operators=array();var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();static
function
connect($P,$V,$H){$f=new
Db;return($f->attach($P,$V,$H)?:$f);}function
__construct(Db$f){$this->conn=$f;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$m){}function
unconvertFunction(array$m){}function
select($R,array$O,array$Z,array$s,array$yf=array(),$_=1,$G=0,$og=false){$Xd=(count($s)<count($O));$J=adminer()->selectQueryBuild($O,$Z,$s,$yf,$_,$G);if(!$J)$J="SELECT".limit(($_GET["page"]!="last"&&$_&&$s&&$Xd&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$O)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($s&&$Xd?"\nGROUP BY ".implode(", ",$s):"").($yf?"\nORDER BY ".implode(", ",$yf):""),$_,($G?$_*$G:0),"\n");$vh=microtime(true);$L=$this->conn->query($J);if($og)echo
adminer()->selectQuery($J,$vh,!$L);return$L;}function
delete($R,$wg,$_=0){$J="FROM ".table($R);return
queries("DELETE".($_?limit1($R,$J,$wg):" $J$wg"));}function
update($R,array$Q,$wg,$_=0,$dh="\n"){$Ii=array();foreach($Q
as$z=>$X)$Ii[]="$z = $X";$J=table($R)." SET$dh".implode(",$dh",$Ii);return
queries("UPDATE".($_?limit1($R,$J,$wg,$dh):" $J$wg"));}function
insert($R,array$Q){return
queries("INSERT INTO ".table($R).($Q?" (".implode(", ",array_keys($Q)).")\nVALUES (".implode(", ",$Q).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$N,array$ng){return
false;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($J,$Wh){}function
convertSearch($v,array$X,array$m){return$v;}function
value($X,array$m){return(method_exists($this->conn,'value')?$this->conn->value($X,$m):$X);}function
quoteBinary($Rg){return
q($Rg);}function
warnings(){}function
tableHelp($E,$be=false){}function
inheritsFrom($R){return
array();}function
inheritedTables($R){return
array();}function
partitionsInfo($R){return
array();}function
hasCStyleEscapes(){return
false;}function
engines(){return
array();}function
supportsIndex(array$S){return!is_view($S);}function
indexAlgorithms(array$Gh){return
array();}function
checkConstraints($R){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($R)."
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'",$this->conn);}function
allFields(){$L=array();if(DB!=""){foreach(get_rows("SELECT TABLE_NAME AS tab, COLUMN_NAME AS field, IS_NULLABLE AS nullable, DATA_TYPE AS type, CHARACTER_MAXIMUM_LENGTH AS length".(JUSH=='sql'?", COLUMN_KEY = 'PRI' AS `primary`":"")."
FROM INFORMATION_SCHEMA.COLUMNS
<<<<<<< HEAD
WHERE TABLE_SCHEMA = '.q($_GET['ns'] != '' ? $_GET['ns'] : DB).'
ORDER BY TABLE_NAME, ORDINAL_POSITION', $this->conn) as $M) {
                $M['null'] = ($M['nullable'] == 'YES');
                $L[$M['tab' ]][] = $M;
            }
        }

return $L;
    }
}class Adminer
{
    public static $instance;

    public $error = '';

    public function name()
    {
        return "<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace('~\\?.*~', '', ME).'?file=logo.png&version=5.4.1')."' width='24' height='24' alt='' id='logo'>Adminer</a>";
    }

    public function credentials()
    {
        return [SERVER, $_GET['username'], get_password()];
    }

    public function connectSsl() {}

    public function permanentLogin($h = false)
    {
        return password_file($h);
    }

    public function bruteForceKey()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public function serverName($P)
    {
        return h($P);
    }

    public function database()
    {
        return DB;
    }

    public function databases($Rc = true)
    {
        return get_databases($Rc);
    }

    public function pluginsLinks() {}

    public function operators()
    {
        return driver()->operators;
    }

    public function schemas()
    {
        return schemas();
    }

    public function queryTimeout()
    {
        return 2;
    }

    public function afterConnect() {}

    public function headers() {}

    public function csp(array$xb)
    {
        return $xb;
    }

    public function head($Ab = null)
    {
        return true;
    }

    public function bodyClass()
    {
        echo ' adminer';
    }

    public function css()
    {
        $L = [];
        foreach (['', '-dark'] as $Te) {
            $o = "adminer$Te.css";
            if (file_exists($o)) {
                $Mc = file_get_contents($o);
                $L["$o?v=".crc32($Mc)] = ($Te ? 'dark' : (preg_match('~prefers-color-scheme:\s*dark~', $Mc) ? '' : 'light'));
            }
        }

return $L;
    }

    public function loginForm()
    {
        echo "<table class='layout'>\n",adminer()->loginFormField('driver', '<tr><th>'.lang(24).'<td>', input_hidden('auth[driver]', 'server').'MySQL / MariaDB'),adminer()->loginFormField('server', '<tr><th>'.lang(25).'<td>', '<input name="auth[server]" value="'.h(SERVER).'" title="hostname[:port]" placeholder="localhost" autocapitalize="off">'),adminer()->loginFormField('username', '<tr><th>'.lang(26).'<td>', '<input name="auth[username]" id="username" autofocus value="'.h($_GET['username']).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password', '<tr><th>'.lang(27).'<td>', '<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db', '<tr><th>'.lang(28).'<td>', '<input name="auth[db]" value="'.h($_GET['db']).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(29)."'>\n",checkbox('auth[permanent]', 1, $_COOKIE['adminer_permanent'], lang(30))."\n";
    }

    public function loginFormField($E, $qd, $Y)
    {
        return $qd.$Y."\n";
    }

    public function login($we, $H)
    {
        if ($H == '') {
            return lang(31, target_blank());
        }

return true;
    }

    public function tableName(array $Gh)
    {
        return h($Gh['Name']);
    }

    public function fieldName(array$m, $yf = 0)
    {
        $U = $m['full_type'];
        $hb = $m['comment'];

        return '<span title="'.h($U.($hb != '' ? ($U ? ': ' : '').$hb : '')).'">'.h($m['field']).'</span>';
    }

    public function selectLinks(array$Gh, $Q = '')
    {
        $E = $Gh['Name'];
        echo '<p class="links">';
        $ve = ['select'=>lang(32)];
        if (support('table') || support('indexes')) {
            $ve['table'] = lang(33);
        }$be = false;
        if (support('table')) {
            $be = is_view($Gh);
            if (! $be) {
                $ve['create'] = lang(34);
            } elseif (support('view')) {
                $ve['view'] = lang(35);
            }
        }if ($Q !== null) {
            $ve['edit'] = lang(36);
        }foreach ($ve as $z=>$X) {
            echo " <a href='".h(ME)."$z=".urlencode($E).($z == 'edit' ? $Q : '')."'".bold(isset($_GET[$z])).">$X</a>";
        }echo doc_link([JUSH=>driver()->tableHelp($E, $be)], '?'),"\n";
    }

    public function foreignKeys($R)
    {
        return foreign_keys($R);
    }

    public function backwardKeys($R, $Fh)
    {
        return [];
    }

    public function backwardKeysPrint(array $Ba, array $M) {}

    public function selectQuery($J, $vh, $Gc = false)
    {
        $L = "</p>\n";
        if (! $Gc && ($Qi = driver()->warnings())) {
            $u = 'warnings';
            $L = ", <a href='#$u'>".lang(37).'</a>'.script("qsl('a').onclick = partial(toggle, '$u');", '')."$L<div id='$u' class='hidden'>\n$Qi</div>\n";
        }

return "<p><code class='jush-".JUSH."'>".h(str_replace("\n", ' ', $J))."</code> <span class='time'>(".format_time($vh).')</span>'.(support('sql') ? " <a href='".h(ME).'sql='.urlencode($J)."'>".lang(12).'</a>' : '').$L;
    }

    public function sqlCommandQuery($J)
    {
        return shorten_utf8(trim( $J), 1000);
    }

    public function sqlPrintAfter() {}

    public function rowDescription($R)
    {
        return '';
    }

    public function rowDescriptions(array $N, array $Uc)
    {
        return $N;
    }

    public function selectLink($X, array $m) {}

    public function selectVal($X, $A, array$m, $Hf)
    {
        $L = ($X === null ? '<i>NULL</i>' : (preg_match('~char|binary|boolean~', $m['type']) && ! preg_match('~var~', $m['type']) ? "<code>$X</code>" : (preg_match('~json~', $m['type']) ? "<code class='jush-js'>$X</code>" : $X)));
        if (is_blob($m) && ! is_utf8($X)) {
            $L = '<i>'.lang(38, strlen($Hf)).'</i>';
        }

return $A ? "<a href='".h($A)."'".(is_url($A) ? target_blank() : '').">$L</a>" : $L;
    }

    public function editVal($X, array $m)
    {
        return $X;
    }

    public function config()
    {
        return [];
    }

    public function tableStructurePrint(array$n, $Gh = null)
    {
        echo "<div class='scrollable'>\n","<table class='nowrap odds'>\n",'<thead><tr><th>'.lang(39).'<td>'.lang(40).(support('comment') ? '<td>'.lang(41) : '')."</thead>\n";
        $_h = driver()->structuredTypes();
        foreach ($n as $m) {
            echo '<tr><th>'.h($m['field']);
            $U = h($m['full_type']);
            $db = h($m['collation']);
            echo "<td><span title='$db'>".(in_array($U, (array) $_h[lang(6)]) ? "<a href='".h(ME.'type='.urlencode($U))."'>$U</a>" : $U.($db && isset($Gh['Collation']) && $db != $Gh['Collation'] ? " $db" : '')).'</span>',($m['null'] ? ' <i>NULL</i>' : ''),($m['auto_increment'] ? ' <i>'.lang(42).'</i>' : '');
            $k = h($m['default']);
            echo (isset($m['default']) ? " <span title='".lang(43)."'>[<b>".($m['generated'] ? "<code class='jush-".JUSH."'>$k</code>" : $k).'</b>]</span>' : ''),(support('comment') ? '<td>'.h($m['comment']) : ''),"\n";
        }echo "</table>\n","</div>\n";
    }

    public function tableIndexesPrint(array$x, array$Gh)
    {
        $Pf = false;
        foreach ($x as $E=>$w) {
            $Pf |= (bool) $w['partial'];
        }echo "<table>\n";
        $Ib = first(driver()->indexAlgorithms($Gh));
        foreach ($x as $E=>$w) {
            ksort($w['columns']);
            $og = [];
            foreach ($w['columns'] as $z=>$X) {
                $og[] = '<i>'.h($X).'</i>'.($w['lengths'][$z] ? '('.$w['lengths'][$z].')' : '').($w['descs'][$z] ? ' DESC' : '');
            }echo "<tr title='".h($E)."'>","<th>$w[type]".($Ib && $w['algorithm'] != $Ib ? " ($w[algorithm])" : ''),'<td>'.implode(', ', $og);
            if ($Pf) {
                echo '<td>'.($w['partial'] ? "<code class='jush-".JUSH."'>WHERE ".h($w['partial']) : '');
            }echo "\n";
        }echo "</table>\n";
    }

    public function selectColumnsPrint(array $O, array $d)
    {
        print_fieldset('select', lang(44), $O);
        $t = 0;
        $O[''] = [];
        foreach ($O as $z=>$X) {
            $X = idx($_GET['columns'], $z, []);
            $c = select_input(" name='columns[$t][col]'", $d, $X['col'], ($z !== '' ? 'selectFieldChange' : 'selectAddRow'));
            echo '<div>'.(driver()->functions || driver()->grouping ? html_select("columns[$t][fun]", [-1=>''] + array_filter([lang(45)=>driver()->functions, lang(46)=>driver()->grouping]), $X['fun']).on_help("event.target.value && event.target.value.replace(/ |\$/, '(') + ')'", 1).script("qsl('select').onchange = function () { helpClose();".($z !== '' ? '' : " qsl('select, input', this.parentNode).onchange();").' };', '')."($c)" : $c)."</div>\n";
            $t++;
        }echo "</div></fieldset>\n";
    }

    public function selectSearchPrint(array $Z, array $d, array $x)
    {
        print_fieldset('search', lang(47), $Z);
        foreach ($x as $t=>$w) {
            if ($w['type'] == 'FULLTEXT') {
                echo '<div>(<i>'.implode('</i>, <i>', array_map('Adminer\h', $w['columns'])).'</i>) AGAINST'," <input type='search' name='fulltext[$t]' value='".h(idx($_GET['fulltext'], $t))."'>",script("qsl('input').oninput = selectFieldChange;", ''),checkbox("boolean[$t]", 1, isset($_GET['boolean'][$t]), 'BOOL'),"</div>\n";
            }
        }$Pa = 'this.parentNode.firstChild.onchange();';
        foreach (array_merge((array) $_GET['where'], [[]]) as $t=>$X) {
            if (! $X || ("$X[col]$X[val]" != '' && in_array($X['op'], adminer()->operators()))) {
                echo '<div>'.select_input(" name='where[$t][col]'", $d, $X['col'], ($X ? 'selectFieldChange' : 'selectAddRow'), '('.lang(48).')'),html_select("where[$t][op]", adminer()->operators(), $X['op'], $Pa),"<input type='search' name='where[$t][val]' value='".h($X['val'])."'>",script("mixin(qsl('input'), {oninput: function () {  $Pa }, onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});", ''),"</div>\n";
            }
        }echo "</div></fieldset>\n";
    }

    public function selectOrderPrint(array$yf, array$d, array$x)
    {
        print_fieldset('sort', lang(49), $yf);
        $t = 0;
        foreach ((array) $_GET['order'] as $z=>$X) {
            if ($X != '') {
                echo '<div>'.select_input(" name='order[$t]'", $d, $X, 'selectFieldChange'),checkbox("desc[$t]", 1, isset($_GET['desc'][$z]), lang(50))."</div>\n";
                $t++;
            }
        }echo '<div>'.select_input(" name='order[$t]'", $d, '', 'selectAddRow'),checkbox("desc[$t]", 1, false, lang(50))."</div>\n","</div></fieldset>\n";
    }

    public function selectLimitPrint($_)
    {
        echo '<fieldset><legend>'.lang(51).'</legend><div>',"<input type='number' name='limit' class='size' value='".intval($_)."'>",script("qsl('input').oninput = selectFieldChange;", ''),"</div></fieldset>\n";
    }

    public function selectLengthPrint($Uh)
    {
        if ($Uh !== null) {
            echo '<fieldset><legend>'.lang(52).'</legend><div>',"<input type='number' name='text_length' class='size' value='".h($Uh)."'>","</div></fieldset>\n";
        }
    }

    public function selectActionPrint(array$x)
    {
        echo '<fieldset><legend>'.lang(53).'</legend><div>',"<input type='submit' value='".lang(44)."'>"," <span id='noindex' title='".lang(54)."'></span>",'<script'.nonce().">\n",'const indexColumns = ';
        $d = [];
        foreach ($x as $w) {
            $_b = reset($w['columns']);
            if ($w['type'] != 'FULLTEXT' && $_b) {
                $d[$_b] = 1;
            }
        }$d[''] = 1;
        foreach ($d as $z=>$X) {
            json_row($z);
        }echo ";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";
    }

    public function selectCommandPrint()
    {
        return ! information_schema(DB);
    }

    public function selectImportPrint()
    {
        return ! information_schema(DB);
    }

    public function selectEmailPrint(array$ic, array$d) {}

    public function selectColumnsProcess(array$d, array$x)
    {
        $O = [];
        $s = [];
        foreach ((array) $_GET['columns'] as $z=>$X) {
            if ($X['fun'] == 'count' || ($X['col'] != '' && (! $X['fun'] || in_array($X['fun'], driver()->functions) || in_array($X['fun'], driver()->grouping)))) {
                $O[$z] = apply_sql_function($X['fun'], ($X['col'] != '' ? idf_escape($X['col']) : '*'));
                if (! in_array($X['fun'], driver()->grouping)) {
                    $s[] = $O[$z];
                }
            }
        }

return [$O, $s];
    }

    public function selectSearchProcess(array $n, array $x)
    {
        $L = [];
        foreach ($x as $t=>$w) {
            if ($w['type'] == 'FULLTEXT' && idx($_GET['fulltext'], $t) != '') {
                $L[] = 'MATCH ('.implode(', ', array_map('Adminer\idf_escape', $w['columns'])).') AGAINST ('.q($_GET['fulltext'][$t]).(isset($_GET['boolean'][$t]) ? ' IN BOOLEAN MODE' : '').')';
            }
        }foreach ((array) $_GET['where'] as $z=>$X) {
            $bb = $X['col'];
            if ("$bb$X[val]" != '' && in_array($X['op'], adminer()->operators())) {
                $lb = [];
                foreach (($bb != '' ? [$bb=>$n[$bb]] : $n) as $E=>$m) {
                    $lg = '';
                    $kb = " $X[op]";
                    if (preg_match('~IN$~', $X['op'])) {
                        $Dd = process_length($X['val']);
                        $kb
                        .= ' '.($Dd != '' ? $Dd : '(NULL)');
                    } elseif ($X['op'] == 'SQL') {
                        $kb = " $X[val]";
                    } elseif (preg_match('~^(I?LIKE) %%$~', $X['op'], $C)) {
                        $kb = " $C[1] ".adminer()->processInput($m, "%$X[val]%");
                    } elseif ($X['op'] == 'FIND_IN_SET') {
                        $lg = "$X[op](".q($X['val']).', ';
                        $kb = ')';
                    } elseif (! preg_match('~NULL$~', $X['op'])) {
                        $kb
                        .= ' '.adminer()->processInput($m, $X['val']);
                    }if ($bb != '' || (isset($m['privileges']['where']) && (preg_match('~^[-\d.'.(preg_match('~IN$~', $X['op']) ? ',' : '').']+$~', $X['val']) || ! preg_match('~'.number_type().'|bit~', $m['type'])) && (! preg_match("~[\x80-\xFF]~", $X['val']) || preg_match('~char|text|enum|set~', $m['type'])) && (! preg_match('~date|timestamp~', $m['type']) || preg_match('~^\d+-\d+-\d+~', $X['val'])))) {
                        $lb[] = $lg.driver()->convertSearch(idf_escape($E), $X, $m).$kb;
                    }
                }$L[] = (count($lb) == 1 ? $lb[0] : ($lb ? '('.implode(' OR ', $lb).')' : '1 = 0'));
            }
        }

return $L;
    }

    public function selectOrderProcess(array$n, array$x)
    {
        $L = [];
        foreach ((array) $_GET['order'] as $z=>$X) {
            if ($X != '') {
                $L[] = (preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~', $X) ? $X : idf_escape($X)).(isset($_GET['desc'][$z]) ? ' DESC' : '');
            }
        }

return $L;
    }

    public function selectLimitProcess()
    {
        return isset($_GET['limit']) ? intval($_GET['limit']) : 50;
    }

    public function selectLengthProcess()
    {
        return isset($_GET['text_length']) ? "$_GET[text_length]" : '100';
    }

    public function selectEmailProcess(array $Z, array $Uc)
    {
        return false;
    }

    public function selectQueryBuild(array$O, array$Z, array$s, array$yf, $_, $G)
    {
        return '';
    }

    public function messageQuery($J, $Vh, $Gc = false)
    {
        restart_session();
        $sd = &get_session('queries');
        if (! idx($sd, $_GET['db'])) {
            $sd[$_GET['db']] = [];
        }if (strlen($J) > 1e6) {
            $J = preg_replace('~[\x80-\xFF]+$~', '', substr($J, 0, 1e6))."\n…";
        }$sd[$_GET['db']][] = [$J, time(), $Vh];
        $sh = 'sql-'.count($sd[$_GET['db']]);
        $L = "<a href='#$sh' class='toggle'>".lang(55)."</a> <a href='' class='jsonly copy'>🗐</a>\n";
        if (! $Gc && ($Qi = driver()->warnings())) {
            $u = 'warnings-'.count($sd[$_GET['db']]);
            $L = "<a href='#$u' class='toggle'>".lang(37)."</a>, $L<div id='$u' class='hidden'>\n$Qi</div>\n";
        }

return " <span class='time'>".@date('H:i:s').'</span>'." $L<div id='$sh' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($J, 1000).'</code></pre>'.($Vh ? " <span class='time'>($Vh)</span>" : '').(support('sql') ? '<p><a href="'.h(str_replace('db='.urlencode(DB), 'db='.urlencode($_GET['db']), ME).'sql=&history='.(count($sd[$_GET['db']]) - 1)).'">'.lang(12).'</a>' : '').'</div>';
    }

    public function editRowPrint($R, array $n, $M, $yi) {}

    public function editFunctions(array$m)
    {
        $L = ($m['null'] ? 'NULL/' : '');
        $yi = isset($_GET['select']) || where($_GET);
        foreach ([driver()->insertFunctions, driver()->editFunctions] as $z=>$bd) {
            if (! $z || (! isset($_GET['call']) && $yi)) {
                foreach ($bd as $Zf=>$X) {
                    if (! $Zf || preg_match("~$Zf~", $m['type'])) {
                        $L
                        .= "/$X";
                    }
                }
            }if ($z && $bd && ! preg_match('~set|bool~', $m['type']) && ! is_blob($m)) {
                $L
                .= '/SQL';
            }
        }if ($m['auto_increment'] && ! $yi) {
            $L = lang(42);
        }

return explode('/', $L);
    }

    public function editInput($R, array$m, $wa, $Y)
    {
        if ($m['type'] == 'enum') {
            return (isset($_GET['select']) ? "<label><input type='radio'$wa value='orig' checked><i>".lang(10).'</i></label> ' : '').enum_input('radio', $wa, $m, $Y, 'NULL');
        }

return '';
    }

    public function editHint($R, array $m, $Y)
    {
        return '';
    }

    public function processInput(array$m, $Y, $r = '')
    {
        if ($r == 'SQL') {
            return $Y;
        }$E = $m['field'];
        $L = q($Y);
        if (preg_match('~^(now|getdate|uuid)$~', $r)) {
            $L = "$r()";
        } elseif (preg_match('~^current_(date|timestamp)$~', $r)) {
            $L = $r;
        } elseif (preg_match('~^([+-]|\|\|)$~', $r)) {
            $L = idf_escape($E)." $r $L";
        } elseif (preg_match('~^[+-] interval$~', $r)) {
            $L = idf_escape($E)." $r ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i", $Y) && JUSH != 'pgsql' ? $Y : $L);
        } elseif (preg_match('~^(addtime|subtime|concat)$~', $r)) {
            $L = "$r(".idf_escape($E).", $L)";
        } elseif (preg_match('~^(md5|sha1|password|encrypt)$~', $r)) {
            $L = "$r($L)";
        }

return unconvert_field($m, $L);
    }

    public function dumpOutput()
    {
        $L = ['text'=>lang(56), 'file'=>lang(57)];
        if (function_exists('gzencode')) {
            $L['gz'] = 'gzip';
        }

return $L;
    }

    public function dumpFormat()
    {
        return (support('dump') ? ['sql'=>'SQL'] : []) + ['csv'=>'CSV,', 'csv;'=>'CSV;', 'tsv'=>'TSV'];
    }

    public function dumpDatabase($j) {}

    public function dumpTable($R, $Ah, $be = 0)
    {
        if ($_POST['format'] != 'sql') {
            echo "\xef\xbb\xbf";
            if ($Ah) {
                dump_csv(array_keys(fields($R)));
            }
        } else {
            if ($be == 2) {
                $n = [];
                foreach (fields($R) as $E=>$m) {
                    $n[] = idf_escape($E)." $m[full_type]";
                }$h = 'CREATE TABLE '.table($R).' ('.implode(', ', $n).')';
            } else {
                $h = create_sql($R, $_POST['auto_increment'], $Ah);
            }set_utf8mb4($h);
            if ($Ah && $h) {
                if ($Ah == 'DROP+CREATE' || $be == 1) {
                    echo 'DROP '.($be == 2 ? 'VIEW' : 'TABLE').' IF EXISTS '.table($R).";\n";
                }if ($be == 1) {
                    $h = remove_definer($h);
                }echo "$h;\n\n";
            }
        }
    }

    public function dumpData($R, $Ah, $J)
    {
        if ($Ah) {
            $Ee = (JUSH == 'sqlite' ? 0 : 1048576);
            $n = [];
            $_d = false;
            if ($_POST['format'] == 'sql') {
                if ($Ah == 'TRUNCATE+INSERT') {
                    echo truncate_sql($R).";\n";
                }$n = fields($R);
                if (JUSH == 'mssql') {
                    foreach ($n as $m) {
                        if ($m['auto_increment']) {
                            echo 'SET IDENTITY_INSERT '.table($R)." ON;\n";
                            $_d = true;
                            break;
                        }
                    }
                }
            }$K = connection()->query($J, 1);
            if ($K) {
                $Qd = '';
                $La = '';
                $ee = [];
                $cd = [];
                $Ch = '';
                $Jc = ($R != '' ? 'fetch_assoc' : 'fetch_row');
                $tb = 0;
                while ($M = $K->$Jc()) {
                    if (! $ee) {
                        $Ii = [];
                        foreach ($M as $X) {
                            $m = $K->fetch_field();
                            if (idx($n[$m->name], 'generated')) {
                                $cd[$m->name] = true;

                                continue;
                            }$ee[] = $m->name;
                            $z = idf_escape($m->name);
                            $Ii[] = "$z = VALUES($z)";
                        }$Ch = ($Ah == 'INSERT+UPDATE' ? "\nON DUPLICATE KEY UPDATE ".implode(', ', $Ii) : '').";\n";
                    }if ($_POST['format'] != 'sql') {
                        if ($Ah == 'table') {
                            dump_csv($ee);
                            $Ah = 'INSERT';
                        }dump_csv($M);
                    } else {
                        if (! $Qd) {
                            $Qd = 'INSERT INTO '.table($R).' ('.implode(', ', array_map('Adminer\idf_escape', $ee)).') VALUES';
                        }foreach ($M as $z=>$X) {
                            if ($cd[$z]) {
                                unset($M[$z]);

                                continue;
                            }$m = $n[$z];
                            $M[$z] = ($X !== null ? unconvert_field($m, preg_match(number_type(), $m['type']) && ! preg_match('~\[~', $m['full_type']) && is_numeric($X) ? $X : q(($X === false ? 0 : $X))) : 'NULL');
                        }$Rg = ($Ee ? "\n" : ' ').'('.implode(",\t", $M).')';
                        if (! $La) {
                            $La = $Qd.$Rg;
                        } elseif (JUSH == 'mssql' ? $tb % 1000 != 0 : strlen($La) + 4 + strlen($Rg) + strlen($Ch) < $Ee) {
                            $La
                            .= ",$Rg";
                        } else {
                            echo $La.$Ch;
                            $La = $Qd.$Rg;
                        }
                    }$tb++;
                }if ($La) {
                    echo $La.$Ch;
                }
            } elseif ($_POST['format'] == 'sql') {
                echo '-- '.str_replace("\n", ' ', connection()->error)."\n";
            }if ($_d) {
                echo 'SET IDENTITY_INSERT '.table($R)." OFF;\n";
            }
        }
    }

    public function dumpFilename($zd)
    {
        return friendly_url($zd != '' ? $zd : (SERVER ?: 'localhost'));
    }

    public function dumpHeaders($zd, $Ve = false)
    {
        $Jf = $_POST['output'];
        $Bc = (preg_match('~sql~', $_POST['format']) ? 'sql' : ($Ve ? 'tar' : 'csv'));
        header('Content-Type: '.($Jf == 'gz' ? 'application/x-gzip' : ($Bc == 'tar' ? 'application/x-tar' : ($Bc == 'sql' || $Jf != 'file' ? 'text/plain' : 'text/csv').'; charset=utf-8')));
        if ($Jf == 'gz') {
            ob_start(function ($zh) {
                return gzencode($zh);
            }, 1e6);
        }

return $Bc;
    }

    public function dumpFooter()
    {
        if ($_POST['format'] == 'sql') {
            echo '-- '.gmdate('Y-m-d H:i:s e')."\n";
        }
    }

    public function importServerPath()
    {
        return 'adminer.sql';
    }

    public function homepage()
    {
        echo '<p class="links">'.($_GET['ns'] == '' && support('database') ? '<a href="'.h(ME).'database=">'.lang(58)."</a>\n" : ''),(support('scheme') ? "<a href='".h(ME)."scheme='>".($_GET['ns'] != '' ? lang(59) : lang(60))."</a>\n" : ''),($_GET['ns'] !== '' ? '<a href="'.h(ME).'schema=">'.lang(61)."</a>\n" : ''),(support('privileges') ? "<a href='".h(ME)."privileges='>".lang(62)."</a>\n" : '');
        if ($_GET['ns'] !== '') {
            echo (support('routine') ? "<a href='#routines'>".lang(63)."</a>\n" : ''),(support('sequence') ? "<a href='#sequences'>".lang(64)."</a>\n" : ''),(support('type') ? "<a href='#user-types'>".lang(6)."</a>\n" : ''),(support('event') ? "<a href='#events'>".lang(65)."</a>\n" : '');
        }

return true;
    }

    public function navigation($Se)
    {
        echo '<h1>'.adminer()->name()." <span class='version'>".VERSION;
        $df = $_COOKIE['adminer_version'];
        echo " <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION, $df) < 0 ? h($df) : '').'</a>',"</span></h1>\n";
        switch_lang();
        if ($Se == 'auth') {
            $Jf = '';
            foreach ((array) $_SESSION['pwds'] as $Ki=>$fh) {
                foreach ($fh as $P=>$Gi) {
                    $E = h(get_setting("vendor-$Ki-$P") ?: get_driver($Ki));
                    foreach ($Gi as $V=>$H) {
                        if ($H !== null) {
                            $Gb = $_SESSION['db'][$Ki][$P][$V];
                            foreach (($Gb ? array_keys($Gb) : ['']) as $j) {
                                $Jf
                                .= "<li><a href='".h(auth_url($Ki, $P, $V, $j))."'>($E) ".h("$V@".($P != '' ? adminer()->serverName($P) : '').($j != '' ? " - $j" : ''))."</a>\n";
                            }
                        }
                    }
                }
            }if ($Jf) {
                echo "<ul id='logins'>\n$Jf</ul>\n".script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");
            }
        } else {
            $T = [];
            if ($_GET['ns'] !== '' && ! $Se && DB != '') {
                connection()->select_db(DB);
                $T = table_status('', true);
            }adminer()->syntaxHighlighting($T);
            adminer()->databasesPrint($Se);
            $ha = [];
            if (DB == '' || ! $Se) {
                if (support('sql')) {
                    $ha[] = "<a href='".h(ME)."sql='".bold(isset($_GET['sql']) && ! isset($_GET['import'])).'>'.lang(55).'</a>';
                    $ha[] = "<a href='".h(ME)."import='".bold(isset($_GET['import'])).'>'.lang(66).'</a>';
                }$ha[] = "<a href='".h(ME).'dump='.urlencode(isset($_GET['table']) ? $_GET['table'] : $_GET['select'])."' id='dump'".bold(isset($_GET['dump'])).'>'.lang(67).'</a>';
            }$Ed = $_GET['ns'] !== '' && ! $Se && DB != '';
            if ($Ed) {
                $ha[] = '<a href="'.h(ME).'create="'.bold($_GET['create'] === '').'>'.lang(68).'</a>';
            }echo $ha ? "<p class='links'>\n".implode("\n", $ha)."\n" : '';
            if ($Ed) {
                if ($T) {
                    adminer()->tablesPrint($T);
                } else {
                    echo "<p class='message'>".lang(11)."</p>\n";
                }
            }
        }
    }

    public function syntaxHighlighting(array$T)
    {
        echo script_src(preg_replace('~\\?.*~', '', ME).'?file=jush.js&version=5.4.1', true);
        if (support('sql')) {
            echo '<script'.nonce().">\n";
            if ($T) {
                $ve = [];
                foreach ($T as $R=>$U) {
                    $ve[] = preg_quote($R, '/');
                }echo 'var jushLinks = { '.JUSH.':';
                json_row(js_escape(ME).(support('table') ? 'table' : 'select').'=$&', '/\b('.implode('|', $ve).')\b/g', false);
                if (support('routine')) {
                    foreach (routines() as $M) {
                        json_row(js_escape(ME).'function='.urlencode($M['SPECIFIC_NAME']).'&name=$&', '/\b'.preg_quote($M['ROUTINE_NAME'], '/').'(?=["`]?\()/g', false);
                    }
                }json_row('');
                echo "};\n";
                foreach (['bac', 'bra', 'sqlite_quo', 'mssql_bra'] as $X) {
                    echo "jushLinks.$X = jushLinks.".JUSH.";\n";
                }if (isset($_GET['sql']) || isset($_GET['trigger']) || isset($_GET['check'])) {
                    $Lh = array_fill_keys(array_keys($T), []);
                    foreach (driver()->allFields() as $R=>$n) {
                        foreach ($n as $m) {
                            $Lh[$R][] = $m['field'];
                        }
                    }echo "addEventListener('DOMContentLoaded', () => { autocompleter = jush.autocompleteSql('".idf_escape('')."', ".json_encode($Lh)."); });\n";
                }
            }echo "</script>\n";
        }echo script("syntaxHighlighting('".preg_replace('~^(\d\.?\d).*~s', '\1', connection()->server_info)."', '".connection()->flavor."');");
    }

    public function databasesPrint($Se)
    {
        $i = adminer()->databases();
        if (DB && $i && ! in_array(DB, $i)) {
            array_unshift($i, DB);
        }echo "<form action=''>\n<p id='dbs'>\n";
        hidden_fields_get();
        $Eb = script("mixin(qsl('select'), {onmousedown: dbMouseDown, onchange: dbChange});");
        echo "<label title='".lang(28)."'>".lang(69).': '.($i ? html_select('db', [''=>''] + $i, DB).$Eb : "<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n").'</label>',"<input type='submit' value='".lang(22)."'".($i ? " class='hidden'" : '').">\n";
        foreach (['import', 'sql', 'schema', 'dump', 'privileges'] as $X) {
            if (isset($_GET[$X])) {
                echo input_hidden($X);
                break;
            }
        }echo "</p></form>\n";
    }

    public function tablesPrint(array$T)
    {
        echo "<ul id='tables'>".script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");
        foreach ($T as $R=>$wh) {
            $R = "$R";
            $E = adminer()->tableName($wh);
            if ($E != '' && ! $wh['partition']) {
                echo '<li><a href="'.h(ME).'select='.urlencode($R).'"'.bold($_GET['select'] == $R || $_GET['edit'] == $R, 'select')." title='".lang(32)."'>".lang(70).'</a> ',(support('table') || support('indexes') ? '<a href="'.h(ME).'table='.urlencode($R).'"'.bold(in_array($R, [$_GET['table'], $_GET['create'], $_GET['indexes'], $_GET['foreign'], $_GET['trigger'], $_GET['check'], $_GET['view']]), (is_view($wh) ? 'view' : 'structure'))." title='".lang(33)."'>$E</a>" : "<span>$E</span>")."\n";
            }
        }echo "</ul>\n";
    }

    public function processList()
    {
        return process_list();
    }

    public function killProcess($u)
    {
        return kill_process($u);
    }
}class Plugins
{
    private static $append = ['dumpFormat'=>true , 'dumpOutput'=>true, 'editRowPrint'=> true, 'editFunctions'=>true, 'config'=>true];

    public $plugins;

    public $error = '';

    private $hooks = [];

    public function __construct($eg)
    {
        if ($eg === null) {
            $eg = [];
            $Fa = 'adminer-plugins';
            if (is_dir($Fa)) {
                foreach (glob("$Fa/*.php") as $o) {
                    $Fd = include_once "./$o";
                }
            }$rd = " href='https://www.adminer.org/plugins/#use'".target_blank();
            if (file_exists("$Fa.php")) {
                $Fd = include_once "./$Fa.php";
                if (is_array($Fd)) {
                    foreach ($Fd as $dg) {
                        $eg[get_class($dg)] = $dg;
                    }
                } else {
                    $this->error
                    .= lang(71, "<b>$Fa.php</b>", $rd).'<br>';
                }
            }foreach (get_declared_classes() as $Ya) {
                if (! $eg[$Ya] && preg_match('~^Adminer\w~i', $Ya)) {
                    $Eg = new \ReflectionClass($Ya);
                    $nb = $Eg->getConstructor();
                    if ($nb && $nb->getNumberOfRequiredParameters()) {
                        $this->error
                        .= lang(72, $rd, "<b>$Ya</b>", "<b>$Fa.php</b>").'<br>';
                    } else {
                        $eg[$Ya] = new $Ya;
                    }
                }
            }
        }$this->plugins = $eg;
        $ia = new Adminer;
        $eg[] = $ia;
        $Eg = new \ReflectionObject($ia);
        foreach ($Eg->getMethods() as $Qe) {
            foreach ($eg as $dg) {
                $E = $Qe->getName();
                if (method_exists($dg, $E)) {
                    $this->hooks[$E][] = $dg;
                }
            }
        }
    }

    public function __call($E, array $Nf)
    {
        $sa = [];
        foreach ($Nf as $z=>$X) {
            $sa[] = &$Nf[$z];
        }$L = null;
        foreach ($this->hooks[$E] as $dg) {
            $Y = call_user_func_array([$dg, $E], $sa);
            if ($Y !== null) {
                if (! self::$append[$E]) {
                    return $Y;
                } $L = $Y + (array) $L;
            }
        }

return $L;
    }
}abstract class Plugin
{
    protected $translations = [];

    public function description()
    {
        return $this->lang('');
    }

    public function screenshot()
    {
        return '';
    }

    protected function lang($v, $F = null)
    {
        $sa = func_get_args();
        $sa[0] = idx($this->translations[LANG], $v) ?: $v;

        return call_user_func_array('Adminer\lang_format', $sa);
    }
}Adminer::$instance = (function_exists('adminer_object') ? adminer_object() : (is_dir('adminer-plugins') || file_exists('adminer-plugins.php') ? new Plugins(null) : new Adminer));
SqlDriver::$drivers = ['server'=>'MySQL / MariaDB'] + SqlDriver::$drivers;
if (! defined('Adminer\DRIVER')) {
    define('Adminer\DRIVER', 'server');
    if (extension_loaded( 'mysqli') && $_GET[ 'ext'] != 'pdo') {
        class Db extends \MySQLi
        {
            public static $instance;

            public $extension = 'MySQLi';

            public $flavorvar = '';

            public function __construct()
            {
                parent::init();
            }

            public function attach($P, $V, $H)
            {
                mysqli_report(MYSQLI_REPORT_OFF);
                [$vd, $fg] = host_port($P);
                $uh = adminer()->connectSsl();
                if ($uh) {
                    $this->ssl_set($uh['key'], $uh['cert'], $uh['ca'], '', '');
                }$L = @$this->real_connect(($P != '' ? $vd : ini_get('mysqli.default_host')), ($P.$V != '' ? $V : ini_get('mysqli.default_user')), ($P.$V.$H != '' ? $H : ini_get('mysqli.default_pw')), null, (is_numeric($fg) ? intval($fg) : ini_get('mysqli.default_port')), (is_numeric($fg) ? null : $fg), ($uh ? ($uh['verify'] !== false ? 2048 : 64) : 0));
                $this->options(MYSQLI_OPT_LOCAL_INFILE, 0);

                return $L ? '' : $this->error;
            }

            public function set_charset($Ra)
            {
                if (parent::set_charset($Ra)) {
                    return true;
                }parent::set_charset('utf8');

                return $this->query("SET NAMES $Ra");
            }

            public function next_result()
            {
                return self::more_results() && parent::next_result();
            }

            public function quote($zh)
            {
                return "'".$this->escape_string($zh)."'";
            }
        }
    } elseif (extension_loaded('mysql') && ! ((ini_bool('sql.safe_mode') || ini_bool('mysql.allow_local_infile')) && extension_loaded('pdo_mysql'))) {
        class Db extends SqlDb
        {
            private $link;

            public function attach($P, $V, $H)
            {
                if (ini_bool('mysql.allow_local_infile')) {
                    return lang(73, "'mysql.allow_local_infile'", 'MySQLi', 'PDO_MySQL');
                }$this->link = @mysql_connect(($P != '' ? $P : ini_get('mysql.default_host')), ($P.$V != '' ? $V : ini_get('mysql.default_user')), ($P.$V.$H != '' ? $H : ini_get('mysql.default_password')), true, 131072);
                if (! $this->link) {
                    return mysql_error();
                }$this->server_info = mysql_get_server_info($this->link);

                return '';
            }

            public function set_charset($Ra)
            {
                if (function_exists('mysql_set_charset')) {
                    if (mysql_set_charset($Ra, $this->link)) {
                        return true;
                    }mysql_set_charset('utf8', $this->link);
                }

return $this->query("SET NAMES  $Ra");
            }

            public function quote($zh)
            {
                return "'".mysql_real_escape_string($zh, $this->link)."'";
            }

            public function select_db($Db)
            {
                return mysql_select_db($Db, $this->link);
            }

            public function query($J, $ri = false)
            {
                $K = @($ri ? mysql_unbuffered_query($J, $this->link) : mysql_query($J, $this->link));
                $this->error = '';
                if (! $K) {
                    $this->errno = mysql_errno($this->link);
                    $this->error = mysql_error($this->link);

                    return false;
                }if ($K === true) {
                    $this->affected_rows = mysql_affected_rows($this->link);
                    $this->info = mysql_info($this->link) ;

                    return true;
                }

return new Result($K);
            }
        }class Result
        {
            public $num_rows;

            private $result;

            private $offset = 0;

            public function __construct($K)
            {
                $this->result = $K;
                $this->num_rows = mysql_num_rows($K);
            }

            public function fetch_assoc()
            {
                return mysql_fetch_assoc($this->result);
            }

            public function fetch_row()
            {
                return mysql_fetch_row($this->result);
            }

            public function fetch_field()
            {
                $L = mysql_fetch_field($this->result, $this->offset++);
                $L->orgtable = $L->table;
                $L->charsetnr = ($L->blob ? 63 : 0);

                return $L;
            }

            public function __destruct()
            {
                mysql_free_result($this->result);
            }
        }
    } elseif (extension_loaded('pdo_mysql')) {
        class Db extends PdoDb
        {
            public $extension = 'PDO_MySQL';

            public function attach($P, $V, $H)
            {
                $wf = [\PDO::MYSQL_ATTR_LOCAL_INFILE=>false];
                $uh = adminer()->connectSsl();
                if ($uh) {
                    if ($uh['key']) {
                        $wf[\PDO::MYSQL_ATTR_SSL_KEY] = $uh['key'];
                    }if ($uh['cert']) {
                        $wf[\PDO::MYSQL_ATTR_SSL_CERT] = $uh['cert'];
                    }if ($uh['ca']) {
                        $wf[\PDO::MYSQL_ATTR_SSL_CA] = $uh['ca'];
                    }if (isset($uh['verify'])) {
                        $wf[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $uh['verify'];
                    }
                }[$vd, $fg] = host_port($P);

                return $this->dsn("mysql:charset=utf8;host=$vd".($fg ? (is_numeric($fg) ? ';port=' : ';unix_socket=').$fg : ''), $V, $H, $wf);
            }

            public function set_charset($Ra)
            {
                return $this->query("SET NAMES $Ra");
            }

            public function select_db($Db)
            {
                return $this->query('USE '.idf_escape($Db));
            }

            public function query($J, $ri = false)
            {
                $this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, ! $ri);

                return parent:: query($J, $ri);
            }
        }
    }class Driver extends SqlDriver
    {
        public static $extensions = ['MySQLi', 'MySQL', 'PDO_MySQL'] ;

        public static $jush = 'sql';

        public $unsigned = ['unsigned', 'zerofill', 'unsigned zerofill'];

        public $operators = ['=', '<', '>', '<=', '>=', '!=', 'LIKE', 'LIKE %%', 'REGEXP', 'IN', 'FIND_IN_SET', 'IS NULL', 'NOT LIKE', 'NOT REGEXP', 'NOT IN', 'IS NOT NULL', 'SQL'];

        public $functions = ['char_length', 'date', 'from_unixtime', 'lower', 'round', 'floor', 'ceil', 'sec_to_time', 'time_to_sec', 'upper'];

        public $grouping = ['avg', 'count', 'count distinct', 'group_concat', 'max', 'min', 'sum'];

        public static function connect($P, $V, $H)
        {
            $f = parent::connect($P, $V, $H);
            if (is_string($f)) {
                if (function_exists('iconv') && ! is_utf8($f) && strlen($Rg = iconv('windows-1250', 'utf-8', $f)) > strlen($f)) {
                    $f = $Rg;
                }

return $f;
            }$f->set_charset(charset($f));
            $f->query('SET sql_quote_show_create = 1, autocommit = 1');
            $f->flavor = (preg_match('~MariaDB~', $f->server_info) ? 'maria' : 'mysql');
            add_driver(DRIVER, ($f->flavor == 'maria' ? 'MariaDB' : 'MySQL'));

            return $f;
        }

        public function __construct(Db$f)
        {
            parent::__construct($f);
            $this->types = [lang(74)=>['tinyint'=>3, 'smallint'=>5, 'mediumint'=>8, 'int'=>10, 'bigint'=>20, 'decimal'=>66, 'float'=>12, 'double'=>21], lang(75)=>['date'=>10, 'datetime'=>19, 'timestamp'=>19, 'time'=>10, 'year'=>4], lang(76)=>['char'=>255, 'varchar'=>65535, 'tinytext'=>255, 'text'=>65535, 'mediumtext'=>16777215, 'longtext'=>4294967295], lang(77)=>['enum'=>65535, 'set'=>64], lang(78)=>['bit'=>20, 'binary'=>255, 'varbinary'=>65535, 'tinyblob'=>255, 'blob'=>65535, 'mediumblob'=>16777215, 'longblob'=>4294967295], lang(79)=>['geometry'=>0, 'point'=>0, 'linestring'=>0, 'polygon'=>0, 'multipoint'=>0, 'multilinestring'=>0, 'multipolygon'=>0, 'geometrycollection'=>0]];
            $this->insertFunctions = ['char'=>'md5/sha1/password/encrypt/uuid', 'binary'=>'md5/sha1', 'date|time'=>'now'];
            $this->editFunctions = [number_type()=>'+/-', 'date'=>'+ interval/- interval', 'time'=>'addtime/subtime', 'char|text'=>'concat'];
            if (min_version('5.7.8', 10.2, $f)) {
                $this->types[lang(76)]['json'] = 4294967295;
            }if (min_version('', 10.7, $f)) {
                $this->types[lang(76)]['uuid'] = 128;
                $this->insertFunctions['uuid'] = 'uuid';
            }if (min_version(9, '', $f)) {
                $this->types[lang(74)]['vector'] = 16383;
                $this->insertFunctions['vector'] = 'string_to_vector';
            }if (min_version(5.1, '', $f)) {
                $this->partitionBy = ['HASH', 'LINEAR HASH', 'KEY', 'LINEAR KEY', 'RANGE', 'LIST'];
            }if (min_version(5.7, 10.2, $f)) {
                $this->generated = ['STORED', 'VIRTUAL'];
            }
        }

        public function unconvertFunction(array $m)
        {
            return preg_match('~binary~', $m['type']) ? "<code class='jush-sql'>UNHEX</code>" : ($m['type'] == 'bit' ? doc_link(['sql'=>'bit-value-literals.html'], "<code>b''</code>") : (preg_match('~geometry|point|linestring|polygon~', $m['type']) ? "<code class='jush-sql'>GeomFromText</code>" : ''));
        }

        public function insert($R, array$Q)
        {
            return $Q ? parent::insert($R, $Q) : queries('INSERT INTO '.table($R)." ()\nVALUES ()");
        }

        public function insertUpdate($R, array$N, array$ng)
        {
            $d = array_keys(reset($N));
            $lg = 'INSERT INTO '.table($R).' ('.implode(', ', $d).") VALUES\n";
            $Ii = [];
            foreach ($d as $z) {
                $Ii[$z] = "$z = VALUES($z)";
            }$Ch = "\nON DUPLICATE KEY UPDATE ".implode(', ', $Ii);
            $Ii = [];
            $re = 0;
            foreach ($N as $Q) {
                $Y = '('.implode(', ', $Q).')';
                if ($Ii && (strlen($lg) + $re + strlen($Y) + strlen($Ch) > 1e6)) {
                    if (! queries($lg.implode(",\n", $Ii).$Ch)) {
                        return false;
                    }$Ii = [];
                    $re = 0;
                }$Ii[] = $Y;
                $re += strlen($Y) + 2;
            }

return queries($lg.implode(",\n", $Ii).$Ch);
        }

        public function slowQuery($J, $Wh)
        {
            if (min_version('5.7.8', '10.1.2')) {
                if ($this->conn->flavor == 'maria') {
                    return "SET STATEMENT max_statement_time=$Wh FOR $J";
                } elseif (preg_match('~^(SELECT\b)(.+)~is', $J, $C)) {
                    return "$C[1] /*+ MAX_EXECUTION_TIME(".($Wh * 1000).") */ $C[2]";
                }
            }
        }

        public function convertSearch($v, array$X, array$m)
        {
            return preg_match('~char|text|enum|set~', $m['type']) && ! preg_match('~^utf8~', $m['collation']) && preg_match('~[\x80-\xFF]~', $X['val']) ? "CONVERT($v USING ".charset($this->conn).')' : $v;
        }

        public function warnings()
        {
            $K = $this->conn->query('SHOW WARNINGS');
            if ($K && $K->num_rows) {
                ob_start();
                print_select_result($K);

                return ob_get_clean();
            }
        }

        public function tableHelp($E, $be = false)
        {
            $ye = ($this->conn->flavor == 'maria');
            if (information_schema(DB)) {
                return strtolower('information-schema-'.($ye ? "$E-table/" : str_replace('_', '-', $E).'-table.html'));
            }if (DB == 'mysql') {
                return $ye ? "mysql$E-table/" : 'system-schema.html';
            }
        }

        public function partitionsInfo($R)
        {
            $Zc = 'FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = '.q(DB).' AND TABLE_NAME = '.q($R);
            $K = $this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $Zc ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");
            $L = [];
            [$L['partition_by'], $L['partition'], $L['partitions']] = $K->fetch_row();
            $Vf = get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $Zc AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");
            $L['partition_names'] = array_keys($Vf);
            $L['partition_values'] = array_values($Vf);

            return $L;
        }

        public function hasCStyleEscapes()
        {
            static $Ma;
            if ($Ma === null) {
                $th = get_val("SHOW VARIABLES LIKE 'sql_mode'", 1, $this->conn);
                $Ma = (strpos( $th, 'NO_BACKSLASH_ESCAPES') === false);
            }

return $Ma;
        }

        public function engines()
        {
            $L = [];
            foreach (get_rows('SHOW ENGINES') as $M) {
                if (preg_match('~YES|DEFAULT~', $M['Support'])) {
                    $L[] = $M['Engine'];
                }
            }

return $L;
        }

        public function indexAlgorithms(array $Gh)
        {
            return preg_match('~^(MEMORY|NDB)$~', $Gh['Engine']) ? ['HASH', 'BTREE'] : [];
        }
    }function idf_escape($v)
    {
        return '`'.str_replace('`', '``', $v).'`';
    }function table($v)
    {
        return idf_escape($v);
    }function get_databases($Rc)
    {
        $L = get_session('dbs');
        if ($L === null) {
            $J = 'SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME';
            $L = ($Rc ? slow_query($J) : get_vals($J));
            restart_session();
            set_session('dbs', $L);
            stop_session();
        }

return $L;
    }function limit($J, $Z, $_, $jf = 0, $dh = ' ')
    {
        return " $J$Z".($_ ? $dh."LIMIT $_".($jf ? " OFFSET $jf" : '') : '');
    }function limit1($R, $J, $Z, $dh = "\n")
    {
        return limit($J, $Z, 1, 0, $dh);
    }function db_collation($j, array $b)
    {
        $L = null;
        $h = get_val('SHOW CREATE DATABASE '.idf_escape($j), 1);
        if (preg_match('~ COLLATE ([^ ]+)~', $h, $C)) {
            $L = $C[1];
        } elseif (preg_match('~ CHARACTER SET ([^ ]+)~', $h, $C)) {
            $L = $b[$C[1]][-1];
        }

return $L;
    }function logged_user()
    {
        return get_val('SELECT USER()');
    }function tables_list()
    {
        return get_key_vals('SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME');
    }function count_tables(array $i)
    {
        $L = [];
        foreach ($i as $j) {
            $L[$j] = count(get_vals('SHOW TABLES IN '.idf_escape($j)));
        }

return $L;
    }function table_status($E = '', $Hc = false)
    {
        $L = [];
        foreach (get_rows($Hc ? 'SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() '.($E != '' ? 'AND TABLE_NAME = '.q($E) : 'ORDER BY Name') : 'SHOW TABLE STATUS'.($E != '' ? ' LIKE '.q(addcslashes($E, '%_\\')) : '')) as $M) {
            if ($M['Engine'] == 'InnoDB') {
                $M['Comment'] = preg_replace('~(?:(.+); )?InnoDB free: .*~', '\1', $M['Comment']);
            }if (! isset($M['Engine'])) {
                $M['Comment'] = '';
            }if ($E != '') {
                $M['Name'] = $E;
            }$L[$M['Name']] = $M;
        }

return $L;
    }function is_view(array $S)
    {
        return $S['Engine'] === null;
    }function fk_support(array $S)
    {
        return preg_match('~InnoDB|IBMDB2I'.(min_version(5.6) ? '|NDB' : '').'~i', $S['Engine']);
    }function fields($R)
    {
        $ye = (connection()->flavor == 'maria');
        $L = [];
        foreach (get_rows('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '.q($R).' ORDER BY ORDINAL_POSITION') as $M) {
            $m = $M['COLUMN_NAME'];
            $U = $M['COLUMN_TYPE'];
            $dd = $M['GENERATION_EXPRESSION'];
            $Ec = $M['EXTRA'];
            preg_match('~^(VIRTUAL|PERSISTENT|STORED)~', $Ec, $cd);
            preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~', $U, $_e);
            $k = $M['COLUMN_DEFAULT'];
            if ($k != '') {
                $ae = preg_match('~text|json~', $_e[1]);
                if (! $ye && $ae) {
                    $k = preg_replace("~^(_\w+)?('.*')$~", '\2', stripslashes($k));
                }if ($ye || $ae) {
                    $k = ($k == 'NULL' ? null : preg_replace_callback("~^'(.*)'$~", function ($C) {
                        return stripslashes(str_replace("''", "'", $C[1]));
                    }, $k));
                }if (! $ye && preg_match('~binary~', $_e[1]) && preg_match('~^0x(\w*)$~', $k, $C)) {
                    $k = pack('H*', $C[1]);
                }
            }$L[$m] = ['field'=>$m, 'full_type'=>$U, 'type'=>$_e[1], 'length'=>$_e[2], 'unsigned'=>ltrim($_e[3].$_e[4]), 'default'=>($cd ? ($ye ? $dd : stripslashes($dd)) : $k), 'null'=>($M['IS_NULLABLE'] == 'YES'), 'auto_increment'=>($Ec == 'auto_increment'), 'on_update'=>(preg_match('~\bon update (\w+)~i', $Ec, $C) ? $C[1] : ''), 'collation'=>$M['COLLATION_NAME'], 'privileges'=>array_flip(explode(',', "$M[PRIVILEGES],where,order")), 'comment'=>$M['COLUMN_COMMENT'], 'primary'=>($M['COLUMN_KEY'] == 'PRI'), 'generated'=>($cd[1] == 'PERSISTENT' ? 'STORED' : $cd[1])];
        }

return $L;
    }function indexes($R, $g = null)
    {
        $L = [];
        foreach (get_rows('SHOW INDEX FROM '.table($R), $g) as $M) {
            $E = $M['Key_name'];
            $L[$E]['type'] = ($E == 'PRIMARY' ? 'PRIMARY' : ($M['Index_type'] == 'FULLTEXT' ? 'FULLTEXT' : ($M['Non_unique'] ? ($M['Index_type'] == 'SPATIAL' ? 'SPATIAL' : 'INDEX') : 'UNIQUE')));
            $L[$E]['columns'][] = $M['Column_name'];
            $L[$E]['lengths'][] = ($M['Index_type'] == 'SPATIAL' ? null : $M['Sub_part']);
            $L[$E]['descs'][] = null;
            $L[$E]['algorithm'] = $M['Index_type'];
        }

return $L;
    }function foreign_keys($R)
    {
        static $Zf = '(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';
        $L = [];
        $ub = get_val('SHOW CREATE TABLE '.table($R), 1);
        if ($ub) {
            preg_match_all("~CONSTRAINT ($Zf) FOREIGN KEY ?\\(((?:$Zf,? ?)+)\\) REFERENCES ($Zf)(?:\\.($Zf))? \\(((?:$Zf,? ?)+)\\)(?: ON DELETE (".driver()->onActions.'))?(?: ON UPDATE ('.driver()->onActions.'))?~', $ub, $Ae, PREG_SET_ORDER);
            foreach ($Ae as $C) {
                preg_match_all("~$Zf~", $C[2], $oh);
                preg_match_all("~$Zf~", $C[5], $Ph);
                $L[idf_unescape($C[1])] = ['db'=>idf_unescape($C[4] != '' ? $C[3] : $C[4]), 'table'=>idf_unescape($C[4] != '' ? $C[4] : $C[3]), 'source'=>array_map('Adminer\idf_unescape', $oh[0]), 'target'=>array_map('Adminer\idf_unescape', $Ph[0]), 'on_delete'=>($C[6] ?: 'RESTRICT'), 'on_update'=>($C[7] ?: 'RESTRICT')];
            }
        }

return $L;
    }function view($E)
    {
        return ['select'=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU', '', get_val('SHOW CREATE VIEW '.table($E), 1))];
    }function collations()
    {
        $L = [];
        foreach (get_rows('SHOW COLLATION') as $M) {
            if ($M['Default']) {
                $L[$M['Charset']][-1] = $M['Collation'];
            } else {
                $L[$M['Charset']][] = $M['Collation'];
            }
        }ksort($L);
        foreach ($L as $z=>$X) {
            sort($L[$z]);
        }

return $L;
    }function information_schema($j)
    {
        return ($j == 'information_schema') || (min_version(5.5) && $j == 'performance_schema');
    }function error()
    {
        return h(preg_replace('~^You have an error.*syntax to use~U', 'Syntax error', connection()->error));
    }function create_database($j, $db)
    {
        return queries('CREATE DATABASE '.idf_escape($j).($db ? ' COLLATE '.q($db) : ''));
    }function drop_databases(array $i)
    {
        $L = apply_queries('DROP DATABASE', $i, 'Adminer\idf_escape');
        restart_session();
        set_session('dbs', null);

        return $L;
    }function rename_database($E, $db)
    {
        $L = false;
        if (create_database($E, $db)) {
            $T = [];
            $Ni = [];
            foreach (tables_list() as $R=>$U) {
                if ($U == 'VIEW') {
                    $Ni[] = $R;
                } else {
                    $T[] = $R;
                }
            }$L = (! $T && ! $Ni) || move_tables($T, $Ni, $E);
            drop_databases($L ? [DB] : []);
        }

return $L;
    }function auto_increment()
    {
        $za = ' PRIMARY KEY';
        if ($_GET['create'] != '' && $_POST['auto_increment_col']) {
            foreach (indexes($_GET['create']) as $w) {
                if (in_array($_POST['fields'][$_POST['auto_increment_col']]['orig'], $w['columns'], true)) {
                    $za = '';
                    break;
                }if ($w['type'] == 'PRIMARY') {
                    $za = ' UNIQUE';
                }
            }
        }

return " AUTO_INCREMENT$za";
    }function alter_table($R, $E, array $n, array $Tc, $hb, $lc, $db, $ya, $Uf)
    {
        $qa = [];
        foreach ($n as $m) {
            if ($m[1]) {
                $k = $m[1][3];
                if (preg_match('~ GENERATED~', $k)) {
                    $m[1][3] = (connection()->flavor == 'maria' ? '' : $m[1][2]);
                    $m[1][2] = $k;
                }$qa[] = ($R != '' ? ($m[0] != '' ? 'CHANGE '.idf_escape($m[0]) : 'ADD') : ' ').' '.implode($m[1]).($R != '' ? $m[2] : '');
            } else {
                $qa[] = 'DROP '.idf_escape($m[0]);
            }
        }$qa = array_merge($qa, $Tc);
        $wh = ($hb !== null ? ' COMMENT='.q($hb) : '').($lc ? ' ENGINE='.q($lc) : '').($db ? ' COLLATE '.q($db) : '').($ya != '' ? " AUTO_INCREMENT=$ya" : '');
        if ($Uf) {
            $Vf = [];
            if ($Uf['partition_by'] == 'RANGE' || $Uf['partition_by'] == 'LIST') {
                foreach ($Uf['partition_names'] as $z=>$X) {
                    $Y = $Uf['partition_values'][$z];
                    $Vf[] = "\n  PARTITION ".idf_escape($X).' VALUES '.($Uf['partition_by'] == 'RANGE' ? 'LESS THAN' : 'IN').($Y != '' ? " ($Y)" : ' MAXVALUE');
                }
            }$wh
            .= "\nPARTITION BY $Uf[partition_by]($Uf[partition])";
            if ($Vf) {
                $wh
                .= ' ('.implode(',', $Vf)."\n)";
            } elseif ($Uf['partitions']) {
                $wh
                .= ' PARTITIONS '.(+$Uf['partitions']);
            }
        } elseif ($Uf === null) {
            $wh
            .= "\nREMOVE PARTITIONING";
        }if ($R == '') {
            return queries('CREATE TABLE '.table($E)." (\n".implode(",\n", $qa)."\n)$wh");
        }if ($R != $E) {
            $qa[] = 'RENAME TO '.table($E);
        }if ($wh) {
            $qa[] = ltrim($wh);
        }

return $qa ? queries('ALTER TABLE '.table($R)."\n".implode(",\n", $qa)) : true;
    }function alter_indexes($R, $qa)
    {
        $Qa = [];
        foreach ($qa as $X) {
            $Qa[] = ($X[2] == 'DROP' ? "\nDROP INDEX ".idf_escape($X[1]) : "\nADD $X[0] ".($X[0] == 'PRIMARY' ? 'KEY ' : '').($X[1] != '' ? idf_escape($X[1]).' ' : '').'('.implode(', ', $X[2]).')');
        }

return queries('ALTER TABLE '.table($R).implode(',', $Qa));
    }function truncate_tables(array $T)
    {
        return apply_queries('TRUNCATE TABLE', $T);
    }function drop_views(array $Ni)
    {
        return queries('DROP VIEW '.implode(', ', array_map('Adminer\table', $Ni)));
    }function drop_tables(array $T)
    {
        return queries('DROP TABLE '.implode(', ', array_map('Adminer\table', $T)));
    }function move_tables(array $T, array $Ni, $Ph)
    {
        $Hg = [];
        foreach ($T as $R) {
            $Hg[] = table($R).' TO '.idf_escape($Ph).'.'.table($R);
        }if (! $Hg || queries('RENAME TABLE '.implode(', ', $Hg))) {
            $Mb = [];
            foreach ($Ni as $R) {
                $Mb[table($R)] = view($R);
            }connection()->select_db($Ph);
            $j = idf_escape(DB);
            foreach ($Mb as $E=>$Mi) {
                if (! queries("CREATE VIEW $E AS ".str_replace(" $j.", ' ', $Mi['select'])) || ! queries("DROP VIEW $j.$E")) {
                    return false;
                }
            }

return true;
        }

return false;
    }function copy_tables(array $T, array $Ni, $Ph)
    {
        queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");
        foreach ($T as $R) {
            $E = ($Ph == DB ? table("copy_$R") : idf_escape($Ph).'.'.table($R));
            if (($_POST['overwrite'] && ! queries("\nDROP TABLE IF EXISTS $E")) || ! queries("CREATE TABLE $E LIKE ".table($R)) || ! queries("INSERT INTO $E SELECT * FROM ".table($R))) {
                return false;
            }foreach (get_rows('SHOW TRIGGERS LIKE '.q(addcslashes($R, '%_\\'))) as $M) {
                $li = $M['Trigger'];
                if (! queries('CREATE TRIGGER '.($Ph == DB ? idf_escape("copy_$li") : idf_escape($Ph).'.'.idf_escape($li))." $M[Timing] $M[Event] ON $E FOR EACH ROW\n$M[Statement];")) {
                    return false;
                }
            }
        }foreach ($Ni as $R) {
            $E = ($Ph == DB ? table("copy_$R") : idf_escape($Ph).'.'.table($R));
            $Mi = view($R);
            if (($_POST['overwrite'] && ! queries("DROP VIEW IF EXISTS $E")) || ! queries("CREATE VIEW $E AS $Mi[select]")) {
                return false;
            }
        }

return true;
    }function trigger($E, $R)
    {
        if ($E == '') {
            return [];
        }$N = get_rows('SHOW TRIGGERS WHERE `Trigger` = '.q($E));

        return reset($N);
    }function triggers($R)
    {
        $L = [];
        foreach (get_rows('SHOW TRIGGERS LIKE '.q(addcslashes($R, '%_\\'))) as $M) {
            $L[$M['Trigger']] = [$M['Timing'], $M['Event']];
        }

return $L;
    }function trigger_options()
    {
        return ['Timing'=>['BEFORE', 'AFTER'], 'Event'=>['INSERT', 'UPDATE', 'DELETE'], 'Type'=>['FOR EACH ROW']];
    }function routine($E, $U)
    {
        $oa = ['bool', 'boolean', 'integer', 'double precision', 'real', 'dec', 'numeric', 'fixed', 'national char', 'national varchar'];
        $ph = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
        $nc = driver()->enumLength;
        $pi = '(('.implode('|', array_merge(array_keys(driver()->types()), $oa)).")\\b(?:\\s*\\(((?:[^'\")]|$nc)++)\\))?"."\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s,]+)['\"]?)?(?:\\s*COLLATE\\s*['\"]?[^'\"\\s,]+['\"]?)?";
        $Zf = "$ph*(".($U == 'FUNCTION' ? '' : driver()->inout).")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$pi";
        $h = get_val("SHOW CREATE $U ".idf_escape($E), 2);
        preg_match("~\\(((?:$Zf\\s*,?)*)\\)\\s*".($U == 'FUNCTION' ? "RETURNS\\s+$pi\\s+" : '').'(.*)~is', $h, $C);
        $n = [];
        preg_match_all("~$Zf\\s*,?~is", $C[1], $Ae, PREG_SET_ORDER);
        foreach ($Ae as $Mf) {
            $n[] = ['field'=>str_replace('``', '`', $Mf[2]).$Mf[3], 'type'=>strtolower($Mf[5]), 'length'=>preg_replace_callback("~$nc~s", 'Adminer\normalize_enum', $Mf[6]), 'unsigned'=>strtolower(preg_replace('~\s+~', ' ', trim("$Mf[8] $Mf[7]"))), 'null'=>true, 'full_type'=>$Mf[4], 'inout'=>strtoupper($Mf[1]), 'collation'=>strtolower($Mf[9])];
        }

return ['fields'=>$n, 'comment'=>get_val('SELECT ROUTINE_COMMENT FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = '.q($E))] + ($U != 'FUNCTION' ? ['definition'=>$C[11]] : ['returns'=>['type'=>$C[12], 'length'=>$C[13], 'unsigned'=>$C[15], 'collation'=>$C[16]], 'definition'=>$C[17], 'language'=>'SQL']);
    }function routines()
    {
        return get_rows('SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()');
    }function routine_languages()
    {
        return [];
    }function routine_id($E, array $M)
    {
        return idf_escape($E);
    }function last_id($K)
    {
        return get_val('SELECT LAST_INSERT_ID()');
    }function explain(Db $f, $J)
    {
        return $f->query('EXPLAIN '.(min_version(5.1) && ! min_version(5.7) ? 'PARTITIONS ' : '').$J);
    }function found_rows(array $S, array $Z)
    {
        return $Z || $S['Engine'] != 'InnoDB' ? null : $S['Rows'];
    }function create_sql($R, $ya, $Ah)
    {
        $L = get_val('SHOW CREATE TABLE '.table($R), 1);
        if (! $ya) {
            $L = preg_replace('~ AUTO_INCREMENT=\d+~', '', $L);
        }

return $L;
    }function truncate_sql($R)
    {
        return 'TRUNCATE '.table($R);
    }function use_sql($Db, $Ah = '')
    {
        $E = idf_escape($Db);
        $L = '';
        if (preg_match('~CREATE~', $Ah) && ($h = get_val("SHOW CREATE DATABASE $E", 1))) {
            set_utf8mb4($h);
            if ($Ah == 'DROP+CREATE') {
                $L = "DROP DATABASE IF EXISTS $E;\n";
            }$L
            .= "$h;\n";
        }

return $L."USE $E";
    }function trigger_sql($R)
    {
        $L = '';
        foreach (get_rows('SHOW TRIGGERS LIKE '.q(addcslashes($R, '%_\\')), null, '-- ') as $M) {
            $L
            .= "\nCREATE TRIGGER ".idf_escape($M['Trigger'])." $M[Timing] $M[Event] ON ".table($M['Table'])." FOR EACH ROW\n$M[Statement];;\n";
        }

return $L;
    }function show_variables()
    {
        return get_rows('SHOW VARIABLES');
    }function show_status()
    {
        return get_rows('SHOW STATUS');
    }function process_list()
    {
        return get_rows('SHOW FULL PROCESSLIST');
    }function convert_field(array $m)
    {
        if (preg_match('~binary~', $m['type'])) {
            return 'HEX('.idf_escape($m['field']).')';
        }if ($m['type'] == 'bit') {
            return 'BIN('.idf_escape($m['field']).' + 0)';
        }if (preg_match('~geometry|point|linestring|polygon~', $m['type'])) {
            return (min_version(8) ? 'ST_' : '').'AsWKT('.idf_escape($m['field']).')';
        }
    }function unconvert_field(array $m, $L)
    {
        if (preg_match('~binary~', $m['type'])) {
            $L = "UNHEX($L)";
        }if ($m['type'] == 'bit') {
            $L = "CONVERT(b$L, UNSIGNED)";
        }if (preg_match('~geometry|point|linestring|polygon~', $m['type'])) {
            $lg = (min_version(8) ? 'ST_' : '');
            $L = $lg."GeomFromText($L, $lg"."SRID($m[field]))";
        }

return $L;
    }function support($Ic)
    {
        return preg_match('~^(comment|columns|copy|database|drop_col|dump|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(5.1) ? '|event' : '').(min_version(8) ? '|descidx' : '').(min_version('8.0.16', '10.2.1') ? '|check' : '').')$~', $Ic);
    }function kill_process($u)
    {
        return queries('KILL '.number($u));
    }function connection_id()
    {
        return 'SELECT CONNECTION_ID()';
    }function max_connections()
    {
        return get_val('SELECT @@max_connections');
    }function types()
    {
        return [];
    }function type_values($u)
    {
        return '';
    }function schemas()
    {
        return [];
    }function get_schema()
    {
        return '';
    }function set_schema($Tg, $g = null)
    {
        return true;
    }
}define('Adminer\JUSH', Driver::$jush);
define('Adminer\SERVER', ''.$_GET[DRIVER]);
define('Adminer\DB', "$_GET[db]");
define('Adminer\ME', preg_replace('~\?.*~', '', relative_uri()).'?'.(sid() ? SID.'&' : '').(SERVER !== null ? DRIVER.'='.urlencode(SERVER).'&' : '').($_GET['ext'] ? 'ext='.urlencode($_GET['ext']).'&' : '').(isset($_GET['username']) ? 'username='.urlencode($_GET['username']).'&' : '').(DB != '' ? 'db='.urlencode(DB).'&'.(isset($_GET['ns']) ? 'ns='.urlencode($_GET['ns']).'&' : '') : ''));
function page_header($Yh, $l = '', $Ka = [], $Zh = '')
{
    page_headers();
    if (is_ajax() && $l) {
        page_messages($l);
        exit;
    }if (! ob_get_level()) {
        ob_start('ob_gzhandler', 4096);
    }$ai = $Yh.($Zh != '' ? ": $Zh" : '');
    $bi = strip_tags($ai.(SERVER != '' && SERVER != 'localhost' ? h(' - '.SERVER) : '').' - '.adminer()->name());
    echo '<!DOCTYPE html>
=======
WHERE TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY TABLE_NAME, ORDINAL_POSITION",$this->conn)as$M){$M["null"]=($M["nullable"]=="YES");$L[$M["tab"]][]=$M;}}return$L;}}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=5.4.1")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($h=false){return
password_file($h);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
serverName($P){return
h($P);}function
database(){return
DB;}function
databases($Rc=true){return
get_databases($Rc);}function
pluginsLinks(){}function
operators(){return
driver()->operators;}function
schemas(){return
schemas();}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$xb){return$xb;}function
head($Ab=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$L=array();foreach(array("","-dark")as$Te){$o="adminer$Te.css";if(file_exists($o)){$Mc=file_get_contents($o);$L["$o?v=".crc32($Mc)]=($Te?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$Mc)?'':'light'));}}return$L;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.lang(24).'<td>',input_hidden("auth[driver]","server")."MySQL / MariaDB"),adminer()->loginFormField('server','<tr><th>'.lang(25).'<td>','<input name="auth[server]" value="'.h(SERVER).'" title="hostname[:port]" placeholder="localhost" autocapitalize="off">'),adminer()->loginFormField('username','<tr><th>'.lang(26).'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(27).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.lang(28).'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(29)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(30))."\n";}function
loginFormField($E,$qd,$Y){return$qd.$Y."\n";}function
login($we,$H){if($H=="")return
lang(31,target_blank());return
true;}function
tableName(array$Gh){return
h($Gh["Name"]);}function
fieldName(array$m,$yf=0){$U=$m["full_type"];$hb=$m["comment"];return'<span title="'.h($U.($hb!=""?($U?": ":"").$hb:'')).'">'.h($m["field"]).'</span>';}function
selectLinks(array$Gh,$Q=""){$E=$Gh["Name"];echo'<p class="links">';$ve=array("select"=>lang(32));if(support("table")||support("indexes"))$ve["table"]=lang(33);$be=false;if(support("table")){$be=is_view($Gh);if(!$be)$ve["create"]=lang(34);elseif(support("view"))$ve["view"]=lang(35);}if($Q!==null)$ve["edit"]=lang(36);foreach($ve
as$z=>$X)echo" <a href='".h(ME)."$z=".urlencode($E).($z=="edit"?$Q:"")."'".bold(isset($_GET[$z])).">$X</a>";echo
doc_link(array(JUSH=>driver()->tableHelp($E,$be)),"?"),"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$Fh){return
array();}function
backwardKeysPrint(array$Ba,array$M){}function
selectQuery($J,$vh,$Gc=false){$L="</p>\n";if(!$Gc&&($Qi=driver()->warnings())){$u="warnings";$L=", <a href='#$u'>".lang(37)."</a>".script("qsl('a').onclick = partial(toggle, '$u');","")."$L<div id='$u' class='hidden'>\n$Qi</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$J))."</code> <span class='time'>(".format_time($vh).")</span>".(support("sql")?" <a href='".h(ME)."sql=".urlencode($J)."'>".lang(12)."</a>":"").$L;}function
sqlCommandQuery($J){return
shorten_utf8(trim($J),1000);}function
sqlPrintAfter(){}function
rowDescription($R){return"";}function
rowDescriptions(array$N,array$Uc){return$N;}function
selectLink($X,array$m){}function
selectVal($X,$A,array$m,$Hf){$L=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$m["type"])&&!preg_match("~var~",$m["type"])?"<code>$X</code>":(preg_match('~json~',$m["type"])?"<code class='jush-js'>$X</code>":$X)));if(is_blob($m)&&!is_utf8($X))$L="<i>".lang(38,strlen($Hf))."</i>";return($A?"<a href='".h($A)."'".(is_url($A)?target_blank():"").">$L</a>":$L);}function
editVal($X,array$m){return$X;}function
config(){return
array();}function
tableStructurePrint(array$n,$Gh=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".lang(39)."<td>".lang(40).(support("comment")?"<td>".lang(41):"")."</thead>\n";$_h=driver()->structuredTypes();foreach($n
as$m){echo"<tr><th>".h($m["field"]);$U=h($m["full_type"]);$db=h($m["collation"]);echo"<td><span title='$db'>".(in_array($U,(array)$_h[lang(6)])?"<a href='".h(ME.'type='.urlencode($U))."'>$U</a>":$U.($db&&isset($Gh["Collation"])&&$db!=$Gh["Collation"]?" $db":""))."</span>",($m["null"]?" <i>NULL</i>":""),($m["auto_increment"]?" <i>".lang(42)."</i>":"");$k=h($m["default"]);echo(isset($m["default"])?" <span title='".lang(43)."'>[<b>".($m["generated"]?"<code class='jush-".JUSH."'>$k</code>":$k)."</b>]</span>":""),(support("comment")?"<td>".h($m["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$x,array$Gh){$Pf=false;foreach($x
as$E=>$w)$Pf|=!!$w["partial"];echo"<table>\n";$Ib=first(driver()->indexAlgorithms($Gh));foreach($x
as$E=>$w){ksort($w["columns"]);$og=array();foreach($w["columns"]as$z=>$X)$og[]="<i>".h($X)."</i>".($w["lengths"][$z]?"(".$w["lengths"][$z].")":"").($w["descs"][$z]?" DESC":"");echo"<tr title='".h($E)."'>","<th>$w[type]".($Ib&&$w['algorithm']!=$Ib?" ($w[algorithm])":""),"<td>".implode(", ",$og);if($Pf)echo"<td>".($w['partial']?"<code class='jush-".JUSH."'>WHERE ".h($w['partial']):"");echo"\n";}echo"</table>\n";}function
selectColumnsPrint(array$O,array$d){print_fieldset("select",lang(44),$O);$t=0;$O[""]=array();foreach($O
as$z=>$X){$X=idx($_GET["columns"],$z,array());$c=select_input(" name='columns[$t][col]'",$d,$X["col"],($z!==""?"selectFieldChange":"selectAddRow"));echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$t][fun]",array(-1=>"")+array_filter(array(lang(45)=>driver()->functions,lang(46)=>driver()->grouping)),$X["fun"]).on_help("event.target.value && event.target.value.replace(/ |\$/, '(') + ')'",1).script("qsl('select').onchange = function () { helpClose();".($z!==""?"":" qsl('select, input', this.parentNode).onchange();")." };","")."($c)":$c)."</div>\n";$t++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$d,array$x){print_fieldset("search",lang(47),$Z);foreach($x
as$t=>$w){if($w["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$w["columns"]))."</i>) AGAINST"," <input type='search' name='fulltext[$t]' value='".h(idx($_GET["fulltext"],$t))."'>",script("qsl('input').oninput = selectFieldChange;",""),checkbox("boolean[$t]",1,isset($_GET["boolean"][$t]),"BOOL"),"</div>\n";}$Pa="this.parentNode.firstChild.onchange();";foreach(array_merge((array)$_GET["where"],array(array()))as$t=>$X){if(!$X||("$X[col]$X[val]"!=""&&in_array($X["op"],adminer()->operators())))echo"<div>".select_input(" name='where[$t][col]'",$d,$X["col"],($X?"selectFieldChange":"selectAddRow"),"(".lang(48).")"),html_select("where[$t][op]",adminer()->operators(),$X["op"],$Pa),"<input type='search' name='where[$t][val]' value='".h($X["val"])."'>",script("mixin(qsl('input'), {oninput: function () { $Pa }, onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});",""),"</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$yf,array$d,array$x){print_fieldset("sort",lang(49),$yf);$t=0;foreach((array)$_GET["order"]as$z=>$X){if($X!=""){echo"<div>".select_input(" name='order[$t]'",$d,$X,"selectFieldChange"),checkbox("desc[$t]",1,isset($_GET["desc"][$z]),lang(50))."</div>\n";$t++;}}echo"<div>".select_input(" name='order[$t]'",$d,"","selectAddRow"),checkbox("desc[$t]",1,false,lang(50))."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($_){echo"<fieldset><legend>".lang(51)."</legend><div>","<input type='number' name='limit' class='size' value='".intval($_)."'>",script("qsl('input').oninput = selectFieldChange;",""),"</div></fieldset>\n";}function
selectLengthPrint($Uh){if($Uh!==null)echo"<fieldset><legend>".lang(52)."</legend><div>","<input type='number' name='text_length' class='size' value='".h($Uh)."'>","</div></fieldset>\n";}function
selectActionPrint(array$x){echo"<fieldset><legend>".lang(53)."</legend><div>","<input type='submit' value='".lang(44)."'>"," <span id='noindex' title='".lang(54)."'></span>","<script".nonce().">\n","const indexColumns = ";$d=array();foreach($x
as$w){$_b=reset($w["columns"]);if($w["type"]!="FULLTEXT"&&$_b)$d[$_b]=1;}$d[""]=1;foreach($d
as$z=>$X)json_row($z);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$ic,array$d){}function
selectColumnsProcess(array$d,array$x){$O=array();$s=array();foreach((array)$_GET["columns"]as$z=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$O[$z]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$s[]=$O[$z];}}return
array($O,$s);}function
selectSearchProcess(array$n,array$x){$L=array();foreach($x
as$t=>$w){if($w["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$t)!="")$L[]="MATCH (".implode(", ",array_map('Adminer\idf_escape',$w["columns"])).") AGAINST (".q($_GET["fulltext"][$t]).(isset($_GET["boolean"][$t])?" IN BOOLEAN MODE":"").")";}foreach((array)$_GET["where"]as$z=>$X){$bb=$X["col"];if("$bb$X[val]"!=""&&in_array($X["op"],adminer()->operators())){$lb=array();foreach(($bb!=""?array($bb=>$n[$bb]):$n)as$E=>$m){$lg="";$kb=" $X[op]";if(preg_match('~IN$~',$X["op"])){$Dd=process_length($X["val"]);$kb
.=" ".($Dd!=""?$Dd:"(NULL)");}elseif($X["op"]=="SQL")$kb=" $X[val]";elseif(preg_match('~^(I?LIKE) %%$~',$X["op"],$C))$kb=" $C[1] ".adminer()->processInput($m,"%$X[val]%");elseif($X["op"]=="FIND_IN_SET"){$lg="$X[op](".q($X["val"]).", ";$kb=")";}elseif(!preg_match('~NULL$~',$X["op"]))$kb
.=" ".adminer()->processInput($m,$X["val"]);if($bb!=""||(isset($m["privileges"]["where"])&&(preg_match('~^[-\d.'.(preg_match('~IN$~',$X["op"])?',':'').']+$~',$X["val"])||!preg_match('~'.number_type().'|bit~',$m["type"]))&&(!preg_match("~[\x80-\xFF]~",$X["val"])||preg_match('~char|text|enum|set~',$m["type"]))&&(!preg_match('~date|timestamp~',$m["type"])||preg_match('~^\d+-\d+-\d+~',$X["val"]))))$lb[]=$lg.driver()->convertSearch(idf_escape($E),$X,$m).$kb;}$L[]=(count($lb)==1?$lb[0]:($lb?"(".implode(" OR ",$lb).")":"1 = 0"));}}return$L;}function
selectOrderProcess(array$n,array$x){$L=array();foreach((array)$_GET["order"]as$z=>$X){if($X!="")$L[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$z])?" DESC":"");}return$L;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$Uc){return
false;}function
selectQueryBuild(array$O,array$Z,array$s,array$yf,$_,$G){return"";}function
messageQuery($J,$Vh,$Gc=false){restart_session();$sd=&get_session("queries");if(!idx($sd,$_GET["db"]))$sd[$_GET["db"]]=array();if(strlen($J)>1e6)$J=preg_replace('~[\x80-\xFF]+$~','',substr($J,0,1e6))."\n…";$sd[$_GET["db"]][]=array($J,time(),$Vh);$sh="sql-".count($sd[$_GET["db"]]);$L="<a href='#$sh' class='toggle'>".lang(55)."</a> <a href='' class='jsonly copy'>🗐</a>\n";if(!$Gc&&($Qi=driver()->warnings())){$u="warnings-".count($sd[$_GET["db"]]);$L="<a href='#$u' class='toggle'>".lang(37)."</a>, $L<div id='$u' class='hidden'>\n$Qi</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $L<div id='$sh' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($J,1000)."</code></pre>".($Vh?" <span class='time'>($Vh)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".urlencode(DB),"db=".urlencode($_GET["db"]),ME).'sql=&history='.(count($sd[$_GET["db"]])-1)).'">'.lang(12).'</a>':'').'</div>';}function
editRowPrint($R,array$n,$M,$yi){}function
editFunctions(array$m){$L=($m["null"]?"NULL/":"");$yi=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$z=>$bd){if(!$z||(!isset($_GET["call"])&&$yi)){foreach($bd
as$Zf=>$X){if(!$Zf||preg_match("~$Zf~",$m["type"]))$L
.="/$X";}}if($z&&$bd&&!preg_match('~set|bool~',$m["type"])&&!is_blob($m))$L
.="/SQL";}if($m["auto_increment"]&&!$yi)$L=lang(42);return
explode("/",$L);}function
editInput($R,array$m,$wa,$Y){if($m["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$wa value='orig' checked><i>".lang(10)."</i></label> ":"").enum_input("radio",$wa,$m,$Y,"NULL");return"";}function
editHint($R,array$m,$Y){return"";}function
processInput(array$m,$Y,$r=""){if($r=="SQL")return$Y;$E=$m["field"];$L=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$r))$L="$r()";elseif(preg_match('~^current_(date|timestamp)$~',$r))$L=$r;elseif(preg_match('~^([+-]|\|\|)$~',$r))$L=idf_escape($E)." $r $L";elseif(preg_match('~^[+-] interval$~',$r))$L=idf_escape($E)." $r ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$L);elseif(preg_match('~^(addtime|subtime|concat)$~',$r))$L="$r(".idf_escape($E).", $L)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$r))$L="$r($L)";return
unconvert_field($m,$L);}function
dumpOutput(){$L=array('text'=>lang(56),'file'=>lang(57));if(function_exists('gzencode'))$L['gz']='gzip';return$L;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($j){}function
dumpTable($R,$Ah,$be=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Ah)dump_csv(array_keys(fields($R)));}else{if($be==2){$n=array();foreach(fields($R)as$E=>$m)$n[]=idf_escape($E)." $m[full_type]";$h="CREATE TABLE ".table($R)." (".implode(", ",$n).")";}else$h=create_sql($R,$_POST["auto_increment"],$Ah);set_utf8mb4($h);if($Ah&&$h){if($Ah=="DROP+CREATE"||$be==1)echo"DROP ".($be==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($be==1)$h=remove_definer($h);echo"$h;\n\n";}}}function
dumpData($R,$Ah,$J){if($Ah){$Ee=(JUSH=="sqlite"?0:1048576);$n=array();$_d=false;if($_POST["format"]=="sql"){if($Ah=="TRUNCATE+INSERT")echo
truncate_sql($R).";\n";$n=fields($R);if(JUSH=="mssql"){foreach($n
as$m){if($m["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$_d=true;break;}}}}$K=connection()->query($J,1);if($K){$Qd="";$La="";$ee=array();$cd=array();$Ch="";$Jc=($R!=''?'fetch_assoc':'fetch_row');$tb=0;while($M=$K->$Jc()){if(!$ee){$Ii=array();foreach($M
as$X){$m=$K->fetch_field();if(idx($n[$m->name],'generated')){$cd[$m->name]=true;continue;}$ee[]=$m->name;$z=idf_escape($m->name);$Ii[]="$z = VALUES($z)";}$Ch=($Ah=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$Ii):"").";\n";}if($_POST["format"]!="sql"){if($Ah=="table"){dump_csv($ee);$Ah="INSERT";}dump_csv($M);}else{if(!$Qd)$Qd="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$ee)).") VALUES";foreach($M
as$z=>$X){if($cd[$z]){unset($M[$z]);continue;}$m=$n[$z];$M[$z]=($X!==null?unconvert_field($m,preg_match(number_type(),$m["type"])&&!preg_match('~\[~',$m["full_type"])&&is_numeric($X)?$X:q(($X===false?0:$X))):"NULL");}$Rg=($Ee?"\n":" ")."(".implode(",\t",$M).")";if(!$La)$La=$Qd.$Rg;elseif(JUSH=='mssql'?$tb%1000!=0:strlen($La)+4+strlen($Rg)+strlen($Ch)<$Ee)$La
.=",$Rg";else{echo$La.$Ch;$La=$Qd.$Rg;}}$tb++;}if($La)echo$La.$Ch;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";if($_d)echo"SET IDENTITY_INSERT ".table($R)." OFF;\n";}}function
dumpFilename($zd){return
friendly_url($zd!=""?$zd:(SERVER?:"localhost"));}function
dumpHeaders($zd,$Ve=false){$Jf=$_POST["output"];$Bc=(preg_match('~sql~',$_POST["format"])?"sql":($Ve?"tar":"csv"));header("Content-Type: ".($Jf=="gz"?"application/x-gzip":($Bc=="tar"?"application/x-tar":($Bc=="sql"||$Jf!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($Jf=="gz"){ob_start(function($zh){return
gzencode($zh);},1e6);}return$Bc;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.lang(58)."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?lang(59):lang(60))."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.lang(61)."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".lang(62)."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".lang(63)."</a>\n":""),(support("sequence")?"<a href='#sequences'>".lang(64)."</a>\n":""),(support("type")?"<a href='#user-types'>".lang(6)."</a>\n":""),(support("event")?"<a href='#events'>".lang(65)."</a>\n":"");return
true;}function
navigation($Se){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$df=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$df)<0?h($df):"")."</a>","</span></h1>\n";switch_lang();if($Se=="auth"){$Jf="";foreach((array)$_SESSION["pwds"]as$Ki=>$fh){foreach($fh
as$P=>$Gi){$E=h(get_setting("vendor-$Ki-$P")?:get_driver($Ki));foreach($Gi
as$V=>$H){if($H!==null){$Gb=$_SESSION["db"][$Ki][$P][$V];foreach(($Gb?array_keys($Gb):array(""))as$j)$Jf
.="<li><a href='".h(auth_url($Ki,$P,$V,$j))."'>($E) ".h("$V@".($P!=""?adminer()->serverName($P):"").($j!=""?" - $j":""))."</a>\n";}}}}if($Jf)echo"<ul id='logins'>\n$Jf</ul>\n".script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");}else{$T=array();if($_GET["ns"]!==""&&!$Se&&DB!=""){connection()->select_db(DB);$T=table_status('',true);}adminer()->syntaxHighlighting($T);adminer()->databasesPrint($Se);$ha=array();if(DB==""||!$Se){if(support("sql")){$ha[]="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".lang(55)."</a>";$ha[]="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".lang(66)."</a>";}$ha[]="<a href='".h(ME)."dump=".urlencode(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".lang(67)."</a>";}$Ed=$_GET["ns"]!==""&&!$Se&&DB!="";if($Ed)$ha[]='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".lang(68)."</a>";echo($ha?"<p class='links'>\n".implode("\n",$ha)."\n":"");if($Ed){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".lang(11)."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=5.4.1",true);if(support("sql")){echo"<script".nonce().">\n";if($T){$ve=array();foreach($T
as$R=>$U)$ve[]=preg_quote($R,'/');echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b('.implode('|',$ve).')\b/g',false);if(support('routine')){foreach(routines()as$M)json_row(js_escape(ME).'function='.urlencode($M["SPECIFIC_NAME"]).'&name=$&','/\b'.preg_quote($M["ROUTINE_NAME"],'/').'(?=["`]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(isset($_GET["sql"])||isset($_GET["trigger"])||isset($_GET["check"])){$Lh=array_fill_keys(array_keys($T),array());foreach(driver()->allFields()as$R=>$n){foreach($n
as$m)$Lh[$R][]=$m["field"];}echo"addEventListener('DOMContentLoaded', () => { autocompleter = jush.autocompleteSql('".idf_escape("")."', ".json_encode($Lh)."); });\n";}}echo"</script>\n";}echo
script("syntaxHighlighting('".preg_replace('~^(\d\.?\d).*~s','\1',connection()->server_info)."', '".connection()->flavor."');");}function
databasesPrint($Se){$i=adminer()->databases();if(DB&&$i&&!in_array(DB,$i))array_unshift($i,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$Eb=script("mixin(qsl('select'), {onmousedown: dbMouseDown, onchange: dbChange});");echo"<label title='".lang(28)."'>".lang(69).": ".($i?html_select("db",array(""=>"")+$i,DB).$Eb:"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".lang(22)."'".($i?" class='hidden'":"").">\n";foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
tablesPrint(array$T){echo"<ul id='tables'>".script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");foreach($T
as$R=>$wh){$R="$R";$E=adminer()->tableName($wh);if($E!=""&&!$wh["partition"])echo'<li><a href="'.h(ME).'select='.urlencode($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select")." title='".lang(32)."'>".lang(70)."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.urlencode($R).'"'.bold(in_array($R,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($wh)?"view":"structure"))." title='".lang(33)."'>$E</a>":"<span>$E</span>")."\n";}echo"</ul>\n";}function
processList(){return
process_list();}function
killProcess($u){return
kill_process($u);}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$error='';private$hooks=array();function
__construct($eg){if($eg===null){$eg=array();$Fa="adminer-plugins";if(is_dir($Fa)){foreach(glob("$Fa/*.php")as$o)$Fd=include_once"./$o";}$rd=" href='https://www.adminer.org/plugins/#use'".target_blank();if(file_exists("$Fa.php")){$Fd=include_once"./$Fa.php";if(is_array($Fd)){foreach($Fd
as$dg)$eg[get_class($dg)]=$dg;}else$this->error
.=lang(71,"<b>$Fa.php</b>",$rd)."<br>";}foreach(get_declared_classes()as$Ya){if(!$eg[$Ya]&&preg_match('~^Adminer\w~i',$Ya)){$Eg=new
\ReflectionClass($Ya);$nb=$Eg->getConstructor();if($nb&&$nb->getNumberOfRequiredParameters())$this->error
.=lang(72,$rd,"<b>$Ya</b>","<b>$Fa.php</b>")."<br>";else$eg[$Ya]=new$Ya;}}}$this->plugins=$eg;$ia=new
Adminer;$eg[]=$ia;$Eg=new
\ReflectionObject($ia);foreach($Eg->getMethods()as$Qe){foreach($eg
as$dg){$E=$Qe->getName();if(method_exists($dg,$E))$this->hooks[$E][]=$dg;}}}function
__call($E,array$Nf){$sa=array();foreach($Nf
as$z=>$X)$sa[]=&$Nf[$z];$L=null;foreach($this->hooks[$E]as$dg){$Y=call_user_func_array(array($dg,$E),$sa);if($Y!==null){if(!self::$append[$E])return$Y;$L=$Y+(array)$L;}}return$L;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($v,$F=null){$sa=func_get_args();$sa[0]=idx($this->translations[LANG],$v)?:$v;return
call_user_func_array('Adminer\lang_format',$sa);}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\MySQLi{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach($P,$V,$H){mysqli_report(MYSQLI_REPORT_OFF);list($vd,$fg)=host_port($P);$uh=adminer()->connectSsl();if($uh)$this->ssl_set($uh['key'],$uh['cert'],$uh['ca'],'','');$L=@$this->real_connect(($P!=""?$vd:ini_get("mysqli.default_host")),($P.$V!=""?$V:ini_get("mysqli.default_user")),($P.$V.$H!=""?$H:ini_get("mysqli.default_pw")),null,(is_numeric($fg)?intval($fg):ini_get("mysqli.default_port")),(is_numeric($fg)?null:$fg),($uh?($uh['verify']!==false?2048:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($L?'':$this->error);}function
set_charset($Ra){if(parent::set_charset($Ra))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $Ra");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($zh){return"'".$this->escape_string($zh)."'";}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach($P,$V,$H){if(ini_bool("mysql.allow_local_infile"))return
lang(73,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$this->link=@mysql_connect(($P!=""?$P:ini_get("mysql.default_host")),($P.$V!=""?$V:ini_get("mysql.default_user")),($P.$V.$H!=""?$H:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($Ra){if(function_exists('mysql_set_charset')){if(mysql_set_charset($Ra,$this->link))return
true;mysql_set_charset('utf8',$this->link);}return$this->query("SET NAMES $Ra");}function
quote($zh){return"'".mysql_real_escape_string($zh,$this->link)."'";}function
select_db($Db){return
mysql_select_db($Db,$this->link);}function
query($J,$ri=false){$K=@($ri?mysql_unbuffered_query($J,$this->link):mysql_query($J,$this->link));$this->error="";if(!$K){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($K===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($K);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($K){$this->result=$K;$this->num_rows=mysql_num_rows($K);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$L=mysql_fetch_field($this->result,$this->offset++);$L->orgtable=$L->table;$L->charsetnr=($L->blob?63:0);return$L;}function
__destruct(){mysql_free_result($this->result);}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach($P,$V,$H){$wf=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);$uh=adminer()->connectSsl();if($uh){if($uh['key'])$wf[\PDO::MYSQL_ATTR_SSL_KEY]=$uh['key'];if($uh['cert'])$wf[\PDO::MYSQL_ATTR_SSL_CERT]=$uh['cert'];if($uh['ca'])$wf[\PDO::MYSQL_ATTR_SSL_CA]=$uh['ca'];if(isset($uh['verify']))$wf[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$uh['verify'];}list($vd,$fg)=host_port($P);return$this->dsn("mysql:charset=utf8;host=$vd".($fg?(is_numeric($fg)?";port=":";unix_socket=").$fg:""),$V,$H,$wf);}function
set_charset($Ra){return$this->query("SET NAMES $Ra");}function
select_db($Db){return$this->query("USE ".idf_escape($Db));}function
query($J,$ri=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$ri);return
parent::query($J,$ri);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");static
function
connect($P,$V,$H){$f=parent::connect($P,$V,$H);if(is_string($f)){if(function_exists('iconv')&&!is_utf8($f)&&strlen($Rg=iconv("windows-1250","utf-8",$f))>strlen($f))$f=$Rg;return$f;}$f->set_charset(charset($f));$f->query("SET sql_quote_show_create = 1, autocommit = 1");$f->flavor=(preg_match('~MariaDB~',$f->server_info)?'maria':'mysql');add_driver(DRIVER,($f->flavor=='maria'?"MariaDB":"MySQL"));return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(74)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(75)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(76)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(77)=>array("enum"=>65535,"set"=>64),lang(78)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(79)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$f))$this->types[lang(76)]["json"]=4294967295;if(min_version('',10.7,$f)){$this->types[lang(76)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version(9,'',$f)){$this->types[lang(74)]["vector"]=16383;$this->insertFunctions['vector']='string_to_vector';}if(min_version(5.1,'',$f))$this->partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");if(min_version(5.7,10.2,$f))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$m){return(preg_match("~binary~",$m["type"])?"<code class='jush-sql'>UNHEX</code>":($m["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):(preg_match("~geometry|point|linestring|polygon~",$m["type"])?"<code class='jush-sql'>GeomFromText</code>":"")));}function
insert($R,array$Q){return($Q?parent::insert($R,$Q):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$N,array$ng){$d=array_keys(reset($N));$lg="INSERT INTO ".table($R)." (".implode(", ",$d).") VALUES\n";$Ii=array();foreach($d
as$z)$Ii[$z]="$z = VALUES($z)";$Ch="\nON DUPLICATE KEY UPDATE ".implode(", ",$Ii);$Ii=array();$re=0;foreach($N
as$Q){$Y="(".implode(", ",$Q).")";if($Ii&&(strlen($lg)+$re+strlen($Y)+strlen($Ch)>1e6)){if(!queries($lg.implode(",\n",$Ii).$Ch))return
false;$Ii=array();$re=0;}$Ii[]=$Y;$re+=strlen($Y)+2;}return
queries($lg.implode(",\n",$Ii).$Ch);}function
slowQuery($J,$Wh){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$Wh FOR $J";elseif(preg_match('~^(SELECT\b)(.+)~is',$J,$C))return"$C[1] /*+ MAX_EXECUTION_TIME(".($Wh*1000).") */ $C[2]";}}function
convertSearch($v,array$X,array$m){return(preg_match('~char|text|enum|set~',$m["type"])&&!preg_match("~^utf8~",$m["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($v USING ".charset($this->conn).")":$v);}function
warnings(){$K=$this->conn->query("SHOW WARNINGS");if($K&&$K->num_rows){ob_start();print_select_result($K);return
ob_get_clean();}}function
tableHelp($E,$be=false){$ye=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower("information-schema-".($ye?"$E-table/":str_replace("_","-",$E)."-table.html"));if(DB=="mysql")return($ye?"mysql$E-table/":"system-schema.html");}function
partitionsInfo($R){$Zc="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$K=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $Zc ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$L=array();list($L["partition_by"],$L["partition"],$L["partitions"])=$K->fetch_row();$Vf=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $Zc AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$L["partition_names"]=array_keys($Vf);$L["partition_values"]=array_values($Vf);return$L;}function
hasCStyleEscapes(){static$Ma;if($Ma===null){$th=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Ma=(strpos($th,'NO_BACKSLASH_ESCAPES')===false);}return$Ma;}function
engines(){$L=array();foreach(get_rows("SHOW ENGINES")as$M){if(preg_match("~YES|DEFAULT~",$M["Support"]))$L[]=$M["Engine"];}return$L;}function
indexAlgorithms(array$Gh){return(preg_match('~^(MEMORY|NDB)$~',$Gh["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($v){return"`".str_replace("`","``",$v)."`";}function
table($v){return
idf_escape($v);}function
get_databases($Rc){$L=get_session("dbs");if($L===null){$J="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$L=($Rc?slow_query($J):get_vals($J));restart_session();set_session("dbs",$L);stop_session();}return$L;}function
limit($J,$Z,$_,$jf=0,$dh=" "){return" $J$Z".($_?$dh."LIMIT $_".($jf?" OFFSET $jf":""):"");}function
limit1($R,$J,$Z,$dh="\n"){return
limit($J,$Z,1,0,$dh);}function
db_collation($j,array$b){$L=null;$h=get_val("SHOW CREATE DATABASE ".idf_escape($j),1);if(preg_match('~ COLLATE ([^ ]+)~',$h,$C))$L=$C[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$h,$C))$L=$b[$C[1]][-1];return$L;}function
logged_user(){return
get_val("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$i){$L=array();foreach($i
as$j)$L[$j]=count(get_vals("SHOW TABLES IN ".idf_escape($j)));return$L;}function
table_status($E="",$Hc=false){$L=array();foreach(get_rows($Hc?"SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($E!=""?"AND TABLE_NAME = ".q($E):"ORDER BY Name"):"SHOW TABLE STATUS".($E!=""?" LIKE ".q(addcslashes($E,"%_\\")):""))as$M){if($M["Engine"]=="InnoDB")$M["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$M["Comment"]);if(!isset($M["Engine"]))$M["Comment"]="";if($E!="")$M["Name"]=$E;$L[$M["Name"]]=$M;}return$L;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
fields($R){$ye=(connection()->flavor=='maria');$L=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$M){$m=$M["COLUMN_NAME"];$U=$M["COLUMN_TYPE"];$dd=$M["GENERATION_EXPRESSION"];$Ec=$M["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Ec,$cd);preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$U,$_e);$k=$M["COLUMN_DEFAULT"];if($k!=""){$ae=preg_match('~text|json~',$_e[1]);if(!$ye&&$ae)$k=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($k));if($ye||$ae){$k=($k=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($C){return
stripslashes(str_replace("''","'",$C[1]));},$k));}if(!$ye&&preg_match('~binary~',$_e[1])&&preg_match('~^0x(\w*)$~',$k,$C))$k=pack("H*",$C[1]);}$L[$m]=array("field"=>$m,"full_type"=>$U,"type"=>$_e[1],"length"=>$_e[2],"unsigned"=>ltrim($_e[3].$_e[4]),"default"=>($cd?($ye?$dd:stripslashes($dd)):$k),"null"=>($M["IS_NULLABLE"]=="YES"),"auto_increment"=>($Ec=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$Ec,$C)?$C[1]:""),"collation"=>$M["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$M[PRIVILEGES],where,order")),"comment"=>$M["COLUMN_COMMENT"],"primary"=>($M["COLUMN_KEY"]=="PRI"),"generated"=>($cd[1]=="PERSISTENT"?"STORED":$cd[1]),);}return$L;}function
indexes($R,$g=null){$L=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$g)as$M){$E=$M["Key_name"];$L[$E]["type"]=($E=="PRIMARY"?"PRIMARY":($M["Index_type"]=="FULLTEXT"?"FULLTEXT":($M["Non_unique"]?($M["Index_type"]=="SPATIAL"?"SPATIAL":"INDEX"):"UNIQUE")));$L[$E]["columns"][]=$M["Column_name"];$L[$E]["lengths"][]=($M["Index_type"]=="SPATIAL"?null:$M["Sub_part"]);$L[$E]["descs"][]=null;$L[$E]["algorithm"]=$M["Index_type"];}return$L;}function
foreign_keys($R){static$Zf='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$L=array();$ub=get_val("SHOW CREATE TABLE ".table($R),1);if($ub){preg_match_all("~CONSTRAINT ($Zf) FOREIGN KEY ?\\(((?:$Zf,? ?)+)\\) REFERENCES ($Zf)(?:\\.($Zf))? \\(((?:$Zf,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$ub,$Ae,PREG_SET_ORDER);foreach($Ae
as$C){preg_match_all("~$Zf~",$C[2],$oh);preg_match_all("~$Zf~",$C[5],$Ph);$L[idf_unescape($C[1])]=array("db"=>idf_unescape($C[4]!=""?$C[3]:$C[4]),"table"=>idf_unescape($C[4]!=""?$C[4]:$C[3]),"source"=>array_map('Adminer\idf_unescape',$oh[0]),"target"=>array_map('Adminer\idf_unescape',$Ph[0]),"on_delete"=>($C[6]?:"RESTRICT"),"on_update"=>($C[7]?:"RESTRICT"),);}}return$L;}function
view($E){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($E),1)));}function
collations(){$L=array();foreach(get_rows("SHOW COLLATION")as$M){if($M["Default"])$L[$M["Charset"]][-1]=$M["Collation"];else$L[$M["Charset"]][]=$M["Collation"];}ksort($L);foreach($L
as$z=>$X)sort($L[$z]);return$L;}function
information_schema($j){return($j=="information_schema")||(min_version(5.5)&&$j=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($j,$db){return
queries("CREATE DATABASE ".idf_escape($j).($db?" COLLATE ".q($db):""));}function
drop_databases(array$i){$L=apply_queries("DROP DATABASE",$i,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$L;}function
rename_database($E,$db){$L=false;if(create_database($E,$db)){$T=array();$Ni=array();foreach(tables_list()as$R=>$U){if($U=='VIEW')$Ni[]=$R;else$T[]=$R;}$L=(!$T&&!$Ni)||move_tables($T,$Ni,$E);drop_databases($L?array(DB):array());}return$L;}function
auto_increment(){$za=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$w){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$w["columns"],true)){$za="";break;}if($w["type"]=="PRIMARY")$za=" UNIQUE";}}return" AUTO_INCREMENT$za";}function
alter_table($R,$E,array$n,array$Tc,$hb,$lc,$db,$ya,$Uf){$qa=array();foreach($n
as$m){if($m[1]){$k=$m[1][3];if(preg_match('~ GENERATED~',$k)){$m[1][3]=(connection()->flavor=='maria'?"":$m[1][2]);$m[1][2]=$k;}$qa[]=($R!=""?($m[0]!=""?"CHANGE ".idf_escape($m[0]):"ADD"):" ")." ".implode($m[1]).($R!=""?$m[2]:"");}else$qa[]="DROP ".idf_escape($m[0]);}$qa=array_merge($qa,$Tc);$wh=($hb!==null?" COMMENT=".q($hb):"").($lc?" ENGINE=".q($lc):"").($db?" COLLATE ".q($db):"").($ya!=""?" AUTO_INCREMENT=$ya":"");if($Uf){$Vf=array();if($Uf["partition_by"]=='RANGE'||$Uf["partition_by"]=='LIST'){foreach($Uf["partition_names"]as$z=>$X){$Y=$Uf["partition_values"][$z];$Vf[]="\n  PARTITION ".idf_escape($X)." VALUES ".($Uf["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$wh
.="\nPARTITION BY $Uf[partition_by]($Uf[partition])";if($Vf)$wh
.=" (".implode(",",$Vf)."\n)";elseif($Uf["partitions"])$wh
.=" PARTITIONS ".(+$Uf["partitions"]);}elseif($Uf===null)$wh
.="\nREMOVE PARTITIONING";if($R=="")return
queries("CREATE TABLE ".table($E)." (\n".implode(",\n",$qa)."\n)$wh");if($R!=$E)$qa[]="RENAME TO ".table($E);if($wh)$qa[]=ltrim($wh);return($qa?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$qa)):true);}function
alter_indexes($R,$qa){$Qa=array();foreach($qa
as$X)$Qa[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$Qa));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$Ni){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Ni)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$Ni,$Ph){$Hg=array();foreach($T
as$R)$Hg[]=table($R)." TO ".idf_escape($Ph).".".table($R);if(!$Hg||queries("RENAME TABLE ".implode(", ",$Hg))){$Mb=array();foreach($Ni
as$R)$Mb[table($R)]=view($R);connection()->select_db($Ph);$j=idf_escape(DB);foreach($Mb
as$E=>$Mi){if(!queries("CREATE VIEW $E AS ".str_replace(" $j."," ",$Mi["select"]))||!queries("DROP VIEW $j.$E"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$Ni,$Ph){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$E=($Ph==DB?table("copy_$R"):idf_escape($Ph).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $E"))||!queries("CREATE TABLE $E LIKE ".table($R))||!queries("INSERT INTO $E SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$M){$li=$M["Trigger"];if(!queries("CREATE TRIGGER ".($Ph==DB?idf_escape("copy_$li"):idf_escape($Ph).".".idf_escape($li))." $M[Timing] $M[Event] ON $E FOR EACH ROW\n$M[Statement];"))return
false;}}foreach($Ni
as$R){$E=($Ph==DB?table("copy_$R"):idf_escape($Ph).".".table($R));$Mi=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $E"))||!queries("CREATE VIEW $E AS $Mi[select]"))return
false;}return
true;}function
trigger($E,$R){if($E=="")return
array();$N=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($E));return
reset($N);}function
triggers($R){$L=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$M)$L[$M["Trigger"]]=array($M["Timing"],$M["Event"]);return$L;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
routine($E,$U){$oa=array("bool","boolean","integer","double precision","real","dec","numeric","fixed","national char","national varchar");$ph="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";$nc=driver()->enumLength;$pi="((".implode("|",array_merge(array_keys(driver()->types()),$oa)).")\\b(?:\\s*\\(((?:[^'\")]|$nc)++)\\))?"."\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s,]+)['\"]?)?(?:\\s*COLLATE\\s*['\"]?[^'\"\\s,]+['\"]?)?";$Zf="$ph*(".($U=="FUNCTION"?"":driver()->inout).")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$pi";$h=get_val("SHOW CREATE $U ".idf_escape($E),2);preg_match("~\\(((?:$Zf\\s*,?)*)\\)\\s*".($U=="FUNCTION"?"RETURNS\\s+$pi\\s+":"")."(.*)~is",$h,$C);$n=array();preg_match_all("~$Zf\\s*,?~is",$C[1],$Ae,PREG_SET_ORDER);foreach($Ae
as$Mf)$n[]=array("field"=>str_replace("``","`",$Mf[2]).$Mf[3],"type"=>strtolower($Mf[5]),"length"=>preg_replace_callback("~$nc~s",'Adminer\normalize_enum',$Mf[6]),"unsigned"=>strtolower(preg_replace('~\s+~',' ',trim("$Mf[8] $Mf[7]"))),"null"=>true,"full_type"=>$Mf[4],"inout"=>strtoupper($Mf[1]),"collation"=>strtolower($Mf[9]),);return
array("fields"=>$n,"comment"=>get_val("SELECT ROUTINE_COMMENT FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = ".q($E)),)+($U!="FUNCTION"?array("definition"=>$C[11]):array("returns"=>array("type"=>$C[12],"length"=>$C[13],"unsigned"=>$C[15],"collation"=>$C[16]),"definition"=>$C[17],"language"=>"SQL",));}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return
array();}function
routine_id($E,array$M){return
idf_escape($E);}function
last_id($K){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$f,$J){return$f->query("EXPLAIN ".(min_version(5.1)&&!min_version(5.7)?"PARTITIONS ":"").$J);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$ya,$Ah){$L=get_val("SHOW CREATE TABLE ".table($R),1);if(!$ya)$L=preg_replace('~ AUTO_INCREMENT=\d+~','',$L);return$L;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($Db,$Ah=""){$E=idf_escape($Db);$L="";if(preg_match('~CREATE~',$Ah)&&($h=get_val("SHOW CREATE DATABASE $E",1))){set_utf8mb4($h);if($Ah=="DROP+CREATE")$L="DROP DATABASE IF EXISTS $E;\n";$L
.="$h;\n";}return$L."USE $E";}function
trigger_sql($R){$L="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")),null,"-- ")as$M)$L
.="\nCREATE TRIGGER ".idf_escape($M["Trigger"])." $M[Timing] $M[Event] ON ".table($M["Table"])." FOR EACH ROW\n$M[Statement];;\n";return$L;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$m){if(preg_match("~binary~",$m["type"]))return"HEX(".idf_escape($m["field"]).")";if($m["type"]=="bit")return"BIN(".idf_escape($m["field"])." + 0)";if(preg_match("~geometry|point|linestring|polygon~",$m["type"]))return(min_version(8)?"ST_":"")."AsWKT(".idf_escape($m["field"]).")";}function
unconvert_field(array$m,$L){if(preg_match("~binary~",$m["type"]))$L="UNHEX($L)";if($m["type"]=="bit")$L="CONVERT(b$L, UNSIGNED)";if(preg_match("~geometry|point|linestring|polygon~",$m["type"])){$lg=(min_version(8)?"ST_":"");$L=$lg."GeomFromText($L, $lg"."SRID($m[field]))";}return$L;}function
support($Ic){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(5.1)?'|event':'').(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').')$~',$Ic);}function
kill_process($u){return
queries("KILL ".number($u));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types(){return
array();}function
type_values($u){return"";}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($Tg,$g=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').(SERVER!==null?DRIVER."=".urlencode(SERVER).'&':'').($_GET["ext"]?"ext=".urlencode($_GET["ext"]).'&':'').(isset($_GET["username"])?"username=".urlencode($_GET["username"]).'&':'').(DB!=""?'db='.urlencode(DB).'&'.(isset($_GET["ns"])?"ns=".urlencode($_GET["ns"])."&":""):''));function
page_header($Yh,$l="",$Ka=array(),$Zh=""){page_headers();if(is_ajax()&&$l){page_messages($l);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$ai=$Yh.($Zh!=""?": $Zh":"");$bi=strip_tags($ai.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
>>>>>>> upstream/master
<html lang="',LANG,'" dir="',lang(80),'">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$bi,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=5.4.1"),'">
';$yb=adminer()->css();if(is_int(key($yb)))$yb=array_fill_keys($yb,'light');$od=in_array('light',$yb)||in_array('',$yb);$md=in_array('dark',$yb)||in_array('',$yb);$Ab=($od?($md?null:false):($md?:null));$Ke=" media='(prefers-color-scheme: dark)'";if($Ab!==false)echo"<link rel='stylesheet'".($Ab?"":$Ke)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=5.4.1")."'>\n";echo"<meta name='color-scheme' content='".($Ab===null?"light dark":($Ab?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=5.4.1");if(adminer()->head($Ab))echo"<link rel='icon' href='data:image/gif;base64,R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=5.4.1")."'>\n";foreach($yb
as$Bi=>$Te){$wa=($Te=='dark'&&!$Ab?$Ke:($Te=='light'&&$md?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$wa href='".h($Bi)."'>\n";}echo"\n<body class='".lang(80)." nojs";adminer()->bodyClass();echo"'>\n";$o=get_temp_dir()."/adminer.version";if(!$_COOKIE["adminer_version"]&&function_exists('openssl_verify')&&file_exists($o)&&filemtime($o)+86400>time()){$Li=unserialize(file_get_contents($o));$ug="-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwqWOVuF5uw7/+Z70djoK
RlHIZFZPO0uYRezq90+7Amk+FDNd7KkL5eDve+vHRJBLAszF/7XKXe11xwliIsFs
DFWQlsABVZB3oisKCBEuI71J4kPH8dKGEWR9jDHFw3cWmoH3PmqImX6FISWbG3B8
h7FIx3jEaw5ckVPVTeo5JRm/1DZzJxjyDenXvBQ/6o9DgZKeNDgxwKzH+sw9/YCO
jHnq1cFpOIISzARlrHMa/43YfeNRAm/tsBXjSxembBPo7aQZLAWHmaj5+K19H10B
nCpz9Y++cipkVEiKRGih4ZEvjoFysEOdRLj6WiD/uUNky4xGeA6LaJqh5XpkFkcQ
fQIDAQAB
-----END PUBLIC KEY-----
";if(openssl_verify($Li["version"],base64_decode($Li["signature"]),$ug)==1)$_COOKIE["adminer_version"]=$Li["version"];}echo
script("mixin(document.body, {onkeydown: bodyKeydown, onclick: bodyClick".(isset($_COOKIE["adminer_version"])?"":", onload: partial(verifyVersion, '".VERSION."', '".js_escape(ME)."', '".get_token()."')")."});
document.body.classList.replace('nojs', 'js');
const offlineMessage = '".js_escape(lang(81))."';
<<<<<<< HEAD
const thousandsSeparator = '".js_escape(lang(4))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'></div>\n",script("mixin(qs('#help'), {onmouseover: () => { helpOpen = 1; }, onmouseout: helpMouseout});"),"<div id='content'>\n","<span id='menuopen' class='jsonly'>".icon('move', '', 'menu', '').'</span>'.script("qs('#menuopen').onclick = event => { qs('#foot').classList.toggle('foot'); event.stopPropagation(); }");
    if ($Ka !== null) {
        $A = substr(preg_replace('~\b(username|db|ns)=[^&]*&~', '', ME), 0, -1);
        echo '<p id="breadcrumb"><a href="'.h($A ?: '.').'">'.get_driver(DRIVER).'</a> » ';
        $A = substr(preg_replace('~\b(db|ns)=[^&]*&~', '', ME), 0, -1);
        $P = adminer()->serverName(SERVER);
        $P = ($P != '' ? $P : lang(25));
        if ($Ka === false) {
            echo "$P\n";
        } else {
            echo "<a href='".h($A)."' accesskey='1' title='Alt+Shift+1'>$P</a> » ";
            if ($_GET['ns'] != '' || (DB != '' && is_array($Ka))) {
                echo '<a href="'.h($A.'&db='.urlencode(DB).(support('scheme') ? '&ns=' : '')).'">'.h(DB).'</a> » ';
            }if (is_array($Ka)) {
                if ($_GET['ns'] != '') {
                    echo '<a href="'.h(substr(ME, 0, -1)).'">'.h($_GET['ns']).'</a> » ';
                }foreach ($Ka as $z=>$X) {
                    $Ob = (is_array($X) ? $X[1] : h($X));
                    if ($Ob != '') {
                        echo "<a href='".h(ME."$z=").urlencode(is_array($X) ? $X[0] : $X)."'>$Ob</a> » ";
                    }
                }
            }echo "$Yh\n";
        }
    }echo "<h2>$ai</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";
    restart_session();
    page_messages($l);
    $i = &get_session('dbs');
    if (DB != '' && $i && ! in_array(DB, $i, true)) {
        $i = null;
    }stop_session();
    define('Adminer\PAGE_HEADER', 1);
}function page_headers()
{
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-cache');
    header('X-Frame-Options: deny');
    header('X-XSS-Protection: 0');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: origin-when-cross-origin');
    foreach (adminer()->csp(csp()) as $xb) {
        $pd = [];
        foreach ($xb as $z=>$X) {
            $pd[] = "$z $X";
        }header('Content-Security-Policy: '.implode('; ', $pd));
    }adminer()->headers();
}function csp()
{
    return [['script-src'=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'", 'connect-src'=>"'self'", 'frame-src'=>'https://www.adminer.org', 'object-src'=>"'none'", 'base-uri'=>"'none'", 'form-action'=>"'self'"]];
}function get_nonce()
{
    static $ff;
    if (! $ff) {
        $ff = base64_encode(rand_string());
    }

return $ff;
}function page_messages($l)
{
    $Ai = preg_replace('~^[^?]*~', '', $_SERVER['REQUEST_URI']);
    $Pe = idx($_SESSION['messages'], $Ai);
    if ($Pe) {
        echo "<div class='message'>".implode("</div>\n<div class='message'>", $Pe).'</div>'.script('messagesPrint();');
        unset($_SESSION['messages'][$Ai]);
    }if ($l) {
        echo "<div class='error'>$l</div>\n";
    }if (adminer()->error) {
        echo "<div class='error'>".adminer()->error."</div>\n";
    }
}function page_footer($Se = '')
{
    echo "</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";
    adminer()->navigation($Se);
    echo "</div>\n";
    if ($Se != 'auth') {
        echo '<form action="" method="post">
=======
const thousandsSeparator = '".js_escape(lang(4))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'></div>\n",script("mixin(qs('#help'), {onmouseover: () => { helpOpen = 1; }, onmouseout: helpMouseout});"),"<div id='content'>\n","<span id='menuopen' class='jsonly'>".icon("move","","menu","")."</span>".script("qs('#menuopen').onclick = event => { qs('#foot').classList.toggle('foot'); event.stopPropagation(); }");if($Ka!==null){$A=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($A?:".").'">'.get_driver(DRIVER).'</a> » ';$A=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$P=adminer()->serverName(SERVER);$P=($P!=""?$P:lang(25));if($Ka===false)echo"$P\n";else{echo"<a href='".h($A)."' accesskey='1' title='Alt+Shift+1'>$P</a> » ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ka)))echo'<a href="'.h($A."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> » ';if(is_array($Ka)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> » ';foreach($Ka
as$z=>$X){$Ob=(is_array($X)?$X[1]:h($X));if($Ob!="")echo"<a href='".h(ME."$z=").urlencode(is_array($X)?$X[0]:$X)."'>$Ob</a> » ";}}echo"$Yh\n";}}echo"<h2>$ai</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";restart_session();page_messages($l);$i=&get_session("dbs");if(DB!=""&&$i&&!in_array(DB,$i,true))$i=null;stop_session();define('Adminer\PAGE_HEADER',1);}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$xb){$pd=array();foreach($xb
as$z=>$X)$pd[]="$z $X";header("Content-Security-Policy: ".implode("; ",$pd));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self'","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
get_nonce(){static$ff;if(!$ff)$ff=base64_encode(rand_string());return$ff;}function
page_messages($l){$Ai=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$Pe=idx($_SESSION["messages"],$Ai);if($Pe){echo"<div class='message'>".implode("</div>\n<div class='message'>",$Pe)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Ai]);}if($l)echo"<div class='error'>$l</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($Se=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($Se);echo"</div>\n";if($Se!="auth")echo'<form action="" method="post">
>>>>>>> upstream/master
<p class="logout">
<span>',h($_GET["username"])."\n",'</span>
<input type="submit" name="logout" value="',lang(82),'" id="logout">
',input_token(),'</form>
<<<<<<< HEAD
';
    }echo "</div>\n\n",script('setupSubmitHighlight(document);');
}function int32($Xe)
{
    while ($Xe >= 2147483648) {
        $Xe -= 4294967296;
    }while ($Xe <= -2147483649) {
        $Xe += 4294967296;
    }

return (int) $Xe;
}function long2str(array $W, $Pi)
{
    $Rg = '';
    foreach ($W as $X) {
        $Rg
        .= pack('V', $X);
    }if ($Pi) {
        return substr($Rg, 0, end($W));
    }

return $Rg;
}function str2long($Rg, $Pi)
{
    $W = array_values(unpack('V*', str_pad($Rg, 4 * ceil(strlen($Rg) / 4), "\0")));
    if ($Pi) {
        $W[] = strlen($Rg);
    }

return $W;
}function xxtea_mx($Wi, $Vi, $Dh, $de)
{
    return int32((($Wi >> 5 & 0x7FFFFFF) ^ $Vi << 2) + (($Vi >> 3 & 0x1FFFFFFF) ^ $Wi << 4)) ^ int32(($Dh ^ $Vi) + ($de ^ $Wi));
}function encrypt_string($yh, $z)
{
    if ($yh == '') {
        return '';
    }$z = array_values(unpack('V*', pack('H*', md5($z))));
    $W = str2long($yh, true);
    $Xe = count($W) - 1;
    $Wi = $W[$Xe];
    $Vi = $W[0];
    $I = floor(6 + 52 / ($Xe + 1));
    $Dh = 0;
    while ($I-- > 0) {
        $Dh = int32($Dh + 0x9E3779B9);
        $cc = $Dh >> 2 & 3;
        for ($Kf = 0; $Kf < $Xe; $Kf++) {
            $Vi = $W[$Kf + 1];
            $We = xxtea_mx($Wi, $Vi, $Dh, $z[$Kf & 3 ^ $cc]);
            $Wi = int32($W[$Kf] + $We);
            $W[$Kf] = $Wi;
        }$Vi = $W[0];
        $We = xxtea_mx($Wi, $Vi, $Dh, $z[$Kf & 3 ^ $cc]);
        $Wi = int32($W[$Xe] + $We);
        $W[$Xe] = $Wi;
    }

return long2str($W, false);
}function decrypt_string($yh, $z)
{
    if ($yh == '') {
        return '';
    }if (! $z) {
        return false;
    }$z = array_values(unpack('V*', pack('H*', md5($z))));
    $W = str2long($yh, false);
    $Xe = count($W) - 1;
    $Wi = $W[$Xe];
    $Vi = $W[0];
    $I = floor(6 + 52 / ($Xe + 1));
    $Dh = int32($I * 0x9E3779B9);
    while ($Dh) {
        $cc = $Dh >> 2 & 3;
        for ($Kf = $Xe; $Kf > 0; $Kf--) {
            $Wi = $W[$Kf - 1];
            $We = xxtea_mx($Wi, $Vi, $Dh, $z[$Kf & 3 ^ $cc]);
            $Vi = int32($W[$Kf] - $We);
            $W[$Kf] = $Vi;
        }$Wi = $W[$Xe];
        $We = xxtea_mx($Wi, $Vi, $Dh, $z[$Kf & 3 ^ $cc]);
        $Vi = int32($W[0] - $We);
        $W[0] = $Vi;
        $Dh = int32($Dh - 0x9E3779B9);
    }

return long2str($W, true);
}$bg = [];
if ($_COOKIE['adminer_permanent']) {
    foreach (explode(' ', $_COOKIE['adminer_permanent']) as $X) {
        [$z] = explode(':', $X);
        $bg[$z] = $X;
    }
}function add_invalid_login()
{
    $Da = get_temp_dir().'/adminer.invalid';
    foreach (glob("$Da*") ?: [$Da] as $o) {
        $q = file_open_lock($o);
        if ($q) {
            break;
        }
    }if (! $q) {
        $q = file_open_lock("$Da-".rand_string());
    }if (! $q) {
        return;
    }$Vd = unserialize(stream_get_contents($q));
    $Vh = time();
    if ($Vd) {
        foreach ($Vd as $Wd=>$X) {
            if ($X[0] < $Vh) {
                unset($Vd[$Wd]);
            }
        }
    }$Ud = &$Vd[adminer()->bruteForceKey()];
    if (! $Ud) {
        $Ud = [$Vh + 30 * 60, 0];
    }$Ud[1]++;
    file_write_unlock($q, serialize($Vd));
}function check_invalid_login(array &$bg)
{
    $Vd = [];
    foreach (glob(get_temp_dir().'/adminer.invalid*') as $o) {
        $q = file_open_lock($o);
        if ($q) {
            $Vd = unserialize(stream_get_contents($q));
            file_unlock($q);
            break;
        }
    }$Ud = idx($Vd, adminer()->bruteForceKey(), []);
    $ef = ($Ud[1] > 29 ? $Ud[0] - time() : 0);
    if ($ef > 0) {
        auth_error(lang(83, ceil($ef / 60)), $bg);
    }
}$xa = $_POST['auth'];
if ($xa) {
    session_regenerate_id();
    $Ki = $xa['driver'];
    $P = $xa['server'];
    $V = $xa['username'];
    $H = (string) $xa['password'];
    $j = $xa['db'];
    set_password($Ki, $P, $V, $H);
    $_SESSION['db'][$Ki][$P][$V][$j] = true;
    if ($xa['permanent']) {
        $z = implode('-', array_map('base64_encode', [$Ki, $P, $V, $j]));
        $pg = adminer()->permanentLogin(true);
        $bg[$z] = "$z:".base64_encode($pg ? encrypt_string($H, $pg) : '');
        cookie('adminer_permanent', implode(' ', $bg));
    }if (count($_POST) == 1 || $Ki != DRIVER || $P != SERVER || $_GET['username'] !== $V || $j != DB) {
        redirect(auth_url($Ki, $P, $V, $j));
    }
} elseif ($_POST['logout'] && (! $_SESSION['token'] || verify_token())) {
    foreach (['pwds', 'db', 'dbs', 'queries'] as $z) {
        set_session($z, null);
    }unset_permanent($bg);
    redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~', '', ME), 0, -1), lang(84).' '.lang(85));
} elseif ($bg && ! $_SESSION['pwds']) {
    session_regenerate_id();
    $pg = adminer()->permanentLogin();
    foreach ($bg as $z=>$X) {
        [, $Xa] = explode(':', $X);
        [$Ki, $P, $V, $j] = array_map('base64_decode', explode('-', $z));
        set_password($Ki, $P, $V, decrypt_string(base64_decode($Xa), $pg));
        $_SESSION['db'][$Ki][$P][$V][$j] = true;
    }
}function unset_permanent(array &$bg)
{
    foreach ($bg as $z=>$X) {
        [$Ki, $P, $V, $j] = array_map('base64_decode', explode('-', $z));
        if ($Ki == DRIVER && $P == SERVER && $V == $_GET['username'] && $j == DB) {
            unset($bg[$z]);
        }
    }cookie('adminer_permanent', implode(' ', $bg));
}function auth_error($l, array &$bg)
{
    $gh = session_name();
    if (isset($_GET['username'])) {
        header('HTTP/1.1 403 Forbidden');
        if (($_COOKIE[$gh] || $_GET[$gh]) && ! $_SESSION['token']) {
            $l = lang(86);
        } else {
            restart_session();
            add_invalid_login();
            $H = get_password();
            if ($H !== null) {
                if ($H === false) {
                    $l
                    .= ($l ? '<br>' : '').lang(87, target_blank(), '<code>permanentLogin()</code>');
                }set_password(DRIVER, SERVER, $_GET['username'], null);
            }unset_permanent($bg);
        }
    }if (! $_COOKIE[$gh] && $_GET[$gh] && ini_bool('session.use_only_cookies')) {
        $l = lang(88);
    }$Nf = session_get_cookie_params();
    cookie('adminer_key', ($_COOKIE['adminer_key'] ?: rand_string()), $Nf['lifetime']);
    if (! $_SESSION['token']) {
        $_SESSION['token'] = rand(1, 1e6);
    }page_header(lang(29), $l, null);
    echo "<form action='' method='post'>\n",'<div>';
    if (hidden_fields($_POST, ['auth'])) {
        echo "<p class='message'>".lang(89)."\n";
    }echo "</div>\n";
    adminer()->loginForm();
    echo "</form>\n";
    page_footer('auth');
    exit;
}if (isset($_GET['username']) && ! class_exists('Adminer\Db')) {
    unset($_SESSION['pwds'][DRIVER]);
    unset_permanent($bg);
    page_header(lang(90), lang(91, implode(', ', Driver::$extensions)), false);
    page_footer('auth');
    exit;
}$f = '';
if (isset($_GET['username']) && is_string(get_password())) {
    [, $fg] = host_port(SERVER);
    if (preg_match('~^\s*([-+]?\d+)~', $fg, $C) && ($C[1] < 1024 || $C[1] > 65535)) {
        auth_error(lang(92), $bg);
    }check_invalid_login($bg);
    $wb = adminer()->credentials();
    $f = Driver::connect($wb[0], $wb[1], $wb[2]);
    if (is_object($f)) {
        Db::$instance = $f;
        Driver::$instance = new Driver($f);
        if ($f->flavor) {
            save_settings(['vendor-'.DRIVER.'-'.SERVER=>get_driver(DRIVER)]);
        }
    }
}$we = null;
if (! is_object($f) || ($we = adminer()->login($_GET['username'], get_password())) !== true) {
    $l = (is_string($f) ? nl_br(h($f)) : (is_string($we) ? $we : lang(93))).(preg_match('~^ | $~', get_password()) ? '<br>'.lang(94) : '');
    auth_error($l, $bg);
}if ($_POST['logout'] && $_SESSION['token'] && ! verify_token()) {
    page_header(lang(82), lang(95));
    page_footer('db');
    exit;
}if (! $_SESSION['token']) {
    $_SESSION['token'] = rand(1, 1e6);
}stop_session(true);
if ($xa && $_POST['token']) {
    $_POST['token'] = get_token();
}$l = '';
if ($_POST) {
    if (! verify_token()) {
        $Nd = 'max_input_vars';
        $Ie = ini_get($Nd);
        if (extension_loaded('suhosin')) {
            foreach (['suhosin.request.max_vars', 'suhosin.post.max_vars'] as $z) {
                $X = ini_get($z);
                if ($X && (! $Ie || $X < $Ie)) {
                    $Nd = $z;
                    $Ie = $X;
                }
            }
        }$l = (! $_POST['token'] && $Ie ? lang(96, "'$Nd'") : lang(95).' '.lang(97));
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $l = lang(98, "'post_max_size'");
    if (isset($_GET['sql'])) {
        $l
        .= ' '.lang(99);
    }
}function print_select_result($K, $g = null, array $Bf = [], $_ = 0)
{
    $ve = [];
    $x = [];
    $d = [];
    $Ia = [];
    $qi = [];
    $L = [];
    for ($t = 0; (! $_ || $t < $_) && ($M = $K->fetch_row()); $t++) {
        if (! $t) {
            echo "<div class='scrollable'>\n","<table class='nowrap odds'>\n",'<thead><tr>';
            for ($y = 0; $y < count($M); $y++) {
                $m = $K->fetch_field();
                $E = $m->name;
                $Af = (isset($m->orgtable) ? $m->orgtable : '');
                $_f = (isset($m->orgname) ? $m->orgname : $E);
                if ($Bf && JUSH == 'sql') {
                    $ve[$y] = ($E == 'table' ? 'table=' : ($E == 'possible_keys' ? 'indexes=' : null));
                } elseif ($Af != '') {
                    if (isset($m->table)) {
                        $L[$m->table] = $Af;
                    }if (! isset($x[$Af])) {
                        $x[$Af] = [];
                        foreach (indexes($Af, $g) as $w) {
                            if ($w['type'] == 'PRIMARY') {
                                $x[$Af] = array_flip($w['columns']);
                                break;
                            }
                        }$d[$Af] = $x[$Af];
                    }if (isset($d[$Af][$_f])) {
                        unset($d[$Af][$_f]);
                        $x[$Af][$_f] = $y;
                        $ve[$y] = $Af;
                    }
                }if ($m->charsetnr == 63) {
                    $Ia[$y] = true;
                }$qi[$y] = $m->type;
                echo '<th'.($Af != '' || $m->name != $_f ? " title='".h(($Af != '' ? "$Af." : '').$_f)."'" : '').'>'.h($E).($Bf ? doc_link(['sql'=>'explain-output.html#explain_'.strtolower($E), 'mariadb'=>'explain/#the-columns-in-explain-select']) : '');
            }echo "</thead>\n";
        }echo '<tr>';
        foreach ($M as $z=>$X) {
            $A = '';
            if (isset($ve[$z]) && ! $d[$ve[$z]]) {
                if ($Bf && JUSH == 'sql') {
                    $R = $M[array_search('table=', $ve)];
                    $A = ME.$ve[$z].urlencode($Bf[$R] != '' ? $Bf[$R] : $R);
                } else {
                    $A = ME.'edit='.urlencode($ve[$z]);
                    foreach ($x[$ve[$z]] as $bb=>$y) {
                        if ($M[$y] === null) {
                            $A = '';
                            break;
                        }$A
                        .= '&where'.urlencode('['.bracket_escape($bb).']').'='.urlencode($M[$y]);
                    }
                }
            } elseif (is_url($X)) {
                $A = $X;
            }if ($X === null) {
                $X = '<i>NULL</i>';
            } elseif ($Ia[$z] && ! is_utf8($X)) {
                $X = '<i>'.lang(38, strlen($X)).'</i>';
            } else {
                $X = h($X);
                if ($qi[$z] == 254) {
                    $X = "<code>$X</code>";
                }
            }if ($A) {
                $X = "<a href='".h($A)."'".(is_url($A) ? target_blank() : '').">$X</a>";
            }echo '<td'.($qi[$z] <= 9 || $qi[$z] == 246 ? " class='number'" : '').">$X";
        }
    }echo ($t ? "</table>\n</div>" : "<p class='message'>".lang(14))."\n";

    return $L;
}function referencable_primary($bh)
{
    $L = [];
    foreach (table_status('', true) as $Hh=>$R) {
        if ($Hh != $bh && fk_support($R)) {
            foreach (fields($Hh) as $m) {
                if ($m['primary']) {
                    if ($L[$Hh]) {
                        unset($L[$Hh]);
                        break;
                    }$L[$Hh] = $m;
                }
            }
        }
    }

return $L;
}function textarea($E, $Y, $N = 10, $eb = 80)
{
    echo "<textarea name='".h($E)."' rows='$N' cols='$eb' class='sqlarea jush-".JUSH."' spellcheck='false' wrap='off'>";
    if (is_array($Y)) {
        foreach ($Y as $X) {
            echo h($X[0])."\n\n\n";
        }
    } else {
        echo h($Y);
    }echo '</textarea>';
}function select_input($wa, array $wf, $Y = '', $rf = '', $cg = '')
{
    $Oh = ($wf ? 'select' : 'input');

    return "<$Oh$wa".($wf ? "><option value=''>$cg".optionlist($wf, $Y, true).'</select>' : " size='10' value='".h($Y)."' placeholder='$cg'>").($rf ? script("qsl('$Oh').onchange = $rf;", '') : '');
}function json_row($z, $X = null, $uc = true)
{
    static $Oc = true;
    if ($Oc) {
        echo '{';
    }if ($z != '') {
        echo ($Oc ? '' : ',')."\n\t\"".addcslashes($z, "\r\n\t\"\\/").'": '.($X !== null ? ($uc ? '"'.addcslashes($X, "\r\n\"\\/").'"' : $X) : 'null');
        $Oc = false;
    } else {
        echo "\n}\n";
        $Oc = true;
    }
}function edit_type($z, array $m, array $b, array $Vc = [], array $Fc = [])
{
    $U = $m['type'];
    echo "<td><select name='".h($z)."[type]' class='type' aria-labelledby='label-type'>";
    if ($U && ! array_key_exists($U, driver()->types()) && ! isset($Vc[$U]) && ! in_array($U, $Fc)) {
        $Fc[] = $U;
    }$_h = driver()->structuredTypes();
    if ($Vc) {
        $_h[lang(100)] = $Vc;
    }echo optionlist(array_merge($Fc, $_h), $U),'</select><td>',"<input name='".h($z)."[length]' value='".h($m['length'])."' size='3'".(! $m['length'] && preg_match('~var(char|binary)$~', $U) ? " class='required'" : '')." aria-labelledby='label-length'>","<td class='options'>",($b ? "<input list='collations' name='".h($z)."[collation]'".(preg_match('~(char|text|enum|set)$~', $U) ? '' : " class='hidden'")." value='".h($m['collation'])."' placeholder='(".lang(101).")'>" : ''),(driver()->unsigned ? "<select name='".h($z)."[unsigned]'".(! $U || preg_match(number_type(), $U) ? '' : " class='hidden'").'><option>'.optionlist(driver()->unsigned, $m['unsigned']).'</select>' : ''),(isset($m['on_update']) ? "<select name='".h($z)."[on_update]'".(preg_match('~timestamp|datetime~', $U) ? '' : " class='hidden'").'>'.optionlist([''=> '('.lang(102).')', 'CURRENT_TIMESTAMP'], (preg_match('~^CURRENT_TIMESTAMP~i', $m['on_update']) ? 'CURRENT_TIMESTAMP' : $m['on_update'])).'</select>' : ''),($Vc ? "<select name='".h($z)."[on_delete]'".(preg_match('~`~', $U) ? '' : " class='hidden'")."><option value=''>(".lang(103).')'.optionlist(explode('|', driver()->onActions), $m['on_delete']).'</select> ' : ' ');
}function process_length($re)
{
    $pc = driver()->enumLength;

    return preg_match("~^\\s*\\(?\\s*$pc(?:\\s*,\\s*$pc)*+\\s*\\)?\\s*\$~", $re) && preg_match_all("~$pc~", $re, $Ae) ? '('.implode(',', $Ae[0]).')' : preg_replace('~^[0-9].*~', '(\0)', preg_replace('~[^-0-9,+()[\]]~', '', $re));
}function process_type(array $m, $cb = 'COLLATE')
{
    return " $m[type]".process_length($m['length']).(preg_match(number_type(), $m['type']) && in_array($m['unsigned'], driver()->unsigned) ? " $m[unsigned]" : '').(preg_match('~char|text|enum|set~', $m['type']) && $m['collation'] ? " $cb ".(JUSH == 'mssql' ? $m['collation'] : q($m['collation'])) : '');
}function process_field(array $m, array $oi)
{
    if ($m['on_update']) {
        $m['on_update'] = str_ireplace('current_timestamp()', 'CURRENT_TIMESTAMP', $m['on_update']);
    }

return [idf_escape(trim($m['field'])), process_type($oi), ($m['null'] ? ' NULL' : ' NOT NULL'), default_value($m), (preg_match('~timestamp|datetime~', $m['type']) && $m['on_update'] ? " ON UPDATE $m[on_update]" : ''), (support('comment') && $m['comment'] != '' ? ' COMMENT '.q($m['comment']) : ''), ($m['auto_increment'] ? auto_increment() : null)];
}function default_value(array $m)
{
    $k = $m['default'];
    $cd = $m['generated'];

    return $k === null ? '' : (in_array($cd, driver()->generated) ? (JUSH == 'mssql' ? " AS ($k)".($cd == 'VIRTUAL' ? '' : " $cd").'' : " GENERATED ALWAYS AS ($k) $cd") : ' DEFAULT '.(! preg_match('~^GENERATED ~i', $k) && (preg_match('~char|binary|text|json|enum|set~', $m['type']) || preg_match('~^(?![a-z])~i', $k)) ? (JUSH == 'sql' && preg_match('~text|json~', $m['type']) ? '('.q($k).')' : q($k)) : str_ireplace('current_timestamp()', 'CURRENT_TIMESTAMP', (JUSH == 'sqlite' ? "($k)" : $k))));
}function type_class($U)
{
    foreach (['char'=>'text', 'date'=>'time|year', 'binary'=>'blob', 'enum'=>'set'] as $z=>$X) {
        if (preg_match("~$z|$X~", $U)) {
            return " class='$z'";
        }
    }
}function edit_fields(array $n, array $b, $U = 'TABLE', array $Vc = [])
{
    $n = array_values($n);
    $Jb = (($_POST ? $_POST['defaults'] : get_setting('defaults')) ? '' : " class='hidden'");
    $ib = (($_POST ? $_POST['comments'] : get_setting('comments')) ? '' : " class='hidden'");
    echo "<thead><tr>\n",($U == 'PROCEDURE' ? '<td>' : ''),"<th id='label-name'>".($U == 'TABLE' ? lang(104) : lang(105)),"<td id='label-type'>".lang(40)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' style='display: none;'></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<td id='label-length'>".lang(106),'<td>'.lang(107);
    if ($U == 'TABLE') {
        echo "<td id='label-null'>NULL\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(42)."'>AI</abbr>",doc_link(['sql'=> 'example-auto-increment.html', 'mariadb'=>'auto_increment/']),"<td id='label-default'$Jb>".lang(43),(support('comment') ? "<td id='label-comment'$ib>".lang(41) : '');
    }echo '<td>'.icon('plus', 'add['.(support('move_col') ? 0 : count($n)).']', '+', lang(108)),"</thead>\n<tbody>\n",script("mixin(qsl('tbody'), {onclick: editingClick, onkeydown: editingKeydown, oninput: editingInput});");
    foreach ($n as $t=>$m) {
        $t++;
        $Cf = $m[($_POST ? 'orig' : 'field')];
        $Ub = (isset($_POST['add'][$t - 1]) || (isset($m['field']) && ! idx($_POST['drop_col'], $t))) && (support('drop_col') || $Cf == '');
        echo '<tr'.($Ub ? '' : " style='display: none;'").">\n",($U == 'PROCEDURE' ? '<td>'.html_select("fields[$t][inout]", explode('|', driver()->inout), $m['inout']) : '').'<th>';
        if ($Ub) {
            echo "<input name='fields[$t][field]' value='".h($m['field'])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST['add'][$t - 1]) ? ' autofocus' : '').'>';
        }echo input_hidden("fields[$t][orig]", $Cf);
        edit_type("fields[$t]", $m, $b, $Vc);
        if ($U == 'TABLE') {
            echo '<td>'.checkbox("fields[$t][null]", 1, $m['null'], '', '', 'block', 'label-null'),"<td><label class='block'><input type='radio' name='auto_increment_col' value='$t'".($m['auto_increment'] ? ' checked' : '')." aria-labelledby='label-ai'></label>","<td$Jb>".(driver()->generated ? html_select("fields[$t][generated]", array_merge(['', 'DEFAULT'], driver()->generated), $m['generated']).' ' : checkbox("fields[$t][generated]", 1, $m['generated'], '', '', '', 'label-default')),"<input name='fields[$t][default]' value='".h($m['default'])."' aria-labelledby='label-default'>",(support('comment') ? "<td$ib><input name='fields[$t][comment]' value='".h($m['comment'])."' data-maxlength='".(min_version(5.5) ? 1024 : 255)."' aria-labelledby='label-comment'>" : '');
        }echo '<td>',(support('move_col') ? icon('plus', "add[$t]", '+', lang(108)).' '.icon('up', "up[$t]", '↑', lang(109)).' '.icon('down', "down[$t]", '↓', lang(110)).' ' : ''),($Cf == '' || support('drop_col') ? icon('cross', "drop_col[$t]", 'x', lang(111)) : '');
    }
}function process_fields(array &$n)
{
    $jf = 0;
    if ($_POST['up']) {
        $le = 0;
        foreach ($n as $z=>$m) {
            if (key($_POST['up']) == $z) {
                unset($n[$z]);
                array_splice($n, $le, 0, [$m]);
                break;
            }if (isset($m['field'])) {
                $le = $jf;
            }$jf++;
        }
    } elseif ($_POST['down']) {
        $Xc = false;
        foreach ($n as $z=>$m) {
            if (isset($m['field']) && $Xc) {
                unset($n[key($_POST['down'])]);
                array_splice($n, $jf, 0, [$Xc]);
                break;
            }if (key($_POST['down']) == $z) {
                $Xc = $m;
            }$jf++;
        }
    } elseif ($_POST['add']) {
        $n = array_values($n);
        array_splice($n, key($_POST['add']), 0, [[]]);
    } elseif (! $_POST['drop_col']) {
        return false;
    }

return true;
}function normalize_enum(array $C)
{
    $X = $C[0];

    return "'".str_replace("'", "''", addcslashes(stripcslashes(str_replace($X[0].$X[0], $X[0], substr($X, 1, -1))), '\\'))."'";
}function grant($ed, array $rg, $d, $pf)
{
    if (! $rg) {
        return true;
    }if ($rg == ['ALL PRIVILEGES', 'GRANT OPTION']) {
        return $ed == 'GRANT' ? queries("$ed ALL PRIVILEGES$pf WITH GRANT OPTION") : queries("$ed ALL PRIVILEGES$pf") && queries("$ed GRANT OPTION$pf");
    }

return queries("$ed ".preg_replace('~(GRANT OPTION)\([^)]*\)~', '\1', implode("$d, ", $rg).$d).$pf);
}function drop_create($Yb, $h, $Zb, $Sh, $ac, $B, $Oe, $Me, $Ne, $mf, $bf)
{
    if ($_POST['drop']) {
        query_redirect($Yb, $B, $Oe);
    } elseif ($mf == '') {
        query_redirect($h, $B, $Ne);
    } elseif ($mf != $bf) {
        $vb = queries($h);
        queries_redirect($B, $Me, $vb && queries($Yb));
        if ($vb) {
            queries($Zb);
        }
    } else {
        queries_redirect($B, $Me, queries($Sh) && queries($ac) && queries($Yb) && queries($h));
    }
}function create_trigger($pf, array $M)
{
    $Xh = " $M[Timing] $M[Event]".(preg_match('~ OF~', $M['Event']) ? " $M[Of]" : '');

    return 'CREATE TRIGGER '.idf_escape($M['Trigger']).(JUSH == 'mssql' ? $pf.$Xh : $Xh.$pf).rtrim(" $M[Type]\n$M[Statement]", ';').';';
}function create_routine($Og, array $M)
{
    $Q = [];
    $n = (array) $M['fields'];
    ksort($n);
    foreach ($n as $m) {
        if ($m['field'] != '') {
            $Q[] = (preg_match('~^('.driver()->inout.')$~', $m['inout']) ? "$m[inout] " : '').idf_escape($m['field']).process_type($m, 'CHARACTER SET');
        }
    }$Lb = rtrim($M['definition'], ';');

    return "CREATE $Og ".idf_escape(trim($M['name'])).' ('.implode(', ', $Q).')'.($Og == 'FUNCTION' ? ' RETURNS'.process_type($M['returns'], 'CHARACTER SET') : '').($M['language'] ? " LANGUAGE $M[language]" : '').(JUSH == 'pgsql' ? ' AS '.q($Lb) : "\n$Lb;");
}function remove_definer($J)
{
    return preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~', '`@`(%|\1)', logged_user()).'`~', '\1', $J);
}function format_foreign_key(array $p)
{
    $j = $p['db'];
    $gf = $p['ns'];

    return ' FOREIGN KEY ('.implode(', ', array_map('Adminer\idf_escape', $p['source'])).') REFERENCES '.($j != '' && $j != $_GET['db'] ? idf_escape($j).'.' : '').($gf != '' && $gf != $_GET['ns'] ? idf_escape($gf).'.' : '').idf_escape($p['table']).' ('.implode(', ', array_map('Adminer\idf_escape', $p['target'])).')'.(preg_match('~^('.driver()->onActions.')$~', $p['on_delete']) ? " ON DELETE $p[on_delete]" : '').(preg_match('~^('.driver()->onActions.')$~', $p['on_update']) ? " ON UPDATE $p[on_update]" : '');
}function tar_file($o, $ci)
{
    $L = pack('a100a8a8a8a12a12', $o, 644, 0, 0, decoct($ci->size), decoct(time()));
    $Wa = 8 * 32;
    for ($t = 0; $t < strlen($L); $t++) {
        $Wa += ord($L[$t]);
    }$L
    .= sprintf('%06o', $Wa)."\0 ";
    echo $L,str_repeat("\0", 512 - strlen($L));
    $ci->send();
    echo str_repeat("\0", 511 - ($ci->size + 511) % 512);
}function doc_link(array $Yf, $Th = '<sup>?</sup>')
{
    $eh = connection()->server_info;
    $Li = preg_replace('~^(\d\.?\d).*~s', '\1', $eh);
    $Ci = ['sql'=>"https://dev.mysql.com/doc/refman/$Li/en/", 'sqlite'=>'https://www.sqlite.org/', 'pgsql'=>'https://www.postgresql.org/docs/'.(connection()->flavor == 'cockroach' ? 'current' : $Li).'/', 'mssql'=>'https://learn.microsoft.com/en-us/sql/', 'oracle'=>'https://www.oracle.com/pls/topic/lookup?ctx=db'.preg_replace('~^.* (\d+)\.(\d+)\.\d+\.\d+\.\d+.*~s', '\1\2', $eh).'&id='];
    if (connection()->flavor == 'maria') {
        $Ci['sql'] = 'https://mariadb.com/kb/en/';
        $Yf['sql'] = (isset($Yf['mariadb']) ? $Yf['mariadb'] : str_replace('.html', '/', $Yf['sql']));
    }

return $Yf[JUSH] ? "<a href='".h($Ci[JUSH].$Yf[JUSH].(JUSH == 'mssql' ? "?view=sql-server-ver$Li" : ''))."'".target_blank().">$Th</a>" : '';
}function db_size($j)
{
    if (! connection()->select_db($j)) {
        return '?';
    }$L = 0;
    foreach (table_status() as $S) {
        $L += $S['Data_length'] + $S['Index_length'];
    }

return format_number($L);
}function set_utf8mb4($h)
{
    static $Q = false;
    if (! $Q && preg_match('~\butf8mb4~i', $h)) {
        $Q = true;
        echo 'SET NAMES '.charset(connection()).";\n\n";
    }
}if (isset($_GET['status'])) {
    $_GET['variables'] = $_GET['status'];
}if (isset($_GET['import'])) {
    $_GET['sql'] = $_GET['import'];
}if (! (DB != '' ? connection()->select_db(DB) : isset($_GET['sql']) || isset($_GET['dump']) || isset($_GET['database']) || isset($_GET['processlist']) || isset($_GET['privileges']) || isset($_GET['user']) || isset($_GET['variables']) || $_GET['script'] == 'connect' || $_GET['script'] == 'kill')) {
    if (DB != '' || $_GET['refresh']) {
        restart_session();
        set_session('dbs', null);
    }if (DB != '') {
        header('HTTP/1.1 404 Not Found');
        page_header(lang(28).': '.h(DB), lang(112), true);
    } else {
        if ($_POST['db'] && ! $l) {
            queries_redirect(substr(ME, 0, -1), lang(113), drop_databases($_POST['db']));
        }page_header(lang(114), $l, false);
        echo "<p class='links'>\n";
        foreach (['database'=>lang(115), 'privileges'=>lang(62), 'processlist'=>lang(116), 'variables'=>lang(117), 'status'=>lang(118)] as $z=>$X) {
            if (support($z)) {
                echo "<a href='".h(ME)."$z='>$X</a>\n";
            }
        }echo '<p>'.lang(119, get_driver(DRIVER), '<b>'.h(connection()->server_info).'</b>', '<b>'.connection()->extension.'</b>')."\n",'<p>'.lang(120, '<b>'.h(logged_user()).'</b>')."\n";
        $i = adminer()->databases();
        if ($i) {
            $Ug = support('scheme');
            $b = collations();
            echo "<form action='' method='post'>\n","<table class='checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr>'.(support('database') ? '<td>' : '').'<th>'.lang(28).(get_session('dbs') !== null ? " - <a href='".h(ME)."refresh=1'>".lang(121).'</a>' : '').'<td>'.lang(122).'<td>'.lang(123).'<td>'.lang(124)." - <a href='".h(ME)."dbsize=1'>".lang(125).'</a>'.script("qsl('a').onclick = partial(ajaxSetHtml, '".js_escape(ME)."script=connect');", '')."</thead>\n";
            $i = ($_GET['dbsize'] ? count_tables($i) : array_flip($i));
            foreach ($i as $j=>$T) {
                $Ng = h(ME).'db='.urlencode($j);
                $u = h('Db-'.$j);
                echo '<tr>'.(support('database') ? '<td>'.checkbox('db[]', $j, in_array($j, (array) $_POST['db']), '', '', '', $u) : ''),"<th><a href='$Ng' id='$u'>".h($j).'</a>';
                $db = h(db_collation($j, $b));
                echo '<td>'.(support('database') ? "<a href='$Ng".($Ug ? '&amp;ns=' : '')."&amp;database=' title='".lang(58)."'>$db</a>" : $db),"<td align='right'><a href='$Ng&amp;schema=' id='tables-".h($j)."' title='".lang(61)."'>".($_GET['dbsize'] ? $T : '?').'</a>',"<td align='right' id='size-".h($j)."'>".($_GET['dbsize'] ? db_size($j) : '?'),"\n";
            }echo "</table>\n",(support('database') ? "<div class='footer'><div>\n".'<fieldset><legend>'.lang(126)." <span id='selected'></span></legend><div>\n".input_hidden('all').script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^db/)); };")."<input type='submit' name='drop' value='".lang(127)."'>".confirm()."\n"."</div></fieldset>\n"."</div></div>\n" : ''),input_token(),"</form>\n",script('tableCheck();');
        }if (! empty(adminer()->plugins)) {
            echo "<div class='plugins'>\n",'<h3>'.lang(128)."</h3>\n<ul>\n";
            foreach (adminer()->plugins as $dg) {
                $Pb = (method_exists($dg, 'description') ? $dg->description() : '');
                if (! $Pb) {
                    $Eg = new \ReflectionObject($dg);
                    if (preg_match('~^/[\s*]+(.+)~', $Eg->getDocComment(), $C)) {
                        $Pb = $C[1];
                    }
                }$Vg = (method_exists($dg, 'screenshot') ? $dg->screenshot() : '');
                echo '<li><b>'.get_class($dg).'</b>'.h($Pb ? ": $Pb" : '').($Vg ? " (<a href='". h($Vg). "'".target_blank().'>'.lang(129).'</a>)' : '')."\n";
            }echo "</ul>\n";
            adminer()->pluginsLinks();
            echo "</div>\n";
        }
    }page_footer('db');
    exit;
}adminer()->afterConnect();
class TmpFile
{
    private $handler;

    public $size;

    public function __construct()
    {
        $this->handler = tmpfile();
    }

    public function write($pb)
    {
        $this->size += strlen($pb);
        fwrite($this->handler, $pb);
    }

    public function send()
    {
        fseek($this->handler, 0);
        fpassthru($this->handler);
        fclose($this->handler);
    }
}if (isset($_GET['select']) && ($_POST['edit'] || $_POST['clone']) && ! $_POST['save']) {
    $_GET['edit'] = $_GET['select'];
}if (isset($_GET['callf'])) {
    $_GET['call'] = $_GET['callf'];
}if (isset($_GET['function'])) {
    $_GET['procedure'] = $_GET['function'];
}if (isset($_GET['download'])) {
    $a = $_GET['download'];
    $n = fields($a);
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.friendly_url("$a-".implode('_', $_GET['where'])).'.'.friendly_url($_GET['field']));
    $O = [idf_escape($_GET['field'])];
    $K = driver()->select($a, $O, [where($_GET, $n)], $O);
    $M = ($K ? $K->fetch_row() : []);
    echo driver()->value($M[0], $n[$_GET['field']]);
    exit;
} elseif (isset($_GET['table'])) {
    $a = $_GET['table'];
    $n = fields($a);
    if (! $n) {
        $l = error() ?: lang(11);
    }$S = table_status1($a);
    $E = adminer()->tableName($S);
    page_header(($n && is_view($S) ? $S['Engine'] == 'materialized view' ? lang(130) : lang(131) : lang(132)).': '.($E != '' ? $E : h($a)), $l);
    $Mg = [];
    foreach ($n as $z=>$m) {
        $Mg += $m['privileges'];
    }adminer()->selectLinks($S, (isset($Mg['insert']) || ! support('table') ? '' : null));
    $hb = $S['Comment'];
    if ($hb != '') {
        echo "<p class='nowrap'>".lang(41).': '.h($hb)."\n";
    }if ($n) {
        adminer()->tableStructurePrint($n, $S);
    }function tables_links(array $T)
    {
        echo "<ul>\n";
        foreach ($T as $R) {
            echo "<li><a href='".h(ME.'table='.urlencode($R))."'>".h($R).'</a>';
        }echo "</ul>\n";
    }$Md = driver()->inheritsFrom($a);
    if ($Md) {
        echo '<h3>'.lang(133)."</h3>\n";
        tables_links($Md);
    }if (support('indexes') && driver()->supportsIndex($S)) {
        echo "<h3 id='indexes'>".lang(134)."</h3>\n";
        $x = indexes($a);
        if ($x) {
            adminer()->tableIndexesPrint($x, $S);
        }echo '<p class="links"><a href="'.h(ME).'indexes='.urlencode($a).'">'.lang(135)."</a>\n";
    }if (! is_view($S)) {
        if (fk_support($S)) {
            echo "<h3 id='foreign-keys'>".lang(100)."</h3>\n";
            $Vc = foreign_keys($a);
            if ($Vc) {
                echo "<table>\n",'<thead><tr><th>'.lang(136).'<td>'.lang(137).'<td>'.lang(103).'<td>'.lang(102)."<td></thead>\n";
                foreach ($Vc as $E=>$p) {
                    echo "<tr title='".h($E)."'>",'<th><i>'.implode('</i>, <i>', array_map('Adminer\h', $p['source'])).'</i>';
                    $A = ($p['db'] != '' ? preg_replace('~db=[^&]*~', 'db='.urlencode($p['db']), ME) : ($p['ns'] != '' ? preg_replace('~ns=[^&]*~', 'ns='.urlencode($p['ns']), ME) : ME));
                    echo "<td><a href='".h($A.'table='.urlencode($p['table']))."'>".($p['db'] != '' && $p['db'] != DB ? '<b>'.h($p['db']).'</b>.' : '').($p['ns'] != '' && $p['ns'] != $_GET['ns'] ? '<b>'.h($p['ns']).'</b>.' : '').h($p['table']).'</a>','(<i>'.implode('</i>, <i>', array_map('Adminer\h', $p['target'])).'</i>)','<td>'.h($p['on_delete']),'<td>'.h($p['on_update']),'<td><a href="'.h(ME.'foreign='.urlencode($a).'&name='.urlencode($E)).'">'.lang(138).'</a>',"\n";
                }echo "</table>\n";
            }echo '<p class="links"><a href="'.h(ME).'foreign='.urlencode($a).'">'.lang(139)."</a>\n";
        }if (support('check')) {
            echo "<h3 id='checks'>".lang(140)."</h3>\n";
            $Ta = driver()->checkConstraints($a);
            if ($Ta) {
                echo "<table>\n";
                foreach ($Ta as $z=>$X) {
                    echo "<tr title='".h($z)."'>","<td><code class='jush-".JUSH."'>".h($X),"<td><a href='".h(ME.'check='.urlencode($a).'&name='.urlencode($z))."'>".lang(138).'</a>',"\n";
                }echo "</table>\n";
            }echo '<p class="links"><a href="'.h(ME).'check='.urlencode($a).'">'.lang(141)."</a>\n";
        }
    }if (support(is_view($S) ? 'view_trigger' : 'trigger')) {
        echo "<h3 id='triggers'>".lang(142)."</h3>\n";
        $ni = triggers($a);
        if ($ni) {
            echo "<table>\n";
            foreach ($ni as $z=>$X) {
                echo "<tr valign='top'><td>".h($X[0]).'<td>'.h($X[1]).'<th>'.h($z)."<td><a href='".h(ME.'trigger='.urlencode($a).'&name='.urlencode($z))."'>".lang(138)."</a>\n";
            }echo "</table>\n";
        }echo '<p class="links"><a href="'.h(ME).'trigger='.urlencode($a).'">'.lang(143)."</a>\n";
    }$Ld = driver()->inheritedTables($a);
    if ($Ld) {
        echo "<h3 id='partitions'>".lang(144)."</h3>\n";
        $Qf = driver()->partitionsInfo($a);
        if ($Qf) {
            echo "<p><code class='jush-".JUSH."'>BY ".h("$Qf[partition_by]($Qf[partition])")."</code>\n";
        }tables_links($Ld);
    }
} elseif (isset($_GET['schema'])) {
    page_header(lang(61), '', [], h(DB.($_GET['ns'] ? ".$_GET[ns]" : '')));
    $Ih = [];
    $Jh = [];
    $da = ($_GET['schema'] ?: $_COOKIE['adminer_schema-'.str_replace('.', '_', DB)]);
    preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~', $da, $Ae, PREG_SET_ORDER);
    foreach ($Ae as $t=>$C) {
        $Ih[$C[1]] = [$C[2], $C[3]];
        $Jh[] = "\n\t'".js_escape($C[1])."': [ $C[2], $C[3] ]";
    }$fi = 0;
    $Ea = -1;
    $Tg = [];
    $Dg = [];
    $pe = [];
    $pa = driver()->allFields();
    foreach (table_status('', true) as $R=>$S) {
        if (is_view($S)) {
            continue;
        }$gg = 0;
        $Tg[$R]['fields'] = [];
        foreach ($pa[$R] as $m) {
            $gg += 1.25;
            $m['pos'] = $gg;
            $Tg[$R]['fields'][$m['field']] = $m;
        }$Tg[$R]['pos'] = ($Ih[$R] ?: [$fi, 0]);
        foreach (adminer()->foreignKeys($R) as $X) {
            if (! $X['db']) {
                $ne = $Ea;
                if (idx($Ih[$R], 1) || idx($Ih[$X['table']], 1)) {
                    $ne = min(idx($Ih[$R], 1, 0), idx($Ih[$X['table']], 1, 0)) - 1;
                } else {
                    $Ea -= .1;
                }while ($pe[(string) $ne]) {
                    $ne -= .0001;
                }$Tg[$R]['references'][$X['table']][(string) $ne] = [$X['source'], $X['target']];
                $Dg[$X['table']][$R][(string) $ne] = $X['target'];
                $pe[(string) $ne] = true;
            }
        }$fi = max($fi, $Tg[$R]['pos'][0] + 2.5 + $gg);
    }echo '<div id="schema" style="height: ',$fi,'em;">
=======
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($Xe){while($Xe>=2147483648)$Xe-=4294967296;while($Xe<=-2147483649)$Xe+=4294967296;return(int)$Xe;}function
long2str(array$W,$Pi){$Rg='';foreach($W
as$X)$Rg
.=pack('V',$X);if($Pi)return
substr($Rg,0,end($W));return$Rg;}function
str2long($Rg,$Pi){$W=array_values(unpack('V*',str_pad($Rg,4*ceil(strlen($Rg)/4),"\0")));if($Pi)$W[]=strlen($Rg);return$W;}function
xxtea_mx($Wi,$Vi,$Dh,$de){return
int32((($Wi>>5&0x7FFFFFF)^$Vi<<2)+(($Vi>>3&0x1FFFFFFF)^$Wi<<4))^int32(($Dh^$Vi)+($de^$Wi));}function
encrypt_string($yh,$z){if($yh=="")return"";$z=array_values(unpack("V*",pack("H*",md5($z))));$W=str2long($yh,true);$Xe=count($W)-1;$Wi=$W[$Xe];$Vi=$W[0];$I=floor(6+52/($Xe+1));$Dh=0;while($I-->0){$Dh=int32($Dh+0x9E3779B9);$cc=$Dh>>2&3;for($Kf=0;$Kf<$Xe;$Kf++){$Vi=$W[$Kf+1];$We=xxtea_mx($Wi,$Vi,$Dh,$z[$Kf&3^$cc]);$Wi=int32($W[$Kf]+$We);$W[$Kf]=$Wi;}$Vi=$W[0];$We=xxtea_mx($Wi,$Vi,$Dh,$z[$Kf&3^$cc]);$Wi=int32($W[$Xe]+$We);$W[$Xe]=$Wi;}return
long2str($W,false);}function
decrypt_string($yh,$z){if($yh=="")return"";if(!$z)return
false;$z=array_values(unpack("V*",pack("H*",md5($z))));$W=str2long($yh,false);$Xe=count($W)-1;$Wi=$W[$Xe];$Vi=$W[0];$I=floor(6+52/($Xe+1));$Dh=int32($I*0x9E3779B9);while($Dh){$cc=$Dh>>2&3;for($Kf=$Xe;$Kf>0;$Kf--){$Wi=$W[$Kf-1];$We=xxtea_mx($Wi,$Vi,$Dh,$z[$Kf&3^$cc]);$Vi=int32($W[$Kf]-$We);$W[$Kf]=$Vi;}$Wi=$W[$Xe];$We=xxtea_mx($Wi,$Vi,$Dh,$z[$Kf&3^$cc]);$Vi=int32($W[0]-$We);$W[0]=$Vi;$Dh=int32($Dh-0x9E3779B9);}return
long2str($W,true);}$bg=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($z)=explode(":",$X);$bg[$z]=$X;}}function
add_invalid_login(){$Da=get_temp_dir()."/adminer.invalid";foreach(glob("$Da*")?:array($Da)as$o){$q=file_open_lock($o);if($q)break;}if(!$q)$q=file_open_lock("$Da-".rand_string());if(!$q)return;$Vd=unserialize(stream_get_contents($q));$Vh=time();if($Vd){foreach($Vd
as$Wd=>$X){if($X[0]<$Vh)unset($Vd[$Wd]);}}$Ud=&$Vd[adminer()->bruteForceKey()];if(!$Ud)$Ud=array($Vh+30*60,0);$Ud[1]++;file_write_unlock($q,serialize($Vd));}function
check_invalid_login(array&$bg){$Vd=array();foreach(glob(get_temp_dir()."/adminer.invalid*")as$o){$q=file_open_lock($o);if($q){$Vd=unserialize(stream_get_contents($q));file_unlock($q);break;}}$Ud=idx($Vd,adminer()->bruteForceKey(),array());$ef=($Ud[1]>29?$Ud[0]-time():0);if($ef>0)auth_error(lang(83,ceil($ef/60)),$bg);}$xa=$_POST["auth"];if($xa){session_regenerate_id();$Ki=$xa["driver"];$P=$xa["server"];$V=$xa["username"];$H=(string)$xa["password"];$j=$xa["db"];set_password($Ki,$P,$V,$H);$_SESSION["db"][$Ki][$P][$V][$j]=true;if($xa["permanent"]){$z=implode("-",array_map('base64_encode',array($Ki,$P,$V,$j)));$pg=adminer()->permanentLogin(true);$bg[$z]="$z:".base64_encode($pg?encrypt_string($H,$pg):"");cookie("adminer_permanent",implode(" ",$bg));}if(count($_POST)==1||DRIVER!=$Ki||SERVER!=$P||$_GET["username"]!==$V||DB!=$j)redirect(auth_url($Ki,$P,$V,$j));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$z)set_session($z,null);unset_permanent($bg);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(84).' '.lang(85));}elseif($bg&&!$_SESSION["pwds"]){session_regenerate_id();$pg=adminer()->permanentLogin();foreach($bg
as$z=>$X){list(,$Xa)=explode(":",$X);list($Ki,$P,$V,$j)=array_map('base64_decode',explode("-",$z));set_password($Ki,$P,$V,decrypt_string(base64_decode($Xa),$pg));$_SESSION["db"][$Ki][$P][$V][$j]=true;}}function
unset_permanent(array&$bg){foreach($bg
as$z=>$X){list($Ki,$P,$V,$j)=array_map('base64_decode',explode("-",$z));if($Ki==DRIVER&&$P==SERVER&&$V==$_GET["username"]&&$j==DB)unset($bg[$z]);}cookie("adminer_permanent",implode(" ",$bg));}function
auth_error($l,array&$bg){$gh=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$gh]||$_GET[$gh])&&!$_SESSION["token"])$l=lang(86);else{restart_session();add_invalid_login();$H=get_password();if($H!==null){if($H===false)$l
.=($l?'<br>':'').lang(87,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);}unset_permanent($bg);}}if(!$_COOKIE[$gh]&&$_GET[$gh]&&ini_bool("session.use_only_cookies"))$l=lang(88);$Nf=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$Nf["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(29),$l,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth")))echo"<p class='message'>".lang(89)."\n";echo"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($bg);page_header(lang(90),lang(91,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$f='';if(isset($_GET["username"])&&is_string(get_password())){list(,$fg)=host_port(SERVER);if(preg_match('~^\s*([-+]?\d+)~',$fg,$C)&&($C[1]<1024||$C[1]>65535))auth_error(lang(92),$bg);check_invalid_login($bg);$wb=adminer()->credentials();$f=Driver::connect($wb[0],$wb[1],$wb[2]);if(is_object($f)){Db::$instance=$f;Driver::$instance=new
Driver($f);if($f->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$we=null;if(!is_object($f)||($we=adminer()->login($_GET["username"],get_password()))!==true){$l=(is_string($f)?nl_br(h($f)):(is_string($we)?$we:lang(93))).(preg_match('~^ | $~',get_password())?'<br>'.lang(94):'');auth_error($l,$bg);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(82),lang(95));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($xa&&$_POST["token"])$_POST["token"]=get_token();$l='';if($_POST){if(!verify_token()){$Nd="max_input_vars";$Ie=ini_get($Nd);if(extension_loaded("suhosin")){foreach(array("suhosin.request.max_vars","suhosin.post.max_vars")as$z){$X=ini_get($z);if($X&&(!$Ie||$X<$Ie)){$Nd=$z;$Ie=$X;}}}$l=(!$_POST["token"]&&$Ie?lang(96,"'$Nd'"):lang(95).' '.lang(97));}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){$l=lang(98,"'post_max_size'");if(isset($_GET["sql"]))$l
.=' '.lang(99);}function
print_select_result($K,$g=null,array$Bf=array(),$_=0){$ve=array();$x=array();$d=array();$Ia=array();$qi=array();$L=array();for($t=0;(!$_||$t<$_)&&($M=$K->fetch_row());$t++){if(!$t){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr>";for($y=0;$y<count($M);$y++){$m=$K->fetch_field();$E=$m->name;$Af=(isset($m->orgtable)?$m->orgtable:"");$_f=(isset($m->orgname)?$m->orgname:$E);if($Bf&&JUSH=="sql")$ve[$y]=($E=="table"?"table=":($E=="possible_keys"?"indexes=":null));elseif($Af!=""){if(isset($m->table))$L[$m->table]=$Af;if(!isset($x[$Af])){$x[$Af]=array();foreach(indexes($Af,$g)as$w){if($w["type"]=="PRIMARY"){$x[$Af]=array_flip($w["columns"]);break;}}$d[$Af]=$x[$Af];}if(isset($d[$Af][$_f])){unset($d[$Af][$_f]);$x[$Af][$_f]=$y;$ve[$y]=$Af;}}if($m->charsetnr==63)$Ia[$y]=true;$qi[$y]=$m->type;echo"<th".($Af!=""||$m->name!=$_f?" title='".h(($Af!=""?"$Af.":"").$_f)."'":"").">".h($E).($Bf?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($E),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}echo"</thead>\n";}echo"<tr>";foreach($M
as$z=>$X){$A="";if(isset($ve[$z])&&!$d[$ve[$z]]){if($Bf&&JUSH=="sql"){$R=$M[array_search("table=",$ve)];$A=ME.$ve[$z].urlencode($Bf[$R]!=""?$Bf[$R]:$R);}else{$A=ME."edit=".urlencode($ve[$z]);foreach($x[$ve[$z]]as$bb=>$y){if($M[$y]===null){$A="";break;}$A
.="&where".urlencode("[".bracket_escape($bb)."]")."=".urlencode($M[$y]);}}}elseif(is_url($X))$A=$X;if($X===null)$X="<i>NULL</i>";elseif($Ia[$z]&&!is_utf8($X))$X="<i>".lang(38,strlen($X))."</i>";else{$X=h($X);if($qi[$z]==254)$X="<code>$X</code>";}if($A)$X="<a href='".h($A)."'".(is_url($A)?target_blank():'').">$X</a>";echo"<td".($qi[$z]<=9||$qi[$z]==246?" class='number'":"").">$X";}}echo($t?"</table>\n</div>":"<p class='message'>".lang(14))."\n";return$L;}function
referencable_primary($bh){$L=array();foreach(table_status('',true)as$Hh=>$R){if($Hh!=$bh&&fk_support($R)){foreach(fields($Hh)as$m){if($m["primary"]){if($L[$Hh]){unset($L[$Hh]);break;}$L[$Hh]=$m;}}}}return$L;}function
textarea($E,$Y,$N=10,$eb=80){echo"<textarea name='".h($E)."' rows='$N' cols='$eb' class='sqlarea jush-".JUSH."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($wa,array$wf,$Y="",$rf="",$cg=""){$Oh=($wf?"select":"input");return"<$Oh$wa".($wf?"><option value=''>$cg".optionlist($wf,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$cg'>").($rf?script("qsl('$Oh').onchange = $rf;",""):"");}function
json_row($z,$X=null,$uc=true){static$Oc=true;if($Oc)echo"{";if($z!=""){echo($Oc?"":",")."\n\t\"".addcslashes($z,"\r\n\t\"\\/").'": '.($X!==null?($uc?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$Oc=false;}else{echo"\n}\n";$Oc=true;}}function
edit_type($z,array$m,array$b,array$Vc=array(),array$Fc=array()){$U=$m["type"];echo"<td><select name='".h($z)."[type]' class='type' aria-labelledby='label-type'>";if($U&&!array_key_exists($U,driver()->types())&&!isset($Vc[$U])&&!in_array($U,$Fc))$Fc[]=$U;$_h=driver()->structuredTypes();if($Vc)$_h[lang(100)]=$Vc;echo
optionlist(array_merge($Fc,$_h),$U),"</select><td>","<input name='".h($z)."[length]' value='".h($m["length"])."' size='3'".(!$m["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($b?"<input list='collations' name='".h($z)."[collation]'".(preg_match('~(char|text|enum|set)$~',$U)?"":" class='hidden'")." value='".h($m["collation"])."' placeholder='(".lang(101).")'>":''),(driver()->unsigned?"<select name='".h($z)."[unsigned]'".(!$U||preg_match(number_type(),$U)?"":" class='hidden'").'><option>'.optionlist(driver()->unsigned,$m["unsigned"]).'</select>':''),(isset($m['on_update'])?"<select name='".h($z)."[on_update]'".(preg_match('~timestamp|datetime~',$U)?"":" class='hidden'").'>'.optionlist(array(""=>"(".lang(102).")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"CURRENT_TIMESTAMP":$m["on_update"])).'</select>':''),($Vc?"<select name='".h($z)."[on_delete]'".(preg_match("~`~",$U)?"":" class='hidden'")."><option value=''>(".lang(103).")".optionlist(explode("|",driver()->onActions),$m["on_delete"])."</select> ":" ");}function
process_length($re){$pc=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$pc(?:\\s*,\\s*$pc)*+\\s*\\)?\\s*\$~",$re)&&preg_match_all("~$pc~",$re,$Ae)?"(".implode(",",$Ae[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$re)));}function
process_type(array$m,$cb="COLLATE"){return" $m[type]".process_length($m["length"]).(preg_match(number_type(),$m["type"])&&in_array($m["unsigned"],driver()->unsigned)?" $m[unsigned]":"").(preg_match('~char|text|enum|set~',$m["type"])&&$m["collation"]?" $cb ".(JUSH=="mssql"?$m["collation"]:q($m["collation"])):"");}function
process_field(array$m,array$oi){if($m["on_update"])$m["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$m["on_update"]);return
array(idf_escape(trim($m["field"])),process_type($oi),($m["null"]?" NULL":" NOT NULL"),default_value($m),(preg_match('~timestamp|datetime~',$m["type"])&&$m["on_update"]?" ON UPDATE $m[on_update]":""),(support("comment")&&$m["comment"]!=""?" COMMENT ".q($m["comment"]):""),($m["auto_increment"]?auto_increment():null),);}function
default_value(array$m){$k=$m["default"];$cd=$m["generated"];return($k===null?"":(in_array($cd,driver()->generated)?(JUSH=="mssql"?" AS ($k)".($cd=="VIRTUAL"?"":" $cd")."":" GENERATED ALWAYS AS ($k) $cd"):" DEFAULT ".(!preg_match('~^GENERATED ~i',$k)&&(preg_match('~char|binary|text|json|enum|set~',$m["type"])||preg_match('~^(?![a-z])~i',$k))?(JUSH=="sql"&&preg_match('~text|json~',$m["type"])?"(".q($k).")":q($k)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($k)":$k)))));}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$z=>$X){if(preg_match("~$z|$X~",$U))return" class='$z'";}}function
edit_fields(array$n,array$b,$U="TABLE",array$Vc=array()){$n=array_values($n);$Jb=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$ib=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?lang(104):lang(105)),"<td id='label-type'>".lang(40)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' style='display: none;'></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<td id='label-length'>".lang(106),"<td>".lang(107);if($U=="TABLE")echo"<td id='label-null'>NULL\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(42)."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",)),"<td id='label-default'$Jb>".lang(43),(support("comment")?"<td id='label-comment'$ib>".lang(41):"");echo"<td>".icon("plus","add[".(support("move_col")?0:count($n))."]","+",lang(108)),"</thead>\n<tbody>\n",script("mixin(qsl('tbody'), {onclick: editingClick, onkeydown: editingKeydown, oninput: editingInput});");foreach($n
as$t=>$m){$t++;$Cf=$m[($_POST?"orig":"field")];$Ub=(isset($_POST["add"][$t-1])||(isset($m["field"])&&!idx($_POST["drop_col"],$t)))&&(support("drop_col")||$Cf=="");echo"<tr".($Ub?"":" style='display: none;'").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$t][inout]",explode("|",driver()->inout),$m["inout"]):"")."<th>";if($Ub)echo"<input name='fields[$t][field]' value='".h($m["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$t-1])?" autofocus":"").">";echo
input_hidden("fields[$t][orig]",$Cf);edit_type("fields[$t]",$m,$b,$Vc);if($U=="TABLE")echo"<td>".checkbox("fields[$t][null]",1,$m["null"],"","","block","label-null"),"<td><label class='block'><input type='radio' name='auto_increment_col' value='$t'".($m["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$Jb>".(driver()->generated?html_select("fields[$t][generated]",array_merge(array("","DEFAULT"),driver()->generated),$m["generated"])." ":checkbox("fields[$t][generated]",1,$m["generated"],"","","","label-default")),"<input name='fields[$t][default]' value='".h($m["default"])."' aria-labelledby='label-default'>",(support("comment")?"<td$ib><input name='fields[$t][comment]' value='".h($m["comment"])."' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'>":"");echo"<td>",(support("move_col")?icon("plus","add[$t]","+",lang(108))." ".icon("up","up[$t]","↑",lang(109))." ".icon("down","down[$t]","↓",lang(110))." ":""),($Cf==""||support("drop_col")?icon("cross","drop_col[$t]","x",lang(111)):"");}}function
process_fields(array&$n){$jf=0;if($_POST["up"]){$le=0;foreach($n
as$z=>$m){if(key($_POST["up"])==$z){unset($n[$z]);array_splice($n,$le,0,array($m));break;}if(isset($m["field"]))$le=$jf;$jf++;}}elseif($_POST["down"]){$Xc=false;foreach($n
as$z=>$m){if(isset($m["field"])&&$Xc){unset($n[key($_POST["down"])]);array_splice($n,$jf,0,array($Xc));break;}if(key($_POST["down"])==$z)$Xc=$m;$jf++;}}elseif($_POST["add"]){$n=array_values($n);array_splice($n,key($_POST["add"]),0,array(array()));}elseif(!$_POST["drop_col"])return
false;return
true;}function
normalize_enum(array$C){$X=$C[0];return"'".str_replace("'","''",addcslashes(stripcslashes(str_replace($X[0].$X[0],$X[0],substr($X,1,-1))),'\\'))."'";}function
grant($ed,array$rg,$d,$pf){if(!$rg)return
true;if($rg==array("ALL PRIVILEGES","GRANT OPTION"))return($ed=="GRANT"?queries("$ed ALL PRIVILEGES$pf WITH GRANT OPTION"):queries("$ed ALL PRIVILEGES$pf")&&queries("$ed GRANT OPTION$pf"));return
queries("$ed ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$d, ",$rg).$d).$pf);}function
drop_create($Yb,$h,$Zb,$Sh,$ac,$B,$Oe,$Me,$Ne,$mf,$bf){if($_POST["drop"])query_redirect($Yb,$B,$Oe);elseif($mf=="")query_redirect($h,$B,$Ne);elseif($mf!=$bf){$vb=queries($h);queries_redirect($B,$Me,$vb&&queries($Yb));if($vb)queries($Zb);}else
queries_redirect($B,$Me,queries($Sh)&&queries($ac)&&queries($Yb)&&queries($h));}function
create_trigger($pf,array$M){$Xh=" $M[Timing] $M[Event]".(preg_match('~ OF~',$M["Event"])?" $M[Of]":"");return"CREATE TRIGGER ".idf_escape($M["Trigger"]).(JUSH=="mssql"?$pf.$Xh:$Xh.$pf).rtrim(" $M[Type]\n$M[Statement]",";").";";}function
create_routine($Og,array$M){$Q=array();$n=(array)$M["fields"];ksort($n);foreach($n
as$m){if($m["field"]!="")$Q[]=(preg_match("~^(".driver()->inout.")\$~",$m["inout"])?"$m[inout] ":"").idf_escape($m["field"]).process_type($m,"CHARACTER SET");}$Lb=rtrim($M["definition"],";");return"CREATE $Og ".idf_escape(trim($M["name"]))." (".implode(", ",$Q).")".($Og=="FUNCTION"?" RETURNS".process_type($M["returns"],"CHARACTER SET"):"").($M["language"]?" LANGUAGE $M[language]":"").(JUSH=="pgsql"?" AS ".q($Lb):"\n$Lb;");}function
remove_definer($J){return
preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~','`@`(%|\1)',logged_user()).'`~','\1',$J);}function
format_foreign_key(array$p){$j=$p["db"];$gf=$p["ns"];return" FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$p["source"])).") REFERENCES ".($j!=""&&$j!=$_GET["db"]?idf_escape($j).".":"").($gf!=""&&$gf!=$_GET["ns"]?idf_escape($gf).".":"").idf_escape($p["table"])." (".implode(", ",array_map('Adminer\idf_escape',$p["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$p["on_delete"])?" ON DELETE $p[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$p["on_update"])?" ON UPDATE $p[on_update]":"");}function
tar_file($o,$ci){$L=pack("a100a8a8a8a12a12",$o,644,0,0,decoct($ci->size),decoct(time()));$Wa=8*32;for($t=0;$t<strlen($L);$t++)$Wa+=ord($L[$t]);$L
.=sprintf("%06o",$Wa)."\0 ";echo$L,str_repeat("\0",512-strlen($L));$ci->send();echo
str_repeat("\0",511-($ci->size+511)%512);}function
doc_link(array$Yf,$Th="<sup>?</sup>"){$eh=connection()->server_info;$Li=preg_replace('~^(\d\.?\d).*~s','\1',$eh);$Ci=array('sql'=>"https://dev.mysql.com/doc/refman/$Li/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$Li)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://www.oracle.com/pls/topic/lookup?ctx=db".preg_replace('~^.* (\d+)\.(\d+)\.\d+\.\d+\.\d+.*~s','\1\2',$eh)."&id=",);if(connection()->flavor=='maria'){$Ci['sql']="https://mariadb.com/kb/en/";$Yf['sql']=(isset($Yf['mariadb'])?$Yf['mariadb']:str_replace(".html","/",$Yf['sql']));}return($Yf[JUSH]?"<a href='".h($Ci[JUSH].$Yf[JUSH].(JUSH=='mssql'?"?view=sql-server-ver$Li":""))."'".target_blank().">$Th</a>":"");}function
db_size($j){if(!connection()->select_db($j))return"?";$L=0;foreach(table_status()as$S)$L+=$S["Data_length"]+$S["Index_length"];return
format_number($L);}function
set_utf8mb4($h){static$Q=false;if(!$Q&&preg_match('~\butf8mb4~i',$h)){$Q=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!=""){header("HTTP/1.1 404 Not Found");page_header(lang(28).": ".h(DB),lang(112),true);}else{if($_POST["db"]&&!$l)queries_redirect(substr(ME,0,-1),lang(113),drop_databases($_POST["db"]));page_header(lang(114),$l,false);echo"<p class='links'>\n";foreach(array('database'=>lang(115),'privileges'=>lang(62),'processlist'=>lang(116),'variables'=>lang(117),'status'=>lang(118),)as$z=>$X){if(support($z))echo"<a href='".h(ME)."$z='>$X</a>\n";}echo"<p>".lang(119,get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".lang(120,"<b>".h(logged_user())."</b>")."\n";$i=adminer()->databases();if($i){$Ug=support("scheme");$b=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),"<thead><tr>".(support("database")?"<td>":"")."<th>".lang(28).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(121)."</a>":"")."<td>".lang(122)."<td>".lang(123)."<td>".lang(124)." - <a href='".h(ME)."dbsize=1'>".lang(125)."</a>".script("qsl('a').onclick = partial(ajaxSetHtml, '".js_escape(ME)."script=connect');","")."</thead>\n";$i=($_GET["dbsize"]?count_tables($i):array_flip($i));foreach($i
as$j=>$T){$Ng=h(ME)."db=".urlencode($j);$u=h("Db-".$j);echo"<tr>".(support("database")?"<td>".checkbox("db[]",$j,in_array($j,(array)$_POST["db"]),"","","",$u):""),"<th><a href='$Ng' id='$u'>".h($j)."</a>";$db=h(db_collation($j,$b));echo"<td>".(support("database")?"<a href='$Ng".($Ug?"&amp;ns=":"")."&amp;database=' title='".lang(58)."'>$db</a>":$db),"<td align='right'><a href='$Ng&amp;schema=' id='tables-".h($j)."' title='".lang(61)."'>".($_GET["dbsize"]?$T:"?")."</a>","<td align='right' id='size-".h($j)."'>".($_GET["dbsize"]?db_size($j):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".lang(126)." <span id='selected'></span></legend><div>\n".input_hidden("all").script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^db/)); };")."<input type='submit' name='drop' value='".lang(127)."'>".confirm()."\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}if(!empty(adminer()->plugins)){echo"<div class='plugins'>\n","<h3>".lang(128)."</h3>\n<ul>\n";foreach(adminer()->plugins
as$dg){$Pb=(method_exists($dg,'description')?$dg->description():"");if(!$Pb){$Eg=new
\ReflectionObject($dg);if(preg_match('~^/[\s*]+(.+)~',$Eg->getDocComment(),$C))$Pb=$C[1];}$Vg=(method_exists($dg,'screenshot')?$dg->screenshot():"");echo"<li><b>".get_class($dg)."</b>".h($Pb?": $Pb":"").($Vg?" (<a href='".h($Vg)."'".target_blank().">".lang(129)."</a>)":"")."\n";}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}adminer()->afterConnect();class
TmpFile{private$handler;var$size;function
__construct(){$this->handler=tmpfile();}function
write($pb){$this->size+=strlen($pb);fwrite($this->handler,$pb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$n=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$O=array(idf_escape($_GET["field"]));$K=driver()->select($a,$O,array(where($_GET,$n)),$O);$M=($K?$K->fetch_row():array());echo
driver()->value($M[0],$n[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$n=fields($a);if(!$n)$l=error()?:lang(11);$S=table_status1($a);$E=adminer()->tableName($S);page_header(($n&&is_view($S)?$S['Engine']=='materialized view'?lang(130):lang(131):lang(132)).": ".($E!=""?$E:h($a)),$l);$Mg=array();foreach($n
as$z=>$m)$Mg+=$m["privileges"];adminer()->selectLinks($S,(isset($Mg["insert"])||!support("table")?"":null));$hb=$S["Comment"];if($hb!="")echo"<p class='nowrap'>".lang(41).": ".h($hb)."\n";if($n)adminer()->tableStructurePrint($n,$S);function
tables_links(array$T){echo"<ul>\n";foreach($T
as$R)echo"<li><a href='".h(ME."table=".urlencode($R))."'>".h($R)."</a>";echo"</ul>\n";}$Md=driver()->inheritsFrom($a);if($Md){echo"<h3>".lang(133)."</h3>\n";tables_links($Md);}if(support("indexes")&&driver()->supportsIndex($S)){echo"<h3 id='indexes'>".lang(134)."</h3>\n";$x=indexes($a);if($x)adminer()->tableIndexesPrint($x,$S);echo'<p class="links"><a href="'.h(ME).'indexes='.urlencode($a).'">'.lang(135)."</a>\n";}if(!is_view($S)){if(fk_support($S)){echo"<h3 id='foreign-keys'>".lang(100)."</h3>\n";$Vc=foreign_keys($a);if($Vc){echo"<table>\n","<thead><tr><th>".lang(136)."<td>".lang(137)."<td>".lang(103)."<td>".lang(102)."<td></thead>\n";foreach($Vc
as$E=>$p){echo"<tr title='".h($E)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$p["source"]))."</i>";$A=($p["db"]!=""?preg_replace('~db=[^&]*~',"db=".urlencode($p["db"]),ME):($p["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".urlencode($p["ns"]),ME):ME));echo"<td><a href='".h($A."table=".urlencode($p["table"]))."'>".($p["db"]!=""&&$p["db"]!=DB?"<b>".h($p["db"])."</b>.":"").($p["ns"]!=""&&$p["ns"]!=$_GET["ns"]?"<b>".h($p["ns"])."</b>.":"").h($p["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$p["target"]))."</i>)","<td>".h($p["on_delete"]),"<td>".h($p["on_update"]),'<td><a href="'.h(ME.'foreign='.urlencode($a).'&name='.urlencode($E)).'">'.lang(138).'</a>',"\n";}echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'foreign='.urlencode($a).'">'.lang(139)."</a>\n";}if(support("check")){echo"<h3 id='checks'>".lang(140)."</h3>\n";$Ta=driver()->checkConstraints($a);if($Ta){echo"<table>\n";foreach($Ta
as$z=>$X)echo"<tr title='".h($z)."'>","<td><code class='jush-".JUSH."'>".h($X),"<td><a href='".h(ME.'check='.urlencode($a).'&name='.urlencode($z))."'>".lang(138)."</a>","\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'check='.urlencode($a).'">'.lang(141)."</a>\n";}}if(support(is_view($S)?"view_trigger":"trigger")){echo"<h3 id='triggers'>".lang(142)."</h3>\n";$ni=triggers($a);if($ni){echo"<table>\n";foreach($ni
as$z=>$X)echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($z)."<td><a href='".h(ME.'trigger='.urlencode($a).'&name='.urlencode($z))."'>".lang(138)."</a>\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'trigger='.urlencode($a).'">'.lang(143)."</a>\n";}$Ld=driver()->inheritedTables($a);if($Ld){echo"<h3 id='partitions'>".lang(144)."</h3>\n";$Qf=driver()->partitionsInfo($a);if($Qf)echo"<p><code class='jush-".JUSH."'>BY ".h("$Qf[partition_by]($Qf[partition])")."</code>\n";tables_links($Ld);}}elseif(isset($_GET["schema"])){page_header(lang(61),"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));$Ih=array();$Jh=array();$da=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$da,$Ae,PREG_SET_ORDER);foreach($Ae
as$t=>$C){$Ih[$C[1]]=array($C[2],$C[3]);$Jh[]="\n\t'".js_escape($C[1])."': [ $C[2], $C[3] ]";}$fi=0;$Ea=-1;$Tg=array();$Dg=array();$pe=array();$pa=driver()->allFields();foreach(table_status('',true)as$R=>$S){if(is_view($S))continue;$gg=0;$Tg[$R]["fields"]=array();foreach($pa[$R]as$m){$gg+=1.25;$m["pos"]=$gg;$Tg[$R]["fields"][$m["field"]]=$m;}$Tg[$R]["pos"]=($Ih[$R]?:array($fi,0));foreach(adminer()->foreignKeys($R)as$X){if(!$X["db"]){$ne=$Ea;if(idx($Ih[$R],1)||idx($Ih[$X["table"]],1))$ne=min(idx($Ih[$R],1,0),idx($Ih[$X["table"]],1,0))-1;else$Ea-=.1;while($pe[(string)$ne])$ne-=.0001;$Tg[$R]["references"][$X["table"]][(string)$ne]=array($X["source"],$X["target"]);$Dg[$X["table"]][$R][(string)$ne]=$X["target"];$pe[(string)$ne]=true;}}$fi=max($fi,$Tg[$R]["pos"][0]+2.5+$gg);}echo'<div id="schema" style="height: ',$fi,'em;">
>>>>>>> upstream/master
<script',nonce(),'>
qs(\'#schema\').onselectstart = () => false;
const tablePos = {',implode(",",$Jh)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$fi,';
document.onmousemove = schemaMousemove;
document.onmouseup = partialArg(schemaMouseup, \'',js_escape(DB),'\');
</script>
';foreach($Tg
as$E=>$R){echo"<div class='table' style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em;'>",'<a href="'.h(ME).'table='.urlencode($E).'"><b>'.h($E)."</b></a>",script("qsl('div').onmousedown = schemaMousedown;");foreach($R["fields"]as$m){$X='<span'.type_class($m["type"]).' title="'.h($m["type"].($m["length"]?"($m[length])":"").($m["null"]?" NULL":'')).'">'.h($m["field"]).'</span>';echo"<br>".($m["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$Qh=>$Fg){foreach($Fg
as$ne=>$Ag){$oe=$ne-idx($Ih[$E],1);$t=0;foreach($Ag[0]as$oh)echo"\n<div class='references' title='".h($Qh)."' id='refs$ne-".($t++)."' style='left: $oe"."em; top: ".$R["fields"][$oh]["pos"]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: ".(-$oe)."em;'></div></div>";}}foreach((array)$Dg[$E]as$Qh=>$Fg){foreach($Fg
as$ne=>$d){$oe=$ne-idx($Ih[$E],1);$t=0;foreach($d
as$Ph)echo"\n<div class='references arrow' title='".h($Qh)."' id='refd$ne-".($t++)."' style='left: $oe"."em; top: ".$R["fields"][$Ph]["pos"]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$oe)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($Tg
as$E=>$R){foreach((array)$R["references"]as$Qh=>$Fg){foreach($Fg
as$ne=>$Ag){$Re=$fi;$Ge=-10;foreach($Ag[0]as$z=>$oh){$hg=$R["pos"][0]+$R["fields"][$oh]["pos"];$ig=$Tg[$Qh]["pos"][0]+$Tg[$Qh]["fields"][$Ag[1][$z]]["pos"];$Re=min($Re,$hg,$ig);$Ge=max($Ge,$hg,$ig);}echo"<div class='references' id='refl$ne' style='left: $ne"."em; top: $Re"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($Ge-$Re)."em;'></div></div>\n";}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".urlencode($da)),'" id="schema-link">',lang(145),'</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$l){save_settings(array_intersect_key($_POST,array_flip(array("output","format","db_style","types","routines","events","table_style","auto_increment","triggers","data_style"))),"adminer_export");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$Bc=dump_headers((count($T)==1?key($T):DB),(DB==""||count($T)>1));$Zd=preg_match('~sql~',$_POST["format"]);if($Zd){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$Ah=$_POST["db_style"];$i=array(DB);if(DB==""){$i=$_POST["databases"];if(is_string($i))$i=explode("\n",rtrim(str_replace("\r","",$i),"\n"));}foreach((array)$i
as$j){adminer()->dumpDatabase($j);if(connection()->select_db($j)){if($Zd){if($Ah)echo
use_sql($j,$Ah).";\n\n";$If="";if($_POST["types"]){foreach(types()as$u=>$U){$qc=type_values($u);if($qc)$If
.=($Ah!='DROP+CREATE'?"DROP TYPE IF EXISTS ".idf_escape($U).";;\n":"")."CREATE TYPE ".idf_escape($U)." AS ENUM ($qc);\n\n";else$If
.="-- Could not export type $U\n\n";}}if($_POST["routines"]){foreach(routines()as$M){$E=$M["ROUTINE_NAME"];$Og=$M["ROUTINE_TYPE"];$h=create_routine($Og,array("name"=>$E)+routine($M["SPECIFIC_NAME"],$Og));set_utf8mb4($h);$If
.=($Ah!='DROP+CREATE'?"DROP $Og IF EXISTS ".idf_escape($E).";;\n":"")."$h;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$M){$h=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($M["Name"]),3));set_utf8mb4($h);$If
.=($Ah!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($M["Name"]).";;\n":"")."$h;;\n\n";}}echo($If&&JUSH=='sql'?"DELIMITER ;;\n\n$If"."DELIMITER ;\n\n":$If);}if($_POST["table_style"]||$_POST["data_style"]){$Ni=array();foreach(table_status('',true)as$E=>$S){$R=(DB==""||in_array($E,(array)$_POST["tables"]));$Bb=(DB==""||in_array($E,(array)$_POST["data"]));if($R||$Bb){$ci=null;if($Bc=="tar"){$ci=new
TmpFile;ob_start(array($ci,'write'),1e5);}adminer()->dumpTable($E,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$Ni[]=$E;elseif($Bb){$n=fields($E);adminer()->dumpData($E,$_POST["data_style"],"SELECT *".convert_fields($n,$n)." FROM ".table($E));}if($Zd&&$_POST["triggers"]&&$R&&($ni=trigger_sql($E)))echo"\nDELIMITER ;;\n$ni\nDELIMITER ;\n";if($Bc=="tar"){ob_end_flush();tar_file((DB!=""?"":"$j/")."$E.csv",$ci);}elseif($Zd)echo"\n";}}if(function_exists('Adminer\foreign_keys_sql')){foreach(table_status('',true)as$E=>$S){$R=(DB==""||in_array($E,(array)$_POST["tables"]));if($R&&!is_view($S))echo
foreign_keys_sql($E);}}foreach($Ni
as$Mi)adminer()->dumpTable($Mi,$_POST["table_style"],1);if($Bc=="tar")echo
pack("x512");}}}adminer()->dumpFooter();exit;}page_header(lang(67),$l,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$Fb=array('','USE','DROP+CREATE','CREATE');$Kh=array('','DROP+CREATE','CREATE');$Cb=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Cb[]='INSERT+UPDATE';$M=get_settings("adminer_export");if(!$M)$M=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"table_style"=>"DROP+CREATE","data_style"=>"INSERT");if(!isset($M["events"])){$M["routines"]=$M["events"]=($_GET["dump"]=="");$M["triggers"]=$M["table_style"];}echo"<tr><th>".lang(146)."<td>".html_radios("output",adminer()->dumpOutput(),$M["output"])."\n","<tr><th>".lang(147)."<td>".html_radios("format",adminer()->dumpFormat(),$M["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".lang(28)."<td>".html_select('db_style',$Fb,$M["db_style"]).(support("type")?checkbox("types",1,$M["types"],lang(6)):"").(support("routine")?checkbox("routines",1,$M["routines"],lang(63)):"").(support("event")?checkbox("events",1,$M["events"],lang(65)):"")),"<tr><th>".lang(123)."<td>".html_select('table_style',$Kh,$M["table_style"]).checkbox("auto_increment",1,$M["auto_increment"],lang(42)).(support("trigger")?checkbox("triggers",1,$M["triggers"],lang(142)):""),"<tr><th>".lang(148)."<td>".html_select('data_style',$Cb,$M["data_style"]),'</table>
<p><input type="submit" value="',lang(67),'">
',input_token(),'
<table>
',script("qsl('table').onclick = dumpClick;");$mg=array();if(DB!=""){$Ua=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$Ua>".lang(123)."</label>".script("qs('#check-tables').onclick = partial(formCheck, /^tables\\[/);",""),"<th style='text-align: right;'><label class='block'>".lang(148)."<input type='checkbox' id='check-data'$Ua></label>".script("qs('#check-data').onclick = partial(formCheck, /^data\\[/);",""),"</thead>\n";$Ni="";$Mh=tables_list();foreach($Mh
as$E=>$U){$lg=preg_replace('~_.*~','',$E);$Ua=($a==""||$a==(substr($a,-1)=="%"?"$lg%":$E));$og="<tr><td>".checkbox("tables[]",$E,$Ua,$E,"","block");if($U!==null&&!preg_match('~table~i',$U))$Ni
.="$og\n";else
echo"$og<td align='right'><label class='block'><span id='Rows-".h($E)."'></span>".checkbox("data[]",$E,$Ua)."</label>\n";$mg[$lg]++;}echo$Ni;if($Mh)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-databases'".($a==""?" checked":"").">".lang(28)."</label>",script("qs('#check-databases').onclick = partial(formCheck, /^databases\\[/);",""),"</thead>\n";$i=adminer()->databases();if($i){foreach($i
as$j){if(!information_schema($j)){$lg=preg_replace('~_.*~','',$j);echo"<tr><td>".checkbox("databases[]",$j,$a==""||$a=="$lg%",$j,"","block")."\n";$mg[$lg]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$Oc=true;foreach($mg
as$z=>$X){if($z!=""&&$X>1){echo($Oc?"<p>":" ")."<a href='".h(ME)."dump=".urlencode("$z%")."'>".h($z)."</a>";$Oc=false;}}}elseif(isset($_GET["privileges"])){page_header(lang(62));echo'<p class="links"><a href="'.h(ME).'user=">'.lang(149)."</a>";$K=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$ed=$K;if(!$K)$K=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($ed?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".lang(26)."<th>".lang(25)."<th></thead>\n";while($M=$K->fetch_assoc())echo'<tr><td>'.h($M["User"])."<td>".h($M["Host"]).'<td><a href="'.h(ME.'user='.urlencode($M["User"]).'&host='.urlencode($M["Host"])).'">'.lang(12)."</a>\n";if(!$ed||DB!="")echo"<tr><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='".lang(12)."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$l&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}restart_session();$td=&get_session("queries");$sd=&$td[DB];if(!$l&&$_POST["clear"]){$sd=array();redirect(remove_from_uri("history"));}stop_session();page_header((isset($_GET["import"])?lang(66):lang(55)),$l);$ue='--'.(JUSH=='sql'?' ':'');if(!$l&&$_POST){$q=false;if(!isset($_GET["import"]))$J=$_POST["query"];elseif($_POST["webfile"]){$rh=adminer()->importServerPath();$q=@fopen((file_exists($rh)?$rh:"compress.zlib://$rh.gz"),"rb");$J=($q?fread($q,1e6):false);}else$J=get_file("sql_file",true,";");if(is_string($J)){if(function_exists('memory_get_usage')&&($Le=ini_bytes("memory_limit"))!="-1")@ini_set("memory_limit",max($Le,strval(2*strlen($J)+memory_get_usage()+8e6)));if($J!=""&&strlen($J)<1e6){$I=$J.(preg_match("~;[ \t\r\n]*\$~",$J)?"":";");if(!$sd||first(end($sd))!=$I){restart_session();$sd[]=array($I,time());set_session("queries",$td);stop_session();}}$ph="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|$ue)[^\n]*\n?|--\r?\n)";$Nb=";";$jf=0;$kc=true;$g=connect();if($g&&DB!=""){$g->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$g);}$gb=0;$sc=array();$Of='[\'"'.(JUSH=="sql"?'`#':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$ue.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$gi=microtime(true);$ja=get_settings("adminer_import");while($J!=""){if(!$jf&&preg_match("~^$ph*+DELIMITER\\s+(\\S+)~i",$J,$C)){$Nb=preg_quote($C[1]);$J=substr($J,strlen($C[0]));}elseif(!$jf&&JUSH=='pgsql'&&preg_match("~^($ph*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$J,$C)){$Nb="\n\\\\\\.\r?\n";$jf=strlen($C[0]);}else{preg_match("($Nb\\s*|$Of)",$J,$C,PREG_OFFSET_CAPTURE,$jf);list($Xc,$gg)=$C[0];if(!$Xc&&$q&&!feof($q))$J
.=fread($q,1e5);else{if(!$Xc&&rtrim($J)=="")break;$jf=$gg+strlen($Xc);if($Xc&&!preg_match("(^$Nb)",$Xc)){$Na=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($gg>0&&strtolower($J[$gg-1])=="e"));$Zf=($Xc=='/*'?'\*/':($Xc=='['?']':(preg_match("~^$ue|^#~",$Xc)?"\n":preg_quote($Xc).($Na?'|\\\\.':''))));while(preg_match("($Zf|\$)s",$J,$C,PREG_OFFSET_CAPTURE,$jf)){$Rg=$C[0][0];if(!$Rg&&$q&&!feof($q))$J
.=fread($q,1e5);else{$jf=$C[0][1]+strlen($Rg);if(!$Rg||$Rg[0]!="\\")break;}}}else{$kc=false;$I=substr($J,0,$gg+($Nb[0]=="\n"?3:0));$gb++;$og="<pre id='sql-$gb'><code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($I)."</code></pre>\n";if(JUSH=="sqlite"&&preg_match("~^$ph*+ATTACH\\b~i",$I,$C)){echo$og,"<p class='error'>".lang(150)."\n";$sc[]=" <a href='#sql-$gb'>$gb</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$og;ob_flush();flush();}$vh=microtime(true);if(connection()->multi_query($I)&&$g&&preg_match("~^$ph*+USE\\b~i",$I))$g->query($I);do{$K=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$og:""),"<p class='error'>".lang(151).(connection()->errno?" (".connection()->errno.")":"").": ".error()."\n";$sc[]=" <a href='#sql-$gb'>$gb</a>";if($_POST["error_stops"])break
2;}else{$Vh=" <span class='time'>(".format_time($vh).")</span>".(strlen($I)<1000?" <a href='".h(ME)."sql=".urlencode(trim($I))."'>".lang(12)."</a>":"");$la=connection()->affected_rows;$Qi=($_POST["only_errors"]?"":driver()->warnings());$Ri="warnings-$gb";if($Qi)$Vh
.=", <a href='#$Ri'>".lang(37)."</a>".script("qsl('a').onclick = partial(toggle, '$Ri');","");$_c=null;$Bf=null;$Ac="explain-$gb";if(is_object($K)){$_=$_POST["limit"];$Bf=print_select_result($K,$g,array(),$_);if(!$_POST["only_errors"]){echo"<form action='' method='post'>\n";$hf=$K->num_rows;echo"<p class='sql-footer'>".($hf?($_&&$hf>$_?lang(152,$_):"").lang(153,$hf):""),$Vh;if($g&&preg_match("~^($ph|\\()*+SELECT\\b~i",$I)&&($_c=explain($g,$I)))echo", <a href='#$Ac'>Explain</a>".script("qsl('a').onclick = partial(toggle, '$Ac');","");$u="export-$gb";echo", <a href='#$u'>".lang(67)."</a>".script("qsl('a').onclick = partial(toggle, '$u');","")."<span id='$u' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ja["output"])." ".html_select("format",adminer()->dumpFormat(),$ja["format"]).input_hidden("query",$I)."<input type='submit' name='export' value='".lang(67)."'>".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$ph*+(CREATE|DROP|ALTER)$ph++(DATABASE|SCHEMA)\\b~i",$I)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(154,$la)."$Vh\n";}echo($Qi?"<div id='$Ri' class='hidden'>\n$Qi</div>\n":"");if($_c){echo"<div id='$Ac' class='hidden explain'>\n";print_select_result($_c,$g,$Bf);echo"</div>\n";}}$vh=microtime(true);}while(connection()->next_result());}$J=substr($J,$jf);$jf=0;}}}}if($kc)echo"<p class='message'>".lang(155)."\n";elseif($_POST["only_errors"])echo"<p class='message'>".lang(156,$gb-count($sc))," <span class='time'>(".format_time($gi).")</span>\n";elseif($sc&&$gb>1)echo"<p class='error'>".lang(151).": ".implode("",$sc)."\n";}else
echo"<p class='error'>".upload_error($J)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form">
';$yc="<input type='submit' value='".lang(157)."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$I=$_GET["sql"];if($_POST)$I=$_POST["query"];elseif($_GET["history"]=="all")$I=$sd;elseif($_GET["history"]!="")$I=idx($sd[$_GET["history"]],0);echo"<p>";textarea("query",$I,20);echo
script(($_POST?"":"qs('textarea').focus();\n")."qs('#form').onsubmit = partial(sqlSubmit, qs('#form'), '".js_escape(remove_from_uri("sql|limit|error_stops|only_errors|history"))."');"),"<p>";adminer()->sqlPrintAfter();echo"$yc\n",lang(158).": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$jd=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".lang(159)."</legend><div>",file_input("SQL$jd: <input type='file' name='sql_file[]' multiple>\n$yc"),"</div></fieldset>\n";$Cd=adminer()->importServerPath();if($Cd)echo"<fieldset><legend>".lang(160)."</legend><div>",lang(161,"<code>".h($Cd)."$jd</code>"),' <input type="submit" name="webfile" value="'.lang(162).'">',"</div></fieldset>\n";echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),lang(163))."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(164))."\n",input_token();if(!isset($_GET["import"])&&$sd){print_fieldset("history",lang(165),$_GET["history"]!="");for($X=end($sd);$X;$X=prev($sd)){$z=key($sd);list($I,$Vh,$fc)=$X;echo'<a href="'.h(ME."sql=&history=$z").'">'.lang(12)."</a>"." <span class='time' title='".@date('Y-m-d',$Vh)."'>".@date("H:i:s",$Vh)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(ltrim(str_replace("\n"," ",str_replace("\r","",preg_replace("~^(#|$ue).*~m",'',$I)))),80,"</code>").($fc?" <span class='time'>($fc)</span>":"")."<br>\n";}echo"<input type='submit' name='clear' value='".lang(166)."'>\n","<a href='".h(ME."sql=&history=all")."'>".lang(167)."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$n=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$n):""):where($_GET,$n));$yi=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($n
as$E=>$m){if(!isset($m["privileges"][$yi?"update":"insert"])||adminer()->fieldName($m)==""||$m["generated"])unset($n[$E]);}if($_POST&&!$l&&!isset($_GET["select"])){$B=$_POST["referer"];if($_POST["insert"])$B=($yi?null:$_SERVER["REQUEST_URI"]);elseif(!preg_match('~^.+&select=.+$~',$B))$B=ME."select=".urlencode($a);$x=indexes($a);$ti=unique_array($_GET["where"],$x);$xg="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($B,lang(168),driver()->delete($a,$xg,$ti?0:1));else{$Q=array();foreach($n
as$E=>$m){$X=process_input($m);if($X!==false&&$X!==null)$Q[idf_escape($E)]=$X;}if($yi){if(!$Q)redirect($B);queries_redirect($B,lang(169),driver()->update($a,$Q,$xg,$ti?0:1));if(is_ajax()){page_headers();page_messages($l);exit;}}else{$K=driver()->insert($a,$Q);$me=($K?last_id($K):0);queries_redirect($B,lang(170,($me?" $me":"")),$K);}}}$M=null;if($_POST["save"])$M=(array)$_POST["fields"];elseif($Z){$O=array();foreach($n
as$E=>$m){if(isset($m["privileges"]["select"])){$ua=($_POST["clone"]&&$m["auto_increment"]?"''":convert_field($m));$O[]=($ua?"$ua AS ":"").idf_escape($E);}}$M=array();if(!support("table"))$O=array("*");if($O){$K=driver()->select($a,$O,array($Z),$O,array(),(isset($_GET["select"])?2:1));if(!$K)$l=error();else{$M=$K->fetch_assoc();if(!$M)$M=false;}if(isset($_GET["select"])&&(!$M||$K->fetch_assoc()))$M=null;}}if(!support("table")&&!$n){if(!$Z){$K=driver()->select($a,array("*"),array(),array("*"));$M=($K?$K->fetch_assoc():false);if(!$M)$M=array(driver()->primary=>"");}if($M){foreach($M
as$z=>$X){if(!$Z)$M[$z]=null;$n[$z]=array("field"=>$z,"null"=>($z!=driver()->primary),"auto_increment"=>($z==driver()->primary));}}}edit_form($a,$n,$M,$yi,$l);}elseif(isset($_GET["create"])){$a=$_GET["create"];$Sf=driver()->partitionBy;$Wf=($Sf?driver()->partitionsInfo($a):array());$Cg=referencable_primary($a);$Vc=array();foreach($Cg
as$Hh=>$m)$Vc[str_replace("`","``",$Hh)."`".str_replace("`","``",$m["field"])]=$Hh;$Ef=array();$S=array();if($a!=""){$Ef=fields($a);$S=table_status1($a);if(count($S)<2)$l=lang(11);}$M=$_POST;$M["fields"]=(array)$M["fields"];if($M["auto_increment_col"])$M["fields"][$M["auto_increment_col"]]["auto_increment"]=true;if($_POST)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($M["fields"])&&!$l){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(171),drop_tables(array($a)));else{$n=array();$pa=array();$Di=false;$Tc=array();$Df=reset($Ef);$na=" FIRST";foreach($M["fields"]as$z=>$m){$p=$Vc[$m["type"]];$oi=($p!==null?$Cg[$p]:$m);if($m["field"]!=""){if(!$m["generated"])$m["default"]=null;$tg=process_field($m,$oi);$pa[]=array($m["orig"],$tg,$na);if(!$Df||$tg!==process_field($Df,$Df)){$n[]=array($m["orig"],$tg,$na);if($m["orig"]!=""||$na)$Di=true;}if($p!==null)$Tc[idf_escape($m["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$Vc[$m["type"]],'source'=>array($m["field"]),'target'=>array($oi["field"]),'on_delete'=>$m["on_delete"],));$na=" AFTER ".idf_escape($m["field"]);}elseif($m["orig"]!=""){$Di=true;$n[]=array($m["orig"]);}if($m["orig"]!=""){$Df=next($Ef);if(!$Df)$na="";}}$Uf=array();if(in_array($M["partition_by"],$Sf)){foreach($M
as$z=>$X){if(preg_match('~^partition~',$z))$Uf[$z]=$X;}foreach($Uf["partition_names"]as$z=>$E){if($E==""){unset($Uf["partition_names"][$z]);unset($Uf["partition_values"][$z]);}}$Uf["partition_names"]=array_values($Uf["partition_names"]);$Uf["partition_values"]=array_values($Uf["partition_values"]);if($Uf==$Wf)$Uf=array();}elseif(preg_match("~partitioned~",$S["Create_options"]))$Uf=null;$D=lang(172);if($a==""){cookie("adminer_engine",$M["Engine"]);$D=lang(173);}$E=trim($M["name"]);queries_redirect(ME.(support("table")?"table=":"select=").urlencode($E),$D,alter_table($a,$E,(JUSH=="sqlite"&&($Di||$Tc)?$pa:$n),$Tc,($M["Comment"]!=$S["Comment"]?$M["Comment"]:null),($M["Engine"]&&$M["Engine"]!=$S["Engine"]?$M["Engine"]:""),($M["Collation"]&&$M["Collation"]!=$S["Collation"]?$M["Collation"]:""),($M["Auto_increment"]!=""?number($M["Auto_increment"]):""),$Uf));}}page_header(($a!=""?lang(34):lang(68)),$l,array("table"=>$a),h($a));if(!$_POST){$qi=driver()->types();$M=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($qi["int"])?"int":(isset($qi["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$M=$S;$M["name"]=$a;$M["fields"]=array();if(!$_GET["auto_increment"])$M["Auto_increment"]="";foreach($Ef
as$m){$m["generated"]=$m["generated"]?:(isset($m["default"])?"DEFAULT":"");$M["fields"][]=$m;}if($Sf){$M+=$Wf;$M["partition_names"][]="";$M["partition_values"][]="";}}}$b=collations();if(is_array(reset($b)))$b=call_user_func_array('array_merge',array_values($b));$mc=driver()->engines();foreach($mc
as$lc){if(!strcasecmp($lc,$M["Engine"])){$M["Engine"]=$lc;break;}}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo
lang(174).": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($M["name"])."' autocapitalize='off'>\n",($mc?html_select("Engine",array(""=>"(".lang(175).")")+$mc,$M["Engine"]).on_help("event.target.value",1).script("qsl('select').onchange = helpClose;")."\n":"");if($b)echo"<datalist id='collations'>".optionlist($b)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($M["Collation"])."' placeholder='(".lang(101).")'>\n");echo"<input type='submit' value='".lang(16)."'>\n";}if(support("columns")){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($M["fields"],$b,"TABLE",$Vc);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",lang(42).": <input type='number' name='Auto_increment' class='size' value='".h($M["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),lang(176),"columnShow(this.checked, 5)","jsonly");$jb=($_POST?$_POST["comments"]:get_setting("comments"));echo(support("comment")?checkbox("comments",1,$jb,lang(41),"editingCommentsClick(this, true);","jsonly").' '.(preg_match('~\n~',$M["Comment"])?"<textarea name='Comment' rows='2' cols='20'".($jb?"":" class='hidden'").">".h($M["Comment"])."</textarea>":'<input name="Comment" value="'.h($M["Comment"]).'" data-maxlength="'.(min_version(5.5)?2048:60).'"'.($jb?"":" class='hidden'").'>'):''),'<p>
<input type="submit" value="',lang(16),'">
';}echo'
';if($a!="")echo'<input type="submit" name="drop" value="',lang(127),'">',confirm(lang(177,$a));if($Sf&&(JUSH=='sql'||$a=="")){$Tf=preg_match('~RANGE|LIST~',$M["partition_by"]);print_fieldset("partition",lang(178),$M["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$Sf),$M["partition_by"]).on_help("event.target.value.replace(/./, 'PARTITION BY \$&')",1).script("qsl('select').onchange = partitionByChange;"),"(<input name='partition' value='".h($M["partition"])."'>)\n",lang(179).": <input type='number' name='partitions' class='size".($Tf||!$M["partition_by"]?" hidden":"")."' value='".h($M["partitions"])."'>\n","<table id='partition-table'".($Tf?"":" class='hidden'").">\n","<thead><tr><th>".lang(180)."<th>".lang(181)."</thead>\n";foreach($M["partition_names"]as$z=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off">',($z==count($M["partition_names"])-1?script("qsl('input').oninput = partitionNameChange;"):''),'<td><input name="partition_values[]" value="'.h(idx($M["partition_values"],$z)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Jd=array("PRIMARY","UNIQUE","INDEX");$S=table_status1($a,true);$Hd=driver()->indexAlgorithms($S);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$Jd[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$Jd[]="SPATIAL";$x=indexes($a);$n=fields($a);$ng=array();if(JUSH=="mongo"){$ng=$x["_id_"];unset($Jd[0]);unset($x["_id_"]);}$M=$_POST;if($M)save_settings(array("index_options"=>$M["options"]));if($_POST&&!$l&&!$_POST["add"]&&!$_POST["drop_col"]){$qa=array();foreach($M["indexes"]as$w){$E=$w["name"];if(in_array($w["type"],$Jd)){$d=array();$se=array();$Qb=array();$Id=(support("partial_indexes")?$w["partial"]:"");$Gd=(in_array($w["algorithm"],$Hd)?$w["algorithm"]:"");$Q=array();ksort($w["columns"]);foreach($w["columns"]as$z=>$c){if($c!=""){$re=idx($w["lengths"],$z);$Ob=idx($w["descs"],$z);$Q[]=($n[$c]?idf_escape($c):$c).($re?"(".(+$re).")":"").($Ob?" DESC":"");$d[]=$c;$se[]=($re?:null);$Qb[]=$Ob;}}$zc=$x[$E];if($zc){ksort($zc["columns"]);ksort($zc["lengths"]);ksort($zc["descs"]);if($w["type"]==$zc["type"]&&array_values($zc["columns"])===$d&&(!$zc["lengths"]||array_values($zc["lengths"])===$se)&&array_values($zc["descs"])===$Qb&&$zc["partial"]==$Id&&(!$Hd||$zc["algorithm"]==$Gd)){unset($x[$E]);continue;}}if($d)$qa[]=array($w["type"],$E,$Q,$Gd,$Id);}}foreach($x
as$E=>$zc)$qa[]=array($zc["type"],$E,"DROP");if(!$qa)redirect(ME."table=".urlencode($a));queries_redirect(ME."table=".urlencode($a),lang(182),alter_indexes($a,$qa));}page_header(lang(134),$l,array("table"=>$a),h($a));$Lc=array_keys($n);if($_POST["add"]){foreach($M["indexes"]as$z=>$w){if($w["columns"][count($w["columns"])]!="")$M["indexes"][$z]["columns"][]="";}$w=end($M["indexes"]);if($w["type"]||array_filter($w["columns"],'strlen'))$M["indexes"][]=array("columns"=>array(1=>""));}if(!$M){foreach($x
as$z=>$w){$x[$z]["name"]=$z;$x[$z]["columns"][]="";}$x[]=array("columns"=>array(1=>""));$M["indexes"]=$x;}$se=(JUSH=="sql"||JUSH=="mssql");$jh=($_POST?$_POST["options"]:get_setting("index_options"));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap">
<thead><tr>
<th id="label-type">',lang(183);$Ad=" class='idxopts".($jh?"":" hidden")."'";if($Hd)echo"<th id='label-algorithm'$Ad>".lang(184).doc_link(array('sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/',));echo'<th><input type="submit" class="wayoff">',lang(185).($se?"<span$Ad> (".lang(186).")</span>":"");if($se||support("descidx"))echo
checkbox("options",1,$jh,lang(107),"indexOptionsShow(this.checked)","jsonly")."\n";echo'<th id="label-name">',lang(187);if(support("partial_indexes"))echo"<th id='label-condition'$Ad>".lang(188);echo'<th><noscript>',icon("plus","add[0]","+",lang(108)),'</noscript>
</thead>
';if($ng){echo"<tr><td>PRIMARY<td>";foreach($ng["columns"]as$z=>$c)echo
select_input(" disabled",$Lc,$c),"<label><input disabled type='checkbox'>".lang(50)."</label> ";echo"<td><td>\n";}$y=1;foreach($M["indexes"]as$w){if(!$_POST["drop_col"]||$y!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$y][type]",array(-1=>"")+$Jd,$w["type"],($y==count($M["indexes"])?"indexesAddRow.call(this);":""),"label-type");if($Hd)echo"<td$Ad>".html_select("indexes[$y][algorithm]",array_merge(array(""),$Hd),$w['algorithm'],"label-algorithm");echo"<td>";ksort($w["columns"]);$t=1;foreach($w["columns"]as$z=>$c){echo"<span>".select_input(" name='indexes[$y][columns][$t]' title='".lang(39)."'",($n&&($c==""||$n[$c])?array_combine($Lc,$Lc):array()),$c,"partial(".($t==count($w["columns"])?"indexesAddColumn":"indexesChangeColumn").", '".js_escape(JUSH=="sql"?"":$_GET["indexes"]."_")."')"),"<span$Ad>",($se?"<input type='number' name='indexes[$y][lengths][$t]' class='size' value='".h(idx($w["lengths"],$z))."' title='".lang(106)."'>":""),(support("descidx")?checkbox("indexes[$y][descs][$t]",1,idx($w["descs"],$z),lang(50)):""),"</span> </span>";$t++;}echo"<td><input name='indexes[$y][name]' value='".h($w["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$Ad><input name='indexes[$y][partial]' value='".h($w["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$y]","x",lang(111)).script("qsl('button').onclick = partial(editingRemoveRow, 'indexes\$1[type]');");}$y++;}echo'</table>
</div>
<p>
<input type="submit" value="',lang(16),'">
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$M=$_POST;if($_POST&&!$l&&!$_POST["add"]){$E=trim($M["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(189),drop_databases(array(DB)));}elseif(DB!==$E){if(DB!=""){$_GET["db"]=$E;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".urlencode($E),lang(190),rename_database($E,$M["collation"]));}else{$i=explode("\n",str_replace("\r","",$E));$Bh=true;$le="";foreach($i
as$j){if(count($i)==1||$j!=""){if(!create_database($j,$M["collation"]))$Bh=false;$le=$j;}}restart_session();set_session("dbs",null);queries_redirect(ME."db=".urlencode($le),lang(191),$Bh);}}else{if(!$M["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($E).(preg_match('~^[a-z0-9_]+$~i',$M["collation"])?" COLLATE $M[collation]":""),substr(ME,0,-1),lang(192));}}page_header(DB!=""?lang(58):lang(115),$l,array(),h(DB));$b=collations();$E=DB;if($_POST)$E=$M["name"];elseif(DB!="")$M["collation"]=db_collation(DB,$b);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$ed){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$ed,$C)&&$C[1]){$E=stripcslashes(idf_unescape("`$C[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($E,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($E).'</textarea><br>':'<input name="name" autofocus value="'.h($E).'" data-maxlength="64" autocapitalize="off">')."\n".($b?html_select("collation",array(""=>"(".lang(101).")")+$b,$M["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",)):""),'<input type="submit" value="',lang(16),'">
';if(DB!="")echo"<input type='submit' name='drop' value='".lang(127)."'>".confirm(lang(177,DB))."\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",lang(108))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ca=($_GET["name"]?:$_GET["call"]);page_header(lang(193).": ".h($ca),$l);$Og=routine($_GET["call"],(isset($_GET["callf"])?"FUNCTION":"PROCEDURE"));$Dd=array();$If=array();foreach($Og["fields"]as$t=>$m){if(substr($m["inout"],-3)=="OUT"&&JUSH=='sql')$If[$t]="@".idf_escape($m["field"])." AS ".idf_escape($m["field"]);if(!$m["inout"]||substr($m["inout"],0,2)=="IN")$Dd[]=$t;}if(!$l&&$_POST){$Oa=array();foreach($Og["fields"]as$z=>$m){$X="";if(in_array($z,$Dd)){$X=process_input($m);if($X===false)$X="''";if(isset($If[$z]))connection()->query("SET @".idf_escape($m["field"])." = $X");}if(isset($If[$z]))$Oa[]="@".idf_escape($m["field"]);elseif(in_array($z,$Dd))$Oa[]=$X;}$J=(isset($_GET["callf"])?"SELECT ":"CALL ").($Og["returns"]["type"]=="record"?"* FROM ":"").table($ca)."(".implode(", ",$Oa).")";$vh=microtime(true);$K=connection()->multi_query($J);$la=connection()->affected_rows;echo
adminer()->selectQuery($J,$vh,!$K);if(!$K)echo"<p class='error'>".error()."\n";else{$g=connect();if($g)$g->select_db(DB);do{$K=connection()->store_result();if(is_object($K))print_select_result($K,$g);else
echo"<p class='message'>".lang(194,$la)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($If)print_select_result(connection()->query("SELECT ".implode(", ",$If)));}}echo'
<form action="" method="post">
';if($Dd){echo"<table class='layout'>\n";foreach($Dd
as$z){$m=$Og["fields"][$z];$E=$m["field"];echo"<tr><th>".adminer()->fieldName($m);$Y=idx($_POST["fields"],$E);if($Y!=""){if($m["type"]=="set")$Y=implode(",",$Y);}input($m,$Y,idx($_POST["function"],$E,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type="submit" value="',lang(193),'">
',input_token(),'</form>

<pre>
';function
pre_tr($Rg){return
preg_replace('~^~m','<tr>',preg_replace('~\|~','<td>',preg_replace('~\|$~m',"",rtrim($Rg))));}$R='(\+--[-+]+\+\n)';$M='(\| .* \|\n)';echo
preg_replace_callback("~^$R?$M$R?($M*)$R?~m",function($C){$Pc=pre_tr($C[2]);return"<table>\n".($C[1]?"<thead>$Pc</thead>\n":$Pc).pre_tr($C[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($Og['comment']))));echo'</pre>
';}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$E=$_GET["name"];$M=$_POST;if($_POST&&!$l&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$M["source"]=array_filter($M["source"],'strlen');ksort($M["source"]);$Ph=array();foreach($M["source"]as$z=>$X)$Ph[$z]=$M["target"][$z];$M["target"]=$Ph;}if(JUSH=="sqlite")$K=recreate_table($a,$a,array(),array(),array(" $E"=>($M["drop"]?"":" ".format_foreign_key($M))));else{$qa="ALTER TABLE ".table($a);$K=($E==""||queries("$qa DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($E)));if(!$M["drop"])$K=queries("$qa ADD".format_foreign_key($M));}queries_redirect(ME."table=".urlencode($a),($M["drop"]?lang(195):($E!=""?lang(196):lang(197))),$K);if(!$M["drop"])$l=lang(198);}page_header(lang(199),$l,array("table"=>$a),h($a));if($_POST){ksort($M["source"]);if($_POST["add"])$M["source"][]="";elseif($_POST["change"]||$_POST["change-js"])$M["target"]=array();}elseif($E!=""){$Vc=foreign_keys($a);$M=$Vc[$E];$M["source"][]="";}else{$M["table"]=$a;$M["source"]=array("");}echo'
<form action="" method="post">
';$oh=array_keys(fields($a));if($M["db"]!="")connection()->select_db($M["db"]);if($M["ns"]!=""){$Ff=get_schema();set_schema($M["ns"]);}$Bg=array_keys(array_filter(table_status('',true),'Adminer\fk_support'));$Ph=array_keys(fields(in_array($M["table"],$Bg)?$M["table"]:reset($Bg)));$rf="this.form['change-js'].value = '1'; this.form.submit();";echo"<p><label>".lang(200).": ".html_select("table",$Bg,$M["table"],$rf)."</label>\n";if(JUSH!="sqlite"){$Gb=array();foreach(adminer()->databases()as$j){if(!information_schema($j))$Gb[]=$j;}echo"<label>".lang(69).": ".html_select("db",$Gb,$M["db"]!=""?$M["db"]:$_GET["db"],$rf)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type="submit" name="change" value="',lang(201),'"></noscript>
<table>
<thead><tr><th id="label-source">',lang(136),'<th id="label-target">',lang(137),'</thead>
';$y=0;foreach($M["source"]as$z=>$X){echo"<tr>","<td>".html_select("source[".(+$z)."]",array(-1=>"")+$oh,$X,($y==count($M["source"])-1?"foreignAddRow.call(this);":""),"label-source"),"<td>".html_select("target[".(+$z)."]",$Ph,idx($M["target"],$z),"","label-target");$y++;}echo'</table>
<p>
<label>',lang(103),': ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$M["on_delete"]),'</label>
<label>',lang(102),': ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$M["on_update"]),'</label>
',doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",)),'<p>
<input type="submit" value="',lang(16),'">
<noscript><p><input type="submit" name="add" value="',lang(202),'"></noscript>
';if($E!="")echo'<input type="submit" name="drop" value="',lang(127),'">',confirm(lang(177,$E));echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$M=$_POST;$Gf="VIEW";if(JUSH=="pgsql"&&$a!=""){$wh=table_status1($a);$Gf=strtoupper($wh["Engine"]);}if($_POST&&!$l){$E=trim($M["name"]);$ua=" AS\n$M[select]";$B=ME."table=".urlencode($E);$D=lang(203);$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$E&&JUSH!="sqlite"&&$U=="VIEW"&&$Gf=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($E).$ua,$B,$D);else{$Rh=$E."_adminer_".uniqid();drop_create("DROP $Gf ".table($a),"CREATE $U ".table($E).$ua,"DROP $U ".table($E),"CREATE $U ".table($Rh).$ua,"DROP $U ".table($Rh),($_POST["drop"]?substr(ME,0,-1):$B),lang(204),$D,lang(205),$a,$E);}}if(!$_POST&&$a!=""){$M=view($a);$M["name"]=$a;$M["materialized"]=($Gf!="VIEW");if(!$l)$l=error();}page_header(($a!=""?lang(35):lang(206)),$l,array("table"=>$a),h($a));echo'
<form action="" method="post">
<p>',lang(187),': <input name="name" value="',h($M["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$M["materialized"],lang(130)):""),'<p>';textarea("select",$M["select"]);echo'<p>
<input type="submit" value="',lang(16),'">
';if($a!="")echo'<input type="submit" name="drop" value="',lang(127),'">',confirm(lang(177,$a));echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$Td=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$xh=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$M=$_POST;if($_POST&&!$l){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),lang(207));elseif(in_array($M["INTERVAL_FIELD"],$Td)&&isset($xh[$M["STATUS"]])){$Sg="\nON SCHEDULE ".($M["INTERVAL_VALUE"]?"EVERY ".q($M["INTERVAL_VALUE"])." $M[INTERVAL_FIELD]".($M["STARTS"]?" STARTS ".q($M["STARTS"]):"").($M["ENDS"]?" ENDS ".q($M["ENDS"]):""):"AT ".q($M["STARTS"]))." ON COMPLETION".($M["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?lang(208):lang(209)),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$Sg.($aa!=$M["EVENT_NAME"]?"\nRENAME TO ".idf_escape($M["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($M["EVENT_NAME"]).$Sg)."\n".$xh[$M["STATUS"]]." COMMENT ".q($M["EVENT_COMMENT"]).rtrim(" DO\n$M[EVENT_DEFINITION]",";").";"));}}page_header(($aa!=""?lang(210).": ".h($aa):lang(211)),$l);if(!$M&&$aa!=""){$N=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$M=reset($N);}echo'
<form action="" method="post">
<table class="layout">
<tr><th>',lang(187),'<td><input name="EVENT_NAME" value="',h($M["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">',lang(212),'<td><input name="STARTS" value="',h("$M[EXECUTE_AT]$M[STARTS]"),'">
<tr><th title="datetime">',lang(213),'<td><input name="ENDS" value="',h($M["ENDS"]),'">
<tr><th>',lang(214),'<td><input type="number" name="INTERVAL_VALUE" value="',h($M["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$Td,$M["INTERVAL_FIELD"]),'<tr><th>',lang(118),'<td>',html_select("STATUS",$xh,$M["STATUS"]),'<tr><th>',lang(41),'<td><input name="EVENT_COMMENT" value="',h($M["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$M["ON_COMPLETION"]=="PRESERVE",lang(215)),'</table>
<p>';textarea("EVENT_DEFINITION",$M["EVENT_DEFINITION"]);echo'<p>
<input type="submit" value="',lang(16),'">
';if($aa!="")echo'<input type="submit" name="drop" value="',lang(127),'">',confirm(lang(177,$aa));echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ca=($_GET["name"]?:$_GET["procedure"]);$Og=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$M=$_POST;$M["fields"]=(array)$M["fields"];if($_POST&&!process_fields($M["fields"])&&!$l){$Cf=routine($_GET["procedure"],$Og);$Rh="$M[name]_adminer_".uniqid();foreach($M["fields"]as$z=>$m){if($m["field"]=="")unset($M["fields"][$z]);}drop_create("DROP $Og ".routine_id($ca,$Cf),create_routine($Og,$M),"DROP $Og ".routine_id($M["name"],$M),create_routine($Og,array("name"=>$Rh)+$M),"DROP $Og ".routine_id($Rh,$M),substr(ME,0,-1),lang(216),lang(217),lang(218),$ca,$M["name"]);}page_header(($ca!=""?(isset($_GET["function"])?lang(219):lang(220)).": ".h($ca):(isset($_GET["function"])?lang(221):lang(222))),$l);if(!$_POST){if($ca=="")$M["language"]="sql";else{$M=routine($_GET["procedure"],$Og);$M["name"]=$ca;}}$b=get_vals("SHOW CHARACTER SET");sort($b);$Pg=routine_languages();echo($b?"<datalist id='collations'>".optionlist($b)."</datalist>":""),'
<form action="" method="post" id="form">
<p>',lang(187),': <input name="name" value="',h($M["name"]),'" data-maxlength="64" autocapitalize="off">
',($Pg?"<label>".lang(21).": ".html_select("language",$Pg,$M["language"])."</label>\n":""),'<input type="submit" value="',lang(16),'">
<div class="scrollable">
<table class="nowrap">
';edit_fields($M["fields"],$b,$Og);if(isset($_GET["function"])){echo"<tr><td>".lang(223);edit_type("returns",(array)$M["returns"],$b,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$M["definition"],20);echo'<p>
<input type="submit" value="',lang(16),'">
';if($ca!="")echo'<input type="submit" name="drop" value="',lang(127),'">',confirm(lang(177,$ca));echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$E=$_GET["name"];$M=$_POST;if($M&&!$l){if(JUSH=="sqlite")$K=recreate_table($a,$a,array(),array(),array(),"",array(),"$E",($M["drop"]?"":$M["clause"]));else{$K=($E==""||queries("ALTER TABLE ".table($a)." DROP CONSTRAINT ".idf_escape($E)));if(!$M["drop"])$K=queries("ALTER TABLE ".table($a)." ADD".($M["name"]!=""?" CONSTRAINT ".idf_escape($M["name"]):"")." CHECK ($M[clause])");}queries_redirect(ME."table=".urlencode($a),($M["drop"]?lang(224):($E!=""?lang(225):lang(226))),$K);}page_header(($E!=""?lang(227).": ".h($E):lang(141)),$l,array("table"=>$a));if(!$M){$Va=driver()->checkConstraints($a);$M=array("name"=>$E,"clause"=>$Va[$E]);}echo'
<form action="" method="post">
<p>';if(JUSH!="sqlite")echo
lang(187).': <input name="name" value="'.h($M["name"]).'" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",),"?"),'<p>';textarea("clause",$M["clause"]);echo'<p><input type="submit" value="',lang(16),'">
';if($E!="")echo'<input type="submit" name="drop" value="',lang(127),'">',confirm(lang(177,$E));echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$E="$_GET[name]";$mi=trigger_options();$M=(array)trigger($E,$a)+array("Trigger"=>$a."_bi");if($_POST){if(!$l&&in_array($_POST["Timing"],$mi["Timing"])&&in_array($_POST["Event"],$mi["Event"])&&in_array($_POST["Type"],$mi["Type"])){$pf=" ON ".table($a);$Yb="DROP TRIGGER ".idf_escape($E).(JUSH=="pgsql"?$pf:"");$B=ME."table=".urlencode($a);if($_POST["drop"])query_redirect($Yb,$B,lang(228));else{if($E!="")queries($Yb);queries_redirect($B,($E!=""?lang(229):lang(230)),queries(create_trigger($pf,$_POST)));if($E!="")queries(create_trigger($pf,$M+array("Type"=>reset($mi["Type"]))));}}$M=$_POST;}page_header(($E!=""?lang(231).": ".h($E):lang(232)),$l,array("table"=>$a));echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>',lang(233),'<td>',html_select("Timing",$mi["Timing"],$M["Timing"],"triggerChange(/^".preg_quote($a,"/")."_[ba][iud]$/, '".js_escape($a)."', this.form);"),'<tr><th>',lang(234),'<td>',html_select("Event",$mi["Event"],$M["Event"],"this.form['Timing'].onchange();"),(in_array("UPDATE OF",$mi["Event"])?" <input name='Of' value='".h($M["Of"])."' class='hidden'>":""),'<tr><th>',lang(40),'<td>',html_select("Type",$mi["Type"],$M["Type"]),'</table>
<p>',lang(187),': <input name="Trigger" value="',h($M["Trigger"]),'" data-maxlength="64" autocapitalize="off">
',script("qs('#form')['Timing'].onchange();"),'<p>';textarea("Statement",$M["Statement"]);echo'<p>
<input type="submit" value="',lang(16),'">
';if($E!="")echo'<input type="submit" name="drop" value="',lang(127),'">',confirm(lang(177,$E));echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){$ea=$_GET["user"];$rg=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$M){foreach(explode(",",($M["Privilege"]=="Grant option"?"":$M["Context"]))as$qb)$rg[$qb][$M["Privilege"]]=$M["Comment"];}$rg["Server Admin"]+=$rg["File access on server"];$rg["Databases"]["Create routine"]=$rg["Procedures"]["Create routine"];unset($rg["Procedures"]["Create routine"]);$rg["Columns"]=array();foreach(array("Select","Insert","Update","References")as$X)$rg["Columns"][$X]=$rg["Tables"][$X];unset($rg["Server Admin"]["Usage"]);foreach($rg["Tables"]as$z=>$X)unset($rg["Databases"][$z]);$af=array();if($_POST){foreach($_POST["objects"]as$z=>$X)$af[$X]=(array)$af[$X]+idx($_POST["grants"],$z,array());}$fd=array();$nf="";if(isset($_GET["host"])&&($K=connection()->query("SHOW GRANTS FOR ".q($ea)."@".q($_GET["host"])))){while($M=$K->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$M[0],$C)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$C[1],$Ae,PREG_SET_ORDER)){foreach($Ae
as$X){if($X[1]!="USAGE")$fd["$C[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$M[0]))$fd["$C[2]$X[2]"]["GRANT OPTION"]=true;}}if(preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~",$M[0],$C))$nf=$C[1];}}if($_POST&&!$l){$of=(isset($_GET["host"])?q($ea)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $of",ME."privileges=",lang(235));else{$cf=q($_POST["user"])."@".q($_POST["host"]);$Xf=$_POST["pass"];if($Xf!=''&&!$_POST["hashed"]&&!min_version(8)){$Xf=get_val("SELECT PASSWORD(".q($Xf).")");$l=!$Xf;}$vb=false;if(!$l){if($of!=$cf){$vb=queries((min_version(5)?"CREATE USER":"GRANT USAGE ON *.* TO")." $cf IDENTIFIED BY ".(min_version(8)?"":"PASSWORD ").q($Xf));$l=!$vb;}elseif($Xf!=$nf)queries("SET PASSWORD FOR $cf = ".q($Xf));}if(!$l){$Lg=array();foreach($af
as$if=>$ed){if(isset($_GET["grant"]))$ed=array_filter($ed);$ed=array_keys($ed);if(isset($_GET["grant"]))$Lg=array_diff(array_keys(array_filter($af[$if],'strlen')),$ed);elseif($of==$cf){$lf=array_keys((array)$fd[$if]);$Lg=array_diff($lf,$ed);$ed=array_diff($ed,$lf);unset($fd[$if]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$if,$C)&&(!grant("REVOKE",$Lg,$C[2]," ON $C[1] FROM $cf")||!grant("GRANT",$ed,$C[2]," ON $C[1] TO $cf"))){$l=true;break;}}}if(!$l&&isset($_GET["host"])){if($of!=$cf)queries("DROP USER $of");elseif(!isset($_GET["grant"])){foreach($fd
as$if=>$Lg){if(preg_match('~^(.+)(\(.*\))?$~U',$if,$C))grant("REVOKE",array_keys($Lg),$C[2]," ON $C[1] FROM $cf");}}}queries_redirect(ME."privileges=",(isset($_GET["host"])?lang(236):lang(237)),!$l);if($vb)connection()->query("DROP USER $cf");}}page_header((isset($_GET["host"])?lang(26).": ".h("$ea@$_GET[host]"):lang(149)),$l,array("privileges"=>array('',lang(62))));$M=$_POST;if($M)$fd=$af;else{$M=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$M["pass"]=$nf;if($nf!="")$M["hashed"]=true;$fd[(DB==""||$fd?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>',lang(25),'<td><input name="host" data-maxlength="60" value="',h($M["host"]),'" autocapitalize="off">
<tr><th>',lang(26),'<td><input name="user" data-maxlength="80" value="',h($M["user"]),'" autocapitalize="off">
<tr><th>',lang(27),'<td><input name="pass" id="pass" value="',h($M["pass"]),'" autocomplete="new-password">
',($M["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8)?"":checkbox("hashed",1,$M["hashed"],lang(238),"typePassword(this.form['pass'], this.checked);")),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".lang(62).doc_link(array('sql'=>"grant.html#priv_level"));$t=0;foreach($fd
as$if=>$ed){echo'<th>'.($if!="*.*"?"<input name='objects[$t]' value='".h($if)."' size='10' autocapitalize='off'>":input_hidden("objects[$t]","*.*")."*.*");$t++;}echo"</thead>\n";foreach(array(""=>"","Server Admin"=>lang(25),"Databases"=>lang(28),"Tables"=>lang(132),"Columns"=>lang(39),"Procedures"=>lang(239),)as$qb=>$Ob){foreach((array)$rg[$qb]as$qg=>$hb){echo"<tr><td".($Ob?">$Ob<td":" colspan='2'").' lang="en" title="'.h($hb).'">'.h($qg);$t=0;foreach($fd
as$if=>$ed){$E="'grants[$t][".h(strtoupper($qg))."]'";$Y=$ed[strtoupper($qg)];if($qb=="Server Admin"&&$if!=(isset($fd["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$E><option><option value='1'".($Y?" selected":"").">".lang(240)."<option value='0'".($Y=="0"?" selected":"").">".lang(241)."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$E value='1'".($Y?" checked":"").($qg=="All privileges"?" id='grants-$t-all'>":">".($qg=="Grant option"?"":script("qsl('input').onclick = function () { if (this.checked) formUncheck('grants-$t-all'); };"))),"</label>";$t++;}}}echo"</table>\n",'<p>
<input type="submit" value="',lang(16),'">
';if(isset($_GET["host"]))echo'<input type="submit" name="drop" value="',lang(127),'">',confirm(lang(177,"$ea@$_GET[host]"));echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$l){$ge=0;foreach((array)$_POST["kill"]as$X){if(adminer()->killProcess($X))$ge++;}queries_redirect(ME."processlist=",lang(242,$ge),$ge||!$_POST["kill"]);}}page_header(lang(116),$l);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds">
',script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});");$t=-1;foreach(adminer()->processList()as$t=>$M){if(!$t){echo"<thead><tr lang='en'>".(support("kill")?"<th>":"");foreach($M
as$z=>$X)echo"<th>$z".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($z),));echo"</thead>\n";}echo"<tr>".(support("kill")?"<td>".checkbox("kill[]",$M[JUSH=="sql"?"Id":"pid"],0):"");foreach($M
as$z=>$X)echo"<td>".((JUSH=="sql"&&$z=="Info"&&preg_match("~Query|Killed~",$M["Command"])&&$X!="")||(JUSH=="pgsql"&&$z=="current_query"&&$X!="<IDLE>")||(JUSH=="oracle"&&$z=="sql_text"&&$X!="")?"<code class='jush-".JUSH."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(ME.($M["db"]!=""?"db=".urlencode($M["db"])."&":"")."sql=".urlencode($X)).'">'.lang(243).'</a>':h($X));echo"\n";}echo'</table>
</div>
<p>
';if(support("kill"))echo($t+1)."/".lang(244,max_connections()),"<p><input type='submit' value='".lang(245)."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif(isset($_GET["select"])){$a=$_GET["select"];$S=table_status1($a);$x=indexes($a);$n=fields($a);$Vc=column_foreign_keys($a);$kf=$S["Oid"];$ka=get_settings("adminer_import");$Mg=array();$d=array();$Xg=array();$zf=array();$Uh="";foreach($n
as$z=>$m){$E=adminer()->fieldName($m);$Ye=html_entity_decode(strip_tags($E),ENT_QUOTES);if(isset($m["privileges"]["select"])&&$E!=""){$d[$z]=$Ye;if(is_shortable($m))$Uh=adminer()->selectLengthProcess();}if(isset($m["privileges"]["where"])&&$E!="")$Xg[$z]=$Ye;if(isset($m["privileges"]["order"])&&$E!="")$zf[$z]=$Ye;$Mg+=$m["privileges"];}list($O,$s)=adminer()->selectColumnsProcess($d,$x);$O=array_unique($O);$s=array_unique($s);$Xd=count($s)<count($O);$Z=adminer()->selectSearchProcess($n,$x);$yf=adminer()->selectOrderProcess($n,$x);$_=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$ui=>$M){$ua=convert_field($n[key($M)]);$O=array($ua?:idf_escape(key($M)));$Z[]=where_check($ui,$n);$L=driver()->select($a,$O,$Z,$O);if($L)echo
first($L->fetch_row());}exit;}$ng=$wi=array();foreach($x
as$w){if($w["type"]=="PRIMARY"){$ng=array_flip($w["columns"]);$wi=($O?$ng:array());foreach($wi
as$z=>$X){if(in_array(idf_escape($z),$O))unset($wi[$z]);}break;}}if($kf&&!$ng){$ng=$wi=array($kf=>0);$x[]=array("type"=>"PRIMARY","columns"=>array($kf));}if($_POST&&!$l){$Ti=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$Va=array();foreach($_POST["check"]as$Sa)$Va[]=where_check($Sa,$n);$Ti[]="((".implode(") OR (",$Va)."))";}$Ti=($Ti?"\nWHERE ".implode(" AND ",$Ti):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$Zc=($O?implode(", ",$O):"*").convert_fields($d,$n,$O)."\nFROM ".table($a);$hd=($s&&$Xd?"\nGROUP BY ".implode(", ",$s):"").($yf?"\nORDER BY ".implode(", ",$yf):"");$J="SELECT $Zc$Ti$hd";if(is_array($_POST["check"])&&!$ng){$si=array();foreach($_POST["check"]as$X)$si[]="(SELECT".limit($Zc,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$n).$hd,1).")";$J=implode(" UNION ALL ",$si);}adminer()->dumpData($a,"table",$J);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$Vc)){if($_POST["save"]||$_POST["delete"]){$K=true;$la=0;$Q=array();if(!$_POST["delete"]){foreach($_POST["fields"]as$E=>$X){$X=process_input($n[$E]);if($X!==null&&($_POST["clone"]||$X!==false))$Q[idf_escape($E)]=($X!==false?$X:idf_escape($E));}}if($_POST["delete"]||$Q){$J=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($Q)).")\nSELECT ".implode(", ",$Q)."\nFROM ".table($a):"");if($_POST["all"]||($ng&&is_array($_POST["check"]))||$Xd){$K=($_POST["delete"]?driver()->delete($a,$Ti):($_POST["clone"]?queries("INSERT $J$Ti".driver()->insertReturning($a)):driver()->update($a,$Q,$Ti)));$la=connection()->affected_rows;if(is_object($K))$la+=$K->num_rows;}else{foreach((array)$_POST["check"]as$X){$Si="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$n);$K=($_POST["delete"]?driver()->delete($a,$Si,1):($_POST["clone"]?queries("INSERT".limit1($a,$J,$Si)):driver()->update($a,$Q,$Si,1)));if(!$K)break;$la+=connection()->affected_rows;}}}$D=lang(246,$la);if($_POST["clone"]&&$K&&$la==1){$me=last_id($K);if($me)$D=lang(170," $me");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page":""),$D,$K);if(!$_POST["delete"]){$jg=(array)$_POST["fields"];edit_form($a,array_intersect_key($n,$jg),$jg,!$_POST["clone"],$l);page_footer();exit;}}elseif(!$_POST["import"]){if(!$_POST["val"])$l=lang(247);else{$K=true;$la=0;foreach($_POST["val"]as$ui=>$M){$Q=array();foreach($M
as$z=>$X){$z=bracket_escape($z,true);$Q[idf_escape($z)]=(preg_match('~char|text~',$n[$z]["type"])||$X!=""?adminer()->processInput($n[$z],$X):"NULL");}$K=driver()->update($a,$Q," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($ui,$n),($Xd||$ng?0:1)," ");if(!$K)break;$la+=connection()->affected_rows;}queries_redirect(remove_from_uri(),lang(246,$la),$K);}}elseif(!is_string($Mc=get_file("csv_file",true)))$l=upload_error($Mc);elseif(!preg_match('~~u',$Mc))$l=lang(248);else{save_settings(array("output"=>$ka["output"],"format"=>$_POST["separator"]),"adminer_import");$K=true;$eb=array_keys($n);preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Mc,$Ae);$la=count($Ae[0]);driver()->begin();$dh=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$N=array();foreach($Ae[0]as$z=>$X){preg_match_all("~((?>\"[^\"]*\")+|[^$dh]*)$dh~",$X.$dh,$Be);if(!$z&&!array_diff($Be[1],$eb)){$eb=$Be[1];$la--;}else{$Q=array();foreach($Be[1]as$t=>$bb)$Q[idf_escape($eb[$t])]=($bb==""&&$n[$eb[$t]]["null"]?"NULL":q(preg_match('~^".*"$~s',$bb)?str_replace('""','"',substr($bb,1,-1)):$bb));$N[]=$Q;}}$K=(!$N||driver()->insertUpdate($a,$N,$ng));if($K)driver()->commit();queries_redirect(remove_from_uri("page"),lang(249,$la),$K);driver()->rollback();}}}$Hh=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(44).": $Hh",$l);$Q=null;if(isset($Mg["insert"])||!support("table")){$Nf=array();foreach((array)$_GET["where"]as$X){if(isset($Vc[$X["col"]])&&count($Vc[$X["col"]])==1&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$X["val"])))))$Nf["set"."[".bracket_escape($X["col"])."]"]=$X["val"];}$Q=$Nf?"&".http_build_query($Nf):"";}adminer()->selectLinks($S,$Q);if(!$d&&support("table"))echo"<p class='error'>".lang(250).($n?".":": ".error())."\n";else{echo"<form action='' id='form'>\n","<div style='display: none;'>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($O,$d);adminer()->selectSearchPrint($Z,$Xg,$x);adminer()->selectOrderPrint($yf,$zf,$x);adminer()->selectLimitPrint($_);adminer()->selectLengthPrint($Uh);adminer()->selectActionPrint($x);echo"</form>\n";$G=$_GET["page"];$Yc=null;if($G=="last"){$Yc=get_val(count_rows($a,$Z,$Xd,$s));$G=floor(max(0,intval($Yc)-1)/$_);}$Yg=$O;$gd=$s;if(!$Yg){$Yg[]="*";$rb=convert_fields($d,$n,$O);if($rb)$Yg[]=substr($rb,2);}foreach($O
as$z=>$X){$m=$n[idf_unescape($X)];if($m&&($ua=convert_field($m)))$Yg[$z]="$ua AS $X";}if(!$Xd&&$wi){foreach($wi
as$z=>$X){$Yg[]=idf_escape($z);if($gd)$gd[]=idf_escape($z);}}$K=driver()->select($a,$Yg,$Z,$gd,$yf,$_,$G,true);if(!$K)echo"<p class='error'>".error()."\n";else{if(JUSH=="mssql"&&$G)$K->seek($_*$G);$jc=array();echo"<form action='' method='post' enctype='multipart/form-data'>\n";$N=array();while($M=$K->fetch_assoc()){if($G&&JUSH=="oracle")unset($M["RNUM"]);$N[]=$M;}if($_GET["page"]!="last"&&$_&&$s&&$Xd&&JUSH=="sql")$Yc=get_val(" SELECT FOUND_ROWS()");if(!$N)echo"<p class='message'>".lang(14)."\n";else{$Ca=adminer()->backwardKeys($a,$Hh);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'>",script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"),"<thead><tr>".(!$s&&$O?"":"<td><input type='checkbox' id='all-page' class='jsonly'>".script("qs('#all-page').onclick = partial(formCheck, /check/);","")." <a href='".h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."'>".lang(251)."</a>");$Ze=array();$bd=array();reset($O);$zg=1;foreach($N[0]as$z=>$X){if(!isset($wi[$z])){$X=idx($_GET["columns"],key($O))?:array();$m=$n[$O?($X?$X["col"]:current($O)):$z];$E=($m?adminer()->fieldName($m,$zg):($X["fun"]?"*":h($z)));if($E!=""){$zg++;$Ze[$z]=$E;$c=idf_escape($z);$wd=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($z);$Ob="&desc%5B0%5D=1";echo"<th id='th[".h(bracket_escape($z))."]'>".script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});","");$ad=apply_sql_function($X["fun"],$E);$nh=isset($m["privileges"]["order"])||$ad;echo($nh?"<a href='".h($wd.($yf[0]==$c||$yf[0]==$z?$Ob:''))."'>$ad</a>":$ad),"<span class='column hidden'>";if($nh)echo"<a href='".h($wd.$Ob)."' title='".lang(50)."' class='text'> ↓</a>";if(!$X["fun"]&&isset($m["privileges"]["where"]))echo'<a href="#fieldset-search" title="'.lang(47).'" class="text jsonly"> =</a>',script("qsl('a').onclick = partial(selectSearch, '".js_escape($z)."');");echo"</span>";}$bd[$z]=$X["fun"];next($O);}}$se=array();if($_GET["modify"]){foreach($N
as$M){foreach($M
as$z=>$X)$se[$z]=max($se[$z],min(40,strlen(utf8_decode($X))));}}echo($Ca?"<th>".lang(252):"")."</thead>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($N,$Vc)as$Xe=>$M){$ti=unique_array($N[$Xe],$x);if(!$ti){$ti=array();reset($O);foreach($N[$Xe]as$z=>$X){if(!preg_match('~^(COUNT|AVG|GROUP_CONCAT|MAX|MIN|SUM)\(~',current($O)))$ti[$z]=$X;next($O);}}$ui="";foreach($ti
as$z=>$X){$m=(array)$n[$z];if((JUSH=="sql"||JUSH=="pgsql")&&preg_match('~char|text|enum|set~',$m["type"])&&strlen($X)>64){$z=(strpos($z,'(')?$z:idf_escape($z));$z="MD5(".(JUSH!='sql'||preg_match("~^utf8~",$m["collation"])?$z:"CONVERT($z USING ".charset(connection()).")").")";$X=md5($X);}$ui
.="&".($X!==null?urlencode("where[".bracket_escape($z)."]")."=".urlencode($X===false?"f":$X):"null%5B%5D=".urlencode($z));}echo"<tr>".(!$s&&$O?"":"<td>".checkbox("check[]",substr($ui,1),in_array(substr($ui,1),(array)$_POST["check"])).($Xd||information_schema(DB)?"":" <a href='".h(ME."edit=".urlencode($a).$ui)."' class='edit'>".lang(253)."</a>"));reset($O);foreach($M
as$z=>$X){if(isset($Ze[$z])){$c=current($O);$m=(array)$n[$z];$X=driver()->value($X,$m);if($X!=""&&(!isset($jc[$z])||$jc[$z]!=""))$jc[$z]=(is_mail($X)?$Ze[$z]:"");$A="";if(is_blob($m)&&$X!="")$A=ME.'download='.urlencode($a).'&field='.urlencode($z).$ui;if(!$A&&$X!==null){foreach((array)$Vc[$z]as$p){if(count($Vc[$z])==1||end($p["source"])==$z){$A="";foreach($p["source"]as$t=>$oh)$A
.=where_link($t,$p["target"][$t],$N[$Xe][$oh]);$A=($p["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.urlencode($p["db"]),ME):ME).'select='.urlencode($p["table"]).$A;if($p["ns"])$A=preg_replace('~([?&]ns=)[^&]+~','\1'.urlencode($p["ns"]),$A);if(count($p["source"])==1)break;}}}if($c=="COUNT(*)"){$A=ME."select=".urlencode($a);$t=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$ti))$A
.=where_link($t++,$W["col"],$W["val"],$W["op"]);}foreach($ti
as$de=>$W)$A
.=where_link($t++,$de,$W);}$xd=select_value($X,$A,$m,$Uh);$u=h("val[$ui][".bracket_escape($z)."]");$kg=idx(idx($_POST["val"],$ui),bracket_escape($z));$ec=!is_array($M[$z])&&is_utf8($xd)&&$N[$Xe][$z]==$M[$z]&&!$bd[$z]&&!$m["generated"];$U=(preg_match('~^(AVG|MIN|MAX)\((.+)\)~',$c,$C)?$n[idf_unescape($C[2])]["type"]:$m["type"]);$Th=preg_match('~text|json|lob~',$U);$Yd=preg_match(number_type(),$U)||preg_match('~^(CHAR_LENGTH|ROUND|FLOOR|CEIL|TIME_TO_SEC|COUNT|SUM)\(~',$c);echo"<td id='$u'".($Yd&&($X===null||is_numeric(strip_tags($xd))||$U=="money")?" class='number'":"");if(($_GET["modify"]&&$ec&&$X!==null)||$kg!==null){$kd=h($kg!==null?$kg:$M[$z]);echo">".($Th?"<textarea name='$u' cols='30' rows='".(substr_count($M[$z],"\n")+1)."'>$kd</textarea>":"<input name='$u' value='$kd' size='$se[$z]'>");}else{$xe=strpos($xd,"<i>…</i>");echo" data-text='".($xe?2:($Th?1:0))."'".($ec?"":" data-warning='".h(lang(254))."'").">$xd";}}next($O);}if($Ca)echo"<td>";adminer()->backwardKeysPrint($Ca,$N[$Xe]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($N||$G){$xc=true;if($_GET["page"]!="last"){if(!$_||(count($N)<$_&&($N||!$G)))$Yc=($G?$G*$_:0)+count($N);elseif(JUSH!="sql"||!$Xd){$Yc=($Xd?false:found_rows($S,$Z));if(intval($Yc)<max(1e4,2*($G+1)*$_))$Yc=first(slow_query(count_rows($a,$Z,$Xd,$s)));else$xc=false;}}$Lf=($_&&($Yc===false||$Yc>$_||$G));if($Lf)echo(($Yc===false?count($N)+1:$Yc-$G*$_)>$_?'<p><a href="'.h(remove_from_uri("page")."&page=".($G+1)).'" class="loadmore">'.lang(255).'</a>'.script("qsl('a').onclick = partial(selectLoadMore, $_, '".lang(256)."…');",""):''),"\n";echo"<div class='footer'><div>\n";if($Lf){$Fe=($Yc===false?$G+(count($N)>=$_?2:1):floor(($Yc-1)/$_));echo"<fieldset>";if(JUSH!="simpledb"){echo"<legend><a href='".h(remove_from_uri("page"))."'>".lang(257)."</a></legend>",script("qsl('a').onclick = function () { pageClick(this.href, +prompt('".lang(257)."', '".($G+1)."')); return false; };"),pagination(0,$G).($G>5?" …":"");for($t=max(1,$G-4);$t<min($Fe,$G+5);$t++)echo
pagination($t,$G);if($Fe>0)echo($G+5<$Fe?" …":""),($xc&&$Yc!==false?pagination($Fe,$G):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Fe'>".lang(258)."</a>");}else
echo"<legend>".lang(257)."</legend>",pagination(0,$G).($G>1?" …":""),($G?pagination($G,$G):""),($Fe>$G?pagination($G+1,$G).($Fe>$G+1?" …":""):"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(259)."</legend>";$Vb=($xc?"":"~ ").$Yc;$sf="const checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$Vb' : checked); selectCount('selected2', this.checked || !checked ? '$Vb' : checked);";echo
checkbox("all",1,0,($Yc!==false?($xc?"":"~ ").lang(153,$Yc):""),$sf)."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':' class="jsonly"'),'><legend>',lang(251),'</legend><div>
<input type="submit" value="',lang(16),'"',($_GET["modify"]?'':' title="'.lang(247).'"'),'>
</div></fieldset>
<fieldset><legend>',lang(126),' <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="',lang(12),'">
<input type="submit" name="clone" value="',lang(243),'">
<input type="submit" name="delete" value="',lang(20),'">',confirm(),'</div></fieldset>
<<<<<<< HEAD
';
                    }$Wc = adminer()->dumpFormat();
                    foreach ((array) $_GET['columns'] as $c) {
                        if ($c['fun']) {
                            unset($Wc['sql']);
                            break;
                        }
                    }if ($Wc) {
                        print_fieldset('export', lang(67)." <span id='selected2'></span>");
                        $Jf = adminer()->dumpOutput();
                        echo ($Jf ? html_select('output', $Jf, $ka['output']).' ' : ''),html_select('format', $Wc, $ka['format'])," <input type='submit' name='export' value='".lang(67)."'>\n","</div></fieldset>\n";
                    }adminer()->selectEmailPrint(array_filter($jc, 'strlen'), $d);
                    echo "</div></div>\n";
                }if (adminer()->selectImportPrint()) {
                    echo '<p>',"<a href='#import'>".lang(66).'</a>',script("qsl('a').onclick = partial(toggle, 'import');", ''),"<span id='import'".($_POST['import'] ? '' : " class='hidden'").'>: ',file_input("<input type='file' name='csv_file'> ".html_select('separator', ['csv'=> 'CSV,', 'csv;'=>'CSV;', 'tsv'=>'TSV'], $ka['format'])." <input type='submit' name='import' value='".lang(66)."'>"),'</span>';
                }echo input_token(),"</form>\n",(! $s && $O ? '' : script('tableCheck();'));
            }
        }
    }if (is_ajax()) {
        ob_end_clean();
        exit;
    }
} elseif (isset($_GET['variables'])) {
    $wh = isset($_GET['status']);
    page_header($wh ? lang(118) : lang(117));
    $Ji = ($wh ? show_status() : show_variables());
    if (! $Ji) {
        echo "<p class='message'>".lang(14)."\n";
    } else {
        echo "<table>\n";
        foreach ($Ji as $M) {
            echo '<tr>';
            $z = array_shift($M);
            echo "<th><code class='jush-".JUSH.($wh ? 'status' : 'set')."'>".h($z).'</code>';
            foreach ($M as $X) {
                echo '<td>'.nl_br(h($X));
            }
        }echo "</table>\n";
    }
} elseif (isset($_GET['script'])) {
    header('Content-Type: text/javascript; charset=utf-8');
    if ($_GET['script'] == 'db') {
        $Eh = ['Data_length'=>0, 'Index_length'=>0, 'Data_free'=>0];
        foreach (table_status() as $E=>$S) {
            json_row("Comment-$E", h($S['Comment']));
            if (! is_view($S) || preg_match('~materialized~i', $S['Engine'])) {
                foreach (['Engine', 'Collation'] as $z) {
                    json_row("$z-$E", h($S[$z]));
                }foreach ($Eh + ['Auto_increment'=>0, 'Rows'=>0] as $z=>$X) {
                    if ($S[$z] != '') {
                        $X = format_number($S[$z]);
                        if ($X >= 0) {
                            json_row("$z-$E", ($z == 'Rows' && $X && $S['Engine'] == (JUSH == 'pgsql' ? 'table' : 'InnoDB') ? "~ $X" : $X));
                        }if (isset($Eh[$z])) {
                            $Eh[$z] += ($S['Engine'] != 'InnoDB' || $z != 'Data_free' ? $S[$z] : 0);
                        }
                    } elseif (array_key_exists($z, $S)) {
                        json_row("$z-$E", '?');
                    }
                }
            }
        }foreach ($Eh as $z=>$X) {
            json_row("sum-$z", format_number($X));
        }json_row('');
    } elseif ($_GET['script'] == 'kill') {
        connection()->query('KILL '.number($_POST['kill']));
    } else {
        foreach (count_tables(adminer()->databases()) as $j=>$X) {
            json_row("tables-$j", $X);
            json_row("size-$j", db_size($j));
        }json_row('');
    }exit;
} else {
    $Nh = array_merge((array) $_POST['tables'], (array) $_POST['views']);
    if ($Nh && ! $l && ! $_POST['search']) {
        $K = true;
        $D = '';
        if (JUSH == 'sql' && $_POST['tables'] && count($_POST['tables']) > 1 && ($_POST['drop'] || $_POST['truncate'] || $_POST['copy'])) {
            queries('SET foreign_key_checks = 0');
        }if ($_POST['truncate']) {
            if ($_POST['tables']) {
                $K = truncate_tables($_POST['tables']);
            }$D = lang(260);
        } elseif ($_POST['move']) {
            $K = move_tables((array) $_POST['tables'], (array) $_POST['views'], $_POST['target']);
            $D = lang(261);
        } elseif ($_POST['copy']) {
            $K = copy_tables((array) $_POST['tables'], (array) $_POST['views'], $_POST['target']);
            $D = lang(262);
        } elseif ($_POST['drop']) {
            if ($_POST['views']) {
                $K = drop_views($_POST['views']);
            }if ($K && $_POST['tables']) {
                $K = drop_tables($_POST['tables']);
            }$D = lang(263);
        } elseif (JUSH == 'sqlite' && $_POST['check']) {
            foreach ((array) $_POST['tables'] as $R) {
                foreach (get_rows('PRAGMA integrity_check('.q($R).')') as $M) {
                    $D
                    .= '<b>'.h($R).'</b>: '.h($M['integrity_check']).'<br>';
                }
            }
        } elseif (JUSH != 'sql') {
            $K = (JUSH == 'sqlite' ? queries('VACUUM') : apply_queries('VACUUM'.($_POST['optimize'] ? '' : ' ANALYZE'),$_POST['tables']));
            $D = lang(264);
        } elseif (! $_POST['tables']) {
            $D = lang(11);
        } elseif ($K = queries(($_POST['optimize'] ? 'OPTIMIZE' : ($_POST['check'] ? 'CHECK' : ($_POST['repair'] ? 'REPAIR' : 'ANALYZE'))).' TABLE '.implode(', ',array_map('Adminer\idf_escape',$_POST['tables'])))) {
            while ($M = $K->fetch_assoc()) {
                $D
                .= '<b>'.h($M['Table']).'</b>: '.h($M['Msg_text']).'<br>';
            }
        }queries_redirect(substr(ME,0,-1),$D,$K);
    }page_header(($_GET['ns'] == '' ? lang(28).': '.h(DB) : lang(265).': '.h($_GET['ns'])),$l,true);
    if (adminer()->homepage()) {
        if ($_GET['ns'] !== '') {
            echo "<h3 id='tables-views'>".lang(266)."</h3>\n";
            $Mh = tables_list();
            if (! $Mh) {
                echo "<p class='message'>".lang(11)."\n";
            } else {
                echo "<form action='' method='post'>\n";
                if (support('table')) {
                    echo '<fieldset><legend>'.lang(267)." <span id='selected2'></span></legend><div>",html_select('op',adminer()->operators(),idx($_POST,'op',JUSH == 'elastic' ? 'should' : 'LIKE %%'))," <input type='search' name='query' value='".h($_POST['query'])."'>",script("qsl('input').onkeydown = partialArg(bodyKeydown, 'search');",'')," <input type='submit' name='search' value='".lang(47)."'>\n","</div></fieldset>\n";
                    if ($_POST['search'] && $_POST['query'] != '') {
                        $_GET['where'][0]['op'] = $_POST['op'];
                        search_tables();
                    }
                }echo "<div class='scrollable'>\n","<table class='nowrap checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr class="wrap">','<td><input id="check-all" type="checkbox" class="jsonly">'.script("qs('#check-all').onclick = partial(formCheck, /^(tables|views)\[/);",''),'<th>'.lang(132),'<td>'.lang(268).doc_link(['sql'=> 'storage-engines.html']),'<td>'.lang(122).doc_link(['sql'=>'charset-charsets.html', 'mariadb'=>'supported-character-sets-and-collations/']),'<td>'.lang(269).doc_link(['sql'=>'show-table-status.html']),'<td>'.lang(270).doc_link(['sql'=>'show-table-status.html']),'<td>'.lang(271).doc_link(['sql'=>'show-table-status.html']),'<td>'.lang(42).doc_link(['sql'=>'example-auto-increment.html', 'mariadb'=>'auto_increment/']),'<td>'.lang(272).doc_link(['sql'=>'show-table-status.html']),(support('comment') ? '<td>'.lang(41).doc_link(['sql'=>'show-table-status.html']) : ''),"</thead>\n";
                $T = 0;
                foreach ($Mh as $E=>$U) {
                    $Mi = ($U !== null && ! preg_match('~table|sequence~i',$U));
                    $u = h('Table-'.$E);
                    echo '<tr><td>'.checkbox(($Mi ? 'views[]' : 'tables[]'),$E,in_array("$E",$Nh,true),'','','',$u),'<th>'.(support('table') || support('indexes') ? "<a href='".h(ME).'table='.urlencode($E)."' title='".lang(33)."' id='$u'>".h($E).'</a>' : h($E));
                    if ($Mi && ! preg_match('~materialized~i',$U)) {
                        $Yh = lang(131);
                        echo '<td colspan="6">'.(support('view') ? "<a href='".h(ME).'view='.urlencode($E)."' title='".lang(35)."'>$Yh</a>" : $Yh),'<td align="right"><a href="'.h(ME).'select='.urlencode($E).'" title="'.lang(32).'">?</a>';
                    } else {
                        foreach (['Engine'=>[], 'Collation'=>[], 'Data_length'=>['create', lang(34)], 'Index_length'=>['indexes', lang(135)], 'Data_free'=>['edit', lang(36)], 'Auto_increment'=>['auto_increment=1&create', lang(34)], 'Rows'=>['select', lang(32)]] as $z=>$A) {
                            $u = " id='$z-".h($E)."'";
                            echo $A ? "<td align='right'>".(support('table') || $z == 'Rows' || (support('indexes') && $z != 'Data_length') ? "<a href='".h(ME."$A[0]=").urlencode($E)."'$u title='$A[1]'>?</a>" : "<span$u>?</span>") : "<td id='$z-".h($E)."'>";
                        }$T++;
                    }echo (support('comment') ? "<td id='Comment-".h($E)."'>" : ''),"\n";
                }echo '<tr><td><th>'.lang(244,count($Mh)),'<td>'.h(JUSH == 'sql' ? get_val('SELECT @@default_storage_engine') : ''),'<td>'.h(db_collation(DB,collations()));
                foreach (['Data_length', 'Index_length', 'Data_free'] as $z) {
                    echo "<td align='right' id='sum-$z'>";
                }echo "\n","</table>\n",script("ajaxSetHtml('".js_escape(ME)."script=db');"),"</div>\n";
                if (! information_schema(DB)) {
                    echo "<div class='footer'><div>\n";
                    $Hi = "<input type='submit' value='".lang(273)."'> ".on_help("'VACUUM'");
                    $vf = "<input type='submit' name='optimize' value='".lang(274)."'> ".on_help(JUSH == 'sql' ? "'OPTIMIZE TABLE'" : "'VACUUM OPTIMIZE'");
                    echo '<fieldset><legend>'.lang(126)." <span id='selected'></span></legend><div>".(JUSH == 'sqlite' ? $Hi."<input type='submit' name='check' value='".lang(275)."'> ".on_help("'PRAGMA integrity_check'") : (JUSH == 'pgsql' ? $Hi.$vf : (JUSH == 'sql' ? "<input type='submit' value='".lang(276)."'> ".on_help("'ANALYZE TABLE'").$vf."<input type='submit' name='check' value='".lang(275)."'> ".on_help("'CHECK TABLE'")."<input type='submit' name='repair' value='".lang(277)."'> ".on_help("'REPAIR TABLE'") : '')))."<input type='submit' name='truncate' value='".lang(278)."'> ".on_help(JUSH == 'sqlite' ? "'DELETE'" : "'TRUNCATE".(JUSH == 'pgsql' ? "'" : " TABLE'")).confirm()."<input type='submit' name='drop' value='".lang(127)."'>".on_help("'DROP TABLE'").confirm()."\n";
                    $i = (support('scheme') ? adminer()->schemas() : adminer()->databases());
                    echo "</div></fieldset>\n";
                    $Wg = '';
                    if (count($i) != 1 && JUSH != 'sqlite') {
                        echo '<fieldset><legend>'.lang(279)." <span id='selected3'></span></legend><div>";
                        $j = (isset($_POST['target']) ? $_POST['target'] : (support('scheme') ? $_GET['ns'] : DB));
                        echo ($i ? html_select('target',$i,$j) : '<input name="target" value="'.h($j).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(280)."'>",(support('copy') ? " <input type='submit' name='copy' value='".lang(281)."'> ".checkbox('overwrite',1,$_POST['overwrite'],lang(282)) : ''),"</div></fieldset>\n";
                        $Wg = " selectCount('selected3', formChecked(this, /^(tables|views)\[/));";
                    }echo "<input type='hidden' name='all' value=''>",script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^(tables|views)\[/));".(support('table') ? " selectCount('selected2', formChecked(this, /^tables\[/) || $T);" : '')."$Wg }"),input_token(),"</div></div>\n";
                }echo "</form>\n",script('tableCheck();');
            }echo "<p class='links'><a href='".h(ME)."create='>".lang(68)."</a>\n",(support('view') ? "<a href='".h(ME)."view='>".lang(206)."</a>\n" : '');
            if (support('routine')) {
                echo "<h3 id='routines'>".lang(63)."</h3>\n";
                $Qg = routines();
                if ($Qg) {
                    echo "<table class='odds'>\n",'<thead><tr><th>'.lang(187).'<td>'.lang(40).'<td>'.lang(223)."<td></thead>\n";
                    foreach ($Qg as $M) {
                        $E = ($M['SPECIFIC_NAME'] == $M['ROUTINE_NAME'] ? '' : '&name='.urlencode($M['ROUTINE_NAME']));
                        echo '<tr>','<th><a href="'.h(ME.($M['ROUTINE_TYPE'] != 'PROCEDURE' ? 'callf=' : 'call=').urlencode($M['SPECIFIC_NAME']).$E).'">'.h($M['ROUTINE_NAME']).'</a>','<td>'.h($M['ROUTINE_TYPE']),'<td>'.h($M['DTD_IDENTIFIER']),'<td><a href="'.h(ME.($M['ROUTINE_TYPE'] != 'PROCEDURE' ? 'function=' : 'procedure=').urlencode($M['SPECIFIC_NAME']).$E).'">'.lang(138).'</a>';
                    }echo "</table>\n";
                }echo '<p class="links">'.(support('procedure') ? '<a href="'.h(ME).'procedure=">'.lang(222).'</a>' : '').'<a href="'.h(ME).'function=">'.lang(221)."</a>\n";
            }if (support('event')) {
                echo "<h3 id='events'>".lang(65)."</h3>\n";
                $N = get_rows('SHOW EVENTS');
                if ($N) {
                    echo "<table>\n",'<thead><tr><th>'.lang(187).'<td>'.lang(283).'<td>'.lang(212).'<td>'.lang(213)."<td></thead>\n";
                    foreach ($N as $M) {
                        echo '<tr>','<th>'.h($M['Name']),'<td>'.($M['Execute at'] ? lang(284).'<td>'.$M['Execute at'] : lang(214).' '.$M['Interval value'].' '.$M['Interval field']."<td>$M[Starts]"),"<td>$M[Ends]",'<td><a href="'.h(ME).'event='.urlencode($M['Name']).'">'.lang(138).'</a>';
                    }echo "</table>\n";
                    $vc = get_val('SELECT @@event_scheduler');
                    if ($vc && $vc != 'ON') {
                        echo "<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($vc)."\n";
                    }
                }echo '<p class="links"><a href="'.h(ME).'event=">'.lang(211)."</a>\n";
            }
        }
    }
}page_footer();
=======
';$Wc=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$c){if($c["fun"]){unset($Wc['sql']);break;}}if($Wc){print_fieldset("export",lang(67)." <span id='selected2'></span>");$Jf=adminer()->dumpOutput();echo($Jf?html_select("output",$Jf,$ka["output"])." ":""),html_select("format",$Wc,$ka["format"])," <input type='submit' name='export' value='".lang(67)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($jc,'strlen'),$d);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import'>".lang(66)."</a>",script("qsl('a').onclick = partial(toggle, 'import');",""),"<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",file_input("<input type='file' name='csv_file'> ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$ka["format"])." <input type='submit' name='import' value='".lang(66)."'>"),"</span>";echo
input_token(),"</form>\n",(!$s&&$O?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$wh=isset($_GET["status"]);page_header($wh?lang(118):lang(117));$Ji=($wh?show_status():show_variables());if(!$Ji)echo"<p class='message'>".lang(14)."\n";else{echo"<table>\n";foreach($Ji
as$M){echo"<tr>";$z=array_shift($M);echo"<th><code class='jush-".JUSH.($wh?"status":"set")."'>".h($z)."</code>";foreach($M
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: text/javascript; charset=utf-8");if($_GET["script"]=="db"){$Eh=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$E=>$S){json_row("Comment-$E",h($S["Comment"]));if(!is_view($S)||preg_match('~materialized~i',$S["Engine"])){foreach(array("Engine","Collation")as$z)json_row("$z-$E",h($S[$z]));foreach($Eh+array("Auto_increment"=>0,"Rows"=>0)as$z=>$X){if($S[$z]!=""){$X=format_number($S[$z]);if($X>=0)json_row("$z-$E",($z=="Rows"&&$X&&$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")?"~ $X":$X));if(isset($Eh[$z]))$Eh[$z]+=($S["Engine"]!="InnoDB"||$z!="Data_free"?$S[$z]:0);}elseif(array_key_exists($z,$S))json_row("$z-$E","?");}}}foreach($Eh
as$z=>$X)json_row("sum-$z",format_number($X));json_row("");}elseif($_GET["script"]=="kill")connection()->query("KILL ".number($_POST["kill"]));else{foreach(count_tables(adminer()->databases())as$j=>$X){json_row("tables-$j",$X);json_row("size-$j",db_size($j));}json_row("");}exit;}else{$Nh=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($Nh&&!$l&&!$_POST["search"]){$K=true;$D="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$K=truncate_tables($_POST["tables"]);$D=lang(260);}elseif($_POST["move"]){$K=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$D=lang(261);}elseif($_POST["copy"]){$K=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$D=lang(262);}elseif($_POST["drop"]){if($_POST["views"])$K=drop_views($_POST["views"]);if($K&&$_POST["tables"])$K=drop_tables($_POST["tables"]);$D=lang(263);}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$M)$D
.="<b>".h($R)."</b>: ".h($M["integrity_check"])."<br>";}}elseif(JUSH!="sql"){$K=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?"":" ANALYZE"),$_POST["tables"]));$D=lang(264);}elseif(!$_POST["tables"])$D=lang(11);elseif($K=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($M=$K->fetch_assoc())$D
.="<b>".h($M["Table"])."</b>: ".h($M["Msg_text"])."<br>";}queries_redirect(substr(ME,0,-1),$D,$K);}page_header(($_GET["ns"]==""?lang(28).": ".h(DB):lang(265).": ".h($_GET["ns"])),$l,true);if(adminer()->homepage()){if($_GET["ns"]!==""){echo"<h3 id='tables-views'>".lang(266)."</h3>\n";$Mh=tables_list();if(!$Mh)echo"<p class='message'>".lang(11)."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".lang(267)." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'>",script("qsl('input').onkeydown = partialArg(bodyKeydown, 'search');","")," <input type='submit' name='search' value='".lang(47)."'>\n","</div></fieldset>\n";if($_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr class="wrap">','<td><input id="check-all" type="checkbox" class="jsonly">'.script("qs('#check-all').onclick = partial(formCheck, /^(tables|views)\[/);",""),'<th>'.lang(132),'<td>'.lang(268).doc_link(array('sql'=>'storage-engines.html')),'<td>'.lang(122).doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')),'<td>'.lang(269).doc_link(array('sql'=>'show-table-status.html',)),'<td>'.lang(270).doc_link(array('sql'=>'show-table-status.html',)),'<td>'.lang(271).doc_link(array('sql'=>'show-table-status.html')),'<td>'.lang(42).doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),'<td>'.lang(272).doc_link(array('sql'=>'show-table-status.html',)),(support("comment")?'<td>'.lang(41).doc_link(array('sql'=>'show-table-status.html',)):''),"</thead>\n";$T=0;foreach($Mh
as$E=>$U){$Mi=($U!==null&&!preg_match('~table|sequence~i',$U));$u=h("Table-".$E);echo'<tr><td>'.checkbox(($Mi?"views[]":"tables[]"),$E,in_array("$E",$Nh,true),"","","",$u),'<th>'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".urlencode($E)."' title='".lang(33)."' id='$u'>".h($E).'</a>':h($E));if($Mi&&!preg_match('~materialized~i',$U)){$Yh=lang(131);echo'<td colspan="6">'.(support("view")?"<a href='".h(ME)."view=".urlencode($E)."' title='".lang(35)."'>$Yh</a>":$Yh),'<td align="right"><a href="'.h(ME)."select=".urlencode($E).'" title="'.lang(32).'">?</a>';}else{foreach(array("Engine"=>array(),"Collation"=>array(),"Data_length"=>array("create",lang(34)),"Index_length"=>array("indexes",lang(135)),"Data_free"=>array("edit",lang(36)),"Auto_increment"=>array("auto_increment=1&create",lang(34)),"Rows"=>array("select",lang(32)),)as$z=>$A){$u=" id='$z-".h($E)."'";echo($A?"<td align='right'>".(support("table")||$z=="Rows"||(support("indexes")&&$z!="Data_length")?"<a href='".h(ME."$A[0]=").urlencode($E)."'$u title='$A[1]'>?</a>":"<span$u>?</span>"):"<td id='$z-".h($E)."'>");}$T++;}echo(support("comment")?"<td id='Comment-".h($E)."'>":""),"\n";}echo"<tr><td><th>".lang(244,count($Mh)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),"<td>".h(db_collation(DB,collations()));foreach(array("Data_length","Index_length","Data_free")as$z)echo"<td align='right' id='sum-$z'>";echo"\n","</table>\n",script("ajaxSetHtml('".js_escape(ME)."script=db');"),"</div>\n";if(!information_schema(DB)){echo"<div class='footer'><div>\n";$Hi="<input type='submit' value='".lang(273)."'> ".on_help("'VACUUM'");$vf="<input type='submit' name='optimize' value='".lang(274)."'> ".on_help(JUSH=="sql"?"'OPTIMIZE TABLE'":"'VACUUM OPTIMIZE'");echo"<fieldset><legend>".lang(126)." <span id='selected'></span></legend><div>".(JUSH=="sqlite"?$Hi."<input type='submit' name='check' value='".lang(275)."'> ".on_help("'PRAGMA integrity_check'"):(JUSH=="pgsql"?$Hi.$vf:(JUSH=="sql"?"<input type='submit' value='".lang(276)."'> ".on_help("'ANALYZE TABLE'").$vf."<input type='submit' name='check' value='".lang(275)."'> ".on_help("'CHECK TABLE'")."<input type='submit' name='repair' value='".lang(277)."'> ".on_help("'REPAIR TABLE'"):"")))."<input type='submit' name='truncate' value='".lang(278)."'> ".on_help(JUSH=="sqlite"?"'DELETE'":"'TRUNCATE".(JUSH=="pgsql"?"'":" TABLE'")).confirm()."<input type='submit' name='drop' value='".lang(127)."'>".on_help("'DROP TABLE'").confirm()."\n";$i=(support("scheme")?adminer()->schemas():adminer()->databases());echo"</div></fieldset>\n";$Wg="";if(count($i)!=1&&JUSH!="sqlite"){echo"<fieldset><legend>".lang(279)." <span id='selected3'></span></legend><div>";$j=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($i?html_select("target",$i,$j):'<input name="target" value="'.h($j).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(280)."'>",(support("copy")?" <input type='submit' name='copy' value='".lang(281)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(282)):""),"</div></fieldset>\n";$Wg=" selectCount('selected3', formChecked(this, /^(tables|views)\[/));";}echo"<input type='hidden' name='all' value=''>",script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^(tables|views)\[/));".(support("table")?" selectCount('selected2', formChecked(this, /^tables\[/) || $T);":"")."$Wg }"),input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo"<p class='links'><a href='".h(ME)."create='>".lang(68)."</a>\n",(support("view")?"<a href='".h(ME)."view='>".lang(206)."</a>\n":"");if(support("routine")){echo"<h3 id='routines'>".lang(63)."</h3>\n";$Qg=routines();if($Qg){echo"<table class='odds'>\n",'<thead><tr><th>'.lang(187).'<td>'.lang(40).'<td>'.lang(223)."<td></thead>\n";foreach($Qg
as$M){$E=($M["SPECIFIC_NAME"]==$M["ROUTINE_NAME"]?"":"&name=".urlencode($M["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($M["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').urlencode($M["SPECIFIC_NAME"]).$E).'">'.h($M["ROUTINE_NAME"]).'</a>','<td>'.h($M["ROUTINE_TYPE"]),'<td>'.h($M["DTD_IDENTIFIER"]),'<td><a href="'.h(ME.($M["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').urlencode($M["SPECIFIC_NAME"]).$E).'">'.lang(138)."</a>";}echo"</table>\n";}echo'<p class="links">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.lang(222).'</a>':'').'<a href="'.h(ME).'function=">'.lang(221)."</a>\n";}if(support("event")){echo"<h3 id='events'>".lang(65)."</h3>\n";$N=get_rows("SHOW EVENTS");if($N){echo"<table>\n","<thead><tr><th>".lang(187)."<td>".lang(283)."<td>".lang(212)."<td>".lang(213)."<td></thead>\n";foreach($N
as$M)echo"<tr>","<th>".h($M["Name"]),"<td>".($M["Execute at"]?lang(284)."<td>".$M["Execute at"]:lang(214)." ".$M["Interval value"]." ".$M["Interval field"]."<td>$M[Starts]"),"<td>$M[Ends]",'<td><a href="'.h(ME).'event='.urlencode($M["Name"]).'">'.lang(138).'</a>';echo"</table>\n";$vc=get_val("SELECT @@event_scheduler");if($vc&&$vc!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($vc)."\n";}echo'<p class="links"><a href="'.h(ME).'event=">'.lang(211)."</a>\n";}}}}page_footer();
>>>>>>> upstream/master

<?php
 require_once ("install_function.php"); 
 $step =0; 
 $ndb =1; 
 $dbserver 	= isset($_POST["dbserver"])?$_POST["dbserver"]:"localhost"; 
 $dbuser 	= isset($_POST["dbuser"])?$_POST["dbuser"]:"root"; 
 $dbpass	= isset($_POST["dbpass"])?$_POST["dbpass"]:""; 
 $dbname 	= isset($_POST["dbname"])?$_POST["dbname"]:"db_xframejs";
 $site_title 	= isset($_POST["site_title"])?$_POST["site_title"]:"Your Title Here";
 $menu_title 	= isset($_POST["menu_title"])?$_POST["menu_title"]:"Main Menu";
 
if (isset($_POST["Test"])) {
  $ndb = isset($_POST["ndb"])?1:0; 
  test_sql_connection($ndb);
  if (!$errors) 
    write_config_file(); 
  if (!$errors) 
    $step=1; 	
}  

if (isset($_POST["idb"])) {
   require_once(CONF_DB); 
   if (!$connect_id = @mysql_connect(HOST,USER,PASS)) 
      $errors[] = "Tidak Bisa Terkoneksi ke Database"; 
   if (!$errors)
      if (!mysql_select_db(DB, $connect_id))
           $errors[] = "Tidak Dapat Menemukan Database $db";
   if ($errors) {
      $dbhost = $host;
	  $dbuser = $user;
	  $dbpass = $pass; 
	  $dbname = $db; 
   }
   if (!$errors) {
    populate_db($connect_id,'db_sistem.sql');
    if (!$errors) $step=2;

   }	     	    
} 
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Install Database</title>
<style type="text/css">
<!--
body {
font-family:Arial, Helvetica, sans-serif;
font-size:11px;
margin:0px;

}

.sub_header {
 height:18px;
 background:#228B22;
 border-top:1px solid #CCCCCC;
 border-bottom:1px solid #666666;
 font-weight:bold;
 color:#FFFFFF;
 padding:4px 0px 2px 5px;
 }

form {
padding-left:4px;
}

td {
font-size:11px;
}
td.alert {
	color:#2103AF;
	text-align:left;
	font-weight:bold;
	background-color: #DDEEFF;
	border: 1px dashed #FF0000;
	padding:5px;
}

#wrapper {
	width:500px;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:10pt;
	padding:0px;
	margin-top: 150px;
	margin-right: auto;
	margin-bottom: 50px;
	margin-left: auto;
}

.button {
padding:2px;
border:1px solid #0066FF;
font-size:11px;
font-family:Verdana, Arial, Helvetica, sans-serif;
}

#forgot {
margin:0px auto;
width:350px;
visibility:hidden;
padding:0px;
}

#forgot h2 {
display:block;
padding:0px 3px 2px 2px;
border-bottom:2px solid #FF0000;
font-size:18px;
font-weight:bold;
}


#loading {
color:#663399;
visibility:hidden;
}
-->
</style>

<script type="text/JavaScript">
<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->

function doInstall() {
  	myinstall = document.getElementById("install");
	myinstall.style.visibility = "hidden";
	myinstall.innerHTML =""; 
  	myload = document.getElementById("loading");
	myload.style.visibility = "visible";
    document.form1.submit();
} 
</script>
</head>

<body onload="MM_preloadImages('image/db_2.jpg')">

<div id="wrapper">
  <form action="" method="post" name="form1" id="form1">
  <img src="image/install.jpg" width="405" height="86" />
  <?php if ($step==0) { ?>
  <table width="79%" border="0" cellspacing="2" cellpadding="2">
      
      
      <tr>
        <td width="36%"><div align="right">Server</div></td>
        <td width="64%"><input name="dbserver" type="text" id="dbserver" value="<?php echo $dbserver;  ?>" size="22" /></td>
      </tr>
      <tr>
        <td><div align="right">User</div></td>
        <td><input name="dbuser" type="text" id="dbuser" value="<?php echo $dbuser;  ?>" size="22" /></td>
      </tr>
      <tr>
        <td><div align="right">Password</div></td>
        <td><input name="dbpass" type="text" id="dbpass" value="<?php echo $dbpass;  ?>" size="22" /></td>
      </tr>
      <tr>
        <td><div align="right">Database</div></td>
        <td><input name="dbname" type="text" id="dbname" value="<?php echo $dbname;  ?>" size="22" /></td>
      </tr>
      <tr>
        <td><div align="right">Site Title</div></td>
        <td><input name="site_title" type="text" id="site_title" value="<?php echo $site_title;  ?>" size="22" /></td>
      </tr>
      <tr>
        <td><div align="right">Menu Title</div></td>
        <td><input name="menu_title" type="text" id="menu_title" value="<?php echo $menu_title;  ?>" size="22" /></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><label>
          <input name="ndb" type="checkbox" id="ndb" value="1" <?php if ($ndb) echo "checked=\"checked\""; ?> />
        Buat Database Baru</label></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="Test" type="submit" id="Test" value="Tes Koneksi"/>
        <a href="./?forgot=1"></a> </td>
      </tr>
      <tr>
        <td colspan="2" class="alert">
		<?php if ($errors) {
		            echo "<ul>";
					foreach($errors as $err) 
					   echo "<li>$err</li>";
					echo "</ul>";    
			}
		 ?></td>
      </tr>
    </table>
	<?php } ?>
	<?php if ($step ==1) { ?>
	<div id="install">
    <table width="79%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="alert"><p>Konfigurasi Mysql Anda Telah Berhasil! </p></td>
      </tr>
      <tr>
        <td><a href="javascript:doInstall()" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('tabel','','image/db_2.jpg',1)"><img src="image/db_1.jpg" alt="Install Tabel" name="tabel" width="241" height="53" border="0" id="tabel" /></a></td>
      </tr>
      <tr>
        <td><hr /></td>
      </tr>
    </table>
	</div>
	<div id="loading">
      <table width="410" border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td width="128"><img src="image/loading.gif" width="128" height="128" /></td>
          <td width="270"><h3 align="left"> Install Struktur Tabel...
            <input name="idb" type="hidden" id="idb" value="1" /></h3>
		  </td>
        </tr>
      </table>
    </div>
	<?php } ?>
    <?php if ($step ==2) { ?>
    <table width="79%" border="0" cellspacing="0" cellpadding="0">
      
      <tr>
        <td><div align="center"><a href="../"><img src="image/sukses.jpg" width="329" height="74" border="0" /></a></div></td>
      </tr>
    </table>
    <?php } ?>
  </form>
</div>
  
</body>
</html>

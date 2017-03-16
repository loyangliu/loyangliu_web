<?php if(!defined('HDWIKI_ROOT')) exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id="html">

<head>
<title>农夫码园</title>	
<link href="style/<?php echo $theme?>/hdwiki.css" rel="stylesheet" type="text/css" media="all"/>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="lang/<?php echo $setting['lang_name']?>/front.js"></script>
<script type="text/javascript" src="js/jquery.dialog-0.8.min.js"></script>
<script type="text/javascript" src="js/login.js"></script>
</head>

<body>
<!--编辑异常中断，下次登录提示-->

<ul id="login" class="w-950 bor_b-ccc"> 
<?php if($user['groupid']=='1') { ?>
<li name="login"><a href="index.php?user-login">登录</a></li>
<li name="register" class="bor_no"><a href="index.php?user-register" >注册</a></li>
<?php } else { ?>
	<li class="bor_no pad10">欢迎你，<a href="index.php?user-space-<?php echo $user['uid']?>"><?php echo $user['username']?></a></li>
	<?php if($user['password']!='') { ?>
	
	<li><a  href="index.php?user-profile">个人管理</a></li>
	<?php if($adminlogin ) { ?><li><a href="index.php?admin_main">系统设置</a></li><?php } ?>
	<li class="bor_no"><a href="index.php?user-logout<?php echo $referer?>" >退出</a></li>
	<?php } else {} 
 } ?>
</ul>


<div class="bg_book">
	<a href="<?php echo WIKI_URL?>" id="logo"><img alt="HDWiki" width="<?php echo $setting['logowidth']?>" src="style/default/logo.gif"/></a>

	<form name="searchform" method="post" action="index.php?search-kw">
		<p id="search">
			<input name="searchtext" class="btn_txt" maxlength="80" size="42" value="<?php if(isset($searchtext)) { ?><?php echo $searchtext?><?php } ?>" type="text"/>
			<input name="default" value="本地搜索" tabindex="2" class="btn_inp enter_doc" onclick="document.searchform.action='index.php?search-default';document.searchform.submit();" type="button"/>
			<input name="full" value="1" tabindex="1"   type="hidden"/>
			<!--
			<input name="search" value="搜 索" tabindex="1" class="btn_inp sea_doc" type="submit"/>
			<a href="index.php?search-fulltext" class="sea_advanced link_black">高级搜索</a>
			-->
		</p>
	</form>
	
	<div id="nav" class="w-950 bor_b-ccc">
		<ul id="nav_left">
			<?php if(!empty($channellist[2])) { ?>
			<?php foreach((array)$channellist[2] as $channel) {?>
			<li><a href="<?php echo $channel['url']?>"><?php echo $channel['name']?></a></li>
			<?php } 
			 } ?>
		</ul>
		<label id="nav_right">
			<a href="index.php?doc-create">我要发表文章</a> 
			<!-- <a href="index.php?doc-sandbox">编辑实验</a> -->
		</label>
	</div>
</div>

<!--ad start -->
<!-- model/base.class.php 1. init_cache()加载广告wiki_advertisement; init_global()中对广告做过滤处理 -->
<!--
	<?php if(isset($advlist[0]) && isset($setting['advmode']) && '1'==$setting['advmode']) { ?>
	<div class="ad" id="advlist_0">
	<?php echo $advlist[0]['code']?>
	</div>
	<?php } elseif(isset($advlist[0]) && (!isset($setting['advmode']) || !$setting['advmode'])) { ?>
	<div class="ad" id="advlist_0">
	</div>
	<?php } ?>
-->
<!--ad end -->
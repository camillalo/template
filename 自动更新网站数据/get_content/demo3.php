<html>
	<head>
		<title>email</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<script type="text/javascript" src="jQuery-1.3.1.min.js"></script>
		
	</head>
	
	<body>
		<?php
			/**
			**链接数据库
			**/
			function relate_db($host,$user,$password,$db){ 
				$conn = mysql_connect($host,$user,$password) or die(mysql_error());
				mysql_select_db($db) or die(mysql_error());
				mysql_query("set names 'utf8'");
				return "success";
			} 
			relate_db($config['db_host'],$config['db_user'],$config['db_password'],$config['db_name']);

			$sql = "select * from email_content order by time desc";
			$row = mysql_query($sql);
			while ($rs = mysql_fetch_array($row)) {
				echo '<h1 class="title">'.$rs['title'].'</h1>';
				echo '<div class="content">'.$rs['content'].'</div>';
			}

		?>
	
	</body>
	<script type="text/javascript">
		//自动更新
		setTimeout("window.location.reload();", 3000*60*10);
	</script>
</html>
<?php
	require_once("config.php");

	$ignore = $config['ignore'];
	$duality = $config['duality'];
	$multi = $config['multi'];
	$xattr = $config['xattr'];
	$limit = $config['limit'];

	$content =iconv("utf-8", "gbk", $_POST['content']);

	$cws = scws_new();
	$cws->set_charset('gbk');
	$cws->set_rule(ini_get('scws.default.fpath') . '/rules.ini');
	$cws->set_dict(ini_get('scws.default.fpath') . '/dict.xdb');

	$cws->set_duality($duality);
	$cws->set_ignore($ignore);
	$cws->set_multi($multi);
	$cws->send_text($content);

	$list = $cws->get_tops($limit, $xattr);
    settype($list, 'array');
    $words = "";
    foreach ($list as $tmp)
    {
        $words .= iconv("gbk", "utf-8", $tmp['word']).",";
    }
    echo $words;
	$cws->close();
?>
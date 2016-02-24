<?php

/**
 * 
 * @author 229602756@qq.com
 *
 */
class html {
	
	public static function array2table($arr,$width = 0){
		$count = count($arr);
		if($count > 0){
			reset($arr);
			$num = count(current($arr));
			$s =  "<table border=\"1\"cellpadding=\"5\" cellspacing=\"0\" width=\"$width\">\n";
			$s .= "<tr>\n";
			foreach(current($arr) as $key => $value){
				$s .= "<th>";
				$s .= $key;
				$s .= "</th>\n";
			}
			$s .= "</tr>\n";
			while ($curr_row = current($arr)) {
				$s .= "<tr>\n";
				$col = 1;
				while (false!==$curr_field = current($curr_row)) {
					$s .= "<td>";
					$s .= $curr_field;
					$s .= "</td>\n";
					next($curr_row);
					$col++;
				}
				while($col <= $num){
					$s .= "<td>&nbsp;</td>\n";
					$col++;
				}
				$s .= "</tr>\n";
				next($arr);
			}
			return $s . "</table>\n";
		}
	}
	
	function pagelink($pageNow,$count,$uriFrame,$pageSize=10) {
		if($count<1)return;
		$pageMax=ceil($count/$pageSize);
		$pageNow<1&&$pageNow=1;
		$pageNow>$pageMax&&$pageNow=$pageMax;

		$strCurrent='<b>'.$pageNow.'</b>';
		$strPrev='';
		$strNext='';

		$long=6;

		for($i=$pageNow-1;$i>0&&($pageNow-$i)<$long;$i--){
			$strPrev='<a href="'.$uriFrame.$i.'">'.$i.'</a>'.$strPrev;
		}
		if($strPrev)$strPrev='<a title="Prev" href="'.$uriFrame.($pageNow-1).'">&laquo;Prev</a>'.$strPrev;

		for($i=$pageNow+1;$i<=$pageMax&&($i-$pageNow)<$long;$i++){
			$strNext.='<a href="'.$uriFrame.$i.'">'.$i.'</a>';
		}
		if($strNext)$strNext.='<a title="Next" href="'.$uriFrame.($pageNow+1).'">Next&raquo;</a>';

		$description=(($pageNow-1)*$pageSize+1);
		$description.=$pageNow*$pageSize>$count?'-'.$count:'-'.$pageNow*$pageSize;
		$description.=' of '.$count;

		if($strPrev||$strNext){
			$selectForm='<select onchange="window.location=\''.$uriFrame.'\'+this.value;">';
			for($i=1;$i<=$pageMax;$i++){
				$selected=$pageNow==$i?'selected="selected" ':'';
				$selectForm.='<option '.$selected.'value="'.$i.'">'.$i.'</option>';
			}
			$selectForm.='</select>';
			return '('.$description.') '.$strPrev.$strCurrent.$strNext.' '.$selectForm;
		}else{
			return "($description)";
		}
	}
	
	function findInputValue($name, $html) {
		$name = preg_quote($name);
		//先找到input
		if(!preg_match('/<input\s+[^>]*name\s*=\s*(?:(?:"'.$name.'")|(?:\''.$name.'\'))[^>]*>/si',$html,$m)){
			return '';
		}
		
		if(!preg_match('/value\s*=\s*(?:(?:"([^"]*)")|(?:\'([^\']*)\'))/si',$m[0],$p)){
			return '';
		}
		
		return array_pop($p);
	}
	
	function alertBack($msg='') {
		die('<script type="text/javascript">alert("'.addslashes($msg).'");history.go(-1);</script>');
	}
}
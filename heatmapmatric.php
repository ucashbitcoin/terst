<?php 
//echo"ok"; die;
$array=array(array(1,2,3,4,5),array(11,12,13,14,15),array(21,22,23,24,25),array(31,32,33,34,35),array(41,42,43,44,45),array(51,52,53,54,55));

   heatmapmatrix($array,4);

function heatmapmatrix($array,$column)
{
	for($i=0;$i<$column;$i++)
	{
		for($j=0;$j<count($array[$i]);$j++)
		{
			$heatmap[]=array($i,$j,$array[$i][$j]);
		}
	}

}
?>

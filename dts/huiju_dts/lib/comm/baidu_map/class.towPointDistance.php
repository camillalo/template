<?php

/**
* 计算百度地图两点之间距离
*
*/
class towPointDistance
{


    public $fP1Lon;
    public $fP1Lat;
    public $fP2Lon;
    public $fP2Lat;

    /**
    * 
    */
    function __construct($fP1Lon,$fP1Lat,$fP2Lon,$fP2Lat){
        $this->fP1Lon = $fP1Lon;
        $this->fP1Lat = $fP1Lat;
        $this->fP2Lon = $fP2Lon;
        $this->fP2Lat = $fP2Lat;
    }



    
    function Handle()
    {
       $string1 = $this->fP1Lon.",".$this->fP1Lat;
       $string2 = $this->fP2Lon.",".$this->fP2Lat;
       $data1 = $this->BD09LLtoWGS84($string1); 
       $data2 = $this->BD09LLtoWGS84($string2); 

        $data1 = explode(",",$data1);
        $data2 = explode(",",$data2);
        $lng = $data1[0];
        $lat = $data1[1];
        $location_lng = $data2[0];
        $location_lat = $data2[1];
        return $this->distanceBetween($lat, $lng, $location_lat, $location_lng);
    }




    /**
    *
     * 计算两个坐标之间的距离(米)
    *
     * @param float $fP1Lat 起点(纬度)
    *
     * @param float $fP1Lon 起点(经度)
    *
     * @param float $fP2Lat 终点(纬度)
    *
     * @param float $fP2Lon 终点(经度)
    *
     * @return int
    *
     */

    function distanceBetween($fP1Lat, $fP1Lon, $fP2Lat, $fP2Lon){

        $fEARTH_RADIUS = 6378137;

        //角度换算成弧度

        $fRadLon1 = deg2rad($fP1Lon);

        $fRadLon2 = deg2rad($fP2Lon);

        $fRadLat1 = deg2rad($fP1Lat);

        $fRadLat2 = deg2rad($fP2Lat);

        //计算经纬度的差值

        $fD1 = abs($fRadLat1 - $fRadLat2);

        $fD2 = abs($fRadLon1 - $fRadLon2);

        //距离计算

        $fP = pow(sin($fD1/2), 2) +

              cos($fRadLat1) * cos($fRadLat2) * pow(sin($fD2/2), 2);

        return intval($fEARTH_RADIUS * 2 * asin(sqrt($fP)) + 0.5);

    }

    /**
    *
     * 百度坐标系转换成标准GPS坐系
    *
     * @param float $lnglat 坐标(如:106.426, 29.553404)
    *
     * @return string 转换后的标准GPS值:
    *
     */

    function BD09LLtoWGS84($string){ // 经度,纬度

        $lnglat = explode(',', $string);

        list($x,$y) = $lnglat;

        $Baidu_Server = "http://api.map.baidu.com/ag/coord/convert?from=0&to=4&x={$x}&y={$y}";

        $result = @file_get_contents($Baidu_Server);

        $json = json_decode($result);

        if($json->error == 0){

            $bx = base64_decode($json->x);

            $by = base64_decode($json->y);

            $GPS_x = 2 * $x - $bx;

            $GPS_y = 2 * $y - $by;

            return $GPS_x.','.$GPS_y;//经度,纬度

        }else

            return $lnglat;

    }

}
    /*
    $lng = 121.456665;
    $lat = 31.243591;

    $location_lng = 121.458821;
    $location_lat = 31.212465;

    $so = new towPointDistance($lng,$lat,$location_lng,$location_lat);
    echo $so->Handle();
*/

?>
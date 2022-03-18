<?php

    $zw=42500;

    function _redis(){
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
        $redis->auth('123456');
        $redis->select(6);
        return $redis;
    }
//
    function js($zw){
        $redis=_redis();
        //建，交，信，广
        $sx=20000+15000+6000+2000;
        $xyk[0]=rand(20000,21000);
        $xyk[1]=rand(15000,16000);
        $xyk[2]=21000-$xyk[1];
        $xyk[3]=22000-$xyk[0];
        $js=[];
        $num=[];
        $redis->hSet('zw'.date('Y-m'),'jh',$xyk[0]);
        $redis->hSet('zw'.date('Y-m'),'jt',$xyk[1]);
        $redis->hSet('zw'.date('Y-m'),'zx',$xyk[2]);
        $redis->hSet('zw'.date('Y-m'),'gf',$xyk[3]);
        for($i=0;$i<count($xyk);$i++){
            if($xyk[$i]>10000)
                $num[$i]=rand(7,12);
            else
                $num[$i]=rand(5,9);
        }
        for($j=0;$j<count($num);$j++){
            $ave=$xyk[$j]/$num[$j];
           for($n=0;$n<$num[$j];$n++){
                if($ave>2000) $dev=rand(250,300);
                elseif($ave>1000) $dev=rand(150,200);
                else $dev=rand(50,100);
                if(array_sum($js[$j][$n])>=$xyk[$j]) continue;
                if($dev%2==0){
                    $js[$j][$n]=round($ave-$dev);
                }else{
                    $js[$j][$n]=round($ave+$dev);
                }

           }
        }

        echo "<pre>";
        print_r($js);
    }


    print_r(js($zw));
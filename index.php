<?php

    $zw=42500;

    function _redis($s=0){
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
        $redis->auth('123456');
        $redis->select($s);
        return $redis;
    }
//

    function index(){
        $redis=_redis();
        $hk=date('Ym');
//        if($redis->hExists('hk_'.$hk.'_*')) echo 1;
        $data=[];
        $k=$redis->Keys('hk_'.$hk.'*');
        if(!empty($k)){
            foreach ($k as $vo){
                $data[]=$redis->hGetAll($vo);
            }
        }
        else
        $data=js(true);
//        foreach ($data as $val){
//            print_r($val);
//        }
        echo "<pre>";
        print_r($data);
    }

    function js($pd=false){
        if(!$pd) return 0;
        $redis=_redis();

        //建，交，信，广
        $sx=20000+15000+6000+2000;
        $xyk[0]=rand(20000,21000);
        $xyk[1]=rand(15000,16000);
        $xyk[2]=21000-$xyk[1];
        $xyk[3]=22000-$xyk[0];
        $js=[];
        $num=[];
        $hk=date('Ym');
        for($i=0;$i<count($xyk);$i++){
            if($xyk[$i]>10000)
                $num[$i]=rand(7,12);
            else
                $num[$i]=rand(5,8);
        }
        for($j=0;$j<count($num);$j++){
            $ave=$xyk[$j]/$num[$j];
           for($n=0;$n<$num[$j];$n++){
                if($ave>2000) $dev=rand(250,300);
                elseif($ave>1000) $dev=rand(150,200);
                else $dev=rand(50,100);
                if(array_sum($js[$j][$n])>=$xyk[$j]) continue;
                $name=get_bank($j);
                if($dev%2==0){
                    $js[$j][$n]=round($ave-$dev);
                    if(!$redis->hExists('hk_'.$hk.'_'.$name,$n))
                        $redis->hSet('hk_'.$hk.'_'.$name,$name.'_'.$n,$js[$j][$n]);
                }else{
                    $js[$j][$n]=round($ave+$dev);
                    if(!$redis->hExists('hk_'.$hk.'_'.$name,$n))
                        $redis->hSet('hk_'.$hk.'_'.$name,$name.'_'.$n,$js[$j][$n]);
                }

           }
        }
        return $js;
    }

    function get_bank($n){
        if($n==0) return 'js';
        if($n==1) return 'jt';
        if($n==2) return 'zx';
        if($n==3) return 'gf';
        return $n;
    }


    print_r(index());
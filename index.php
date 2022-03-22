<?php

    $zw=42500;

    function _redis($s=0){
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
        $redis->auth('123456');
        $redis->select(6);
        return $redis;
    }
//

    function index($zw){
       $hk=date('Ym');
       $list=gen($hk);
       $c=0;
       $gf=[];
       foreach ($list as $value){
           if($value['name']=='广发银行'){
               $gf['name']=$value['name'];
               $gf['c']=$value['c'];
               $gf['list']=$value['list'];
               continue;
           }
           echo "名称：".$value['name'];
           echo "。刷款金额：".$value['c'];
           echo "。刷款次数:".count($value['list']);
           echo "具体刷款：";
           print_r($value['list']);
           echo "<br>";
           $c+=$value['c'];
       }
//       print_r($gf);exit;
        echo "名称：".$gf['name'];
        echo "。应刷金额：".$gf['c'];
        if(is_array($gf['list']))
            echo "。刷款次数:".count($gf['list']);
        echo "。实刷金额：".($zw-$c);
        echo "具体刷款：";
        print_r($gf['list']);
        echo "<br>";
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
                if(is_array($js[$j][$n]))
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

    function gen($hk){
        $redis=_redis();
        $data=[];
        $k=$redis->Keys('hk_'.$hk.'*');
        echo "<pre>";
        if(!empty($k)){
            foreach ($k as $vo){
                $data[]=$redis->hGetAll($vo);
            }
        }
        else
            $data=js(true);
//        if($redis->get('gen_'.$hk))
//            return json_decode($redis->get('gen_'.$hk),1);
        if(!$redis->exists('gen_'.$hk)){
            $list=[];
            foreach ($data as $k=>$val){
                foreach ($val as $key=>$v){
                    if(strpos($key, 'js') !== false)
                        $list[$k]['name']='建设银行';
                    if(strpos($key,'jt') !== false)
                        $list[$k]['name']='交通银行';
                    if(strpos($key,'zx') !== false)
                        $list[$k]['name']='中信银行';
                    if(strpos($key,'gf') !== false)
                        $list[$k]['name']='广发银行';
                }
                $list[$k]['c']=array_sum($val);
                $list[$k]['list']=$val;
            }
            if(isset($list[0]['name']))
                $redis->set('gen_'.$hk,json_encode($list,1));
            return $list;
        }
        $list=json_decode($redis->get('gen_'.$hk),1);
        return $list;
    }

    function get_bank($n){
        if($n==0) return 'js';
        if($n==1) return 'jt';
        if($n==2) return 'zx';
        if($n==3) return 'gf';
        return $n;
    }

    function del(){
        $redis=_redis(6);
        $hk=date('Ym');
        //        $hk=date('Ym',strtotime('-1 month'));
        $keys=$redis->Keys('hk_'.$hk.'*');
        if(!empty($keys)){
            foreach ($keys as $vo){
                $redis->del($vo);
                echo $vo;
            }
        }
        $res=$redis->del('gen_'.$hk);
        echo $res;
    }

    print_r(index($zw));
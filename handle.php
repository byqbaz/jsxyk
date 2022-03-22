<?php
    function _redis($s=0){
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
        $redis->auth('123456');
        $redis->select($s);
        return $redis;
    }

    function del(){
        $redis=_redis(6);
    //    $hk=date('Ym');
        $hk=date('Ym',strtotime('-1 month'));
        $keys=$redis->Keys('hk_'.$hk.'*');
        if(!empty($keys)){
            foreach ($keys as $vo){
                $redis->del($vo);
                echo $vo;
            }
        }
        $redis->del('gen_'.$hk);
        echo 1;
    }

    print_r(del());

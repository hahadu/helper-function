<?php
/**
 *  +----------------------------------------------------------------------
 *  | Created by  hahadu (a low phper and coolephp)
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2020. [hahadu] All rights reserved.
 *  +----------------------------------------------------------------------
 *  | SiteUrl: https://github.com/hahadu
 *  +----------------------------------------------------------------------
 *  | Author: hahadu <582167246@qq.com>
 *  +----------------------------------------------------------------------
 *  | Date: 2020/10/1 下午7:13
 *  +----------------------------------------------------------------------
 *  | Description:   时间处理函数
 *  +----------------------------------------------------------------------
 **/

namespace Hahadu\Helper;


class DateHelper
{
    /**
     * 传入时间戳,计算距离现在的时间
     * @param  number $time 时间戳
     * @return string     返回多少以前
     */
    static public function format_time($time) {
        $time = (int) substr($time, 0, 10);
        $int = time() - $time;
        $str = '';
        if ($int <= 2){
            $str = sprintf('刚刚', $int);
        }elseif ($int < 60){
            $str = sprintf('%d秒前', $int);
        }elseif ($int < 3600){
            $str = sprintf('%d分钟前', floor($int / 60));
        }elseif ($int < 86400){
            $str = sprintf('%d小时前', floor($int / 3600));
        }elseif ($int < 1728000){
            $str = sprintf('%d天前', floor($int / 86400));
        }elseif ($int < 20736000){
            $moth = floor($int / 1728000);
            if($moth<=3){
                $str = sprintf('%d月前', $moth);
            }else{
                $str = date('m月d日 H:i', $time);
            }

        }else{
            $str = date('Y年m月d日 H:i', $time);
        }
        return $str;
    }

}
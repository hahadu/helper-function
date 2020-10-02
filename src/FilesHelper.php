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
 *  | Date: 2020/10/1 下午5:19
 *  +----------------------------------------------------------------------
 *  | Description:   cooleAdmin
 *  +----------------------------------------------------------------------
 **/

namespace Hahadu\Helper;


class FilesHelper
{
    /****
     * 保存远程文件到本地
     * @param $url
     * @param string $path
     * @return string
     */
    static public function download_file($url, $path = '/Upload/Download/')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);
        $filename = pathinfo($url, PATHINFO_BASENAME);
        $resource = fopen($path . $filename, 'a');
        fwrite($resource, $file);
        fclose($resource);
        return $path . $filename;
    }

    /****
     * 创建压缩文件
     * @param string $zipName 如果有多个文件则为数组，如果是单个文件则为字符串
     * $zipName = array(file1,file2,file3) or $zipName = 'file.text';
     * @param string $files  压缩包名 $zipName = 'test.zip'
     * @return array|string[]
     */
    static public function zip_create($zipName,$files){
        //$files = array('upload/qrcode/1/1.jpg');
        $zip = new \ZipArchive;//使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释
        /*
         * 通过ZipArchive的对象处理zip文件
         * $zip->open这个方法如果对zip文件对象操作成功，$zip->open这个方法会返回TRUE
         * $zip->open这个方法第一个参数表示处理的zip文件名。
         * 这里重点说下第二个参数，它表示处理模式
         * ZipArchive::OVERWRITE 总是以一个新的压缩包开始，此模式下如果已经存在则会被覆盖。
         * ZIPARCHIVE::CREATE 如果不存在则创建一个zip压缩包，若存在系统就会往原来的zip文件里添加内容。
         *
         * 这里不得不说一个大坑。
         * 我的应用场景是需要每次都是创建一个新的压缩包，如果之前存在，则直接覆盖，不要追加
         * so，根据官方文档和参考其他代码，$zip->open的第二个参数我应该用 ZipArchive::OVERWRITE
         * 问题来了，当这个压缩包不存在的时候，会报错：ZipArchive::addFile(): Invalid or uninitialized Zip object
         * 也就是说，通过我的测试发现，ZipArchive::OVERWRITE 不会新建，只有当前存在这个压缩包的时候，它才有效
         * 所以我的解决方案是 $zip->open($zipName, \ZIPARCHIVE::OVERWRITE | \ZIPARCHIVE::CREATE)
         *
         * 以上总结基于我当前的运行环境来说
         * */
        if ($zip->open($zipName, \ZIPARCHIVE::OVERWRITE | \ZIPARCHIVE::CREATE)!==TRUE) {
            exit('无法打开文件，或者文件创建失败');
        }
        if(is_array($files)){
            foreach($files as $val){
                //$attachfile = $attachmentDir . $val['filepath']; //获取原始文件路径
                if(file_exists($val)){
                    //addFile函数首个参数如果带有路径，则压缩的文件里包含的是带有路径的文件压缩
                    //若不希望带有路径，则需要该函数的第二个参数
                    $zip->addFile($val, basename($val));//第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
                }
            }
        }else{
            if(file_exists($files)){
                //addFile函数首个参数如果带有路径，则压缩的文件里包含的是带有路径的文件压缩
                //若不希望带有路径，则需要该函数的第二个参数
                $zip->addFile($files, basename($files));//第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
            }
        }
        $zip->close();//关闭

        if(!file_exists($zipName)){
            $return = array(
                //	'zipName' => $zipName,
                'status'  => 'ERROR',  //创建失败
            );
            //  $status ="无法找到文件"; //即使创建，仍有可能失败
        }else{
            $return = array(
                'zipName' => $zipName,
                'status'  => "success"
            );
        }
        return $return;

        /*
        //如果不要下载，下面这段删掉即可，如需返回压缩包下载链接，只需 return $zipName;
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename='.basename($zipName)); //文件名
        header("Content-Type: application/zip"); //zip格式的
        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        header('Content-Length: '. filesize($zipName)); //告诉浏览器，文件大小
        @readfile($zipName);
        */
    }

    /**
     * 返回文件格式
     * @param  string $str 文件名
     * @return string      文件格式
     */
    static public function file_format($str){
        // 取文件后缀名
        $str=strtolower(pathinfo($str, PATHINFO_EXTENSION));
        // 图片格式
        $image=array('webp','jpg','png','ico','bmp','gif','tif','pcx','tga','bmp','pxc','tiff','jpeg','exif','fpx','svg','psd','cdr','pcd','dxf','ufo','eps','ai','hdri');
        // 视频格式
        $video=array('mp4','avi','3gp','rmvb','gif','wmv','mkv','mpg','vob','mov','flv','swf','mp3','ape','wma','aac','mmf','amr','m4a','m4r','ogg','wav','wavpack');
        // 压缩格式
        $zip=array('rar','zip','tar','cab','uue','jar','iso','z','7-zip','ace','lzh','arj','gzip','bz2','tz');
        // 文档格式
        $text=array('exe','doc','ppt','xls','wps','txt','lrc','wfs','torrent','html','htm','java','js','css','less','php','pdf','pps','host','box','docx','word','perfect','dot','dsf','efe','ini','json','lnk','log','msi','ost','pcs','tmp','xlsb');
        // 匹配不同的结果
        switch ($str) {
            case in_array($str, $image):
                return 'image';
                break;
            case in_array($str, $video):
                return 'video';
                break;
            case in_array($str, $zip):
                return 'zip';
                break;
            case in_array($str, $text):
                return 'text';
                break;
            default:
                return 'image';
                break;
        }
    }

}
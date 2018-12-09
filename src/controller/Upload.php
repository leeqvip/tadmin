<?php

namespace  tadmin\controller;

use tadmin\support\controller\AbstractController;
use tadmin\service\upload\contract\Factory as FactoryUploader;
use think\Request;
use think\facade\Config;

class Upload extends AbstractController
{
    public function image(Request $request, FactoryUploader $uploader)
    {
        $data = [];

        $files = $uploader->multiple(...array_keys($_FILES));

        return json([
            'errno' => 0,
            'data' => array_column(array_values($files), 'save_name'),
        ]);
    }

    public function ueditor()
    {
        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", '', $this->configJson()), true);
        $action = $this->request->get('action');
        switch ($action) {
            case 'config':
                $result = $CONFIG;
                break;
            /* 上传图片 */
            case 'uploadimage':
            /* 上传涂鸦 */
            case 'uploadscrawl':
            /* 上传视频 */
            case 'uploadvideo':
            /* 上传文件 */
            case 'uploadfile':
                $result = $this->actionUpload($CONFIG);
                break;

            /* 列出图片 */
            case 'listimage':
            /* 列出文件 */
            case 'listfile':
                $result = $this->listFile($CONFIG);
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $result = $this->actionCrawler($CONFIG);
                break;

            default:
                $result = ['state' => '请求地址出错'];
                break;
        }

        /* 输出结果 */
        if ($this->request->has('callback', 'get')) {
            if (preg_match("/^[\w_]+$/", $this->request->get('callback'))) {
                echo htmlspecialchars($this->request->get('callback')).'('.$result.')';
            } else {
                return json(['state' => 'callback参数不合法']);
            }
        } else {
            return json($result);
        }
    }

    /**
     * 抓取远程图片
     * User: TechLee
     * Date: 17-01-09
     * Time: 下午19:18.
     */
    private function actionCrawler($CONFIG)
    {
        set_time_limit(0);
        /* 上传配置 */
        $config = array(
            'pathFormat' => $CONFIG['catcherPathFormat'],
            'maxSize' => $CONFIG['catcherMaxSize'],
            'allowFiles' => $CONFIG['catcherAllowFiles'],
            'oriName' => 'remote.png',
        );
        $fieldName = $CONFIG['catcherFieldName'];
        /* 抓取远程图片 */
        $list = array();
        $source = $this->request->param($fieldName);
        foreach ($source as $imgUrl) {
            $item = new Uploader($imgUrl, $config, 'remote');
            $info = $item->getFileInfo();
            array_push($list, array(
                'state' => $info['state'],
                'url' => $info['url'],
                'size' => $info['size'],
                'title' => htmlspecialchars($info['title']),
                'original' => htmlspecialchars($info['original']),
                'source' => htmlspecialchars($imgUrl),
            ));
        }
        /* 返回抓取数据 */
        return ['state' => count($list) ? 'SUCCESS' : 'ERROR', 'list' => $list];
    }

    /**
     * 上传附件和上传视频
     * User: TechLee
     * Date: 17-01-09
     * Time: 下午19:18.
     */
    private function actionUpload($CONFIG)
    {
        /* 上传配置 */
        $base64 = 'upload';
        switch (htmlspecialchars($this->request->get('action'))) {
            case 'uploadimage':
                $config = array(
                    'pathFormat' => $CONFIG['imagePathFormat'],
                    'maxSize' => $CONFIG['imageMaxSize'],
                    'allowFiles' => $CONFIG['imageAllowFiles'],
                );
                $fieldName = $CONFIG['imageFieldName'];
                break;
            case 'uploadscrawl':
                $config = array(
                    'pathFormat' => $CONFIG['scrawlPathFormat'],
                    'maxSize' => $CONFIG['scrawlMaxSize'],
                    'allowFiles' => $CONFIG['scrawlAllowFiles'],
                    'oriName' => 'scrawl.png',
                );
                $fieldName = $CONFIG['scrawlFieldName'];
                $base64 = 'base64';
                break;
            case 'uploadvideo':
                $config = array(
                    'pathFormat' => $CONFIG['videoPathFormat'],
                    'maxSize' => $CONFIG['videoMaxSize'],
                    'allowFiles' => $CONFIG['videoAllowFiles'],
                );
                $fieldName = $CONFIG['videoFieldName'];
                break;
            case 'uploadfile':
            default:
                $config = array(
                    'pathFormat' => $CONFIG['filePathFormat'],
                    'maxSize' => $CONFIG['fileMaxSize'],
                    'allowFiles' => $CONFIG['fileAllowFiles'],
                );
                $fieldName = $CONFIG['fileFieldName'];
                break;
        }
        /* 生成上传实例对象并完成上传 */
        $up = new Uploader($fieldName, $config, $base64);

        /*
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
         *     "url" => "",            //返回的地址
         *     "title" => "",          //新文件名
         *     "original" => "",       //原始文件名
         *     "type" => ""            //文件类型
         *     "size" => "",           //文件大小
         * )
         */

        /* 返回数据 */
        return $up->getFileInfo();
    }

    private function listFile($CONFIG)
    {
        /* 判断类型 */
        switch ($this->request->get('action')) {
            /* 列出文件 */
            case 'listfile':
                $allowFiles = $CONFIG['fileManagerAllowFiles'];
                $listSize = $CONFIG['fileManagerListSize'];
                $path = $CONFIG['fileManagerListPath'];
                break;
            /* 列出图片 */
            case 'listimage':
            default:
                $allowFiles = $CONFIG['imageManagerAllowFiles'];
                $listSize = $CONFIG['imageManagerListSize'];
                $path = $CONFIG['imageManagerListPath'];
        }
        $allowFiles = substr(str_replace('.', '|', join('', $allowFiles)), 1);

        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = $start + $size;

        /* 获取文件列表 */
        $path = $_SERVER['DOCUMENT_ROOT'].('/' == substr($path, 0, 1) ? '' : '/').$path;
        $files = $this->getfiles($path, $allowFiles);
        if (!count($files)) {
            return json_encode(array(
                'state' => 'no match file',
                'list' => array(),
                'start' => $start,
                'total' => count($files),
            ));
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; --$i) {
            $list[] = $files[$i];
        }
        //倒序
        //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
        //    $list[] = $files[$i];
        //}

        /* 返回数据 */
        return [
            'state' => 'SUCCESS',
            'list' => $list,
            'start' => $start,
            'total' => count($files),
        ];
    }

    /**
     * 遍历获取目录下的指定类型的文件.
     *
     * @param $path
     * @param array $files
     *
     * @return array
     */
    private function getfiles($path, $allowFiles, &$files = array())
    {
        if (!is_dir($path)) {
            return null;
        }

        if ('/' != substr($path, strlen($path) - 1)) {
            $path .= '/';
        }

        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ('.' != $file && '..' != $file) {
                $path2 = $path.$file;
                if (is_dir($path2)) {
                    self::getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.')$/i', $file)) {
                        $files[] = array(
                            'url' => substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime' => filemtime($path2),
                        );
                    }
                }
            }
        }

        return $files;
    }

    private function configJson()
    {
        $uploadPath = '/';
        $uploadPath .= Config::get('auth.upload.path', 'uploads');
        $uploadPath = str_replace(DIRECTORY_SEPARATOR, '/', $uploadPath);

        $imageMaxSize = Config::get('auth.upload.image_max_size', 2048000);
        $videoMaxSize = Config::get('auth.upload.video_max_size', 2048000);
        $fileMaxSize = Config::get('auth.upload.file_max_size', 2048000);
        $imageAllowFiles = json_encode(Config::get('auth.upload.image_allow_files', ['.png', '.jpg']));
        $videoAllowFiles = json_encode(Config::get('auth.upload.video_allow_files', ['.flv', '.swf', '.ogg', '.wmv', '.mp4']));
        $fileAllowFiles = json_encode(Config::get('auth.upload.file_allow_files', [
            '.png', '.jpg',
            '.flv', '.swf', '.ogg', '.wmv', '.mp4',
            '.rar', '.zip', '.tar', '.gz',
            '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.pdf', '.txt', '.md', '.xml',
        ]));
        $imageCompressEnable = Config::get('auth.upload.image_compress_enable') ? 'true' : 'false';
        $imageCompressBorder = Config::get('auth.upload.image_compress_border', 2560);

        return '{
                /* 上传图片配置项 */
                "imageActionName": "uploadimage", /* 执行上传图片的action名称 */
                "imageFieldName": "upfile", /* 提交的图片表单名称 */
                "imageMaxSize": '.$imageMaxSize.', /* 上传大小限制，单位B */
                "imageAllowFiles": '.$imageAllowFiles.', /* 上传图片格式显示 */
                "imageCompressEnable": '.$imageCompressEnable.', /* 是否压缩图片,默认是true */
                "imageCompressBorder": '.$imageCompressBorder.', /* 图片压缩最长边限制 */
                "imageInsertAlign": "none", /* 插入的图片浮动方式 */
                "imageUrlPrefix": "", /* 图片访问路径前缀 */
                "imagePathFormat": "'.$uploadPath.'/images/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                                            /* {filename} 会替换成原文件名,配置这项需要注意中文乱码问题 */
                                            /* {rand:6} 会替换成随机数,后面的数字是随机数的位数 */
                                            /* {time} 会替换成时间戳 */
                                            /* {yyyy} 会替换成四位年份 */
                                            /* {yy} 会替换成两位年份 */
                                            /* {mm} 会替换成两位月份 */
                                            /* {dd} 会替换成两位日期 */
                                            /* {hh} 会替换成两位小时 */
                                            /* {ii} 会替换成两位分钟 */
                                            /* {ss} 会替换成两位秒 */
                                            /* 非法字符 \ : * ? " < > | */
                                            /* 具请体看线上文档: fex.baidu.com/ueditor/#use-format_upload_filename */

                /* 涂鸦图片上传配置项 */
                "scrawlActionName": "uploadscrawl", /* 执行上传涂鸦的action名称 */
                "scrawlFieldName": "upfile", /* 提交的图片表单名称 */
                "scrawlPathFormat": "'.$uploadPath.'/images/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                "scrawlMaxSize": '.$imageMaxSize.', /* 上传大小限制，单位B */
                "scrawlUrlPrefix": "", /* 图片访问路径前缀 */
                "scrawlInsertAlign": "none",

                /* 截图工具上传 */
                "snapscreenActionName": "uploadimage", /* 执行上传截图的action名称 */
                "snapscreenPathFormat": "'.$uploadPath.'/images/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                "snapscreenUrlPrefix": "", /* 图片访问路径前缀 */
                "snapscreenInsertAlign": "none", /* 插入的图片浮动方式 */

                /* 抓取远程图片配置 */
                "catcherLocalDomain": ["127.0.0.1", "localhost", "img.baidu.com"],
                "catcherActionName": "catchimage", /* 执行抓取远程图片的action名称 */
                "catcherFieldName": "source", /* 提交的图片列表表单名称 */
                "catcherPathFormat": "'.$uploadPath.'/images/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                "catcherUrlPrefix": "", /* 图片访问路径前缀 */
                "catcherMaxSize": '.$imageMaxSize.', /* 上传大小限制，单位B */
                "catcherAllowFiles": '.$imageAllowFiles.', /* 抓取图片格式显示 */

                /* 上传视频配置 */
                "videoActionName": "uploadvideo", /* 执行上传视频的action名称 */
                "videoFieldName": "upfile", /* 提交的视频表单名称 */
                "videoPathFormat": "'.$uploadPath.'/video/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                "videoUrlPrefix": "", /* 视频访问路径前缀 */
                "videoMaxSize": '.$videoMaxSize.', /* 上传大小限制，单位B，默认100MB */
                "videoAllowFiles": '.$videoAllowFiles.', /* 上传视频格式显示 */

                /* 上传文件配置 */
                "fileActionName": "uploadfile", /* controller里,执行上传视频的action名称 */
                "fileFieldName": "upfile", /* 提交的文件表单名称 */
                "filePathFormat": "'.$uploadPath.'/file/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                "fileUrlPrefix": "", /* 文件访问路径前缀 */
                "fileMaxSize": '.$fileMaxSize.', /* 上传大小限制，单位B，默认50MB */
                "fileAllowFiles": '.$fileAllowFiles.', /* 上传文件格式显示 */

                /* 列出指定目录下的图片 */
                "imageManagerActionName": "listimage", /* 执行图片管理的action名称 */
                "imageManagerListPath": "'.$uploadPath.'/images/", /* 指定要列出图片的目录 */
                "imageManagerListSize": 20, /* 每次列出文件数量 */
                "imageManagerUrlPrefix": "", /* 图片访问路径前缀 */
                "imageManagerInsertAlign": "none", /* 插入的图片浮动方式 */
                "imageManagerAllowFiles": '.$imageAllowFiles.', /* 列出的文件类型 */

                /* 列出指定目录下的文件 */
                "fileManagerActionName": "listfile", /* 执行文件管理的action名称 */
                "fileManagerListPath": "'.$uploadPath.'/file/", /* 指定要列出文件的目录 */
                "fileManagerUrlPrefix": "", /* 文件访问路径前缀 */
                "fileManagerListSize": 20, /* 每次列出文件数量 */
                "fileManagerAllowFiles": '.$fileAllowFiles.' /* 列出的文件类型 */
            }';
    }
}

/**
 * Created by JetBrains PhpStorm.
 * User: taoqili
 * Date: 12-7-18
 * Time: 上午11: 32
 * UEditor编辑器通用上传类.
 */
class Uploader
{
    private $fileField; //文件域名
    private $file; //文件上传对象
    private $base64; //文件上传对象
    private $config; //配置信息
    private $oriName; //原始文件名
    private $fileName; //新文件名
    private $fullName; //完整文件名,即从当前配置目录开始的URL
    private $filePath; //完整文件名,即从当前配置目录开始的URL
    private $fileSize; //文件大小
    private $fileType; //文件类型
    private $stateInfo; //上传状态信息,
    private $stateMap = array( //上传状态映射表，国际化用户需考虑此处数据的国际化
        'SUCCESS', //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        '文件大小超出 upload_max_filesize 限制',
        '文件大小超出 MAX_FILE_SIZE 限制',
        '文件未被完整上传',
        '没有文件被上传',
        '上传文件为空',
        'ERROR_TMP_FILE' => '临时文件错误',
        'ERROR_TMP_FILE_NOT_FOUND' => '找不到临时文件',
        'ERROR_SIZE_EXCEED' => '文件大小超出网站限制',
        'ERROR_TYPE_NOT_ALLOWED' => '文件类型不允许',
        'ERROR_CREATE_DIR' => '目录创建失败',
        'ERROR_DIR_NOT_WRITEABLE' => '目录没有写权限',
        'ERROR_FILE_MOVE' => '文件保存时出错',
        'ERROR_FILE_NOT_FOUND' => '找不到上传文件',
        'ERROR_WRITE_CONTENT' => '写入文件内容错误',
        'ERROR_UNKNOWN' => '未知错误',
        'ERROR_DEAD_LINK' => '链接不可用',
        'ERROR_HTTP_LINK' => '链接不是http链接',
        'ERROR_HTTP_CONTENTTYPE' => '链接contentType不正确',
        'INVALID_URL' => '非法 URL',
        'INVALID_IP' => '非法 IP',
    );

    /**
     * 构造函数.
     *
     * @param string $fileField 表单名称
     * @param array  $config    配置项
     * @param bool   $base64    是否解析base64编码，可省略。若开启，则$fileField代表的是base64编码的字符串表单名
     */
    public function __construct($fileField, $config, $type = 'upload')
    {
        $this->fileField = $fileField;
        $this->config = $config;
        $this->type = $type;
        if ('remote' == $type) {
            $this->saveRemote();
        } elseif ('base64' == $type) {
            $this->upBase64();
        } else {
            $this->upFile();
        }
    }

    /**
     * 上传文件的主处理方法.
     *
     * @return mixed
     */
    private function upFile()
    {
        $file = $this->file = $_FILES[$this->fileField];
        if (!$file) {
            $this->stateInfo = $this->getStateInfo('ERROR_FILE_NOT_FOUND');

            return;
        }

        if ($this->file['error']) {
            $this->stateInfo = $this->getStateInfo($file['error']);

            return;
        } elseif (!file_exists($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo('ERROR_TMP_FILE_NOT_FOUND');

            return;
        } elseif (!is_uploaded_file($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo('ERROR_TMPFILE');

            return;
        }

        $this->oriName = $file['name'];
        $this->fileSize = $file['size'];
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo('ERROR_SIZE_EXCEED');

            return;
        }

        //检查是否不允许的文件格式
        if (!$this->checkType()) {
            $this->stateInfo = $this->getStateInfo('ERROR_TYPE_NOT_ALLOWED');

            return;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->stateInfo = $this->getStateInfo('ERROR_CREATE_DIR');

            return;
        } elseif (!is_writeable($dirname)) {
            $this->stateInfo = $this->getStateInfo('ERROR_DIR_NOT_WRITEABLE');

            return;
        }

        //移动文件
        if (!(move_uploaded_file($file['tmp_name'], $this->filePath) && file_exists($this->filePath))) {
            //移动失败
            $this->stateInfo = $this->getStateInfo('ERROR_FILE_MOVE');
        } else {
            //移动成功
            $this->stateInfo = $this->stateMap[0];
        }
    }

    /**
     * 处理base64编码的图片上传.
     *
     * @return mixed
     */
    private function upBase64()
    {
        $base64Data = $_POST[$this->fileField];
        $img = base64_decode($base64Data);

        $this->oriName = $this->config['oriName'];
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo('ERROR_SIZE_EXCEED');

            return;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->stateInfo = $this->getStateInfo('ERROR_CREATE_DIR');

            return;
        } elseif (!is_writeable($dirname)) {
            $this->stateInfo = $this->getStateInfo('ERROR_DIR_NOT_WRITEABLE');

            return;
        }

        //移动文件
        if (!(file_put_contents($this->filePath, $img) && file_exists($this->filePath))) {
            //移动失败
            $this->stateInfo = $this->getStateInfo('ERROR_WRITE_CONTENT');
        } else {
            //移动成功
            $this->stateInfo = $this->stateMap[0];
        }
    }

    /**
     * 拉取远程图片.
     *
     * @return mixed
     */
    private function saveRemote()
    {
        $imgUrl = htmlspecialchars($this->fileField);
        $imgUrl = str_replace('&amp;', '&', $imgUrl);

        //http开头验证
        if (0 !== strpos($imgUrl, 'http')) {
            $this->stateInfo = $this->getStateInfo('ERROR_HTTP_LINK');

            return;
        }

        preg_match('/(^https*:\/\/[^:\/]+)/', $imgUrl, $matches);
        $host_with_protocol = count($matches) > 1 ? $matches[1] : '';

        // 判断是否是合法 url
        if (!filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
            $this->stateInfo = $this->getStateInfo('INVALID_URL');

            return;
        }

        preg_match('/^https*:\/\/(.+)/', $host_with_protocol, $matches);
        $host_without_protocol = count($matches) > 1 ? $matches[1] : '';

        // 此时提取出来的可能是 ip 也有可能是域名，先获取 ip
        $ip = gethostbyname($host_without_protocol);
        // 判断是否是私有 ip
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            $this->stateInfo = $this->getStateInfo('INVALID_IP');

            return;
        }

        //获取请求头并检测死链
        $heads = get_headers($imgUrl, 1);
        if (!(stristr($heads[0], '200') && stristr($heads[0], 'OK'))) {
            $this->stateInfo = $this->getStateInfo('ERROR_DEAD_LINK');

            return;
        }
        //格式验证(扩展名验证和Content-Type验证)
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileType, $this->config['allowFiles']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], 'image')) {
            $this->stateInfo = $this->getStateInfo('ERROR_HTTP_CONTENTTYPE');

            return;
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false, // don't follow redirects
            ))
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);

        $this->oriName = $m ? $m[1] : '';
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo('ERROR_SIZE_EXCEED');

            return;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->stateInfo = $this->getStateInfo('ERROR_CREATE_DIR');

            return;
        } elseif (!is_writeable($dirname)) {
            $this->stateInfo = $this->getStateInfo('ERROR_DIR_NOT_WRITEABLE');

            return;
        }

        //移动文件
        if (!(file_put_contents($this->filePath, $img) && file_exists($this->filePath))) {
            //移动失败
            $this->stateInfo = $this->getStateInfo('ERROR_WRITE_CONTENT');
        } else {
            //移动成功
            $this->stateInfo = $this->stateMap[0];
        }
    }

    /**
     * 上传错误检查.
     *
     * @param $errCode
     *
     * @return string
     */
    private function getStateInfo($errCode)
    {
        return !$this->stateMap[$errCode] ? $this->stateMap['ERROR_UNKNOWN'] : $this->stateMap[$errCode];
    }

    /**
     * 获取文件扩展名.
     *
     * @return string
     */
    private function getFileExt()
    {
        return strtolower(strrchr($this->oriName, '.'));
    }

    /**
     * 重命名文件.
     *
     * @return string
     */
    private function getFullName()
    {
        //替换日期事件
        $t = time();
        $d = explode('-', date('Y-y-m-d-H-i-s'));
        $format = $this->config['pathFormat'];
        $format = str_replace('{yyyy}', $d[0], $format);
        $format = str_replace('{yy}', $d[1], $format);
        $format = str_replace('{mm}', $d[2], $format);
        $format = str_replace('{dd}', $d[3], $format);
        $format = str_replace('{hh}', $d[4], $format);
        $format = str_replace('{ii}', $d[5], $format);
        $format = str_replace('{ss}', $d[6], $format);
        $format = str_replace('{time}', $t, $format);

        //过滤文件名的非法自负,并替换文件名
        $oriName = substr($this->oriName, 0, strrpos($this->oriName, '.'));
        $oriName = preg_replace("/[\|\?\"\<\>\/\*\\\\]+/", '', $oriName);
        $format = str_replace('{filename}', $oriName, $format);

        //替换随机字符串
        $randNum = rand(1, getrandmax()).rand(1, getrandmax());
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
        }

        $ext = $this->getFileExt();

        return $format.$ext;
    }

    /**
     * 获取文件名.
     *
     * @return string
     */
    private function getFileName()
    {
        return substr($this->filePath, strrpos($this->filePath, '/') + 1);
    }

    /**
     * 获取文件完整路径.
     *
     * @return string
     */
    private function getFilePath()
    {
        $fullname = $this->fullName;
        $rootPath = env('root_path');
        if (in_array(substr($rootPath, -1, 1), array('\\', '/'))) {
            $rootPath = substr($rootPath, 0, -1);
        }

        if ('/' != substr($fullname, 0, 1)) {
            $fullname = '/'.$fullname;
        }

        return $rootPath.$fullname;
    }

    /**
     * 文件类型检测.
     *
     * @return bool
     */
    private function checkType()
    {
        return in_array($this->getFileExt(), $this->config['allowFiles']);
    }

    /**
     * 文件大小检测.
     *
     * @return bool
     */
    private function checkSize()
    {
        return $this->fileSize <= ($this->config['maxSize']);
    }

    /**
     * 获取当前上传成功文件的各项信息.
     *
     * @return array
     */
    public function getFileInfo()
    {
        return array(
            'state' => $this->stateInfo,
            'url' => $this->fullName,
            'title' => $this->fileName,
            'original' => $this->oriName,
            'type' => $this->fileType,
            'size' => $this->fileSize,
        );
    }
}

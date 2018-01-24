<?php

namespace Lib;

/**
 * CSV导出
 */
class CSVExport {
    const CSV_DOWN = 1;
    const CSV_FILE = 2;

    /**
     * 默认10000行
     * @var integer
     * @access private
     */
    private $bufferRow = 10000;

    /**
     * The pointer to the cvs file.
     * @var resource
     * @access private
     */
    private $filePointer = null;

    /**
     * 行计数器
     * @var int
     * @access private
     */
    private $rowCounter = null;

    /**
     * The delimiter for the csv file.
     * @var str
     * @access private
     */
    private $delimiter = null;

    /**
     * 错误消息
     * @var null
     */
    private $error = null;

    /**
     * 构造函数
     * @param string $filename 文件名
     * @param string $path     文件路径
     * @param int    $type     类型，支持下载，直接导出文件
     */
    function __construct($filename, $path = '', $delimiter = ',', $type = self::CSV_DOWN) {
        $this->rowCounter = 0;
        $this->delimiter  = $delimiter;
        //HTTP下载头信息
        if ($type == self::CSV_DOWN) {
            $this->httpHeader($filename);
            $this->open();
        } else {
            $this->open(rtrim($path, '/') . "/" . $filename);
        }
    }

    /**
     * 打开读
     *
     * @return [type] [description]
     */
    public function open($file = 'php://output') {
        try {
            $this->filePointer = fopen($file, 'a');
        } catch (Exception $e) {
            throw new Exception('The file "' . $file . '" cannot be open.');
        }
    }

    /**
     * 批量写数据
     * @param  array $multiData array
     * @return [type]            [description]
     */
    public function batchPut($multiData) {
        if (empty($multiData) || !is_array($multiData)) {
            $this->error = "CSV行不能为空，且必须为数组";
            return false;
        }
        foreach ($multiData as $index => $row) {
            $res = $this->put($row);
        }
        return true;
    }

    /**
     * 写数据
     *
     * @param  array $row 数据行
     * @return self
     */
    public function put($rowData) {
        if (empty($rowData) || !is_array($rowData)) {
            $this->error = "CSV行不能为空，且必须为数组";
            return false;
        }
        $this->rowCounter++;

        //刷新一下输出buffer，防止由于数据过多造成问题
        foreach ($rowData as $key => &$value) {
            $value = iconv('utf-8', 'gb2312//IGNORE', $value);
        }
        fputcsv($this->filePointer, $rowData, $this->delimiter);

        //刷新buffer
        if (($this->rowCounter % $this->bufferRow) == 0) {
            ob_flush();
            flush();
        }
        return true;
    }

    /**
     * 设置下载头
     *
     * @param string $file_name [description]
     */
    public function httpHeader($file_name) {
        header('Pragma: public');
        // disable caching
        header("Pragma: no-cache");
        header("Expires: 0");
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: pre-check=0, post-check=0, max-age=0');
        header('Cache-Control: max-age=0');
        header('Content-Encoding: none');
        header('Content-Type: application/vnd.ms-excel');
        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        // disposition / encoding on response body
        header('Content-Disposition: attachment;filename=' . $file_name);
        header('Content-Transfer-Encoding: binary');
    }

    /**
     * 返回错误信息
     * @return string
     */
    public function getError() {
        return $this->error;
    }


    /**
     * 直接输出
     *
     * @return [type] [description]
     */
    public function output()
    {
        if ($this->filePointer) {
            ob_flush();
            flush();
        }
    }


    /**
     * 析构，关闭句柄
     */
    public function __destruct() {
        if ($this->filePointer) {
            fclose($this->filePointer);
        }
    }
}
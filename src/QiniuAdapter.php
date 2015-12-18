<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use League\Flysystem\Adapter\AbstractAdapter;
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;

class QiniuAdapter extends AbstractAdapter{
    public $accessKey;
    public $secretKey;
    public $bucketName;

    public $uploadManager;
    public $bucketManager;
    
    private $_auth;
    private $_token;
    
    function __construct($accessKey, $secretKey,$bucketName) {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->bucketName = $bucketName;
        
        $this->_auth = new Auth($this->accessKey, $this->secretKey);
        $this->_token = $this->_auth->uploadToken($this->bucketName);

        $this->uploadManager = new UploadManager();
        $this->bucketManager = new BucketManager($this->_auth);
    }

    
    public function copy($path, $newpath) {
        $ret = $this->bucketManager->copy($this->bucketName, $path, $this->bucket, $newpath);
        return $ret ? true : false;
    }

    public function createDir($dirname, \League\Flysystem\Config $config) {
        return ['path' => $dirname, 'type' => 'dir'];
    }

    public function delete($path) {
        return $this->bucketManager->delete($this->bucketName, $path);
    }

    public function deleteDir($dirname) {
        return true;
    }

    public function getMetadata($path) {
        $ret = $this->bucketManager->stat($this->bucketName, $path);
        $ret[0]['key'] = $path;
        return $this->normalizeFileInfo($ret[0]);
    }

    public function getMimetype($path) {
        $ret = $this->bucketManager->stat($this->bucketName, $path);
        return ['mimetype' => $ret[0]['mimeType']];
    }

    public function getSize($path) {
        $ret = $this->getMetadata($path);
        return $ret['size'];
    }

    public function getTimestamp($path) {
        $ret = $this->getMetadata($path);
        return $ret['timestamp'];
    }

    public function getVisibility($path) {
        return true;
    }

    public function has($path) {
        $ret = $this->bucketManager->stat($this->bucketName, $path);
        return is_array(array_shift($ret));
    }

    public function listContents($directory = '', $recursive = false) {
        $list = [];
        $ret = $this->bucketManager->listFiles($this->bucketName, $directory);
        foreach ($ret[0] as $v) {
            $list[] = $this->normalizeFileInfo($v);
        }
        return $list;
    }

    public function read($path) {
        $contents = file_get_contents('http://'.$this->bucketName.'.qiniudn.com/'.$path);
        return compact('contents', 'path');
    }

    public function readStream($path) {
        return false;
    }

    public function rename($path, $newpath) {
        $ret = $this->bucketManager->rename($this->bucketName, $path, $newpath);
        return $ret ? true : false;
    }

    public function setVisibility($path, $visibility) {
        return false;
    }

    public function update($path, $contents, \League\Flysystem\Config $config) {
        $this->delete($path);
        return $this->write($path, $contents, $config); 
    }

    public function updateStream($path, $resource, \League\Flysystem\Config $config) {
        $this->delete($path);
        return $this->writeStream($path, $resource, $config); 
    }

    public function write($path, $contents, \League\Flysystem\Config $config) {
        list($ret, $error) = $this->uploadManager->put($this->_token, $path, $contents);
        return $error ? false : $ret;
    }

    public function writeStream($path, $resource, \League\Flysystem\Config $config) {
        return false;
    }

    private function normalizeFileInfo($info){
        return array(
            'type' => 'file',
            'path' => $info['key'],
            'timestamp' => floor($info['putTime']/10000000),
            'size' => $info['fsize'],
        );
    }

}

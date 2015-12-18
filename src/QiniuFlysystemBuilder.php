<?php

namespace aiddroid\flysystem\qiniu;

use aiddroid\flysystem\qiniu\QiniuAdapter;
use League\Flysystem\Filesystem;
use trntv\filekit\filesystem\FilesystemBuilderInterface;

/**
 * Class QiniuFlysystemBuilder
 * @author aiddroid <aiddroid@gmail.com>
 */
class QiniuFlysystemBuilder implements FilesystemBuilderInterface
{
    public $accessKey;
    public $secretKey;
    public $bucketName;

    public function build()
    {
        $adapter = new QiniuAdapter($this->accessKey, $this->secretKey, $this->bucketName);
        return new Filesystem($adapter);
    }
}


# filesystem-qiniu
Flysystem Adapter for Qiniu sdk v7.x


## 安装

* 通过composer，这是推荐的方式，可以使用composer.json 声明依赖，或者运行下面的命令。
```bash
$ composer require aiddroid/flysystem-qiniu
```

### Yii2配置config/web.php
```php
  'components' => [
        //...
        'fileStorage' => [
            'class' => '\trntv\filekit\Storage',
            'baseUrl' => '@storageUrl/source',
            'filesystem' => [
                'class' => '\aiddroid\flysystem\qiniu\QiniuFlysystemBuilder',
                'accessKey' => 'testAccessKey',//Qiniu的配置参数 http://www.qiniu.com/
                'secretKey' => 'testSecretKey',
                'bucketName' => 'testbucket',
            ]
        ],
        //...
```

### Yii2用法
```php
$uploadedFile = \yii\web\UploadedFile::getInstance($uploadForm, 'avatarFile');
Yii::$app->fileStorage->save($uploadedFile);
```

### 一般用法
```php
use aiddroid\flysystem\qiniu\QiniuAdapter;
$adapter = new QiniuAdapter($accessKey, $secretKey,$bucketName);
$adapter->write('filepath','contents');
```


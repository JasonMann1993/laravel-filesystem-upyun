<h1 align="center"> laravel-filesystem-upyun </h1>

<p align="center"> A Upyun storage filesystem for Laravel..</p>

## 扩展包要求

-   PHP >= 7.0

## 安装

```shell
$ composer require jasonmann/laravel-filesystem-upyun -vvv
```

## 配置

#### Laravel

1. 将服务提供者 `Jasonmann\LaravelFilesystem\Upyun\UpyunServiceProvider::class` 注册到 `config/app.php` 文件:

```php
'providers' => [
    // Other service providers...
   Jasonmann\LaravelFilesystem\Upyun\UpyunServiceProvider::class,
],
```

2. 在 `config/filesystems.php` 配置文件中添加你的新驱动

```php
<?php
return [
   'disks' => [
        //...
        'upyun' => [
                        'driver'        => 'upyun', 
                        'bucket'        => 'your-bucket-name',// 服务名字
                        'operator'      => 'oparator-name', // 操作员的名字
                        'password'      => 'operator-password', // 操作员的密码
                        'domain'        => 'xxxxx.b0.upaiyun.com', // 服务分配的域名
                        'protocol'     => 'https', // 服务使用的协议，如需使用 http，在此配置 http
                    ],
        //...
    ]
];
```

#### Lumen

1. 将服务提供者 `Jasonmann\LaravelFilesystem\Upyun\UpyunServiceProvider::class` 注册到 `bootstrap/app.php` 文件:

```php
$app->register(\Jasonmann\LaravelFilesystem\Upyun\UpyunServiceProvider::class);
```

2. 在 `config/filesystems.php` 配置文件中添加你的新驱动

```php
<?php
return [
   'disks' => [
        //...
        'upyun' => [
                        'driver'        => 'upyun', 
                        'bucket'        => 'your-bucket-name',// 服务名字
                        'operator'      => 'oparator-name', // 操作员的名字
                        'password'      => 'operator-password', // 操作员的密码
                        'domain'        => 'xxxxx.b0.upaiyun.com', // 服务分配的域名
                        'protocol'     => 'https', // 服务使用的协议，如需使用 http，在此配置 http
                    ],
        //...
    ]
];
```

## 使用

```php
bool $flysystem->write('file.md', 'contents');

bool $flysystem->writeStream('file.md', fopen('path/to/your/local/file.jpg', 'r'));

bool $flysystem->update('file.md', 'new contents');

bool $flysystem->updateStram('file.md', fopen('path/to/your/local/file.jpg', 'r'));

bool $flysystem->rename('foo.md', 'bar.md');

bool $flysystem->copy('foo.md', 'foo2.md');

bool $flysystem->delete('file.md');

bool $flysystem->has('file.md');

string|false $flysystem->read('file.md');

array $flysystem->listContents();

array $flysystem->getMetadata('file.md');

int $flysystem->getSize('file.md');

string $flysystem->getUrl('file.md'); 

string $flysystem->getMimetype('file.md');

int $flysystem->getTimestamp('file.md');
// 获取 upyun 实例 实例中可用方法，可以参照 https://github.com/JasonMann1993/upyun-php-sdk/blob/master/src/Upyun/Upyun.php
obj $flysystem->kernel();
```

## License

MIT
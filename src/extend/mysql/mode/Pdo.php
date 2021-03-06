<?php

namespace fize\database\extend\mysql\mode;


use fize\database\extend\mysql\Db;
use fize\database\middleware\Pdo as Middleware;

/**
 * PDO
 *
 * PDO方式(推荐使用)MySQL数据库模型类
 */
class Pdo extends Db
{
    use Middleware;

    /**
     * Pdo方式构造
     * @param string $host    服务器地址
     * @param string $user    用户名
     * @param string $pwd     用户密码
     * @param string $dbname  数据库名
     * @param int    $port    端口号，选填，MySQL默认是3306
     * @param string $charset 指定编码，选填，默认utf8
     * @param array  $opts    PDO连接的其他选项，选填
     * @param string $socket  指定应使用的套接字或命名管道,windows不可用，选填，默认不指定
     */
    public function __construct($host, $user, $pwd, $dbname, $port = null, $charset = "utf8", array $opts = [], $socket = null)
    {
        $dsn = "mysql:host={$host};dbname={$dbname}";
        if (!empty($port)) {
            $dsn .= ";port={$port}";
        }
        if (!empty($socket)) {
            $dsn .= ";unix_socket={$socket}";
        }
        if (!empty($charset)) {
            $dsn .= ";charset={$charset}";
        }
        $this->pdoConstruct($dsn, $user, $pwd, $opts);
    }

    /**
     * 析构时关闭PDO
     */
    public function __destruct()
    {
        $this->pdoDestruct();
        parent::__destruct();
    }
}

<?php
/** @noinspection PhpComposerExtensionStubsInspection */

namespace fize\db\realization\access\mode;

use fize\db\realization\access\Db;
use fize\db\middleware\Pdo as Middleware;
use fize\db\exception\Exception;


/**
 * PDO方式(推荐使用)ACCESS数据库模型类
 */
class Pdo extends Db
{
    use Middleware {
        Middleware::query as protected queryPdo;
    }

    /**
     * Pdo方式构造必须实例化$this->_pdo
     * @param string $file Access文件路径
     * @param string $pwd 用户密码
     * @param string $prefix 指定全局前缀，选填，默认空字符
     * @param string $driver 指定ODBC驱动名称。
     */
    public function __construct($file, $pwd = null, $prefix = "", $driver = null)
    {
        $this->tablePrefix = $prefix;
        if (is_null($driver)) {  //默认驱动名
            $driver = "Microsoft Access Driver (*.mdb, *.accdb)";
        }
        $dsn = "odbc:Driver={" . $driver . "};DSN='';DBQ=" . realpath($file) . ";";
        if($pwd) {
            $dsn .= "PWD={$pwd};";
        }
        $this->pdoConstruct($dsn, null, null);
    }

    /**
     * 析构时释放PDO资源
     */
    public function __destruct()
    {
        $this->pdoDestruct();
        parent::__destruct();
    }

    /**
     * 根据SQL预处理语句和绑定参数，返回实际的SQL
     * @param string $sql SQL语句，支持原生的ODBC问号预处理
     * @param array $params 可选的绑定参数
     * @return string
     */
    private function getRealSql($sql, array $params = [])
    {
        if(!$params) {
            return $sql;
        }
        $temp = explode('?', $sql);
        $last_sql = "";
        for ($i = 0; $i < count($temp) - 1; $i++) {
            $last_sql .= $temp[$i] . $this->parseValue($params[$i]);
        }
        $last_sql .= $temp[count($temp) - 1];
        return $last_sql;
    }

    /**
     * 执行一个SQL语句并返回相应结果
     * @param string $sql SQL语句，支持原生的pdo问号预处理
     * @param array $params 可选的绑定参数
     * @param callable $callback 如果定义该记录集回调函数则不返回数组而直接进行循环回调
     * @return array|int|null SELECT语句返回数组或不返回，INSERT/REPLACE返回自增ID，其余返回受影响行数
     * @throws Exception
     */
    public function query($sql, array $params = [], callable $callback = null)
    {
        $sql = $this->getRealSql($sql, $params);
        $sql = iconv('UTF-8', 'GBK', $sql);

        if (stripos($sql, "SELECT") === 0) {
            $sql = $this->getRealSql($sql, $params);
            if ($callback !== null) {
                foreach ($this->pdo->query($sql) as $row) {
                    array_walk($row, function(&$value){
                        $value = iconv('GBK', 'UTF-8', $value);
                    });
                    $callback($row);
                }
                return null;
            } else {
                $rows = [];
                foreach ($this->pdo->query($sql) as $row) {
                    array_walk($row, function(&$value){
                        $value = iconv('GBK', 'UTF-8', $value);
                    });
                    $rows[] = $row;
                }
                return $rows;
            }
        }
        return $this->queryPdo($sql);
    }
}
<?php

namespace fize\database\extend\pgsql;

use fize\database\core\Db as CoreDb;

/**
 * 数据库
 *
 * PostgreSQL的ORM模型
 */
abstract class Db extends CoreDb
{
    use Feature;

    /**
     * @var string LIMIT语句
     */
    protected $limit = "";

    /**
     * 设置LIMIT,支持链式调用
     * @param int $rows   要返回的记录数
     * @param int $offset 要设置的偏移量
     * @return $this
     */
    public function limit($rows, $offset = null)
    {
        if (is_null($offset)) {
            $this->limit = (string)$rows;
        } else {
            $this->limit = (string)$offset . "," . (string)$rows;
        }
        return $this;
    }
}

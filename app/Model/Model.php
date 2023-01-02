<?php
require_once('Connection.php');
require_once __DIR__ . "/../Http/Response/Response.php";

class Model extends Connection
{
    protected $table;
    protected $tableKey = 'id';
    protected $selectedColumms = '*';
    protected $offset = 0;
    protected $limit = null;
    protected $meta = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function query(string $sql, array $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {

            Response::toJSON([
                                 'Erro'        => 'Failed to execute SQL query',
                                 'debug_error' => $e->getMessage(),
                             ],
                             500);
        }
    }

    public function execStatement(string $sql, array $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);

            return $stmt->rowCount();
        } catch (\PDOException $e) {
            Response::toJSON([
                                 'Erro'        => 'Failed to execute SQL script statement',
                                 'debug_error' => $e->getMessage(),
                             ],
                             500);
        }
    }

    public function select($columns)
    {
        $this->selectedColumms = $columns;

        return $this;
    }

    public function paginate($page, $limit = 25)
    {
        $count        = $this->count();
        $this->offset = ($page - 1) * $limit;
        $this->limit  = $limit;

        $this->meta = [
            'total_records' => $count,
            'current_page'  => (int) $page,
            'total_pages'   => ceil($count / $limit),
        ];

        return $this;
    }

    public function find($id)
    {
        $stmt = "
            SELECT 
               {$this->selectedColumms}
            FROM
                {$this->table}
            WHERE {$this->tableKey} = ?;
        ";

        return $this->query($stmt, [$id])[0] ?? [];
    }

    public function findByColumn($column, $value, $limit = 1)
    {
        $stmt = "
            SELECT 
               {$this->selectedColumms}
            FROM
                {$this->table}
            WHERE {$column} = ? LIMIT {$limit};
        ";

        return $this->query($stmt, [$value]);
    }

    public function findAll()
    {
        $stmt = "
            SELECT 
               {$this->selectedColumms}
            FROM
                {$this->table}
        ";

        if ($this->limit) {
            $stmt .= " LIMIT {$this->limit} OFFSET {$this->offset}";
        }

        $data = $this->query($stmt);

        $response['data'] = $data;

        if ($this->limit) {
            $response['meta'] = $this->meta;
        }

        return $response;
    }

    public function insert(array $data)
    {
        $columns = implode(',', array_keys($data));
        $values  = ':' . implode(',:', array_keys($data));

        $stmt = "
                INSERT INTO 
                    $this->table
                ($columns)
                    VALUES
                ($values);           
              ";

        return $this->execStatement($stmt, $data);
    }

    public function update(int $id, array $data)
    {
        foreach ($data as $column => $value) {
            $columns[] = "$column = :$column";
        }

        $data[$this->tableKey] = $id;

        $columns = implode(',', $columns);

        $stmt = "
                UPDATE 
                    {$this->table}
                SET {$columns}
                WHERE {$this->tableKey} = :{$this->tableKey};           
              ";

        return $this->execStatement($stmt, $data);
    }

    public function delete(int $id)
    {
        $stmt = "
            DELETE 
                from $this->table
            WHERE {$this->tableKey} = ?
        ";

        return $this->execStatement($stmt, [$id]);
    }

    public function count()
    {
        $stmt = "
            SELECT 
                COUNT({$this->tableKey}) AS COUNT
            FROM {$this->table};
        ";

        return $this->query($stmt)[0]['COUNT'] ?? 0;
    }
}
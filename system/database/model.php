<?php

abstract class model
{

    protected $db;

    protected $where;

    protected $where_params = array();

    protected $limit;

    protected $order_by;

    protected $all = 0;

    public function __construct()
    {
        $this->db = new driver;
    }

    public function all()
    {
        $this->all = 1;

        return $this;
    }

    public function where($name, $operator, $value)
    {
        $this->where = $name." ".$operator." :where_value";
        $this->where_params = [":where_value" => $value];

        return $this;
    }

    public function limit($fields)
    {
        if (is_array($fields))
        {
            $this->limit = implode(",", $fields);
        }
        else
        {
            $this->limit = $fields;
        }

        return $this;
    }

    public function order_by($fields)
    {
        if (is_array($fields))
        {
            $this->order_by = implode(" ", $fields);
        }

        return $this;
    }

    public function select($table, $fields = null)
    {
        if ($fields == null)
        {
            $fields = "*";
        }
        else
        {
            if (is_array($fields))
            {
                $fields = implode(",", $fields);
            }
        }

        $sql = "SELECT $fields FROM $table";

        if ($this->where != null)
        {
            $sql .= " WHERE ".$this->where;
        }

        if ($this->order_by != null)
        {
            $sql .= " ORDER BY ".$this->order_by;
        }

        if ($this->limit != null)
        {
            $sql .= " LIMIT ".$this->limit;
        }

        if ($this->where != null)
        {
            $query = $this->db->prepare($sql);
            $this->db->auto_bind($query, ["where_value" => $this->where_params[":where_value"]]);
            $this->db->execute($query);
        }
        else
        {
            $query = $this->db->query($sql);
        }

        $this->where = null;
        $this->where_params = array();
        $this->limit = null;
        $this->order_by = null;

        if ($this->all == 0)
        {
            $this->all = 0;
            return $this->fetch($this->db->fetch($query));
        }
        else
        {
            $i = 0;
            $result = array();
    
            while($data = $this->db->fetch($query))
            {
                $result[$i] = $data;
                $i++;
            }

            $this->all = 0;
            return $this->fetch($result);
        }
    }

    public function insert($table, $data = array())
    {
        $keys = array_keys($data);
        $params = array();

        foreach ($keys as $key)
        {
            $params[] = ":".$key;
        }

        $sql = "INSERT INTO $table (".implode(",", $keys).") VALUES (".implode(",", $params).")";

        $query = $this->db->prepare($sql);
        $this->db->auto_bind($query, $data);

        return $this->db->execute($query);
    }

    public function update($table, $data, $where)
    {
        $data_key = key($data);
        $data_val = current($data);
        $where_key = key($where);
        $where_val = current($where);

        $sql  = "UPDATE $table SET $data_key = :data_value";
        $sql .= " WHERE $where_key = :where_value";

        $query = $this->db->prepare($sql);
        $this->db->bind($query, ":data_value", $data_val);
        $this->db->bind($query, ":where_value", $where_val);

        return $this->db->execute($query);
    }

    public function delete($table, $where)
    {
        $where_key = key($where);
        $where_val = current($where);

        $sql = "DELETE FROM $table WHERE $where_key = :where_value";

        $query = $this->db->prepare($sql);
        $this->db->bind($query, ":where_value", $where_val);

        return $this->db->execute($query);
    }

    public function fetchColumn($table, $where = null)
    {
        $sql = "SELECT COUNT(*) FROM $table";

        if ($where != null)
        {
            $where_key = key($where);
            $where_val = current($where);

            $sql .= " WHERE $where_key = :where_value";

            $query = $this->db->prepare($sql);
            $this->db->bind($query, ":where_value", $where_val);
            $this->db->execute($query);

            return $this->db->fetchColumn($query);
        }

        $query = $this->db->query($sql);

        return $this->db->fetchColumn($query);
    }

    public function fetch($array)
    {
        $data = new stdClass;
        $super = "_";

        foreach ($array as $key => $val)
        {
            if (is_int($key))
            {
                $key = $super.$key;
            }

            $data->$key = $val;
        }

        return $data;
    }

}

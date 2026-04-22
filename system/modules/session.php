<?php

class session extends model
{

    public function __construct()
    {
        parent::__construct();
        $this->start();
    }

    public function start()
    {
        session_name("user");
        session_start();
    }

    public function count()
    {
        $query = $this->db->query("SELECT COUNT(*) FROM sessions");
        $data = $this->db->fetchColumn($query);
      
        return ($data) ? $data : 0;
    }

    public function online()
    {
        $query = $this->db->query("SELECT COUNT(user) FROM sessions WHERE user != 0");
        $data = $this->db->fetchColumn($query);
      
        return ($data) ? $data : 0;
    }

    public function get($id, $column)
    {
        $query = $this->db->prepare("SELECT $column FROM sessions WHERE session = :session");
        $this->db->bind($query, ":session", $id);
        $this->db->execute($query);

        $row = $this->db->fetch($query);

        if (!$row || !isset($row->$column))
        {
            return 0;
        }

        return $row->$column;
    }

    public function read($id)
    {
        $query = $this->db->prepare("SELECT * FROM sessions WHERE session = :session");
        $this->db->bind($query, ":session", $id);
        $this->db->execute($query);

        $data = $this->db->fetch($query);
        return $this->fetch($data);
    }

    public function write($id, $user = 0)
    {
        $array = array 
        (
            "session"   => $id,
            "user"      => $user,
            "ip"        => settings::$ip,
            "timestamp" => settings::$date." ".settings::$time,
            "browser"   => settings::$ua
        );

        $keys = implode(", ", array_keys($array));
        $vals  = implode(", :", array_keys($array));

        $query = $this->db->prepare("REPLACE INTO sessions ($keys) VALUES (:$vals)");

        $this->db->auto_bind($query, $array);
        $data = $this->db->execute($query);

        return ($data) ? 1 : 0;
    }

    public function control($id)
    {
        if ($this->count() != 0)
        {
            if ($this->get($id, "user") != 0)
            {
                $this->write($id, $this->get($id, "user"));
            }
            else
            {
                $this->write($id);
            }
        }
        else
        {
            $this->write($id);
        }

        $this->clean();
    }

    public function destroy($id)
    {
        $query = $this->db->prepare("DELETE FROM sessions WHERE session = :session");
        $this->db->bind($query, ":session", $id);
        $data = $this->db->execute($query);
      
        if ($data)
        {
            session_destroy();
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function clean()
    {
        $result = $this->db->prepare("DELETE FROM sessions WHERE timestamp < (NOW() - INTERVAL 15 MINUTE)");
        $this->db->execute($result);
    }

    public function close()
    {
        session_write_close();
    }

    public function __destruct()
    {
        $this->close();
    }

}

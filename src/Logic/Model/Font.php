<?php

namespace App\Logic\Model;

use App\Core\Model;

class Font extends Model
{
    public int $id;
    public string $name;
    public string $path;

    function __construct()
    {
        parent::__construct();

        $this->db->prepare(<<<SQL
            CREATE TABLE IF NOT EXISTS fonts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                path VARCHAR(200) NOT NULL
            )
        SQL)->execute();
    }

    function fetchAll()
    {
        $stmt = $this->db->prepare('SELECT * FROM fonts');
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\Logic\Model\Font');
        $fonts = $stmt->fetchAll();
        return $fonts;
    }

    function fetchOne(int $id)
    {
        $stmt = $this->db->prepare('SELECT * FROM fonts WHERE id = :id');
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\Logic\Model\Font');
        $font = $stmt->fetch();
        return $font;
    }

    function insert()
    {
        if (empty($this->name) || empty($this->path)) {
            throw new \Exception('INVALID!');
        }

        $stmt = $this->db->prepare('INSERT INTO fonts (name, path) VALUES (:name, :path)');
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':path', $this->path);
        $stmt->execute();
        $this->id = $this->db->lastInsertId();
    }

    function delete()
    {
        if (empty($this->id)) {
            throw new \Exception('INVALID!');
        }

        $stmt = $this->db->prepare('SELECT * FROM fonts WHERE id = :id');
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\Logic\Model\Font');
        $font = $stmt->fetch();

        if (empty($font)) {
            throw new \Exception('INVALID!');
        }

        $this->name = $font->name;
        $this->path = $font->path;

        $stmt = $this->db->prepare('DELETE FROM fonts WHERE id = :id');
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
    }
}

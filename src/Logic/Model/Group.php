<?php

namespace App\Logic\Model;

use App\Core\Model;
use App\Logic\Model\Font;

class Group extends Model
{
    public int $id;
    public string $name;
    public array $fonts;

    function __construct()
    {
        parent::__construct();

        $this->db->prepare(<<<SQL
            CREATE TABLE IF NOT EXISTS groups (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL
            )
        SQL)->execute();
        $this->db->prepare(<<<SQL
            CREATE TABLE IF NOT EXISTS groups_fonts (
                group_id INTEGER NOT NULL REFERENCES groups (id),
                font_id INTEGER NOT NULL REFERENCES fonts (id),
                PRIMARY KEY (group_id, font_id)
            )
        SQL)->execute();
    }

    function fetchAll()
    {
        $stmt = $this->db->prepare(
            <<<SQL
                SELECT
                    g.*,
                    f.id AS font_id,
                    f.name AS font_name,
                    f.path AS font_path
                FROM groups AS g
                JOIN groups_fonts AS gf ON g.id = gf.group_id
                JOIN fonts AS f ON gf.font_id = f.id
            SQL
        );
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $this->_process($result);
    }

    function fetchOne(int $id)
    {
        $stmt = $this->db->prepare(
            <<<SQL
                SELECT
                    g.*,
                    f.id AS font_id,
                    f.name AS font_name,
                    f.path AS font_path
                FROM groups AS g
                WHERE g.id = :id
                JOIN groups_fonts AS gf ON g.id = gf.group_id
                JOIN fonts AS f ON gf.font_id = f.id
            SQL
        );
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $this->_process($result);
    }

    function insert()
    {
        if (empty($this->name) || empty($this->fonts)) {
            throw new \Exception('INVALID!');
        }

        $stmt = $this->db->prepare('INSERT INTO groups (name) VALUES (:name)');
        $stmt->bindParam(':name', $this->name);
        $stmt->execute();
        $this->id = $this->db->lastInsertId();

        $stmt = $this->db->prepare('INSERT INTO groups_fonts (group_id, font_id) VALUES (:groupId, :fontId)');
        foreach ($this->fonts as $font) {
            $stmt->bindParam(':groupId', $this->id, \PDO::PARAM_INT);
            $stmt->bindParam(':fontId', $font->id, \PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    function update() {}

    function delete()
    {
        if (empty($this->id)) {
            throw new \Exception('INVALID!');
        }

        $stmt = $this->db->prepare('SELECT * FROM groups WHERE id = :id');
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\Logic\Model\Font');
        $group = $stmt->fetch();

        if (empty($group)) {
            throw new \Exception('INVALID!');
        }

        $this->name = $group->name;

        $stmt = $this->db->prepare('DELETE FROM groups WHERE id = :id');
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    private function _process(array $result)
    {
        $groups = [];

        foreach ($result as $row) {
            $exGroup = array_filter($groups, function ($group) use ($row) {
                return $group['id'] === $row['id'];
            });
            if (empty($exGroup)) {
                $groups[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'fonts' => [],
                ];
            }

            foreach ($groups as &$group) {
                if ($group['id'] === $row['id']) {
                    $font = new Font();
                    $font->id = $row['font_id'];
                    $font->name = $row['font_name'];
                    $font->path = $row['font_path'];
                    $group['fonts'][] = $font;
                    break;
                }
            }
        }

        return $groups;
    }
}

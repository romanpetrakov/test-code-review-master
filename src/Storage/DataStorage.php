<?php

namespace App\Storage;

use App\Model;

class DataStorage
{
    /**
     * @var \PDO 
     */
    public $pdo;

    public function __construct()
    {//параметры работы с базой данных должны быьт вынесены в конфигурационные файлы.
        $this->pdo = new \PDO('mysql:dbname=task_tracker;host=127.0.0.1', 'user');
    }

    /**
     * @param int $projectId
     * @throws Model\NotFoundException
     */
    public function getProjectById($projectId)
    {
        $stmt = $this->pdo->query('SELECT * FROM project WHERE id = ' . (int) $projectId);
        //почему для  $projectId не используется параметризация?

        if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return new Model\Project($row);
        }

        throw new Model\NotFoundException();
    }

    /**
     * @param int $project_id
     * @param int $limit
     * @param int $offset
     */
    public function getTasksByProjectId(int $project_id, $limit, $offset)// остуствует декларация типов параметров метода
    {
        $stmt = $this->pdo->query("SELECT * FROM task WHERE project_id = $project_id LIMIT ?, ?");
        //почему для  $projectId не используется параметризация?
        $stmt->execute([$limit, $offset]);

        $tasks = [];
        foreach ($stmt->fetchAll() as $row) {
            $tasks[] = new Model\Task($row);
        }

        return $tasks;
    }

    /**
     * @param array $data
     * @param int $projectId
     * @return Model\Task
     */
    public function createTask(array $data, $projectId)
    {
        $data['project_id'] = $projectId;

        $fields = implode(',', array_keys($data));
        $values = implode(',', array_map(function ($v) {
            return is_string($v) ? '"' . $v . '"' : $v;
        }, $data));
//данные в $data взяты из _REQUEST не провалидированы и подставляются в запрос. нет ли тут уязвимости?
        // а есле в data окажутся данные которые по структуре не совпадут с таблицей в бд?
        $this->pdo->query("INSERT INTO task ($fields) VALUES ($values)");
        $data['id'] = $this->pdo->query('SELECT MAX(id) FROM task')->fetchColumn();

        return new Model\Task($data);
    }
}

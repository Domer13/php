<?php


namespace NewK\routes;


use PDO;
use PDOException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Requests
{
    /**
     * @var PDO
     */
    private $db;

    /**
     * Requests constructor.
     */
    public function __construct(Container $container)
    {
        $this->db = $container->get('db');
    }

    public function NewRequest(Request $request, Response $response)
    {
        $token = $request->getHeader("X-Token")[0];
        $sql = "SELECT * FROM tokens WHERE tokens.token = '$token'";
        try {
            $result = $this->fetchDB($sql, 0);
            $uid = $result->uid;
        } catch (PDOException $e) {
            return $response->withJson($this->displayError($e));
        }
        $body = $request->getParsedBody();
        $conditions = [];
        $bindVars = [];
        $updateable = ['title', 'description'];

        foreach ($updateable as $key) {
            if (isset($body[$key])) {
                $bindVars[$key] = $body[$key];
                $conditions[] = $key . ' = :' . $key;
            }
        }

        $sql_request = "INSERT INTO requests (title, description, uid, state, vote) VALUES (:title,:description,:uid, 0, 0)";
        try {
            $insert = $this->db->prepare($sql_request);
            foreach ($bindVars as $key => $value) {
                $insert->bindParam(':' . $key, $value);
            }
            $insert->bindParam(':uid', $uid);
            $insert->execute();
            $id = $this->db->lastInsertId();
            $db = null;


            return $response->withJson(['id' => $id, 'uid' => $uid] + $body);

        } catch (PDOException $e) {
            return $response->withJson($this->displayError($e));
        }
    }

    public function ListRequests(Request $request, Response $response)
    {
        $sql = "SELECT * FROM requests";
        try {
            return $response->withJson($this->fetchDB($sql));

        } catch (PDOException $e) {
            return $response->withJson($this->displayError($e));
        }
    }

    public function UpdateRequest(Request $request, Response $response)
    {
        $requestId = $request->getAttribute('id');
        $token = $request->getHeader("X-Token")[0];
        $sql_user_id_by_token = "SELECT * FROM tokens WHERE tokens.token = '" . $token . "'";
        try {
            $result = $this->fetchDB($sql_user_id_by_token, 0);
            $uid = $result->uid;
            $sql_users_id = "SELECT * FROM users WHERE id = $uid";
            $type = $this->fetchDB($sql_users_id, 0)->type;
        } catch (PDOException $e) {
            return $response->withJson($this->displayError($e));
        }
        $body = $request->getParsedBody();

        $conditions = [];
        $bindVars = [];
        $updateable = ['title', 'description'];
        if ($type == 0) {
            $updateable[] = 'vote';
        } else {
            $updateable[] = 'state';
        }

        foreach ($updateable as $key) {
            if (isset($body[$key])) {
                $bindVars[$key] = $body[$key];
                $conditions[] = $key . ' = :' . $key;
            }
        }
        if (count($conditions) > 1) {
            $sql_update = "UPDATE requests SET " . implode(', ', $conditions) . " WHERE id = :id";
            try {
                $updSql = $this->db->prepare($sql_update);
                foreach ($bindVars as $key => $value) {
                    $updSql->bindParam(':' . $key, $value);
                }
                $updSql->bindParam(":id", $requestId);
                $updSql->execute();
                return $response->withJson($this->fetchDB("select * from requests where id=" . $requestId, 0));
            } catch (PDOException $e) {
                return $response->withJson($this->displayError($e));
            }
        }
    }

    private function displayError(PDOException $exception)
    {
        return [
            'error' => $exception->getMessage(),
        ];
    }

    private function fetchDB($sql, $one = true)
    {
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        if ($one) {
            return $result;
        } else {
            return $result[0];
        }
    }
}
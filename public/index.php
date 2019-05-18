<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\App;

require '../src/config/db.php';
require '../vendor/autoload.php';

function forSelect($sql, $one = false)
{
    $db = new db();
    $db = $db->connect();
    $db->query("SET NAMES 'utf8'");
    $db->query("SET CHARSET 'utf8'");
    $stmt = $db->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    if ($one) {
        return $result;
    } else {
        return $result[0];
    }

}


$app = new App;
//user get/post/put/delete
$app->get('/users', function (Request $request, Response $response) {
    $sql = "SELECT * FROM users";
    try {
        return $response->withJson(forSelect($sql));
    } catch (PDOException $e) {
        echo '{"error": {"text": ' . $e->getMessage() . '}';
    }
});
$app->get('/users/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');

    $sql = "SELECT * FROM users WHERE id = " . $id;
    try {
        $db = new db();
        $db = $db->connect();
        $db->query("SET NAMES 'utf8'");
        $db->query("SET CHARSET 'utf8'");
        $stmt = $db->query($sql);

        $user = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        return $response->withJson($user);

    } catch (PDOException $e) {
        echo '{"error": {"text": ' . $e->getMessage() . '}';
    }
});
$app->post('/users', function (Request $request, Response $response) {

    $body = $request->getParsedBody();
    $login = $body['login'];
    $password = md5($body['password']);
    if (!empty($body['type'])) {
        $type = $body['type'];
    } else {
        $type = 0;
    };

    $sql = "INSERT INTO users (login,password,type) VALUES(:login,:password,:type)";

    $sql_token = "INSERT INTO tokens (uid,token) VALUE(:uid,:password,:token)";
    try {


        $db = new db();
        $db = $db->connect();
        $stmt1 = $db->prepare($sql);
        $stmt1->bindParam(':login', $login);
        $stmt1->bindParam(':password', $password);
        $stmt1->bindParam(':type', $type);
        $stmt1->execute();

        $stmt2 = $db->prepare($sql_token);
        $stmt2->bindParam(':uid', $uid);
        $stmt2->bindParam(':token', $token);
        $stmt2->execute();

        $db = null;
        echo '{"notice":{"text":"User Added"}';

    } catch (PDOException $e) {
        echo '{"error": {"text": ' . $e->getMessage() . '}';
    }
});
$app->put('/users/update/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');

    $login = $request->getParam('login');
    $password = $request->getParam('password');

    $sql = "UPDATE users SET login = :login,password = :password WHERE id = $id";

    try {
        $db = new db();
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':password', $password);

        $stmt->execute();

        $db = null;

        echo '{"notice":{"text":"User Updated"}';

    } catch (PDOException $e) {
        echo '{"error": {"text": ' . $e->getMessage() . '}';
    }
});
$app->delete('/users/delete/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM users WHERE id = $id";

    try {
        $db = new db();
        $db = $db->connect();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        $db = null;

        echo '{"notice":{"text":"User Deleted"}';

    } catch (PDOException $e) {
        echo '{"error": {"text": ' . $e->getMessage() . '}';
    }
});

//Подача заявки get/post/put/delete
$app->post('/request', function (Request $request, Response $response) {
    $token = $request->getHeader("X-Token")[0];
    $sql = "SELECT * FROM tokens WHERE tokens.token = $token";
    try {
        $result = forSelect($sql, 0);
        $uid = $result->uid;

        $body = $request->getParsedBody();
        $title = $body['title'];
        $description = $body['description'];
    } catch (PDOException $e) {
        echo '{"error": {"text": ' . $e->getMessage() . '}';
    }

    if (isset($body['state'])) {
        $state = $body['state'];
    } else {
        $state = 0;
    }
    if (isset($body['vote'])) {
        $vote = $body['vote'];
    } else {
        $vote = 0;
    }
    $sql_request = "INSERT INTO requests (title, description, state, vote, uid) VALUES (:title,:description,:state,:vote,:uid)";
    try {
        $db = new db();
        $db = $db->connect();
        $stmt1 = $db->prepare($sql_request);
        $stmt1->bindParam(':title', $title);
        $stmt1->bindParam(':description', $description);
        $stmt1->bindParam(':state', $state);
        $stmt1->bindParam(':vote', $vote);
        $stmt1->bindParam(':uid', $uid);
        $stmt1->execute();
        $db = null;

        $db = new db();
        $search_id = "SELECT * FROM requests WHERE title = '" . $title . "' AND uid = $uid";
        $db = $db->connect();
        $stmt5 = $db->query($search_id);
        $sid = $stmt5->fetchAll(PDO::FETCH_OBJ);
        $id = $sid[0]->id;
        $db = null;

        return json_encode($sid[0]);

    } catch (PDOException $e) {
        echo '{"error": {"text": ' . $e->getMessage() . '}';
    }
});
$app->get('/request', function (Request $request, Response $response) {
    $sql = "SELECT * FROM requests";
    try {
        return $response->withJson(forSelect($sql));

    } catch (PDOException $e) {
        echo '{"error": {"text": ' . $e->getMessage() . '}';
    }
});
$app->put('/request/{id}', function (Request $request, Response $response) {
    $id_put = $request->getAttribute('id');
    $token = $request->getHeader("X-Token")[0];
    $sql_user_id_by_token = "SELECT * FROM tokens WHERE tokens.token = '".$token."'";
    echo $sql_user_id_by_token;
    try {
        $result = forSelect($sql_user_id_by_token, 0);
        $uid = $result->uid;
        $sql_users_id = "SELECT * FROM users WHERE id = $uid";

        $type = forSelect($sql_users_id, 0)->type;
        $body = $request->getParsedBody();

    } catch (PDOException $e) {
        return '{"error 1": {"text": ' . $e->getMessage() . '}';
    }
    if ($type == 0) {
        $str = '';
        if (isset($body['title'])) {
            $title = $body['title'];
            $str .= 'title = :title , ';
        }
        if (isset($body['description'])) {
            $description = $body['description'];
            $str .= 'description = :description , ';
        }
        if (isset($body['vote'])) {
            $vote = $body['vote'];
            $str .= 'vote = :vote , ';
        }
        if (!empty($str)) {
            $str = substr($str,0,strlen($str)-3);
            $sql_update = "UPDATE requests SET $str WHERE id = $id_put";
            echo $sql_update;
            try {
                $db = new db();
                $db = $db->connect();
                $upd_if_type_0 = $db->prepare($sql_update);
                if (isset($title)) { $upd_if_type_0->bindParam(':title', $title); }
                if (isset($description)) { $upd_if_type_0->bindParam(':description', $description); }
                if (isset($vote)) { $upd_if_type_0->bindParam(':vote', $vote); }
                if (isset($title)) {
                    $upd_if_type_0->execute();
                    $db = null;

                    $search_id = "SELECT * FROM requests WHERE title = '" . $title . "' AND uid = $uid";
                    return json_encode(forSelect($search_id,0));

                }

                $search_id = "SELECT * FROM requests WHERE id = $id_put";
                return json_encode(forSelect($search_id, 0));
            } catch (PDOException $e) {
                echo '{"error 2": {"text": ' . $e->getMessage() . '}';
            }
        }
    } elseif ($type == 1) {
        $str = '';
        if (isset($body['state'])) {
            $state = $body['state'];
            $str .= 'state =:state';
        }
        if (!empty($str)) {
            $sql_update = "UPDATE requests SET $str WHERE id = $id_put";
            echo $sql_update;
            try {
                $db = new db();
                $db = $db->connect();
                $upd_if_type_1 = $db->prepare($sql_update);
                if (isset($title)) { $upd_if_type_1->bindParam(':title', $title); }
                if (isset($description)) { $upd_if_type_1->bindParam(':description', $description); }
                if (isset($state)) { $upd_if_type_1->bindParam(':state', $state); }
                if (isset($vote)) { $upd_if_type_1->bindParam(':vote', $vote); }
                if (isset($title)) {
                    $upd_if_type_1->execute();
                    $db = null;

                    $search_id = "SELECT * FROM requests WHERE title = '" . $title . "' AND uid = $uid";
                    return json_encode(forSelect($search_id,0));

                }
            } catch (PDOException $e) {
                echo '{"error 3": {"text": ' . $e->getMessage() . '}';
            }
        }

        $search_id = "SELECT * FROM requests WHERE id = $id_put";
        return json_encode(forSelect($search_id, 0));
    }

});
$app->run();
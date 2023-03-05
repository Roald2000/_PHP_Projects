

<?php

header('Access-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: GET,POST,PUT,DELETE');
header('Access-Control-Allow-Headers:*');

class Main
{

    private $HostName = "localhost";
    private $HostUser = "root";
    private $HostPassword = "";
    private $Database = "crud_db";

    public function Sanitze(string $input)
    {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
        return $input;
    }

    public function SendResponse(int $code, string $message)
    {
        //* 200 OK / Fetched, Found
        //* 201 Created, Updated
        //* 204 Deleted / No Content
        //! 400 Bad Request / Invalid Request
        //! 401 Unauthorized : Has no acces to the resource
        //! 403 Forbidden : Is authenticated but has no access to the resource
        //! 404 Not Found : Resource not found
        //! 409 Conflict : Request Couldn't proceed due to resource's current conflicted state
        $accepted_codes = [200, 201, 204, 400, 401, 403, 404, 409, 500];
        $isAccepted = array_search($code, $accepted_codes);
        if (!$isAccepted) {
            header("HTTP/1.1 $code");
            $response = ['status' => $code, 'message' => 'Unregistered Response'];
        } else {
            header("HTTP/1.1 $code");
            $response = ['status' => $code, 'message' => $message];
        }
        return json_encode($response);
    }

    private function setConnection()
    {
        try {
            $dsn = "mysql:host=$this->HostName;dbname=$this->Database";
            $connection = new PDO($dsn, $this->HostUser, $this->HostPassword);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
        } catch (PDOException $error) {
            $message = "ERROR : $error";
            return $this->SendResponse(401, $message);
        }
    }
    public function connect()
    {
        return $this->setConnection();
    }
}

class ViewList extends Main
{
    public function fetch_list()
    {
        $db = parent::connect();
        $query_fetch_list = "SELECT * FROM crud_tbl";
        $_fetch_list = $db->query($query_fetch_list);
        if ($_fetch_list->rowCount() !== 0) {
            return json_encode(['data' => $_fetch_list->fetchAll(PDO::FETCH_ASSOC)]);
        } else {
            return json_encode(['data' => [],'message'=>"Add items to fill the list"]);
        }
    }

    public function fetch_item(array $params)
    {
        $fetchItem = parent::Sanitze($params['fetchItem']);
        if (!empty($fetchItem)) {
            $db = parent::connect();
            $query_fetch_item = "SELECT * FROM crud_tbl WHERE CONCAT(row_id,item_name,item_price,`timestamp`) LIKE ?";
            $_fetch_item = $db->prepare($query_fetch_item);
            $_fetch_item->execute(["%$fetchItem%"]);
            if ($_fetch_item->rowCount() !== 0) {
                return json_encode(['data' => $_fetch_item->fetchAll(PDO::FETCH_ASSOC)]);
            } else {
                return parent::SendResponse(404, "No Result for '$fetchItem'");
            }
        } else {
            return parent::SendResponse(400, "Input Field Cannot be empty!");
        }
    }

    public function item_detail(array $params)
    {
        $item = parent::Sanitze($params['item']);
        if (!empty($item)) {
            $db = parent::connect();
            $query_item_detail = "SELECT * FROM crud_tbl WHERE row_id = ? LIMIT 1";
            $_item_detail = $db->prepare($query_item_detail);
            $_item_detail->execute([$item]);
            if ($_item_detail->rowCount() !== 0) {
                return json_encode(['data' => $_item_detail->fetchAll(PDO::FETCH_ASSOC)]);
            } else {
                return parent::SendResponse(404, "Item With an ID of '$item' does not exist!");
            }
        } else {
            return parent::SendResponse(400, "Input Field Cannot be empty!");
        }
    }
}

class Controller extends Main
{
    public function createItem(array $params)
    {
        $db = parent::connect();
        $item_name = parent::Sanitze($params['item_name']);
        $item_price = parent::Sanitze($params['item_price']);
        if (empty($item_name) || empty($item_price)) {
            return parent::SendResponse(400, "Input Field Cannot be empty!");
        } else {
            $query_create_item = "INSERT INTO crud_tbl(item_name,item_price) VALUES(?,?)";
            $_create_item = $db->prepare($query_create_item);
            $payload = [$item_name, $item_price];
            $_create_item->execute($payload);
            return parent::SendResponse(201, "Item '$item_name' created!");
        }
    }

    public function isItemValid(int $row_id)
    {
        $db = parent::connect();
        $row_id = parent::Sanitze($row_id);
        if (empty($row_id)) {
            return parent::SendResponse(400, "Invalid ID");
        } else {
            $query_item = "SELECT row_id FROM crud_tbl WHERE row_id = ? LIMIT 1";
            $_item = $db->prepare($query_item);
            $_item->execute([$row_id]);
            if ($_item->rowCount() !== 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function updateItem(int $row_id, array $params)
    {
        if ($this->isItemValid($row_id)) {
            $db = parent::connect();
            $item_name = parent::Sanitze($params['item_name']);
            $item_price = parent::Sanitze($params['item_price']);
            $payload = [$item_name, $item_price, $row_id];
            $query_update_item = "UPDATE crud_tbl SET item_name = ?, item_price = ? WHERE row_id = ? LIMIT 1";
            $_update_item = $db->prepare($query_update_item);
            $_update_item->execute($payload);
            return parent::SendResponse(201, "Item updated!");
        } else {
            return parent::SendResponse(404, "Item does not exist, cannot be updated!");
        }
    }
    public function deleteItem(int $row_id)
    {
        if ($this->isItemValid($row_id)) {
            $db = parent::connect();
            $query_delete_item = "DELETE FROM crud_tbl WHERE row_id = ? LIMIT 1";
            $_delete_item = $db->prepare($query_delete_item);
            $_delete_item->execute([$row_id]);
            return parent::SendResponse(204, "Item Deleted!");
        } else {
            return parent::SendResponse(404, "Item does not exist, cannot be deleted!");
        }
    }
}

$methods = $_SERVER["REQUEST_METHOD"];
$Message = new Main;
$View = new ViewList;
$Control = new Controller;

switch ($methods) {
    case 'GET':
        if (isset($_GET['fetch_list'])) {
            echo $View->fetch_list();
        } else if (isset($_GET['fetch_item'])) {
            echo $View->fetch_item($_GET);
        } else if (isset($_GET['item_detail'])) {
            echo $View->item_detail($_GET);
        } else {
            echo $Message->SendResponse(403, 'Invalid Request');
        }
        break;
    case 'POST':
        if (isset($_GET['create_item'])) {
            $request_body = json_decode(file_get_contents('php://input'), true);
            echo $Control->createItem($request_body);
        } else {
            echo $Message->SendResponse(403, 'Invalid Request');
        }
        break;
    case 'PUT':
        if (isset($_GET['upate_item'])) {
            $request_body = json_decode(file_get_contents('php://input'), true);
            echo $Control->updateItem($_GET['row_id'], $request_body);
        } else {
            echo $Message->SendResponse(403, 'Invalid Request');
        }
        break;
    case 'DELETE':
        if (isset($_GET['delete_item'])) {
            echo $Control->deleteItem($_GET['row_id']);
        } else {
            echo $Message->SendResponse(403, 'Invalid Request');
        }
        break;
    default:
        $Message = new Main;
        echo $Message->SendResponse(403, 'Request Method for accessing resource not allowed!');
        break;
}

?>
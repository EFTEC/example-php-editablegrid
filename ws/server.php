<?php /** @noinspection ForgottenDebugOutputInspection */

use eftec\PdoOne;

header('Content-Type: application/json');

include '../vendor/autoload.php';
include '../repo/CountryRepo.php';
include '../repo/PlayersRepo.php';

$pdoOne = new PdoOne('mysql', '127.0.0.1', 'root', 'abc.123', 'example_editable_grid');
$pdoOne->logLevel = 3;
try {
    $pdoOne->connect();
} catch (Exception $e) {
    var_dump($e->getMessage());
    die(1);
}

$action = @$_GET['action'];

//$countries=CountryRepo::toList();
//var_dump($countries);

switch ($action) {
    case 'add':
        $record = PlayersRepo::factoryNull(); // we create an empty array

        try {
            PlayersRepo::insert($record);
            $result = ['result' => true];
        } catch (Exception $e) {
            $result = ['result' => false, 'message' => $e->getMessage()];
        }
        echo json_encode($result);
        break;
    case 'save':
        $record = $_POST['record'];
        unset($record['CountryName']); // we delete the field countryname
        $record['IsActive'] = ($record['IsActive']) ? 1 : 0; // we convert isactive(boolean) to 1 or 0.
        try {
            PlayersRepo::update($record);
            $result = ['result' => true];
        } catch (Exception $e) {
            $result = ['result' => false, 'message' => $e->getMessage()];
        }

        echo json_encode($result);
        break;
    case 'delete':
        $id = $_POST['id'];
        try {
            PlayersRepo::deleteById($id);
            $result = ['result' => true];
        } catch (Exception $e) {
            $result = ['result' => false, 'message' => $e->getMessage()];
        }

        echo json_encode($result);
        break;
    case 'get':
        $r = [];
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 5;
        $rowInit = ($limit * ($page - 1));
        try {
            $result['records'] =
                $pdoOne->select('ID,Players.Name,Country.Name as CountryName,IsActive')
                       ->limit("$rowInit,$limit")
                       ->from('Players')
                       ->left('Country on Players.IdCounty=Country.IdCounty')
                       ->toList();
            $result['total'] = count($result['records']);
        } catch (Exception $e) {
            $result = ['result' => false, 'message' => $e->getMessage()];
        }

        echo json_encode($result);
        break;
    case 'countries':
        try {
            $result = CountryRepo::toList();
        } catch (Exception $e) {
            $result = ['result' => false, 'message' => $e->getMessage()];
        }
        echo json_encode($result);
        break;
    default:
        echo json_encode(['result' => false, 'message' => '???? ' . $action]);
}

<?php
include "C:/xampp/htdocs/TCC_SITE/conexao.php";
session_start();

// Função para normalizar o endereço
function normalizeAddress($logradouro, $numero, $bairro, $cidade, $estado, $pais) {
    return sprintf(
        '%s, %s, %s, %s, %s, %s',
        ucwords(strtolower(trim($logradouro))),
        $numero,
        ucwords(strtolower(trim($bairro))),
        ucwords(strtolower(trim($cidade))),
        strtoupper(trim($estado)),
        ucwords(strtolower(trim($pais)))
    );
}

// Função para converter endereço em latitude e longitude usando OpenCage API
function getCoordinatesFromAddress($logradouro, $numero, $bairro, $cidade, $estado, $pais) {
    $apiKey = 'b8105969c5b84c71887a58e3aedae65b';
    $endereco = urlencode(normalizeAddress($logradouro, $numero, $bairro, $cidade, $estado, $pais));
    $apiUrl = "https://api.opencagedata.com/geocode/v1/json?q={$endereco}&key={$apiKey}";

    $response = file_get_contents($apiUrl);
    if ($response === FALSE) {
        return null;
    }

    $data = json_decode($response, true);
    if (isset($data['results'][0])) {
        $location = $data['results'][0]['geometry'];
        $message = isset($data['results'][0]['components']['country_code']) && $data['results'][0]['components']['country_code'] !== 'BR'
            ? 'Localização fora do Brasil. Resultados podem não ser precisos.'
            : null;
        return [
            'latitude' => $location['lat'],
            'longitude' => $location['lng'],
            'message' => $message
        ];
    }
    return null;
}

// Função para atualizar a tabela com as coordenadas
function updateCoordinates($conn, $id, $latitude, $longitude, $type) {
    try {
        if ($type === 'faculdade') {
            $sql = "UPDATE faculdade SET latitude = ?, longitude = ? WHERE idfaculdade = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ddi', $latitude, $longitude, $id);
        } elseif ($type === 'usuario') {
            $sql = "UPDATE usuario SET latitude = ?, longitude = ? WHERE idusuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ddi', $latitude, $longitude, $id);
        } else {
            throw new Exception('Tipo inválido');
        }
        if (!$stmt->execute()) {
            throw new Exception('Erro ao executar a atualização: ' . $stmt->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log($e->getMessage()); // Log error message
        return false;
    }
    return true;
}

// Verificar se o usuário está logado
$response = [];

if (isset($_SESSION['idusuario'])) {
    // Buscar endereço do usuário
    $stmt = $conn->prepare("SELECT u.idusuario, u.logradouro, u.numero, u.bairro, c.nome AS cidade, e.nome AS estado, u.latitude, u.longitude 
                            FROM usuario u 
                            JOIN cidade c ON u.idcidade = c.idcidade 
                            JOIN estado e ON u.idestado = e.idestado 
                            WHERE u.idusuario = ?");
    $stmt->bind_param("i", $_SESSION['idusuario']);
    $stmt->execute();
    $stmt->bind_result($idUsuario, $logradouro, $numero, $bairro, $nomeCidade, $nomeEstado, $latitude, $longitude);
    $stmt->fetch();
    $stmt->close();

    if ($logradouro && $numero && $bairro && $nomeCidade && $nomeEstado) {
        if (!$latitude || !$longitude) {
            $coordinates = getCoordinatesFromAddress($logradouro, $numero, $bairro, $nomeCidade, $nomeEstado, 'Brasil');
            if ($coordinates) {
                $latitude = $coordinates['latitude'];
                $longitude = $coordinates['longitude'];
                updateCoordinates($conn, $idUsuario, $latitude, $longitude, 'usuario');
                $response['user'] = [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'logradouro' => $logradouro,
                    'numero' => $numero,
                    'bairro' => $bairro
                ];
                if ($coordinates['message']) {
                    $response['message'] = $coordinates['message'];
                }
            } else {
                $response['error'] = 'Não foi possível obter as coordenadas do usuário.';
            }
        } else {
            $response['user'] = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'logradouro' => $logradouro,
                'numero' => $numero,
                'bairro' => $bairro
            ];
        }
    } else {
        $response['error'] = 'Endereço do usuário não encontrado.';
    }
} else {
    $response['error'] = 'Usuário não está logado.';
}

// Buscar todas as faculdades no banco de dados
$sqlFaculdades = "SELECT f.idfaculdade, f.logradouro, f.numero, f.bairro, c.nome AS cidade, f.nome, f.latitude, f.longitude 
                  FROM faculdade f 
                  JOIN cidade c ON f.idcidade = c.idcidade";
$resultFaculdades = $conn->query($sqlFaculdades);

$faculdades = [];

if ($resultFaculdades->num_rows > 0) {
    while ($row = $resultFaculdades->fetch_assoc()) {
        $idFaculdade = $row['idfaculdade'];
        $latitude = $row['latitude'];
        $longitude = $row['longitude'];
        if (!$latitude || !$longitude) {
            $coordinatesFaculdade = getCoordinatesFromAddress(
                $row['logradouro'],
                $row['numero'],
                $row['bairro'],
                $row['cidade'],
                'São Paulo', // Estado padrão SP
                'Brasil'
            );
            if ($coordinatesFaculdade) {
                $latitude = $coordinatesFaculdade['latitude'];
                $longitude = $coordinatesFaculdade['longitude'];
                updateCoordinates($conn, $idFaculdade, $latitude, $longitude, 'faculdade');
            } else {
                $latitude = null;
                $longitude = null;
            }
        }
        if ($latitude !== null && $longitude !== null) {
            $faculdades[] = [
                'idfaculdade' => $idFaculdade, // Adicionando o ID da faculdade
                'latitude' => $latitude,
                'longitude' => $longitude,
                'nome' => $row['nome'],
                'logradouro' => $row['logradouro'],
                'numero' => $row['numero'],
                'bairro' => $row['bairro']
            ];
        }
    }
    $response['faculdades'] = $faculdades;
} else {
    $response['error'] = 'Nenhuma faculdade encontrada.';
}

// Retornar a resposta em JSON
header('Content-Type: application/json');
echo json_encode($response);
?>


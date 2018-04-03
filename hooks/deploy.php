<?php

$body = (string) file_get_contents("php://input");
$payload = json_decode($body, true);

if (!isset($payload['ref'])) {
    header("HTTP/1.1 400 Bad Request");
    exit(0);
}

if ($payload['ref'] === 'refs/heads/master') {
    file_put_contents('/data/doctrine/deploy', time());
} else {
    file_put_contents('/data/doctrine/deploy-staging', $payload['after']);
}

echo json_encode(['success' => true]);

<?php
function jsonResponse(int $httpCode, string $status, string $message, $data = null): void
{
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function readJsonBody(): array
{
    $raw = file_get_contents('php://input');
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        jsonResponse(400, 'error', 'Body harus berupa JSON object yang valid.', null);
    }
    return $decoded;
}

function requireFields(array $data, array $fields): void
{
    foreach ($fields as $field) {
        if (!array_key_exists($field, $data) || (is_string($data[$field]) && trim($data[$field]) === '')) {
            jsonResponse(400, 'error', "Field wajib: {$field}.", null);
        }
    }
}

function validPositiveInt($value): bool
{
    return filter_var($value, FILTER_VALIDATE_INT) !== false && (int)$value > 0;
}

function handleApiError(Throwable $e): void
{
    error_log('[UTS Perpustakaan API] ' . $e->getMessage());
    jsonResponse(500, 'error', 'Terjadi kesalahan pada server.', null);
}

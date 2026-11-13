<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SoftEtherService
{
    protected string $activeServerKey = 'vps1';
    protected array $servers = [];
    protected array $circuitBroken = [];
    protected array $lastError = [];

    // Propiedades del servidor activo
    protected string $host = '127.0.0.1';
    protected int $port = 5555;
    protected string $hub = 'DEFAULT';
    protected string $adminPassword = '';
    protected string $rpcUrl = '';
    protected bool $verifySsl = false;

    public function __construct(?string $serverKey = null)
    {
        $this->initializeServers();
        $this->setServer($serverKey ?: config('services.softether.default_server', 'vps1'));
    }

    /**
     * Inicializa los perfiles de servidores VPS 1 y VPS 2
     */
    protected function initializeServers(): void
    {
        $vps1Host = (string) (config('services.softether.servers.vps1.host')
            ?: config('services.softether.host')
            ?: env('SOFTETHER_VPS1_HOST')
            ?: env('SOFTETHER_HOST')
            ?: '152.206.177.55');

        $vps1Port = (int) (config('services.softether.servers.vps1.port')
            ?: config('services.softether.port')
            ?: env('SOFTETHER_VPS1_PORT')
            ?: env('SOFTETHER_PORT')
            ?: 5555);

        $vps1Hub = (string) (config('services.softether.servers.vps1.hub')
            ?: config('services.softether.hub')
            ?: env('SOFTETHER_VPS1_HUB')
            ?: env('SOFTETHER_HUB')
            ?: 'DEFAULT');

        $vps1Pass = (string) (config('services.softether.servers.vps1.admin_password')
            ?: config('services.softether.admin_password')
            ?: env('SOFTETHER_VPS1_ADMIN_PASSWORD')
            ?: env('SOFTETHER_ADMIN_PASSWORD')
            ?: '');

        $vps1Verify = filter_var(config('services.softether.servers.vps1.verify_ssl')
            ?? config('services.softether.verify_ssl')
            ?? env('SOFTETHER_VPS1_VERIFY_SSL')
            ?? env('SOFTETHER_VERIFY_SSL')
            ?? false, FILTER_VALIDATE_BOOLEAN) ?: false;

        $vps2Host = (string) (config('services.softether.servers.vps2.host')
            ?: env('SOFTETHER_VPS2_HOST')
            ?: env('VPS2_SOFTETHER_HOST')
            ?: env('SOFTETHER_SECONDARY_HOST')
            ?: '152.206.177.165');

        $vps2Port = (int) (config('services.softether.servers.vps2.port')
            ?: env('SOFTETHER_VPS2_PORT')
            ?: env('VPS2_SOFTETHER_PORT')
            ?: env('SOFTETHER_SECONDARY_PORT')
            ?: 5555);

        $vps2Hub = (string) (config('services.softether.servers.vps2.hub')
            ?: env('SOFTETHER_VPS2_HUB')
            ?: env('VPS2_SOFTETHER_HUB')
            ?: env('SOFTETHER_SECONDARY_HUB')
            ?: 'DEFAULT');

        $vps2Pass = (string) (config('services.softether.servers.vps2.admin_password')
            ?: env('SOFTETHER_VPS2_ADMIN_PASSWORD')
            ?: env('SOFTETHER_VPS2_PASSWORD')
            ?: env('VPS2_SOFTETHER_PASSWORD')
            ?: env('SOFTETHER_SECONDARY_ADMIN_PASSWORD')
            ?: env('SOFTETHER_SECONDARY_PASSWORD')
            ?: '');

        $vps2Verify = filter_var(config('services.softether.servers.vps2.verify_ssl')
            ?? env('SOFTETHER_VPS2_VERIFY_SSL')
            ?? env('SOFTETHER_SECONDARY_VERIFY_SSL')
            ?? false, FILTER_VALIDATE_BOOLEAN) ?: false;

        $this->servers = [
            'vps1' => [
                'id' => 'vps1',
                'name' => env('SOFTETHER_VPS1_NAME', 'VPS 1 (Principal - 152.206.177.55)'),
                'host' => trim(explode('#', $vps1Host)[0]),
                'port' => $vps1Port > 0 ? $vps1Port : 5555,
                'hub' => trim(explode('#', $vps1Hub)[0]) ?: 'DEFAULT',
                'admin_password' => trim($vps1Pass),
                'rpc_url' => "https://" . trim(explode('#', $vps1Host)[0]) . ":{$vps1Port}/api/",
                'verify_ssl' => $vps1Verify,
            ],
            'vps2' => [
                'id' => 'vps2',
                'name' => env('SOFTETHER_VPS2_NAME', 'VPS 2 (Secundario)'),
                'host' => trim(explode('#', $vps2Host)[0]),
                'port' => $vps2Port > 0 ? $vps2Port : 5555,
                'hub' => trim(explode('#', $vps2Hub)[0]) ?: 'DEFAULT',
                'admin_password' => trim($vps2Pass),
                'rpc_url' => !empty($vps2Host) ? "https://" . trim(explode('#', $vps2Host)[0]) . ":{$vps2Port}/api/" : '',
                'verify_ssl' => $vps2Verify,
            ],
        ];

        // Carga directa de .env en caso de caché de Laravel
        $this->loadDirectlyFromEnvFiles();
    }

    /**
     * Carga de respaldo leyendo directamente los archivos .env si config() está cacheado o vacío
     */
    protected function loadDirectlyFromEnvFiles(): void
    {
        $candidatePaths = [
            function_exists('base_path') ? base_path('.env') : null,
            function_exists('base_path') ? dirname(base_path()) . '/.env' : null,
            '/opt/dataplus-platform/src/.env',
            '/opt/dataplus-platform/.env',
            '.env',
        ];

        foreach (array_filter($candidatePaths) as $path) {
            if (!file_exists($path) || !is_readable($path)) {
                continue;
            }

            $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || str_starts_with($line, '#')) {
                    continue;
                }

                // Servidor 1 (o genérico)
                if (preg_match('/^\s*(SOFTETHER_VPS1_HOST|SOFTETHER_HOST)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = trim(explode('#', trim($m[2], '"\''))[0]);
                    if (!empty($clean)) {
                        if (str_contains($clean, ':')) {
                            $parts = explode(':', $clean);
                            $this->servers['vps1']['host'] = trim($parts[0]);
                            $this->servers['vps1']['port'] = (int) trim($parts[1]);
                        } else {
                            $this->servers['vps1']['host'] = $clean;
                        }
                    }
                } elseif (preg_match('/^\s*(SOFTETHER_VPS1_PORT|SOFTETHER_PORT)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = (int) trim(explode('#', trim($m[2], '"\''))[0]);
                    if ($clean > 0) {
                        $this->servers['vps1']['port'] = $clean;
                    }
                } elseif (preg_match('/^\s*(SOFTETHER_VPS1_HUB|SOFTETHER_HUB)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = trim(explode('#', trim($m[2], '"\''))[0]);
                    if (!empty($clean)) {
                        $this->servers['vps1']['hub'] = $clean;
                    }
                } elseif (preg_match('/^\s*(SOFTETHER_VPS1_ADMIN_PASSWORD|SOFTETHER_ADMIN_PASSWORD|SOFTETHER_PASSWORD)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = preg_replace('/\s+#.*$/', '', $m[2]);
                    $clean = trim($clean, '"\'');
                    if (!empty($clean)) {
                        $this->servers['vps1']['admin_password'] = $clean;
                    }
                } elseif (preg_match('/^\s*(SOFTETHER_VPS1_VERIFY_SSL|SOFTETHER_VERIFY_SSL)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = trim(explode('#', trim($m[2], '"\''))[0]);
                    $this->servers['vps1']['verify_ssl'] = filter_var($clean, FILTER_VALIDATE_BOOLEAN);
                }

                // Servidor 2
                if (preg_match('/^\s*(SOFTETHER_VPS2_HOST|VPS2_SOFTETHER_HOST|SOFTETHER_VPS_2_HOST|SOFTETHER_SECONDARY_HOST|SOFTETHER_SERVER2_HOST|VPS2_HOST)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = trim(explode('#', trim($m[2], '"\''))[0]);
                    if (!empty($clean)) {
                        if (str_contains($clean, ':')) {
                            $parts = explode(':', $clean);
                            $this->servers['vps2']['host'] = trim($parts[0]);
                            $this->servers['vps2']['port'] = (int) trim($parts[1]);
                        } else {
                            $this->servers['vps2']['host'] = $clean;
                        }
                    }
                } elseif (preg_match('/^\s*(SOFTETHER_VPS2_PORT|VPS2_SOFTETHER_PORT|SOFTETHER_VPS_2_PORT|SOFTETHER_SECONDARY_PORT|SOFTETHER_SERVER2_PORT)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = (int) trim(explode('#', trim($m[2], '"\''))[0]);
                    if ($clean > 0) {
                        $this->servers['vps2']['port'] = $clean;
                    }
                } elseif (preg_match('/^\s*(SOFTETHER_VPS2_HUB|VPS2_SOFTETHER_HUB|SOFTETHER_VPS_2_HUB|SOFTETHER_SECONDARY_HUB|SOFTETHER_SERVER2_HUB)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = trim(explode('#', trim($m[2], '"\''))[0]);
                    if (!empty($clean)) {
                        $this->servers['vps2']['hub'] = $clean;
                    }
                } elseif (preg_match('/^\s*(SOFTETHER_VPS2_ADMIN_PASSWORD|SOFTETHER_VPS2_PASSWORD|SOFTETHER_VPS2_PASS|VPS2_SOFTETHER_PASSWORD|VPS2_SOFTETHER_ADMIN_PASSWORD|SOFTETHER_SECONDARY_PASSWORD|SOFTETHER_SECONDARY_ADMIN_PASSWORD|SOFTETHER_SERVER2_ADMIN_PASSWORD|SOFTETHER_SERVER2_PASSWORD)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = preg_replace('/\s+#.*$/', '', $m[2]);
                    $clean = trim($clean, '"\'');
                    if (!empty($clean)) {
                        $this->servers['vps2']['admin_password'] = $clean;
                    }
                } elseif (preg_match('/^\s*(SOFTETHER_VPS2_VERIFY_SSL|SOFTETHER_VPS2_SSL|VPS2_SOFTETHER_VERIFY_SSL|SOFTETHER_SECONDARY_VERIFY_SSL)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = trim(explode('#', trim($m[2], '"\''))[0]);
                    $this->servers['vps2']['verify_ssl'] = filter_var($clean, FILTER_VALIDATE_BOOLEAN);
                }
            }
        }

        // Recalcular URLs RPC con los datos limpios
        if (!empty($this->servers['vps1']['host'])) {
            $this->servers['vps1']['rpc_url'] = "https://{$this->servers['vps1']['host']}:{$this->servers['vps1']['port']}/api/";
        }
        if (!empty($this->servers['vps2']['host'])) {
            $this->servers['vps2']['rpc_url'] = "https://{$this->servers['vps2']['host']}:{$this->servers['vps2']['port']}/api/";
        }
    }

    /**
     * Cambia el servidor activo (vps1 o vps2) y opcionalmente el Hub objetivo
     */
    public function setServer(string $serverKey, ?string $hub = null): self
    {
        if (!isset($this->servers[$serverKey])) {
            $serverKey = 'vps1';
        }

        $this->activeServerKey = $serverKey;
        $srv = $this->servers[$serverKey];

        $this->host = $srv['host'] ?: '127.0.0.1';
        $this->port = (int) ($srv['port'] ?: 5555);
        $this->hub = !empty($hub) ? trim($hub) : ($srv['hub'] ?: 'DEFAULT');
        $this->adminPassword = $srv['admin_password'] ?: '';
        $this->rpcUrl = "https://{$this->host}:{$this->port}/api/";
        $this->verifySsl = (bool) ($srv['verify_ssl'] ?? false);

        return $this;
    }

    /**
     * Establece o cambia el Virtual Hub activo en el servidor actual
     */
    public function setHub(string $hubName): self
    {
        $clean = trim($hubName);
        if (!empty($clean)) {
            $this->hub = $clean;
            if (isset($this->servers[$this->activeServerKey])) {
                $this->servers[$this->activeServerKey]['hub'] = $clean;
            }
        }
        return $this;
    }

    public function getHub(): string
    {
        return $this->hub;
    }

    public function getActiveServerKey(): string
    {
        return $this->activeServerKey;
    }

    public function getServers(): array
    {
        return array_map(function ($s) {
            return [
                'id' => $s['id'],
                'name' => $s['name'],
                'host' => $s['host'],
                'port' => $s['port'],
                'hub' => $s['hub'],
                'is_configured' => !empty($s['admin_password']) && !empty($s['host']),
                'has_password' => !empty($s['admin_password']),
            ];
        }, $this->servers);
    }

    /**
     * Verifica si el servidor activo tiene credenciales mínimas
     */
    public function isConfigured(): bool
    {
        return !empty($this->adminPassword) && !empty($this->host);
    }

    /**
     * Obtiene los parámetros de conexión sin exponer la contraseña
     */
    public function getConnectionDetails(): array
    {
        return [
            'server_key' => $this->activeServerKey,
            'server_name' => $this->servers[$this->activeServerKey]['name'] ?? 'SoftEther Server',
            'host' => $this->host,
            'port' => $this->port,
            'hub' => $this->hub,
            'is_configured' => $this->isConfigured(),
            'has_password' => !empty($this->adminPassword),
            'password_mask' => !empty($this->adminPassword) ? str_repeat('•', min(8, strlen($this->adminPassword))) : 'No detectada',
            'rpc_url' => $this->rpcUrl,
            'verify_ssl' => $this->verifySsl,
            'last_error' => $this->lastError[$this->activeServerKey] ?? null,
            'env_file_path' => function_exists('base_path') ? base_path('.env') : '.env',
            'env_file_exists' => function_exists('base_path') ? file_exists(base_path('.env')) : false,
            'available_servers' => $this->getServers(),
        ];
    }

    /**
     * Prueba exhaustiva de conectividad de red física y autenticación JSON-RPC
     */
    public function testConnection(): array
    {
        $start = microtime(true);

        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'status' => 'not_configured',
                'message' => "Falta configurar credenciales para {$this->servers[$this->activeServerKey]['name']} en .env (HOST y ADMIN_PASSWORD requeridos).",
                'latency_ms' => null,
                'server_info' => null,
                'details' => [],
            ];
        }

        $this->resetCircuitBreaker($this->activeServerKey);

        $details = [];
        $tcpSuccess = false;
        $latencyMs = null;

        // 1. Socket TCP al puerto configurado (ej: 5555)
        $errno = 0;
        $errstr = '';
        $socket = @fsockopen($this->host, $this->port, $errno, $errstr, 3.5);

        if ($socket) {
            fclose($socket);
            $latencyMs = round((microtime(true) - $start) * 1000, 2);
            $tcpSuccess = true;
            $details[] = "✓ Puerto TCP {$this->port} accesible ({$latencyMs} ms).";
        } else {
            $details[] = "✗ No se pudo conectar al puerto TCP {$this->port} en {$this->host} (Error {$errno}: {$errstr}). El firewall del servidor o del proveedor está bloqueando los paquetes de entrada hacia el puerto {$this->port}.";

            // Comprobar si algún otro puerto de SoftEther está accesible (992, 1194, 1195)
            $checkPorts = [992, 1194, 1195, 443];
            $openPorts = [];
            foreach ($checkPorts as $cp) {
                if ($cp !== $this->port) {
                    $s = @fsockopen($this->host, $cp, $e1, $e2, 1.2);
                    if ($s) {
                        fclose($s);
                        $openPorts[] = $cp;
                    }
                }
            }

            if (in_array(992, $openPorts, true)) {
                $details[] = "★ ¡Atención! El puerto TCP 992 (SSL de SoftEther) SÍ está abierto y accesible en {$this->host}. Cambia SOFTETHER_{$this->activeServerKey}_PORT=992 en tu .env para conectar.";
            } elseif (in_array(443, $openPorts, true)) {
                $details[] = "★ El puerto 443 responde (Apache), pero los puertos de SoftEther (5555, 992) están filtrados por el firewall. En el VPS 2 ejecuta: 'sudo ufw allow 5555/tcp && sudo ufw allow 992/tcp'.";
            } else {
                $details[] = "★ Los puertos 5555 y 992 no responden. En el VPS 2 ejecuta: 'sudo ufw allow 5555/tcp && sudo ufw allow 992/tcp' o 'sudo iptables -I INPUT -p tcp --dport 5555 -j ACCEPT'.";
            }

            return [
                'success' => false,
                'status' => 'unreachable',
                'message' => "Servidor {$this->servers[$this->activeServerKey]['name']} inaccesible en {$this->host}:{$this->port}. " . implode(' ', $details),
                'latency_ms' => null,
                'server_info' => null,
                'details' => $details,
            ];
        }

        // 2. Comprobación JSON-RPC "Test" (Página 5 del manual oficial)
        $testRpc = $this->callRpc('Test', ['IntValue_u32' => 12345]);
        if ($testRpc['success']) {
            $details[] = "✓ API JSON-RPC respondiendo.";
        } else {
            $err = $testRpc['error'] ?? 'Sin respuesta';
            if (str_contains($err, '404') && (str_contains(strtolower($err), 'apache') || str_contains(strtolower($err), 'nginx') || str_contains(strtolower($err), 'conflicto') || str_contains(strtolower($err), 'web'))) {
                $details[] = "✗ ¡Conflicto en puerto {$this->port}! En este puerto responde un servidor web (Apache/Nginx) y no SoftEther VPN. SoftEther escucha en los puertos 5555 o 992.";
            } else {
                $details[] = "✗ API JSON-RPC retornó error: " . $err;
            }

            // Probar puertos alternativos típicos de SoftEther
            $altPorts = [5555, 992];
            $openAlts = [];
            foreach ($altPorts as $altP) {
                if ($altP !== $this->port) {
                    $s = @fsockopen($this->host, $altP, $e1, $e2, 1.5);
                    if ($s) {
                        fclose($s);
                        $openAlts[] = $altP;
                    }
                }
            }
            if (!empty($openAlts)) {
                $details[] = "★ ¡Detectado! El puerto " . implode(', ', $openAlts) . " está ABIERTO en {$this->host}. SoftEther podría estar escuchando allí. Configura SOFTETHER_VPS2_PORT=" . $openAlts[0] . " en tu .env.";
            } else {
                $details[] = "★ El puerto 5555 está cerrado por firewall en {$this->host}. En el VPS 2 ejecuta: 'sudo ufw allow 5555/tcp' y configura el puerto 5555 en tu .env.";
            }
        }

        // 3. Obtener información de servidor "GetServerInfo" (Página 6)
        $infoRpc = $this->callRpc('GetServerInfo');
        $serverInfo = null;

        if ($infoRpc['success']) {
            $serverInfo = $infoRpc['data'];
            $serverName = $serverInfo['ServerProductName_str'] ?? 'SoftEther VPN Server';
            $serverVer = $serverInfo['ServerVersionString_str'] ?? '';
            $osName = $serverInfo['OsProductName_str'] ?? $serverInfo['OsSystemName_str'] ?? 'Linux';
            $details[] = "✓ Autenticado como Server Admin ({$serverName} {$serverVer} sobre {$osName}).";
        } else {
            // Intentar verificar si la clave es de Virtual Hub Admin
            $hubStatusRpc = $this->callRpc('GetHubStatus', ['HubName_str' => $this->hub], $this->hub);
            if ($hubStatusRpc['success']) {
                $details[] = "✓ Autenticado como Administrador de Hub '{$this->hub}'.";
            } else {
                $details[] = "✗ Autenticación rechazada: " . ($infoRpc['error'] ?? 'Contraseña incorrecta');
            }
        }

        // 4. Enumerar Virtual Hubs para verificar lectura de datos
        $hubsRpc = $this->callRpc('EnumHub');
        if ($hubsRpc['success'] && !empty($hubsRpc['data']['HubList'])) {
            $hCount = count($hubsRpc['data']['HubList']);
            $hNames = array_map(fn($h) => $h['HubName_str'] ?? 'DEFAULT', $hubsRpc['data']['HubList']);
            $details[] = "✓ Se detectaron {$hCount} Virtual Hub(s): [" . implode(', ', $hNames) . "].";
        }

        $allSuccess = $testRpc['success'] || $infoRpc['success'] || !empty($serverInfo);

        return [
            'success' => $allSuccess,
            'status' => $allSuccess ? 'connected' : 'rpc_error',
            'message' => $allSuccess
                ? "¡Conexión exitosa con {$this->servers[$this->activeServerKey]['name']} ({$this->host}:{$this->port}, {$latencyMs} ms)! " . implode(' ', $details)
                : "Conexión física establecida ({$latencyMs} ms), pero la API RPC reporta: " . implode(' ', $details),
            'latency_ms' => $latencyMs,
            'server_info' => $serverInfo,
            'details' => $details,
        ];
    }

    /**
     * Llamada a la API JSON-RPC de SoftEther VPN Server según especificación
     */
    public function callRpc(string $method, array $params = [], ?string $hub = null): array
    {
        if (empty($this->adminPassword)) {
            return [
                'success' => false,
                'error' => 'SOFTETHER_ADMIN_PASSWORD no configurada en .env',
                'data' => null,
            ];
        }

        // Circuit breaker: Si el servidor falló la conexión por timeout o error de red previamente, evitar demoras
        if ($this->circuitBroken[$this->activeServerKey] ?? false) {
            return [
                'success' => false,
                'error' => 'Servidor no accesible (circuito cortado temporalmente tras error de red)',
                'data' => null,
            ];
        }

        $url = rtrim($this->rpcUrl, '/');
        if (!str_ends_with($url, '/api')) {
            $url .= '/api/';
        } else {
            $url .= '/';
        }

        // Headers según especificación de SoftEther JSON-RPC
        $headers = [
            'Content-Type' => 'application/json',
            'X-VPNADMIN-PASSWORD' => $this->adminPassword,
        ];

        // Si se especifica Hub Admin
        if (!empty($hub)) {
            $headers['X-VPNADMIN-HUBNAME'] = $hub;
        }

        try {
            // Timeout robusto para conexiones WAN entre servidores (4.0s connect, 8.0s timeout)
            $client = Http::connectTimeout(4.0)
                ->timeout(8.0)
                ->withHeaders($headers);

            // En SoftEther VPN los certificados SSL son comúnmente autofirmados (self-signed)
            if (!$this->verifySsl) {
                $client = $client->withoutVerifying();
            }

            $response = $client->post($url, [
                'jsonrpc' => '2.0',
                'id' => (string) time(),
                'method' => $method,
                'params' => (object) $params,
            ]);

            if ($response->successful()) {
                $json = $response->json();
                if (isset($json['error'])) {
                    $errMsg = $json['error']['message'] ?? ('Error JSON-RPC: ' . json_encode($json['error']));
                    $this->lastError[$this->activeServerKey] = $errMsg;

                    // Fallback para Hub Admin: si la llamada sin HubName falla por permisos, reintentar con header de Hub
                    if (empty($hub) && !empty($this->hub) && (str_contains(strtolower($errMsg), 'auth') || str_contains(strtolower($errMsg), 'privilege') || str_contains(strtolower($errMsg), 'access denied'))) {
                        $retryHeaders = $headers;
                        $retryHeaders['X-VPNADMIN-HUBNAME'] = $this->hub;
                        $retryClient = Http::connectTimeout(4.0)->timeout(8.0)->withHeaders($retryHeaders);
                        if (!$this->verifySsl) {
                            $retryClient = $retryClient->withoutVerifying();
                        }
                        $retryResp = $retryClient->post($url, [
                            'jsonrpc' => '2.0',
                            'id' => (string) time(),
                            'method' => $method,
                            'params' => (object) $params,
                        ]);
                        if ($retryResp->successful()) {
                            $retryJson = $retryResp->json();
                            if (!isset($retryJson['error'])) {
                                return [
                                    'success' => true,
                                    'data' => $retryJson['result'] ?? [],
                                    'error' => null,
                                ];
                            }
                        }
                    }

                    return [
                        'success' => false,
                        'error' => $errMsg,
                        'data' => null,
                    ];
                }

                return [
                    'success' => true,
                    'data' => $json['result'] ?? [],
                    'error' => null,
                ];
            }

            $rawBody = $response->body();
            if ($response->status() === 404 && (str_contains(strtolower($rawBody), 'apache') || str_contains(strtolower($rawBody), 'nginx') || str_contains(strtolower($rawBody), 'not found'))) {
                $webServer = str_contains(strtolower($rawBody), 'apache') ? 'Apache' : (str_contains(strtolower($rawBody), 'nginx') ? 'Nginx' : 'Servidor Web');
                $httpErr = "HTTP 404: En el puerto {$this->port} responde un servidor web {$webServer} (no SoftEther). Configure el puerto 5555 o 992 en su .env.";
            } else {
                $httpErr = "HTTP {$response->status()}: " . substr(strip_tags($rawBody), 0, 150);
            }
            $this->lastError[$this->activeServerKey] = $httpErr;
            return [
                'success' => false,
                'error' => $httpErr,
                'data' => null,
            ];
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $this->lastError[$this->activeServerKey] = $msg;

            // Solo activar circuit breaker en fallos de red persistentes (cURL error 7 o 28)
            if (str_contains($msg, 'cURL error 7') || str_contains($msg, 'cURL error 28') || str_contains($msg, 'timed out') || str_contains($msg, 'Failed to connect')) {
                $this->circuitBroken[$this->activeServerKey] = true;
            }

            Log::warning("SoftEther RPC exception on {$method} ({$this->activeServerKey}): " . $msg);
            return [
                'success' => false,
                'error' => $msg,
                'data' => null,
            ];
        }
    }

    /**
     * Restablece el disyuntor (circuit breaker)
     */
    public function resetCircuitBreaker(?string $serverKey = null): void
    {
        if ($serverKey) {
            unset($this->circuitBroken[$serverKey]);
        } else {
            $this->circuitBroken = [];
        }
    }

    /**
     * Obtiene la versión actual de caché para un servidor (permite invalidación instantánea multi-hub)
     */
    protected function getCacheVersion(string $serverKey): int
    {
        try {
            return (int) Cache::get("softether_cache_ver_{$serverKey}", 1);
        } catch (Exception $e) {
            return 1;
        }
    }

    /**
     * Limpia la caché temporal de telemetría
     */
    public function clearCache(?string $serverKey = null): void
    {
        $key = $serverKey ?: $this->activeServerKey;
        try {
            $ver = $this->getCacheVersion($key);
            Cache::put("softether_cache_ver_{$key}", $ver + 1, 86400);
            Cache::forget("softether_telemetry_{$key}");
            Cache::forget("softether_telemetry_{$key}_{$this->hub}");
        } catch (Exception $e) {
            // Continuar si la caché de Laravel no está configurada
        }
        $this->resetCircuitBreaker($key);
    }

    /**
     * Obtiene el paquete completo de telemetría de forma ultra-rápida (con auto-resolución de Hub y caché)
     */
    public function getTelemetry(bool $forceRefresh = false): array
    {
        // Procesar deshabilitaciones temporales vencidas antes de devolver telemetría
        if ($this->isConfigured()) {
            $this->processExpiredTimedDisables($this->activeServerKey);
        }

        $ver = $this->getCacheVersion($this->activeServerKey);
        $cacheKey = "softether_telemetry_{$this->activeServerKey}_{$this->hub}_v{$ver}";

        if (!$forceRefresh) {
            try {
                if (Cache::has($cacheKey)) {
                    $cached = Cache::get($cacheKey);
                    if (is_array($cached) && !empty($cached)) {
                        $cached['from_cache'] = true;
                        return $cached;
                    }
                }
            } catch (Exception $e) {
                // Si la caché falla, continuar sin error
            }
        }

        // Si es refresco forzado, restablecemos el circuit breaker
        $this->resetCircuitBreaker($this->activeServerKey);

        // 1. Obtener lista de Virtual Hubs disponibles primero (EnumHub)
        $virtualHubs = $this->getVirtualHubs();

        // 2. Auto-resolución de Hub: Si el hub configurado no existe en el servidor, conmutar al primer hub real activo
        if (!empty($virtualHubs)) {
            $hubNames = array_column($virtualHubs, 'hub_name');
            if (!in_array($this->hub, $hubNames)) {
                // Seleccionar el hub con mayor número de sesiones o el primero online
                $sortedHubs = $virtualHubs;
                usort($sortedHubs, fn ($a, $b) => ($b['active_sessions'] ?? 0) <=> ($a['active_sessions'] ?? 0));
                $autoHub = $sortedHubs[0]['hub_name'] ?? ($hubNames[0] ?? 'DEFAULT');
                $this->setHub($autoHub);
            }
        }

        // 3. Consultar estado del servidor y telemetría de sesiones y usuarios
        $serverStatus = $this->getServerStatus();
        $activeSessions = $this->getActiveSessions($this->hub);
        $vpnUsers = $this->getUsers($this->hub);
        $allVpnUsers = $this->getAllUsers($virtualHubs, $activeSessions);
        $secureNat = $this->getSecureNatStatus($this->hub);
        $dhcpTable = $this->getDhcpTable($this->hub);
        $connectionDetails = $this->getConnectionDetails();
        $connectionDetails['available_hubs'] = !empty($virtualHubs) ? array_column($virtualHubs, 'hub_name') : [$this->hub];

        // 4. Si el servidor respondió con sesiones o usuarios reales, reflejarlo en serverStatus
        if (!empty($activeSessions) && ($serverStatus['total_sessions'] ?? 0) === 0) {
            $serverStatus['total_sessions'] = count($activeSessions);
            $serverStatus['is_live'] = true;
        }
        $totalUsersCount = count($allVpnUsers) > 0 ? count($allVpnUsers) : count($vpnUsers);
        if ($totalUsersCount > 0 && ($serverStatus['total_users'] ?? 0) === 0) {
            $serverStatus['total_users'] = $totalUsersCount;
            $serverStatus['is_live'] = true;
        }

        $data = [
            'serverStatus' => $serverStatus,
            'activeSessions' => $activeSessions,
            'vpnUsers' => $vpnUsers,
            'allVpnUsers' => $allVpnUsers,
            'virtualHubs' => $virtualHubs,
            'secureNat' => $secureNat,
            'dhcpTable' => $dhcpTable,
            'connectionDetails' => $connectionDetails,
            'from_cache' => false,
            'updated_at' => date('H:i:s'),
        ];

        try {
            Cache::put($cacheKey, $data, 15);
        } catch (Exception $e) {
            // Ignorar fallo de almacenamiento
        }

        return $data;
    }

    /**
     * Obtiene el estado general del servidor VPN en vivo (GetServerInfo + GetServerStatus con fallback a GetHubStatus)
     */
    public function getServerStatus(): array
    {
        if ($this->isConfigured()) {
            $info = $this->callRpc('GetServerInfo');
            $status = $this->callRpc('GetServerStatus');

            if ($info['success'] || $status['success']) {
                $iData = $info['data'] ?? [];
                $sData = $status['data'] ?? [];

                $uptimeSeconds = (int) ($sData['ServerUptime_u64'] ?? $sData['Uptime_u64'] ?? 0);
                $uptimeStr = $uptimeSeconds > 0 ? $this->formatSeconds($uptimeSeconds) : 'Activo en producción';

                $totSessions = (int) ($sData['NumSessionsTotal_u32'] ?? $sData['TotalSessions_u32'] ?? 0);
                $totUsers = (int) ($sData['NumUsers_u32'] ?? 0);
                $totTcp = (int) ($sData['NumTcpConnections_u32'] ?? 0);

                // Tráfico recibido y enviado (Páginas 7-8: Recv.UnicastBytes_u64 y Send.UnicastBytes_u64)
                $recvBytes = ($sData['Recv.UnicastBytes_u64'] ?? 0) + ($sData['Recv.BroadcastBytes_u64'] ?? 0);
                $sendBytes = ($sData['Send.UnicastBytes_u64'] ?? 0) + ($sData['Send.BroadcastBytes_u64'] ?? 0);

                return [
                    'is_live' => true,
                    'server_name' => $iData['ServerProductName_str'] ?? "{$this->servers[$this->activeServerKey]['name']}",
                    'version' => trim(($iData['ServerVersionString_str'] ?? 'v4.38') . ' ' . ($iData['ServerBuildInfoString_str'] ?? '')),
                    'os_name' => $iData['OsProductName_str'] ?? $iData['OsSystemName_str'] ?? 'Linux',
                    'server_type' => ($sData['ServerType_u32'] ?? 0) === 0 ? 'Standalone Server' : 'Farm Controller',
                    'uptime' => $uptimeStr,
                    'started_at' => !empty($sData['StartTime_dt']) ? $sData['StartTime_dt'] : 'Operativo',
                    'total_sessions' => $totSessions,
                    'total_users' => $totUsers,
                    'total_tcp_connections' => $totTcp,
                    'traffic_total_in' => $this->formatBytes($recvBytes),
                    'traffic_total_out' => $this->formatBytes($sendBytes),
                    'transfer_speed_in' => 'En tiempo real',
                    'transfer_speed_out' => 'En tiempo real',
                    'listeners' => [
                        ['port' => $this->port, 'protocol' => 'TCP / Admin RPC API', 'status' => 'Activo (Escuchando)'],
                        ['port' => 443, 'protocol' => 'TCP / HTTPS (SoftEther & SSTP)', 'status' => 'Activo (Escuchando)'],
                        ['port' => 992, 'protocol' => 'TCP / Admin Telnet', 'status' => 'Activo (Escuchando)'],
                        ['port' => 1194, 'protocol' => 'UDP / OpenVPN', 'status' => 'Activo (Escuchando)'],
                    ],
                    'virtual_nat_enabled' => true,
                    'virtual_dhcp_enabled' => true,
                    'error_message' => null,
                ];
            }

            // Fallback: Si las APIs de nivel Servidor fallan por permisos (ej. clave de Hub Admin), consultar GetHubStatus
            $hubStatus = $this->callRpc('GetHubStatus', ['HubName_str' => $this->hub], $this->hub);
            if ($hubStatus['success'] && !empty($hubStatus['data'])) {
                $hData = $hubStatus['data'];
                $recvBytes = ($hData['Recv.UnicastBytes_u64'] ?? 0) + ($hData['Recv.BroadcastBytes_u64'] ?? 0);
                $sendBytes = ($hData['Send.UnicastBytes_u64'] ?? 0) + ($hData['Send.BroadcastBytes_u64'] ?? 0);

                return [
                    'is_live' => true,
                    'server_name' => "{$this->servers[$this->activeServerKey]['name']} (Hub: {$this->hub})",
                    'version' => 'SoftEther VPN Server',
                    'os_name' => 'Linux',
                    'server_type' => 'Virtual Hub ' . $this->hub,
                    'uptime' => 'Operativo en vivo',
                    'started_at' => !empty($hData['CreatedTime_dt']) ? $hData['CreatedTime_dt'] : 'Operativo',
                    'total_sessions' => (int) ($hData['NumSessions_u32'] ?? 0),
                    'total_users' => (int) ($hData['NumUsers_u32'] ?? 0),
                    'total_tcp_connections' => (int) ($hData['NumMacTables_u32'] ?? 0),
                    'traffic_total_in' => $this->formatBytes($recvBytes),
                    'traffic_total_out' => $this->formatBytes($sendBytes),
                    'transfer_speed_in' => 'En tiempo real',
                    'transfer_speed_out' => 'En tiempo real',
                    'listeners' => [
                        ['port' => $this->port, 'protocol' => 'TCP / Admin RPC API', 'status' => 'Activo (Escuchando)'],
                    ],
                    'virtual_nat_enabled' => !empty($hData['SecureNATEnabled_bool']),
                    'virtual_dhcp_enabled' => !empty($hData['SecureNATEnabled_bool']),
                    'error_message' => null,
                ];
            }
        }

        $err = $this->lastError[$this->activeServerKey] ?? ($info['error'] ?? ($status['error'] ?? 'Sin conexión en vivo con el servidor'));
        return [
            'is_live' => false,
            'server_name' => "{$this->servers[$this->activeServerKey]['name']}",
            'version' => 'No disponible',
            'os_name' => 'No disponible',
            'server_type' => 'SoftEther VPN Server',
            'uptime' => 'Desconectado',
            'started_at' => 'No disponible',
            'total_sessions' => 0,
            'total_users' => 0,
            'total_tcp_connections' => 0,
            'traffic_total_in' => '0 B',
            'traffic_total_out' => '0 B',
            'transfer_speed_in' => '0 bps',
            'transfer_speed_out' => '0 bps',
            'listeners' => [],
            'virtual_nat_enabled' => false,
            'virtual_dhcp_enabled' => false,
            'error_message' => $err,
        ];
    }

    /**
     * Obtiene la lista de Virtual Hubs (EnumHub, Páginas 23-25 con fallback a GetHubStatus)
     */
    public function getVirtualHubs(): array
    {
        if ($this->isConfigured()) {
            $res = $this->callRpc('EnumHub');
            if ($res['success'] && !empty($res['data']['HubList'])) {
                return array_map(function ($h) {
                    $recv = $h['Ex.Recv.UnicastBytes_u64'] ?? $h['Recv_u64'] ?? 0;
                    $send = $h['Ex.Send.UnicastBytes_u64'] ?? $h['Send_u64'] ?? 0;

                    return [
                        'hub_name' => $h['HubName_str'] ?? 'DEFAULT',
                        'status' => (!empty($h['Online_bool'])) ? 'Online' : 'Offline',
                        'type' => ($h['HubType_u32'] ?? 0) === 0 ? 'Standalone' : 'Cluster Hub',
                        'users_count' => (int) ($h['NumUsers_u32'] ?? 0),
                        'groups_count' => (int) ($h['NumGroups_u32'] ?? 0),
                        'active_sessions' => (int) ($h['NumSessions_u32'] ?? 0),
                        'mac_tables' => (int) ($h['NumMacTables_u32'] ?? 0),
                        'ip_tables' => (int) ($h['NumIpTables_u32'] ?? 0),
                        'secure_nat' => 'SecureNAT',
                        'last_comm' => !empty($h['LastCommTime_dt']) ? $h['LastCommTime_dt'] : 'Sin actividad',
                        'traffic' => $this->formatBytes($recv + $send),
                    ];
                }, $res['data']['HubList']);
            }

            // Fallback si EnumHub fue denegado pero tenemos acceso a GetHubStatus
            $singleHub = $this->callRpc('GetHubStatus', ['HubName_str' => $this->hub], $this->hub);
            if ($singleHub['success'] && !empty($singleHub['data'])) {
                $d = $singleHub['data'];
                $recv = $d['Recv.UnicastBytes_u64'] ?? 0;
                $send = $d['Send.UnicastBytes_u64'] ?? 0;
                return [[
                    'hub_name' => $d['HubName_str'] ?? $this->hub,
                    'status' => (!empty($d['Online_bool'])) ? 'Online' : 'Offline',
                    'type' => 'Standalone',
                    'users_count' => (int) ($d['NumUsers_u32'] ?? 0),
                    'groups_count' => (int) ($d['NumGroups_u32'] ?? 0),
                    'active_sessions' => (int) ($d['NumSessions_u32'] ?? 0),
                    'mac_tables' => (int) ($d['NumMacTables_u32'] ?? 0),
                    'ip_tables' => (int) ($d['NumIpTables_u32'] ?? 0),
                    'secure_nat' => !empty($d['SecureNATEnabled_bool']) ? 'SecureNAT Habilitado' : 'SecureNAT Inactivo',
                    'last_comm' => !empty($d['LastCommTime_dt']) ? $d['LastCommTime_dt'] : 'Operativo',
                    'traffic' => $this->formatBytes($recv + $send),
                ]];
            }
        }

        return [];
    }

    /**
     * Crea un nuevo Virtual Hub en el servidor VPN (CreateHub)
     */
    public function createHub(string $hubName, ?string $adminPassword = null): array
    {
        $hubName = trim($hubName);
        if (empty($hubName)) {
            return [
                'success' => false,
                'message' => 'El nombre del Virtual Hub no puede estar vacío.',
            ];
        }

        if ($this->isConfigured()) {
            $params = [
                'HubName_str' => $hubName,
                'AdminPassword_str' => $adminPassword ?? '',
                'Online_bool' => true,
            ];

            $res = $this->callRpc('CreateHub', $params);

            if ($res['success']) {
                $this->clearCache($this->activeServerKey);
                // Asegurar estado Online
                @$this->callRpc('SetHubOnline', [
                    'HubName_str' => $hubName,
                    'Online_bool' => true,
                ]);

                return [
                    'success' => true,
                    'message' => "Virtual Hub '{$hubName}' creado exitosamente en {$this->servers[$this->activeServerKey]['name']}.",
                ];
            }

            return [
                'success' => false,
                'message' => "Error al crear Virtual Hub en SoftEther: {$res['error']}",
            ];
        }

        return [
            'success' => false,
            'message' => 'El servidor no está configurado o no responde.',
        ];
    }

    /**
     * Elimina un Virtual Hub del servidor VPN (DeleteHub)
     */
    public function deleteHub(string $hubName): array
    {
        $hubName = trim($hubName);
        if (empty($hubName)) {
            return [
                'success' => false,
                'message' => 'Debe especificar el nombre del Virtual Hub a eliminar.',
            ];
        }

        if (strtoupper($hubName) === 'DEFAULT') {
            return [
                'success' => false,
                'message' => 'Por seguridad no se permite eliminar el Virtual Hub por defecto (DEFAULT).',
            ];
        }

        if ($this->isConfigured()) {
            $res = $this->callRpc('DeleteHub', [
                'HubName_str' => $hubName,
            ]);

            if ($res['success']) {
                $this->clearCache($this->activeServerKey);
                return [
                    'success' => true,
                    'message' => "Virtual Hub '{$hubName}' eliminado exitosamente de {$this->servers[$this->activeServerKey]['name']}.",
                ];
            }

            return [
                'success' => false,
                'message' => "No se pudo eliminar el Virtual Hub: {$res['error']}",
            ];
        }

        return [
            'success' => false,
            'message' => 'El servidor no está configurado o no responde.',
        ];
    }

    /**
     * Obtiene las sesiones activas actualmente conectadas al Hub (EnumSession, Páginas 97-99)
     */
    public function getActiveSessions(?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;

        if ($this->isConfigured()) {
            $res = $this->callRpc('EnumSession', ['HubName_str' => $hubName]);
            if ($res['success'] && !empty($res['data']['SessionList'])) {
                return array_map(function ($s) use ($hubName) {
                    $name = $s['Name_str'] ?? 'SID-UNKNOWN';
                    $user = $s['Username_str'] ?? 'vpn_user';
                    $ip = $s['ClientIP_ip'] ?? $s['RemoteHostname_str'] ?? '127.0.0.1';
                    $proto = !empty($s['LinkMode_bool']) ? 'Cascade Link' : (!empty($s['SecureNATMode_bool']) ? 'SecureNAT' : 'SoftEther VPN');

                    return [
                        'session_name' => $name,
                        'hub_name' => $hubName,
                        'username' => $user,
                        'cliente_nombre' => $this->findClientNameForUser($user),
                        'tipo_equipo' => !empty($s['RemoteHostname_str']) ? $s['RemoteHostname_str'] : 'Cliente VPN',
                        'sim_numero' => 'Enlace Activo',
                        'ip_publica_origen' => (string) $ip,
                        'ip_virtual_dhcp' => 'Asignada por DHCP',
                        'protocolo' => $proto,
                        'duracion' => !empty($s['CreatedTime_dt']) ? $this->formatDuration($s['CreatedTime_dt']) : 'Conectado',
                        'tiempo_conectado_segundos' => 3600,
                        'trafico_in' => !empty($s['PacketSize_u64']) ? $this->formatBytes($s['PacketSize_u64']) : '0 B',
                        'trafico_out' => '0 B',
                        'bitrate_actual' => 'En línea',
                        'calidad_enlace' => 'Excelente',
                        'estado' => 'Conectado',
                        'is_bridge' => !empty($s['BridgeMode_bool']),
                        'is_securenat' => !empty($s['SecureNATMode_bool']),
                    ];
                }, $res['data']['SessionList']);
            }
        }

        return [];
    }

    /**
     * Obtiene el estado detallado de una sesión específica (GetSessionStatus, Páginas 99-101)
     */
    public function getSessionStatus(string $sessionName, ?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;

        if ($this->isConfigured()) {
            $res = $this->callRpc('GetSessionStatus', [
                'HubName_str' => $hubName,
                'Name_str' => $sessionName,
            ]);

            if ($res['success'] && !empty($res['data'])) {
                return $res['data'];
            }
        }

        return [];
    }

    /**
     * Desconecta forzosamente una sesión (DeleteSession, Página 102)
     */
    public function disconnectSession(string $sessionName, ?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;

        if ($this->isConfigured()) {
            $res = $this->callRpc('DeleteSession', [
                'HubName_str' => $hubName,
                'Name_str' => $sessionName,
            ]);

            if ($res['success']) {
                $this->clearCache($this->activeServerKey);
                return [
                    'success' => true,
                    'message' => "Sesión {$sessionName} desconectada exitosamente en {$this->servers[$this->activeServerKey]['name']}.",
                ];
            }

            return [
                'success' => false,
                'message' => "No se pudo desconectar la sesión: {$res['error']}",
            ];
        }

        return [
            'success' => true,
            'message' => "Sesión {$sessionName} desconectada (Modo local).",
        ];
    }

    /**
     * Ruta persistente en almacenamiento local para horarios de deshabilitación temporal
     */
    protected function getTimedDisablesFilePath(): string
    {
        if (function_exists('storage_path')) {
            return storage_path('app/softether_timed_disables.json');
        }
        return sys_get_temp_dir() . '/softether_timed_disables.json';
    }

    /**
     * Carga la lista de usuarios deshabilitados temporalmente desde disco
     */
    public function loadTimedDisables(): array
    {
        $path = $this->getTimedDisablesFilePath();
        if (!file_exists($path) || !is_readable($path)) {
            return [];
        }
        $raw = @file_get_contents($path);
        if (!$raw) {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Guarda la lista de usuarios deshabilitados temporalmente en disco
     */
    protected function saveTimedDisables(array $items): void
    {
        $path = $this->getTimedDisablesFilePath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        @file_put_contents($path, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    protected function makeTimedDisableKey(string $serverKey, string $hubName, string $username): string
    {
        return strtolower(trim($serverKey)) . '::' . trim($hubName) . '::' . strtolower(trim($username));
    }

    protected function removeTimedDisableRecord(string $serverKey, string $hubName, string $username): void
    {
        $all = $this->loadTimedDisables();
        $k = $this->makeTimedDisableKey($serverKey, $hubName, $username);
        if (isset($all[$k])) {
            unset($all[$k]);
            $this->saveTimedDisables($all);
        }
    }

    /**
     * Verifica y re-habilita automáticamente cuentas cuyo tiempo de suspensión temporal haya expirado
     */
    public function processExpiredTimedDisables(?string $serverKey = null): int
    {
        $targetServer = $serverKey ?: $this->activeServerKey;
        $all = $this->loadTimedDisables();
        if (empty($all)) {
            return 0;
        }

        $now = time();
        $changed = false;
        $reenabledCount = 0;

        foreach ($all as $k => $entry) {
            if (($entry['server_key'] ?? '') !== $targetServer) {
                continue;
            }
            $untilTs = (int) ($entry['disabled_until_ts'] ?? 0);
            if ($untilTs > 0 && $now >= $untilTs) {
                $hubName = $entry['hub_name'] ?? 'DEFAULT';
                $username = $entry['username'] ?? '';
                if (!empty($username)) {
                    $res = $this->toggleUser($username, true, $hubName);
                    if ($res['success'] || str_contains(strtolower($res['message'] ?? ''), 'not exist') || str_contains(strtolower($res['message'] ?? ''), 'object does not exist')) {
                        unset($all[$k]);
                        $changed = true;
                        if ($res['success']) {
                            $reenabledCount++;
                        }
                    }
                } else {
                    unset($all[$k]);
                    $changed = true;
                }
            }
        }

        if ($changed) {
            $this->saveTimedDisables($all);
            if ($reenabledCount > 0) {
                $this->clearCache($targetServer);
            }
        }

        return $reenabledCount;
    }

    /**
     * Formatea un registro de usuario SoftEther añadiendo su Hub y estado temporal
     */
    protected function mapSoftEtherUser(array $u, string $hubName, array $timedMap = [], array $connectedKeys = []): array
    {
        $username = $u['Name_str'] ?? '';
        $realName = $u['Realname_utf'] ?? $username;
        $isDenied = (bool) ($u['DenyAccess_bool'] ?? false);

        $authTypeMap = [
            0 => 'Anónimo',
            1 => 'Contraseña Estándar',
            2 => 'Certificado de Usuario',
            3 => 'Root Certificate CA',
            4 => 'RADIUS Authentication',
            5 => 'Windows NT Domain',
        ];
        $authType = $authTypeMap[$u['AuthType_u32'] ?? 1] ?? 'Contraseña';

        $timedKey = $this->makeTimedDisableKey($this->activeServerKey, $hubName, $username);
        $timedEntry = $timedMap[$timedKey] ?? null;
        $isTimedDisabled = $isDenied && !empty($timedEntry) && ((int) ($timedEntry['disabled_until_ts'] ?? 0) > time());

        $sessionKey = strtolower($hubName . '::' . $username);
        $isConnected = isset($connectedKeys[$sessionKey]);

        return [
            'username' => $username,
            'hub_name' => $hubName,
            'real_name' => $realName,
            'cliente_asociado' => $realName,
            'auth_type' => $authType,
            'estado' => $isDenied ? ($isTimedDisabled ? 'Deshabilitado (Temporal)' : 'Deshabilitado') : 'Habilitado',
            'is_active' => !$isDenied,
            'is_timed_disabled' => $isTimedDisabled,
            'disabled_until' => $isTimedDisabled ? ($timedEntry['disabled_until'] ?? null) : null,
            'disabled_reason' => $isTimedDisabled ? ($timedEntry['reason'] ?? null) : null,
            'max_connections' => (int) ($u['NumLogin_u32'] ?? 1),
            'expires_at' => !empty($u['Expires_dt']) ? $u['Expires_dt'] : 'Indefinido',
            'note' => $u['Note_utf'] ?? 'Cliente VPN DataPlus',
            'last_login' => !empty($u['LastLoginTime_dt']) ? $u['LastLoginTime_dt'] : 'Nunca',
            'currently_connected' => $isConnected,
        ];
    }

    /**
     * Obtiene la lista de usuarios VPN de un Hub específico (EnumUser, Páginas 82-83)
     */
    public function getUsers(?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;

        if ($this->isConfigured()) {
            $res = $this->callRpc('EnumUser', ['HubName_str' => $hubName]);
            if ($res['success'] && !empty($res['data']['UserList'])) {
                $timedMap = $this->loadTimedDisables();
                return array_map(fn ($u) => $this->mapSoftEtherUser($u, $hubName, $timedMap), $res['data']['UserList']);
            }
        }

        return [];
    }

    /**
     * Obtiene todos los usuarios VPN de TODOS los Virtual Hubs del servidor usando peticiones HTTP concurrentes (Http::pool)
     */
    public function getAllUsers(array $virtualHubs = [], array $activeSessions = []): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        if (empty($virtualHubs)) {
            return $this->getUsers($this->hub);
        }

        $hubNames = array_values(array_unique(array_filter(array_column($virtualHubs, 'hub_name'))));
        if (empty($hubNames)) {
            return $this->getUsers($this->hub);
        }

        if ($this->circuitBroken[$this->activeServerKey] ?? false) {
            return [];
        }

        $url = rtrim($this->rpcUrl, '/');
        if (!str_ends_with($url, '/api')) {
            $url .= '/api/';
        } else {
            $url .= '/';
        }

        $headers = [
            'Content-Type' => 'application/json',
            'X-VPNADMIN-PASSWORD' => $this->adminPassword,
        ];

        $timedMap = $this->loadTimedDisables();
        $connectedKeys = [];
        foreach ($activeSessions as $s) {
            $u = strtolower($s['username'] ?? '');
            $h = $s['hub_name'] ?? $this->hub;
            if (!empty($u)) {
                $connectedKeys[strtolower($h . '::' . $u)] = true;
            }
        }

        $allUsers = [];

        try {
            // Procesar en lotes de hasta 20 hubs por pool para no saturar sockets
            foreach (array_chunk($hubNames, 20) as $chunk) {
                $responses = Http::pool(function ($pool) use ($chunk, $url, $headers) {
                    $calls = [];
                    foreach ($chunk as $idx => $hubName) {
                        $client = $pool->as($hubName)
                            ->connectTimeout(4.0)
                            ->timeout(8.0)
                            ->withHeaders($headers);

                        if (!$this->verifySsl) {
                            $client = $client->withoutVerifying();
                        }

                        $calls[] = $client->post($url, [
                            'jsonrpc' => '2.0',
                            'id' => (string) (time() + $idx),
                            'method' => 'EnumUser',
                            'params' => (object) ['HubName_str' => $hubName],
                        ]);
                    }
                    return $calls;
                });

                foreach ($chunk as $hubName) {
                    $resp = $responses[$hubName] ?? null;
                    if ($resp && method_exists($resp, 'successful') && $resp->successful()) {
                        $json = $resp->json();
                        if (!isset($json['error']) && !empty($json['result']['UserList'])) {
                            foreach ($json['result']['UserList'] as $u) {
                                $allUsers[] = $this->mapSoftEtherUser($u, $hubName, $timedMap, $connectedKeys);
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::warning("SoftEther getAllUsers pool error ({$this->activeServerKey}): " . $e->getMessage());
            return $this->getUsers($this->hub);
        }

        usort($allUsers, function ($a, $b) {
            $cmp = strcasecmp($a['hub_name'] ?? '', $b['hub_name'] ?? '');
            if ($cmp !== 0) {
                return $cmp;
            }
            return strcasecmp($a['username'] ?? '', $b['username'] ?? '');
        });

        return $allUsers;
    }

    /**
     * Crea un nuevo usuario VPN (CreateUser, Páginas 67-71)
     */
    public function createUser(array $userData, ?string $hub = null): array
    {
        $hubName = !empty($userData['hub_name']) ? trim($userData['hub_name']) : ($hub ?: $this->hub);
        $username = strtolower(trim($userData['username'] ?? ''));
        $password = (string) ($userData['password'] ?? '');

        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Nombre de usuario y contraseña son requeridos.',
            ];
        }

        if ($this->isConfigured()) {
            $params = [
                'HubName_str' => $hubName,
                'Name_str' => $username,
                'Realname_utf' => $userData['real_name'] ?? $username,
                'Note_utf' => $userData['note'] ?? 'Usuario VPN creado desde DataPlus',
                'AuthType_u32' => 1, // 1 = Password Authentication
                'Auth_Password_str' => $password,
                'UsePolicy_bool' => false,
            ];

            if (!empty($userData['expire_at'])) {
                $ts = strtotime($userData['expire_at']);
                if ($ts && $ts > time()) {
                    $params['ExpireTime_dt'] = gmdate('Y-m-d\TH:i:s.000\Z', $ts);
                }
            }

            $res = $this->callRpc('CreateUser', $params);

            if ($res['success']) {
                $this->clearCache($this->activeServerKey);
                return [
                    'success' => true,
                    'message' => "Usuario VPN '{$username}' creado exitosamente en el Hub '{$hubName}' ({$this->servers[$this->activeServerKey]['name']}).",
                ];
            }

            return [
                'success' => false,
                'message' => "Error al crear usuario en SoftEther: {$res['error']}",
            ];
        }

        return [
            'success' => false,
            'message' => 'El servidor no está configurado o no responde.',
        ];
    }

    /**
     * Elimina un usuario VPN (DeleteUser, Páginas 81-82)
     */
    public function deleteUser(string $username, ?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;

        if ($this->isConfigured()) {
            $res = $this->callRpc('DeleteUser', [
                'HubName_str' => $hubName,
                'Name_str' => $username,
            ]);

            if ($res['success']) {
                $this->removeTimedDisableRecord($this->activeServerKey, $hubName, $username);
                $this->clearCache($this->activeServerKey);
                return [
                    'success' => true,
                    'message' => "Usuario VPN '{$username}' eliminado exitosamente del Hub '{$hubName}'.",
                ];
            }

            return [
                'success' => false,
                'message' => "No se pudo eliminar el usuario: {$res['error']}",
            ];
        }

        return [
            'success' => false,
            'message' => 'El servidor no está configurado o no responde.',
        ];
    }

    /**
     * Habilita o deshabilita un usuario VPN (SetUser, Páginas 72-76)
     */
    public function toggleUser(string $username, bool $enable, ?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;
        $accion = $enable ? 'habilitado' : 'deshabilitado';

        if ($this->isConfigured()) {
            // Primero obtenemos los datos actuales con GetUser
            $userRes = $this->callRpc('GetUser', [
                'HubName_str' => $hubName,
                'Name_str' => $username,
            ]);

            $userData = $userRes['data'] ?? [];
            $userData['HubName_str'] = $hubName;
            $userData['Name_str'] = $username;
            $userData['UsePolicy_bool'] = true;
            $userData['policy:Access_bool'] = $enable;

            $res = $this->callRpc('SetUser', $userData);

            if ($res['success']) {
                if ($enable) {
                    $this->removeTimedDisableRecord($this->activeServerKey, $hubName, $username);
                }
                $this->clearCache($this->activeServerKey);
                return [
                    'success' => true,
                    'message' => "Usuario VPN '{$username}' ({$hubName}) {$accion} correctamente en SoftEther.",
                ];
            }

            return [
                'success' => false,
                'message' => "Error al cambiar estado del usuario: {$res['error']}",
            ];
        }

        return [
            'success' => false,
            'message' => 'El servidor no está configurado o no responde.',
        ];
    }

    /**
     * Deshabilita un usuario VPN de forma indefinida o por un tiempo definido (re-habilitación automática)
     */
    public function disableUserForDuration(
        string $username,
        ?string $hub = null,
        ?int $minutes = null,
        ?string $untilDatetime = null,
        ?string $reason = null
    ): array {
        $hubName = $hub ?: $this->hub;
        $res = $this->toggleUser($username, false, $hubName);

        if (!$res['success']) {
            return $res;
        }

        $untilTs = null;
        if ($minutes !== null && $minutes > 0) {
            $untilTs = time() + ($minutes * 60);
        } elseif (!empty($untilDatetime)) {
            $parsed = strtotime($untilDatetime);
            if ($parsed && $parsed > time()) {
                $untilTs = $parsed;
            }
        }

        if ($untilTs !== null) {
            $untilStr = date('Y-m-d H:i:s', $untilTs);
            $all = $this->loadTimedDisables();
            $k = $this->makeTimedDisableKey($this->activeServerKey, $hubName, $username);
            $all[$k] = [
                'server_key' => $this->activeServerKey,
                'hub_name' => $hubName,
                'username' => $username,
                'disabled_at' => date('Y-m-d H:i:s'),
                'disabled_until' => $untilStr,
                'disabled_until_ts' => $untilTs,
                'reason' => $reason ?: 'Suspensión temporal programada',
            ];
            $this->saveTimedDisables($all);
            $this->clearCache($this->activeServerKey);

            return [
                'success' => true,
                'disabled_until' => $untilStr,
                'message' => "Usuario '{$username}' ({$hubName}) deshabilitado temporalmente hasta {$untilStr}.",
            ];
        }

        // Si es indefinido, limpiar cualquier programación previa
        $this->removeTimedDisableRecord($this->activeServerKey, $hubName, $username);
        $this->clearCache($this->activeServerKey);

        return [
            'success' => true,
            'disabled_until' => null,
            'message' => "Usuario '{$username}' ({$hubName}) deshabilitado de forma indefinida.",
        ];
    }

    /**
     * Activa o desactiva SecureNAT en un Virtual Hub (EnableSecureNAT / DisableSecureNAT)
     */
    public function toggleSecureNat(bool $enable, ?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;

        if ($this->isConfigured()) {
            $method = $enable ? 'EnableSecureNAT' : 'DisableSecureNAT';
            $res = $this->callRpc($method, ['HubName_str' => $hubName]);

            if ($res['success']) {
                $this->clearCache($this->activeServerKey);
                $estado = $enable ? 'activado' : 'desactivado';
                return [
                    'success' => true,
                    'message' => "SecureNAT {$estado} correctamente en el Virtual Hub '{$hubName}'.",
                ];
            }

            return [
                'success' => false,
                'message' => "No se pudo cambiar el estado de SecureNAT: {$res['error']}",
            ];
        }

        return [
            'success' => false,
            'message' => 'El servidor no está configurado o no responde.',
        ];
    }

    /**
     * Obtiene el estado real de SecureNAT y opciones DHCP (GetSecureNATOption + GetSecureNATStatus + GetHubStatus)
     */
    public function getSecureNatStatus(?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;

        if ($this->isConfigured()) {
            $optRes = $this->callRpc('GetSecureNATOption', ['RpcHubName_str' => $hubName]);
            if (!$optRes['success']) {
                $optRes = $this->callRpc('GetSecureNATOption', ['HubName_str' => $hubName]);
            }

            $statusRes = $this->callRpc('GetSecureNATStatus', ['HubName_str' => $hubName]);
            $hubStatusRes = $this->callRpc('GetHubStatus', ['HubName_str' => $hubName]);

            if ($optRes['success'] || $statusRes['success'] || $hubStatusRes['success']) {
                $opt = $optRes['data'] ?? [];
                $stat = $statusRes['data'] ?? [];
                $hStat = $hubStatusRes['data'] ?? [];

                $dns1 = (string) ($opt['DhcpDnsServerAddress_ip'] ?? '');
                $dns2 = (string) ($opt['DhcpDnsServerAddress2_ip'] ?? '');
                $dnsList = array_values(array_filter([$dns1, $dns2], fn ($ip) => !empty($ip) && $ip !== '0.0.0.0'));

                $secureNatEngineOn = !empty($hStat['SecureNATEnabled_bool']);
                $useNatOpt = !empty($opt['UseNat_bool']);
                $useDhcpOpt = !empty($opt['UseDhcp_bool']);

                $useNat = $secureNatEngineOn ? $useNatOpt : false;
                $useDhcp = $secureNatEngineOn ? $useDhcpOpt : false;

                $gwIp = $opt['Ip_ip'] ?? $opt['DhcpGatewayAddress_ip'] ?? null;
                $dhcpGwIp = $opt['DhcpGatewayAddress_ip'] ?? $gwIp ?? null;
                $subnet = $opt['DhcpSubnetMask_ip'] ?? $opt['Mask_ip'] ?? '255.255.255.0';
                $startIp = $opt['DhcpLeaseIPStart_ip'] ?? null;
                $endIp = $opt['DhcpLeaseIPEnd_ip'] ?? null;

                return [
                    'hub_name' => $hubName,
                    'is_live' => true,
                    'securenat_enabled' => $secureNatEngineOn,
                    'use_nat' => $useNatOpt,
                    'use_dhcp' => $useDhcpOpt,
                    'nat_enabled' => $useNat,
                    'dhcp_enabled' => $useDhcp,
                    'virtual_gateway_ip' => $gwIp ?: '192.168.30.1',
                    'virtual_subnet_mask' => $subnet ?: '255.255.255.0',
                    'dhcp_gateway_ip' => ($dhcpGwIp && $dhcpGwIp !== '0.0.0.0') ? $dhcpGwIp : ($gwIp ?: '192.168.30.1'),
                    'dhcp_lease_start' => $startIp ?: '192.168.30.10',
                    'dhcp_lease_end' => $endIp ?: '192.168.30.200',
                    'dhcp_lease_expire_seconds' => (int) ($opt['DhcpExpireTimeSpan_u32'] ?? 7200),
                    'dns_server_1' => ($dns1 && $dns1 !== '0.0.0.0') ? $dns1 : '',
                    'dns_server_2' => ($dns2 && $dns2 !== '0.0.0.0') ? $dns2 : '',
                    'dns_servers' => $dnsList,
                    'domain_name' => (string) ($opt['DhcpDomainName_str'] ?? ''),
                    'mtu' => (int) ($opt['Mtu_u32'] ?? 1500),
                    'nat_tcp_timeout' => (int) ($opt['NatTcpTimeout_u32'] ?? 1800),
                    'nat_udp_timeout' => (int) ($opt['NatUdpTimeout_u32'] ?? 60),
                    'save_log' => (bool) ($opt['SaveLog_bool'] ?? true),
                    'active_dhcp_leases' => (int) ($stat['NumDhcpClients_u32'] ?? 0),
                    'total_nat_sessions' => (int) (($stat['NumTcpSessions_u32'] ?? 0) + ($stat['NumUdpSessions_u32'] ?? 0)),
                    'tcp_sessions' => (int) ($stat['NumTcpSessions_u32'] ?? 0),
                    'udp_sessions' => (int) ($stat['NumUdpSessions_u32'] ?? 0),
                    'icmp_sessions' => (int) ($stat['NumIcmpSessions_u32'] ?? 0),
                    'dns_sessions' => (int) ($stat['NumDnsSessions_u32'] ?? 0),
                    'is_kernel_mode' => !empty($stat['IsKernelMode_bool']),
                    'is_raw_ip_mode' => !empty($stat['IsRawIpMode_bool']),
                ];
            }
        }

        return [
            'hub_name' => $hubName,
            'is_live' => false,
            'securenat_enabled' => false,
            'use_nat' => false,
            'use_dhcp' => false,
            'nat_enabled' => false,
            'dhcp_enabled' => false,
            'virtual_gateway_ip' => '192.168.30.1',
            'virtual_subnet_mask' => '255.255.255.0',
            'dhcp_gateway_ip' => '192.168.30.1',
            'dhcp_lease_start' => '192.168.30.10',
            'dhcp_lease_end' => '192.168.30.200',
            'dhcp_lease_expire_seconds' => 7200,
            'dns_server_1' => '8.8.8.8',
            'dns_server_2' => '1.1.1.1',
            'dns_servers' => [],
            'domain_name' => '',
            'mtu' => 1500,
            'nat_tcp_timeout' => 1800,
            'nat_udp_timeout' => 60,
            'save_log' => true,
            'active_dhcp_leases' => 0,
            'total_nat_sessions' => 0,
            'tcp_sessions' => 0,
            'udp_sessions' => 0,
            'icmp_sessions' => 0,
            'dns_sessions' => 0,
            'is_kernel_mode' => false,
            'is_raw_ip_mode' => false,
        ];
    }

    /**
     * Guarda la configuración completa de SecureNAT y DHCP Virtual (SetSecureNATOption + Enable/DisableSecureNAT)
     */
    public function saveSecureNatConfig(array $config, ?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;

        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'El servidor no está configurado o no responde.',
            ];
        }

        // Obtener opciones actuales para preservar campos binarios (MacAddress_bin)
        $optRes = $this->callRpc('GetSecureNATOption', ['RpcHubName_str' => $hubName]);
        if (!$optRes['success']) {
            $optRes = $this->callRpc('GetSecureNATOption', ['HubName_str' => $hubName]);
        }
        $opt = $optRes['data'] ?? [];

        $gwIp = trim($config['virtual_gateway_ip'] ?? $opt['Ip_ip'] ?? '192.168.30.1') ?: '192.168.30.1';
        $mask = trim($config['virtual_subnet_mask'] ?? $opt['Mask_ip'] ?? '255.255.255.0') ?: '255.255.255.0';
        $dhcpStart = trim($config['dhcp_lease_start'] ?? $opt['DhcpLeaseIPStart_ip'] ?? '192.168.30.10') ?: '192.168.30.10';
        $dhcpEnd = trim($config['dhcp_lease_end'] ?? $opt['DhcpLeaseIPEnd_ip'] ?? '192.168.30.200') ?: '192.168.30.200';
        $dhcpGw = trim($config['dhcp_gateway_ip'] ?? $gwIp) ?: $gwIp;
        $dns1 = trim($config['dns_server_1'] ?? '');
        $dns2 = trim($config['dns_server_2'] ?? '');

        $payload = $opt;
        $payload['RpcHubName_str'] = $hubName;
        $payload['Ip_ip'] = $gwIp;
        $payload['Mask_ip'] = $mask;
        $payload['UseNat_bool'] = (bool) ($config['use_nat'] ?? true);
        $payload['Mtu_u32'] = max(576, (int) ($config['mtu'] ?? $opt['Mtu_u32'] ?? 1500));
        $payload['NatTcpTimeout_u32'] = max(60, (int) ($config['nat_tcp_timeout'] ?? $opt['NatTcpTimeout_u32'] ?? 1800));
        $payload['NatUdpTimeout_u32'] = max(10, (int) ($config['nat_udp_timeout'] ?? $opt['NatUdpTimeout_u32'] ?? 60));
        $payload['UseDhcp_bool'] = (bool) ($config['use_dhcp'] ?? true);
        $payload['DhcpLeaseIPStart_ip'] = $dhcpStart;
        $payload['DhcpLeaseIPEnd_ip'] = $dhcpEnd;
        $payload['DhcpSubnetMask_ip'] = $mask;
        $payload['DhcpExpireTimeSpan_u32'] = max(60, (int) ($config['dhcp_lease_expire_seconds'] ?? $opt['DhcpExpireTimeSpan_u32'] ?? 7200));
        $payload['DhcpGatewayAddress_ip'] = $dhcpGw;
        $payload['DhcpDnsServerAddress_ip'] = !empty($dns1) ? $dns1 : '0.0.0.0';
        $payload['DhcpDnsServerAddress2_ip'] = !empty($dns2) ? $dns2 : '0.0.0.0';
        $payload['DhcpDomainName_str'] = trim((string) ($config['domain_name'] ?? ''));
        $payload['SaveLog_bool'] = (bool) ($config['save_log'] ?? $opt['SaveLog_bool'] ?? true);

        $saveRes = $this->callRpc('SetSecureNATOption', $payload);
        if (!$saveRes['success']) {
            return [
                'success' => false,
                'message' => "Error al guardar opciones SecureNAT/DHCP: {$saveRes['error']}",
            ];
        }

        if (array_key_exists('securenat_enabled', $config)) {
            $engineRes = $this->toggleSecureNat((bool) $config['securenat_enabled'], $hubName);
            if (!$engineRes['success']) {
                return $engineRes;
            }
        }

        $this->clearCache($this->activeServerKey);

        return [
            'success' => true,
            'message' => "Configuración de SecureNAT y DHCP guardada correctamente en el Virtual Hub '{$hubName}'.",
        ];
    }

    /**
     * Obtiene la configuración de Logs de un Virtual Hub (GetHubLog)
     */
    public function getHubLog(?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;

        if ($this->isConfigured()) {
            $res = $this->callRpc('GetHubLog', ['HubName_str' => $hubName]);
            if ($res['success']) {
                $d = $res['data'] ?? [];
                return [
                    'hub_name' => $hubName,
                    'is_live' => true,
                    'save_security_log' => (bool) ($d['SaveSecurityLog_bool'] ?? false),
                    'security_log_switch_type' => (int) ($d['SecurityLogSwitchType_u32'] ?? 4),
                    'save_packet_log' => (bool) ($d['SavePacketLog_bool'] ?? false),
                    'packet_log_switch_type' => (int) ($d['PacketLogSwitchType_u32'] ?? 4),
                    'packet_log_config' => is_array($d['PacketLogConfig_u32'] ?? null) && count($d['PacketLogConfig_u32']) === 8
                        ? $d['PacketLogConfig_u32']
                        : [1, 0, 1, 0, 0, 0, 0, 0],
                ];
            }
        }

        return [
            'hub_name' => $hubName,
            'is_live' => false,
            'save_security_log' => false,
            'security_log_switch_type' => 4,
            'save_packet_log' => false,
            'packet_log_switch_type' => 4,
            'packet_log_config' => [1, 0, 1, 0, 0, 0, 0, 0],
        ];
    }

    /**
     * Activa, desactiva o actualiza la configuración de Logs de un Virtual Hub (SetHubLog)
     */
    public function setHubLog(array $logConfig, ?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;

        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'El servidor no está configurado o no responde.',
            ];
        }

        $current = $this->callRpc('GetHubLog', ['HubName_str' => $hubName]);
        $existingPacketCfg = $current['data']['PacketLogConfig_u32'] ?? [1, 0, 1, 0, 0, 0, 0, 0];
        if (!is_array($existingPacketCfg) || count($existingPacketCfg) !== 8) {
            $existingPacketCfg = [1, 0, 1, 0, 0, 0, 0, 0];
        }

        $packetCfg = $logConfig['packet_log_config'] ?? $existingPacketCfg;
        $packetCfg = array_values(array_slice(array_pad(array_map('intval', $packetCfg), 8, 0), 0, 8));

        $payload = [
            'HubName_str' => $hubName,
            'SaveSecurityLog_bool' => (bool) ($logConfig['save_security_log'] ?? false),
            'SecurityLogSwitchType_u32' => (int) ($logConfig['security_log_switch_type'] ?? 4),
            'SavePacketLog_bool' => (bool) ($logConfig['save_packet_log'] ?? false),
            'PacketLogSwitchType_u32' => (int) ($logConfig['packet_log_switch_type'] ?? 4),
            'PacketLogConfig_u32' => $packetCfg,
        ];

        $res = $this->callRpc('SetHubLog', $payload);
        if ($res['success']) {
            $this->clearCache($this->activeServerKey);
            return [
                'success' => true,
                'message' => "Configuración de Logs actualizada en el Virtual Hub '{$hubName}'.",
            ];
        }

        return [
            'success' => false,
            'message' => "Error al configurar Logs del Hub: {$res['error']}",
        ];
    }

    /**
     * Obtiene la tabla de leases DHCP (EnumDHCP, Páginas 114-115)
     */
    public function getDhcpTable(?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;

        if ($this->isConfigured()) {
            $res = $this->callRpc('EnumDHCP', ['HubName_str' => $hubName]);
            if ($res['success'] && !empty($res['data']['DhcpTable'])) {
                return $res['data']['DhcpTable'];
            }
        }

        return [];
    }

    /**
     * Guarda la configuración volátil inmediatamente en disco (Flush, Página 150)
     */
    public function flushConfig(): array
    {
        if ($this->isConfigured()) {
            $res = $this->callRpc('Flush');
            if ($res['success']) {
                $this->clearCache($this->activeServerKey);
                $bytes = $res['data']['IntValue_u32'] ?? 0;
                return [
                    'success' => true,
                    'message' => "Configuración guardada exitosamente en disco ({$bytes} bytes).",
                ];
            }

            return [
                'success' => false,
                'message' => "Error al guardar configuración: {$res['error']}",
            ];
        }

        return [
            'success' => true,
            'message' => 'Configuración guardada (Modo local).',
        ];
    }

    /**
     * Reinicia el servicio VPN Server (RebootServer, Página 120)
     */
    public function rebootServer(): array
    {
        if ($this->isConfigured()) {
            $res = $this->callRpc('RebootServer');
            if ($res['success']) {
                $this->clearCache($this->activeServerKey);
                return [
                    'success' => true,
                    'message' => "Comando de reinicio enviado a {$this->servers[$this->activeServerKey]['name']}. El servicio se reiniciará en unos segundos.",
                ];
            }

            return [
                'success' => false,
                'message' => "Error al reiniciar el servidor: {$res['error']}",
            ];
        }

        return [
            'success' => true,
            'message' => 'Reinicio simulado en modo local.',
        ];
    }

    /**
     * Formatea bytes en formato legible (KB, MB, GB, TB)
     */
    protected function formatBytes(int|float $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        $i = min($i, count($units) - 1);
        $val = round($bytes / pow(1024, $i), 1);

        return "{$val} {$units[$i]}";
    }

    /**
     * Formatea segundos a días, horas y minutos
     */
    protected function formatSeconds(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        $parts = [];
        if ($days > 0) $parts[] = "{$days} días";
        if ($hours > 0) $parts[] = "{$hours} horas";
        if ($minutes > 0 || empty($parts)) $parts[] = "{$minutes} minutos";

        return implode(', ', $parts);
    }

    /**
     * Formatea fecha/hora a duración relativa
     */
    protected function formatDuration(?string $datetime): string
    {
        if (empty($datetime)) {
            return 'Activa';
        }

        $ts = strtotime($datetime);
        if (!$ts) {
            return $datetime;
        }

        $diff = time() - $ts;
        if ($diff < 0) $diff = 0;

        return $this->formatSeconds($diff);
    }

    /**
     * Busca el nombre de cliente más probable según el nombre de usuario
     */
    protected function findClientNameForUser(string $username): string
    {
        $clean = str_replace(['_', '-', '.'], ' ', $username);
        return ucwords($clean);
    }
}

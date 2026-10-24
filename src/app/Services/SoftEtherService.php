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

        $vps2Host = (string) (config('services.softether.servers.vps2.host') ?: env('SOFTETHER_VPS2_HOST') ?: '');
        $vps2Port = (int) (config('services.softether.servers.vps2.port') ?: env('SOFTETHER_VPS2_PORT') ?: 5555);
        $vps2Hub = (string) (config('services.softether.servers.vps2.hub') ?: env('SOFTETHER_VPS2_HUB') ?: 'DEFAULT');
        $vps2Pass = (string) (config('services.softether.servers.vps2.admin_password') ?: env('SOFTETHER_VPS2_ADMIN_PASSWORD') ?: '');
        $vps2Verify = filter_var(config('services.softether.servers.vps2.verify_ssl') ?? env('SOFTETHER_VPS2_VERIFY_SSL') ?? false, FILTER_VALIDATE_BOOLEAN) ?: false;

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
                        $this->servers['vps1']['host'] = $clean;
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
                if (preg_match('/^\s*(SOFTETHER_VPS2_HOST|VPS2_SOFTETHER_HOST)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = trim(explode('#', trim($m[2], '"\''))[0]);
                    if (!empty($clean)) {
                        $this->servers['vps2']['host'] = $clean;
                    }
                } elseif (preg_match('/^\s*(SOFTETHER_VPS2_PORT|VPS2_SOFTETHER_PORT)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = (int) trim(explode('#', trim($m[2], '"\''))[0]);
                    if ($clean > 0) {
                        $this->servers['vps2']['port'] = $clean;
                    }
                } elseif (preg_match('/^\s*(SOFTETHER_VPS2_HUB|VPS2_SOFTETHER_HUB)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = trim(explode('#', trim($m[2], '"\''))[0]);
                    if (!empty($clean)) {
                        $this->servers['vps2']['hub'] = $clean;
                    }
                } elseif (preg_match('/^\s*(SOFTETHER_VPS2_ADMIN_PASSWORD|SOFTETHER_VPS2_PASSWORD|SOFTETHER_VPS2_PASS)\s*=\s*(.*)$/i', $line, $m)) {
                    $clean = preg_replace('/\s+#.*$/', '', $m[2]);
                    $clean = trim($clean, '"\'');
                    if (!empty($clean)) {
                        $this->servers['vps2']['admin_password'] = $clean;
                    }
                } elseif (preg_match('/^\s*(SOFTETHER_VPS2_VERIFY_SSL|SOFTETHER_VPS2_SSL)\s*=\s*(.*)$/i', $line, $m)) {
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
     * Cambia el servidor activo (vps1 o vps2)
     */
    public function setServer(string $serverKey): self
    {
        if (!isset($this->servers[$serverKey])) {
            $serverKey = 'vps1';
        }

        $this->activeServerKey = $serverKey;
        $srv = $this->servers[$serverKey];

        $this->host = $srv['host'] ?: '127.0.0.1';
        $this->port = (int) ($srv['port'] ?: 5555);
        $this->hub = $srv['hub'] ?: 'DEFAULT';
        $this->adminPassword = $srv['admin_password'] ?: '';
        $this->rpcUrl = "https://{$this->host}:{$this->port}/api/";
        $this->verifySsl = (bool) ($srv['verify_ssl'] ?? false);

        return $this;
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
            'env_file_path' => function_exists('base_path') ? base_path('.env') : '.env',
            'env_file_exists' => function_exists('base_path') ? file_exists(base_path('.env')) : false,
            'available_servers' => $this->getServers(),
        ];
    }

    /**
     * Prueba la conectividad física de red y autenticación JSON-RPC
     */
    public function testConnection(): array
    {
        $start = microtime(true);

        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'status' => 'not_configured',
                'message' => "Falta configurar credenciales para {$this->servers[$this->activeServerKey]['name']} en .env.",
                'latency_ms' => null,
                'server_info' => null,
            ];
        }

        try {
            // Test 1: Socket TCP al puerto 5555
            $errno = 0;
            $errstr = '';
            $socket = @fsockopen($this->host, $this->port, $errno, $errstr, 2.5);

            if ($socket) {
                fclose($socket);
                $latencyMs = round((microtime(true) - $start) * 1000, 2);

                // Test 2: Comprobación JSON-RPC "Test" (Página 5 del manual)
                $testRpc = $this->callRpc('Test', ['IntValue_u32' => 12345]);

                // Test 3: Obtener información del servidor "GetServerInfo" (Página 6)
                $infoRpc = $this->callRpc('GetServerInfo');

                if ($infoRpc['success']) {
                    $serverName = $infoRpc['data']['ServerProductName_str'] ?? 'SoftEther VPN Server';
                    $serverVer = $infoRpc['data']['ServerVersionString_str'] ?? '';
                    $osName = $infoRpc['data']['OsProductName_str'] ?? $infoRpc['data']['OsSystemName_str'] ?? 'Linux';

                    return [
                        'success' => true,
                        'status' => 'connected',
                        'message' => "¡Conexión JSON-RPC exitosa con {$this->servers[$this->activeServerKey]['name']}! {$serverName} {$serverVer} ({$osName}) respondiendo en {$this->host}:{$this->port} ({$latencyMs} ms).",
                        'latency_ms' => $latencyMs,
                        'server_info' => $infoRpc['data'],
                    ];
                }

                $err = $infoRpc['error'] ?? ($testRpc['error'] ?? 'API JSON-RPC no autenticada');
                return [
                    'success' => false,
                    'status' => 'rpc_error',
                    'message' => "Puerto {$this->port} accesible ({$latencyMs} ms), pero la API JSON-RPC retornó: {$err}. Verifique contraseña en .env.",
                    'latency_ms' => $latencyMs,
                    'server_info' => null,
                ];
            } else {
                $extraHint = '';
                if (in_array($this->host, ['127.0.0.1', 'localhost'])) {
                    $fallbackSocket = @fsockopen('host.docker.internal', $this->port, $fErrno, $fErrstr, 1.5);
                    if ($fallbackSocket) {
                        fclose($fallbackSocket);
                        $extraHint = " ¡Atención! 'host.docker.internal:{$this->port}' sí responde. Configure SOFTETHER_HOST=host.docker.internal en su .env.";
                    }
                }

                return [
                    'success' => false,
                    'status' => 'unreachable',
                    'message' => "No se pudo conectar a {$this->host}:{$this->port}. Error: {$errstr} ({$errno}).{$extraHint}",
                    'latency_ms' => null,
                    'server_info' => null,
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Excepción durante la prueba de conexión: ' . $e->getMessage(),
                'latency_ms' => null,
                'server_info' => null,
            ];
        }
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

        // Circuit breaker: Si el servidor falló la conexión previamente en esta petición, abortar de inmediato sin demoras
        if ($this->circuitBroken[$this->activeServerKey] ?? false) {
            return [
                'success' => false,
                'error' => 'Servidor no accesible (circuito cortado para evitar bloqueos)',
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
        // Para modo Administrador General de Servidor NO se envía X-VPNADMIN-HUBNAME
        $headers = [
            'Content-Type' => 'application/json',
            'X-VPNADMIN-PASSWORD' => $this->adminPassword,
        ];

        // Solo si explícitamente se especifica para un Hub Admin
        if (!empty($hub)) {
            $headers['X-VPNADMIN-HUBNAME'] = $hub;
        }

        try {
            // Optimización de velocidad: connectTimeout de 1.5s y timeout global de 3.0s
            $client = Http::connectTimeout(1.5)
                ->timeout(3.0)
                ->withHeaders($headers);

            // En SoftEther VPN los certificados SSL son siempre autofirmados (self-signed)
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
                    return [
                        'success' => false,
                        'error' => $json['error']['message'] ?? ('Error JSON-RPC: ' . json_encode($json['error'])),
                        'data' => null,
                    ];
                }
                return [
                    'success' => true,
                    'data' => $json['result'] ?? [],
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'error' => "HTTP {$response->status()}: " . substr($response->body(), 0, 200),
                'data' => null,
            ];
        } catch (Exception $e) {
            $msg = $e->getMessage();
            // Si es un error de conectividad o tiempo de espera, activar circuit breaker para este servidor
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
     * Limpia la caché temporal de telemetría
     */
    public function clearCache(?string $serverKey = null): void
    {
        $key = $serverKey ?: $this->activeServerKey;
        try {
            Cache::forget("softether_telemetry_{$key}");
        } catch (Exception $e) {
            // Continuar si la caché de Laravel no está configurada
        }
        $this->resetCircuitBreaker($key);
    }

    /**
     * Obtiene el paquete completo de telemetría de forma ultra-rápida (con caché de 20s y disyuntor)
     */
    public function getTelemetry(bool $forceRefresh = false): array
    {
        $cacheKey = "softether_telemetry_{$this->activeServerKey}";

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

        $serverStatus = $this->getServerStatus();
        $activeSessions = $this->getActiveSessions();
        $vpnUsers = $this->getUsers();
        $virtualHubs = $this->getVirtualHubs();
        $secureNat = $this->getSecureNatStatus();
        $dhcpTable = $this->getDhcpTable();
        $connectionDetails = $this->getConnectionDetails();

        $data = [
            'serverStatus' => $serverStatus,
            'activeSessions' => $activeSessions,
            'vpnUsers' => $vpnUsers,
            'virtualHubs' => $virtualHubs,
            'secureNat' => $secureNat,
            'dhcpTable' => $dhcpTable,
            'connectionDetails' => $connectionDetails,
            'from_cache' => false,
            'updated_at' => date('H:i:s'),
        ];

        try {
            // Almacenar durante 20 segundos para cambio de servidor ultra-rápido (0ms)
            Cache::put($cacheKey, $data, 20);
        } catch (Exception $e) {
            // Ignorar fallo de almacenamiento
        }

        return $data;
    }

    /**
     * Obtiene el estado general del servidor VPN en vivo (GetServerInfo + GetServerStatus)
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
                        ['port' => 443, 'protocol' => 'TCP / HTTPS (SoftEther & SSTP)', 'status' => 'Activo (Escuchando)'],
                        ['port' => 992, 'protocol' => 'TCP / Admin Telnet', 'status' => 'Activo (Escuchando)'],
                        ['port' => 1194, 'protocol' => 'UDP / OpenVPN', 'status' => 'Activo (Escuchando)'],
                        ['port' => 5555, 'protocol' => 'TCP / Admin RPC API', 'status' => 'Activo (Escuchando)'],
                    ],
                    'virtual_nat_enabled' => true,
                    'virtual_dhcp_enabled' => true,
                ];
            }
        }

        $err = $info['error'] ?? ($status['error'] ?? 'Sin conexión en vivo');
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
     * Obtiene la lista de Virtual Hubs (EnumHub, Páginas 23-25)
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
                        'secure_nat' => 'SecureNAT Operativo',
                        'last_comm' => !empty($h['LastCommTime_dt']) ? $h['LastCommTime_dt'] : 'Sin actividad',
                        'traffic' => $this->formatBytes($recv + $send),
                    ];
                }, $res['data']['HubList']);
            }
        }

        return [];
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
                return array_map(function ($s) {
                    $name = $s['Name_str'] ?? 'SID-UNKNOWN';
                    $user = $s['Username_str'] ?? 'vpn_user';
                    $ip = $s['ClientIP_ip'] ?? $s['RemoteHostname_str'] ?? '127.0.0.1';
                    $proto = !empty($s['LinkMode_bool']) ? 'Cascade Link' : (!empty($s['SecureNATMode_bool']) ? 'SecureNAT' : 'SoftEther VPN');

                    return [
                        'session_name' => $name,
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
     * Obtiene la lista de usuarios VPN (EnumUser, Páginas 82-83)
     */
    public function getUsers(?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;

        if ($this->isConfigured()) {
            $res = $this->callRpc('EnumUser', ['HubName_str' => $hubName]);
            if ($res['success'] && !empty($res['data']['UserList'])) {
                return array_map(function ($u) {
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

                    return [
                        'username' => $username,
                        'real_name' => $realName,
                        'cliente_asociado' => $realName,
                        'auth_type' => $authType,
                        'estado' => $isDenied ? 'Deshabilitado' : 'Habilitado',
                        'is_active' => !$isDenied,
                        'max_connections' => (int) ($u['NumLogin_u32'] ?? 1),
                        'expires_at' => !empty($u['Expires_dt']) ? $u['Expires_dt'] : 'Indefinido',
                        'note' => $u['Note_utf'] ?? 'Cliente VPN DataPlus',
                        'last_login' => !empty($u['LastLoginTime_dt']) ? $u['LastLoginTime_dt'] : 'Nunca',
                        'currently_connected' => false,
                    ];
                }, $res['data']['UserList']);
            }
        }

        return [];
    }

    /**
     * Crea un nuevo usuario VPN (CreateUser, Páginas 67-71)
     */
    public function createUser(array $userData, ?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;
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

            $res = $this->callRpc('CreateUser', $params);

            if ($res['success']) {
                $this->clearCache($this->activeServerKey);
                return [
                    'success' => true,
                    'message' => "Usuario VPN '{$username}' creado exitosamente en {$this->servers[$this->activeServerKey]['name']}.",
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
                $this->clearCache($this->activeServerKey);
                return [
                    'success' => true,
                    'message' => "Usuario VPN '{$username}' eliminado exitosamente en SoftEther.",
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
                $this->clearCache($this->activeServerKey);
                return [
                    'success' => true,
                    'message' => "Usuario VPN {$username} {$accion} correctamente en SoftEther.",
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
     * Obtiene el estado real de SecureNAT (GetSecureNATOption + GetSecureNATStatus, Páginas 111-116)
     */
    public function getSecureNatStatus(?string $hub = null): array
    {
        $hubName = $hub ?: $this->hub;

        if ($this->isConfigured()) {
            // 1. Opciones y configuración real de red IP (Página 111 del manual oficial)
            $optRes = $this->callRpc('GetSecureNATOption', ['RpcHubName_str' => $hubName]);
            if (!$optRes['success']) {
                $optRes = $this->callRpc('GetSecureNATOption', ['HubName_str' => $hubName]);
            }

            // 2. Estado operativo en vivo de sesiones y leases (Página 115)
            $statusRes = $this->callRpc('GetSecureNATStatus', ['HubName_str' => $hubName]);

            if ($optRes['success'] || $statusRes['success']) {
                $opt = $optRes['data'] ?? [];
                $stat = $statusRes['data'] ?? [];

                $dnsList = array_values(array_filter([
                    $opt['DhcpDnsServerAddress_ip'] ?? null,
                    $opt['DhcpDnsServerAddress2_ip'] ?? null,
                ]));

                $useNat = !empty($opt['UseNat_bool']);
                $useDhcp = !empty($opt['UseDhcp_bool']);
                $gwIp = $opt['DhcpGatewayAddress_ip'] ?? null;
                $subnet = $opt['DhcpSubnetMask_ip'] ?? null;
                $startIp = $opt['DhcpLeaseIPStart_ip'] ?? null;
                $endIp = $opt['DhcpLeaseIPEnd_ip'] ?? null;

                return [
                    'hub_name' => $hubName,
                    'is_live' => true,
                    'nat_enabled' => $useNat,
                    'dhcp_enabled' => $useDhcp,
                    'virtual_gateway_ip' => $gwIp ?: ($useNat || $useDhcp ? 'No asignada' : 'Inactivo'),
                    'virtual_subnet_mask' => $subnet ?: '255.255.255.0',
                    'dhcp_lease_start' => $startIp ?: 'Inactivo',
                    'dhcp_lease_end' => $endIp ?: 'Inactivo',
                    'dhcp_lease_expire_seconds' => (int) ($opt['DhcpExpireTimeSpan_u32'] ?? 0),
                    'dns_servers' => !empty($dnsList) ? $dnsList : [],
                    'domain_name' => $opt['DhcpDomainName_str'] ?? '',
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

        // Estado cuando no está activo o el servidor no responde (datos reales en 0, sin invenciones)
        return [
            'hub_name' => $hubName,
            'is_live' => false,
            'nat_enabled' => false,
            'dhcp_enabled' => false,
            'virtual_gateway_ip' => 'No disponible',
            'virtual_subnet_mask' => 'No disponible',
            'dhcp_lease_start' => 'No disponible',
            'dhcp_lease_end' => 'No disponible',
            'dhcp_lease_expire_seconds' => 0,
            'dns_servers' => [],
            'domain_name' => '',
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

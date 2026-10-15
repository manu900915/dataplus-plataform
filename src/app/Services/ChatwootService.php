<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatwootService
{
    protected string $baseUrl;
    protected string $accessToken;
    protected int $accountId;
    protected int $inboxId;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) (config('services.chatwoot.base_url') ?: env('CHATWOOT_BASE_URL', 'https://chat.dataplus.cu/')), '/');
        $this->accessToken = (string) (config('services.chatwoot.access_token') ?: env('CHATWOOT_ACCESS_TOKEN', 'p9wKrE7Zt44CJWgAVtfzvjx4'));
        $this->accountId = (int) (config('services.chatwoot.account_id') ?: env('CHATWOOT_ACCOUNT_ID', 1));
        $this->inboxId = (int) (config('services.chatwoot.inbox_id') ?: env('CHATWOOT_INBOX_ID', 1));
    }

    /**
     * Formatea un número de teléfono a estándar E.164
     */
    public function formatPhoneNumber(string $phone): string
    {
        $cleaned = trim($phone);
        // Quitar espacios, guiones y paréntesis
        $digitsOnly = preg_replace('/[^\d]/', '', $cleaned);

        if (empty($digitsOnly)) {
            return '';
        }

        // Si ya empieza con +, dejar el formato
        if (str_starts_with($cleaned, '+')) {
            return '+' . $digitsOnly;
        }

        // Si es número móvil cubano de 8 dígitos empezando en 5 (ej. 53866416)
        if (strlen($digitsOnly) === 8 && str_starts_with($digitsOnly, '5')) {
            return '+53' . $digitsOnly;
        }

        // Si ya tiene código de Cuba 53 y 10 dígitos (ej. 5353866416)
        if (strlen($digitsOnly) === 10 && str_starts_with($digitsOnly, '53')) {
            return '+' . $digitsOnly;
        }

        // Caso general: agregar prefijo +
        return '+' . $digitsOnly;
    }

    /**
     * Busca un contacto existente por teléfono o crea uno nuevo en Chatwoot
     */
    public function buscarOCrearContacto(string $nombre, string $telefono): ?int
    {
        $formattedPhone = $this->formatPhoneNumber($telefono);
        if (empty($formattedPhone)) {
            return null;
        }

        $digits = preg_replace('/[^\d]/', '', $formattedPhone);
        // Si tiene código de Cuba (53), obtener los últimos 8 dígitos para búsqueda flexible
        $shortSearch = strlen($digits) >= 8 ? substr($digits, -8) : $digits;

        // 1. Intentar buscar por teléfono
        try {
            $searchUrl = "{$this->baseUrl}/api/v1/accounts/{$this->accountId}/contacts/search";
            $searchResp = Http::withHeaders([
                'api_access_token' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(10)->get($searchUrl, [
                'q' => $shortSearch,
            ]);

            if ($searchResp->successful()) {
                $payload = $searchResp->json('payload') ?? [];
                if (!empty($payload) && is_array($payload)) {
                    return (int) $payload[0]['id'];
                }
            }
        } catch (Exception $e) {
            Log::warning("Chatwoot: Error al buscar contacto {$formattedPhone}: " . $e->getMessage());
        }

        // 2. Si no existe, crear el contacto
        try {
            $createUrl = "{$this->baseUrl}/api/v1/accounts/{$this->accountId}/contacts";
            $createResp = Http::withHeaders([
                'api_access_token' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($createUrl, [
                'inbox_id' => $this->inboxId,
                'name' => $nombre ?: "Cliente {$shortSearch}",
                'phone_number' => $formattedPhone,
            ]);

            if ($createResp->successful()) {
                $contact = $createResp->json('payload.contact') ?? $createResp->json();
                return isset($contact['id']) ? (int) $contact['id'] : null;
            }

            // Si falló por duplicado (422), buscar por el teléfono completo
            if ($createResp->status() === 422) {
                $retryResp = Http::withHeaders([
                    'api_access_token' => $this->accessToken,
                ])->timeout(10)->get("{$this->baseUrl}/api/v1/accounts/{$this->accountId}/contacts/search", [
                    'q' => $formattedPhone,
                ]);

                $payload = $retryResp->json('payload') ?? [];
                if (!empty($payload)) {
                    return (int) $payload[0]['id'];
                }
            }

            Log::error("Chatwoot: No se pudo crear contacto {$nombre} ({$formattedPhone}). Respuesta: " . $createResp->body());
        } catch (Exception $e) {
            Log::error("Chatwoot: Excepción al crear contacto {$nombre}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Obtiene una conversación abierta existente para el contacto o crea una nueva
     */
    public function obtenerOCrearConversacion(int $contactId): ?int
    {
        // 1. Revisar si el contacto ya tiene conversaciones en el inbox de WhatsApp
        try {
            $convsUrl = "{$this->baseUrl}/api/v1/accounts/{$this->accountId}/contacts/{$contactId}/conversations";
            $convsResp = Http::withHeaders([
                'api_access_token' => $this->accessToken,
            ])->timeout(10)->get($convsUrl);

            if ($convsResp->successful()) {
                $payload = $convsResp->json('payload') ?? [];
                foreach ($payload as $conv) {
                    if (isset($conv['inbox_id']) && (int) $conv['inbox_id'] === $this->inboxId) {
                        return (int) $conv['id'];
                    }
                }
                // Si hay alguna conversación registrada, usar la última
                if (!empty($payload) && isset($payload[0]['id'])) {
                    return (int) $payload[0]['id'];
                }
            }
        } catch (Exception $e) {
            Log::warning("Chatwoot: Error al obtener conversaciones de contacto {$contactId}: " . $e->getMessage());
        }

        // 2. Si no hay conversación previa, crear una nueva en el inbox de WhatsApp
        try {
            $createConvUrl = "{$this->baseUrl}/api/v1/accounts/{$this->accountId}/conversations";
            $createConvResp = Http::withHeaders([
                'api_access_token' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($createConvUrl, [
                'inbox_id' => $this->inboxId,
                'contact_id' => $contactId,
                'status' => 'open',
            ]);

            if ($createConvResp->successful()) {
                return (int) $createConvResp->json('id');
            }

            Log::error("Chatwoot: Error creando conversación para contacto {$contactId}: " . $createConvResp->body());
        } catch (Exception $e) {
            Log::error("Chatwoot: Excepción al crear conversación: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Envía un mensaje saliente a través del canal de WhatsApp de Chatwoot
     *
     * @return array{success: bool, message: string, conversation_id: ?int, chatwoot_url: ?string}
     */
    public function enviarMensaje(string $nombre, string $telefono, string $mensaje): array
    {
        if (empty($telefono)) {
            return [
                'success' => false,
                'message' => 'El cliente no tiene un número telefónico registrado.',
                'conversation_id' => null,
                'chatwoot_url' => null,
            ];
        }

        $contactId = $this->buscarOCrearContacto($nombre, $telefono);
        if (!$contactId) {
            return [
                'success' => false,
                'message' => "No se pudo sincronizar el contacto {$nombre} ({$telefono}) en Chatwoot.",
                'conversation_id' => null,
                'chatwoot_url' => null,
            ];
        }

        $conversationId = $this->obtenerOCrearConversacion($contactId);
        if (!$conversationId) {
            return [
                'success' => false,
                'message' => "No se pudo abrir una conversación de WhatsApp en Chatwoot para {$nombre}.",
                'conversation_id' => null,
                'chatwoot_url' => null,
            ];
        }

        // Enviar mensaje saliente
        try {
            $msgUrl = "{$this->baseUrl}/api/v1/accounts/{$this->accountId}/conversations/{$conversationId}/messages";
            $msgResp = Http::withHeaders([
                'api_access_token' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(12)->post($msgUrl, [
                'content' => $mensaje,
                'message_type' => 'outgoing',
                'private' => false,
            ]);

            if ($msgResp->successful()) {
                $chatwootUrl = "{$this->baseUrl}/app/accounts/{$this->accountId}/conversations/{$conversationId}";
                return [
                    'success' => true,
                    'message' => "Mensaje enviado con éxito vía WhatsApp (Asistencia Técnica)",
                    'conversation_id' => $conversationId,
                    'chatwoot_url' => $chatwootUrl,
                ];
            }

            Log::error("Chatwoot: Error al enviar mensaje: " . $msgResp->body());
            return [
                'success' => false,
                'message' => 'Chatwoot rechazó el envío del mensaje: ' . $msgResp->status(),
                'conversation_id' => $conversationId,
                'chatwoot_url' => null,
            ];
        } catch (Exception $e) {
            Log::error("Chatwoot: Excepción al enviar mensaje: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de conexión con Chatwoot: ' . $e->getMessage(),
                'conversation_id' => $conversationId,
                'chatwoot_url' => null,
            ];
        }
    }
}

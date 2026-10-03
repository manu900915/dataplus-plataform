#!/usr/bin/env bash
# ============================================================
# dataplus-platform · despliegue en servidor (VPS)
# Uso:  ./deploy.sh            -> despliegue normal (sin downtime)
#       ./deploy.sh --with-ssl -> reserva para futura config TLS
# Requisitos: docker + docker compose plugin v2, archivo .env
# ============================================================
set -euo pipefail

COMPOSE_FILE="docker-compose.prod.yml"
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

log() { printf '\n\033[1;32m[deploy]\033[0m %s\n' "$*"; }
err() { printf '\n\033[1;31m[ERROR]\033[0m %s\n' "$*" >&2; exit 1; }

[ -f "$COMPOSE_FILE" ] || err "No existe $COMPOSE_FILE"
[ -f .env ] || err "Falta el archivo .env (copia y completa las credenciales)"

# 1) Backup de la base de datos ANTES de migrar
log "Respaldo de PostgreSQL..."
if docker ps --format '{{.Names}}' | grep -q '^dp-prod-db$'; then
  mkdir -p backups
  STAMP=$(date +%Y%m%d_%H%M%S)
  docker exec dp-prod-db pg_dump -U "${POSTGRES_USER:-dataplus}" "${POSTGRES_DB:-dataplus}" \
    | gzip > "backups/db_${STAMP}.sql.gz"
  # conservar solo los últimos 10 backups
  ls -t backups/db_*.sql.gz 2>/dev/null | tail -n +11 | xargs -r rm -f
  log "Backup listo: backups/db_${STAMP}.sql.gz"
else
  log "Base de datos no corriendo aún (primer despliegue), omito backup."
fi

# 2) Actualizar código
log "Actualizando código desde GitHub..."
for i in 1 2 3; do git pull --ff-only origin main && break || { echo "Reintentando git pull ($i/3)..."; sleep 3; }; done

# 3) Construir imagen (Dockerfile cachea capas, rápido si no cambió)
log "Construyendo imagen..."
docker compose -f "$COMPOSE_FILE" build app

# 4) Levantar infraestructura y aplicar migraciones antes del relanzamiento
#    (lldap NO se levanta aqui: ya existe uno en produccion, se usa via LDAP_HOST en .env)
# Asegurar que la red externa proxy_net exista
docker network inspect proxy_net >/dev/null 2>&1 || docker network create proxy_net
log "Arrancando db/redis/mailpit si hace falta..." 
docker compose -f "$COMPOSE_FILE" up -d db redis mailpit

log "Ejecutando migraciones..."
docker compose -f "$COMPOSE_FILE" run --rm app php artisan migrate --force

# 5) Relanzar todo (app + nginx) con la nueva versión
log "Relanzando servicios..."
docker compose -f "$COMPOSE_FILE" up -d

# 6) Caches de producción
log "Optimizando Laravel..."
docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:cache
docker compose -f "$COMPOSE_FILE" exec -T app php artisan route:cache
docker compose -f "$COMPOSE_FILE" exec -T app php artisan view:cache

# 7) Worker/scheduler si existen en el compose
docker compose -f "$COMPOSE_FILE" up -d --no-deps worker scheduler 2>/dev/null || true

# 8) Smoke test
log "Prueba de humo..."
sleep 3
CODE=$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:${DP_PROD_HTTP_PORT:-8080}/up" || echo 000)
[ "$CODE" = "200" ] && log "Health check OK (/up → 200)" || err "Health check falló (/up → $CODE). Revisa: docker compose -f $COMPOSE_FILE logs app"

log "✅ Despliegue completado."

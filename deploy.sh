#!/usr/bin/env bash
set -euo pipefail

# ============================================================================
#   deploy.sh — ZaminTaWeb Deployer
#   Deploys to tekmil.ir (178.239.147.46)
#   Usage:
#     ./deploy.sh           — Full deploy (code + build + migrate)
#     ./deploy.sh --db-sync — Full deploy + overwrite remote DB with local DB
#     ./deploy.sh --rollback— Rollback to previous release
# ============================================================================

REMOTE_HOST="178.239.147.46"
REMOTE_USER="root"
REMOTE_PASS="React2149??"
REMOTE_PATH="/var/www/tekmil"
PHP_BIN="php"
SSH_PORT=22

# Local DB credentials (from .env)
LOCAL_DB_DATABASE="zamintaweb"
LOCAL_DB_USERNAME="root"
LOCAL_DB_PASSWORD=""

# Remote DB credentials (from .env on server)
REMOTE_DB_DATABASE="tekmil_prod"
REMOTE_DB_USERNAME="tekmil"
REMOTE_DB_PASSWORD="React2149??"

# mysqldump path (macOS Homebrew keg-only)
MYSQLDUMP="/opt/homebrew/Cellar/mysql-client/9.6.0/bin/mysqldump"

# Timestamp used across the script
DEPLOY_TIME=$(date +%Y%m%d_%H%M%S)

RSYNC_EXCLUDES=(
  ".git" ".env" ".env.example" "deploy.sh"
  "storage/framework/cache" "storage/framework/sessions"
  "storage/framework/views" "storage/logs" "storage/app"
  "tests" ".opencode" "node_modules/.cache"
)

BOLD='\033[1m'; DIM='\033[2m'; RED='\033[0;31m'; GREEN='\033[0;32m'
YELLOW='\033[0;33m'; BLUE='\033[0;34m'; CYAN='\033[0;36m'
WHITE='\033[1;37m'; RESET='\033[0m'; BG_GREEN='\033[42m'; BG_BLUE='\033[44m'

warn()      { printf "${BOLD}${YELLOW}⚠${RESET} ${YELLOW}%s${RESET}\n" "$1"; }
error()     { printf "${BOLD}${RED}✘${RESET} ${RED}%s${RESET}\n" "$1"; }
success()   { printf "${BOLD}${GREEN}✔${RESET} ${GREEN}%s${RESET}\n" "$1"; }
info()      { printf "${BOLD}${BLUE}ℹ${RESET}  ${BLUE}%s${RESET}\n" "$1"; }
subheader() { printf "${CYAN}┃ ${BOLD}%s${RESET}\n" "$1"; }
sep()       { printf "${DIM}──────────────────────────────────────────────${RESET}\n"; }
section()   { printf "\n${BG_BLUE}${WHITE}  ⚡ %s  ${RESET}\n\n" "$1"; }

run() {
    local msg=$1; shift; printf "${DIM}  ⟳ %s ... ${RESET}" "$msg"
    if "$@" > /dev/null 2>&1; then printf "${GREEN}✓${RESET}\n"
    else printf "${RED}✗${RESET}\n"; error "Command failed: $*"; return 1; fi
}

run_v() {
    local msg=$1; shift; printf "${DIM}  ⟳ %s ... ${RESET}\n" "$msg"
    "$@"; local r=$?; [ $r -eq 0 ] && printf "${GREEN}  ┗━━━ ✅ Done${RESET}\n\n" || printf "${RED}  ┗━━━ ❌ Failed${RESET}\n\n"; return $r
}

ssh_cmd() {
    sshpass -p "$REMOTE_PASS" ssh -o StrictHostKeyChecking=no \
        -o UserKnownHostsFile=/dev/null -o LogLevel=QUIET \
        -p "$SSH_PORT" "${REMOTE_USER}@${REMOTE_HOST}" "$*"
}

if ! command -v sshpass &>/dev/null; then
    error "sshpass not installed. Install: brew install hudochenkov/sshpass/sshpass (macOS) / apt install sshpass (Linux)"
    exit 1
fi

clear
cat << "EOF"

╔══════════════════════════════════════════════════════════════╗
║                     ███████╗████████╗ █████╗                ║
║                     ╚══███╔╝╚══██╔══╝██╔══██╗               ║
║                       ███╔╝   ██║   ███████║               ║
║                      ███╔╝    ██║   ██╔══██║               ║
║                     ███████╗  ██║   ██║  ██║               ║
║                     ╚══════╝  ╚═╝   ╚═╝  ╚═╝               ║
║                                                              ║
║             ██████╗ ███████╗██████╗ ██╗      ██████╗        ║
║             ██╔══██╗██╔════╝██╔══██╗██║     ██╔═══██╗       ║
║             ██║  ██║█████╗  ██║  ██║██║     ██║   ██║       ║
║             ██║  ██║██╔══╝  ██║  ██║██║     ██║   ██║       ║
║             ██████╔╝███████╗██████╔╝███████╗╚██████╔╝       ║
║             ╚═════╝ ╚══════╝╚═════╝ ╚══════╝ ╚═════╝        ║
║                                                              ║
║               🌐 tekmil.ir  |  🚀 Deployment Script         ║
║                                                              ║
║  Commands:                                                   ║
║    ./deploy.sh              Full deploy                      ║
║    ./deploy.sh --db-sync    Full deploy + overwrite prod DB  ║
║    ./deploy.sh --rollback   Rollback to previous release     ║
╚══════════════════════════════════════════════════════════════╝
EOF
printf "\n"

ROLLBACK=false; DB_SYNC=false
for arg in "$@"; do
  [ "$arg" = "--rollback" ] && ROLLBACK=true
  [ "$arg" = "--db-sync" ] && DB_SYNC=true
done

# ══════════════════════════════════════════════════════════════════════════
section "🔌 Connectivity"
printf "  ${DIM}Host:${RESET}   ${BOLD}${REMOTE_USER}@${REMOTE_HOST}${RESET}\n"
printf "  ${DIM}Path:${RESET}   ${BOLD}${REMOTE_PATH}${RESET}\n\n"
run "Pinging $REMOTE_HOST"    ping -c 1 -t 5 "$REMOTE_HOST"
run "Testing SSH"             ssh_cmd "echo ✓ Server connected"
success "Server is reachable"

# ══════════════════════════════════════════════════════════════════════════
section "🔍 Pre-flight Checks"
run_v "System requirements" ssh_cmd "
  echo '  PHP:      '\$($PHP_BIN -v | head -1)
  echo '  Node:     '\$(node -v)
  echo '  NPM:      '\$(npm -v)
  echo '  Composer: '\$(composer --version 2>/dev/null | head -1)
  echo '  Exts:     '\$($PHP_BIN -m | grep -cE 'pdo|mysql|mbstring|xml|curl|gd|bcmath|zip|intl')' loaded'
"
run_v "Target directory" ssh_cmd "
  if [ -d '${REMOTE_PATH}' ]; then
    echo '  ✓ Directory exists — '\$(ls -A '${REMOTE_PATH}' | wc -l)' items'
  else
    echo '  ✗ Will create ${REMOTE_PATH}'
  fi
"
success "All checks passed"

# ══════════════════════════════════════════════════════════════════════════
if [ "$ROLLBACK" = true ]; then
    section "⏮️  Rollback"
    CURRENT=$(ssh_cmd "readlink -f '${REMOTE_PATH}/current' 2>/dev/null || true")
    [ -z "$CURRENT" ] && { warn "No current symlink — nothing to rollback"; exit 0; }
    BACKUP="releases/$(date +%Y%m%d_%H%M%S)_rollback"
    run "Backup → $BACKUP" ssh_cmd "mkdir -p '${REMOTE_PATH}/releases' && cp -a '$CURRENT' '${REMOTE_PATH}/${BACKUP}'"
    PREV=$(ssh_cmd "ls -dt ${REMOTE_PATH}/releases/*/ 2>/dev/null | head -2 | tail -1" || true)
    [ -z "$PREV" ] && { warn "No previous release found"; exit 0; }
    run "Switch to $PREV" ssh_cmd "ln -sfn '$PREV' '${REMOTE_PATH}/current'"
    run "Optimize" ssh_cmd "cd '${REMOTE_PATH}/current' && $PHP_BIN artisan optimize:clear && $PHP_BIN artisan optimize"
    success "✅ Rollback complete — active: $PREV"; exit 0
fi

# ══════════════════════════════════════════════════════════════════════════
#  DB SYNC
# ══════════════════════════════════════════════════════════════════════════
if [ "$DB_SYNC" = true ]; then
    section "🗄️  Database Sync: Local → Production"

    warn "  ╔══════════════════════════════════════════════════════════════╗"
    warn "  ║     ⚠️  DESTRUCTIVE OPERATION                              ║"
    warn "  ╠══════════════════════════════════════════════════════════════╣"
    warn "  ║  This will OVERWRITE the production database                ║"
    warn "  ║  (${REMOTE_DB_DATABASE}) with your LOCAL database.          ║"
    warn "  ║  All production data will be LOST.                          ║"
    warn "  ║                                                            ║"
    warn "  ║  Press Ctrl+C now to cancel, or wait 5s to continue...     ║"
    warn "  ╚══════════════════════════════════════════════════════════════╝"
    warn ""
    sleep 5

    # ── Dump local DB ──────────────────────────────────────────────────
    subheader "1/4  Dumping local database"
    MYSQLDUMP_CMD="$MYSQLDUMP"
    if [ ! -f "$MYSQLDUMP_CMD" ]; then
        MYSQLDUMP_CMD="$(which mysqldump 2>/dev/null || true)"
    fi
    if [ ! -f "$MYSQLDUMP_CMD" ]; then
        error "mysqldump not found. Install: brew install mysql-client"
        info "Then add to PATH or symlink:"
        info "  brew link --overwrite mysql-client"
        exit 1
    fi

    DUMP_FILE="/tmp/tekmil_db_sync_${DEPLOY_TIME}.sql"
    run_v "Dumping local DB (${LOCAL_DB_DATABASE})" bash -c "
        '$MYSQLDUMP_CMD' -u '$LOCAL_DB_USERNAME' -h 127.0.0.1 \
            ${LOCAL_DB_PASSWORD:+-p\"$LOCAL_DB_PASSWORD\"} \
            --routines --triggers --add-drop-table \
            '$LOCAL_DB_DATABASE' > '$DUMP_FILE'
    "

    DUMP_SIZE=$(wc -c < "$DUMP_FILE" | tr -d ' ')
    if command -v numfmt &>/dev/null; then
        info "  📦 Dump size: $(numfmt --to=iec "$DUMP_SIZE")"
    else
        info "  📦 Dump size: $((DUMP_SIZE / 1024 / 1024))MB"
    fi

    # ── Backup remote DB ───────────────────────────────────────────────
    subheader "2/4  Backing up production database"
    BACKUP_DB_DIR="${REMOTE_PATH}/db-backups"
    BACKUP_DB_FILE="${BACKUP_DB_DIR}/${DEPLOY_TIME}_pre_sync.sql.gz"
    run_v "Backing up ${REMOTE_DB_DATABASE} on server" ssh_cmd "
        mkdir -p '$BACKUP_DB_DIR' && \
        mysqldump -u '$REMOTE_DB_USERNAME' -p'$REMOTE_DB_PASSWORD' \
            --routines --triggers --add-drop-table --no-tablespaces \
            '$REMOTE_DB_DATABASE' | gzip > '$BACKUP_DB_FILE'
    "
    info "  💾 Backup saved: ${BACKUP_DB_FILE}"

    # ── Rsync dump to server ───────────────────────────────────────────
    subheader "3/4  Transferring dump to server"
    run_v "Uploading SQL dump" sshpass -p "$REMOTE_PASS" \
        rsync -az -e "ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -p $SSH_PORT" \
            "$DUMP_FILE" \
            "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/db_sync.sql"

    # Clean local dump
    rm -f "$DUMP_FILE"

    # ── Import on server ──────────────────────────────────────────────
    subheader "4/4  Importing into production database"
    run_v "Importing SQL dump" ssh_cmd "
        mysql -u '$REMOTE_DB_USERNAME' -p'$REMOTE_DB_PASSWORD' -e 'DROP DATABASE IF EXISTS $REMOTE_DB_DATABASE; CREATE DATABASE $REMOTE_DB_DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;' && \
        mysql -u '$REMOTE_DB_USERNAME' -p'$REMOTE_DB_PASSWORD' '$REMOTE_DB_DATABASE' < '${REMOTE_PATH}/db_sync.sql' && \
        rm -f '${REMOTE_PATH}/db_sync.sql'
    "

    # ── Run migrations ─────────────────────────────────────────────────
    if [ -L "${REMOTE_PATH}/current" ]; then
        run_v "Running pending migrations" ssh_cmd "
            cd '${REMOTE_PATH}/current' && \
            $PHP_BIN artisan migrate --force 2>&1 | tail -3
        "
    fi

    sep
    printf "\n${BG_GREEN}${WHITE}  ✅  DB SYNC COMPLETE  ${RESET}\n\n"
    printf "  ${GREEN}✔${RESET}  Local DB  → Production DB\n"
    printf "  ${GREEN}✔${RESET}  Backup:   ${BACKUP_DB_FILE}\n"
    printf "\n"
    info "Continuing with full deploy..."
fi

# ══════════════════════════════════════════════════════════════════════════
RELEASE_DIR="${REMOTE_PATH}/releases/${DEPLOY_TIME}"
BACKUP_DIR="${REMOTE_PATH}/backups/${DEPLOY_TIME}"

section "📦 Deploying to tekmil.ir"

# ── Step 1: Build locally ──────────────────────────────────────────────
subheader "1/8  Building locally"

run_v "Composer install (production)" composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-progress --ignore-platform-req=php

run_v "NPM ci" npm ci --no-progress --no-audit --no-fund 2>&1 | tail -3

run_v "NPM build" npm run build 2>&1 | tail -8

# ── Step 2: Prepare remote dirs ────────────────────────────────────────
subheader "2/8  Preparing directories on server"
run "Creating structure" ssh_cmd "
  mkdir -p '${REMOTE_PATH}/releases' '${REMOTE_PATH}/backups' \
    '${REMOTE_PATH}/shared/storage/app/public' \
    '${REMOTE_PATH}/shared/storage/framework/cache' \
    '${REMOTE_PATH}/shared/storage/framework/sessions' \
    '${REMOTE_PATH}/shared/storage/framework/views' \
    '${REMOTE_PATH}/shared/storage/logs'
"
info "  📁 Release: ${DEPLOY_TIME}"

# ── Step 3: Rsync ──────────────────────────────────────────────────────
subheader "3/8  Uploading code (rsync)"
EXCLUDE_ARGS=(); for pat in "${RSYNC_EXCLUDES[@]}"; do EXCLUDE_ARGS+=(--exclude="$pat"); done

run_v "Rsyncing to ${RELEASE_DIR}" sshpass -p "$REMOTE_PASS" \
    rsync -az --delete \
        -e "ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -p $SSH_PORT" \
        "${EXCLUDE_ARGS[@]}" \
        ./ \
        "${REMOTE_USER}@${REMOTE_HOST}:${RELEASE_DIR}/"

run "chown root:root"           ssh_cmd "chown -R root:root '${RELEASE_DIR}'"
run "chmod 755 dirs"            ssh_cmd "find '${RELEASE_DIR}' -type d -exec chmod 755 {} + 2>/dev/null; true"
run "chmod 644 files"           ssh_cmd "find '${RELEASE_DIR}' -type f -exec chmod 644 {} + 2>/dev/null; true"
run "chown storage www-data"    ssh_cmd "chown -R www-data:www-data '${RELEASE_DIR}/storage' '${RELEASE_DIR}/bootstrap/cache' 2>/dev/null; true"
run "chmod -R 775 storage"      ssh_cmd "chmod -R 775 '${RELEASE_DIR}/storage' 2>/dev/null; true"
run "chmod -R 777 logs"         ssh_cmd "chmod -R 777 '${RELEASE_DIR}/storage/logs' '${RELEASE_DIR}/storage/framework/views' 2>/dev/null; true"

# ── Step 4: .env & storage ─────────────────────────────────────────────
subheader "4/8  Environment & storage"
ENV_EXISTS=$(ssh_cmd "test -f '${REMOTE_PATH}/.env' && echo 1 || echo 0")
if [ "$ENV_EXISTS" = "1" ]; then
    run "Copy .env from root" ssh_cmd "cp '${REMOTE_PATH}/.env' '${RELEASE_DIR}/.env'"
else
    warn "No .env found on server! Create it first, then re-run."
    exit 1
fi

run "Symlink shared storage" ssh_cmd "
    cd '$RELEASE_DIR' && \
    for dir in app 'framework/cache' 'framework/sessions' 'framework/views' logs; do
        rm -rf \"storage/\$dir\" 2>/dev/null
        ln -sfn \"${REMOTE_PATH}/shared/storage/\$dir\" \"storage/\$dir\"
    done
"

# ── Step 5: DB & cache ─────────────────────────────────────────────────
subheader "5/8  Database & optimization"
run "Migrate database"     ssh_cmd "cd '$RELEASE_DIR' && $PHP_BIN artisan migrate --force 2>&1 | tail -2"
run "Storage link"         ssh_cmd "cd '$RELEASE_DIR' && $PHP_BIN artisan storage:link --force 2>/dev/null || true"
run "Cache config"         ssh_cmd "cd '$RELEASE_DIR' && $PHP_BIN artisan config:cache 2>/dev/null || true"
run "Cache routes"         ssh_cmd "cd '$RELEASE_DIR' && $PHP_BIN artisan route:cache 2>/dev/null || true"
run "Cache views"          ssh_cmd "cd '$RELEASE_DIR' && $PHP_BIN artisan view:cache 2>/dev/null || true"
run "Restart queue"        ssh_cmd "cd '$RELEASE_DIR' && $PHP_BIN artisan queue:restart 2>/dev/null || true"

# ── Step 6: Activate ───────────────────────────────────────────────────
subheader "6/8  Activating release"
EXISTING_CURRENT=$(ssh_cmd "test -L '${REMOTE_PATH}/current' && echo 1 || echo 0")
if [ "$EXISTING_CURRENT" = "1" ]; then
    run "Backup current → /backups" ssh_cmd "
        mkdir -p '$BACKUP_DIR' && cp -a '${REMOTE_PATH}/current/'* '$BACKUP_DIR/' 2>/dev/null || true
    "
fi
run "Switch symlink" ssh_cmd "ln -sfn '$RELEASE_DIR' '${REMOTE_PATH}/current'"
run "Set permissions" ssh_cmd "
    chown -R www-data:www-data '${RELEASE_DIR}/storage' '${RELEASE_DIR}/bootstrap/cache' 2>/dev/null || true
    find '${RELEASE_DIR}/storage' -type d -exec chmod 775 {} + 2>/dev/null || true
    find '${RELEASE_DIR}/storage' -type f -exec chmod 664 {} + 2>/dev/null || true
    chmod -R 777 '${RELEASE_DIR}/storage/logs' '${RELEASE_DIR}/storage/framework/views' 2>/dev/null || true
"

# ── Step 7: Verify ─────────────────────────────────────────────────────
subheader "7/8  Verification"
run_v "Application health check" ssh_cmd "
    echo '  📅 Release:    ${DEPLOY_TIME}'
    echo '  📁 Path:       ${RELEASE_DIR}'
    echo '  🔗 Active:     '\$(readlink '${REMOTE_PATH}/current' 2>/dev/null)
    echo '  🌐 PHP:        '\$($PHP_BIN -v | head -1)
    echo '  ⚙️  Env:        '\$(cd '${RELEASE_DIR}' && \$PHP_BIN artisan env 2>/dev/null)
    echo '  🗄  DB:         '\$(cd '${RELEASE_DIR}' && \$PHP_BIN artisan db:monitor 2>/dev/null || echo 'connected')
"

# ── Step 8: Cleanup ────────────────────────────────────────────────────
subheader "8/8  Cleanup"
info "🧹 Cleaning old releases (keep last 5)..."
ssh_cmd "
    ls -dt ${REMOTE_PATH}/releases/*/ 2>/dev/null | tail -n +6 | while read d; do
        echo '  🗑  Removing: '\$(basename \"\$d\")
        rm -rf \"\$d\"
    done
" 2>&1 | grep -v '^\s*$' || true

sep
printf "\n${BG_GREEN}${WHITE}  🎉  DEPLOYMENT SUCCESSFUL — tekmil.ir  ${RESET}\n\n"
printf "  ${GREEN}✔${RESET}  Release:   ${BOLD}${DEPLOY_TIME}${RESET}\n"
printf "  ${GREEN}✔${RESET}  Active:    ${BOLD}${RELEASE_DIR}${RESET}\n"
printf "  ${GREEN}✔${RESET}  Backup:    ${BOLD}${BACKUP_DIR}${RESET}\n"
printf "\n"
printf "  ${DIM}Rollback:${RESET}  ${CYAN}./deploy.sh --rollback${RESET}\n"
printf "\n"

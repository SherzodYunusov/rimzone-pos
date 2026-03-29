#!/bin/bash

# ══════════════════════════════════════════════════════════
#  RIMzone — Server Deploy Script
#  Ishlatish: bash deploy.sh
# ══════════════════════════════════════════════════════════

set -e  # Xato bo'lsa to'xta

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

info()    { echo -e "${BLUE}[INFO]${NC} $1"; }
success() { echo -e "${GREEN}[OK]${NC}   $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC} $1"; }
error()   { echo -e "${RED}[ERR]${NC}  $1"; exit 1; }

echo ""
echo "══════════════════════════════════════"
echo "   RIMzone Deploy Script"
echo "══════════════════════════════════════"
echo ""

# ── 1. PHP versiyasini topish ──────────────────────────────
info "PHP tekshirilmoqda..."
if command -v php8.3 &>/dev/null; then
    PHP="php8.3"
elif command -v php8.2 &>/dev/null; then
    PHP="php8.2"
elif command -v php &>/dev/null; then
    PHP="php"
else
    error "PHP topilmadi!"
fi
success "PHP: $($PHP -r 'echo PHP_VERSION;')"

# ── 2. .env fayl ──────────────────────────────────────────
info ".env fayl tekshirilmoqda..."
if [ ! -f ".env" ]; then
    warn ".env topilmadi — .env.example dan ko'chirilmoqda..."
    cp .env.example .env

    # APP_KEY generate
    $PHP artisan key:generate --force
    success "APP_KEY yaratildi"

    # SECRET_KEYWORD so'rash
    echo ""
    echo -e "${YELLOW}SECRET_KEYWORD ni kiriting (parol o'zgartirish uchun maxfiy so'z):${NC}"
    read -r SECRET_WORD
    sed -i "s|SECRET_KEYWORD=|SECRET_KEYWORD=${SECRET_WORD}|" .env
    success ".env sozlandi"
else
    # APP_KEY bo'shmi?
    APP_KEY_VAL=$(grep "^APP_KEY=" .env | cut -d'=' -f2)
    if [ -z "$APP_KEY_VAL" ]; then
        warn "APP_KEY bo'sh — generate qilinmoqda..."
        $PHP artisan key:generate --force
        success "APP_KEY yaratildi"
    else
        success ".env mavjud"
    fi

    # SECRET_KEYWORD bo'shmi?
    SECRET_VAL=$(grep "^SECRET_KEYWORD=" .env | cut -d'=' -f2)
    if [ -z "$SECRET_VAL" ]; then
        warn "SECRET_KEYWORD bo'sh!"
        echo -e "${YELLOW}SECRET_KEYWORD ni kiriting:${NC}"
        read -r SECRET_WORD
        sed -i "s|SECRET_KEYWORD=.*|SECRET_KEYWORD=${SECRET_WORD}|" .env
        success "SECRET_KEYWORD sozlandi"
    fi
fi

# ── 3. Production env ─────────────────────────────────────
sed -i "s|APP_ENV=local|APP_ENV=production|" .env
sed -i "s|APP_DEBUG=true|APP_DEBUG=false|" .env
success "APP_ENV=production, APP_DEBUG=false"

# ── 4. Composer ───────────────────────────────────────────
info "Composer dependencies o'rnatilmoqda..."
if command -v composer &>/dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction 2>/dev/null
    success "Composer tayyor"
else
    warn "Composer topilmadi — o'tkazib yuborildi"
fi

# ── 5. SQLite database fayl ───────────────────────────────
info "Database tekshirilmoqda..."
DB_PATH=$(grep "^DB_DATABASE=" .env | cut -d'=' -f2)
if [ -z "$DB_PATH" ] || [ "$DB_PATH" = "laravel" ]; then
    DB_FILE="database/database.sqlite"
else
    DB_FILE="$DB_PATH"
fi

if [ ! -f "$DB_FILE" ]; then
    warn "SQLite fayl topilmadi — yaratilmoqda: $DB_FILE"
    touch "$DB_FILE"
    success "SQLite fayl yaratildi"
else
    success "SQLite fayl mavjud"
fi

# ── 6. Migratsiyalar ──────────────────────────────────────
info "Migratsiyalar ishga tushirilmoqda..."
$PHP artisan migrate --force 2>&1 | tail -3
success "Migratsiyalar bajarildi"

# ── 7. Admin user ─────────────────────────────────────────
info "Admin foydalanuvchi tekshirilmoqda..."
ADMIN_EXISTS=$($PHP artisan tinker --execute="echo App\Models\User::where('email','Rimzone.monipos@gmail.com')->exists() ? '1' : '0';" 2>/dev/null | tr -d '[:space:]')
if [ "$ADMIN_EXISTS" != "1" ]; then
    warn "Admin topilmadi — yaratilmoqda..."
    $PHP artisan db:seed --class=AdminSeeder --force
    success "Admin yaratildi: Rimzone.monipos@gmail.com"
else
    success "Admin mavjud"
fi

# ── 8. Storage permissions ────────────────────────────────
info "Permissions sozlanmoqda..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
if [ -f "database/database.sqlite" ]; then
    chmod 664 database/database.sqlite 2>/dev/null || true
fi
success "Permissions to'g'rilandi"

# ── 9. Cache ──────────────────────────────────────────────
info "Cache tozalanmoqda va qayta yig'ilmoqda..."
$PHP artisan config:clear  2>/dev/null
$PHP artisan route:clear   2>/dev/null
$PHP artisan view:clear    2>/dev/null
$PHP artisan config:cache  2>/dev/null
$PHP artisan route:cache   2>/dev/null
success "Cache tayyor"

# ── Tayyor ────────────────────────────────────────────────
echo ""
echo -e "${GREEN}══════════════════════════════════════${NC}"
echo -e "${GREEN}   Deploy muvaffaqiyatli yakunlandi!${NC}"
echo -e "${GREEN}══════════════════════════════════════${NC}"
echo ""
echo -e "  Login:  ${YELLOW}Rimzone.monipos@gmail.com${NC}"
echo -e "  Parol:  ${YELLOW}N976644224${NC}"
echo ""

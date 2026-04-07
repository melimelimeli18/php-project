#!/bin/bash

# ─────────────────────────────────────────────
#  NeonDB Connection Checker — Laravel Project
# ─────────────────────────────────────────────

# Load .env from Laravel project root
ENV_FILE=".env"

if [ ! -f "$ENV_FILE" ]; then
  echo "❌ .env file not found. Run this script from your Laravel project root."
  exit 1
fi

# Parse .env values
get_env() {
  grep -E "^${1}=" "$ENV_FILE" | cut -d '=' -f2- | tr -d '"' | tr -d "'"
}

DB_CONNECTION=$(get_env "DB_CONNECTION")
DB_HOST=$(get_env "DB_HOST")
DB_PORT=$(get_env "DB_PORT")
DB_DATABASE=$(get_env "DB_DATABASE")
DB_USERNAME=$(get_env "DB_USERNAME")
DB_PASSWORD=$(get_env "DB_PASSWORD")

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  🔍 Laravel NeonDB Connection Checker"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "  Driver   : ${DB_CONNECTION:-not set}"
echo "  Host     : ${DB_HOST:-not set}"
echo "  Port     : ${DB_PORT:-not set}"
echo "  Database : ${DB_DATABASE:-not set}"
echo "  Username : ${DB_USERNAME:-not set}"
echo "  Password : ${DB_PASSWORD:+********}"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# ── Method 1: Laravel artisan (most accurate) ──
echo "▶ Method 1: Laravel artisan (php artisan db:show)"
echo ""
if command -v php &>/dev/null && [ -f "artisan" ]; then
  php artisan db:show 2>&1
  ARTISAN_EXIT=$?
  echo ""
  if [ $ARTISAN_EXIT -eq 0 ]; then
    echo "  ✅ Artisan db:show — Connected!"
  else
    echo "  ❌ Artisan db:show — Failed"
  fi
else
  echo "  ⚠️  Skipped — php or artisan not found in current directory"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# ── Method 2: Laravel tinker one-liner ──
echo "▶ Method 2: Laravel tinker (DB::connection check)"
echo ""
if command -v php &>/dev/null && [ -f "artisan" ]; then
  php artisan tinker --execute="
    try {
        \DB::connection()->getPdo();
        echo '  ✅ Connected to: ' . \DB::connection()->getDatabaseName() . PHP_EOL;
    } catch (\Exception \$e) {
        echo '  ❌ Connection failed: ' . \$e->getMessage() . PHP_EOL;
        exit(1);
    }
  " 2>&1
else
  echo "  ⚠️  Skipped — php or artisan not found in current directory"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# ── Method 3: Direct psql fallback ──
echo "▶ Method 3: Direct psql (fallback)"
echo ""
if command -v psql &>/dev/null; then
  export PGPASSWORD="$DB_PASSWORD"
  RESULT=$(psql \
    -h "$DB_HOST" \
    -p "${DB_PORT:-5432}" \
    -d "$DB_DATABASE" \
    -U "$DB_USERNAME" \
    --set=sslmode=require \
    -c "SELECT NOW() AS connected_at, current_database() AS database, current_user AS user;" \
    2>&1)
  PSQL_EXIT=$?
  echo "$RESULT"
  echo ""
  if [ $PSQL_EXIT -eq 0 ]; then
    echo "  ✅ psql — Connected!"
  else
    echo "  ❌ psql — Failed"
  fi
else
  echo "  ⚠️  Skipped — psql not installed"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "  💡 Tips for NeonDB:"
echo "     - DB_CONNECTION should be: pgsql"
echo "     - DB_HOST format: ep-xxx-xxx.region.aws.neon.tech"
echo "     - DB_PORT: 5432"
echo "     - Add in config/database.php under pgsql:"
echo "       'sslmode' => 'require'"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
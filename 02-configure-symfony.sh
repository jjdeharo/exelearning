#!/bin/sh
#
# Symfony configuration script
#
set -eo pipefail

# Colors for messages
GREEN='\033[0;32m'
NC='\033[0m' # No Color

# Function to check the availability of a database
check_db_availability() {
    local db_host="$1"
    local db_port="$2"
    echo -e "${GREEN}Waiting for $db_host:$db_port to be ready...${NC}"
    while ! nc -w 1 "$db_host" "$db_port" > /dev/null 2>&1; do
        # Show some progress
        echo -n '.'
        sleep 1
    done
    echo -e "${GREEN}\n\nGreat, $db_host is ready!${NC}"
}

# If DB_HOST is set, check the availability of the database
if [ -n "$DB_HOST" ]; then
    check_db_availability "$DB_HOST" "$DB_PORT"
fi

# Execute pre-configuration commands if set
if [ -n "$PRE_CONFIGURE_COMMANDS" ]; then
    echo "Executing pre-configure commands..."
    eval "$PRE_CONFIGURE_COMMANDS"
fi

# If BASE_PATH is defined, generates the NGINX subdir.conf
if [ -n "$BASE_PATH" ] && [ -f "/etc/nginx/server-conf.d/subdir.conf.template" ]; then
    echo "Replacing subdir.conf.template with env var: $BASE_PATH"
    envsubst '\$BASE_PATH' < /etc/nginx/server-conf.d/subdir.conf.template > /etc/nginx/server-conf.d/subdir.conf
fi

# Update the database schema
echo -e "${GREEN}Creating database tables${NC}"
php bin/console doctrine:schema:update --force

echo -e "${GREEN}Running migrations{NC}"
php bin/console doctrine:migrations:migrate --no-interaction || echo "No migrations found, skipping..."

# Create test user using environment variables
echo -e "${GREEN}Creating test user${NC}"
php bin/console app:create-user "${TEST_USER_EMAIL}" "${TEST_USER_PASSWORD}" "${TEST_USER_USERNAME}" --no-fail

# Clear cache and configure other Symfony settings
echo -e "${GREEN}Configuring other Symfony settings${NC}"
php bin/console cache:clear
php bin/console assets:install public

# Execute post-configuration commands if set
if [ -n "$POST_CONFIGURE_COMMANDS" ]; then
    echo "Executing post-configure commands..."
    eval "$POST_CONFIGURE_COMMANDS"
fi

echo -e "${GREEN}Symfony has been successfully configured.${NC}"

# Always return 0 (success)
exit 0

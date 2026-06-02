#!/usr/bin/env bash
set -e

echo "=============================="
echo " ZaminTaWeb - Setup Script"
echo "=============================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check prerequisites
check_command() {
    if ! command -v "$1" &> /dev/null; then
        echo -e "${RED}Error: $1 is not installed.${NC}"
        exit 1
    fi
}

echo -e "${YELLOW}Checking prerequisites...${NC}"
check_command php
check_command composer
check_command npm

# Copy .env if not exists
if [ ! -f .env ]; then
    echo -e "${YELLOW}Creating .env from .env.example...${NC}"
    cp .env.example .env
    echo -e "${GREEN}.env created. Please edit DB credentials if needed.${NC}"
else
    echo -e "${GREEN}.env already exists.${NC}"
fi

# Install PHP dependencies
echo -e "${YELLOW}Installing PHP dependencies...${NC}"
composer install --no-interaction

# Generate APP_KEY
if ! grep -q "APP_KEY=base64" .env; then
    echo -e "${YELLOW}Generating APP_KEY...${NC}"
    php artisan key:generate
fi

# Reminder to create database
DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
echo -e "${YELLOW}Make sure the '$DB_DATABASE' database exists.${NC}"
echo -e "${YELLOW}  Create it: mysql -u root -e 'CREATE DATABASE $DB_DATABASE;'${NC}"

# Run migrations
echo -e "${YELLOW}Running migrations...${NC}"
php artisan migrate --force

# Run seeders
echo -e "${YELLOW}Seeding database...${NC}"
php artisan db:seed --force

# Install Node dependencies
echo -e "${YELLOW}Installing Node dependencies...${NC}"
npm install

# Build frontend
echo -e "${YELLOW}Building frontend...${NC}"
npm run build

echo -e "${GREEN}"
echo "=============================="
echo " Setup Complete!"
echo "=============================="
echo ""
echo " Default login credentials:"
echo "   Email:    admin@zaminta.com"
echo "   Password: password"
echo ""
echo " Run the dev server:"
echo "   composer dev"
echo "=============================="
echo -e "${NC}"

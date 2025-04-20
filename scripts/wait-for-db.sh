#!/bin/bash

# Скрипт для ожидания доступности базы данных
# Использование: wait-for-db.sh [хост] [пользователь] [пароль] [база_данных] [таймаут]

HOST=${1:-"db"}
USER=${2:-"sovpadem_user"}
PASSWORD=${3:-"sovpadem_password"}
DATABASE=${4:-"sovpadem"}
TIMEOUT=${5:-60}

echo "Waiting for MySQL database at $HOST..."
start_time=$(date +%s)
end_time=$((start_time + TIMEOUT))

while [ $(date +%s) -lt $end_time ]; do
    if mysql -h"$HOST" -u"$USER" -p"$PASSWORD" -e "SELECT 1" "$DATABASE" &> /dev/null; then
        echo "Database is available!"
        exit 0
    fi
    echo "Database is not available yet, waiting..."
    sleep 2
done

echo "Timeout reached. Database is not available."
exit 1
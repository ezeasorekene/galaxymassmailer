#!/bin/bash

echo "Setting recommended file permissions..."

# Apply correct folder and file ownership
find . -type d -exec chmod 0755 {} \;
find . -type f -exec chmod 0644 {} \;
find . -type d -exec chown ${USER}:$USER {} \;
find . -type f -exec chown ${USER}:$USER {} \;
chmod +x scripts/*.sh
chmod 755 storage
chmod 777 storage/sessions
chmod 777 storage/cookies
chmod 755 extras

if [ -d "extras/mysql" ]; then
    chmod 755 extras/mysql
fi

echo "Compleleted file permissions setup..."

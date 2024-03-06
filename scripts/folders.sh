#!/bin/bash

echo "Creating folders..."

# Create storage directory
if [ ! -d "storage" ]; then
    mkdir -m 755 -p storage
fi

# Create upload directory
if [ ! -d "public/uploads" ]; then
    mkdir -m 755 -p public/uploads
fi

# Create log directory
if [ ! -d "storage/logs" ]; then
    mkdir -m 755 -p storage/logs
fi

# Create session directory
if [ ! -d "storage/sessions" ]; then
    mkdir -m 755 -p storage/sessions
fi

# Create cookie directory
if [ ! -d "storage/cookies" ]; then
    mkdir -m 755 -p storage/cookies
fi

# Create other important directory
if [ ! -d "extras" ]; then
    mkdir -m 755 extras
fi

echo "Folders created successfully"

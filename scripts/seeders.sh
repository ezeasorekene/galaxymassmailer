#!/bin/bash


# Perform database migrations
vendor/bin/phinx seed:run -s UserSeeder -e production
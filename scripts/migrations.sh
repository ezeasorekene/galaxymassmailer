#!/bin/bash


# Perform database migrations
vendor/bin/phinx migrate -e production


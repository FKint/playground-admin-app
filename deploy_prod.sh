#!/bin/bash
docker-compose -f docker/docker-compose.yml.production --project-name playgroundadminapp --project-dir . pull
docker-compose -f docker/docker-compose.yml.production --project-name playgroundadminapp --project-dir . up -d
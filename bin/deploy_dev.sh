#!/bin/bash
set -e  # Exit immediately if a command exits with a non-zero status
  

docker compose -f docker-compose.yml up  -d --build
# Deployment completed
echo "Deployment completed successfully at $(date)" 
docker compose logs -f  app 
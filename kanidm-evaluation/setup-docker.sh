#!/usr/bin/env sh

# Check if container exists and remove it
if docker ps -a --format '{{.Names}}' | grep -q '^kanidmd$'; then
    echo "Stopping and removing existing kanidmd container..."
    docker stop kanidmd
    docker rm kanidmd
fi

# Check if volume exists and remove it
if docker volume ls --format '{{.Name}}' | grep -q '^kanidm$'; then
    echo "Removing existing kanidm volume..."
    docker volume rm kanidm
fi

docker volume create kanidm
docker create --name kanidmd \
  -p '8443:8443' \
  -p '3636:3636' \
  -v kanidmd:/data \
  docker.io/kanidm/server:latest

docker cp server.toml kanidmd:/data/server.toml

docker run --rm -i -t -v kanidmd:/data \
  docker.io/kanidm/server:latest \
  kanidmd cert-generate

docker start kanidmd
docker exec -i -t kanidmd \
  kanidmd recover-account admin

docker exec -i -t kanidmd \
  kanidmd recover-account idm_admin


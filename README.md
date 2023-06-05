# prometheus-solaxcloud-exporter-php
Prometheus exporter for SolaxCloud inverter data.

## Usage

### Build the container

`docker build . -t prometheus-solaxcloud-exporter-php`

### Running the container

Make sure to properly set the 2 environment variables in docker-compose.yml

`docker compose up -d`

### Check if it works

`curl localhost:8092/metrics`
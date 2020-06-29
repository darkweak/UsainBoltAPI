#!/bin/sh

services=("admin:admin." "api:api." "client:" "mercure:mercure.") # Define your services keys following this format: "{container key}:{sub DNS}". To define root DNS write nothing after the colon
text="version: '3.4'
services:"

for k in "${services[@]}" ; do
    key=${k%%:*}
    value=${k#*:}
    text+="
  $key:
    labels:
      - traefik.http.routers.$key-\${RANDOM_UNIQUE_KEY}.entrypoints=web-secure
      - traefik.http.routers.$key-\${RANDOM_UNIQUE_KEY}.rule=Host(\`${value}\${DOMAIN_NAME}\`)
      - traefik.http.routers.$key-\${RANDOM_UNIQUE_KEY}.tls=true
      - traefik.http.routers.$key-\${RANDOM_UNIQUE_KEY}.tls.domains[0].main=\${DOMAIN_NAME}
      - traefik.http.routers.$key-\${RANDOM_UNIQUE_KEY}.tls.domains[0].sans=*.\${DOMAIN_NAME}
      - traefik.http.routers.$key-\${RANDOM_UNIQUE_KEY}.tls.certresolver=sample
"
done

echo "$text" > ./docker-compose.override.yml

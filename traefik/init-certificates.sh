#!/bin/sh

current_directory="`dirname "$0"`"
domain_name=$(grep DOMAIN_NAME ../.env | cut -d '=' -f 2-)
certificates_directory="${current_directory}/../certs"

openssl req \
       -newkey rsa:2048 -nodes -keyout ${certificates_directory}/localhost.key \
       -x509 -days 365 -out ${certificates_directory}/localhost.crt \
       -subj "/C=FR/ST=France/L=Lille/O=Darkweak Company/CN=*.domain.com"

certificate=$(echo "$(cat ${certificates_directory}/localhost.crt)" | base64)
key=$(echo "$(cat ${certificates_directory}/localhost.key)" | base64)

echo "{
  \"sample\": {
    \"Certificates\": [
      {
        \"domain\": {
          \"main\": "\"${domain_name}\"",
          \"sans\": [
            "\"*.${domain_name}\""
          ]
        },
        \"certificate\": "\"${certificate}\"",
        \"key\": "\"${key}\"",
        \"Store\": "\"default\""
      }
    ]
  }
}" > acme.json

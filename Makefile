DC=docker-compose
DC_DOWN=$(DC) down --remove-orphans
DC_UP=$(DC) up -d --remove-orphans
DC_BUILD=$(DC) up -d --remove-orphans --build --force-recreate

down: ## Stop all containers
	$(DC_DOWN)
	cd traefik && $(DC_DOWN)

init: ## Init app
	bash ./init-dc.sh
	cd traefik && ./init-certificates.sh && chmod 600 acme.json
	$(DC_BUILD)
	cd traefik && $(DC_BUILD)

build: ## Build containers
	$(DC_BUILD)
	cd traefik && $(DC_BUILD)

start: ## Start all containers
	$(DC_UP)
	cd traefik && $(DC_UP)

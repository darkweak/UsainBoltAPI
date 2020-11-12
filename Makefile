DC=docker-compose
DC_DOWN=$(DC) down --remove-orphans
DC_EXEC=$(DC) exec
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

update: ## Update all containers
	$(DC_BUILD)
	$(MAKE) start
	$(DC_EXEC) php composer update
	cd client && yarn && yarn upgrade
	cd admin && yarn && yarn upgrade

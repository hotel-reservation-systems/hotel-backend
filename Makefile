APP_NAME ?= hotel-backend
APP_PLATFORM ?= NodeJS
BUILD_ID ?= $(shell git rev-parse --short HEAD)
SSH_HOST ?= mars.cs.qc.cuny.edu
SSH_USERNAME ?= specify_in_travis_repo_settings
SSH_PASSPHRASE ?= specify_in_travis_repo_settings

ssh-ok:
	sudo sed -i "20i\ForwardAgent yes" /etc/ssh/ssh_config && \
	sudo sed -i "35i\StrictHostKeyChecking no" /etc/ssh/ssh_config

get-code:
	@sshpass -p ${SSH_PASS} ssh ${SSH_USER}@${SSH_HOST} "cd public_html/cs370/project2/hotel-backend; git pull"

update-backend:
	sudo locale-gen en_US.UTF-8
	@sshpass -p ${SSH_PASS} ssh ${SSH_USER}@${SSH_HOST} "rm -rf public_html/cs370/project2/backend"
	@sshpass -p ${SSH_PASS} ssh ${SSH_USER}@${SSH_HOST} "mkdir public_html/cs370/project2/backend"
	@sshpass -p ${SSH_PASS} ssh ${SSH_USER}@${SSH_HOST} "cp -r public_html/cs370/project2/hotel-backend/* public_html/cs370/project2/backend/"

repair-permissions:
	@sshpass -p ${SSH_PASS} ssh ${SSH_USER}@${SSH_HOST} "chmod -R 755 public_html/cs370/project2/backend"
	@sshpass -p ${SSH_PASS} ssh ${SSH_USER}@${SSH_HOST} "chmod 777 public_html/cs370/project2/backend"
	@sshpass -p ${SSH_PASS} ssh ${SSH_USER}@${SSH_HOST} "chmod 777 public_html/cs370/project2/backend/project2.sqlite3"

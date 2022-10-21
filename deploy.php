<?php

/** create a file the deploy */

namespace Deployer;

require 'recipe/common.php';

const APP = 'SITE-MODE';
const REPOSITORY = 'git@github.com:HandersonSilva/site-mode.git';
const NUMBER_RELEASE = 2;
const AMBIENTE = 'production';
const IMAGE_NAME = 'site-mode';
const VERSION = '1.0.3';
const EXTERNAL_PORT = '8080';
const PORT = '80';
const REPLICAS = 1;

inventory('deployment/hosts.yml');

set('application', APP);

set('repository', REPOSITORY);
set('keep_releases', NUMBER_RELEASE);
set('default_stage', AMBIENTE);
set('default_timeout', 1200);

task('docker-build', function () {
	run('cd {{release_path}} && docker build -t handersonsilva/switch-' . IMAGE_NAME . ':' . VERSION . ' -f ' . AMBIENTE . '.dockerfile .');
})->desc('Build image');

task('docker-push', function () {
	run('docker push handersonsilva/switch-' . IMAGE_NAME . ':' . VERSION);
})->desc('Push image');

task('remove-service', function () {
	try {
		run("docker service rm " . IMAGE_NAME);
	} catch (\Exception $e) {
		write($e->getMessage());
	}
	return true;
})->desc('Remove service');

task('create-service', function () {
	run("docker service create --name " . IMAGE_NAME . " --with-registry-auth --replicas " . REPLICAS . " -dt -p " . EXTERNAL_PORT . ":" . PORT . " handersonsilva/switch-" . IMAGE_NAME . ":" . VERSION);
})->desc('Create service');

task('deploy', [
	'deploy:info',
	'deploy:prepare',
	'deploy:release',
	'deploy:update_code',
	'copy-config',
	'docker-build',
	'docker-push',
	'remove-service',
	'create-service',
	'deploy:shared',
	'deploy:writable',
	'deploy:symlink',
	'cleanup'
])->desc('Deploy project');

after('deploy:failed', 'deploy:unlock');
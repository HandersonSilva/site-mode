<?php
/** create a file the deploy */
namespace Deployer;

require 'recipe/common.php';

$imageName = 'landpage';
$version = '1.0.3';
$port = '8080';
$replicas = 1;

inventory('deployment/hosts.yml');

set('default_stage', 'production');

set('application', 'SITE');

set('repository', 'git@github.com:HandersonSilva/site-mode.git');
set('keep_releases', 2);

task('docker-build', function () use ($imageName, $version) {
    run('cd {{release_path}} && docker build -t handersonsilva/switch-'.$imageName.':'.$version.' .');
  })->desc('Build image');

task('docker-push', function () use ($imageName, $version) {
    run('docker push handersonsilva/switch-'.$imageName.':'.$version);
  })->desc('Push image');

task('docker-clear-image', function () {
    run('docker image prune -a -f');
  })->desc('Push image');

task('remove-service', function () use ($imageName) {
    try{
        run("docker service rm {$imageName}");
    }catch(\Exception $e){
        write($e->getMessage());
    }
    return true;
  })->desc('Remove service');

 task('create-service', function () use ($imageName, $version, $port, $replicas) {
    run("docker service create --name {$imageName} --replicas {$replicas} -dt -p {$port}:80 handersonsilva/switch-{$imageName}:{$version}");
  })->desc('Create service');
 

task('deploy',[
    'deploy:info',
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'docker-build',
    'docker-push',
    'docker-clear-image',
    'remove-service',
    'create-service',
    'deploy:shared',
    'deploy:writable',
    'deploy:symlink',
    'cleanup'    
])->desc('Deploy project');

after('deploy:failed', 'deploy:unlock');
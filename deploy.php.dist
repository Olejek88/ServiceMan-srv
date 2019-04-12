<?php
namespace Deployer;

require 'recipe/yii2-app-advanced.php';

// Project name
set('application', 'tt-dev');

// Project repository
set('repository', 'https://bitbucket.org/4938x8_tes-team/tt_backend.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys
add('shared_files', [
    'api/config/main-local.php',
    'api/config/params-local.php',
]);
add('shared_dirs', [
    'api/runtime',
]);

// Writable dirs by web server 
add('writable_dirs', []);


// Hosts

host('tt-dev.local')
//    ->user('koputo')
//    ->set('deploy_path', '~/{{application}}');
    ->set('deploy_path', '/media/www/api.tt.tehnosber.ru');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

task('changeright', function() {
    run('cd {{deploy_path}} && sudo chown -R -h -L www-data\: html && sudo chmod -R g+w html');
});

task('run_migration_content', function() {
    run('{{bin/php}} {{release_path}}/yii migrate-content/up --interactive=0');
});

task('run_migration_rbac_init', function() {
    run('{{bin/php}} {{release_path}}/yii migrate/up --interactive=0 --migrationPath=@yii/rbac/migrations');
});

task('run_migration_rbac', function() {
    run('{{bin/php}} {{release_path}}/yii migrate-rbac/up --interactive=0');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

after('success', 'changeright');

after('deploy:run_migrations', 'run_migration_content');
after('deploy:run_migrations', 'run_migration_rbac_init');
after('deploy:run_migrations', 'run_migration_rbac');

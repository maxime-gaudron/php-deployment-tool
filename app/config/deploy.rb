set :stages,                 %w(production)
set :default_stage,          "production"
set :stage_dir,              'app/config/deploy' # needed for Symfony2 only

require 'capistrano/ext/multistage'

set :application,            "php-deployment-tool"

set :repository,             "git@github.com:maxime-gaudron/#{application}.git"
set :scm,                    :git
set :copy_exclude,           [ ".git" ]

set :model_manager,          "doctrine"

set :use_composer,           true
set :update_vendors,         false

set :shared_files,           [ "/app/config/parameters.yml", "app/config/deploy/production.rb", app_path + "/data/data.db3" ]
set :shared_children,        [ app_path + "/logs", web_path + "/uploads", app_path + "/tasks" ]

set :deploy_via,             :remote_cache
set :group_writable,         true

role(:web)                   { domain }
role(:app, :primary => true) { domain }
role(:db, :primary => true)  { domain }

set  :dump_assetic_assets,   true
set  :update_assets_version, true
set  :use_sudo,              false
set  :keep_releases,         1
set  :clear_controllers,     fetch(:clear_controllers, true)

logger.level = Logger::MAX_LEVEL

ssh_options[:forward_agent] = true
default_run_options[:pty]   = false

before "symfony:cache:warmup", "symfony:doctrine:schema:update"

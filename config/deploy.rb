# config valid only for current version of Capistrano
lock "3.6.1"

set :application, "edumax-api"
set :repo_url, "git@gitlab.acensi.fr:edumax/api.git"

set :session_path,           fetch(:var_path) + "/sessions"
set :linked_files,           [fetch(:app_config_path) + "/parameters.yml"]
set :linked_dirs,            [fetch(:log_path), fetch(:session_path)]

# Default value for keep_releases is 5
set :keep_releases, 1

set :composer_install_flags, "--no-dev --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction"

namespace :deploy do
  after :starting, 'composer:install_executable'
end

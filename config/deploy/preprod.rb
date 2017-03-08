# server-based syntax
# ======================
# Defines a single server with a list of roles and multiple properties.
# You can define all roles on a single server, or split them:

server 'sejedu-ew01.msp.fr.clara.net', user: 'per-viascola-adl', roles: %w{app web}

# Configuration
# =============
# You can set any configuration variable like in config/deploy.rb
# These variables are then only loaded and set in this stage.
# For available Capistrano configuration variables see the documentation page.
# http://capistranorb.com/documentation/getting-started/configuration/
# Feel free to add new variables to customise your setup.

set :branch,       'develop'
set :deploy_to,    '/data/www/sites/PER/VIASCOLA/preprod.console.eduplateforme.com/api'
set :tmp_dir,      '/data/www/sites/PER/VIASCOLA/preprod.console.eduplateforme.com'
set :symfony_env,  'preprod'

# Custom SSH Options
# ==================
# You may pass any option but keep in mind that net/ssh understands a
# limited set of options, consult the Net::SSH documentation.
# http://net-ssh.github.io/net-ssh/classes/Net/SSH.html#method-c-start

SSHKit.config.command_map[:php] = "/opt/rh/rh-php56/root/usr/bin/php"
SSHKit.config.command_map[:composer] = "/opt/rh/rh-php56/root/usr/bin/php #{shared_path.join("composer.phar")}"

set :ssh_options, {
  forward_agent: false
}

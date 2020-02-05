# Gernzy Ecommerce 
This is the main repo for Gernzy. For the composer package, see the `server` folder. 

# Docker
A sample docker-compose file is included for convenience (WIP)

# Installation (for development purposes)
- Create a new Laravel project: composer create-project --prefer-dist laravel/laravel shop
- Install gernzy via local path method: https://getcomposer.org/doc/05-repositories.md#path
- Add the service provider to your Laravel app under config/providers.php: `Lab19\Cart\CartServiceProvider::class`
- Create a database for the project
- Adjust your .env file with the correct local credentials
- If you're using docker, add the env file path to docker-compose https://docs.docker.com/compose/environment-variables/#the-env_file-configuration-option
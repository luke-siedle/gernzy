# Gernzy Ecommerce 
This is the main repo for Gernzy. For the composer package, see the `server` folder. 

# Docker
A sample docker-compose file is included for convenience (WIP)

# Installation (for development purposes)
- Create a new Laravel project: composer create-project --prefer-dist laravel/laravel shop
- See "Required composer changes" below
- Add the service provider to your Laravel app under config/providers.php: `Gernzy\Server\GernzyServiceProvider::class`
- Adjust your auto-generated .env file to the database credentials you require
- Create a database for the project
- Adjust your shop/.env file with the correct local credentials
- Quickly migrate and seed your environment: sh scripts/seed-environment.sh

# Installation with Docker
- Create a new Laravel project in a /shop/ directory: composer create-project --prefer-dist laravel/laravel shop
- See "Required composer changes" below
- Add the service provider to your Laravel app under config/providers.php: `Gernzy\Server\GernzyServiceProvider::class`
- Adjust your auto-generated .env file to the database credentials you require
- DB_DATABASE=gernzy DB_USER=gernzy DB_PASSWORD=gernzy docker-compose up -d 
- Quickly migrate and seed your environment: sh scripts/docker-seed-environment.sh

# Required composer changes
- Install gernzy via local path method: https://getcomposer.org/doc/05-repositories.md#path
- View example below:

```
"require": {
        ...
        "gernzy/server": "@dev",
        ...
},
...
"autoload": {
	...
	"psr-4": {
	    "App\\": "app/",
	    "Gernzy\\Server\\": "../server/src"
	},
	...
},
...
"repositories": [
    {
        "type": "path",
        "url": "../server"
    }
]
```


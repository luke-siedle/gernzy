#/bin/bash
docker exec -i gernzy_app php artisan migrate ;
docker exec -i gernzy_app php artisan db:seed --class="Gernzy\Server\Database\Seeds\ProductsSeeder" ;
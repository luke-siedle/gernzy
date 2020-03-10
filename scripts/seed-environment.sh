#/bin/bash
PARENT_DIR="$(dirname `pwd`)"
cd $PARENT_DIR/shop ;
php artisan migrate ;
php artisan db:seed --class="Gernzy\Server\Database\Seeds\ProductsSeeder" ;
php artisan db:seed --class="Gernzy\Server\Database\Seeds\AdminClientSeeder" ;
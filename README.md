#STEP 1: Init the app with docker

Run command: "docker-compose up -d"

Then, install the app with these command

docker-compose run --rm php composer install
docker-compose run --rm php yii init

#STEP 2: config the database

Go to the file config/db.php

Edit the lines as below

'dsn' => 'mysql:host=mysql;dbname=yii2advanced',
'username' => 'yii2advanced',
'password' => 'secret',


#STEP 3: Test the app with dummy generation

Run migration:
docker-compose run --rm php yii migrate

Run dummy data generation:
docker-compose run --rm php yii dummy/generate-data -u=XX -m=YY

XX & YY: integer numbers

E.g: docker-compose run --rm php yii dummy/generate-data -u=2 -m=5

#Step 4: Setup store procedure

Go to PHPMyAdmin at http://127.0.0.1:8080/index.php

Run the query in the file "mysql-store-procedure.txt"

#STEP 4:

Test with the APIs for total records:

E.g: http://127.0.0.1:8000/index.php/messages/total?period_start=2018-06-05&period_end=2020-06-01&period_group_unit=month


#STEP 5: Unit Test

Run command: docker-compose run --rm php vendor/bin/codecept generate:test unit Dummy

#STEP 6: Chart

Go to the page: http://127.0.0.1:8000/site/chart
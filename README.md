
## Glover Maker Checker Assessment 

### Prerequisites

- Follow this [link](https://laravel.com/docs/8.x/installation#getting-started-on-macos) for basic laravel setup with sail
- PHP v8.1

### Start up

To start project, perform the following step in the order

- Clone the repository by running the command
- git clone 'https://github.com/Geoslim/maker-checker.git'
- cd maker-checker
- Run composer install
- Run 'cp .env.example .env'
- Fill your configuration settings in the '.env' file you created above
- Turn on Docker Desktop
- Run './vendor/bin/sail up' to start up the application
- Run 'php artisan key:generate'
- Run 'php artisan migrate --seed'

### Administrators with login information

#### email => viserys@gmail.com
#### password => secret
#### roles => [maker, checker]
________________________________________________________________

#### email => aegon@gmail.com
#### password => secret
#### roles => [checker]

________________________________________________________________

#### email => daemon@gmail.com
#### password => secret
#### roles => [maker]

________________________________________________________________

#### email => rhaegar@gmail.com
#### password => secret
#### roles => [maker]

________________________________________________________________
________________________________________________________________

#### email => jamie@gmail.com
#### password => secret
#### roles => [user]

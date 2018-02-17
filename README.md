# Test Task

## Setup
### Composer
First run `composer install` in command line to install dependencies

### `.env` File
The required config is `DATABASE_URL`. Please fill it with proper data. If you use [homestead](#homestead),
default url should be `mysql://homestead:secret@127.0.0.1:3306/homestead`

## Homestead
This project uses [Laravel Homestead](https://laravel.com/docs/5.5/homestead) box.

### Requirements
- [Vagrant](https://www.vagrantup.com/)
- VirtualBox or another virtual machine provider (Parallels, Hyper-V, VMware)

### Configuring
It's supposed that you have already installed composer packages (including dev ones).
Next you need to run `php vendor/bin/homestead make`. It will create `Homestead.yaml` file 
in project root directory. Edit this file according your needs (see [documentation](https://laravel.com/docs/5.5/homestead#configuring-homestead)).

### Usage
For start configured homestead, simply run `vagrant up` from project root. More information [here](https://laravel.com/docs/5.5/homestead#launching-the-vagrant-box).
For suspend machine you can run `vagrant halt`

### Browser access
In `hosts` file map hostname from the `Homestead.yaml` to IP address from the same file. By default it will be `192.168.10.10 homestead.test`.
After that you can access application via [http://homestead.test](http://homestead.test).
You can change appropriate configs in the `Homestead.yaml` file to change IP address and hostname.

### SSH access
For using virtual machine terminal via ssh, run `vagrant ssh`

## Database
### Setup
For setup database execute migrations by running `bin/console doctrine:migrations:migrate`.

**NOTE**: If you're using homestead run command from virtual machine terminal. 

## Demo data
For setup demo data, you can use fixtures. 
Run `bin/console doctrine:fixtures:load` to upload data to database.

**NOTE**: If you're using homestead run command from virtual machine terminal. 

### Users
Demo data contain 20 users with username `userN` where `N` is sequential number from 1 to 20.
Passwords are same for all users: `pass123`.  

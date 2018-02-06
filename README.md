# Test Task

## Setup
### Composer
First run `composer install` in command line to install dependencies

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

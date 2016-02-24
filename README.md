# connect

## WARNING

Connect is currently under high development.
Also currently the "scp" and the "ssh" commands are working (basically)

## Usage

Make the connect.phar executable and run 

    ./connect.phar command

### Commands
 
#### ssh

#### scp

## Build

### Download Composer
    
Run this in your terminal to get the latest Composer version:

    curl -sS https://getcomposer.org/installer | php

Or if you don't have curl:

    php -r "readfile('https://getcomposer.org/installer');" | php

### Download Box

Run this in your terminal to download a ready-to-use version of Box as a Phar:

     curl -LSs https://box-project.github.io/box2/installer.php | php

Or if you don't have curl:

    php -r "readfile('https://box-project.github.io/box2/installer.php');" | php

### Install Dependency

Install the dependencies for connect:

    php composer.phar install --no-dev --optimize-autoloader
    
### Build Phar

Build the connect.phar:

    php box.phar build

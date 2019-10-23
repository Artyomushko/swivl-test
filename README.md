### TEST TASK ###

##### SET UP #####

1. Clone project  
`git clone git@github.com:Artyomushko/swivl-test.git`
2. Run command for install composer packages  
`composer i`
3. Run command for installing Homestead  
`php vendor/bin/homestead make`
4. Copy Homestead.yaml.example to Homestead.yaml and change PATH_TO_PROJECT to your path  
**You need VirtualBox to be installed.**
5. Run command for starting virtual machine  
`vagrant up`
6. Go to 192.168.10.10 to see API docs
**Login and password to DB in Homestead:**  
*Login*: homestead  
*Paswd*: secret  
7. For DB migration run this command  
`php bin/console doctrine:migration:migrate`
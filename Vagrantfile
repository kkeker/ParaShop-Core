Vagrant.configure("2") do |config|
    config.vm.box = "debian/jessie64"
    config.vm.hostname = "AppserverCouch"
    config.vm.box_check_update = false

    config.vm.network "forwarded_port", guest: 9080, host: 9080
    config.vm.network "forwarded_port", guest: 8091, host: 8091

    config.vm.synced_folder "./src", "/vagrant", type: "virtualbox"
    config.vm.synced_folder "./sysconfig", "/SystemConf", type: "virtualbox"
    config.vm.synced_folder "./tmp", "/SystemTmp", type: "virtualbox"

    config.vm.provider "virtualbox" do |vb|
        vb.gui = false
        vb.memory = "4094"
        vb.cpus = "2"
        vb.name = "Appserver.io CouchBase"
    end

    config.vm.provision "shell", inline: <<-SHELL
        sudo echo "deb http://deb.appserver.io/ jessie main" > /etc/apt/sources.list.d/appserver.list
        wget http://deb.appserver.io/appserver.gpg -O - | sudo apt-key add -
        wget http://packages.couchbase.com/releases/couchbase-release/couchbase-release-1.0-2-amd64.deb
        sudo dpkg -i couchbase-release-1.0-2-amd64.deb
        rm -f couchbase-release-1.0-2-amd64.deb
        wget http://nginx.org/keys/nginx_signing.key
        sudo apt-key add nginx_signing.key
        rm -f nginx_signing.key
        sudo echo "deb http://nginx.org/packages/debian/ jessie nginx" > /etc/apt/sources.list.d/nginx.list
        sudo echo "deb-src http://nginx.org/packages/debian/ jessie nginx" >> /etc/apt/sources.list.d/nginx.list
        sudo apt update && sudo apt upgrade -y
        sudo apt install appserver-dist libcouchbase-dev build-essential autoconf zlib1g-dev -y
        wget http://packages.couchbase.com/releases/4.5.1/couchbase-server-enterprise_4.5.1-debian8_amd64.deb
        sudo dpkg -i couchbase-server-enterprise_4.5.1-debian8_amd64.deb
        rm -f couchbase-server-enterprise_4.5.1-debian8_amd64.deb
        sudo systemctl enable appserver.service
        sudo systemctl enable appserver-php5-fpm.service
        sudo systemctl enable appserver-watcher.service
        sudo systemctl enable couchbase-server.service
        sudo ln -s /vagrant /opt/appserver/webapps/myapp
        sudo ln -s /opt/appserver/bin/* /usr/bin/
        sudo ln -s /opt/appserver/sbin/* /usr/sbin/
        sudo pecl install pcs-1.3.1
        sudo echo "extension=pcs.so" > /opt/appserver/etc/conf.d/pcs.ini
        sudo pecl install couchbase
        sudo echo "extension=couchbase.so" > /opt/appserver/etc/conf.d/couchbase.ini
        sudo sed -i 's/^/;/' /opt/appserver/etc/conf.d/mysql.ini
        sudo sed -i 's/^/;/' /opt/appserver/etc/conf.d/mysqli.ini
        sudo apt remove module-assistant build-essential autoconf zlib1g-dev -y
        sudo apt-get autoremove -y
        sudo systemctl restart couchbase-server.service
        sudo systemctl restart appserver-php5-fpm.service
        sudo systemctl restart appserver-watcher.service
        sudo systemctl restart appserver.service
        sudo apt install nginx -y
        sudo systemctl enable nginx.service
        sudo sed -i 's/worker_processes  1/worker_processes auto/g' /etc/nginx/nginx.conf
        sudo echo > /etc/nginx/conf.d/default.conf
        sudo systemctl restart nginx.service
    SHELL
end
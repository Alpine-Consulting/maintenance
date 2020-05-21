## Alpine Maintenance Magento 2
This free module allows to easily replace Magento maintenance mode page by just simply uploading html file to your pub directory.
## How to install
Run composer installation command:
```
composer require alpine/maintenance
```
## How to use
1. Create you own maintenance page html file, name it **index.html** 
2. Create maintenance directory in Magento pub folder. 
3. Place created html in maintenance directory. 
You can also place additional resources like styles and images there, but remember to include pub root path.
4. **Enable Magento production mode**
```
bin/magento deploy:mode:set production
```


[![Alpine Logo](https://alpineinc.com/wp-content/uploads/2020/02/logo-1.png)](https://alpineinc.com/)

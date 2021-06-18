# Appboxo Connector

Appboxo Connector is a Magento 2 module providing a more sophisticated integration with Shopboxo

## Features

* Creates **Appboxo Connector** integration with these permissions:
```
Sales → Operations → Orders → Actions → Create
Sales → Operations → Orders → Actions → View
Sales → Operations → Orders → Actions → Reorder
Sales → Operations → Orders → Actions → Edit
Sales → Operations → Orders → Actions → Cancel
Sales → Operations → Orders → Actions → Accept or Deny Payment
Sales → Operations → Orders → Actions → Capture
Sales → Operations → Orders → Actions → Invoice
Sales → Operations → Orders → Actions → Comment
Catalog → Inventory → Products
Catalog → Inventory → Categories
Customers → All customers
Carts → Manage carts
Stores → Settings → Configuration → Inventory section
```
* Extends built-in REST API for fetching products and managing carts

## Prerequisites

The module supports Magento 2.2 or above versions. Make sure you have access to the terminal for executing Magento commands.

## Installation

Run the below command to install the connector via composer
```
composer require appboxo/connector
```

After installation please run these commands for the module to start working
```
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento c:c
```

## Setup integration

1. Login to Magento admin panel
2. Go to Appboxo Connector page `Stores -> Configuration -> Appboxo Connector`
3. Click on **Generate Token** button
4. Click on **Copy Token** and paste it in your integration settings in [Shopboxo dashboard](https://shop.appboxo.com/integration)

## Authors

* *Initial work* - [Irfan Ullah](https://github.com/Irfanbh)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Support
In case of any issues, please contact [Appboxo Team](mailto:support@appboxo.com?subject=[Appboxo%20Connector%20issue])

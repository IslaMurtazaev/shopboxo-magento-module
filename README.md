# Appboxo Connector

Appboxo Connector is Magento2 mdoule to providing basic connection link between Appboxo and client's stores. 

## Getting Started

For current test stage it will install by simply upload the app folder to your Magento2 root. and run Magento upgrad and deploy commands.

### Prerequisites

You will Magento 2.2 or above vesrion to install this module. Make sure you have access to terminal for executing various Magento commands. 


### Installing

Upload copy of app folder to your Magento2 root and run bellow commands. 

```
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento c:c
```


## Running the tests

After installation login to your Magento admin and goto 
```
Stores -> Configuration -> Appboxo Connector
```
Please make sure that you have valide Appboxo Email and Appboxo Key which are required for generating token. After enter email and key provided by Appboxo team click on **Generate Token** button. After success message click on **Copy Token** button and send that to Appboxo team using Email, Skype, Whatsapp or other source. 


## Version

0.0.1

## Authors

* **Irfan Ullah** - *Initial work* - [Irfanbh](https://github.com/Irfanbh)

See also the list of [contributors](https://github.com/Appboxo/shopboxo-magento-module/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Support
Incase of any issue please contact with Appboxo Team. 
Thanks

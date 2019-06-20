## What is TreoCore?

TreoCore is an open-source software ecosystem developed by TreoLabs GmbH and distributed under GPLv3 License for free. TreCore is designed for rapid development of web-based responsive business applications of any kind (ERP, PIM, CRM, DMS, MDM, DAM, etc.). It is an excellent tool for cost-effective and timely application development that comes with the powerful out-of-the-box functionality.

TreoCore is a single page application (SPA) with an API-centric, service-oriented architecture, and flexible data model based on configurable entities and relations. You can organize any data and business-related processes directly in TreoCore, many of them via a simple and user-friendly configuration.

### For Whom?

TreoCore is the best fit **for businesses**, who want to:

- solve custom business problems;
- store data and organize business processes;
- set up and use a middleware to connect with the third-party systems;
- create added value and best experience for their employees, customers, and partners;
- extend the functionality of the existing software infrastructure.

### TreoCore Add-Ons

The following add-on applications are available on the TreoCore basis:

- TreoPIM
- TreoCRM

Both applications can be used in a single instance and can be extended with numerous modules.

### What is on Board?

| Feature                     | Description                                                  |
| --------------------------- | ------------------------------------------------------------ |
| Dashboards                  | Use multiple dashboards to control all main activities in the system. |
| Module Manager              | It allows you to install or update any module directly from the administration panel – just choose the version you want to use. |
| Entity Manager              | You can configure the data model directly from the administration panel, create new or edit existing entities and set relations of different types. |
| Dynamic field logic         | You can configure the conditions that make some fields invisible, read-only or editable. |
| Layout Manager              | Use it to configure any User Interface in the system or to show up the panels for the related entities via drag-and-drop. |
| Label Manager               | You can edit any label in the system, in all languages you want to use. |
| Configurable navigation     | Use the drag-and-drop functionality to set a navigation as you wish, also separately for each user, if needed. |
| Scheduled jobs              | You can configure, which jobs should be run by cron and at what schedule. |
| Notifications               | Set up a system or e-mail notifications for different events in the system. |
| Data import and export      | You can import or export any data to any and from any entity in the system, even those you have just created. |
| Advanced mass updates       | Choose the entries to be updated, set the new values and do a bulk update. |
| Advanced search and filters | You can configure the filters and search criteria as you wish and save them, if you want to use this saved filter (criterion) later. |
| Portals                     | Use this additional layer to give the third parties access to your system. Use portal roles to restrict their access rights. |
| Change log and stream       | See all changes made to the entries (who, old and new value, when) and add your own posts with a timestamp and attachments. |
| Queue Manager               | Use it if you want to run or control processes in the background. |
| Access Control Lists (ACL)  | Enterprise Level ACL based on Teams and Roles, with access level (own, team, all). You are able to edit the permissions even for each field separately. |
| REST API                    | Integrate it with any third-party software; fully automated. |

### Advantages of Using TreoCore

- Really quick time to market and low implementation costs!
- Configurable, flexible and customizable
- Free - 100% open source, licensed under GPLv3
- REST API
- Web-based and platform independent
- Based on modern technologies
- Decent code quality
- Service-oriented architecture (SOA)
- Responsive and user-friendly UI
- Configurable (entities, relations, layouts, labels, navigation, etc.)
- Extensible with modules
- Very fast
- Easy to maintain and support
- Many out-of-the-box features
- Best for Rapid Application Development

### What Technologies Is It Based On?

TreoCore was created based on EspoCRM. It uses:

- PHP7 – pure PHP, without any frameworks to achieve the best possible performance;
- backbone.js – framework for SPA Frontend;
- Composer – dependency manager for PHP;
- Some libraries from Zend Framework 3;
- MySQL 5.

### Integrations

TreoCore has a REST API and can be integrated with any third-party system. You can also use import and export functions or use our modules (import feeds and export feeds) to get even more flexibility.

### Documentation

We are working on our documentation. The current version of user guide is available [here](https://treopim.com/help) and the documentation for administrators is available [here](docs/en/administration/).
<!-- correct these links when the documentation is prepared-->

### Requirements

- Unix-based system. Linux Mint is recommended.
- PHP 7.1 or above (with pdo_mysql, openssl, json, zip, gd, mbstring, xml, curl, exif extensions)
- MySQL 5.5.3 or above

### Configuration Instructions Based on Your Server

* [Apache server configuration](docs/en/administration/apache-server-configuration.md)
* [Nginx server configuration](docs/en/administration/nginx-server-configuration.md)

### Installation

> Installation guide is based on **Linux Mint OS**. Of course, you can use any unix-based system, but make sure that your OS supports the following commands described below.<br/>

To create your new TreoCore application, first make sure you are using PHP 7.1 or above and have [Composer](https://getcomposer.org/) installed. 

1. Create your new project by running:

   ```
   composer create-project treolabs/treocore my-treocore-project
   ```
   > **my-treocore-project** – project name

2. Change recursively the user and group ownership for project files. It is important for the code generation mechanism.
   ```
   sudo chown -R www-data:www-data my-treocore-project/
   ```
3. Make the cron handler files executable:
   ```
   sudo chmod +x my-treocore-project/bin/cron.sh
   ```
4. Configure the crontab:
    - the crontab should be configured for **www-data** user. You can do it by running:
      ```
      sudo crontab -u www-data -e
      ```
    - enter the following configuration:
      ```
      * * * * * cd /var/www/my-treocore-project; ./bin/cron.sh process-treocore /usr/bin/php 
      ```
      >**/var/www/my-treocore-project** – a path to the project root <br/>
      >**process-treocore** – a unique process ID. You should use a different process ID if you have several TreoCore projects on one server<br/>
      >**/usr/bin/php** – PHP7.1 or above

5. Install TreoCore following the installation wizard in web interface. Go to http://YOUR_PROJECT/

### License

TreoCore is published under the GNU GPLv3 [license](LICENSE.txt).

### Support

- TreoCore is developed and supported by [TreoLabs GmbH](https://treolabs.com/).
- Feel free to join [our Community](https://community.treolabs.com/).
- To contact us, please visit [our Website](https://treolabs.com/).

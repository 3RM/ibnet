![cutter](https://ibnet.org/images/site/ibnet-large-color.png)

-------------------
### ibnet.org | Independent Baptist Network

Independent Baptist Network is an online community of Independent Baptists.

The IBNet application is built on the [Yii2 framework](https://www.yiiframework.com/) using the [Advanced Project Template](https://www.yiiframework.com/extension/yiisoft/yii2-app-advanced/doc/guide/2.0/en).

It includes three tiers: 
 * front end - the main site
 * back end - admin panel
 * console - console scripts
 
each of which is a separate Yii application.


New Yii Installation
-------------------
### Install Yii with advanced template
[Follow the Yii Guide](https://www.yiiframework.com/extension/yiisoft/yii2-app-advanced/doc/guide/2.0/en/start-installation)


Want to Contribute?
-------------------
Contributions are welcome!

### Fork this Repository
This installation assumes Ubuntu server and php7.

### Configure the app
 * Add common/config/main-local.php (db and mailer)
 * Add frontend/config/main-local.php (set unique cookie validation key)

### Run Migrations
```
php yii migrate 
php yii migrate --migrationPath=@fedemotta/cronjob/migrations
php yii migrate/up --migrationPath=@vendor/rmrevin/yii2-comments/migrations/
php yii migrate --migrationPath=@vendor/uguranyum/yii2-icalender/migration --interactive=0
```
A future update of this repository will include instructions for using fake data

### Send a pull request
Make your updates and send a pull request.  All contributors are invited to review and discuss.


Have Ideas for New Features and Improvements?
-------------------
Create a new issue.  The enhancement label will be added.

Did you find a bug?  Create a new issue.  The bug label will be added.
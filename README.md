# This addon is provided with community support only - you welcome to fork it and add or fix what you want!

# MX Easy BackUp

MX Easy BackUp simplifies the task of backing up your ExpressionEngine site. You can backup your DB, a system folder, custom files / directories and store it locally, remotely to [Amazon S3] /  SFTP / FTP / Email. It supports compression as well as methods of automatically scheduled backups. With MX Easy BackUp, it takes less than 1 min to backup your files before the next EE update.

[Amazon S3]: http://aws.amazon.com/

Requirements
---------------

*  [ExpressionEngine 2][]
*  PHP 5 >= 5.2.1 (safe_mode = off)
*  S3 - cURL PHP extension


[ExpressionEngine 2]: http://ellislab.com/

Installation
---------------

 1. Download the latest version of MX Easy BackUp and extract the .zip to your desktop
 2. Copy *system/expressionengine/third_party/mx\_easy\_backup* to your *system/expressionengine/third_party/mx\_easy\_backup*
 3. If you plan to use Cron : If you are using a Unix server (or Unix variant, like Linux, OS X, FreeBSD, etc.) you must set *cron.mx\_easy\_backup.php* CHMOD to **755**

 
Activation
---------------

 1. Log into your control panel
 2. Browse to Addons > Modules
 3. Enable the module
  
Configuration
---------------

### Settings:Backup Options

#### Default server path for Backup files

This is the directory path you would like to backup


**Security Notice**
**Be sure that your local backup folder is secure. I recommend you should place the backup folder above the public web "root" folder.**

#### Method

**PHP** - performing backup/restore with the PHP method.

**SYSTEM** - performing backup by means of mysqldump zip/gzip  system commands and restore with mysql command. At this moment, this method is available only for the *nix system, so if you have the Windows server, this option will be hidden.

#### Archive type
only fo **SYSTEM** method

### Settings:Amazon S3 settings

####AWS Access Key
This is actually a username. It's represented by an alphanumeric text string that uniquely identifies a user who owns an account. Two different accounts cannot have the same AWS Access Key.

####AWS Secret Key

This key plays a role of a password. It's regarded as secret because it is assumed to be known only by an owner. So when you type it in a given box, it's displayed as asterisk or dots. The Password and the Access Key constitute a secure information set that confirms the user's identity.

While performing backup by sending to S3, you have to provide these two keys. 

### S3 Bucket Name

Amazon S3 bucket for backup files. 

####Create a new bucke

You can create a new S3 bucket right from MX Easy BackUp. 

Although Amazon will allow you to use capital letters and periods in the namespace, it is not recommended to do so due to the naming restrictions that are enforced by DNS. In order to conform with the DNS requirements, we recommend you follow these additional guidelines while creating buckets:

*	Bucket names should not contain upper case letters
*	Bucket names should not contain underscores (_)
*	Bucket names should not end with a dash
*	Bucket names should be between 3 and 63 characters long
*	Bucket names cannot contain dashes next to periods (e.g., "my- bucket.com" and "my.-bucket" are invalid)


### Settings: FTP

#### Host
Your host. 

#### Port
Your host port. 

####Username
Your ftp username. 

####Password
Your ftp password.

####Directory for backup files storage
Directory on the FTP server where backup files must be stored. 

####Use passive mode
Some FTP servers require the connection to be established in a passive mode (i.e. your computer establishes the connection so the flow of data is set up and initiated by you). 

### Settings: SFTP

#### Host
Your  host. 

#### Port
Your host port. 

####Username
Your username. 

####Password
Your password.

####Directory for backup files storage
Directory on the server where backup files must be stored. 

### Settings:Send back up to Email

#### Email Address for backup
Email address(es) you would like to get your backup.  

#### Email Subject 

#### Email Message Body 
Here is a template for your email. You can use the following tags in it:

*	**{size}** - a backup file size
*	**{filename}** - a backup file name
*	**{plan_id}** - a backup plan id
*	**{data}** - a backup date
*	**{time}** - a backup time
*	**{plan_name}** - a backup plan name


### Settings:Notification Preferences
You can setup categories of notifications you would like to receive information about every time according to the backup results.

#### Email Address for Notification
Email address(es) you would like to get your backup notifications to.  

#### Email Subject 

#### Email Message Body 
Here is a template for your email. You can use the following tags in it:

*	**{size}** - a backup file size
*	**{filename}** - a backup file name
*	**{plan_id}** - a backup plan id
*	**{data}** - a backup date
*	**{time}** - a backup time
*	**{plan_name}** - a backup plan name

Backup Plan Settings
---------------
### Name for a Backup Plan
A name for your backup plan

### Server Path for Backup Files
A path to the local directory where the backup archive will be stored.

### Backup Options

### Sent Files after Backup
MX Easy BackUp can automatically send files to a remote server (FTP, SFTP, Amazon S3) or to an e-mail address. In this case, an original file in a local folder will be deleted. The list is based on your settings. Those methods without any settings will not be available for choosing.

### Optional files/dir for backup
Here you should enter paths to optional files/directories you want to backup. 

### Choose tables for DB dump
If you don't need to backup a full DB, you can choose separate tables. 

### Backup type
Full or Differential


Cron Setup
---------------
###You can used MX Easy Backup with [Cron plugin][]
		
		# example
		{exp:cron minute="*" hour="1" day="*" month="*" module="mx_easy_backup:start_backup" task_id="2"}{/exp:cron}

[Cron plugin] :http://expressionengine.com/index.php?affiliate=eecms&page=/downloads/details/expressionengine_cron/

###Cron with wget or third-party services which can ping you page. You can find URL in your MX Easy Backup task settings page.

		#wget cron example
		0 13 * * * wget http://yourdomain.com/index.php?ACT=XX&task_id=1  

FAQ
---------------
### Q: How can I restore my backup in case of a disaster?
 A: Currently, the MX Easy Backup does not offer an automatic restore option. There are quite a number of reasons for it. Mainly due to the fact that it would be a "hot" restore; besides, due to a great amount of different possible web server configurations such an action can result into many unpleasant issues, and just a single failure would lead to a completely unusable ExpressionEngine installation. Therefore, I recommend that the database restore should be implemented manually. 


### Q: In "Sent files after Backup" list I have only a "none" option. What should I do?
 A: You need enter the Settings page and set up your remote servers options.  

### Q: How to find the path to php executable for use in cron scripts?
 A: Start by typing at a command line: "whereis php".  Do this as the user that the cron job will be run under. This will show you the path to your executable. 

### Q: I can't receive some files to my Google Mail account ?
 A: [Gmail Help][] :
 
*	1. As a security measure to prevent potential viruses, Gmail doesn't allow you to send or receive executable files (such as files ending in .exe) that could contain damaging executable code. In addition, Gmail does not allow you to send or receive files that are corrupted. Gmail won't accept these types of files even if they are sent in a zipped (.zip, .tar, .tgz, .taz, .z, .gz) format. If this type of message is sent to your Gmail address, it is bounced back to the sender automatically. 
 
*	2. You can send and receive messages up to 25 megabytes (MB) total (including attachments). Any message that exceeds this limit will not be delivered to your inbox and will be returned to the sender.
 
 [Gmail Help] : http://mail.google.com/support/bin/answer.py?answer=6590
 


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/MaxLazar/mx-easy-backup/trend.png)](https://bitdeli.com/free "Bitdeli Badge")


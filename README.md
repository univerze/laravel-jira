# Laravel5 Jira service

Easy access Jira rest api in Laravel5.

* [Installation and Requirements](#installation)
* [Getting Versions](#versions)
* [Searching issues](#searching)
* [Creating issues](#creating)
* [Editing issues](#editing)
* [Changing status or version of issues](#transitions)

<a name="installation"></a>
## Installation and Requirements

```sh
composer require univerze/laravel-jira
```

Afterwards, run `composer update` from your command line.

Then, update `config/app.php` by adding an entry for the service provider.

```php
'providers' => [
    // ...
    Univerze\Jira\JiraServiceProvider::class,
];

'aliases' => [
  	// ...
  	'Jira' => Univerze\Jira\Facade\JiraFacade::class,
];
```

Finally, from the command line again, run `php artisan vendor:publish` to publish
the default configuration file to config/jira.php.
YOu can either set your credentials in the configuration file or you can enter the credentials to new created instance.
```php
use Univerze\Jira\Jira;

$jira = new Jira("https://[yourDomain].atlassian.net/", "username", "password");
$response = $jira->search( 'project = YourProject AND labels = somelabel' );
```
Alternative you can change the credentials to use afterwards.
```php
use Univerze\Jira\Jira;

$jira = new Jira();
$jira->url      = "https://[yourDomain].atlassian.net/";
$jira->username = "username";
$jira->password = "password";
$response = $jira->search( 'project = YourProject AND labels = somelabel' );
```

<a name="versions"></a>
## Getting versions

Get all versions of a project:
```php
$jira = new Jira();
$response = $jira->getVersions( 'YourProject' );
```

In addition you can request details for a specific version listed in the versions-list
```php
$jira = new getVersion();
$response = $jira->getVersion( '20351' );
```

<a name="searching"></a>
## Searching issues

The search method will take the jql query string:

```php
$jira = new Jira();
$response = $jira->search( 'project = YourProject AND labels = somelabel' );
```

You can build and test the jql beforehand if you go to your Jira site Issues > Search for Issues > Advanced Search.

Further information can be found on [JIRA documentation - search issues](https://developer.atlassian.com/jiradev/jira-apis/jira-rest-apis/jira-rest-api-tutorials/jira-rest-api-example-query-issues)

> **NOTE** jql parameter is already included in the payload

<a name="creating"></a>
## Creating issues

```php
$jira = new Jira();
$issue = $jira->create( array(
    'project'     => array(
        'key' => 'YourProject'
    ),
    'summary'     => 'This is the summary',
    'description' => 'Description here',
    'issuetype'   => array(
        'name' => 'Bug'
    )
) );
```

Further information can be found on [JIRA documentation - create issue](https://developer.atlassian.com/jiradev/jira-apis/jira-rest-apis/jira-rest-api-tutorials/jira-rest-api-example-create-issue)

> **NOTE** fields parameter is already included in the payload

<a name="editing"></a>
## Editing issues

```php
$jira = new Jira();
$jira->update( 'ISSUE-1234', array(
    'description' => 'this is my new description'
) );
```

In this case the JIRA api will return "204 - No Content" instead of issue details.

Further information can be found on [JIRA documentation - edit issue](https://developer.atlassian.com/jiradev/jira-apis/jira-rest-apis/jira-rest-api-tutorials/jira-rest-api-example-edit-issues)

> **NOTE** fields parameter is already included in the payload

<a name="transitions"></a>
## Changing status or version of issues

Get all available transitions for an issue. Only transactions are listed, that are visible in context for the user
```php
$jira = new Jira();
$jira->getTransitions( 'ISSUE-1234') );
```

If you want to change the status of an issue you have to execute a available transition. Tha available transitions you can get with getTransitions
```php
$jira = new Jira();
$jira->doTransitions( 'ISSUE-1234', '3') );
```

You can move an issue to another version
```php
$jira = new Jira();
$jira->updateVersion( 'ISSUE-1234', '20351') );
```

---

Released under the MIT License. See the LICENSE file for details.
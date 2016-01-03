# Ultimed Integration Kit

## Installation

Create a `composer.json` file in the root directory:

```
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/lan0/ultimed-integration"
        }
    ]
}
```

Run the following command:

```
composer require ultimed/integration
```

## Examples

 - [Authentication](examples/authenticate.php)
 - Patients
    - [Show details of one patient](examples/authentication.php)
 - Files
    - [Upload a new file](examples/upload-file.php)
 - History items
    - [Create new history item](examples/create-history-item.php)
 - Attachments
    - [Create attachment](examples/create-attachment.php)
 - Pictures
    - [Create picture](examples/create-picture.php)

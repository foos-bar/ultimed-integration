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
    - [Show details of one patient](examples/patient-show.php)
    - [Search patients by custom field](examples/patient-by-custom-field.php)
 - Files
    - [Upload a new file](examples/upload-file.php)
    - [Update an existing file](examples/update-file.php)
 - History items
    - [Create new history item](examples/create-history-item.php)
    - [Update existing history item](examples/update-history-item.php)

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

## Tests

Install development dependencies and run the regression suite:

```sh
composer install
composer test
```

The suite covers OAuth header formatting, request and response handling, and
real HTTP transport. The transport tests require the cURL extension, local
TCP sockets, and `proc_open`.

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

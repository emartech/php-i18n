[ ![Codeship Status for emartech/php-i18n](https://app.codeship.com/projects/27708760-f2b7-0134-aeff-123376db6b99/status?branch=master)](https://app.codeship.com/projects/209769)

# php-i18n

Install dependencies with docker: 

```
$ docker run --rm --interactive --tty --volume $PWD:/app composer install --ignore-platform-reqs --no-scripts
```

Update dependencies with docker: 

```
$ docker run --rm --interactive --tty --volume $PWD:/app composer update --ignore-platform-reqs --no-scripts
```

Run tests

```
$ docker run -v $(pwd):/app --rm phpunit/phpunit test/ --bootstrap vendor/autoload.php
```

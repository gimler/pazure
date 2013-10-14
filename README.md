Pazure
============

Pazure is a command line tool to interact with the Azure API.

[![Build Status](https://secure.travis-ci.org/gimler/pazure.png?branch=master)](http://travis-ci.org/gimler/pazure)

Installation
------------

### Locally

Download the
[`pazure.phar`](https://github.com/gimler/pazure/raw/master/pazure.phar) file and
store it somewhere on your computer.

### Globally

You can run these commands to easily access `pazure` from anywhere on your system:

    $ sudo wget https://github.com/gimler/pazure/raw/master/pazure.phar -O /usr/local/bin/pazure

or with curl:

    $ sudo curl https://github.com/gimler/pazure/raw/master/pazure.phar -o /usr/local/bin/pazure

then:

    $ sudo chmod a+x /usr/local/bin/pazure

### Define Service

Copy the service definition and add your `subscription_id` and `key_path` see also [Sourcing from a JSON document](http://guzzlephp.org/tour/using_services.html#sourcing-from-a-json-document)

    $ cp services.json.dist services.json

Then, just run `pazure` in order to run pazure

Update
------

### Locally

The `self-update` command tries to update pazure itself:

    $ php pazure.phar self-update

### Globally

You can update pazure through this command:

    $ sudo pazure self-update


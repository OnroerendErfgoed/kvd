KVD Library
===========

[![Build Status](https://travis-ci.org/OnroerendErfgoed/kvd.svg&branch=master)](https://travis-ci.org/OnroerendErfgoed/kvd)
[![Coverage](https://coveralls.io/repos/OnroerendErfgoed/kvd/badge.png?branch=master)](https://coveralls.io/r/OnroerendErfgoed/kvd?branch=master)
[![Packagist](https://poser.pugx.org/oe/kvd/v/stable.svg)](https://packagist.org/packages/oe/kvd)

Oude php bibliotheek met ondersteunende code. Wordt gebruikt door:
 * de inventaris
 * OAR
 * CAI
 * mogelijk bibliotheek

Installatie kan via composer.

```bash
$ curl -sS https://getcomposer.org/installer | php
```

```bash
$ php composer.phar update
```

Unit tests uitvoeren en code coverage bekijken
```bash
$ ./vendor/bin/phing genTestReports
$ firefox build/reports/coverage/index.html &
```

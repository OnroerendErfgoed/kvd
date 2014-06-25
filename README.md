KVD Library
===========

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

Unit tests uitvoeren
```bash
$ ./vendor/bin/phing runTests
```

Unit tests uitvoeren en code coverage bekijken
```bash
$ ./vendor/bin/phing genTestReports
$ firefox build/reports/coverage/index.html &
```

Om de ws-crab unit tests uit te voeren moet je eerst een wachtwoord toevoegen 
aan build.properties.

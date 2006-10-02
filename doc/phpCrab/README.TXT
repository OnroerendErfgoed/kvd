PHPCRAB
=======
Versie: $Id$
Auteur: Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
Revisie: $Rev$

Dit pakket bevat een aantal classes die het mogelijk maken om te werken met CRAB (de webservice van het AGIV die de straten en adressen van Vlaanderen ontsluit via het SOAP protocol. Het pakket bevat drie mappen:
- classes: Bevat de classes zelf. Deze verwijzen niet naar elkaar. Het is de verantwoordelijkheid van de programmeur om dit te doen via bv. een __autoload() functie (zie de php handleiding voor meer info over het gebruik van __autoload).
- tests: Bevat de Unit Tests voor dit pakket. Deze werden geschreven voor SimpleTest ( zie http://www.lastcraft.com). Ze verschaffen ook enig inzicht in het aanroepen van de code.
- docs: API-documentatie aangemaakt met phpDocumentor.

Zoals elk stuk software is dit nog steeds in ontwikkeling (de Crab1 class is al vrij veel gebruikt, de Crab2 class is heeft enkel nog maar Unit Testing doorstaan) en dus niet noodzakelijk vrij van bugs. Deze kunnen gemeld worden aan Koen Van Daele.

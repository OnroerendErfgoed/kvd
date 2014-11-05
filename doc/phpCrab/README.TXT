PHPCRAB
=======
Versie: $Id: README.TXT 210 2006-10-02 14:07:14Z vandaeko $
Auteur: Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
Revisie: $Rev$

NOTE BY BDKOCK ON 2014/11/5: this README has become obsolete, instead see README.md in the repository root dir.

Dit pakket bevat een aantal classes die het mogelijk maken om te werken met CRAB (de webservice van het AGIV die de straten en adressen van Vlaanderen ontsluit via het SOAP protocol. Het pakket bevat drie mappen:
- classes: Bevat de classes zelf. Deze verwijzen niet naar elkaar. Het is de verantwoordelijkheid van de programmeur om dit te doen via bv. een __autoload() functie (zie de php handleiding voor meer info over het gebruik van __autoload).
- tests: Bevat de Unit Tests voor dit pakket. Deze werden geschreven voor SimpleTest ( zie http://www.lastcraft.com). Ze verschaffen ook enig inzicht in het aanroepen van de code. Een script om de tests te runnen wordt niet meegeleverd. Op de SimpleTest website kun je meer informatie hierover vinden. Het script om de tests te runnen moet onder andere de volgende constanten definieren: CRABUSER en CRABPWD. Deze zijn nodig voor de Crab1 en Crab2 gateways om te kunnen connecteren met Crab. 
- docs: API-documentatie aangemaakt met phpDocumentor.

Zoals elk stuk software is dit nog steeds in ontwikkeling  en dus niet noodzakelijk vrij van bugs. Deze kunnen gemeld worden aan de auteur.

Met dank aan Joeri Theelen voor het bezorgen van de nodige API-documentatie van Crab.

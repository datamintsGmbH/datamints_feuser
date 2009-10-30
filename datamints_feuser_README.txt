datamints_feuser
================

Primär:
-------
- Template wie bei ALOE-FeUser
- evtl. "templateble" via cObj:FORM
- Registrieren eines Users (alle Felder von FE-Users können editiert werden)
- Editieren eines Users (alle Felder von FE-Users können editiert werden)
- encryption via salted MD5
- Nach dem registrieren wird der User einer per TS angegebenen Gruppe zugewiesen
- Feldtyp aus TCA holen und die versch. Eingabefelder für das Template rendern
- E-Mail-Benachrichtigung an (kommaseparierte) Admin(s) bei Registrierung
- double-opt-in (Bestätigungs-E-Mail an Registrar mit Bestätigungs-Link)
- locallang.xml (und demnach _LOCAL_LANG) verwenden
- Image-Feld berücksichtigen (timestamp an Name?)


Sekundär:
---------
- JS-Prüfung von Feldinhalten während der Eingabe
- (Userdaten aus OpenID in Standardfelder importieren ... geht das?)



TypoScript:
===========
- templateFile
- storagePID
- Anzeigefelder (kommasepariert)
- Pflichtfelder (kommasepariert)
- Admin-E-Mail-Adressen (kommasepariert)
- Admin-Name (für E-Mail-Adresse)
- Verzeichnis-Pfad für Bilddateien
- Max Größe für Bilder


Flexform:
=========
- templateFile
- storagePID
- Anzeigetyp (edit/register)
- Anzeigefelder (Auswahlfeld)
- Pflichtfelder (Auswahlfeld)



Locallang:
==========
- Admin-E-Mail-Text
- User-E-Mail-Text




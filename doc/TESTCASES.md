# Testfälle: Fahrzeugkosten-Tracker

## 1. Benutzer-Authentifizierung
| ID | Testfall | Beschreibung | Erwartetes Ergebnis |
|----|----------|--------------|---------------------|
| 1.1 | Registrierung | Neues Konto anlegen mit validen Daten | Benutzer wird registriert und zum Login weitergeleitet. |
| 1.2 | Login (Erfolgreich) | Einloggen mit korrekten Daten | Dashboard wird angezeigt, Session ist aktiv. |
| 1.3 | Login (Ungültig) | Einloggen mit falschem Passwort | Fehlermeldung erscheint, Zugriff verweigert. |
| 1.4 | Deaktivierter User | Admin setzt Status auf 'deactivated' in DB | Login wird verweigert mit Hinweis auf Deaktivierung. |

## 2. Fahrzeugverwaltung
| ID | Testfall | Beschreibung | Erwartetes Ergebnis |
|----|----------|--------------|---------------------|
| 2.1 | Fahrzeug anlegen | Neues Fahrzeug mit Kennzeichen hinzufügen | Fahrzeug erscheint in der Liste. |
| 2.2 | Fahrzeug löschen | Bestehendes Fahrzeug löschen | Fahrzeug verschwindet aus der Liste (Soft-Delete in DB). |
| 2.3 | Zugriffsschutz | Versuch, Fahrzeuge anderer User aufzurufen | Zugriff wird verweigert (Query-Filter nach user_id). |

## 3. Kostenbuchungen
| ID | Testfall | Beschreibung | Erwartetes Ergebnis |
|----|----------|--------------|---------------------|
| 3.1 | Allgemeine Buchung | Werkstatt-Kosten für ein Fahrzeug erfassen | Buchung erscheint in der Übersicht. |
| 3.2 | Tankbuchung | Buchung mit Litern und Preis/L erfassen | Buchung erscheint, Tankdetails sind sichtbar. |
| 3.3 | Kategorien (m:n) | Buchung mehreren Kategorien zuordnen | Alle gewählten Kategorien werden in der Liste angezeigt. |
| 3.4 | Filterung | Buchungen nach Monat/Jahr/Fahrzeug filtern | Liste zeigt nur die entsprechenden Einträge. |

## 4. Auswertungen
| ID | Testfall | Beschreibung | Erwartetes Ergebnis |
|----|----------|--------------|---------------------|
| 4.1 | Monatliche Kosten | Anzeige der summierten Kosten pro Monat | Korrekte Summenbildung im Dashboard. |
| 4.2 | Durchschnittsverbrauch | Anzeige des Verbrauchs (l/100km) | Berechnung erfolgt auf Basis von KM-Stand und Litern. |

## 5. Sicherheitstests
| ID | Testfall | Beschreibung | Erwartetes Ergebnis |
|----|----------|--------------|---------------------|
| 5.1 | SQL-Injection | Versuch, ' OR 1=1 -- in Login einzugeben | Login schlägt fehl, kein DB-Ausbruch möglich. |
| 5.2 | XSS-Schutz | `<script>alert(1)</script>` als Fahrzeugname | Script wird als Text angezeigt, kein Alert-Popup. |
| 5.3 | CSRF-Schutz | Formular-Submit ohne CSRF-Token | Anfrage wird mit Fehlermeldung abgelehnt. |

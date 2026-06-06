# Testprotokoll: Fahrzeugkosten-Tracker

Dieses Dokument enthält detaillierte Testfälle für die manuelle Validierung der Applikation.

---

## 1. Authentifizierung & Login-Sicherheit

### TF 1.1: Erfolgreicher Login (Standard-Benutzer)
**Voraussetzung:** Ein registrierter Benutzer existiert (z.B. `user@example.com`).
1. Navigieren Sie zur Login-Seite (`/login`).
2. Geben Sie die E-Mail und das Passwort ein.
3. Klicken Sie auf "Login".
**Erwartetes Ergebnis:** Weiterleitung zum Dashboard (`/dashboard`). Eine Willkommensnachricht mit dem Namen des Benutzers ist sichtbar.

### TF 1.2: Erfolgreicher Login (Administrator)
**Voraussetzung:** Ein Administrator-Benutzer existiert (Rolle `administrator`).
1. Navigieren Sie zur Login-Seite.
2. Geben Sie Admin-Daten ein.
3. Klicken Sie auf "Login".
**Erwartetes Ergebnis:** Weiterleitung zum Dashboard. In der Navigation ist der Punkt "Benutzerverwaltung" (Admin) sichtbar.

### TF 1.3: Fehlgeschlagener Login (Falsche Daten)
1. Navigieren Sie zur Login-Seite.
2. Geben Sie eine existierende E-Mail, aber ein falsches Passwort ein.
3. Klicken Sie auf "Login".
**Erwartetes Ergebnis:** Fehlermeldung "Invalid email or password" erscheint. Keine Weiterleitung.

### TF 1.4: Rate Limiting (Brute-Force Schutz)
1. Navigieren Sie zur Login-Seite.
2. Geben Sie 15 Mal hintereinander falsche Anmeldedaten für dieselbe E-Mail ein.
**Erwartetes Ergebnis:** Beim 16. Versuch erscheint die Meldung: "Too many failed login attempts. Please try again in 15 minutes."

### TF 1.5: Login deaktivierter Benutzer
**Voraussetzung:** Ein Benutzer mit Status `deactivated` existiert.
1. Versuchen Sie, sich mit dem deaktivierten Account einzuloggen.
**Erwartetes Ergebnis:** Fehlermeldung: "Your account has been deactivated. Please contact an administrator."

### TF 1.6: Logout
1. Klicken Sie im eingeloggten Zustand auf "Logout".
**Erwartetes Ergebnis:** Die Session wird beendet. Weiterleitung zur Login-Seite. Ein Zugriff auf `/dashboard` ist nicht mehr ohne erneuten Login möglich.

---

## 2. Rollenbasierte Ansichten & Berechtigungen

### TF 2.1: Zugriff als Gast (Nicht eingeloggt)
1. Versuchen Sie, die URL `/dashboard` direkt aufzurufen.
**Erwartetes Ergebnis:** Automatische Weiterleitung zur Login-Seite.

### TF 2.2: Zugriff auf Admin-Bereich als Standard-User
1. Loggen Sie sich als normaler Benutzer ein.
2. Versuchen Sie, die URL `/admin/users` manuell aufzurufen.
**Erwartetes Ergebnis:** Zugriff verweigert (Fehlermeldung oder Weiterleitung, je nach Implementierung in `requireRole`).

### TF 2.3: Admin-Funktionen
1. Loggen Sie sich als Administrator ein.
2. Navigieren Sie zur Benutzerverwaltung.
**Erwartetes Ergebnis:** Liste aller Benutzer wird angezeigt. Status (Aktiv/Deaktiviert) kann geändert werden.

---

## 3. CRUD-Operationen: Fahrzeugverwaltung

### TF 3.1: Fahrzeug anlegen (Create)
1. Navigieren Sie zu "Meine Fahrzeuge" -> "Fahrzeug hinzufügen".
2. Geben Sie Name, Kennzeichen und Erstzulassung ein.
3. Speichern Sie das Formular.
**Erwartetes Ergebnis:** Das Fahrzeug erscheint in der Liste "Meine Fahrzeuge".

### TF 3.2: Fahrzeug-Liste einsehen (Read)
1. Navigieren Sie zu "Meine Fahrzeuge".
**Erwartetes Ergebnis:** Alle Ihre Fahrzeuge werden tabellarisch aufgelistet. Andere Benutzer sehen Ihre Fahrzeuge nicht.

### TF 3.3: Fahrzeug bearbeiten (Update)
1. Klicken Sie bei einem Fahrzeug auf "Bearbeiten".
2. Ändern Sie das Kennzeichen.
3. Speichern Sie.
**Erwartetes Ergebnis:** Die Änderung wird in der Liste korrekt übernommen.

### TF 3.4: Fahrzeug löschen (Delete)
1. Klicken Sie bei einem Fahrzeug auf "Löschen".
2. Bestätigen Sie die Abfrage.
**Erwartetes Ergebnis:** Das Fahrzeug verschwindet aus der Liste. (Technisch: `deleted_at` wird in DB gesetzt).

---

## 4. CRUD-Operationen: Buchungen (Kosten)

### TF 4.1: Allgemeine Buchung hinzufügen
1. Navigieren Sie zu "Buchungen" -> "Hinzufügen".
2. Wählen Sie ein Fahrzeug, Datum, Betrag und eine Kategorie (z.B. Werkstatt).
3. Speichern Sie.
**Erwartetes Ergebnis:** Buchung erscheint in der Übersicht.

### TF 4.2: Tankbuchung (Automatische Berechnung)
1. Navigieren Sie zu "Buchung hinzufügen".
2. Aktivieren Sie "Tankbuchung".
3. Geben Sie Liter (z.B. 50) und Preis pro Liter (z.B. 1.80) ein.
4. Speichern Sie.
**Erwartetes Ergebnis:** Der Gesamtbetrag wird automatisch berechnet (90.00) und gespeichert.

### TF 4.3: Buchungen filtern
1. Nutzen Sie in der Buchungsliste den Filter für ein bestimmtes Fahrzeug oder einen Monat.
**Erwartetes Ergebnis:** Die Liste aktualisiert sich und zeigt nur die passenden Einträge.

### TF 4.4: Buchung löschen
1. Löschen Sie eine vorhandene Buchung.
**Erwartetes Ergebnis:** Die Buchung wird aus der Liste und der Statistik (Dashboard) entfernt.

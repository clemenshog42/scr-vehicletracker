# Testprotokoll: Fahrzeugkosten-Tracker

## 1. Authentifizierung & Login-Sicherheit

### TF 1.1: Erfolgreicher Login (Standard-Benutzer)
**Voraussetzung:** Ein registrierter Benutzer existiert (z.B. `user@example.com`).
1. Navigiere zur Login-Seite (`/login`).
2. Gib die E-Mail und das Passwort ein.
3. Klicke auf "Login".
**Erwartetes Ergebnis:** Weiterleitung zum Dashboard (`/dashboard`). Eine Willkommensnachricht mit dem Namen des Benutzers ist sichtbar.

### TF 1.2: Erfolgreicher Login (Administrator)
**Voraussetzung:** Ein Administrator-Benutzer existiert (Rolle `administrator`).
1. Navigiere zur Login-Seite.
2. Gib Admin-Daten ein.
3. Klicke auf "Login".
**Erwartetes Ergebnis:** Weiterleitung zum Dashboard. In der Navigation ist der Punkt "Benutzerverwaltung" (Admin) sichtbar.

### TF 1.3: Fehlgeschlagener Login (Falsche Daten)
1. Navigiere zur Login-Seite.
2. Gib eine existierende E-Mail, aber ein falsches Passwort ein.
3. Klicke auf "Login".
**Erwartetes Ergebnis:** Fehlermeldung "Invalid email or password" erscheint. Keine Weiterleitung.

### TF 1.4: Rate Limiting (Brute-Force Schutz)
1. Navigiere zur Login-Seite.
2. Gib 15 Mal hintereinander falsche Anmeldedaten für dieselbe E-Mail ein.
**Erwartetes Ergebnis:** Beim 16. Versuch erscheint die Meldung: "Too many failed login attempts. Please try again in 15 minutes."

### TF 1.5: Login deaktivierter Benutzer
**Voraussetzung:** Ein Benutzer mit Status `deactivated` existiert.
1. Versuche den Login mit dem deaktivierten Account.
**Erwartetes Ergebnis:** Fehlermeldung: "Your account has been deactivated. Please contact an administrator."

### TF 1.6: Logout
1. Klicke im eingeloggten Zustand auf "Logout".
**Erwartetes Ergebnis:** Die Session wird beendet. Weiterleitung zur Login-Seite. Ein Zugriff auf `/dashboard` ist nicht mehr ohne erneuten Login möglich.

---

## 2. Rollenbasierte Ansichten & Berechtigungen

### TF 2.1: Zugriff als Gast (Nicht eingeloggt)
1. Versuche, die URL `/dashboard` direkt aufzurufen.
**Erwartetes Ergebnis:** Automatische Weiterleitung zur Login-Seite.

### TF 2.2: Zugriff auf Admin-Bereich als Standard-User
1. Führe den Login als normaler Benutzer durch.
2. Versuche, die URL `/admin/users` manuell aufzurufen.
**Erwartetes Ergebnis:** Zugriff verweigert (Fehlermeldung oder Weiterleitung, je nach Implementierung in `requireRole`).

### TF 2.3: Admin-Funktionen
1. Führe den Login als Administrator durch.
2. Navigiere zur Benutzerverwaltung.
**Erwartetes Ergebnis:** Liste aller Benutzer wird angezeigt. Status (Aktiv/Deaktiviert) kann geändert werden.

---

## 3. CRUD-Operationen: Fahrzeugverwaltung

### TF 3.1: Fahrzeug anlegen (Create)
1. Navigiere zu "Meine Fahrzeuge" -> "Fahrzeug hinzufügen".
2. Gib Name, Kennzeichen und Erstzulassung ein.
3. Speichere das Formular.
**Erwartetes Ergebnis:** Das Fahrzeug erscheint in der Liste "Meine Fahrzeuge".

### TF 3.2: Fahrzeug-Liste einsehen (Read)
1. Navigiere zu "Meine Fahrzeuge".
**Erwartetes Ergebnis:** Alle eigenen Fahrzeuge werden tabellarisch aufgelistet. Andere Benutzer sehen diese Fahrzeuge nicht.

### TF 3.3: Fahrzeug bearbeiten (Update)
1. Klicke bei einem Fahrzeug auf "Bearbeiten".
2. Ändere das Kennzeichen.
3. Speichere.
**Erwartetes Ergebnis:** Die Änderung wird in der Liste korrekt übernommen.

### TF 3.4: Fahrzeug löschen (Delete)
1. Klicke bei einem Fahrzeug auf "Löschen".
2. Bestätige die Abfrage.
**Erwartetes Ergebnis:** Das Fahrzeug verschwindet aus der Liste. (Technisch: `deleted_at` wird in DB gesetzt).

---

## 4. CRUD-Operationen: Buchungen (Kosten)

### TF 4.1: Allgemeine Buchung hinzufügen
1. Navigiere zu "Buchungen" -> "Hinzufügen".
2. Wähle ein Fahrzeug, Datum, Betrag und eine Kategorie (z.B. Werkstatt).
3. Speichere.
**Erwartetes Ergebnis:** Buchung erscheint in der Übersicht.

### TF 4.2: Tankbuchung (Automatische Berechnung)
1. Navigiere zu "Buchung hinzufügen".
2. Aktiviere "Tankbuchung".
3. Gib Liter (z.B. 50) und Preis pro Liter (z.B. 1.80) ein.
4. Speichere.
**Erwartetes Ergebnis:** Der Gesamtbetrag wird automatisch berechnet (90.00) und gespeichert.

### TF 4.3: Buchungen filtern
1. Nutze in der Buchungsliste den Filter für ein bestimmtes Fahrzeug oder einen Monat.
**Erwartetes Ergebnis:** Die Liste aktualisiert sich und zeigt nur die passenden Einträge.

### TF 4.4: Buchung löschen
1. Lösche eine vorhandene Buchung.
**Erwartetes Ergebnis:** Die Buchung wird aus der Liste und der Statistik (Dashboard) entfernt.

# EuroKeep Backend – Sicherheitsanalyse (`src/`, CakePHP 5)

**Datum Erstanalyse:** 2026-08-26
**Datum dieses Updates:** 2026-08-26 (Re-Analyse nach Fixes)
**Scope:** `src/` (CakePHP-5-Backend), ergänzend `config/` und die Docker-/nginx-Deployment-Konfiguration dort, wo sie einen Code-Fund direkt verstärkt.
**Methodik:** Manuelle Code-Analyse aller Controller, Tables, Entities, Middleware, Authenticator/Identifier und der zentralen Konfiguration, bewertet gegen OWASP Top 10 (2021) und OWASP API Security Top 10.
**Nicht Teil dieser Analyse:** Frontend (`frontend/`), Datenbank-Schema/Migrationen im Detail (dazu existiert bereits eine gesonderte, aktuelle Analyse in `CLAUDE.md`), Abhängigkeits-/CVE-Scan (`composer.lock`).

---

## Update-Hinweis (diese Re-Analyse)

Seit der Erstanalyse wurden fünf Bereiche angefasst: Logdatei/Logging, das `OwnedTable`-Verhalten für `Transactions`, die Apikey-Authentifizierung und der Debug-Level. Alle fünf wurden gegengeprüft. Ergebnis: **vier der ursprünglich kritischen Funde sind faktisch behoben**, einer ist **nur zur Hälfte behoben** (Code-Default korrigiert, aber die produktive Umgebungsvariable überschreibt ihn weiterhin), und die Apikey-Änderung hat **zwei neue, kleinere Findings** eröffnet, die im Zuge dieser Analyse ergänzt wurden. Details siehe unten je Fund.

---

## ✅ Seit der Erstanalyse behoben

| # | Fund | Was wurde geändert | Verifiziert |
|---|---|---|---|
| C1 (alt) | Apikey = Klartext-E-Mail | `users.api_key` ist jetzt ein eigenständiges Feld, per `UserRotateKeyCommand` mit `bin2hex(random_bytes(128))` (1024 Bit CSPRNG-Entropie) erzeugt; `ApikeyIdentifier` prüft jetzt `WHERE api_key = ...` statt `WHERE email = ...`. | ✅ Code gelesen, Migration (`api_key`-Spalte, `string(256)`) vorhanden. |
| C2 (alt) | `TransactionsTable` ohne Eigentümerprüfung | `TransactionsTable extends OwnedTable` (vorher `extends Table`); `transactions.user_id` wurde per Migration ergänzt (FK + Index, `20260125222124_Accounting.php`); `PiggybankController::fetch()`/`save()` fangen jetzt `RecordNotFoundException`/`UnauthorizedException`/`ForbiddenException` rund um den Transaktions-Ownership-Check ab. | ✅ Code + Migration gelesen. Damit greift automatisch auch der `beforeSave`-Zwang aus `UserOwnedBehavior` (s. vorherige Änderung dieser Session) auf `Transactions.user_id`. |
| C3 (alt) | `Authorization`-Header wurden in `requests.log` geloggt | Die `file_put_contents(...)`-Zeile in `Application.php::getAuthenticationService()` wurde entfernt. | ✅ Code gelesen, Zeile ist weg. **Aber:** siehe C1(neu)/Restrisiko unten – die alte Datei liegt noch auf der Platte. |
| W5 (alt) | `password`-Validierung erzwang `numeric` | `UsersTable::validationDefault()` nutzt jetzt `->scalar('password')` statt `->numeric('password')`. | ✅ Code gelesen. |

Damit ist der schwerwiegendste Angriffspfad aus der Erstanalyse (E-Mail-Login + globaler Lese-/Schreibzugriff auf alle Transaktionen) geschlossen. Die verbleibenden und neuen Funde unten sind kleiner im Blast-Radius, aber teils weiterhin praktisch ausnutzbar.

---

## 🔴 Kritisch

### C1 — `DEBUG=true` ist in Produktion weiterhin aktiv (Fix nur teilweise wirksam)

**Wo:** `config/app.php` (Code-Fix vorhanden) vs. `docker-compose.yml` (Override weiterhin aktiv)

Der Default in `config/app.php` wurde korrigiert:

```php
'debug' => filter_var(Cake\Core\env('DEBUG', false), FILTER_VALIDATE_BOOLEAN),
```

Das ist die richtige Änderung für den Fail-Safe-Fall (fehlt die Umgebungsvariable komplett, ist jetzt `debug=false` der Default). **Die tatsächlich laufende Produktivumgebung wird davon aber nicht berührt**, weil `docker-compose.yml` den Wert weiterhin explizit setzt:

```yaml
php.eurokeep:
  environment:
    - DEBUG=true
```

Dieser Wert hat Vorrang vor dem Code-Default. Solange diese Zeile in `docker-compose.yml` steht, läuft die produktive Instanz (`eurokeep.nox.kiwi`) **weiterhin mit vollem Debug-Modus** – jede unbehandelte Exception liefert Stacktrace, Dateipfade und teils Query-Details in der JSON-Antwort. Das betrifft nach wie vor konkret erreichbare Stellen wie `SummaryController::complete()`/`closing()` (`new DateTime($queryParam)` ohne Validierung) oder `PiggybankController::save()`s innere Schleife (s. W3/W6 unten).

**OWASP:** A05:2021 – Security Misconfiguration.

**Empfehlung:** `DEBUG=true` aus `docker-compose.yml` für den `php.eurokeep`-Service entfernen bzw. auf `false` setzen. Der Code-Fix allein reicht nicht – er ist nur das Sicherheitsnetz für den Fall, dass die Variable *fehlt*, nicht dafür, dass sie *falsch gesetzt* ist.

---

### C2 — Stale `requests.log` weiterhin vorhanden und über nginx direkt abrufbar

**Wo:** `var/www/html/requests.log` (Datei), `etc/nginx/conf.d/*.conf` (unverändert, nicht Teil der gemeldeten Fixes)

Das Logging selbst ist behoben (s. oben), aber zwei Dinge sind liegen geblieben:

1. Die Datei `requests.log` existiert weiterhin physisch im Anwendungsverzeichnis und enthält – aus der Zeit vor dem Fix – noch einen zuvor mitgeschnittenen `Authorization`-Header im Klartext.
2. Die nginx-Konfiguration wurde **nicht** geändert: `root /var/www/html/;` ohne Sperre für Dotfiles/Log-/Backup-Dateien ist weiterhin aktiv. Jede Datei im App-Root, die keine `.php`-Endung hat, wird nach wie vor über `location / { try_files $uri $uri/ /index.php?$args; }` direkt ausgeliefert.

In Summe: `GET https://eurokeep.nox.kiwi/requests.log` liefert weiterhin einen alten, potenziell noch gültigen `Authorization`-Header aus – nur die Quelle für *neue* Einträge ist jetzt versiegt. Zusätzlich bleiben `composer.lock`, `README.md`, `phpunit.xml.dist` und die 75-MB-`backup.7z` über denselben Mechanismus erreichbar.

**OWASP:** A05:2021 – Security Misconfiguration; A09:2021 – Security Logging and Monitoring Failures (Restrisiko aus altem Log).

**Empfehlung:**
1. `requests.log` jetzt löschen (der alte Header darin ist ggf. noch ein gültiges Bearer-Credential, s. u. – Rotation der betroffenen Apikeys/Passwörter vorsorglich prüfen).
2. nginx `root` auf `webroot/` beschränken bzw. mindestens `location ~ /\.` sowie `.log`/`.lock`/`.md`/`.7z`/`.env`/`.sql` explizit sperren (`deny all;`). Dieser Punkt war schon in der Erstanalyse offen und wurde von den gemeldeten Fixes nicht adressiert.

---

## 🟠 Wichtig

### W1 — Neu durch den Apikey-Fix: `api_key` fehlt in `User::$_hidden` → Leak über den Login-Response

**Wo:** `src/Model/Entity/User.php`, `src/Controller/AuthController.php::login()`

Die neue, gute Apikey-Implementierung hat einen Nebeneffekt: `User::$_hidden` versteckt weiterhin nur `password`:

```php
protected array $_hidden = [
    'password',
];
```

`api_key` ist nicht gelistet. `AuthController::login()` setzt aber die komplette `User`-Entity in die Response:

```php
$this->set('user', $foundUser);
...
$this->viewBuilder()->setOption('serialize', ['user', 'success', 'errors']);
```

Damit wird der frisch eingeführte, hochsensible `api_key` (funktional gleichwertig zu einem Passwort – er reicht allein für vollen API-Zugriff) bei **jedem Login im Klartext an den Client zurückgegeben**, obwohl er dafür nirgends gebraucht wird (kein Frontend-Feature nutzt ihn). Das vergrößert unnötig die Angriffsfläche: Netzwerklogs, Browser-DevTools, Fehlertracking-Tools (Sentry o. ä.) im Frontend, `localStorage`-Persistenz – überall dort, wo die Login-Response ohnehin schon durchläuft, liegt jetzt zusätzlich ein dauerhaft gültiges Bearer-Credential (kein Session-Timeout, keine CSRF-Bindung).

**OWASP:** A02:2021 – Cryptographic Failures (unnötige Exposition eines Credentials); API3:2023 – Broken Object Property Level Authorization (Excessive Data Exposure).

**Empfehlung:** `api_key` in `User::$_hidden` aufnehmen. Falls das Frontend den Key tatsächlich einmalig anzeigen soll (z. B. nach einer expliziten "Generate API Key"-Aktion), dafür einen eigenen, dedizierten Response-Pfad bauen (analog zum CLI-Command, der ihn genau einmal ausgibt), statt ihn dauerhaft über die normale User-Serialisierung mitzuschleifen.

### W2 — `api_key` wird im Klartext in der Datenbank gespeichert

**Wo:** `src/Model/Table/UsersTable.php::setApiKey()`, `src/Model/Entity/User.php`

```php
public function setApiKey(User $user, string $apiKey): void
{
    $user->api_key = $apiKey;
    ...
}
```

Anders als `password` (gehasht über `_setPassword()`-Mutator mit `DefaultPasswordHasher`) gibt es für `api_key` keinen Hash-Mutator – der Wert landet 1:1 im Klartext in der Spalte `users.api_key`. Die Entropie des Keys selbst ist mit 1024 Bit CSPRNG exzellent (Brute-Force ist kein realistisches Risiko), das Restrisiko ist ein **DB-Kompromittierungsszenario** (Backup-Leak, SQL-Injection an anderer Stelle, Insider-Zugriff): Wer die Datenbank lesen kann, hat direkt nutzbare Bearer-Credentials für jeden Nutzer, statt "nur" Passworthashes, die zumindest noch geknackt werden müssten.

**OWASP:** A02:2021 – Cryptographic Failures.

**Empfehlung:** `api_key` wie ein Passwort behandeln: beim Speichern hashen (z. B. `hash('sha256', $apiKey)` reicht hier, da der Key selbst schon hochentropisch ist – ein teurer Passwort-Hash wie bcrypt ist nicht nötig und würde nur den Lookup verlangsamen), beim Identifizieren denselben Hash berechnen und danach suchen (`WHERE api_key_hash = hash('sha256', $submitted)`). Der Klartext wird dann nur noch einmalig bei Erzeugung angezeigt (das leistet `UserRotateKeyCommand` bereits korrekt).

### W3 — Cross-Tenant-Referenzen bei `account_id` weiterhin ungeprüft (jetzt auch für `Transactions` relevant)

**Wo:** `src/Controller/TransactionsController.php::add()`/`edit()`, `src/Controller/SubscriptionController.php::add()`, `src/Controller/PiggybankController.php::add()`

Dieser Fund aus der Erstanalyse besteht in der Sache fort, hat sich durch den `Transactions`-Fix aber leicht verschoben:

- `user_id` wird für `Transactions`/`Budget`/`Subscription`/`Piggybank` inzwischen zuverlässig durch `UserOwnedBehavior::beforeSave()` erzwungen (aus einer vorangegangenen Änderung dieser Session) – dieser Teil ist also bereits abgesichert.
- `account_id` bleibt in allen vier Controllern weiterhin clientgesteuert und wird **nicht** gegen den Besitz des Aufrufers geprüft (nur die `existsIn`-Regel: das Konto muss irgendwo existieren, nicht dem Aufrufer gehören).

Für `TransactionsController::add()` ergibt sich dadurch jetzt ein konkreter, hässlicherer Fehlermodus als vorher: Legt ein Nutzer eine Transaktion mit einem fremden `account_id` an, wird die Zeile zunächst **erfolgreich in die DB geschrieben** (der `existsIn`-Check lässt es zu), und erst danach schlägt `TransactionsTable::_onSaveSuccess()` beim automatischen Neuberechnen des Kontostands fehl, weil `Transaction::getAccount()` → `AccountTable::get()` jetzt (dank C2-Fix) eine `UnauthorizedException` wirft, sobald das Konto einem anderen Nutzer gehört. Diese Exception ist **nicht abgefangen** → Ergebnis: eine verwaiste, fremdem Konto zugeordnete Transaktion bleibt in der DB stehen, während der Request mit einer (dank C1 aktuell live sichtbaren) 401-Exception crasht, statt sauber mit 400/403 abgelehnt zu werden.

**OWASP:** A01:2021 – Broken Access Control; A08:2021 – Software and Data Integrity Failures (inkonsistenter Teilerfolg).

**Empfehlung:** Vor dem `patchEntity()`/`save()` in allen vier `add()`-Methoden (und in `TransactionsController::edit()`) explizit `$this->Account->get($data['account_id'])` aufrufen (nutzt die vorhandene `OwnedTable`-Prüfung, wirft sauber `UnauthorizedException` *vor* jedem DB-Write) und bei Fehlschlag mit 400/403 antworten, statt das der zufälligen Reihenfolge der Save-Callbacks zu überlassen.

### W4 — Keine Autorisierungs-/Rollenprüfung trotz vorhandenem Rollenmodell

**Wo:** `src/Controller/AccounttypeController.php`, `src/Controller/CategoriesController.php`, `src/Model/Table/RoleTable.php`

Unverändert seit der Erstanalyse. Es existiert ein `role_id` auf `users` und eine `Role`-Tabelle, aber nirgends im Code wird `role_id` ausgelesen oder geprüft. `Accounttype` und `Categories` sind systemweit geteilte Referenzdaten (keine `user_id`-Spalte) – trotzdem kann jeder eingeloggte Nutzer sie über `add/edit/delete` verändern oder löschen, wodurch andere Nutzer betroffen sind.

**OWASP:** A01:2021 – Broken Access Control (Missing Function Level Access Control).

**Empfehlung:** Wie zuvor – entweder pro Nutzer scopen (falls fachlich gewollt) oder Schreibzugriff auf eine Admin-Rolle beschränken (`role_id`-Check bzw. `cakephp/authorization`).

### W5 — Kein Brute-Force-/Rate-Limiting-Schutz beim Login

**Wo:** `src/Controller/AuthController.php`

Unverändert. `login()` erlaubt beliebig viele Versuche ohne Sperre, Backoff, Captcha oder IP-/Account-Throttling.

**OWASP:** A07:2021 – Identification and Authentication Failures.

**Empfehlung:** Rate-Limiting pro IP/Account (Redis ist laut Compose-Topologie ohnehin vorhanden), progressive Verzögerung, Logging fehlgeschlagener Logins.

### W6 — Unbehandelte Exception im inneren Loop von `PiggybankController::save()`

**Wo:** `src/Controller/PiggybankController.php::save()`

Der äußere Ownership-Check dieser Methode (`$transactionsTable->get($transactionId)`) ist jetzt korrekt mit try/catch abgesichert (siehe „Behoben" oben). Innerhalb der Schleife über `$data['parts']` bleibt aber:

```php
foreach ($data['parts'] as $piggybankId => $balance) {
    $piggybank = $this->Piggybank->get($piggybankId);
    ...
}
```

`$this->Piggybank->get($piggybankId)` (Ownership-geprüft über `OwnedTable::get()`) wird hier weiterhin ungefangen aufgerufen. Ein `piggybankId` aus fremdem Besitz im Client-Payload `parts` führt zu einer unbehandelten `UnauthorizedException` mitten in der Schleife (teilweise bereits verarbeitete `parts` sind zu diesem Zeitpunkt ggf. schon gespeichert – kein Transaktionsrahmen um die Schleife).

**OWASP:** A09:2021 – Security Logging and Monitoring Failures; A01:2021 – Broken Access Control (sauberer Ablehnungspfad fehlt).

**Empfehlung:** Gleiches try/catch wie beim äußeren Check ergänzen, idealerweise die gesamte Schleife in eine DB-Transaktion wickeln, damit ein Abbruch mitten in `parts` nicht einen inkonsistenten Teilzustand hinterlässt.

### W7 — Tote `CorsMiddleware` mit riskanter Konfiguration

**Wo:** `src/Middleware/CorsMiddleware.php`

Unverändert, weiterhin nicht in `Application::middleware()` eingebunden (aktuell kein aktives Risiko), aber weiterhin mit `allowCredentials()` + `allowMethods(['*'])` + hartkodierter `localhost`-Origin – ein Muster, das bei versehentlicher Reaktivierung leicht zur klassischen CORS-Schwachstelle wird.

**Empfehlung:** Datei entfernen oder mit expliziter Origin-Allow-List versehen, falls sie doch gebraucht wird.

---

## 🟡 Verbesserung

- **Kein Unique-Index auf `users.api_key`.** Bei 1024 Bit CSPRNG-Entropie ist eine Kollision praktisch ausgeschlossen, ein `UNIQUE`-Index wäre aber die korrekte Absicherung *und* beschleunigt zugleich den aktuell unindizierten `WHERE api_key = ...`-Lookup in `ApikeyIdentifier` (Full-Table-Scan bei jedem Apikey-Request).
- **`StatsController`** exportiert Finanz-Metriken im Prometheus-Textformat über den normalen User-Auth-Pfad (Session oder Apikey). Weiterhin laut `CLAUDE.md` nicht ans Frontend angebunden/WIP, aber live erreichbar. Vor Produktivnahme dedizierten Metrics-Auth-Mechanismus vorsehen statt Wiederverwendung der User-Authentifizierung.
- **`Piggybank::save()`** enthält weiterhin hartkodierte Werte (`'account_id' => 20`, `'user_id' => 1`) – WIP-Leftover, kein direktes Sicherheitsrisiko für Dritte, aber ein Datenintegritätsrisiko.
- **CSRF nur für den Session-Pfad wirksam** – nach wie vor korrekt so (Bearer-Auth ist von Natur aus CSRF-resistent), jetzt mit einem echten Secret hinter dem Apikey-Pfad ohnehin weniger relevant als zuvor.
- **Fehlende Response-Header-Hardening** (`X-Content-Type-Options`, `X-Frame-Options`) – unverändert, geringe Ausnutzbarkeit bei reiner JSON-API, aber günstige Defense-in-Depth-Maßnahme.
- **Secrets im Klartext** (`Security.salt`, DB-Passwort) in `docker-compose.yml`/`config/app_local.php` – unverändert seit der Erstanalyse, nicht Teil der gemeldeten Fixes. Weiterhin offen: keine echten Secrets als hartkodierter `env()`-Fallback verwenden, Secrets aus einem Secret-Store beziehen.
- **DB-seitige Integritätslücken** (fehlende Foreign Keys/Indizes an anderer Stelle, `subscription.*_id` mit `default => 1`, etc.) – bereits in `CLAUDE.md` dokumentiert, hier nur informativ referenziert.

---

## OWASP-Mapping (aktueller Stand)

| OWASP-Kategorie | Betroffene Funde |
|---|---|
| A01 – Broken Access Control | W3 (Cross-Tenant `account_id`), W4 (fehlendes RBAC), W6 (Piggybank-Loop) |
| A02 – Cryptographic Failures | W1 (api_key-Leak im Login-Response), W2 (api_key im Klartext), Secrets im Klartext (Verbesserung) |
| A05 – Security Misconfiguration | C1 (Debug in Prod, Compose-Override), C2 (Datei-Exposure via nginx), W7 (CORS-Zeitbombe) |
| A07 – Identification & Authentication Failures | W5 (kein Brute-Force-Schutz) |
| A08 – Software and Data Integrity Failures | W3 (inkonsistenter Teilerfolg bei Transactions) |
| A09 – Security Logging and Monitoring Failures | C2 (Alt-Log noch abrufbar), W6 (fehlendes Error-Handling) |
| API3:2023 – Broken Object Property Level Authorization | W1 (Excessive Data Exposure: api_key im Response) |

---

## Priorisierte nächste Schritte

1. **`docker-compose.yml`: `DEBUG=true` beim `php.eurokeep`-Service entfernen/auf `false` setzen.** (C1 – Minuten, der Code-Fix allein greift nicht, solange das hier überschrieben wird.)
2. **`requests.log` löschen und nginx `root` auf `webroot/` beschränken** (bzw. Dotfiles/`.log`/`.lock`/`.7z` sperren). (C2 – < 1 Stunde, schließt die letzte konkrete Zero-Auth-Credential-Exposition.)
3. **`api_key` in `User::$_hidden` aufnehmen**, damit der neu eingeführte, hochsensible Wert nicht weiter über den Login-Response nach außen geht. (W1 – Minuten.)
4. **`api_key` gehasht statt im Klartext speichern** (`UsersTable::setApiKey()`/`ApikeyIdentifier`). (W2 – kleiner Umbau.)
5. **`account_id`-Ownership-Check vor jedem Save bei `Transactions`/`Subscription`/`Piggybank`** ergänzen, inkl. sauberem 400/403 statt des aktuellen Teil-Erfolg-dann-Crash-Verhaltens bei `Transactions::add()`. (W3.)
6. Danach: W4–W7 (RBAC auf globalen Referenzdaten, Login-Rate-Limiting, Piggybank-Loop-Exception, tote CORS-Middleware) sowie die weiterhin offenen Secrets-im-Klartext aus der Erstanalyse.

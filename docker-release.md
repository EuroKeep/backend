# EuroKeep als Docker-Image veröffentlichen

Ein Image enthält das komplette Produkt: Angular-Frontend, nginx und PHP-FPM/CakePHP.
Von außen braucht es nur eine **MariaDB** (Redis/Memcached werden nicht verwendet, der Cache liegt im Dateisystem).

```
Browser ──HTTPS──> Reverse-Proxy (TLS) ──HTTP:8080──> [ eurokeep-Image: nginx → PHP-FPM ] ──> MariaDB
```

## ⛔ Vor dem ersten öffentlichen Release zu erledigen

1. **Frontend ist noch nicht versioniert.** Es liegt als eigenständiges (künftiges) Repository unter `/dockerhub/docker/eurokeep/frontend` und kommt als Build-Context `frontend=./frontend` ins Image. Das Image enthält den Frontend-Stand, der gerade auf der Platte liegt. Für nachvollziehbare Releases muss das Frontend-Repo angelegt und ein definierter Stand ausgecheckt sein.

Alles andere ist getestet: frische DB, Migrationen, Seeds, Benutzer, Login, API und Healthcheck.

**Datenbank-Schema (Stand 2026-09-28):** Eine einzige klassische Migration `config/Migrations/20260928215459_Initial.php` (erzeugt mit `bin/cake bake migration_snapshot` aus der Produktionsdatenbank) enthält das komplette Schema. Neuinstallationen bekommen alle Tabellen. Auf bestehenden Datenbanken (Tabelle `users` vorhanden) wird sie nur als erledigt eingetragen und ändert nichts. Das Seed `config/Seeds/DefaultDataSeed.php` füllt Rollen, Kontotypen, Zahlungsarten und Kategorien, aber nur leere Tabellen. Die alten Migrationen und Seeds liegen in `var/www/migrations-archive-2026-09-28/` (außerhalb des Repos). Auf bestehenden Datenbanken zeigt `bin/cake migrations status` sie als `MISSING` an. Das ist harmlos und lässt sich bei Bedarf mit `DELETE FROM cake_migrations WHERE version < 20260928215459;` bereinigen.

## Was ist wo?

| Datei | Zweck |
|---|---|
| `docker/php/Dockerfile` (in `/dockerhub/docker/eurokeep/`) | Build-Ziel `standalone` = Gesamt-Image. Ohne `--target` entsteht wie bisher das reine PHP-Backend-Image (`runtime`) für die Development-Stacks (`*-development.yml`). |
| `.dockerignore` | Hält Secrets (`config/app_local.php`, `.env`), `.git` und Build-Reste aus dem Image. |
| `frontend/` (in `/dockerhub/docker/eurokeep/`) | Angular-Frontend, eigenes Repository. Build-Context `frontend`, mit eigener `.dockerignore` (`node_modules/`, `dist/`, `.angular/`). |
| `docker/standalone/etc/nginx/nginx.conf` | nginx im Container: `/a/` = Angular, alles andere = API. Nur `webroot/` ist öffentlich. |
| `docker/standalone/usr/local/bin/eurokeep-start` | Startet nginx und PHP-FPM. Stirbt einer davon, beendet sich der Container. |
| `standalone-production.yml` / `integrated-production.yml` (in `/dockerhub/docker/eurokeep/`) | **Werden veröffentlicht.** Gesamt-Image mit eigener MariaDB bzw. mit externer DB, ziehen nur fertige Images. |
| `traefik.yml` | **Wird veröffentlicht.** Drop-In für Betrieb hinter Traefik. |
| `.env.example` | **Wird veröffentlicht.** Vorlage für die `.env` der Nutzer. |
| `build.yml` | Nur lokal: baut das Gesamt-Image aus `docker/` statt es zu ziehen. |

**Zwei Umgebungen:** `docker/` (Production, daraus entsteht das veröffentlichte Image) und `docker-development/` (Entwicklung) enthalten jeweils eine eigene Kopie von Dockerfile und Standalone-Konfiguration. Änderungen am Image immer in **beiden** Verzeichnissen machen.

```sh
# Production-Image bauen (für den Release), siehe Schritt 1
# Entwicklungs-Image bauen und lokal testen (Projekt "eurokeep-standalone", berührt den normalen Stack nicht):
docker compose --env-file <test.env> -f standalone-production.yml -f build.yml up -d --build
```

## 1. Image bauen

```sh
# aus /dockerhub/docker/eurokeep
docker build -f docker/php/Dockerfile --build-context docker=./docker \
    --build-context frontend=./frontend \
    --target standalone -t eurokeep/eurokeep:latest backend
```

Das Frontend wird im Build aus `./frontend` mitgebaut (Node 26, dauert einige Minuten). Ein vorheriges `npm run build` ist nicht nötig.

## 2. Konfiguration (`.env`)

Die Umgebungsvariablen des **Images** (für `docker run`):

```sh
DATABASE_URL=mysql://eurokeep:<db-passwort>@db:3306/eurokeep?encoding=utf8mb4
SECURITY_SALT=<Ausgabe von: openssl rand -hex 32>
APP_FULL_BASE_URL=https://eurokeep.example.org
DEBUG=false
```

| Variable | Pflicht | Bedeutung |
|---|---|---|
| `DATABASE_URL` | ja | Verbindung zur MariaDB. |
| `SECURITY_SALT` | ja | Zufallswert. **Nach dem ersten Start nie mehr ändern.** |
| `APP_FULL_BASE_URL` | ja | Öffentliche Adresse inkl. `https://`. **Fehlt sie, antwortet die API mit leeren Seiten** (Schutz gegen Host-Header-Angriffe in `config/bootstrap.php`). |
| `DEBUG` | nein | Standard `false`. In Produktion nie `true`, sonst gibt die App Stacktraces aus. |

Mit `standalone-production.yml` sieht die `.env` etwas anders aus (Vorlage: `.env.example`): Statt `DATABASE_URL` gibt es `DATABASE_NAME`, `DATABASE_USER` und `DATABASE_USER_PW`, daraus setzt die Compose-Datei die URL selbst zusammen und legt die Datenbank passend an. Dazu kommen `EUROKEEP_VERSION` (Image-Version) und `EUROKEEP_PORT` (Host-Port).

`.env` gehört **nie** in Git oder ins Image. Fehlen `DATABASE_URL` oder `SECURITY_SALT`, bricht der Container absichtlich mit einer Fehlermeldung ab.

## 3. Erstinstallation (einmalig)

Migrationen und Seeds (Rolle, Kontotypen, Kategorien) laufen bei jedem Containerstart automatisch (`docker-entrypoint.sh`, abschaltbar mit `EUROKEEP_MIGRATE=0` / `EUROKEEP_SEED=0`). Von Hand bleibt nur der erste Benutzer:

```sh
docker run --rm -it --env-file .env eurokeep/eurokeep:latest bin/cake user_add   # fragt E-Mail, Name, Passwort
```

Die Seeds sind idempotent: Tabellen, in denen schon Daten stehen, werden übersprungen.
Liegt die Datenbank in einem Docker-Netz, gehört noch `--network <netz>` in jeden Aufruf.

## 4. Starten

```sh
docker run -d --name eurokeep --env-file .env -p 8080:8080 --restart unless-stopped \
    -v eurokeep-uploads:/var/www/html/uploads eurokeep/eurokeep:latest
```

**`-v …:/var/www/html/uploads` nicht vergessen:** Dort liegen die hochgeladenen Abo-Logos. Ohne benanntes Volume legt Docker ein anonymes an, das beim nächsten Update nicht mehr am neuen Container hängt.

App: `https://<deine-domain>/a/`. Der Container spricht nur HTTP auf Port 8080, TLS übernimmt der Reverse-Proxy (Traefik, Caddy, nginx).

> **Login funktioniert nur über HTTPS.** Session-Cookies sind `secure`. Über `http://localhost:8080` meldet der Login „erfolgreich“, aber der Browser vergisst die Session sofort wieder.

## 5. Veröffentlichen (Docker Hub)

Images landen in der Docker-Hub-Organisation `eurokeep`:

| Image | Inhalt |
|---|---|
| `eurokeep/eurokeep` | Gesamt-Image (Build-Ziel `standalone`) |
| `eurokeep/app` | Nur PHP-FPM-Backend (Build-Ziel `runtime`; die Development-Stacks bauen ihre eigene `:dev`-Variante) |
| `eurokeep/nginx` | nginx mit gebautem Frontend (derzeit von keiner Compose-Datei verwendet) |

```sh
docker login
VERSION=1.0.0
docker build -f docker/php/Dockerfile --build-context docker=./docker \
    --build-context frontend=./frontend \
    --build-arg EUROKEEP_VERSION=$VERSION --build-arg VCS_REF=$(git -C backend rev-parse --short HEAD) \
    --target standalone -t eurokeep/eurokeep:$VERSION -t eurokeep/eurokeep:latest backend
# Secret-Check (siehe "Gut zu wissen") und Smoke-Test mit standalone-production.yml, dann:
docker push eurokeep/eurokeep:$VERSION
docker push eurokeep/eurokeep:latest
```

`EUROKEEP_VERSION` und `VCS_REF` landen als OCI-Labels im Image (`docker inspect`, Docker Hub).

Die Beschreibung der Docker-Hub-Seite steht in `dockerhub-overview.md` (in `/dockerhub/docker/eurokeep/`). Unter hub.docker.com → `eurokeep/eurokeep` → *Overview* einfügen. Die Compose-Datei darin ist eine gekürzte Kopie von `standalone-production.yml`. Wer die ändert, passt sie dort mit an.

Danach auf der Website veröffentlichen: `standalone-production.yml`, `integrated-production.yml`, `traefik.yml` und `.env.example` (Nutzer speichern sie als `.env`). Nur diese Dateien, nie die `.env` dieses Servers.

- **Tags:** Solange es keine Versionen gibt, nur `latest`. Später zusätzlich eine feste Version (`1.0.0`) und diese nie überschreiben.
- **ARM-Server** (z. B. Raspberry Pi): Der Build-Host kann derzeit nur `linux/amd64`. Für arm64 einmalig die QEMU-Emulation installieren (`docker run --privileged --rm tonistiigi/binfmt --install arm64`, der Frontend-Build dauert damit deutlich länger), dann für beide Architekturen bauen und direkt pushen und in `dockerhub-overview.md` unter *Architectures* `linux/arm64` ergänzen:
  `docker buildx build --platform linux/amd64,linux/arm64 -f docker/php/Dockerfile --build-context docker=./docker --build-context frontend=./frontend --target standalone -t eurokeep/eurokeep:latest --push backend`

## 6. Betrieb mit Docker Compose (so bekommen es die Nutzer)

Die veröffentlichte `standalone-production.yml` enthält das Gesamt-Image und MariaDB, mit benannten Volumes für Datenbank, Logos und Logs. MariaDB hat einen Healthcheck, EuroKeep startet erst, wenn die Datenbank bereit ist.

```sh
cp .env.example .env      # ausfüllen: APP_FULL_BASE_URL, SECURITY_SALT, DATABASE_USER_PW
docker compose -f standalone-production.yml up -d
docker compose -f standalone-production.yml exec eurokeep bin/cake user_add
```

Fehlt ein Pflichtwert in der `.env`, bricht `docker compose` mit einer Meldung ab, bevor irgendetwas startet.

**Update auf eine neue Version:**
1. Backup: `docker compose -f standalone-production.yml exec db mariadb-dump -u eurokeep -p eurokeep > backup.sql`
2. `EUROKEEP_VERSION` in der `.env` erhöhen
3. `docker compose -f standalone-production.yml pull && docker compose -f standalone-production.yml up -d` (Migrationen laufen beim Start automatisch)

## Gut zu wissen

- **Logs:** Zugriffe und PHP-FPM-Meldungen landen in `docker logs`. App-Fehler schreibt CakePHP nach `logs/error.log`. Die Compose-Datei hängt dafür das Volume `logs` ein, bei `docker run` geht die Datei ohne eigenes Volume mit dem Container verloren.
- **Sicherheit:** Das Image läuft als unprivilegierter Benutzer (`uid 10001`), nur `webroot/` ist per HTTP erreichbar, gefährliche PHP-Funktionen (`exec` usw.) sind gesperrt. Deshalb zeigt `bin/cake` ohne Argumente einen Fehler statt der Hilfe. Die Befehle selbst funktionieren.
- **Vor jedem Push prüfen, dass keine Secrets im Image stecken:**
  `docker run --rm --entrypoint cat eurokeep/eurokeep:latest config/app_local.php` muss Platzhalter wie `__SALT__` zeigen.

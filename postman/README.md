# Postman — Sloneek API

Import these two files into Postman:

1. `Sloneek-API.postman_collection.json`
2. `Sloneek-Local.postman_environment.json`

Select environment **Sloneek — Local (Sail)**.

## Quick test flow

1. `./vendor/bin/sail up -d`
2. `./vendor/bin/sail artisan queue:work`
3. Postman: **0. Setup → Login** → **0. Setup → Me**
4. Postman: **Articles → Create article**
5. Terminal: `./vendor/bin/sail artisan articles:dispatch-digests --cutoff="2030-01-01 00:00:00"`
6. Browser: http://localhost:8025

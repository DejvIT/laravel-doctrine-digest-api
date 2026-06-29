# Postman — Sloneek API

Import these two files into Postman:

1. `Sloneek-API.postman_collection.json`
2. `Sloneek-Local.postman_environment.json`

Select environment **Sloneek — Local (Sail)**.

## Setup

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan queue:work   # keep running in a separate terminal
```

## Test article distribution

1. Postman: **0. Setup → Login** → **0. Setup → Me**
2. Postman: **Articles → Create article**
3. Dispatch digests (use single quotes — avoids shell `dquote>` issues):

```bash
./vendor/bin/sail artisan articles:dispatch-digests --cutoff='2030-01-01 00:00:00'
```

4. Open http://localhost:8025 and search for your article title.

### How dispatch works

`articles:dispatch-digests` always queues **one job per subscriber in the database** (e.g. 100 after full seed, 3 after `DistributionDemoSeeder` only).

Each job checks whether that subscriber follows the article's category:

- **Match** → digest email is sent
- **No match** → job finishes with no email

So the command does not target a subset of subscribers — it runs the full subscriber list, and category filtering happens inside each job.

The `--cutoff` value only selects which undistributed articles are included (`created < cutoff`). It does not limit which subscribers are processed.

## Optional: predictable multi-subscriber demo

Use this when you want exactly 3 subscribers on one category and 2 seeded articles — easier to verify the multi-subscriber fix without noise from the full dataset.

**Best on a fresh database** (otherwise you still dispatch jobs for every subscriber already seeded):

```bash
./vendor/bin/sail artisan db:seed --class=DistributionDemoSeeder
```

Then run steps 1–4 above, or skip Postman and dispatch directly after seeding. In Mailpit, search for `distribution.subscriber` — expect **3 emails**, each with **2 articles**.

Demo credentials:

- Blogger: `distribution.blogger@example.com` / `password`
- Subscribers: `distribution.subscriber1@example.com`, `distribution.subscriber2@example.com`, `distribution.subscriber3@example.com`

Automated test:

```bash
./vendor/bin/sail test --filter=DistributionTest
```

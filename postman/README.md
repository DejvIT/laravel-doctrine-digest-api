# Postman — Sloneek API

Import these two files into Postman:

1. `Sloneek-API.postman_collection.json`
2. `Sloneek-Local.postman_environment.json`

Select environment **Sloneek — Local (Sail)**.

## Setup

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan doctrine:migrations:migrate
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan queue:work   # keep running in a separate terminal
```

Doctrine migrations create domain tables (bloggers, articles, categories, …). Laravel migrations create queue/cache infrastructure (`jobs`, `job_batches`, `cache`).

## Test article distribution

1. Postman: **0. Setup → Login** → **0. Setup → Me**
2. Postman: **Articles → Create article**
3. Dispatch digests (use single quotes — avoids shell `dquote>` issues):

```bash
./vendor/bin/sail artisan articles:dispatch-digests --cutoff='2030-01-01 00:00:00'
```

4. Open http://localhost:8025 and search for your article title.

### How dispatch works

`articles:dispatch-digests` queues **one job per subscriber who follows at least one category** with undistributed articles before the cutoff.

Each job loads articles for that subscriber's subscribed categories:

- **Match** → digest email is sent
- **No match** → job finishes with no email (should not happen for selected subscribers)

If undistributed articles exist but **no subscriber follows their categories**, the command skips email jobs and runs finalize directly — articles are still marked as distributed.

The `--cutoff` value selects which undistributed articles are included (`created < cutoff`).

## Optional: predictable multi-subscriber demo

Use this when you want exactly 3 subscribers on one category and 2 seeded articles — easier to verify the multi-subscriber fix without noise from the full dataset.

**Best on a fresh database** (otherwise other seeded subscribers may also receive digests):

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

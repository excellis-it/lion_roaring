# Production Rules & Deploy Instructions — Lion Roaring

**Audience: any AI coding assistant or developer touching the live server.**
Read this file **before** any action that reaches production. Every rule here is mandatory unless
the human operator explicitly overrides it in the current conversation.

---

## 0. The one rule that outranks the rest

> **Never push anything to production without asking first.**

When the operator says *"upload to production"*, *"deploy"*, *"push it live"*, *"fix it on the
server"* — **stop and ask for confirmation before the first write**, and state in the question:

1. Exactly **which files** will change (full list, no "and related files").
2. What each change does, in one line.
3. Whether anything **destructive or outward-facing** is involved (DB writes, emails, queue jobs,
   cache/config clears, service restarts).
4. The **rollback** path.

Proceed only after an explicit yes. A "yes" covers **that** deploy only — the next one needs its
own confirmation. Reading, diffing and dry-running on production never needs confirmation;
**writing does**.

---

## 1. Environments

| | Path / address |
|---|---|
| SSH | `ssh lion-roaring-production` (passwordless, key `~/.ssh/id_ed25519`) |
| App root (live) | `/var/www/lionroaring_org/project` |
| Local backend (source of truth for new work) | `/Volumes/CrucialMacExt/MacOffload/MAMP_htdocs/lion_roaring` |
| Flutter app repo | `/Users/excellisitmacmini/masum/flutter_apps/lion-roaring-app` |
| Backups | `/root/code-backups/<timestamp>/` |
| Sites served | lionroaring.org (Global) **and** lionroaring.us — one codebase, two vhosts |
| Apache logs | `/var/log/apache2/lionroaring_org_ssl_access.log*` (per-vhost; API traffic is **not** in `access.log`) |
| Log dashboard | `https://lionroaring.org/logs-monitor.php` |

**The production app directory is not a git repo.** Deploys are file copies. There is no
`git pull`, no branch, no revert — the backup directory *is* the undo button.

**Production code lags the local backend.** Local has features production does not.
**Never copy a whole file from local to production.** Always port the specific change
(see §3).

---

## 2. Data safety — non-negotiable

The production database holds live member, payment and subscription records.

**Never run, on production:**

- `migrate:fresh`, `migrate:refresh`, `migrate:reset`, `db:wipe`, `db:seed`
- `TRUNCATE` / `DELETE` / `DROP` on any application table
- Any `UPDATE` against real member data
- `php artisan optimize:clear` without checking what is cached first
- Anything that dispatches mail, notifications or queue jobs — **ask first**, these reach real people

**Requires explicit, specific confirmation each time (never assume):**

- Any schema change (`php artisan migrate`) — state which migrations will run
- Deleting or truncating **any** table, including diagnostic ones (Telescope, logs, sessions)
- Editing `.env`
- Restarting Apache, PHP-FPM, supervisor, or the queue worker
- Running a command that sends email (e.g. `subscription:send-reminder` deactivates accounts
  after 3 reminders)
- Clearing the cache when the app uses it for in-flight state (registration payment intents are
  cached for 30 minutes)

**Always allowed without asking:** reading files, `SELECT` queries, `php -l`, `git diff`,
tailing logs, `supervisorctl status`, `md5sum`.

Before any DB-touching step, take a dump first:

```bash
ssh lion-roaring-production 'cd /var/www/lionroaring_org/project && \
  mysqldump --single-transaction --quick $(php artisan tinker --execute="echo config(\"database.connections.mysql.database\");" | tail -1) \
  | gzip > /root/code-backups/db-$(date +%Y%m%d-%H%M%S).sql.gz'
```

---

## 3. Deploy procedure (the only approved one)

Per file, never in bulk. `opcache.validate_timestamps=On`, so a copied file takes effect
immediately — **no restart needed, and none should be done casually.**

```bash
TS=$(date +%Y%m%d-%H%M%S)
REL=app/Services/Example.php          # repeat per file

# 1. Fetch the LIVE version — never assume it matches local
scp lion-roaring-production:/var/www/lionroaring_org/project/$REL /tmp/prod/$REL

# 2. Diff against local and port ONLY the intended change into the prod copy.
#    Watch line endings: several production files are CRLF. Preserve them.
diff /tmp/prod/$REL ./$REL

# 3. Syntax check the patched copy
php -l /tmp/prod/$REL

# 4. Back up the live file BEFORE overwriting
ssh lion-roaring-production "mkdir -p /root/code-backups/$TS/$(dirname $REL) && \
  cp -p /var/www/lionroaring_org/project/$REL /root/code-backups/$TS/$REL"

# 5. Upload
scp /tmp/prod/$REL lion-roaring-production:/var/www/lionroaring_org/project/$REL

# 6. Verify byte-for-byte + syntax on the server
md5 -q /tmp/prod/$REL
ssh lion-roaring-production "cd /var/www/lionroaring_org/project && md5sum $REL && php -l $REL"
```

Blade/view changes also need `php artisan view:clear`. Config changes need
`php artisan config:clear`. Nothing else is cleared without asking.

### Rollback

```bash
ssh lion-roaring-production "cp -p /root/code-backups/<TS>/<path> /var/www/lionroaring_org/project/<path>"
```

Never delete a backup directory.

---

## 4. Mandatory post-deploy check — scheduler & worker

**Run this after every production modification, no exceptions**, even for a one-line change.
Supervisor has silently broken before: a missing `directory=` left `schedule:work` failing every
minute for 47 days, so no scheduled command ran at all.

```bash
ssh lion-roaring-production "supervisorctl status; \
  echo '--- scheduler ---'; tail -c 400 /var/www/lionroaring_org/project/storage/logs/scheduler.log; \
  echo '--- worker ---';    tail -c 200 /var/www/lionroaring_org/project/storage/logs/worker.log; \
  ps -eo pid,cmd | grep -E 'schedule:work|queue:work' | grep -v grep"
```

**Healthy looks like:**

```
laravel-scheduler                  RUNNING   pid ...., uptime ...
laravel-worker:laravel-worker_00   RUNNING   pid ...., uptime ...
[<timestamp>] Execution #N output:
No scheduled commands are ready to run.
```

**Broken looks like** `Could not open input file: artisan`, `STOPPED`, `FATAL`, `BACKOFF`, or an
execution counter that has reset and is climbing with errors. If broken, report it — do not
restart supervisor without asking (a restart can release a backlog of member emails).

Supervisor configs: `/etc/supervisor/conf.d/laravel-scheduler.conf`, `laravel-worker.conf`.
Both must carry `directory=/var/www/lionroaring_org/project`.

Also confirm the site still answers:

```bash
ssh lion-roaring-production "curl -sk -o /dev/null -w 'org=%{http_code}\n' https://lionroaring.org/; \
                             curl -sk -o /dev/null -w 'us=%{http_code}\n'  https://lionroaring.us/"
```

Then check `storage/logs/laravel.log` (or the log dashboard) for new errors caused by the change.

---

## 5. Audit before you touch anything

Investigation is read-only. Do not "fix while looking".

- Reproduce from evidence: Apache access logs, `laravel.log`, DB rows — not assumptions.
- Confirm which of the two sites (`.org` / `.us`) and which user type (`Global`, `G_R`,
  `Regional`, SUPER ADMIN) the report came from. Behaviour differs per host and per role.
- When a listing shows a row that 404s on open, suspect scope drift between `index()`,
  `fetchData()` and `edit()` — they must use the same country rule.
- Verify a fix on production with a **read-only** probe (reflection on a service method, a
  `SELECT`, a `curl` of a GET endpoint) before declaring it done.

## 6. Audit your own changes before reporting done

Every deploy gets a self-review pass looking for:

- Comparisons that are case- or type-sensitive where the data is not (promo codes, emails)
- Typed parameters reached from validation callbacks, where a bad type turns a 422 into a 500
- New strictness applied to existing rows — check the live data actually satisfies it
- Money paths: never charge, capture or refund on a path that has not passed every check
- Behaviour that differs between the released mobile app and the new one (the released build
  cannot be changed — a backend fix must be backward compatible)

---

## 7. Known production quirks

| Thing | State | Note |
|---|---|---|
| `APP_ENV` | `local` on production | Makes some framework code take the dev path. Do not "fix" without discussing. |
| `APP_DEBUG` | `false` | Keep it false. |
| Telescope | **Disabled** (`config/telescope.php` defaults to `false`) | It once grew to 17M rows / 14 GB and slowed the whole site. Never re-enable on production. |
| Queue | `database`, table **`custom_jobs`** | The `jobs` table is the app's job-postings feature, unrelated. |
| Sessions | `database` | |
| Cache | `file` | Registration payment intents live here for 30 min — clearing cache mid-checkout can strand a member. |
| Log dashboard | `public/logs-monitor.php` | Password-protected; reads `storage/logs/*.log`. |
| Payments | Stripe, manual capture on registration | Card is authorised, captured only after every check passes. Never move a capture earlier. |
| API status codes | `200` = success, `201` = **failure** | Do not "fix" a `statusCode == 201 -> error` check. |
| PHP | 8.2 + php-fpm, `opcache.validate_timestamps=On` | File edits apply without restart. |

---

## 8. Secrets

Never print, copy, commit or paste `.env` contents, Stripe keys, DB passwords or Passport keys
into a chat, a file, a commit message or a log. Read config through `config()` in tinker instead
of reading `.env`. Never regenerate Passport keys — live mobile tokens depend on them.

---

## 9. Reporting back

After a deploy, report: files changed (with paths), the backup directory, verification output
(md5 parity, `php -l`, scheduler/worker status, HTTP checks), and anything left undone or
needing a decision. If something failed, say so with the output — never report success on an
unverified step.

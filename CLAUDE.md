# Lion Roaring — backend

## Production work

**Read [docs/PRODUCTION-RULES.md](docs/PRODUCTION-RULES.md) before any action that reaches the
live server.** It is mandatory, not advisory.

The rules that get violated most often:

1. **Ask for confirmation before every upload to production**, listing the exact files and the
   rollback path. "Yes" applies to that deploy only.
2. **Never copy a whole file from local to production** — production code lags local. Fetch the
   live file, port the specific change into it, `php -l`, back up, then upload.
3. **Never lose production data.** No `migrate:fresh`/`refresh`/`seed`, no `TRUNCATE`/`DELETE`,
   no schema change, no `.env` edit, no service restart, no command that sends email — without
   explicit confirmation each time.
4. **After every production modification, verify the scheduler and queue worker are running**
   (`supervisorctl status` + `storage/logs/scheduler.log`). They have failed silently for weeks
   before.
5. Audit read-only first; audit your own change before reporting it done.

# MIS — testing and operations

## Automated tests

Run the full suite from the project root:

```bash
cd /opt/lampp/htdocs/VERLOX/verlox.uk/mis
php artisan test
```

**Continuous integration:** the `verlox.uk` Git repository includes `.github/workflows/ci.yml`, which runs `composer install` and `php artisan test` on pushes/PRs that touch `mis/**`.

**What is covered**

- **`MisPagesSmokeTest`** — GET `200` for marketing routes and many MIS index/create screens as a **verified admin**.
- **`MisGuestAndBoundaryTest`** — Guests redirect to **login** from MIS; users without MIS access get **403**; **unverified** users can use **profile** (`auth` only) but not **`/dashboard`** or **`/mis/*`** (`verified`); verified users without MIS see the default dashboard.
- **`MisAccessAndRolesTest`** — Core role flows (verification redirect to MIS, finance spot-check, VA CRM redirects, VA dashboard/help).
- **`MisFinanceComprehensiveGetTest`** — Finance user GET `200` on every **non-super** MIS list/create/export/booking calendar JSON route (no IDs).
- **`MisSuperRoutesForbiddenTest`** — Finance user GET **403** on every **`mis.super`** GET route (settings, users, offerings, pipeline stages editor, event types, availability UI, Google connect URL, etc.).
- **`MisVaComprehensiveGetTest`** — VA-only **200** on dashboard, help, **network map**, and all VA module index/create routes; **302** to VA dashboard from CRM/finance/Zoho.
- **`MisHelpPageTest`** — Help `200` for admin/finance/VA; all section/workflow anchor ids; TOC accessibility; key in-app URLs present; admin-only copy.
- **`MisParameterizedShowRoutesTest`** — Selected **show/edit** routes with real database rows (lead, client, quotation, invoice, contract, booking, conversation, VA records).
- **Auth / profile / public API** — Existing feature tests in `tests/Feature`.

**What is still out of scope**

- Most **POST/PATCH/DELETE** actions, file uploads, Stripe checkout, Zoho/Google **live** APIs, and webhook signature handling are not fully exercised.
- Production monitoring, backups, and alerting remain your hosting responsibility (e.g. snapshots, uptime checks, log shipping).

## Email verification

`User` implements `MustVerifyEmail`. Unverified accounts are redirected away from MIS (and other `verified` routes) to the verification notice until they confirm email.

## Role quick reference

| Role | `is_admin` | `mis_role` | MIS access |
|------|------------|------------|------------|
| Admin | true | null | Full, including `mis.super` routes |
| Finance | false | `finance` | Standard MIS; no `mis.super` |
| VA only | false | `va` | Dashboard, **Help**, **Network map**, and `mis.va.*` only |
| None | false | null | 403 on MIS |

## Deployment checklist (short)

1. `php artisan migrate --force`
2. `php artisan config:cache` / `route:cache` / `view:cache` as appropriate
3. `npm ci && npm run build` (or your asset pipeline)
4. Set `APP_URL`, mail, Stripe, Zoho, and webhook URLs in `.env`
5. Run `php artisan test` in CI or before release
6. Confirm email delivery for verification links in production

## Support / logs

- Application logs: `storage/logs/laravel.log`
- Web server and PHP-FPM logs: see host documentation (e.g. Virtualmin, nginx, Apache)

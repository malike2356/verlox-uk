# verlox.uk (static site)

One-page, professional marketing website for **Verlox UK**.

## Local preview

```bash
cd /opt/lampp/htdocs/allBusiness/velox/verlox.uk
python3 -m http.server 8089
```

Then open `http://localhost:8089/`.

## Deploy

Use the deploy script at:

- `/opt/lampp/htdocs/deploy_verlox_uk.sh`

Secrets are read from `/opt/lampp/htdocs/.access.deploy` (`PROPRENEUR_*` vars are reused for SSH + GitHub token).


# summit_starterkit

Child theme of Summit Base. SDCs and default styles live in the parent; override brand in `css/src/app.css`.

## Build

```sh
npm install
npm run build
npm run dev       # watch
npm run browser   # BrowserSync; proxy host is in package.json
```

`css/dist/` is gitignored. Commit `css/src/app.css` and rebuild on deploy.

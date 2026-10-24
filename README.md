# Formosa Virtual Airways — GitHub Pages starter

A bilingual (Traditional Chinese / English) static website starter for an independent virtual airline community.

## Included
- Responsive home page
- Fleet and planned routes
- Recruitment flow
- About and non-affiliation disclaimer
- Facebook, Discord and vAMSYS link placeholders
- No backend, database, build step or paid hosting required

## Important branding note
This starter intentionally uses the fictional working name **Formosa Virtual Airways** and original CSS illustrations. It does not include Taiwan Tigerair's official logo, wordmark, livery or other proprietary artwork. Do not present this site as an official airline or use third-party branding without permission. If you later receive written permission, update the branding only within the granted scope.

## 1. Configure links
Open `assets/site.js` and replace the empty values:

```js
const SITE_CONFIG = {
  facebookUrl: "https://www.facebook.com/your-page",
  discordUrl: "https://discord.gg/your-invite",
  vamsysUrl: "https://your-airline.vamsys.io",
  contactEmail: "your-contact@example.com"
};
```

Use your actual URLs. Leave links empty until they are ready. Do not add private tokens, passwords, or API keys to this file.

## 2. Publish with GitHub Pages
1. Create or sign in to a GitHub account.
2. Create a repository, for example `formosa-virtual-airways`.
3. Upload all files and folders in this ZIP, keeping the structure:
   - `index.html`
   - `assets/styles.css`
   - `assets/site.js`
4. Open repository **Settings → Pages**.
5. Under **Build and deployment**, choose **Deploy from a branch**.
6. Select branch `main` and folder `/ (root)`, then save.
7. Wait for deployment and open the URL shown on the Pages settings screen.

A project repository normally publishes at `https://YOUR-USERNAME.github.io/REPOSITORY-NAME/`. A repository named `YOUR-USERNAME.github.io` can publish at `https://YOUR-USERNAME.github.io/`.

## 3. Edit content
- Page content: `index.html`
- Colors, layout, responsive styling: `assets/styles.css`
- Social links and contact email: `assets/site.js`

After editing, commit the changes to the `main` branch; GitHub Pages will publish the update.

## 4. vAMSYS
The vAMSYS button is a placeholder. Set `vamsysUrl` after you create the VA and confirm the correct portal URL. Do not advertise registration as open until the portal works.

## 5. Current site status
- Routes are proposals for virtual operations, not a real airline timetable.
- Aircraft types are illustrative; update them to match your actual simulator and fleet policy.
- This website is static. It does not provide user accounts, pilot records, flight tracking, or a live route map. Use vAMSYS for those functions.
- Fonts are loaded from Google Fonts when online; system fallback fonts are included.

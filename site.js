/*
  Formosa Virtual Airways — site settings
  Replace the example URLs below with your real Facebook, Discord invite,
  and vAMSYS portal URLs. Leave a value as an empty string until ready.
*/
const SITE_CONFIG = {
  facebookUrl: "",
  discordUrl: "",
  vamsysUrl: "",
  contactEmail: "contact@example.com"
};

document.addEventListener("DOMContentLoaded", () => {
  const menuButton = document.querySelector(".menu-toggle");
  const nav = document.querySelector("#primary-nav");

  if (menuButton && nav) {
    menuButton.addEventListener("click", () => {
      const open = menuButton.getAttribute("aria-expanded") !== "true";
      menuButton.setAttribute("aria-expanded", String(open));
      nav.classList.toggle("open", open);
    });
    nav.querySelectorAll("a").forEach(link => link.addEventListener("click", () => {
      nav.classList.remove("open");
      menuButton.setAttribute("aria-expanded", "false");
    }));
  }

  const links = {
    facebook: SITE_CONFIG.facebookUrl,
    discord: SITE_CONFIG.discordUrl,
    vamsys: SITE_CONFIG.vamsysUrl
  };
  let configured = 0;
  document.querySelectorAll("[data-link]").forEach(anchor => {
    const key = anchor.dataset.link;
    const url = links[key];
    if (url && /^https:\/\/\S+/i.test(url)) {
      anchor.href = url;
      anchor.target = "_blank";
      anchor.rel = "noopener noreferrer";
      anchor.classList.remove("is-disabled");
      configured++;
    } else {
      anchor.href = "#links";
      anchor.classList.add("is-disabled");
      anchor.setAttribute("aria-disabled", "true");
      anchor.addEventListener("click", event => event.preventDefault());
    }
  });

  const help = document.querySelector("#link-help");
  if (help && configured === 3) {
    help.textContent = "所有入口已設定完成。 / All links are configured.";
  } else if (help) {
    help.textContent = "尚有 " + (3 - configured) + " 個入口待設定。請編輯 assets/site.js 中的 SITE_CONFIG。 / " + (3 - configured) + " link(s) pending. Edit SITE_CONFIG in assets/site.js.";
  }

  const contactLink = document.querySelector('a[href="mailto:contact@example.com"]');
  if (contactLink && SITE_CONFIG.contactEmail && SITE_CONFIG.contactEmail !== "contact@example.com") {
    contactLink.href = "mailto:" + SITE_CONFIG.contactEmail;
  }

  const year = document.querySelector("#year");
  if (year) year.textContent = new Date().getFullYear();
});

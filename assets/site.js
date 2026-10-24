const menuButton = document.querySelector(".menu-toggle");
const nav = document.querySelector("#primary-nav");
if (menuButton && nav) {
  menuButton.addEventListener("click", () => {
    const isOpen = nav.classList.toggle("open");
    menuButton.setAttribute("aria-expanded", String(isOpen));
  });
  nav.querySelectorAll("a").forEach((link) => link.addEventListener("click", () => {
    nav.classList.remove("open");
    menuButton.setAttribute("aria-expanded", "false");
  }));
}

const dateInput = document.querySelector("#flight-date");
if (dateInput) {
  const today = new Date();
  const localDate = new Date(today.getTime() - today.getTimezoneOffset() * 60000)
    .toISOString().slice(0, 10);
  dateInput.value = localDate;
  dateInput.min = localDate;
}

const origin = document.querySelector("#origin");
const destination = document.querySelector("#destination");
const swapButton = document.querySelector("#swap-airports");
if (swapButton && origin && destination) {
  swapButton.addEventListener("click", () => {
    const oldOrigin = origin.value;
    const oldDestination = destination.value;
    if ([...origin.options].some((option) => option.value === oldDestination) &&
        [...destination.options].some((option) => option.value === oldOrigin)) {
      origin.value = oldDestination;
      destination.value = oldOrigin;
    }
  });
}

const flightForm = document.querySelector("#flight-form");
const result = document.querySelector("#search-result");
if (flightForm && result) {
  flightForm.addEventListener("submit", (event) => {
    event.preventDefault();
    const originText = origin.options[origin.selectedIndex].text;
    const destinationText = destination.options[destination.selectedIndex].text;
    const date = document.querySelector("#flight-date").value;
    if (origin.value === destination.value) {
      result.textContent = "請選擇不同的出發機場與目的地。";
      return;
    }
    result.textContent = `模擬航班規劃：${originText} → ${destinationText}｜日期：${date}。這是社群規劃工具，不會訂購真實機票；請以社群公告及模擬器航路資料為準。`;
  });
}

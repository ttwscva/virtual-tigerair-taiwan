// Virtual Tigerair Taiwan - 最新消息資料庫
const NEWS_DATA = [
    {
        date: "2026-10-25",
        badge: "重要公告",
        badgeType: "primary",
        title: "Virtual Tigerair Taiwan 官方網站全新改版上線",
        content: "全新官方網站正式啟用！完整整合 A320 家族機隊清冊、互動式全航網地圖及最新時刻表，為熱愛虛擬飛行的飛友提供擬真且流暢的營運環境。",
        link: "index.html#about"
    },
    {
        date: "2026-10-20",
        badge: "招募消息",
        badgeType: "alert",
        title: "2026 SMARTCAT 飛行組員熱烈招募中",
        content: "歡迎各主流飛行模擬平台（MSFS / X-Plane / P3D）愛好者加入行列。申請表單已改版為直通審核信箱，敬請踴躍投遞！",
        link: "join.html"
    },
    {
        date: "2026-10-15",
        badge: "航務資訊",
        badgeType: "primary",
        title: "vAMSYS Pegasus ACARS 系統對接就緒",
        content: "vTTW 樞紐基地（RCTP）與呼號通訊參數已配置完成，支援自動 PIREP 與連線記錄，歡迎飛行員依指引完成報到。",
        link: "routes.html"
    }
];

// 自動渲染至首頁公告容器
function renderNews() {
    const container = document.getElementById('news-list');
    if (!container) return;

    container.innerHTML = '';

    NEWS_DATA.forEach(item => {
        const itemEl = document.createElement('div');
        itemEl.className = 'news-item';

        const hasLink = item.link && item.link.trim() !== "";
        const titleHtml = hasLink 
            ? `<a href="${item.link}" class="news-title-link">${item.title} &rarr;</a>`
            : `<span class="news-title-text">${item.title}</span>`;

        itemEl.innerHTML = `
            <div class="news-meta">
                <span class="news-date">${item.date}</span>
                <span class="news-badge ${item.badgeType === 'alert' ? 'badge-alert' : 'badge-primary'}">${item.badge}</span>
            </div>
            <div class="news-body">
                <h3 class="news-title">${titleHtml}</h3>
                <p class="news-desc">${item.content}</p>
            </div>
        `;
        container.appendChild(itemEl);
    });
}

document.addEventListener('DOMContentLoaded', renderNews);

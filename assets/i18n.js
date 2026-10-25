// Virtual Tigerair Taiwan - 多語系對照字典
const TRANSLATIONS = {
    zh: {
        page_title: "Virtual Tigerair Taiwan | 虛擬台灣虎航 - 航向無限可能",
        nav_about: "關於我們",
        nav_fleet: "機隊資料",
        nav_routes: "航線網絡",
        nav_timetable: "時刻表",
        nav_team: "組織成員",
        nav_join: "加入我們",
        hero_title: "熱情、專注、滑順 <span>想飛就飛！</span>",
        hero_desc: "專為飛行模擬同好打造的擬真低成本航空體驗。桃園/高雄/台中/台南直飛日、韓、東南亞，邀請您加入 SMARTCAT 行列！",
        hero_cta: "立即申請加入飛行員",
        news_section_title: "最新消息",
        news_badge_primary: "重要公告",
        news_badge_alert: "招募消息",
        news_title_1: "Virtual Tigerair Taiwan 官方網站全新改版上線 →",
        news_desc_1: "全新官方網站正式啟用！完整整合 A320 家族機隊清冊、互動式全航網地圖及最新時刻表，為熱愛虛擬飛行的飛友提供擬真且流暢的營運環境。",
        news_title_2: "2026 SMARTCAT 飛行組員熱烈招募中 →",
        news_desc_2: "歡迎各主流飛行模擬平台（MSFS / X-Plane / P3D）愛好者加入行列。申請表單已改版為直通審核信箱，敬請踴躍投遞！",
        news_title_3: "vAMSYS Pegasus ACARS 系統對接就緒 →",
        news_desc_3: "vTTW 樞紐基地（RCTP）與呼號通訊參數已配置完成，支援自動 PIREP 與連線記錄，歡迎飛行員依指引完成報到。",
        features_title: "探索 vTTW 核心特色",
        card1_title: "✈️ 擬真航線與 vAMSYS",
        card1_desc: "串接業界頂尖 vAMSYS 系統，支援自動 PIREP 航班記錄與階級晉升考核，體驗真實 LCC 的營運快感。",
        card1_link: "前往航線網絡 →",
        card2_title: "🎯 A320 機隊與高品質塗裝",
        card2_desc: "主力營運 Airbus A320ceo 與 A320neo，包含官方聯名與董陽孜書法等專屬彩繪機，讓您每一次進場都充滿自信。",
        card2_link: "查看完整機隊名冊 →",
        card3_title: "💬 自由飛行與交流",
        card3_desc: "歡迎各主流模擬飛行平台玩家加入，不限連線網絡與飛行方式，共同在天空中翱翔並交流飛行熱忱。",
        card3_link: "填表申請加入 →",
        partners_title: "合作夥伴與技術支援",
        partner_vamsys_role: "航司運營核心與 Pegasus ACARS 自動記錄系統",
        partner_simbrief_role: "Navigraph 專業飛行簽派 (OFP) 與油量酬載計算",
        partner_vatsim_role: "全球擬真即時航管與線上虛擬飛行連線網絡",
        partner_ourairports_role: "全球開源機場座標、跑道與航行資料庫支援",
        footer_col1_title: "關於虛擬台虎",
        footer_col2_title: "航務專區",
        footer_col3_title: "飛行員指南",
        footer_col4_title: "關注我們",
        footer_col5_title: "飛行模擬支援工具",
        footer_novice: "新手報到須知",
        footer_faq: "常見問題",
        footer_fb: "📘 Facebook 社團",
        footer_yt: "▶️ Youtube 頻道",
        footer_disclaimer: "⚠️ 虛擬航空社群免責聲明 (Disclaimer)：本網站與社群為非營利之飛行模擬愛好者團體（Virtual Airline），專供飛行模擬愛好者交流使用。支援微軟模擬飛行 (MSFS)、X-Plane 及 Prepar3D 等主流平台，且不限制連線網絡。本團體與真實世界的「台灣虎航股份有限公司 (Tigerair Taiwan)」無任何商業、法律或實質隸屬關係。所有商標與識別權利歸原版權所有者所有。"
    },
    en: {
        page_title: "Virtual Tigerair Taiwan | vTTW - Keep Flying",
        nav_about: "About Us",
        nav_fleet: "Fleet",
        nav_routes: "Routes",
        nav_timetable: "Timetable",
        nav_team: "Our Team",
        nav_join: "Join Us",
        hero_title: "Passionate, Dedicated, Smooth. <span>Just Fly!</span>",
        hero_desc: "A realistic virtual low-cost carrier experience built for flight simulator enthusiasts. Direct flights from Taipei, Kaohsiung, Taichung, and Tainan across Asia-Pacific. Welcome to SMARTCAT!",
        hero_cta: "Apply for Pilot Position",
        news_section_title: "Latest News",
        news_badge_primary: "Notice",
        news_badge_alert: "Recruitment",
        news_title_1: "Virtual Tigerair Taiwan Official Website Launch →",
        news_desc_1: "Our new official website is live! Complete with interactive A320 fleet records, route maps, and real-world timetables for an immersive virtual airline experience.",
        news_title_2: "2026 SMARTCAT Flight Crew Openings →",
        news_desc_2: "Welcoming aviators from MSFS, X-Plane, and P3D! Applications are sent directly to the flight operations team. Apply today!",
        news_title_3: "vAMSYS Pegasus ACARS Integration Complete →",
        news_desc_3: "Hub parameters (RCTP) and callsign telephony configured. Automatic PIREP dispatch and tracking are fully active for all onboard pilots.",
        features_title: "Discover vTTW Core Highlights",
        card1_title: "✈️ Realistic Routes & vAMSYS",
        card1_desc: "Powered by the industry-leading vAMSYS system with automatic PIREP recording and rank promotions, simulating genuine LCC operations.",
        card1_link: "Explore Route Network →",
        card2_title: "🎯 A320 Fleet & Custom Liveries",
        card2_desc: "Operating modern Airbus A320ceo and A320neo aircraft, featuring exclusive liveries and authentic Tiger stripes for confident approaches.",
        card2_link: "View Fleet List →",
        card3_title: "💬 Open Skies & Community",
        card3_desc: "Join fellow flight enthusiasts across all major simulation platforms. Fly online or offline and share your aviation passion with us.",
        card3_link: "Submit Application →",
        partners_title: "Partners & Technology Ecosystem",
        partner_vamsys_role: "Virtual Airline Management & Pegasus ACARS System",
        partner_simbrief_role: "Navigraph Operational Flight Planning (OFP) & Dispatch",
        partner_vatsim_role: "Global Virtual Air Traffic Control & Real-time Network",
        partner_ourairports_role: "Open-source Global Airport, Runway & Navdata Support",
        footer_col1_title: "About vTTW",
        footer_col2_title: "Flight Operations",
        footer_col3_title: "Pilot Resources",
        footer_col4_title: "Connect With Us",
        footer_col5_title: "Simulation Tools",
        footer_novice: "Pilot Onboarding Guide",
        footer_faq: "FAQ",
        footer_fb: "📘 Facebook Group",
        footer_yt: "▶️ YouTube Channel",
        footer_disclaimer: "⚠️ Community Disclaimer: Virtual Tigerair Taiwan is an independent, non-profit virtual flight simulation enthusiast community. We support MSFS, X-Plane, and Prepar3D across any network. We are NOT affiliated with, endorsed by, or connected to Tigerair Taiwan Co., Ltd. All commercial trademarks remain the property of their respective owners."
    },
    ja: {
        page_title: "Virtual Tigerair Taiwan | ヴァーチャル台湾タイガーエア",
        nav_about: "会社概要",
        nav_fleet: "保有機材",
        nav_routes: "就航路線",
        nav_timetable: "時刻表",
        nav_team: "組織構成",
        nav_join: "クルー採用",
        hero_title: "情熱、プロフェッショナル <span>今すぐ空へ！</span>",
        hero_desc: "フライトシミュレーター愛好家のために設計された本格派バーチャルLCC。台北・高雄・台中・台南からアジア太平洋へ。SMARTCATの仲間になりませんか？",
        hero_cta: "パイロットに応募する",
        news_section_title: "最新のお知らせ",
        news_badge_primary: "重要告知",
        news_badge_alert: "採用情報",
        news_title_1: "Virtual Tigerair Taiwan 公式サイトリニューアル →",
        news_desc_1: "公式サイトが新しくなりました！A320機隊名簿、インタラクティブ路線マップ、最新時刻表を統合し、よりリアルな運用環境をお届けします。",
        news_title_2: "2026年度 SMARTCAT 運航乗務員募集中 →",
        news_desc_2: "MSFS、X-Plane、P3Dをご利用の皆様を歓迎します！専用フォームから審査チームへ直接エントリー可能です。",
        news_title_3: "vAMSYS Pegasus ACARS システム接続完了 →",
        news_desc_3: "拠点（RCTP）とコールサイン通報設定が完了しました。自動PIREP記録および運航ランク昇格システムが稼働中です。",
        features_title: "vTTW の特色と強み",
        card1_title: "✈️ リアルな路線網と vAMSYS",
        card1_desc: "航空シミュレーター標準の vAMSYS と連携。PIREP自動記録や階級昇格審査など、本格的なLCC運航を体験できます。",
        card1_link: "路線ネットワークを見る →",
        card2_title: "🎯 A320 機隊と高精細リペイント",
        card2_desc: "Airbus A320ceo / A320neo を主力機として運用。公式コラボ塗装や董陽孜氏の書道特別塗装機もラインナップ。",
        card2_link: "機材一覧を確認する →",
        card3_title: "💬 自由なフライトとコミュニティ",
        card3_desc: "ネットワークの制限なく、お好みのフライトスタイルで空の旅を楽しめます。仲間と一緒にフライトを楽しみましょう。",
        card3_link: "入社申請はこちら →",
        partners_title: "パートナー＆技術サポート",
        partner_vamsys_role: "航空会社管理・Pegasus ACARS 自動運航記録システム",
        partner_simbrief_role: "Navigraph 運航便ディスパッチ＆燃料計算システム",
        partner_vatsim_role: "世界最大級のバーチャル航空管制＆オンラインフライト",
        partner_ourairports_role: "世界空港データ・滑走路オープンソースデータベース",
        footer_col1_title: "会社案内",
        footer_col2_title: "運航情報",
        footer_col3_title: "パイロットガイド",
        footer_col4_title: "公式SNS",
        footer_col5_title: "シミュレーションツール",
        footer_novice: "新任パイロット登録手順",
        footer_faq: "よくあるご質問",
        footer_fb: "📘 Facebook グループ",
        footer_yt: "▶️ YouTube チャンネル",
        footer_disclaimer: "⚠️ 免責事項：当組織（Virtual Tigerair Taiwan）は、航空シミュレーター愛好家による非営利のバーチャルエアライン団体です。実在の「タイガーエア台湾（台湾虎航股份有限公司）」とは一切の提携・関係がありません。すべての商標権は権利者に帰属します。"
    }
};

// 語言設定主函數
function setLanguage(lang) {
    if (!TRANSLATIONS[lang]) lang = 'zh';
    localStorage.setItem('vttw_preferred_lang', lang);

    // 更新網頁標題
    if (TRANSLATIONS[lang].page_title) {
        document.title = TRANSLATIONS[lang].page_title;
    }

    // 依據 data-i18n 屬性替換所有文字
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (TRANSLATIONS[lang][key]) {
            el.innerHTML = TRANSLATIONS[lang][key];
        }
    });

    // 更新下拉選單選取狀態
    const selectEl = document.getElementById('langSelect');
    if (selectEl) {
        selectEl.value = lang;
    }
}

// 頁面載入時自動初始化語言
document.addEventListener('DOMContentLoaded', () => {
    const savedLang = localStorage.getItem('vttw_preferred_lang') || 'zh';
    setLanguage(savedLang);
});

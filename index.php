<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>tmopro.ru — Сантехника Оптом</title>
  <meta name="theme-color" content="#4f46e5">
  <link rel="manifest" href="manifest.json">
  <link rel="icon" href="icon.svg" type="image/svg+xml">
  <script src="vue.global.prod.js"></script>
  <style>
    * { box-sizing: border-box; margin: 0; }
    body { font-family: Inter, system-ui, -apple-system, Segoe UI, Arial, sans-serif; background: #f8fafc; color: #0f172a; line-height: 1.5; }
    a { color: inherit; text-decoration: none; }
    button { border: 0; cursor: pointer; font: inherit; }
    input, select { font: inherit; }
    img { max-width: 100%; display: block; }
    [v-cloak] { display: none !important; }
    @keyframes floatUp { 0% { transform: translateY(8px); opacity: 0; } 100% { transform: translateY(0); opacity: 1; } }
    .animate-floatUp { animation: floatUp .55s ease-out both; }
    .number-smooth { transition: color .25s ease, opacity .25s ease, transform .25s ease; }
    .fallback { max-width: 760px; margin: 80px auto; padding: 32px; border-radius: 28px; background: #fff; box-shadow: 0 24px 80px rgba(15,23,42,.12); font-family: Inter, system-ui, sans-serif; color: #0f172a; }
    .fallback h1 { margin: 0 0 12px; font-size: 32px; line-height: 1.1; }
    .fallback p { margin: 0 0 20px; color: #64748b; line-height: 1.7; }
    .fallback a { display: inline-flex; margin-right: 10px; border-radius: 16px; background: #4f46e5; color: #fff; padding: 13px 18px; font-weight: 800; text-decoration: none; }
    /* Tailwind-like utilities */
    .min-h-screen { min-height: 100vh; }
    .overflow-hidden { overflow: hidden; }
    .pb-24 { padding-bottom: 96px; }
    .fixed { position: fixed; }
    .inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
    .-z-10 { z-index: -10; }
    .absolute { position: absolute; }
    .left-1\/2 { left: 50%; }
    .top-\[-180px\] { top: -180px; }
    .h-\[420px\] { height: 420px; }
    .w-\[720px\] { width: 720px; }
    .-translate-x-1\/2 { transform: translateX(-50%); }
    .rounded-full { border-radius: 9999px; }
    .blur-3xl { filter: blur(64px); }
    .opacity-20 { opacity: 0.2; }
    .right-\[-160px\] { right: -160px; }
    .top-\[360px\] { top: 360px; }
    .h-\[360px\] { height: 360px; }
    .w-\[360px\] { width: 360px; }
    .bg-emerald-200\/30 { background: rgba(167,243,208,0.3); }
    .sticky { position: sticky; }
    .top-0 { top: 0; }
    .z-40 { z-index: 40; }
    .border-b { border-bottom: 1px solid #f1f5f9; }
    .bg-white\/75 { background: rgba(255,255,255,0.75); }
    .backdrop-blur-xl { backdrop-filter: blur(24px); }
    .mx-auto { margin-left: auto; margin-right: auto; }
    .max-w-7xl { max-width: 1280px; }
    .flex { display: flex; }
    .items-center { align-items: center; }
    .justify-between { justify-content: space-between; }
    .px-4 { padding-left: 16px; padding-right: 16px; }
    .py-4 { padding-top: 16px; padding-bottom: 16px; }
    .gap-3 { gap: 12px; }
    .group { position: relative; }
    .h-11 { height: 44px; }
    .w-11 { width: 44px; }
    .grid { display: grid; }
    .place-items-center { place-items: center; }
    .overflow-hidden { overflow: hidden; }
    .rounded-2xl { border-radius: 16px; }
    .rounded-xl { border-radius: 12px; }
    .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
    .ring-1 { box-shadow: 0 0 0 1px rgba(0,0,0,0.05); }
    .ring-inset { box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05); }
    .border-t-transparent { border-top-color: transparent; }
    .text-\[10px\] { font-size: 10px; }
    .px-1\.5 { padding-left: 6px; padding-right: 6px; }
    .py-0\.5 { padding-top: 2px; padding-bottom: 2px; }
    .bg-white { background: #fff; }
    .shadow-\[0_8px_30px_rgb\(0\,0\,0\,0\.08\)\] { box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
    .transition-transform { transition: transform 0.3s; }
    .duration-300 { transition-duration: 300ms; }
    .group-hover\:-translate-y-0\.5:hover { transform: translateY(-2px); }
    .text-xs { font-size: 12px; }
    .font-black { font-weight: 900; }
    .tracking-tight { letter-spacing: -0.025em; }
    .text-white { color: #fff; }
    .text-slate-950 { color: #0f172a; }
    .text-lg { font-size: 18px; }
    .sm\:text-6xl { font-size: 60px; }
    .max-w-4xl { max-width: 896px; }
    .max-w-2xl { max-width: 672px; }
    .leading-8 { line-height: 2rem; }
    .text-slate-700 { color: #334155; }
    .text-emerald-600 { color: #059669; }
    .transition { transition-property: all; transition-duration: 150ms; }
    .font-extrabold { font-weight: 800; }
    .leading-snug { line-height: 1.375; }
    .mt-1 { margin-top: 4px; }
    .text-slate-400 { color: #94a3b8; }
    .font-bold { font-weight: 700; }
    .hidden { display: none; }
    .lg\:block { display: block; }
    .max-w-3xl { max-width: 768px; }
    .pt-16 { padding-top: 64px; }
    .text-center { text-align: center; }
    .mb-2 { margin-bottom: 8px; }
    .inline-flex { display: inline-flex; }
    .rounded-full { border-radius: 9999px; }
    .bg-white { background: #fff; }
    .px-3 { padding-left: 12px; padding-right: 12px; }
    .py-1 { padding-top: 4px; padding-bottom: 4px; }
    .text-sm { font-size: 14px; }
    .text-slate-600 { color: #475569; }
    .mt-3 { margin-top: 12px; }
    .text-4xl { font-size: 36px; }
    .leading-tight { line-height: 1.25; }
    .mt-5 { margin-top: 20px; }
    .max-w-xl { max-width: 576px; }
    .text-slate-500 { color: #64748b; }
    .leading-relaxed { line-height: 1.625; }
    .mt-8 { margin-top: 32px; }
    .flex-wrap { flex-wrap: wrap; }
    .gap-3 { gap: 12px; }
    .justify-center { justify-content: center; }
    .rounded-2xl { border-radius: 16px; }
    .px-5 { padding-left: 20px; padding-right: 20px; }
    .py-3 { padding-top: 12px; padding-bottom: 12px; }
    .text-white { color: #fff; }
    .transition-all { transition: all 0.3s; }
    .hover\:-translate-y-0\.5:hover { transform: translateY(-2px); }
    .hover\:shadow-lg:hover { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    .active\:scale-95:active { transform: scale(0.95); }
    .rounded-2xl { border-radius: 16px; }
    .bg-white { background: #fff; }
    .shadow-\[0_8px_30px_rgb\(0\,0\,0\,0\.04\)\] { box-shadow: 0 8px 30px rgba(0,0,0,0.04); }
    .p-3 { padding: 12px; }
    .sm\:flex-row { flex-direction: row; }
    .sm\:items-center { align-items: center; }
    .sm\:justify-between { justify-content: space-between; }
    .px-2 { padding-left: 8px; padding-right: 8px; }
    .text-slate-950 { color: #0f172a; }
    .flex { display: flex; }
    .rounded-2xl { border-radius: 16px; }
    .bg-slate-50 { background: #f8fafc; }
    .p-1 { padding: 4px; }
    .space-y-2 > * + * { margin-top: 8px; }
    .w-full { width: 100%; }
    .border { border: 1px solid #f1f5f9; }
    .bg-slate-50 { background: #f8fafc; }
    .outline-none { outline: none; }
    .focus\:border-transparent:focus { border-color: transparent; }
    .focus\:bg-white:focus { background: #fff; }
    .focus\:ring-4:focus { box-shadow: 0 0 0 4px rgba(79,70,229,0.2); }
    .mt-7 { margin-top: 28px; }
    .uppercase { text-transform: uppercase; }
    .tracking-widest { letter-spacing: 0.1em; }
    .mt-3 { margin-top: 12px; }
    .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
    .gap-2 { gap: 8px; }
    .overflow-x-auto { overflow-x: auto; }
    .min-w-\[920px\] { min-width: 920px; }
    .border-separate { border-collapse: separate; }
    .border-spacing-y-2 { border-spacing: 0 8px; }
    .text-left { text-align: left; }
    .py-3 { padding-top: 12px; padding-bottom: 12px; }
    .px-4 { padding-left: 16px; padding-right: 16px; }
    .rounded-l-2xl { border-top-left-radius: 16px; border-bottom-left-radius: 16px; }
    .rounded-r-2xl { border-top-right-radius: 16px; border-bottom-right-radius: 16px; }
    .hover\:bg-white:hover { background: #fff; }
    .hover\:shadow-md:hover { box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .rounded-full { border-radius: 9999px; }
    .bg-slate-100 { background: #f1f5f9; }
    .px-3 { padding-left: 12px; padding-right: 12px; }
    .py-1 { padding-top: 4px; padding-bottom: 4px; }
    .text-slate-600 { color: #475569; }
    .text-slate-500 { color: #64748b; }
    .grid { display: grid; }
    .md\:grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
    .xl\:grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
    .gap-5 { gap: 20px; }
    .border { border: 1px solid #f1f5f9; }
    .bg-white { background: #fff; }
    .p-5 { padding: 20px; }
    .shadow-\[0_8px_30px_rgb\(0\,0\,0\,0\.04\)\] { box-shadow: 0 8px 30px rgba(0,0,0,0.04); }
    .hover\:-translate-y-1:hover { transform: translateY(-4px); }
    .hover\:shadow-xl:hover { box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
    .group-hover\:scale-105:hover { transform: scale(1.05); }
    .mb-5 { margin-bottom: 20px; }
    .items-start { align-items: flex-start; }
    .gap-4 { gap: 16px; }
    .mb-2 { margin-bottom: 8px; }
    .tracking-widest { letter-spacing: 0.1em; }
    .text-slate-400 { color: #94a3b8; }
    .text-lg { font-size: 18px; }
    .leading-snug { line-height: 1.375; }
    .px-3 { padding-left: 12px; padding-right: 12px; }
    .py-1 { padding-top: 4px; padding-bottom: 4px; }
    .text-xs { font-size: 12px; }
    .mb-5 { margin-bottom: 20px; }
    .p-4 { padding: 16px; }
    .w-full { width: 100%; }
    .px-5 { padding-left: 20px; padding-right: 20px; }
    .py-4 { padding-top: 16px; padding-bottom: 16px; }
    .hover\:-translate-y-0\.5:hover { transform: translateY(-2px); }
    .hover\:shadow-lg:hover { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    .inset-x-3 { left: 12px; right: 12px; }
    .bottom-3 { bottom: 12px; }
    .z-50 { z-index: 50; }
    .border { border: 1px solid #e2e8f0; }
    .border-slate-200\/70 { border-color: rgba(226,232,240,0.7); }
    .bg-white\/90 { background: rgba(255,255,255,0.9); }
    .p-2 { padding: 8px; }
    .shadow-\[0_18px_60px_rgb\(15\,23\,42\,0\.16\)\] { box-shadow: 0 18px 60px rgba(15,23,42,0.16); }
    .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
    .gap-1 { gap: 4px; }
    .h-5 { height: 20px; }
    .w-5 { width: 20px; }
    .text-xs { font-size: 12px; }
    .font-bold { font-weight: 700; }
    .md\:hidden { display: none; }
    .lg\:flex { display: flex; }
    .gap-4 { gap: 16px; }
    .lg\:pb-0 { padding-bottom: 0; }
    .lg\:px-8 { padding-left: 32px; padding-right: 32px; }
    .lg\:py-16 { padding-top: 64px; padding-bottom: 64px; }
    .lg\:grid { display: grid; }
    .lg\:grid-cols-\[280px_1fr\] { grid-template-columns: 280px 1fr; }
    .lg\:gap-8 { gap: 32px; }
    .lg\:pb-20 { padding-bottom: 80px; }
    .sm\:px-6 { padding-left: 24px; padding-right: 24px; }
    .lg\:px-8 { padding-left: 32px; padding-right: 32px; }
    /* Accent colors */
    .bg-indigo-600 { background: #4f46e5; }
    .bg-emerald-600 { background: #059669; }
    .bg-slate-900 { background: #0f172a; }
    .text-indigo-700 { color: #4338ca; }
    .text-emerald-700 { color: #047857; }
    .text-slate-700 { color: #334155; }
    .bg-indigo-50 { background: #eef2ff; }
    .bg-emerald-50 { background: #ecfdf5; }
    .bg-slate-100 { background: #f1f5f9; }
    .ring-indigo-100 { box-shadow: 0 0 0 1px #e0e7ff; }
    .ring-emerald-100 { box-shadow: 0 0 0 1px #d1fae5; }
    .ring-slate-200 { box-shadow: 0 0 0 1px #e2e8f0; }
    .border-indigo-600 { border-color: #4f46e5; }
    .border-emerald-600 { border-color: #059669; }
    .border-slate-900 { border-color: #0f172a; }
    .animate-bounce { animation: bounce 1s infinite; }
    .animate-spin { animation: spin 1s linear infinite; }
    @keyframes bounce { 0%, 100% { transform: translateY(-25%); animation-timing-function: cubic-bezier(0.8,0,1,1); } 50% { transform: translateY(0); animation-timing-function: cubic-bezier(0,0,0.2,1); } }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .lg\:text-5xl { font-size: 48px; }
    .sm\:text-xl { font-size: 20px; }
    .lg\:text-2xl { font-size: 24px; }
    .lg\:mt-8 { margin-top: 32px; }
    @media (min-width: 640px) { .sm\:flex-row { flex-direction: row; } .sm\:items-center { align-items: center; } .sm\:justify-between { justify-content: space-between; } .sm\:px-6 { padding-left: 24px; padding-right: 24px; } .sm\:text-xl { font-size: 20px; } }
    @media (min-width: 768px) { .md\:grid-cols-2 { grid-template-columns: repeat(2, 1fr); } .md\:hidden { display: none; } .md\:pb-0 { padding-bottom: 0; } }
    @media (min-width: 1024px) { .lg\:block { display: block; } .lg\:flex { display: flex; } .lg\:grid { display: grid; } .lg\:grid-cols-\[280px_1fr\] { grid-template-columns: 280px 1fr; } .lg\:gap-8 { gap: 32px; } .lg\:pb-0 { padding-bottom: 0; } .lg\:pb-20 { padding-bottom: 80px; } .lg\:px-8 { padding-left: 32px; padding-right: 32px; } .lg\:py-16 { padding-top: 64px; padding-bottom: 64px; } .lg\:text-5xl { font-size: 48px; } .lg\:text-2xl { font-size: 24px; } .lg\:mt-8 { margin-top: 32px; } }
    @media (min-width: 1280px) { .xl\:grid-cols-3 { grid-template-columns: repeat(3, 1fr); } }
  </style>
</head>
<body style="background:rgba(248,250,252,0.5);color:#0f172a;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;">
  <div id="app" v-cloak class="min-h-screen overflow-hidden pb-24 md:pb-0" :style="pageStyle">
    <div class="pointer-events-none fixed inset-0 -z-10">
      <div :class="['absolute left-1/2 top-[-180px] h-[420px] w-[720px] -translate-x-1/2 rounded-full blur-3xl opacity-20', accentBg]"></div>
      <div class="absolute right-[-160px] top-[360px] h-[360px] w-[360px] rounded-full bg-emerald-200/30 blur-3xl"></div>
    </div>

    <header class="sticky top-0 z-40 border-b border-slate-100 bg-white/75 backdrop-blur-xl">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="index.php" class="group flex items-center gap-3">
          <span v-if="settings.logo_type === 'image' && settings.logo_url" class="grid h-11 w-11 place-items-center overflow-hidden rounded-2xl bg-white shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-transform duration-300 group-hover:-translate-y-0.5">
            <img :src="settings.logo_url" alt="tmopro.ru" class="h-full w-full object-cover">
          </span>
          <span v-else :class="['grid h-11 w-11 place-items-center rounded-2xl text-white shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-transform duration-300 group-hover:-translate-y-0.5', accentBg]">
            <span class="text-xs font-black tracking-tight">{{ settings.logo_text || 'TMO' }}</span>
          </span>
          <span>
            <span class="block text-lg font-800 font-bold tracking-tight">{{ settings.site_short_name || 'tmopro.ru' }}</span>
            <span class="block text-xs font-medium text-slate-500">сантехника оптом для бизнеса</span>
          </span>
        </a>

        <div class="hidden items-center gap-6 md:flex">
          <a :href="'tel:' + settings.phone" class="text-sm font-semibold text-slate-700 transition hover:text-slate-950">{{ settings.phone }}</a>
          <a :href="'mailto:' + settings.email_manager" class="text-sm font-semibold text-slate-500 transition hover:text-slate-950">{{ settings.email_manager }}</a>
        </div>

        <a href="checkout.php" class="relative flex items-center gap-3 rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm font-bold shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
          <span :class="['grid h-8 w-8 place-items-center rounded-xl text-white', accentBg, cartBump ? 'animate-bounce' : '']">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
          </span>
          <span class="hidden sm:block">Корзина</span>
          <span :class="['rounded-full px-2 py-0.5 text-xs text-white', accentBg]">{{ cartCount }}</span>
        </a>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
      <section class="mb-10 grid gap-8 lg:grid-cols-[1.1fr_.9fr] lg:items-end">
        <div class="animate-floatUp">
          <div :class="['mb-5 inline-flex rounded-full px-4 py-2 text-sm font-bold ring-1 ring-inset', accentSoft, accentText, accentRing]">B2B-каталог с мгновенным расчетом опта</div>
          <h1 class="max-w-4xl text-4xl font-extrabold tracking-[-0.04em] text-slate-950 sm:text-6xl">{{ settings.hero_title }}</h1>
          <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">{{ settings.hero_subtitle }}</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] animate-floatUp">
          <div class="grid grid-cols-3 gap-3 text-center">
            <div class="rounded-2xl bg-slate-50 p-4"><div class="text-2xl font-extrabold">8</div><div class="text-xs font-semibold text-slate-500">SKU</div></div>
            <div class="rounded-2xl bg-slate-50 p-4"><div class="text-2xl font-extrabold">4</div><div class="text-xs font-semibold text-slate-500">бренда</div></div>
            <div class="rounded-2xl bg-slate-50 p-4"><div class="text-2xl font-extrabold">10+</div><div class="text-xs font-semibold text-slate-500">опт</div></div>
          </div>
        </div>
      </section>

      <section class="grid gap-6 lg:grid-cols-[290px_1fr]">
        <aside class="h-fit rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] lg:sticky lg:top-24">
          <div class="mb-5 flex items-center justify-between">
            <h2 class="text-base font-extrabold">Фильтры</h2>
            <button @click="resetFilters" class="text-xs font-bold text-slate-400 transition hover:text-slate-950">Сбросить</button>
          </div>

          <label class="block text-xs font-bold uppercase tracking-widest text-slate-400">Поиск по артикулу</label>
          <input v-model.trim="search" type="text" placeholder="Например, GEB" class="mt-2 w-full rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-transparent focus:bg-white focus:ring-4 focus:ring-indigo-500/20">

          <div class="mt-7">
            <div class="mb-3 text-xs font-bold uppercase tracking-widest text-slate-400">Категория</div>
            <div class="space-y-3">
              <div v-for="cat in categoryList" :key="cat.id" class="space-y-1">
                <div class="text-xs font-bold text-slate-500 px-4 py-1">{{ cat.name }}</div>
                <button v-for="sub in cat.subcategories" :key="sub.id" @click="toggleCategory(sub.name)" :class="['flex w-full items-center justify-between rounded-2xl px-4 py-2.5 text-sm font-bold transition-all duration-300', selectedCategories.includes(sub.name) ? accentBg + ' text-white shadow-md' : 'bg-slate-50 text-slate-600 hover:bg-white hover:shadow-md']">
                  <span>{{ sub.name }}</span><span>{{ countBy('category', sub.name) }}</span>
                </button>
              </div>
            </div>
          </div>

          <div class="mt-7">
            <div class="mb-3 text-xs font-bold uppercase tracking-widest text-slate-400">Бренд</div>
            <div class="grid grid-cols-2 gap-2">
              <button v-for="brand in brands" :key="brand" @click="toggleBrand(brand)" :class="['rounded-2xl px-3 py-3 text-sm font-bold transition-all duration-300', selectedBrands.includes(brand) ? accentBg + ' text-white shadow-md' : 'bg-slate-50 text-slate-600 hover:bg-white hover:shadow-md']">{{ brand }}</button>
            </div>
          </div>
        </aside>

        <div>
          <div class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-3 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:flex-row sm:items-center sm:justify-between">
            <div class="px-2 text-sm font-bold text-slate-500">Найдено: <span class="text-slate-950">{{ filteredProducts.length }}</span></div>
            <div class="flex rounded-2xl bg-slate-50 p-1">
              <button @click="view = 'table'" :class="viewButton('table')">Таблица</button>
              <button @click="view = 'grid'" :class="viewButton('grid')">Плитка</button>
            </div>
          </div>

          <div v-if="loading" class="rounded-2xl border border-slate-100 bg-white p-12 text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <div :class="['mx-auto mb-4 h-10 w-10 animate-spin rounded-full border-4 border-t-transparent', accentBorder]"></div>
            <p class="font-bold text-slate-500">Загружаем каталог...</p>
          </div>

          <div v-else-if="view === 'table'" class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <div class="overflow-x-auto p-2">
              <table class="w-full min-w-[920px] border-separate border-spacing-y-2">
                <thead>
                  <tr class="text-left text-xs font-bold uppercase tracking-widest text-slate-400">
                    <th class="px-4 py-3">Товар</th><th class="px-4 py-3">Категория</th><th class="px-4 py-3">Остаток</th><th class="px-4 py-3">Цена</th><th class="px-4 py-3">Кол-во</th><th class="px-4 py-3"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="product in filteredProducts" :key="product.id" class="group rounded-2xl transition-all duration-300 hover:-translate-y-0.5 hover:bg-white hover:shadow-md">
                    <td class="rounded-l-2xl px-4 py-4">
                      <div class="flex items-center gap-3">
                        <img v-if="product.image" :src="product.image" class="h-12 w-12 rounded-xl object-cover">
                        <div>
                          <div class="font-extrabold text-slate-950">{{ product.name }}</div>
                          <div class="mt-1 text-xs font-bold text-slate-400">{{ product.article }} · {{ product.brand }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-4"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ product.category }}</span></td>
                    <td class="px-4 py-4 text-sm font-bold text-slate-500">{{ product.stock }} шт</td>
                    <td class="px-4 py-4"> <price-block :product="product" :qty="qty[product.id]"></price-block> </td>
                    <td class="px-4 py-4"><qty-control :model-value="qty[product.id]" @update:model-value="setQty(product.id, $event)"></qty-control></td>
                    <td class="rounded-r-2xl px-4 py-4"><button @click="addToCart(product)" :class="['rounded-2xl px-5 py-3 text-sm font-extrabold text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg active:scale-95', accentBg]">В корзину</button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-else class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            <article v-for="product in filteredProducts" :key="product.id" class="group rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
              <div v-if="product.image" class="mb-4 overflow-hidden rounded-xl">
                <img :src="product.image" class="h-48 w-full object-cover transition-transform duration-500 group-hover:scale-105">
              </div>
              <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                  <div class="mb-2 text-xs font-bold uppercase tracking-widest text-slate-400">{{ product.article }}</div>
                  <h3 class="text-lg font-extrabold leading-snug">{{ product.name }}</h3>
                </div>
                <span :class="['rounded-full px-3 py-1 text-xs font-bold', accentSoft, accentText]">{{ product.brand }}</span>
              </div>
              <div class="mb-5 flex items-center justify-between rounded-2xl bg-slate-50 p-4">
                <span class="text-sm font-bold text-slate-500">{{ product.category }}</span>
                <span class="text-sm font-bold text-slate-500">{{ product.stock }} шт</span>
              </div>
              <div class="mb-5 flex items-center justify-between gap-4">
                <price-block :product="product" :qty="qty[product.id]"></price-block>
                <qty-control :model-value="qty[product.id]" @update:model-value="setQty(product.id, $event)"></qty-control>
              </div>
              <button @click="addToCart(product)" :class="['w-full rounded-2xl px-5 py-4 text-sm font-extrabold text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg active:scale-95', accentBg]">Добавить в заявку</button>
            </article>
          </div>
        </div>
      </section>
    </main>

    <nav class="fixed inset-x-3 bottom-3 z-50 rounded-[1.6rem] border border-slate-200/70 bg-white/90 p-2 shadow-[0_18px_60px_rgb(15,23,42,0.16)] backdrop-blur-xl md:hidden">
      <div class="grid grid-cols-4 gap-1">
        <a href="index.php" :class="mobileNavClass(true)">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 10v10h14V10"/></svg>
          <span>Каталог</span>
        </a>
        <button @click="view = view === 'table' ? 'grid' : 'table'" :class="mobileNavClass(false)">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
          <span>Вид</span>
        </button>
        <a :href="'tel:' + settings.phone" :class="mobileNavClass(false)">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3.1 5.18 2 2 0 0 1 5.11 3h3a2 2 0 0 1 2 1.72c.12.9.33 1.77.63 2.6a2 2 0 0 1-.45 2.11L9 10.7a16 16 0 0 0 4.3 4.3l1.27-1.27a2 2 0 0 1 2.11-.45c.83.3 1.7.51 2.6.63A2 2 0 0 1 22 16.92Z"/></svg>
          <span>Звонок</span>
        </a>
        <a href="checkout.php" :class="mobileNavClass(false)" class="relative">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
          <span>Корзина</span>
          <b :class="['absolute right-2 top-1 rounded-full px-1.5 text-[10px] text-white', accentBg]">{{ cartCount }}</b>
        </a>
      </div>
    </nav>
  </div>
  <div id="app-fallback" class="fallback">
    <h1>Сайт загружается</h1>
    <p>Если каталог не появился через несколько секунд, браузер не смог загрузить скрипты интерфейса. Попробуйте обновить страницу или открыть сайт в другом браузере.</p>
    <a href="index.php?v=3">Обновить</a>
    <a href="admin.php?v=3">Админка</a>
  </div>

  <script>
    if (!window.Vue) {
      throw new Error('Vue CDN failed to load');
    }

    const { createApp } = Vue;

    const QtyControl = {
      props: ['modelValue'],
      emits: ['update:modelValue'],
      template: `
        <div class="inline-flex items-center rounded-2xl border border-slate-100 bg-slate-50 p-1">
          <button type="button" @click="update(Math.max(1, Number(modelValue) - 1))" class="grid h-9 w-9 place-items-center rounded-xl bg-white font-extrabold text-slate-700 shadow-sm transition active:scale-95">−</button>
          <input :value="modelValue" @input="update($event.target.value)" type="number" min="1" class="h-9 w-14 bg-transparent text-center text-sm font-extrabold outline-none">
          <button type="button" @click="update(Number(modelValue) + 1)" class="grid h-9 w-9 place-items-center rounded-xl bg-white font-extrabold text-slate-700 shadow-sm transition active:scale-95">+</button>
        </div>
      `,
      methods: {
        update(value) {
          const normalized = Math.max(1, parseInt(value || 1, 10));
          this.$emit('update:modelValue', normalized);
        }
      }
    };

    const PriceBlock = {
      props: ['product', 'qty'],
      template: `
        <div class="min-w-[130px]">
          <div v-if="Number(qty) >= 10" class="space-y-0.5">
            <div class="text-xs font-bold text-slate-400 line-through number-smooth">{{ money(product.price_base) }}</div>
            <div class="text-lg font-extrabold text-emerald-600 number-smooth">{{ money(product.price_wholesale) }}</div>
          </div>
          <div v-else class="space-y-0.5">
            <div class="text-lg font-extrabold text-slate-950 number-smooth">{{ money(product.price_base) }}</div>
            <div class="text-xs font-bold text-slate-400">опт от 10 шт: {{ money(product.price_wholesale) }}</div>
          </div>
        </div>
      `,
      methods: {
        money(value) {
          return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', maximumFractionDigits: 0 }).format(value);
        }
      }
    };

    createApp({
      components: { QtyControl, PriceBlock },
      data() {
        return {
          settings: { site_name: 'tmopro.ru — Сантехника Оптом', site_short_name: 'tmopro.ru', phone: '+7 (800) 555-35-35', email_manager: 'info@tmopro.ru', theme_color: 'indigo', default_view: 'table', logo_type: 'text', logo_text: 'TMO', logo_url: '', background_type: 'gradient', background_color: '#f8fafc', background_image: '', hero_title: 'Премиальная сантехника оптом для комплектации объектов.', hero_subtitle: 'Подберите позиции, укажите количество и отправьте заявку на счет. Оптовая цена включается автоматически от 10 штук.' },
          products: [],
          categories: [],
          qty: {},
          selectedCategories: [],
          selectedBrands: [],
          search: '',
          view: 'table',
          loading: true,
          cartBump: false
        };
      },
      computed: {
        categoryList() { return this.categories; },
        brands() { return [...new Set(this.products.map(item => item.brand))]; },
        cart() { return JSON.parse(localStorage.getItem('tmopro_cart') || '[]'); },
        cartCount() { return this.cart.reduce((sum, item) => sum + Number(item.qty), 0); },
        filteredProducts() {
          return this.products.filter(product => {
            const byCategory = !this.selectedCategories.length || this.selectedCategories.includes(product.category);
            const byBrand = !this.selectedBrands.length || this.selectedBrands.includes(product.brand);
            const bySearch = !this.search || product.article.toLowerCase().includes(this.search.toLowerCase());
            return byCategory && byBrand && bySearch;
          });
        },
        accentBg() { return { indigo: 'bg-indigo-600', emerald: 'bg-emerald-600', slate: 'bg-slate-900' }[this.settings.theme_color] || 'bg-indigo-600'; },
        accentText() { return { indigo: 'text-indigo-700', emerald: 'text-emerald-700', slate: 'text-slate-700' }[this.settings.theme_color] || 'text-indigo-700'; },
        accentSoft() { return { indigo: 'bg-indigo-50', emerald: 'bg-emerald-50', slate: 'bg-slate-100' }[this.settings.theme_color] || 'bg-indigo-50'; },
        accentRing() { return { indigo: 'ring-indigo-100', emerald: 'ring-emerald-100', slate: 'ring-slate-200' }[this.settings.theme_color] || 'ring-indigo-100'; },
        accentBorder() { return { indigo: 'border-indigo-600', emerald: 'border-emerald-600', slate: 'border-slate-900' }[this.settings.theme_color] || 'border-indigo-600'; },
        pageStyle() {
          if (this.settings.background_type === 'image' && this.settings.background_image) {
            return { backgroundImage: `linear-gradient(rgba(248,250,252,.82), rgba(248,250,252,.96)), url('${this.settings.background_image}')`, backgroundSize: 'cover', backgroundAttachment: 'fixed', backgroundPosition: 'center' };
          }
          if (this.settings.background_type === 'solid') {
            return { backgroundColor: this.settings.background_color || '#f8fafc' };
          }
          return {};
        }
      },
      async mounted() {
        try {
          const [settingsResponse, productsResponse, categoriesResponse] = await Promise.all([fetch('settings.json'), fetch('products.json'), fetch('categories.json')]);
          this.settings = await settingsResponse.json();
          this.products = await productsResponse.json();
          this.categories = await categoriesResponse.json();
          this.view = this.settings.default_view || 'table';
          this.products.forEach(product => this.qty[product.id] = 1);
          document.title = this.settings.site_name;
        } finally {
          this.loading = false;
        }
      },
      methods: {
        money(value) { return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', maximumFractionDigits: 0 }).format(value); },
        setQty(id, value) { this.qty[id] = Math.max(1, parseInt(value || 1, 10)); },
        toggleCategory(category) { this.selectedCategories = this.toggle(this.selectedCategories, category); },
        toggleBrand(brand) { this.selectedBrands = this.toggle(this.selectedBrands, brand); },
        toggle(list, value) { return list.includes(value) ? list.filter(item => item !== value) : [...list, value]; },
        countBy(field, value) { return this.products.filter(product => product[field] === value).length; },
        resetFilters() { this.selectedCategories = []; this.selectedBrands = []; this.search = ''; },
        viewButton(target) {
          return ['rounded-xl px-4 py-2 text-sm font-extrabold transition-all duration-300', this.view === target ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-400 hover:text-slate-950'];
        },
        mobileNavClass(active) {
          return ['flex flex-col items-center justify-center gap-1 rounded-2xl py-2 text-[11px] font-extrabold transition active:scale-95', active ? this.accentBg + ' text-white shadow-md' : 'text-slate-500'];
        },
        addToCart(product) {
          const cart = JSON.parse(localStorage.getItem('tmopro_cart') || '[]');
          const amount = Number(this.qty[product.id] || 1);
          const existing = cart.find(item => item.id === product.id);
          if (existing) {
            existing.qty += amount;
          } else {
            cart.push({ ...product, qty: amount });
          }
          localStorage.setItem('tmopro_cart', JSON.stringify(cart));
          this.cartBump = false;
          requestAnimationFrame(() => this.cartBump = true);
          setTimeout(() => this.cartBump = false, 700);
        }
      }
    }).mount('#app');
    document.getElementById('app-fallback')?.remove();

    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => navigator.serviceWorker.register('sw.js'));
    }
  </script>
</body>
</html>

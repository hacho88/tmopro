<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Оформление заказа — tmopro.ru</title>
  <meta name="theme-color" content="#4f46e5">
  <link rel="icon" href="icon.svg" type="image/svg+xml">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="vue.global.prod.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'ui-sans-serif', 'system-ui', 'Segoe UI', 'Arial']
          }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    [v-cloak] { display: none; }
    :root { --tmo-accent-rgb: 79,70,229; }
  </style>
</head>
<body class="bg-slate-50/50 text-slate-950 antialiased">
  <div id="app" v-cloak class="min-h-screen">
    <div class="pointer-events-none fixed inset-0 -z-10">
      <div :class="['absolute left-1/2 top-[-220px] h-[440px] w-[760px] -translate-x-1/2 rounded-full blur-3xl opacity-20', accentBg]"></div>
    </div>

    <header class="border-b border-slate-100 bg-white/75 backdrop-blur-xl">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="index.php" class="flex items-center gap-3">
          <span :class="['grid h-11 w-11 place-items-center rounded-2xl text-white shadow-[0_8px_30px_rgb(0,0,0,0.08)]', accentBg]">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18h-5"/><path d="M18 14H8"/><path d="M4 22h16"/><path d="M6 22V5a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3v17"/></svg>
          </span>
          <span>
            <span class="block text-lg font-extrabold tracking-tight">tmopro.ru</span>
            <span class="block text-xs font-medium text-slate-500">оформление заявки</span>
          </span>
        </a>
        <a href="index.php" class="rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm font-bold text-slate-600 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition hover:-translate-y-0.5 hover:text-slate-950 hover:shadow-md">Вернуться в каталог</a>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
      <div class="mb-8">
        <div :class="['mb-4 inline-flex rounded-full px-4 py-2 text-sm font-bold ring-1 ring-inset', accentSoft, accentText, accentRing]">Финальный шаг</div>
        <h1 class="text-4xl font-extrabold tracking-[-0.04em] sm:text-5xl">Запрос счета и резерва</h1>
        <p class="mt-4 max-w-2xl text-lg leading-8 text-slate-600">Заполните реквизиты компании. Менеджер проверит наличие, зафиксирует цены и отправит счет.</p>
      </div>

      <form action="send_order.php" method="post" @submit="prepareSubmit" class="grid gap-6 lg:grid-cols-[1fr_430px]">
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:p-8">
          <h2 class="mb-6 text-xl font-extrabold">Данные юридического лица</h2>
          <div class="grid gap-5 sm:grid-cols-2">
            <label class="sm:col-span-2"><span class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-400">Название компании / ИП</span><input name="company" required class="w-full rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4 font-semibold outline-none transition focus:border-transparent focus:bg-white focus:ring-4 focus:ring-[rgba(var(--tmo-accent-rgb),0.18)]"></label>
            <label><span class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-400">ИНН</span><input name="inn" required class="w-full rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4 font-semibold outline-none transition focus:border-transparent focus:bg-white focus:ring-4 focus:ring-[rgba(var(--tmo-accent-rgb),0.18)]"></label>
            <label><span class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-400">Контактное лицо</span><input name="contact_person" required class="w-full rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4 font-semibold outline-none transition focus:border-transparent focus:bg-white focus:ring-4 focus:ring-[rgba(var(--tmo-accent-rgb),0.18)]"></label>
            <label><span class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-400">Телефон</span><input name="phone" type="tel" required class="w-full rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4 font-semibold outline-none transition focus:border-transparent focus:bg-white focus:ring-4 focus:ring-[rgba(var(--tmo-accent-rgb),0.18)]"></label>
            <label><span class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-400">Email</span><input name="email" type="email" required class="w-full rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4 font-semibold outline-none transition focus:border-transparent focus:bg-white focus:ring-4 focus:ring-[rgba(var(--tmo-accent-rgb),0.18)]"></label>
            <label class="sm:col-span-2"><span class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-400">Промокод (если есть)</span><input name="coupon_code" class="w-full rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4 font-semibold outline-none transition focus:border-transparent focus:bg-white focus:ring-4 focus:ring-[rgba(var(--tmo-accent-rgb),0.18)]" placeholder="Введите код скидки"></label>
          </div>
          <input type="hidden" name="cart_json" :value="cartJson">
          <button type="submit" :disabled="cart.length === 0" :class="['mt-8 w-full rounded-2xl px-6 py-5 text-base font-extrabold text-white shadow-lg transition-all duration-300 hover:-translate-y-0.5 active:scale-[.99] disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none', cart.length ? accentBg : 'bg-slate-300']">Запросить счет и резерв</button>
        </section>

        <aside class="h-fit rounded-2xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] lg:sticky lg:top-8">
          <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-extrabold">Summary</h2>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">{{ totalQty }} шт</span>
          </div>

          <div v-if="cart.length === 0" class="rounded-2xl bg-slate-50 p-6 text-center">
            <p class="font-bold text-slate-500">Корзина пуста</p>
            <a href="index.php" :class="['mt-4 inline-flex rounded-2xl px-5 py-3 text-sm font-extrabold text-white', accentBg]">Выбрать товары</a>
          </div>

          <div v-else class="space-y-3">
            <div v-for="item in cart" :key="item.id" class="rounded-2xl bg-slate-50 p-4">
              <div class="flex justify-between gap-4">
                <div>
                  <div class="font-extrabold leading-snug">{{ item.name }}</div>
                  <div class="mt-1 text-xs font-bold text-slate-400">{{ item.article }} · {{ item.brand }}</div>
                </div>
                <button type="button" @click="removeItem(item.id)" class="h-8 w-8 shrink-0 rounded-xl bg-white text-slate-400 transition hover:text-red-500">×</button>
              </div>
              <div class="mt-4 flex items-center justify-between text-sm font-bold text-slate-500">
                <span>{{ item.qty }} × {{ money(unitPrice(item)) }}</span>
                <span class="text-slate-950">{{ money(lineTotal(item)) }}</span>
              </div>
            </div>

            <div class="mt-6 space-y-3 border-t border-slate-100 pt-5">
              <div class="flex justify-between text-sm font-bold text-slate-500"><span>Сумма по рознице</span><span>{{ money(baseTotal) }}</span></div>
              <div class="flex justify-between text-sm font-bold text-emerald-600"><span>Оптовая экономия</span><span>−{{ money(savings) }}</span></div>
              <div class="flex justify-between text-xl font-extrabold"><span>Итого</span><span>{{ money(total) }}</span></div>
            </div>
          </div>
        </aside>
      </form>
    </main>
  </div>

  <script>
    const { createApp } = Vue;
    createApp({
      data() {
        return {
          settings: { theme_color: 'indigo' },
          cart: []
        };
      },
      computed: {
        cartJson() { return JSON.stringify(this.cart); },
        totalQty() { return this.cart.reduce((sum, item) => sum + Number(item.qty), 0); },
        baseTotal() { return this.cart.reduce((sum, item) => sum + Number(item.price_base) * Number(item.qty), 0); },
        total() { return this.cart.reduce((sum, item) => sum + this.lineTotal(item), 0); },
        savings() { return Math.max(0, this.baseTotal - this.total); },
        accentBg() { return { indigo: 'bg-indigo-600', emerald: 'bg-emerald-600', slate: 'bg-slate-900' }[this.settings.theme_color] || 'bg-indigo-600'; },
        accentText() { return { indigo: 'text-indigo-700', emerald: 'text-emerald-700', slate: 'text-slate-700' }[this.settings.theme_color] || 'text-indigo-700'; },
        accentSoft() { return { indigo: 'bg-indigo-50', emerald: 'bg-emerald-50', slate: 'bg-slate-100' }[this.settings.theme_color] || 'bg-indigo-50'; },
        accentRing() { return { indigo: 'ring-indigo-100', emerald: 'ring-emerald-100', slate: 'ring-slate-200' }[this.settings.theme_color] || 'ring-indigo-100'; }
      },
      async mounted() {
        this.cart = JSON.parse(localStorage.getItem('tmopro_cart') || '[]');
        const settingsResponse = await fetch('settings.json');
        this.settings = await settingsResponse.json();
        const rgbMap = { indigo: '79,70,229', emerald: '5,150,105', slate: '15,23,42' };
        document.documentElement.style.setProperty('--tmo-accent-rgb', rgbMap[this.settings.theme_color] || '79,70,229');
      },
      methods: {
        unitPrice(item) { return Number(item.qty) >= 10 ? Number(item.price_wholesale) : Number(item.price_base); },
        lineTotal(item) { return this.unitPrice(item) * Number(item.qty); },
        money(value) { return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', maximumFractionDigits: 0 }).format(value); },
        removeItem(id) {
          this.cart = this.cart.filter(item => item.id !== id);
          localStorage.setItem('tmopro_cart', JSON.stringify(this.cart));
        },
        prepareSubmit() {
          localStorage.removeItem('tmopro_cart');
        }
      }
    }).mount('#app');
  </script>
</body>
</html>

<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TMOPRO — Сантехника Оптом</title>
  <meta name="theme-color" content="#008A4E">
  <link rel="manifest" href="manifest.json">
  <link rel="icon" href="icon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <script src="vue.global.prod.js"></script>
  <style>
    .fallback { max-width: 760px; margin: 80px auto; padding: 32px; border-radius: 24px; background: #fff; box-shadow: 0 24px 80px rgba(15,23,42,.12); font-family: Inter, system-ui, sans-serif; color: #0f172a; }
    .fallback h1 { margin: 0 0 12px; font-size: 32px; line-height: 1.1; }
    .fallback p { margin: 0 0 20px; color: #64748b; line-height: 1.7; }
    .fallback a { display: inline-flex; margin-right: 10px; border-radius: 16px; background: #008A4E; color: #fff; padding: 13px 18px; font-weight: 800; text-decoration: none; }
    [v-cloak] { display: none !important; }
  </style>
</head>
<body>
  <div id="app" v-cloak class="min-h-screen">
    <!-- Header -->
    <header class="sticky top-0 z-40 glass border-b" style="border-color: rgba(0,0,0,0.05);">
      <div class="container flex items-center justify-between py-4 gap-4">
        <a href="index.php" class="flex items-center gap-3">
          <span v-if="settings.logo_type === 'image' && settings.logo_url" class="grid place-items-center overflow-hidden rounded-xl bg-white shadow-md" style="width: 44px; height: 44px;">
            <img :src="settings.logo_url" alt="TMOPRO" style="width: 100%; height: 100%; object-fit: contain; padding: 4px;">
          </span>
          <span v-else class="grid place-items-center rounded-xl text-white" :class="accentBg" style="width: 44px; height: 44px;">
            <span class="text-xs font-black">{{ settings.logo_text || 'TMO' }}</span>
          </span>
          <span class="min-w-0">
            <span class="block text-lg font-extrabold tracking-tight">{{ settings.site_short_name || 'TMOPRO' }}</span>
            <span class="block text-xs font-medium text-gray-500">сантехника оптом</span>
          </span>
        </a>

        <div class="hidden md:flex items-center gap-6">
          <a :href="'tel:' + settings.phone" class="text-sm font-semibold text-gray-700 transition hover:text-primary">{{ settings.phone }}</a>
          <a :href="'mailto:' + settings.email_manager" class="text-sm font-semibold text-gray-500 transition hover:text-gray-900">{{ settings.email_manager }}</a>
        </div>

        <a href="checkout.php" class="relative flex items-center gap-3 rounded-xl border bg-white px-4 py-3 text-sm font-bold shadow-sm transition hover-lift">
          <span :class="['grid place-items-center rounded-lg text-white', accentBg]" style="width: 32px; height: 32px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
          </span>
          <span class="hidden sm:block">Корзина</span>
          <span :class="['badge text-white', accentBg]" style="font-size: 11px; padding: 2px 8px;">{{ cartCount }}</span>
        </a>
      </div>
    </header>

    <!-- Hero -->
    <section class="hero-gradient text-white relative">
      <div class="hero-grid"></div>
      <div class="container relative z-10 py-14 lg:py-16">
        <div class="grid gap-10 lg:grid-cols-2 items-center">
          <div class="animate-fadeUp">
            <div class="inline-flex items-center gap-2 badge badge-primary mb-6" style="background: rgba(0,138,78,0.2); color: #4ade80; border: 1px solid rgba(74,222,128,0.2);">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              B2B-каталог с мгновенным расчетом опта
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tighter mb-6 leading-tight" style="max-width: 20ch;">
              {{ settings.hero_title }}
            </h1>
            <p class="text-base sm:text-lg text-gray-400 mb-8 max-w-xl leading-relaxed">
              {{ settings.hero_subtitle }}
            </p>
            <div class="flex flex-wrap gap-3">
              <a href="#catalog" class="btn btn-lg btn-primary">Перейти в каталог</a>
              <a :href="'tel:' + settings.phone" class="btn btn-lg btn-secondary">Позвонить нам</a>
            </div>
            <a href="#catalog" class="inline-flex items-center gap-2 mt-6 text-sm font-bold text-gray-300 transition" style="opacity:.9;">
              <span>Смотреть позиции и цены</span>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
            </a>
          </div>
          <div class="hidden lg:block animate-fadeUp delay-2">
            <div class="grid grid-cols-2 gap-4">
              <div class="card p-6 animate-fadeUp delay-1" style="background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                <div class="text-3xl font-black text-primary mb-1">99+</div>
                <div class="text-sm text-gray-400">SKU в наличии</div>
              </div>
              <div class="card p-6 animate-fadeUp delay-2 mt-8" style="background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                <div class="text-3xl font-black text-primary mb-1">TMOPRO</div>
                <div class="text-sm text-gray-400">Собственный бренд</div>
              </div>
              <div class="card p-6 animate-fadeUp delay-3" style="background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                <div class="text-3xl font-black text-accent mb-1">-30%</div>
                <div class="text-sm text-gray-400">Опт от 10 шт</div>
              </div>
              <div class="card p-6 animate-fadeUp delay-4 mt-8" style="background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                <div class="text-3xl font-black text-primary mb-1">24/7</div>
                <div class="text-sm text-gray-400">Поддержка клиентов</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Catalog -->
    <main id="catalog" class="container py-10 lg:py-16 catalog-shell">
      <div class="grid gap-8 lg:grid-cols-sidebar">
        <!-- Sidebar Filters -->
        <aside class="h-fit lg:sticky lg:top-24">
          <div class="card p-5 lg:p-6 mb-6">
            <div class="flex items-center justify-between mb-5">
              <h2 class="text-base font-extrabold">Фильтры</h2>
              <button @click="resetFilters" class="text-xs font-bold text-gray-400 transition hover:text-gray-900">Сбросить</button>
            </div>

            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Поиск по артикулу</label>
            <input v-model.trim="search" type="text" placeholder="Например, Cu001" class="input mb-6">

            <div class="mb-6">
              <div class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Категория</div>
              <div class="space-y-2">
                <div v-for="cat in categoryList" :key="cat.id">
                  <div class="text-xs font-bold text-gray-500 px-1 py-1">{{ cat.name }}</div>
                  <button v-for="sub in cat.subcategories" :key="sub.id" @click="toggleCategory(sub.name)"
                    :class="['chip w-full justify-between', selectedCategories.includes(sub.name) ? 'chip-active' : 'chip-default']">
                    <span>{{ sub.name }}</span>
                    <span class="opacity-60" style="font-size: 11px;">{{ countBy('category', sub.name) }}</span>
                  </button>
                </div>
              </div>
            </div>

            <div>
              <div class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Бренд</div>
              <div class="flex flex-wrap gap-2">
                <button v-for="brand in brands" :key="brand" @click="toggleBrand(brand)"
                  :class="['chip', selectedBrands.includes(brand) ? 'chip-active' : 'chip-default']">
                  {{ brand }}
                </button>
              </div>
            </div>
          </div>
        </aside>

        <!-- Products -->
        <div>
          <!-- Toolbar -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 p-4 card">
            <div class="text-sm font-bold text-gray-500">
              Найдено: <span class="text-gray-900 font-extrabold">{{ filteredProducts.length }}</span> товаров
            </div>
            <div class="flex rounded-xl bg-gray-100 p-1 gap-1">
              <button @click="view = 'table'" :class="['btn btn-sm', view === 'table' ? 'bg-white text-gray-900 shadow-sm' : 'btn-ghost']">Таблица</button>
              <button @click="view = 'grid'" :class="['btn btn-sm', view === 'grid' ? 'bg-white text-gray-900 shadow-sm' : 'btn-ghost']">Плитка</button>
            </div>
          </div>

          <!-- Loading -->
          <div v-if="loading" class="card p-12 text-center">
            <div :class="['mx-auto mb-4 animate-spin rounded-full border-4 border-t-transparent', accentBorder]" style="width: 40px; height: 40px;"></div>
            <p class="font-bold text-gray-500">Загружаем каталог...</p>
          </div>

          <!-- Table View -->
          <div v-else-if="view === 'table'" class="table-wrap">
            <table class="table min-w-900">
              <thead>
                <tr>
                  <th>Товар</th>
                  <th>Категория</th>
                  <th>Остаток</th>
                  <th>Цена</th>
                  <th>Кол-во</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="product in filteredProducts" :key="product.id" class="animate-fadeIn">
                  <td>
                    <div class="flex items-center gap-3">
                      <img v-if="product.image" :src="product.image" class="rounded-lg object-cover" style="width: 48px; height: 48px;">
                      <div>
                        <div class="font-extrabold text-gray-900">{{ product.name }}</div>
                        <div class="mt-1 text-xs font-bold text-gray-400">{{ product.article }}</div>
                      </div>
                    </div>
                  </td>
                  <td><span class="badge badge-gray">{{ product.category }}</span></td>
                  <td class="text-sm font-bold text-gray-500">{{ product.stock }} шт</td>
                  <td><price-block :product="product" :qty="qty[product.id]"></price-block></td>
                  <td>
                    <qty-control :model-value="qty[product.id]" @update:model-value="setQty(product.id, $event)"></qty-control>
                  </td>
                  <td>
                    <button @click="addToCart(product)" :class="['btn btn-sm btn-primary', cartBump ? 'animate-bounce' : '']">В корзину</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Grid View -->
          <div v-else class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            <article v-for="product in filteredProducts" :key="product.id" class="card-product animate-fadeIn">
              <div v-if="product.image" class="overflow-hidden" style="height: 200px;">
                <img :src="product.image" class="product-img">
              </div>
              <div class="p-5">
                <div class="flex items-start justify-between gap-3 mb-3">
                  <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">{{ product.article }}</div>
                    <h3 class="text-lg font-extrabold leading-snug">{{ product.name }}</h3>
                  </div>
                  <span class="badge badge-primary flex-shrink-0">{{ product.brand }}</span>
                </div>
                <div class="flex items-center justify-between mb-4 p-3 rounded-xl bg-gray-50">
                  <span class="text-sm font-bold text-gray-500">{{ product.category }}</span>
                  <span class="text-sm font-bold text-gray-500">{{ product.stock }} шт</span>
                </div>
                <div class="flex items-center justify-between gap-4 mb-4">
                  <price-block :product="product" :qty="qty[product.id]"></price-block>
                  <qty-control :model-value="qty[product.id]" @update:model-value="setQty(product.id, $event)"></qty-control>
                </div>
                <button @click="addToCart(product)" class="btn btn-primary w-full">Добавить в заявку</button>
              </div>
            </article>
          </div>
        </div>
      </div>
    </main>

    <!-- Mobile Navigation -->
    <nav class="mobile-nav md:hidden">
      <div class="grid grid-cols-4 gap-1">
        <a href="index.php" :class="['flex flex-col items-center justify-center gap-1 rounded-xl py-2 text-xs font-extrabold transition', 'bg-primary text-white shadow-md']">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 10v10h14V10"/></svg>
          <span>Каталог</span>
        </a>
        <button @click="view = view === 'table' ? 'grid' : 'table'" class="flex flex-col items-center justify-center gap-1 rounded-xl py-2 text-xs font-extrabold text-gray-500 transition">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
          <span>Вид</span>
        </button>
        <a :href="'tel:' + settings.phone" class="flex flex-col items-center justify-center gap-1 rounded-xl py-2 text-xs font-extrabold text-gray-500 transition">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3.1 5.18 2 2 0 0 1 5.11 3h3a2 2 0 0 1 2 1.72c.12.9.33 1.77.63 2.6a2 2 0 0 1-.45 2.11L9 10.7a16 16 0 0 0 4.3 4.3l1.27-1.27a2 2 0 0 1 2.11-.45c.83.3 1.7.51 2.6.63A2 2 0 0 1 22 16.92Z"/></svg>
          <span>Звонок</span>
        </a>
        <a href="checkout.php" class="relative flex flex-col items-center justify-center gap-1 rounded-xl py-2 text-xs font-extrabold text-gray-500 transition">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
          <span>Корзина</span>
          <b :class="['absolute top-1 rounded-full px-1.5 text-white', accentBg]" style="font-size: 10px; right: 8px;">{{ cartCount }}</b>
        </a>
      </div>
    </nav>

    <!-- Footer spacer for mobile -->
    <div class="md:hidden" style="height: 100px;"></div>
  </div>

  <div id="app-fallback" class="fallback">
    <h1>Сайт загружается</h1>
    <p>Если каталог не появился через несколько секунд, браузер не смог загрузить скрипты интерфейса. Попробуйте обновить страницу или открыть сайт в другом браузере.</p>
    <a href="index.php?v=3">Обновить</a>
    <a href="panel.php">Админка</a>
  </div>

  <script>
    if (!window.Vue) { throw new Error('Vue CDN failed to load'); }
    const { createApp } = Vue;

    const QtyControl = {
      props: ['modelValue'],
      emits: ['update:modelValue'],
      template: `
        <div class="qty-control">
          <button type="button" @click="update(Math.max(1, Number(modelValue) - 1))" class="qty-btn">−</button>
          <input :value="modelValue" @input="update($event.target.value)" type="number" min="1" class="qty-input">
          <button type="button" @click="update(Number(modelValue) + 1)" class="qty-btn">+</button>
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
        <div style="min-width: 130px;">
          <div v-if="Number(qty) >= 10" class="flex flex-col">
            <span class="text-xs font-bold text-gray-400 line-through number-smooth">{{ money(product.price_base) }}</span>
            <span class="text-lg font-extrabold text-primary number-smooth">{{ money(product.price_wholesale) }}</span>
          </div>
          <div v-else class="flex flex-col">
            <span class="text-lg font-extrabold text-gray-900 number-smooth">{{ money(product.price_base) }}</span>
            <span class="text-xs font-bold text-gray-400">опт от 10 шт: {{ money(product.price_wholesale) }}</span>
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
          settings: { site_name: 'TMOPRO — Сантехника Оптом', site_short_name: 'TMOPRO', phone: '+7 (966) 085-34-70', email_manager: 'info@tmopro.ru', theme_color: 'emerald', default_view: 'table', logo_type: 'text', logo_text: 'TMO', logo_url: '', background_type: 'gradient', background_color: '#f8fafc', background_image: '', hero_title: 'Сантехника оптом от производителя. Все на одной площадке.', hero_subtitle: 'Подберите позиции, укажите количество и отправьте заявку на счет. Оптовая цена включается автоматически от 10 штук.' },
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
            const bySearch = !this.search || product.article.toLowerCase().includes(this.search.toLowerCase()) || product.name.toLowerCase().includes(this.search.toLowerCase());
            return byCategory && byBrand && bySearch;
          });
        },
        accentBg() { return { indigo: 'bg-primary', emerald: 'bg-primary', slate: 'bg-dark-2' }[this.settings.theme_color] || 'bg-primary'; },
        accentBorder() { return { indigo: 'border-primary', emerald: 'border-primary', slate: 'border-dark-2' }[this.settings.theme_color] || 'border-primary'; }
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
        addToCart(product) {
          const cart = JSON.parse(localStorage.getItem('tmopro_cart') || '[]');
          const amount = Number(this.qty[product.id] || 1);
          const existing = cart.find(item => item.id === product.id);
          if (existing) { existing.qty += amount; }
          else { cart.push({ ...product, qty: amount }); }
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

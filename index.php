<?php
session_start();
$b2bUser = !empty($_SESSION['b2b_user_id']);
$b2bName = $_SESSION['b2b_user_name'] ?? '';
?>
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
  <link rel="stylesheet" href="style.css?v=lux-gold-g">
  <script src="vue.global.prod.js"></script>
  <style>
    .fallback { max-width: 760px; margin: 80px auto; padding: 32px; border-radius: 24px; background: #fff; box-shadow: 0 24px 80px rgba(15,23,42,.12); font-family: Inter, system-ui, sans-serif; color: #0f172a; }
    .fallback h1 { margin: 0 0 12px; font-size: 32px; line-height: 1.1; }
    .fallback p { margin: 0 0 20px; color: #64748b; line-height: 1.7; }
    .fallback a { display: inline-flex; margin-right: 10px; border-radius: 16px; background: #008A4E; color: #fff; padding: 13px 18px; font-weight: 800; text-decoration: none; }
    [v-cloak] { display: none !important; }
  </style>
</head>
<body class="theme-luxury">
  <div id="app" v-cloak class="min-h-screen">
    <!-- Header -->
    <header class="sticky top-0 z-40 lux-header">
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

        <nav class="hidden lg:flex items-center gap-8 lux-nav">
          <a href="#catalog" class="lux-nav-link">Продукция</a>
          <a href="#catalog" class="lux-nav-link">Решения</a>
          <a href="#catalog" class="lux-nav-link">Ресурсы</a>
          <a href="#catalog" class="lux-nav-link">О нас</a>
          <a href="#catalog" class="lux-nav-link">Контакты</a>
        </nav>

        <div class="hidden md:flex items-center gap-6">
          <a :href="'tel:' + settings.phone" class="lux-meta-link">{{ settings.phone }}</a>
          <a :href="'mailto:' + settings.email_manager" class="lux-meta-link">{{ settings.email_manager }}</a>
        </div>

        <div class="flex items-center gap-3">
          <?php if ($b2bUser): ?>
            <a href="profile.php" class="hidden sm:flex items-center gap-2 text-sm font-extrabold text-gray-700 hover:text-emerald-700 transition">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <?= htmlspecialchars($b2bName, ENT_QUOTES, 'UTF-8') ?>
            </a>
          <?php else: ?>
            <a href="login.php" class="hidden sm:flex items-center gap-2 text-sm font-extrabold text-gray-700 hover:text-emerald-700 transition">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
              Вход для клиентов
            </a>
          <?php endif; ?>
          <a href="checkout.php" class="relative lux-cart">
            <span class="lux-cart-icon" aria-hidden="true">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
            </span>
            <span class="hidden sm:block">Корзина</span>
            <span class="lux-cart-badge">{{ cartCount }}</span>
          </a>
          <a href="#catalog" class="hidden sm:inline-flex lux-btn-gold">
            <span>Запросить расчет</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="m13 5 7 7-7 7"/></svg>
          </a>
        </div>
      </div>
    </header>

    <!-- Hero -->
    <section class="lux-hero">
      <div class="lux-hero-bg" aria-hidden="true">
        <picture v-if="settings.background_image">
          <source v-if="settings.background_image_mobile" media="(max-width: 1024px)" :srcset="settings.background_image_mobile">
          <img :src="settings.background_image" alt="">
        </picture>
      </div>
      <div class="container lux-hero-inner">
        <div class="lux-hero-copy">
          <div class="lux-kicker">Премиальные решения для водоснабжения и отопления</div>
          <h1 class="lux-title">
            <span class="lux-title-strong">СОЗДАНО ДЛЯ</span>
            <span class="lux-title-accent">ПРОФЕССИОНАЛОВ</span>
          </h1>
          <p class="lux-subtitle">{{ settings.hero_subtitle }}</p>
          <div class="lux-hero-actions">
            <a href="#catalog" class="lux-btn-gold">
              <span>Получить расчет</span>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="m13 5 7 7-7 7"/></svg>
            </a>
            <a href="#catalog" class="lux-btn-ghost">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              <span>Скачать каталог</span>
            </a>
          </div>
        </div>
        <div class="lux-hero-media" aria-hidden="true">
          <div class="lux-hero-media-placeholder"></div>
        </div>
      </div>
    </section>

    <section class="container py-10 lg:py-16">
      <div class="section-head mb-8">
        <h2 class="text-2xl sm:text-3xl font-black tracking-tight">Категории</h2>
        <p class="text-sm sm:text-base text-gray-500 font-semibold mt-2">Быстрый доступ к основным группам товара</p>
      </div>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <button v-for="cat in topCategories" :key="cat.name" @click="toggleCategory(cat.name); document.getElementById('catalog')?.scrollIntoView({ behavior: 'smooth' });"
          class="category-tile hover-lift">
          <span class="category-media" aria-hidden="true">
            <img v-if="cat.image" :src="cat.image" class="category-media-img" @error="cat.image = ''">
            <span v-else class="category-media-placeholder"></span>
          </span>
          <span class="category-body">
            <span class="category-title">{{ cat.name }}</span>
            <span class="category-meta">{{ cat.count }} позиций</span>
          </span>
          <span class="category-icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
          </span>
        </button>
      </div>
    </section>

    <section class="container pb-10 lg:pb-0">
      <div class="grid gap-4 lg:grid-cols-3">
        <div class="card p-6 hover-lift">
          <div class="adv-icon mb-4" :class="accentBg">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
          </div>
          <div class="text-lg font-extrabold">Мгновенный расчет опта</div>
          <div class="text-sm font-semibold text-gray-500 mt-2">Цена автоматически пересчитывается от количества</div>
        </div>
        <div class="card p-6 hover-lift">
          <div class="adv-icon mb-4 bg-dark">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 7h-7"/><path d="M14 17H7"/><path d="M7 7v10"/><path d="M17 7v10"/></svg>
          </div>
          <div class="text-lg font-extrabold">Прайс как инструмент</div>
          <div class="text-sm font-semibold text-gray-500 mt-2">Плитка или таблица — выбирай удобный режим</div>
        </div>
        <div class="card p-6 hover-lift">
          <div class="adv-icon mb-4 bg-accent">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 8v4l3 3"/><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
          </div>
          <div class="text-lg font-extrabold">Быстрая поддержка</div>
          <div class="text-sm font-semibold text-gray-500 mt-2">Ответим по наличию, доставке и условиям</div>
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
            <live-search v-model="search" @select="onSearchSelect"></live-search>
            <div class="mb-4"></div>

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
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 p-4 toolbar-glass">
            <div class="text-sm font-bold text-gray-500">
              Найдено: <span class="text-gray-900 font-extrabold">{{ filteredProducts.length }}</span> товаров
            </div>
            <div class="flex items-center gap-3">
              <button type="button" @click="toggleDense" :class="['density-toggle', dense ? 'is-on' : '']">
                <span class="density-dot" aria-hidden="true"></span>
                <span>Плотно</span>
              </button>
              <div class="segmented">
                <button @click="view = 'grid'" :class="['segmented-item', view === 'grid' ? 'is-active' : '']">Плитка</button>
                <button @click="view = 'table'" :class="['segmented-item', view === 'table' ? 'is-active' : '']">Таблица</button>
              </div>
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
                      <a :href="'product.php?id=' + product.id" class="shrink-0">
                        <img v-if="product.image" :src="product.image" @error="onProductImgError(product)" class="rounded-lg object-cover" style="width: 48px; height: 48px;">
                        <span v-else class="table-img-placeholder" aria-hidden="true" style="display:inline-block;width:48px;height:48px;"></span>
                      </a>
                      <div>
                        <a :href="'product.php?id=' + product.id" class="font-extrabold text-gray-900 hover:underline" style="text-decoration:none;">{{ product.name }}</a>
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
          <div v-else :class="['grid gap-5 sm:grid-cols-2', dense ? 'xl:grid-cols-4 dense-grid' : 'xl:grid-cols-3']">
            <article v-for="product in filteredProducts" :key="product.id" class="card-product animate-fadeIn">
              <a :href="'product.php?id=' + product.id" class="block">
                <div class="product-media">
                  <img v-if="product.image" :src="product.image" class="product-img" @error="onProductImgError(product)">
                  <div v-else class="product-img-placeholder" aria-hidden="true"></div>
                </div>
              </a>
              <div class="p-5">
                <div class="flex items-start justify-between gap-3 mb-3">
                  <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">{{ product.article }}</div>
                    <a :href="'product.php?id=' + product.id" class="block" style="text-decoration:none;">
                      <h3 class="text-lg font-extrabold leading-snug text-gray-900 hover:underline">{{ product.name }}</h3>
                    </a>
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

    const LiveSearch = {
      props: ['modelValue'],
      emits: ['update:modelValue', 'select'],
      data() {
        return {
          query: this.modelValue || '',
          results: [],
          show: false,
          activeIndex: -1,
          loading: false,
          debounceTimer: null
        };
      },
      watch: {
        modelValue(val) { this.query = val; }
      },
      template: `
        <div class="live-search" style="position:relative;">
          <input
            v-model="query"
            @input="onInput"
            @keydown.down.prevent="moveDown"
            @keydown.up.prevent="moveUp"
            @keydown.enter.prevent="selectActive"
            @keydown.esc="show = false"
            @focus="onFocus"
            @blur="onBlur"
            type="text"
            placeholder="Например, Cu001"
            class="input"
            style="width:100%;"
            autocomplete="off"
          >
          <div v-if="loading" class="live-search-loader">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>
          </div>
          <div v-if="show && results.length" class="live-search-dropdown">
            <a
              v-for="(item, idx) in results"
              :key="item.id"
              :href="'product.php?id=' + item.id"
              @mouseenter="activeIndex = idx"
              :class="['live-search-item', idx === activeIndex ? 'is-active' : '']"
              @click.prevent="goToProduct(item)"
            >
              <div class="live-search-img-wrap">
                <img v-if="item.image" :src="item.image" class="live-search-img">
                <div v-else class="live-search-img-placeholder"></div>
              </div>
              <div class="live-search-info">
                <div class="live-search-name">{{ item.name }}</div>
                <div class="live-search-meta">{{ item.article }} · {{ item.brand }} · {{ item.category }}</div>
                <div class="live-search-price">{{ money(item.price_base) }}</div>
              </div>
            </a>
          </div>
          <div v-else-if="show && query.length >= 2 && !loading && !results.length" class="live-search-dropdown">
            <div class="live-search-empty">Ничего не найдено</div>
          </div>
        </div>
      `,
      methods: {
        money(value) {
          return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', maximumFractionDigits: 0 }).format(value);
        },
        onInput() {
          this.$emit('update:modelValue', this.query);
          clearTimeout(this.debounceTimer);
          if (this.query.length < 2) { this.results = []; this.show = false; return; }
          this.loading = true;
          this.debounceTimer = setTimeout(() => this.fetchResults(), 250);
        },
        async fetchResults() {
          try {
            const res = await fetch('api/search.php?q=' + encodeURIComponent(this.query) + '&limit=8');
            const data = await res.json();
            this.results = data.results || [];
            this.activeIndex = -1;
            this.show = true;
          } catch (e) {
            this.results = [];
          } finally {
            this.loading = false;
          }
        },
        onFocus() {
          if (this.query.length >= 2 && this.results.length) this.show = true;
        },
        onBlur() {
          setTimeout(() => { this.show = false; }, 200);
        },
        moveDown() {
          if (!this.results.length) return;
          this.activeIndex = (this.activeIndex + 1) % this.results.length;
        },
        moveUp() {
          if (!this.results.length) return;
          this.activeIndex = (this.activeIndex - 1 + this.results.length) % this.results.length;
        },
        selectActive() {
          if (this.activeIndex >= 0 && this.results[this.activeIndex]) {
            this.goToProduct(this.results[this.activeIndex]);
          }
        },
        goToProduct(item) {
          this.$emit('select', item);
          window.location.href = 'product.php?id=' + item.id;
        }
      }
    };

    createApp({
      components: { QtyControl, PriceBlock, LiveSearch },
      data() {
        return {
          settings: { site_name: 'TMOPRO — Сантехника Оптом', site_short_name: 'TMOPRO', phone: '+7 (966) 085-34-70', email_manager: 'info@tmopro.ru', theme_color: 'emerald', default_view: 'table', logo_type: 'text', logo_text: 'TMO', logo_url: '', background_type: 'gradient', background_color: '#f8fafc', background_image: '', background_image_mobile: '', hero_title: 'Сантехника оптом от производителя. Все на одной площадке.', hero_subtitle: 'Подберите позиции, укажите количество и отправьте заявку на счет. Оптовая цена включается автоматически от 10 штук.' },
          products: [],
          categories: [],
          qty: {},
          selectedCategories: [],
          selectedBrands: [],
          search: '',
          view: 'grid',
          dense: false,
          loading: true,
          cartBump: false
        };
      },
      computed: {
        categoryList() { return this.categories; },
        topCategories() {
          const byName = new Map();
          (this.categories || []).forEach(cat => {
            (cat.subcategories || []).forEach(sub => {
              const name = (sub && sub.name) ? String(sub.name) : '';
              if (!name) return;
              const count = this.countBy('category', name);
              const image = (sub && sub.image) ? String(sub.image) : '';
              const existing = byName.get(name);
              const shouldReplace = !existing
                || (Number(existing.count) || 0) < (count || 0)
                || (!existing.image && image);
              if (shouldReplace) byName.set(name, { name, count, image });
            });
          });
          return Array.from(byName.values()).sort((a, b) => (b.count || 0) - (a.count || 0)).slice(0, 9);
        },
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
          this.view = this.settings.default_view || 'grid';
          this.dense = localStorage.getItem('tmopro_dense') === '1';
          this.products.forEach(product => this.qty[product.id] = 1);
          document.title = this.settings.site_name;
        } finally {
          this.loading = false;
        }
      },
      methods: {
        money(value) { return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB', maximumFractionDigits: 0 }).format(value); },
        onProductImgError(product) { try { product.image = ''; } catch (e) {} },
        setQty(id, value) { this.qty[id] = Math.max(1, parseInt(value || 1, 10)); },
        toggleDense() {
          this.dense = !this.dense;
          localStorage.setItem('tmopro_dense', this.dense ? '1' : '0');
        },
        toggleCategory(category) { this.selectedCategories = this.toggle(this.selectedCategories, category); },
        toggleBrand(brand) { this.selectedBrands = this.toggle(this.selectedBrands, brand); },
        toggle(list, value) { return list.includes(value) ? list.filter(item => item !== value) : [...list, value]; },
        countBy(field, value) { return this.products.filter(product => product[field] === value).length; },
        resetFilters() { this.selectedCategories = []; this.selectedBrands = []; this.search = ''; },
        onSearchSelect(item) {
          this.search = item.article || item.name;
        },
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

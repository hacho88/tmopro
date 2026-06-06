<?php
$f = file_get_contents('index.php');

// 1. Replace hero fallback + else wrapper with just opening comment
$fallback = '    <!-- Dynamic Blocks -->
    <?php if (empty($blocks)): ?>
      <!-- Hero — Premium Dark -->
      <section class="relative bg-[#0d0d0d] min-h-[520px] md:min-h-[580px] w-full overflow-hidden flex items-center">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
          <img src="<?= e($heroBg) ?>" alt="" class="w-full h-full object-cover object-center" onerror="this.style.display=\'none\'">
        </div>
        <!-- Dark Overlay -->
        <div class="absolute inset-0 z-10" style="background: linear-gradient(90deg, rgba(0,0,0,0.92) 0%, rgba(0,0,0,0.75) 45%, rgba(0,0,0,0.35) 100%);"></div>
        <!-- Content -->
        <div class="relative z-20 max-w-7xl mx-auto px-4 w-full pt-24 pb-16">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
              <span class="text-xs font-semibold uppercase tracking-[0.15em] mb-3 block" style="color: #d4af37;">ПРЕМИАЛЬНАЯ САНТЕХНИКА</span>
              <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight mb-4">Сантехника оптом<br>от производителя</h1>
              <p class="text-base md:text-lg mb-8 max-w-lg" style="color: #9ca3af;">TMOPRO — ваш надежный партнер в сфере оптовых поставок сантехники премиум-класса.</p>
              <div class="flex flex-wrap gap-3">
                <a href="#catalog" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-sm transition-all duration-200 hover:brightness-110" style="background: #d4af37; color: #000;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/></svg>
                  МГНОВЕННЫЙ РАСЧЕТ
                </a>
                <a href="#catalog" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-sm border border-white/30 text-white transition-all duration-200 hover:border-white hover:bg-white/10">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  СКАЧАТЬ ПРАЙС-ЛИСТ
                </a>
              </div>
            </div>
            <div class="hidden md:block"></div>
          </div>
          <!-- Bottom right badge -->
          <div class="absolute bottom-6 right-4 md:right-8 flex items-center gap-3 px-5 py-3 rounded-xl backdrop-blur-md border border-white/10" style="background: rgba(13,13,13,0.7);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d4af37" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <div>
              <div class="text-[11px] font-bold uppercase tracking-wider" style="color: #d4af37;">Гарантия качества</div>
              <div class="text-[10px] text-gray-400 mt-0.5">Вся продукция сертифицирована</div>
            </div>
          </div>
        </div>
      </section>
    <?php else: ?>';

$f = str_replace($fallback, '    <!-- Dynamic Blocks -->', $f);

// 2. Remove hardcoded features section
$feat = '
    <!-- Features — dark premium -->
    <section class="bg-[#121212] border-y border-white/[0.06]">
      <div class="max-w-7xl mx-auto px-4 w-full py-10 md:py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full border flex items-center justify-center flex-shrink-0" style="border-color: rgba(212,175,55,0.25); color: #d4af37;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2v20M2 12h20"/></svg>
            </div>
            <div>
              <div class="text-sm font-semibold text-white uppercase tracking-wider">Оптовые цены</div>
              <div class="text-xs mt-1" style="color: #9ca3af;">Прямые поставки от производителя без посредников</div>
            </div>
          </div>
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full border flex items-center justify-center flex-shrink-0" style="border-color: rgba(212,175,55,0.25); color: #d4af37;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16v16H4z"/><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/></svg>
            </div>
            <div>
              <div class="text-sm font-semibold text-white uppercase tracking-wider">Мгновенный расчет</div>
              <div class="text-xs mt-1" style="color: #9ca3af;">Цена автоматически пересчитывается от количества</div>
            </div>
          </div>
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full border flex items-center justify-center flex-shrink-0" style="border-color: rgba(212,175,55,0.25); color: #d4af37;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
              <div class="text-sm font-semibold text-white uppercase tracking-wider">Быстрая доставка</div>
              <div class="text-xs mt-1" style="color: #9ca3af;">Отгрузка в день заказа по всей России</div>
            </div>
          </div>
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full border flex items-center justify-center flex-shrink-0" style="border-color: rgba(212,175,55,0.25); color: #d4af37;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div>
              <div class="text-sm font-semibold text-white uppercase tracking-wider">Сертификация</div>
              <div class="text-xs mt-1" style="color: #9ca3af;">Вся продукция с официальными сертификатами</div>
            </div>
          </div>
        </div>
      </div>
    </section>';

$f = str_replace($feat, '', $f);

// 3. Remove endif after blocks loop (there's a stray <?php endif; ?> after the foreach)
$f = str_replace("        <?php endif; ?>\n    <?php endif; ?>\n", "        <?php endif; ?>\n", $f);

file_put_contents('index.php', $f);
echo "done\n";

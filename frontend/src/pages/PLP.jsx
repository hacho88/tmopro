import { useState, useMemo } from 'react'
import { SlidersHorizontal, Grid3X3, LayoutList, ChevronDown } from 'lucide-react'
import { products } from '../data/catalogData'
import ProductCard from '../components/ProductCard'
import SidebarFilters from '../components/SidebarFilters'
import BottomSheet from '../components/BottomSheet'

function PLP() {
  const [viewMode, setViewMode] = useState('grid')
  const [mobileFiltersOpen, setMobileFiltersOpen] = useState(false)
  const [activeFilters, setActiveFilters] = useState({})
  const [sortBy, setSortBy] = useState('default')

  const toggleFilter = (groupId, optionId) => {
    setActiveFilters((prev) => {
      const current = prev[groupId] || []
      const updated = current.includes(optionId)
        ? current.filter((id) => id !== optionId)
        : [...current, optionId]
      return { ...prev, [groupId]: updated }
    })
  }

  const clearFilters = () => setActiveFilters({})

  const filteredProducts = useMemo(() => {
    let result = [...products]

    Object.entries(activeFilters).forEach(([groupId, optionIds]) => {
      if (optionIds.length === 0) return
      result = result.filter((p) => {
        const val = p[groupId]
        if (!val) return false
        return optionIds.includes(val)
      })
    })

    if (sortBy === 'price-asc') result.sort((a, b) => a.price - b.price)
    if (sortBy === 'price-desc') result.sort((a, b) => b.price - a.price)
    if (sortBy === 'rating') result.sort((a, b) => b.rating - a.rating)

    return result
  }, [activeFilters, sortBy])

  const activeFilterCount = Object.values(activeFilters).flat().length

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      {/* Breadcrumb + Title */}
      <div className="mb-8">
        <div className="text-xs font-bold text-slate-400 mb-2">Home / Catalog / Faucets & Taps</div>
        <h1 className="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Faucets & Taps</h1>
        <p className="text-sm text-slate-500 font-semibold mt-1">{filteredProducts.length} products found</p>
      </div>

      {/* Toolbar */}
      <div className="flex items-center justify-between gap-4 mb-6 p-3 bg-white rounded-2xl border border-slate-100">
        {/* Mobile filter button */}
        <button
          onClick={() => setMobileFiltersOpen(true)}
          className="md:hidden flex items-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm font-bold rounded-xl"
        >
          <SlidersHorizontal size={16} />
          Filters {activeFilterCount > 0 && `(${activeFilterCount})`}
        </button>

        {/* Sort */}
        <div className="flex items-center gap-2 ml-auto">
          <span className="hidden sm:inline text-xs font-bold text-slate-400 uppercase tracking-wider">Sort</span>
          <div className="relative">
            <select
              value={sortBy}
              onChange={(e) => setSortBy(e.target.value)}
              className="appearance-none bg-surface-100 text-sm font-semibold text-slate-700 pl-4 pr-10 py-2 rounded-xl cursor-pointer outline-none focus:ring-2 focus:ring-tech-blue/20"
            >
              <option value="default">Default</option>
              <option value="price-asc">Price: Low to High</option>
              <option value="price-desc">Price: High to Low</option>
              <option value="rating">Highest Rated</option>
            </select>
            <ChevronDown size={14} className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
          </div>

          {/* View toggle */}
          <div className="hidden sm:flex items-center bg-surface-100 rounded-xl p-1">
            <button
              onClick={() => setViewMode('grid')}
              className={`p-2 rounded-lg transition-colors ${viewMode === 'grid' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-400 hover:text-slate-600'}`}
              aria-label="Grid view"
            >
              <Grid3X3 size={16} />
            </button>
            <button
              onClick={() => setViewMode('list')}
              className={`p-2 rounded-lg transition-colors ${viewMode === 'list' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-400 hover:text-slate-600'}`}
              aria-label="List view"
            >
              <LayoutList size={16} />
            </button>
          </div>
        </div>
      </div>

      {/* Content */}
      <div className="flex gap-8">
        {/* Desktop Sidebar */}
        <div className="hidden md:block w-64 flex-shrink-0">
          <div className="sticky top-24">
            <SidebarFilters
              activeFilters={activeFilters}
              onFilterChange={toggleFilter}
              onClear={clearFilters}
            />
          </div>
        </div>

        {/* Product Grid */}
        <div className="flex-1 min-w-0">
          {viewMode === 'grid' ? (
            <div className="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
              {filteredProducts.map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          ) : (
            <div className="space-y-4">
              {filteredProducts.map((product) => (
                <ProductListRow key={product.id} product={product} />
              ))}
            </div>
          )}

          {filteredProducts.length === 0 && (
            <div className="text-center py-20">
              <div className="text-5xl mb-4">🔧</div>
              <h3 className="text-lg font-extrabold text-slate-900 mb-2">No products found</h3>
              <p className="text-sm text-slate-500">Try adjusting your filters or search criteria.</p>
            </div>
          )}
        </div>
      </div>

      {/* Mobile Bottom Sheet */}
      <BottomSheet
        isOpen={mobileFiltersOpen}
        onClose={() => setMobileFiltersOpen(false)}
        activeFilters={activeFilters}
        onFilterChange={toggleFilter}
        onClear={clearFilters}
      />
    </div>
  )
}

function ProductListRow({ product }) {
  const stockColors = {
    'in-stock': 'bg-emerald-50 text-emerald-700',
    '2-3-days': 'bg-amber-50 text-amber-700',
    '1-week': 'bg-slate-100 text-slate-600',
  }

  return (
    <div className="bg-white rounded-2xl border border-slate-100 p-4 flex gap-5 items-center hover:shadow-lg transition-shadow">
      <div className="w-24 h-24 flex-shrink-0 bg-surface-100 rounded-xl overflow-hidden">
        <img src={product.image} alt={product.name} className="w-full h-full object-cover" loading="lazy" />
      </div>
      <div className="flex-1 min-w-0">
        <div className="flex items-center gap-2 mb-1">
          <span className="text-label">{product.article}</span>
          <span className={`text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-md ${stockColors[product.stockStatus]}`}>
            {product.stockLabel}
          </span>
        </div>
        <h3 className="text-sm font-bold text-slate-900 mb-1 truncate">{product.name}</h3>
        <div className="flex items-center gap-4 text-xs text-slate-500 font-semibold">
          <span>{product.brand}</span>
          <span>{product.category}</span>
        </div>
      </div>
      <div className="flex-shrink-0 text-right">
        <div className="text-lg font-black text-slate-900">{product.price.toLocaleString('ru-RU')} {product.currency}</div>
        {product.oldPrice && (
          <div className="text-sm text-slate-400 line-through">{product.oldPrice.toLocaleString('ru-RU')}</div>
        )}
        <button className="mt-2 btn-primary py-2 px-5 text-xs rounded-xl">Add to Cart</button>
      </div>
    </div>
  )
}

export default PLP

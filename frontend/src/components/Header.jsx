import { useState } from 'react'
import { Link } from 'react-router-dom'
import { Search, ShoppingCart, Phone, Menu, X, ChevronRight } from 'lucide-react'
import { megaMenuData } from '../data/catalogData'

function Header() {
  const [megaOpen, setMegaOpen] = useState(null)
  const [mobileOpen, setMobileOpen] = useState(false)
  const [searchOpen, setSearchOpen] = useState(false)

  return (
    <header className="sticky top-0 z-50 bg-slate-850 text-white">
      {/* Top bar */}
      <div className="border-b border-slate-700/50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 h-9 flex items-center justify-between text-xs">
          <div className="flex items-center gap-6 text-slate-300">
            <span className="flex items-center gap-1.5"><Phone size={12} /> +7 (966) 085-34-70</span>
            <span className="hidden sm:inline">info@tmopro.ru</span>
            <span className="hidden md:inline text-slate-400">Mon–Fri 9:00–18:00</span>
          </div>
          <div className="flex items-center gap-4 text-slate-300">
            <Link to="/" className="hover:text-white transition-colors">B2B Portal</Link>
            <Link to="/" className="hover:text-white transition-colors">Installers</Link>
          </div>
        </div>
      </div>

      {/* Main header */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <div className="flex items-center gap-8">
          <Link to="/" className="text-xl font-black tracking-tight">
            TMO<span className="text-tech-blue">PRO</span>
          </Link>

          {/* Desktop Nav */}
          <nav className="hidden lg:flex items-center gap-1">
            {megaMenuData.map((cat) => (
              <div
                key={cat.id}
                className="relative"
                onMouseEnter={() => setMegaOpen(cat.id)}
                onMouseLeave={() => setMegaOpen(null)}
              >
                <button className="px-4 py-2 text-sm font-semibold text-slate-200 hover:text-white transition-colors rounded-lg hover:bg-slate-700/50">
                  {cat.name}
                </button>
                {megaOpen === cat.id && <MegaMenuPanel category={cat} />}
              </div>
            ))}
          </nav>
        </div>

        {/* Right actions */}
        <div className="flex items-center gap-3">
          <button
            onClick={() => setSearchOpen(!searchOpen)}
            className="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-700/50 transition-colors"
            aria-label="Search"
          >
            <Search size={20} />
          </button>
          <button className="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-700/50 transition-colors relative" aria-label="Cart">
            <ShoppingCart size={20} />
            <span className="absolute top-1 right-1 w-4 h-4 bg-tech-blue rounded-full text-[10px] font-bold flex items-center justify-center">3</span>
          </button>
          <button
            className="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-700/50 transition-colors"
            onClick={() => setMobileOpen(true)}
            aria-label="Menu"
          >
            <Menu size={20} />
          </button>
        </div>
      </div>

      {/* Search overlay */}
      {searchOpen && (
        <div className="absolute top-full left-0 right-0 bg-white border-b border-slate-200 shadow-lg">
          <div className="max-w-3xl mx-auto px-4 py-4">
            <div className="relative">
              <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
              <input
                type="text"
                placeholder="Search by article, brand, or product name..."
                className="w-full pl-12 pr-4 py-3 bg-surface-100 rounded-2xl text-slate-900 placeholder-slate-400 outline-none focus:ring-2 focus:ring-tech-blue/30"
                autoFocus
              />
              <button onClick={() => setSearchOpen(false)} className="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-600">
                <X size={18} />
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Mobile menu */}
      {mobileOpen && (
        <div className="fixed inset-0 z-50 lg:hidden">
          <div className="absolute inset-0 bg-black/50" onClick={() => setMobileOpen(false)} />
          <div className="absolute right-0 top-0 bottom-0 w-full max-w-sm bg-white shadow-2xl overflow-y-auto">
            <div className="flex items-center justify-between p-4 border-b border-slate-100">
              <span className="text-lg font-black text-slate-900">Menu</span>
              <button onClick={() => setMobileOpen(false)} className="p-2 hover:bg-slate-100 rounded-xl">
                <X size={20} className="text-slate-600" />
              </button>
            </div>
            <div className="p-4 space-y-1">
              {megaMenuData.map((cat) => (
                <MobileNavItem key={cat.id} category={cat} />
              ))}
            </div>
          </div>
        </div>
      )}
    </header>
  )
}

function MegaMenuPanel({ category }) {
  return (
    <div className="absolute top-full left-0 mt-0 w-[640px] bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden animate-fadeIn">
      <div className="grid grid-cols-2">
        <div className="p-6">
          <div className="text-label mb-4">Shop by Type</div>
          <div className="space-y-1">
            {category.shopByType.map((type) => (
              <Link
                key={type.slug}
                to={`/category/${type.slug}`}
                className="flex items-center justify-between px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-surface-100 hover:text-tech-blue transition-colors"
              >
                <span>{type.name}</span>
                <span className="text-xs text-slate-400">{type.count}</span>
              </Link>
            ))}
          </div>
        </div>
        <div className="p-6 bg-surface-50 border-l border-slate-100">
          <div className="text-label mb-4">Shop by Brand</div>
          <div className="flex flex-wrap gap-2">
            {category.shopByBrand.map((brand) => (
              <Link
                key={brand}
                to={`/brand/${brand.toLowerCase().replace(/\s+/g, '-')}`}
                className="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:border-tech-blue hover:text-tech-blue transition-colors"
              >
                {brand}
              </Link>
            ))}
          </div>
          <div className="mt-6 p-4 bg-gradient-to-br from-slate-850 to-slate-800 rounded-xl">
            <div className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Featured</div>
            <div className="text-sm font-bold text-white">New {category.name} Collection 2024</div>
            <Link to={`/category/${category.slug}`} className="inline-flex items-center gap-1 mt-2 text-xs font-bold text-tech-blue hover:underline">
              Explore <ChevronRight size={12} />
            </Link>
          </div>
        </div>
      </div>
    </div>
  )
}

function MobileNavItem({ category }) {
  const [open, setOpen] = useState(false)
  return (
    <div>
      <button
        onClick={() => setOpen(!open)}
        className="w-full flex items-center justify-between px-3 py-3 text-left font-semibold text-slate-900 hover:bg-surface-100 rounded-xl transition-colors"
      >
        <span>{category.name}</span>
        <ChevronRight size={16} className={`text-slate-400 transition-transform ${open ? 'rotate-90' : ''}`} />
      </button>
      {open && (
        <div className="pl-4 space-y-1 pb-2">
          {category.shopByType.map((type) => (
            <Link
              key={type.slug}
              to={`/category/${type.slug}`}
              className="flex items-center justify-between px-3 py-2 text-sm text-slate-600 hover:text-tech-blue rounded-lg transition-colors"
              onClick={() => setOpen(false)}
            >
              <span>{type.name}</span>
              <span className="text-xs text-slate-400">{type.count}</span>
            </Link>
          ))}
        </div>
      )}
    </div>
  )
}

export default Header

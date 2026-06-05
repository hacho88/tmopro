import { useParams, Link } from 'react-router-dom'
import { ChevronRight, Star, Shield, Truck, RotateCcw } from 'lucide-react'
import { products } from '../data/catalogData'
import ProductGallery from '../components/ProductGallery'
import VariantSelector from '../components/VariantSelector'
import TechnicalSpecs from '../components/TechnicalSpecs'
import CompatibilityBlock from '../components/CompatibilityBlock'

function PDP() {
  const { id } = useParams()
  const product = products.find((p) => p.id === Number(id))

  if (!product) {
    return (
      <div className="max-w-7xl mx-auto px-4 py-20 text-center">
        <h1 className="text-2xl font-black text-slate-900 mb-4">Product not found</h1>
        <Link to="/" className="btn-primary">Back to catalog</Link>
      </div>
    )
  }

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      {/* Breadcrumb */}
      <nav className="flex items-center gap-1 text-xs font-bold text-slate-400 mb-6">
        <Link to="/" className="hover:text-slate-600 transition-colors">Home</Link>
        <ChevronRight size={12} />
        <Link to="/" className="hover:text-slate-600 transition-colors">{product.category}</Link>
        <ChevronRight size={12} />
        <span className="text-slate-900">{product.name}</span>
      </nav>

      {/* Main grid */}
      <div className="grid gap-8 lg:grid-cols-2 mb-16">
        {/* Left: Gallery */}
        <div>
          <ProductGallery product={product} />
        </div>

        {/* Right: Product info */}
        <div className="space-y-6">
          {/* Brand + Article */}
          <div className="flex items-center gap-3">
            <span className="px-3 py-1 bg-slate-900 text-white text-xs font-extrabold uppercase tracking-wider rounded-lg">
              {product.brand}
            </span>
            <span className="text-label">SKU: {product.article}</span>
          </div>

          {/* Title */}
          <h1 className="text-2xl sm:text-3xl font-black text-slate-900 leading-tight tracking-tight">
            {product.name}
          </h1>

          {/* Rating */}
          <div className="flex items-center gap-3">
            <div className="flex items-center gap-1">
              {[1, 2, 3, 4, 5].map((s) => (
                <Star
                  key={s}
                  size={16}
                  className={s <= Math.round(product.rating) ? 'text-amber-400 fill-amber-400' : 'text-slate-200'}
                />
              ))}
            </div>
            <span className="text-sm font-bold text-slate-700">{product.rating}</span>
            <span className="text-sm text-slate-400">({product.reviews} reviews)</span>
          </div>

          {/* Features */}
          <div className="flex flex-wrap gap-2">
            {product.features.map((f, i) => (
              <span key={i} className="px-3 py-1.5 bg-surface-100 rounded-lg text-xs font-extrabold text-slate-600 border border-slate-100">
                {f}
              </span>
            ))}
          </div>

          {/* Variant selector */}
          <VariantSelector product={product} />

          {/* Trust badges */}
          <div className="grid grid-cols-3 gap-3 pt-4 border-t border-slate-100">
            <div className="text-center p-3 rounded-xl bg-surface-50">
              <Shield size={20} className="mx-auto text-tech-blue mb-1.5" />
              <div className="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">5 Year Warranty</div>
            </div>
            <div className="text-center p-3 rounded-xl bg-surface-50">
              <Truck size={20} className="mx-auto text-tech-blue mb-1.5" />
              <div className="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Free Shipping</div>
            </div>
            <div className="text-center p-3 rounded-xl bg-surface-50">
              <RotateCcw size={20} className="mx-auto text-tech-blue mb-1.5" />
              <div className="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">30 Day Returns</div>
            </div>
          </div>
        </div>
      </div>

      {/* Below the fold */}
      <div className="grid gap-8 lg:grid-cols-3">
        {/* Specs - spans 2 cols */}
        <div className="lg:col-span-2">
          <TechnicalSpecs product={product} />
        </div>

        {/* Compatibility */}
        <div>
          <CompatibilityBlock items={product.compatibility} />
        </div>
      </div>
    </div>
  )
}

export default PDP

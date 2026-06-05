import { useState } from 'react'
import { Minus, Plus, Package } from 'lucide-react'

function VariantSelector({ product }) {
  const [selectedVariant, setSelectedVariant] = useState(product.variants[0])
  const [qty, setQty] = useState(1)

  const totalPrice = selectedVariant.price * qty

  return (
    <div className="space-y-6">
      {/* Price header */}
      <div>
        <div className="flex items-baseline gap-3 mb-1">
          <span className="text-3xl font-black text-slate-900">{totalPrice.toLocaleString('ru-RU')} {product.currency}</span>
          {qty === 1 && product.oldPrice && (
            <span className="text-lg font-semibold text-slate-400 line-through">{product.oldPrice.toLocaleString('ru-RU')}</span>
          )}
        </div>
        {qty > 1 && (
          <div className="text-sm text-slate-500 font-semibold">
            {selectedVariant.price.toLocaleString('ru-RU')} {product.currency} per unit
          </div>
        )}
      </div>

      {/* Variant selector */}
      <div>
        <div className="text-label mb-3">Finish / Color</div>
        <div className="flex flex-wrap gap-2">
          {product.variants.map((variant) => (
            <button
              key={variant.id}
              onClick={() => setSelectedVariant(variant)}
              className={`flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 font-semibold text-sm transition-all ${
                selectedVariant.id === variant.id
                  ? 'border-tech-blue bg-tech-blue-light text-tech-blue'
                  : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300'
              }`}
            >
              <span
                className="w-4 h-4 rounded-full border border-slate-200 shadow-sm"
                style={{ backgroundColor: variant.color }}
              />
              <span>{variant.name}</span>
              <span className="text-xs text-slate-400 font-bold">{variant.price.toLocaleString('ru-RU')}</span>
            </button>
          ))}
        </div>
      </div>

      {/* Stock */}
      <div className="flex items-center gap-2 p-3 bg-emerald-50 rounded-xl">
        <Package size={18} className="text-emerald-600" />
        <span className="text-sm font-bold text-emerald-700">{product.stockLabel}</span>
        <span className="text-xs text-emerald-500 font-semibold">— Ships within 24h</span>
      </div>

      {/* Quantity + Add to cart */}
      <div className="flex items-center gap-3">
        <div className="flex items-center bg-surface-100 rounded-xl border border-slate-200">
          <button
            onClick={() => setQty(Math.max(1, qty - 1))}
            className="w-11 h-11 flex items-center justify-center hover:bg-slate-200 rounded-l-xl transition-colors"
            aria-label="Decrease quantity"
          >
            <Minus size={16} className="text-slate-600" />
          </button>
          <span className="w-12 text-center text-sm font-extrabold text-slate-900">{qty}</span>
          <button
            onClick={() => setQty(qty + 1)}
            className="w-11 h-11 flex items-center justify-center hover:bg-slate-200 rounded-r-xl transition-colors"
            aria-label="Increase quantity"
          >
            <Plus size={16} className="text-slate-600" />
          </button>
        </div>
        <button className="flex-1 btn-primary py-3.5 rounded-2xl text-sm font-extrabold shadow-lg shadow-tech-blue/20">
          Add to Cart
        </button>
      </div>

      {/* B2B note */}
      <div className="p-4 bg-surface-50 rounded-xl border border-slate-100">
        <div className="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1">B2B Pricing</div>
        <div className="text-sm text-slate-600 font-semibold">
          Contractors: <span className="text-tech-blue font-bold">{(selectedVariant.price * 0.85).toLocaleString('ru-RU')} {product.currency}</span> at 10+ units
        </div>
      </div>
    </div>
  )
}

export default VariantSelector

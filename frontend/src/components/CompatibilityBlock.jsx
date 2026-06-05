import { useState } from 'react'
import { AlertTriangle, Check, Plus } from 'lucide-react'

function CompatibilityBlock({ items }) {
  const [added, setAdded] = useState(new Set())

  const requiredItems = items.filter((i) => i.required)
  const optionalItems = items.filter((i) => !i.required)

  const toggleAdd = (id) => {
    setAdded((prev) => {
      const next = new Set(prev)
      if (next.has(id)) next.delete(id)
      else next.add(id)
      return next
    })
  }

  const totalRequired = requiredItems.reduce((s, i) => s + i.price, 0)
  const totalOptional = Array.from(added).reduce((s, id) => {
    const item = optionalItems.find((i) => i.id === id)
    return s + (item?.price || 0)
  }, 0)

  return (
    <div className="bg-white rounded-3xl border border-slate-100 p-6">
      <div className="flex items-center gap-2 mb-5">
        <AlertTriangle size={18} className="text-amber-500" />
        <h3 className="text-base font-extrabold text-slate-900">Required for Installation</h3>
      </div>

      {/* Required items */}
      {requiredItems.length > 0 && (
        <div className="mb-6">
          <div className="text-label mb-3 text-amber-600">Required — Do not forget</div>
          <div className="space-y-3">
            {requiredItems.map((item) => (
              <div key={item.id} className="flex items-center gap-4 p-4 bg-amber-50/50 rounded-2xl border border-amber-100">
                <div className="w-16 h-16 bg-surface-100 rounded-xl flex-shrink-0 flex items-center justify-center">
                  <span className="text-2xl">🔧</span>
                </div>
                <div className="flex-1 min-w-0">
                  <div className="flex items-center gap-2">
                    <span className="text-xs font-extrabold uppercase tracking-wider text-amber-600 bg-amber-100 px-2 py-0.5 rounded-md">Required</span>
                  </div>
                  <div className="text-sm font-bold text-slate-900 mt-1">{item.name}</div>
                  <div className="text-sm font-extrabold text-slate-900 mt-0.5">{item.price.toLocaleString('ru-RU')} ₽</div>
                </div>
                <button
                  onClick={() => toggleAdd(item.id)}
                  className={`w-10 h-10 rounded-xl flex items-center justify-center transition-colors ${
                    added.has(item.id) ? 'bg-emerald-500 text-white' : 'bg-slate-900 text-white hover:bg-slate-800'
                  }`}
                  aria-label={added.has(item.id) ? 'Remove' : 'Add'}
                >
                  {added.has(item.id) ? <Check size={16} /> : <Plus size={16} />}
                </button>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Optional items */}
      {optionalItems.length > 0 && (
        <div>
          <div className="text-label mb-3">Recommended Accessories</div>
          <div className="space-y-3">
            {optionalItems.map((item) => (
              <div key={item.id} className="flex items-center gap-4 p-4 bg-surface-50 rounded-2xl border border-slate-100 hover:border-slate-200 transition-colors">
                <div className="w-16 h-16 bg-white rounded-xl flex-shrink-0 flex items-center justify-center shadow-sm">
                  <span className="text-2xl">🔧</span>
                </div>
                <div className="flex-1 min-w-0">
                  <div className="text-sm font-bold text-slate-900">{item.name}</div>
                  <div className="text-sm font-extrabold text-slate-900 mt-0.5">{item.price.toLocaleString('ru-RU')} ₽</div>
                </div>
                <button
                  onClick={() => toggleAdd(item.id)}
                  className={`w-10 h-10 rounded-xl flex items-center justify-center transition-colors ${
                    added.has(item.id) ? 'bg-emerald-500 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:border-slate-300'
                  }`}
                  aria-label={added.has(item.id) ? 'Remove' : 'Add'}
                >
                  {added.has(item.id) ? <Check size={16} /> : <Plus size={16} />}
                </button>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Bundle total */}
      {(added.size > 0 || requiredItems.length > 0) && (
        <div className="mt-6 pt-5 border-t border-slate-100">
          <div className="flex items-center justify-between mb-4">
            <span className="text-sm font-bold text-slate-500">Bundle Total</span>
            <span className="text-xl font-black text-slate-900">{(totalRequired + totalOptional).toLocaleString('ru-RU')} ₽</span>
          </div>
          <button className="w-full btn-primary py-3 rounded-2xl text-sm font-extrabold">
            Add Bundle to Cart
          </button>
        </div>
      )}
    </div>
  )
}

export default CompatibilityBlock

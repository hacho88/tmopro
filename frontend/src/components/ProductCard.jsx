import { useState } from 'react'
import { Link } from 'react-router-dom'
import { Heart, Eye, Star } from 'lucide-react'

function ProductCard({ product }) {
  const [hovered, setHovered] = useState(false)
  const [favorited, setFavorited] = useState(false)

  const stockColors = {
    'in-stock': 'bg-emerald-50 text-emerald-700',
    '2-3-days': 'bg-amber-50 text-amber-700',
    '1-week': 'bg-slate-100 text-slate-600',
  }

  return (
    <article
      className="card-premium group relative"
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
    >
      {/* Image */}
      <Link to={`/product/${product.id}`} className="block relative overflow-hidden rounded-t-3xl">
        <div className="aspect-square bg-gradient-to-br from-surface-100 to-surface-200 relative">
          <img
            src={product.image}
            alt={product.name}
            className="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
            loading="lazy"
          />
          {/* Hover overlay with quick actions */}
          <div className={`absolute inset-0 bg-slate-900/40 flex items-center justify-center gap-3 transition-opacity duration-300 ${hovered ? 'opacity-100' : 'opacity-0'}`}>
            <Link
              to={`/product/${product.id}`}
              className="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg hover:scale-110 transition-transform"
              aria-label="Quick view"
            >
              <Eye size={20} className="text-slate-800" />
            </Link>
          </div>
          {/* Brand badge */}
          <div className="absolute top-3 left-3">
            <span className="px-2.5 py-1 bg-white/90 backdrop-blur-sm rounded-lg text-[10px] font-extrabold uppercase tracking-wider text-slate-700 shadow-sm">
              {product.brand}
            </span>
          </div>
          {/* Stock badge */}
          <div className="absolute top-3 right-3">
            <span className={`px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider ${stockColors[product.stockStatus] || 'bg-slate-100 text-slate-600'}`}>
              {product.stockLabel}
            </span>
          </div>
          {/* Favorite */}
          <button
            onClick={(e) => { e.preventDefault(); setFavorited(!favorited) }}
            className="absolute bottom-3 right-3 w-9 h-9 bg-white/90 backdrop-blur-sm rounded-lg flex items-center justify-center shadow-sm hover:scale-110 transition-transform"
            aria-label={favorited ? 'Remove from favorites' : 'Add to favorites'}
          >
            <Heart size={16} className={favorited ? 'text-rose-500 fill-rose-500' : 'text-slate-400'} />
          </button>
        </div>
      </Link>

      {/* Content */}
      <div className="p-5">
        <div className="flex items-center gap-2 mb-2">
          <span className="text-label">{product.article}</span>
          <span className="w-1 h-1 rounded-full bg-slate-300" />
          <div className="flex items-center gap-1">
            <Star size={10} className="text-amber-400 fill-amber-400" />
            <span className="text-xs font-bold text-slate-600">{product.rating}</span>
            <span className="text-xs text-slate-400">({product.reviews})</span>
          </div>
        </div>

        <Link to={`/product/${product.id}`} className="block">
          <h3 className="text-sm font-bold text-slate-900 leading-snug mb-3 line-clamp-2 group-hover:text-tech-blue transition-colors">
            {product.name}
          </h3>
        </Link>

        <div className="flex items-end justify-between mb-4">
          <div>
            <div className="flex items-baseline gap-2">
              <span className="text-xl font-black text-slate-900">{product.price.toLocaleString('ru-RU')} {product.currency}</span>
              {product.oldPrice && (
                <span className="text-sm font-semibold text-slate-400 line-through">{product.oldPrice.toLocaleString('ru-RU')}</span>
              )}
            </div>
          </div>
        </div>

        {/* Add to cart - appears on hover */}
        <div className={`transition-all duration-300 ${hovered ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'}`}>
          <button className="w-full btn-primary py-2.5 text-sm rounded-xl">
            Add to Cart
          </button>
        </div>
      </div>
    </article>
  )
}

export default ProductCard

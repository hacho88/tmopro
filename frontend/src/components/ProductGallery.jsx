import { useState } from 'react'
import { ZoomIn, Ruler } from 'lucide-react'

function ProductGallery({ product }) {
  const [activeIndex, setActiveIndex] = useState(0)
  const [showDrawings, setShowDrawings] = useState(false)
  const [zoomed, setZoomed] = useState(false)

  const images = [
    product.image,
    'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=800&h=800&fit=crop',
    'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=800&h=800&fit=crop',
  ]

  return (
    <div className="space-y-4">
      {/* Main image */}
      <div className="relative aspect-square bg-gradient-to-br from-surface-100 to-surface-200 rounded-3xl overflow-hidden group">
        {showDrawings ? (
          <div className="w-full h-full flex items-center justify-center bg-white p-8">
            <svg viewBox="0 0 400 400" className="w-full h-full max-w-md">
              <rect x="20" y="20" width="360" height="360" fill="none" stroke="#0F172A" strokeWidth="1.5" />
              <rect x="60" y="60" width="280" height="280" fill="none" stroke="#0F172A" strokeWidth="1" strokeDasharray="4 4" />
              <circle cx="200" cy="200" r="80" fill="none" stroke="#0F172A" strokeWidth="1.5" />
              <line x1="60" y1="200" x2="340" y2="200" stroke="#0F172A" strokeWidth="0.5" />
              <line x1="200" y1="60" x2="200" y2="340" stroke="#0F172A" strokeWidth="0.5" />
              <text x="30" y="15" fontSize="10" fontFamily="monospace" fill="#64748B">FRONT VIEW</text>
              <text x="200" y="195" fontSize="8" fontFamily="monospace" fill="#64748B" textAnchor="middle">A-A</text>
              <line x1="120" y1="120" x2="280" y2="120" stroke="#0F172A" strokeWidth="1" markerEnd="url(#arrow)" />
              <text x="200" y="140" fontSize="10" fontFamily="monospace" fill="#0F172A" textAnchor="middle">160 mm</text>
              <line x1="120" y1="120" x2="120" y2="280" stroke="#0F172A" strokeWidth="1" />
              <text x="100" y="205" fontSize="10" fontFamily="monospace" fill="#0F172A" textAnchor="middle" transform="rotate(-90 100 205)">240 mm</text>
              <defs>
                <marker id="arrow" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto" markerUnits="strokeWidth">
                  <path d="M0,0 L0,6 L9,3 z" fill="#0F172A" />
                </marker>
              </defs>
            </svg>
          </div>
        ) : (
          <>
            <img
              src={images[activeIndex]}
              alt={product.name}
              className={`w-full h-full object-cover transition-transform duration-500 ${zoomed ? 'scale-150 cursor-zoom-out' : 'cursor-zoom-in'}`}
              onClick={() => setZoomed(!zoomed)}
            />
            {!zoomed && (
              <button
                onClick={() => setZoomed(true)}
                className="absolute top-4 right-4 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-sm hover:scale-110 transition-transform"
                aria-label="Zoom"
              >
                <ZoomIn size={18} className="text-slate-700" />
              </button>
            )}
          </>
        )}
      </div>

      {/* Thumbnails */}
      <div className="flex gap-3">
        {images.map((img, i) => (
          <button
            key={i}
            onClick={() => { setActiveIndex(i); setShowDrawings(false) }}
            className={`w-20 h-20 rounded-xl overflow-hidden border-2 transition-all ${activeIndex === i && !showDrawings ? 'border-tech-blue shadow-md' : 'border-transparent hover:border-slate-200'}`}
          >
            <img src={img} alt="" className="w-full h-full object-cover" />
          </button>
        ))}
        <button
          onClick={() => setShowDrawings(!showDrawings)}
          className={`w-20 h-20 rounded-xl border-2 flex flex-col items-center justify-center gap-1 transition-all ${showDrawings ? 'border-tech-blue bg-tech-blue-light shadow-md' : 'border-slate-200 hover:border-slate-300 bg-white'}`}
        >
          <Ruler size={20} className={showDrawings ? 'text-tech-blue' : 'text-slate-400'} />
          <span className={`text-[10px] font-extrabold uppercase tracking-wider ${showDrawings ? 'text-tech-blue' : 'text-slate-500'}`}>Drawing</span>
        </button>
      </div>
    </div>
  )
}

export default ProductGallery

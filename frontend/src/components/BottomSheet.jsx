import { useEffect } from 'react'
import { X, SlidersHorizontal } from 'lucide-react'
import SidebarFilters from './SidebarFilters'

function BottomSheet({ isOpen, onClose, activeFilters, onFilterChange, onClear }) {
  useEffect(() => {
    if (isOpen) document.body.style.overflow = 'hidden'
    else document.body.style.overflow = ''
    return () => { document.body.style.overflow = '' }
  }, [isOpen])

  if (!isOpen) return null

  return (
    <div className="fixed inset-0 z-50 md:hidden">
      <div className="absolute inset-0 bg-black/40" onClick={onClose} />
      <div className="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl max-h-[85vh] flex flex-col animate-slideUp">
        {/* Header */}
        <div className="flex items-center justify-between px-5 pt-5 pb-3 border-b border-slate-100 flex-shrink-0">
          <div className="flex items-center gap-2">
            <SlidersHorizontal size={18} className="text-slate-700" />
            <span className="text-base font-extrabold text-slate-900">Filters</span>
          </div>
          <button onClick={onClose} className="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-slate-100 transition-colors">
            <X size={18} className="text-slate-500" />
          </button>
        </div>

        {/* Filters scroll area */}
        <div className="flex-1 overflow-y-auto p-5 scrollbar-hide">
          <SidebarFilters
            activeFilters={activeFilters}
            onFilterChange={onFilterChange}
            onClear={onClear}
          />
        </div>

        {/* Footer actions */}
        <div className="flex-shrink-0 p-5 border-t border-slate-100 bg-white">
          <button onClick={onClose} className="w-full btn-primary py-3.5 rounded-2xl text-sm font-extrabold">
            Show Results
          </button>
        </div>
      </div>
    </div>
  )
}

export default BottomSheet

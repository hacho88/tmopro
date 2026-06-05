import { useState } from 'react'
import { ChevronDown, RotateCcw } from 'lucide-react'
import { filterGroups } from '../data/catalogData'

function SidebarFilters({ activeFilters, onFilterChange, onClear }) {
  const [expanded, setExpanded] = useState(() => {
    const init = {}
    filterGroups.forEach((g) => { init[g.id] = true })
    return init
  })

  const toggleGroup = (id) => setExpanded((p) => ({ ...p, [id]: !p[id] }))
  const activeCount = Object.values(activeFilters).flat().length

  return (
    <aside className="space-y-5">
      {/* Header */}
      <div className="flex items-center justify-between">
        <h2 className="text-sm font-extrabold uppercase tracking-wider text-slate-900">Filters</h2>
        {activeCount > 0 && (
          <button onClick={onClear} className="flex items-center gap-1 text-xs font-bold text-tech-blue hover:underline">
            <RotateCcw size={12} /> Reset ({activeCount})
          </button>
        )}
      </div>

      {/* Filter groups */}
      {filterGroups.map((group) => (
        <div key={group.id} className="bg-white rounded-2xl border border-slate-100 p-4">
          <button
            onClick={() => toggleGroup(group.id)}
            className="w-full flex items-center justify-between mb-3"
          >
            <span className="text-sm font-extrabold text-slate-900">{group.label}</span>
            <ChevronDown size={16} className={`text-slate-400 transition-transform ${expanded[group.id] ? 'rotate-180' : ''}`} />
          </button>

          {expanded[group.id] && (
            <div className="space-y-1.5">
              {group.options.map((opt) => {
                const isActive = (activeFilters[group.id] || []).includes(opt.id)
                if (group.type === 'color') {
                  return (
                    <label
                      key={opt.id}
                      className={`flex items-center gap-3 px-3 py-2 rounded-xl cursor-pointer transition-colors ${isActive ? 'bg-tech-blue-light' : 'hover:bg-surface-50'}`}
                    >
                      <input
                        type="checkbox"
                        className="sr-only"
                        checked={isActive}
                        onChange={() => onFilterChange(group.id, opt.id)}
                      />
                      <span
                        className="w-5 h-5 rounded-full border border-slate-200 shadow-sm flex-shrink-0"
                        style={{ backgroundColor: opt.color }}
                      />
                      <span className={`text-sm font-semibold flex-1 ${isActive ? 'text-tech-blue' : 'text-slate-700'}`}>{opt.label}</span>
                      <span className="text-xs text-slate-400">{opt.count}</span>
                    </label>
                  )
                }
                return (
                  <label
                    key={opt.id}
                    className={`flex items-center gap-3 px-3 py-2 rounded-xl cursor-pointer transition-colors ${isActive ? 'bg-tech-blue-light' : 'hover:bg-surface-50'}`}
                  >
                    <input
                      type="checkbox"
                      className="sr-only"
                      checked={isActive}
                      onChange={() => onFilterChange(group.id, opt.id)}
                    />
                    <span className={`w-4 h-4 rounded border flex items-center justify-center flex-shrink-0 ${isActive ? 'bg-tech-blue border-tech-blue' : 'border-slate-300 bg-white'}`}>
                      {isActive && (
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3"><path d="M5 12l5 5L20 7"/></svg>
                      )}
                    </span>
                    <span className={`text-sm font-semibold flex-1 ${isActive ? 'text-tech-blue' : 'text-slate-700'}`}>{opt.label}</span>
                    <span className="text-xs text-slate-400">{opt.count}</span>
                  </label>
                )
              })}
            </div>
          )}
        </div>
      ))}
    </aside>
  )
}

export default SidebarFilters

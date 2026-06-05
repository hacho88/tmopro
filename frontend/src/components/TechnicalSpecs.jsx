import { useState } from 'react'
import { FileText, Download, ChevronDown } from 'lucide-react'

function TechnicalSpecs({ product }) {
  const [activeTab, setActiveTab] = useState('specs')

  const tabs = [
    { id: 'specs', label: 'Specifications' },
    { id: 'inbox', label: 'In the Box' },
    { id: 'manual', label: 'Manuals' },
  ]

  return (
    <div className="bg-white rounded-3xl border border-slate-100 overflow-hidden">
      {/* Tab header */}
      <div className="flex border-b border-slate-100 overflow-x-auto scrollbar-hide">
        {tabs.map((tab) => (
          <button
            key={tab.id}
            onClick={() => setActiveTab(tab.id)}
            className={`px-6 py-4 text-sm font-extrabold whitespace-nowrap transition-colors relative ${
              activeTab === tab.id ? 'text-slate-900' : 'text-slate-400 hover:text-slate-600'
            }`}
          >
            {tab.label}
            {activeTab === tab.id && (
              <span className="absolute bottom-0 left-4 right-4 h-0.5 bg-slate-900 rounded-full" />
            )}
          </button>
        ))}
      </div>

      {/* Tab content */}
      <div className="p-6">
        {activeTab === 'specs' && (
          <table className="w-full">
            <tbody>
              {Object.entries(product.specs).map(([key, value], i) => (
                <tr key={key} className={`${i % 2 === 0 ? 'bg-surface-50' : 'bg-white'} rounded-lg`}>
                  <td className="px-4 py-3 text-sm font-bold text-slate-500 w-1/3 rounded-l-lg">{key}</td>
                  <td className="px-4 py-3 text-sm font-extrabold text-slate-900 rounded-r-lg">{value}</td>
                </tr>
              ))}
            </tbody>
          </table>
        )}

        {activeTab === 'inbox' && (
          <ul className="space-y-3">
            {product.inBox.map((item, i) => (
              <li key={i} className="flex items-start gap-3 p-3 rounded-xl bg-surface-50">
                <div className="w-8 h-8 bg-white rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                  <PackageIcon />
                </div>
                <span className="text-sm font-semibold text-slate-700">{item}</span>
              </li>
            ))}
          </ul>
        )}

        {activeTab === 'manual' && (
          <div className="space-y-3">
            <a href="#" className="flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-tech-blue hover:bg-tech-blue-light transition-colors group">
              <div className="w-12 h-12 bg-slate-900 rounded-xl flex items-center justify-center flex-shrink-0">
                <FileText size={22} className="text-white" />
              </div>
              <div className="flex-1 min-w-0">
                <div className="text-sm font-extrabold text-slate-900 group-hover:text-tech-blue transition-colors">Installation Manual</div>
                <div className="text-xs text-slate-500 font-semibold">PDF, 4.2 MB</div>
              </div>
              <Download size={18} className="text-slate-400 group-hover:text-tech-blue transition-colors" />
            </a>
            <a href="#" className="flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-tech-blue hover:bg-tech-blue-light transition-colors group">
              <div className="w-12 h-12 bg-slate-900 rounded-xl flex items-center justify-center flex-shrink-0">
                <FileText size={22} className="text-white" />
              </div>
              <div className="flex-1 min-w-0">
                <div className="text-sm font-extrabold text-slate-900 group-hover:text-tech-blue transition-colors">Technical Data Sheet</div>
                <div className="text-xs text-slate-500 font-semibold">PDF, 2.8 MB</div>
              </div>
              <Download size={18} className="text-slate-400 group-hover:text-tech-blue transition-colors" />
            </a>
          </div>
        )}
      </div>
    </div>
  )
}

function PackageIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748B" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
      <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
      <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
      <line x1="12" y1="22.08" x2="12" y2="12"/>
    </svg>
  )
}

export default TechnicalSpecs

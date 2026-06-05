import { Routes, Route } from 'react-router-dom'
import Header from './components/Header'
import PLP from './pages/PLP'
import PDP from './pages/PDP'

function App() {
  return (
    <div className="min-h-screen bg-surface-50">
      <Header />
      <main>
        <Routes>
          <Route path="/" element={<PLP />} />
          <Route path="/category/:slug" element={<PLP />} />
          <Route path="/product/:id" element={<PDP />} />
        </Routes>
      </main>
    </div>
  )
}

export default App

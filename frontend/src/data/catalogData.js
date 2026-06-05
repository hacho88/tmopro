export const megaMenuData = [
  {
    id: 'faucets', name: 'Faucets & Taps', slug: 'faucets-taps',
    shopByType: [
      { name: 'Washbasin Faucets', slug: 'washbasin-faucets', count: 124 },
      { name: 'Bidet Faucets', slug: 'bidet-faucets', count: 38 },
      { name: 'Kitchen Faucets', slug: 'kitchen-faucets', count: 89 },
      { name: 'Bathtub Faucets', slug: 'bathtub-faucets', count: 56 },
      { name: 'Shower Faucets', slug: 'shower-faucets', count: 72 },
      { name: 'Sensor Faucets', slug: 'sensor-faucets', count: 21 },
    ],
    shopByBrand: ['Grohe','Hansgrohe','Dornbracht','Axor','KLUDI'],
  },
  {
    id: 'toilets', name: 'Toilets & Flushes', slug: 'toilets-flushes',
    shopByType: [
      { name: 'Close-Coupled Toilets', slug: 'close-coupled', count: 45 },
      { name: 'Wall-Hung Toilets', slug: 'wall-hung', count: 67 },
      { name: 'Rimless Toilets', slug: 'rimless', count: 34 },
      { name: 'Flush Plates', slug: 'flush-plates', count: 89 },
      { name: 'Toilet Seats', slug: 'toilet-seats', count: 52 },
    ],
    shopByBrand: ['Duravit','Geberit','Villeroy & Boch','LAUFEN'],
  },
  {
    id: 'showers', name: 'Showers', slug: 'showers',
    shopByType: [
      { name: 'Overhead Showers', slug: 'overhead-showers', count: 98 },
      { name: 'Hand Showers', slug: 'hand-showers', count: 112 },
      { name: 'Shower Sets', slug: 'shower-sets', count: 76 },
      { name: 'Shower Panels', slug: 'shower-panels', count: 34 },
      { name: 'Thermostatic Mixers', slug: 'thermostatic-mixers', count: 45 },
    ],
    shopByBrand: ['Hansgrohe','Grohe','Dornbracht','Gessi'],
  },
  {
    id: 'bathtubs', name: 'Bathtubs', slug: 'bathtubs',
    shopByType: [
      { name: 'Freestanding Bathtubs', slug: 'freestanding', count: 42 },
      { name: 'Built-in Bathtubs', slug: 'built-in', count: 58 },
      { name: 'Corner Bathtubs', slug: 'corner', count: 23 },
      { name: 'Whirlpool Systems', slug: 'whirlpool', count: 15 },
    ],
    shopByBrand: ['Bette','Kaldewei','Duravit','Villeroy & Boch'],
  },
]

export const filterGroups = [
  {
    id: 'connectionType',
    label: 'Connection Type',
    options: [
      { id: 'g12', label: 'G 1/2"', count: 312 },
      { id: 'g38', label: 'G 3/8"', count: 189 },
      { id: 'g34', label: 'G 3/4"', count: 156 },
    ],
  },
  {
    id: 'material',
    label: 'Material / Finish',
    type: 'color',
    options: [
      { id: 'chrome', label: 'Chrome', count: 423, color: '#C0C0C0' },
      { id: 'matte-black', label: 'Matte Black', count: 156, color: '#1A1A1A' },
      { id: 'brushed-gold', label: 'Brushed Gold', count: 89, color: '#C9A35E' },
      { id: 'brushed-nickel', label: 'Brushed Nickel', count: 67, color: '#B8B8B8' },
      { id: 'matte-white', label: 'Matte White', count: 45, color: '#F5F5F5' },
    ],
  },
  {
    id: 'flowRate',
    label: 'Flow Rate',
    options: [
      { id: 'fr5', label: '≤ 5 L/min', count: 134 },
      { id: 'fr10', label: '5–10 L/min', count: 289 },
      { id: 'fr20', label: '10–20 L/min', count: 178 },
    ],
  },
  {
    id: 'brand',
    label: 'Brand',
    options: [
      { id: 'grohe', label: 'Grohe', count: 312 },
      { id: 'hansgrohe', label: 'Hansgrohe', count: 278 },
      { id: 'duravit', label: 'Duravit', count: 156 },
      { id: 'geberit', label: 'Geberit', count: 134 },
      { id: 'dornbracht', label: 'Dornbracht', count: 89 },
    ],
  },
  {
    id: 'stockStatus',
    label: 'Availability',
    options: [
      { id: 'in-stock', label: 'In Stock', count: 412 },
      { id: '2-3-days', label: 'Delivery: 2–3 days', count: 234 },
      { id: '1-week', label: 'Delivery: 1 week', count: 89 },
    ],
  },
]

export const products = [
  {
    id: 1, article: '23409000',
    name: 'Grohe Eurocube Single-Lever Washbasin Faucet M-Size',
    brand: 'Grohe', category: 'Washbasin Faucets',
    price: 18900, oldPrice: 22400, currency: '₽',
    image: 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=600&h=600&fit=crop',
    stockStatus: 'in-stock', stockLabel: 'In Stock',
    rating: 4.8, reviews: 124,
    connection: 'g12', material: 'chrome', flowRate: 'fr10', installationDepth: 'id80',
    variants: [
      { id: 'chrome', name: 'Chrome', color: '#C0C0C0', price: 18900 },
      { id: 'matte-black', name: 'Matte Black', color: '#1A1A1A', price: 22400 },
      { id: 'brushed-gold', name: 'Brushed Gold', color: '#C9A35E', price: 28900 },
    ],
    specs: {
      'Connection Type': 'G 1/2"', 'Flow Rate': '7.5 L/min',
      'Operating Pressure': '1–10 bar', 'Installation Depth': '65 mm',
      'Material': 'Brass, Chrome-plated', 'Cartridge': 'Ceramic disc 46 mm', 'Warranty': '5 years',
    },
    inBox: ['Faucet body with cartridge','Mounting hardware set','Flexible connection hoses (2x)','Installation instructions','Warranty certificate'],
    compatibility: [
      { id: 101, name: 'Grohe Pop-Up Waste Set', price: 3400, required: true },
      { id: 102, name: 'Grohe Universal Flexible Hose 3/8"', price: 1200, required: false },
    ],
  },
  {
    id: 2, article: '71070000',
    name: 'Hansgrohe Raindance Select S 240 Overhead Shower',
    brand: 'Hansgrohe', category: 'Overhead Showers',
    price: 45600, oldPrice: 52900, currency: '₽',
    image: 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=600&h=600&fit=crop',
    stockStatus: '2-3-days', stockLabel: 'Delivery: 2–3 days',
    rating: 4.9, reviews: 89,
    connection: 'g12', material: 'chrome', flowRate: 'fr20', installationDepth: 'id100',
    variants: [
      { id: 'chrome', name: 'Chrome', color: '#C0C0C0', price: 45600 },
      { id: 'brushed-nickel', name: 'Brushed Nickel', color: '#B8B8B8', price: 51200 },
    ],
    specs: {
      'Connection Type': 'G 1/2"', 'Flow Rate': '16 L/min',
      'Operating Pressure': '1–6 bar', 'Installation Depth': '95 mm',
      'Material': 'Brass, Chrome-plated', 'Spray Modes': 'Rain, RainAir, Whirl', 'Warranty': '5 years',
    },
    inBox: ['Overhead shower with arm','Ceiling connector','Mounting set','Installation manual'],
    compatibility: [
      { id: 201, name: 'Hansgrohe iBox Universal Rough-in', price: 18900, required: true },
      { id: 202, name: 'Hansgrohe Shower Hose 1.6m', price: 4200, required: false },
    ],
  },
  {
    id: 3, article: '22260979',
    name: 'Duravit D-Neo Wall-Hung Toilet Rimless',
    brand: 'Duravit', category: 'Wall-Hung Toilets',
    price: 32400, currency: '₽',
    image: 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=600&h=600&fit=crop',
    stockStatus: 'in-stock', stockLabel: 'In Stock',
    rating: 4.7, reviews: 203,
    connection: 'g12', material: 'matte-white', flowRate: 'fr5', installationDepth: 'id150',
    variants: [
      { id: 'matte-white', name: 'Matte White', color: '#F5F5F5', price: 32400 },
    ],
    specs: {
      'Connection Type': 'G 1/2"', 'Water Consumption': '4.5/3 L',
      'Installation Depth': '140 mm', 'Material': 'Ceramic, HygieneGlaze',
      'Flush Type': 'Dual flush', 'Seat': 'Soft-close included', 'Warranty': '10 years ceramic',
    },
    inBox: ['Toilet bowl','Soft-close seat with cover','Fixing set','Installation template'],
    compatibility: [
      { id: 301, name: 'Geberit Duofix Frame 112 cm', price: 28900, required: true },
      { id: 302, name: 'Geberit Sigma01 Flush Plate', price: 8900, required: true },
      { id: 303, name: 'Geberit Silent-PP Waste Pipe', price: 2400, required: false },
    ],
  },
  {
    id: 4, article: 'CL10030000-00',
    name: 'Dornbracht CL.1 Single-Lever Kitchen Mixer Pull-Down',
    brand: 'Dornbracht', category: 'Kitchen Faucets',
    price: 78900, oldPrice: 91500, currency: '₽',
    image: 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=600&h=600&fit=crop',
    stockStatus: '1-week', stockLabel: 'Delivery: 1 week',
    rating: 4.9, reviews: 45,
    connection: 'g34', material: 'brushed-gold', flowRate: 'fr20', installationDepth: 'id80',
    variants: [
      { id: 'chrome', name: 'Chrome', color: '#C0C0C0', price: 78900 },
      { id: 'brushed-gold', name: 'Brushed Gold', color: '#C9A35E', price: 94500 },
      { id: 'matte-black', name: 'Matte Black', color: '#1A1A1A', price: 86700 },
    ],
    specs: {
      'Connection Type': 'G 3/4"', 'Flow Rate': '12 L/min',
      'Operating Pressure': '1–10 bar', 'Installation Depth': '72 mm',
      'Material': 'Brass, Gold-plated', 'Spray Types': 'Standard, Shower, Jet', 'Warranty': '5 years',
    },
    inBox: ['Kitchen mixer with pull-down hose','Counterweight','Mounting set','Installation instructions'],
    compatibility: [
      { id: 401, name: 'Dornbracht Base Set G 3/4"', price: 5600, required: true },
    ],
  },
  {
    id: 5, article: '39125000',
    name: 'Grohe Eurosmart Cosmopolitan Thermostatic Shower Mixer',
    brand: 'Grohe', category: 'Shower Faucets',
    price: 26700, currency: '₽',
    image: 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=600&h=600&fit=crop',
    stockStatus: 'in-stock', stockLabel: 'In Stock',
    rating: 4.6, reviews: 178,
    connection: 'g12', material: 'chrome', flowRate: 'fr20', installationDepth: 'id100',
    variants: [
      { id: 'chrome', name: 'Chrome', color: '#C0C0C0', price: 26700 },
      { id: 'matte-black', name: 'Matte Black', color: '#1A1A1A', price: 31200 },
    ],
    specs: {
      'Connection Type': 'G 1/2"', 'Flow Rate': '15 L/min',
      'Operating Pressure': '1–5 bar', 'Installation Depth': '90 mm',
      'Material': 'Brass, Chrome-plated', 'Temperature Safety': 'SafeStop 38°C', 'Warranty': '5 years',
    },
    inBox: ['Thermostatic mixer body','Trim set','Rough-in valve','Mounting hardware'],
    compatibility: [
      { id: 501, name: 'Grohe Rapido SmartBox Universal', price: 15600, required: true },
      { id: 502, name: 'Grohe Rainshower SmartActive Head', price: 18900, required: false },
    ],
  },
  {
    id: 6, article: '11200000104',
    name: 'Geberit Sigma30 Dual-Flush Plate Brushed Chrome',
    brand: 'Geberit', category: 'Flush Plates',
    price: 12400, currency: '₽',
    image: 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=600&h=600&fit=crop',
    stockStatus: 'in-stock', stockLabel: 'In Stock',
    rating: 4.8, reviews: 312,
    connection: 'g12', material: 'brushed-nickel', flowRate: 'fr5', installationDepth: 'id50',
    variants: [
      { id: 'brushed-nickel', name: 'Brushed Nickel', color: '#B8B8B8', price: 12400 },
      { id: 'chrome', name: 'Chrome', color: '#C0C0C0', price: 11200 },
    ],
    specs: {
      'Connection Type': 'G 1/2"', 'Actuation': 'Dual flush',
      'Material': 'ABS / Brushed chrome', 'Installation Depth': '45 mm',
      'Compatibility': 'Geberit UP320/UP720', 'Warranty': '3 years',
    },
    inBox: ['Flush plate with actuator','Mounting frame','Spacer set','Installation instructions'],
    compatibility: [
      { id: 601, name: 'Geberit Duofix UP320 Cistern', price: 24500, required: true },
    ],
  },
]

import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { cities } from '../data/mockData';
import { MapContainer, TileLayer, Marker, Tooltip, GeoJSON } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Premium animated custom marker
const customIcon = new L.DivIcon({
  className: 'custom-leaflet-marker',
  html: `
    <div class="relative flex flex-col items-center justify-center group cursor-pointer" style="width: 40px; height: 40px;">
      <div class="w-8 h-8 rounded-full bg-premium-emerald/30 absolute animate-ping" style="top: 50%; left: 50%; transform: translate(-50%, -50%);"></div>
      <svg class="w-10 h-10 text-premium-emerald drop-shadow-lg transition-colors hover:text-emerald-500 relative z-10" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
      </svg>
    </div>
  `,
  iconSize: [40, 40],
  iconAnchor: [20, 40],
  popupAnchor: [0, -40],
});

export default function MapInteractive() {
  const navigate = useNavigate();
  const [geoData, setGeoData] = useState(null);

  useEffect(() => {
    fetch('/maharashtra.geojson')
      .then(res => res.json())
      .then(data => setGeoData(data))
      .catch(err => console.error("Error loading boundary data:", err));
  }, []);

  // Coordinates strictly centering Maharashtra
  const center = [19.7515, 75.7139];
  const zoom = 6.4;

  const geoStyle = {
    color: '#10b981',
    weight: 3,
    opacity: 0.6,
    fillColor: '#10b981',
    fillOpacity: 0.08,
    dashArray: '6, 6'
  };

  return (
    <div className="relative w-full aspect-[4/3] max-w-4xl mx-auto rounded-3xl overflow-hidden shadow-2xl border-4 border-slate-50 group">
      
      {/* react-leaflet Container */}
      <MapContainer 
        center={center} 
        zoom={zoom} 
        scrollWheelZoom={false}
        className="w-full h-full z-0 bg-slate-50"
      >
        {/* Premium CartoDB Voyager Map Tiles */}
        <TileLayer
          url="https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png"
          attribution='&copy; <a href="https://carto.com/">CARTO</a>'
        />

        {/* State Boundary Highlight */}
        {geoData && <GeoJSON data={geoData} style={geoStyle} />}

        {cities.map((city) => (
          <Marker 
            key={city.id} 
            position={[city.coordinates.lat, city.coordinates.lng]} 
            icon={customIcon}
            eventHandlers={{
              click: () => {
                navigate(`/city/${city.id}`);
              }
            }}
          >
            <Tooltip direction="top" offset={[0, -40]} opacity={1} className="premium-tooltip">
              <div className="text-center p-1 w-40">
                <span className="font-extrabold text-premium-emerald text-xl block mb-1">{city.name}</span>
                <span className="text-xs text-slate-500 font-bold uppercase tracking-wider block mb-2 border-b border-slate-100 pb-2">
                  {city.propertyCount} Properties
                </span>
                <span className="w-full inline-block bg-emerald-50 text-premium-emerald font-bold py-1.5 rounded-lg text-sm">
                  Click to View &rarr;
                </span>
              </div>
            </Tooltip>
          </Marker>
        ))}
      </MapContainer>
      
      {/* Decorative Floating UI Element */}
      <div className="absolute top-4 right-4 z-20 bg-white/90 backdrop-blur-md px-4 py-2 rounded-xl shadow-lg border border-slate-200 pointer-events-none">
        <span className="flex items-center text-sm font-bold text-slate-700">
          <span className="w-2 h-2 rounded-full bg-premium-emerald animate-pulse mr-2"></span>
          Live Auction Map
        </span>
      </div>
    </div>
  );
}

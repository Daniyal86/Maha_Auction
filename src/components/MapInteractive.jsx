import { motion } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
import { cities } from '../data/mockData';

export default function MapInteractive() {
  const navigate = useNavigate();

  return (
    <div className="relative w-full aspect-[4/3] max-w-4xl mx-auto group perspective-1000">
      
      {/* 3D Floating GIF Map of Maharashtra */}
      <motion.div 
        className="absolute inset-0 flex items-center justify-center transition-transform duration-700"
        initial={{ rotateX: 30, rotateZ: -5, rotateY: 10, y: 20 }}
        animate={{ rotateX: 0, rotateZ: 0, rotateY: 0, y: 0 }}
        transition={{ duration: 1.5, ease: "easeOut" }}
      >
        <img 
          src="/maharashtra-map.gif" 
          alt="Map of Maharashtra" 
          className="w-full h-full object-contain filter drop-shadow-2xl opacity-90 transition-transform duration-500 hover:scale-105"
          style={{
            mixBlendMode: 'multiply',
            filter: "drop-shadow(0px 20px 25px rgba(0,0,0,0.15)) drop-shadow(0px 10px 10px rgba(0,0,0,0.1)) contrast(1.1)"
          }}
        />
        
        {/* Render Cities strictly positioned over the SVG map */}
        <div className="absolute inset-0 w-full h-full pointer-events-none" style={{ padding: '5%' }}>
          <div className="relative w-full h-full">
            {cities.map((city) => (
              <motion.div
                key={city.id}
                className="absolute flex flex-col items-center cursor-pointer group/node pointer-events-auto"
                style={{ left: `${city.coordinates.x}%`, top: `${city.coordinates.y}%`, zIndex: 10 }}
                whileHover={{ scale: 1.2, zIndex: 50 }}
                onClick={() => navigate(`/city/${city.id}`)}
              >
                {/* Tooltip */}
                <div className="absolute bottom-12 bg-white border border-slate-200 px-5 py-3 rounded-xl shadow-2xl opacity-0 group-hover/node:opacity-100 transition-all duration-300 whitespace-nowrap pointer-events-none transform translate-y-4 group-hover/node:translate-y-0 z-50">
                  <span className="font-bold text-premium-emerald text-xl block">{city.name}</span>
                  <span className="text-sm text-slate-600 font-bold">{city.propertyCount} Properties Available</span>
                  <div className="absolute -bottom-2 left-1/2 -translate-x-1/2 w-4 h-4 bg-white border-b border-r border-slate-200 rotate-45"></div>
                </div>
                
                {/* 3D Map Pin Node */}
                <div className="relative flex flex-col items-center justify-center">
                  <div className="w-8 h-8 rounded-full bg-premium-emerald/30 absolute animate-ping"></div>
                  
                  {/* Custom Pin Shape */}
                  <svg className="w-10 h-10 text-premium-emerald drop-shadow-lg group-hover/node:text-emerald-500 transition-colors" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                  </svg>
                </div>
                
                {/* City Label */}
                <span className="mt-1 text-sm font-extrabold text-slate-800 group-hover/node:text-premium-emerald transition-colors bg-white/95 px-3 py-1 rounded-lg shadow-lg border border-slate-200 pointer-events-none relative -top-2">
                  {city.name}
                </span>
              </motion.div>
            ))}
          </div>
        </div>
      </motion.div>
    </div>
  );
}

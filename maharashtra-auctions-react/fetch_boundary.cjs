const https = require('https');
const fs = require('fs');

const url = 'https://nominatim.openstreetmap.org/search.php?state=Maharashtra&country=India&polygon_geojson=1&format=jsonv2';

https.get(url, { headers: { 'User-Agent': 'NodeJS/GeoJSON Fetcher' } }, (res) => {
  let data = '';
  res.on('data', chunk => data += chunk);
  res.on('end', () => {
    try {
      const json = JSON.parse(data);
      if (json && json.length > 0) {
        // Find the one that is the administrative boundary
        const boundary = json.find(item => item.addresstype === 'state');
        if (boundary && boundary.geojson) {
          const geojsonFeature = {
            type: "FeatureCollection",
            features: [
              {
                type: "Feature",
                properties: { name: "Maharashtra" },
                geometry: boundary.geojson
              }
            ]
          };
          fs.writeFileSync('public/maharashtra.geojson', JSON.stringify(geojsonFeature));
          console.log('Saved to public/maharashtra.geojson');
        } else {
          console.log('No state boundary geojson found', json);
        }
      } else {
        console.log('No results found');
      }
    } catch(e) {
      console.error(e);
    }
  });
});
